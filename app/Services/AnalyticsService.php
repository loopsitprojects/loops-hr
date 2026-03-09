<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Get Hiring Volume: Number of 'Hired' candidates grouped by Department and Month.
     * Hired status corresponds to 'joined' or maybe 'offer_accepted'? 
     * User said "Hired", typically implies 'joined'. I will check if 'joined' is the final state.
     * Based on migration: 'joined' seems to be the final success state.
     */
    public function getHiringVolume(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Candidate::query()
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(candidates.finalized_at, candidates.updated_at), "%Y-%m") as month'),
                'departments.name as department_name',
                DB::raw('count(*) as total')
            )
            ->join('departments', 'candidates.department_id', '=', 'departments.id')
            ->whereIn('candidates.stage', ['joined', 'offer_accepted']);

        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('candidates.finalized_at', [$startDate, $endDate])
                  ->orWhere(function($sq) use ($startDate, $endDate) {
                      $sq->whereNull('candidates.finalized_at')
                         ->whereBetween('candidates.updated_at', [$startDate, $endDate]);
                  });
            });
        }

        return $query->groupBy('month', 'department_name')
            ->orderBy('month')
            ->get();
    }

    /**
     * Active Designations: Count of open/active positions per designation.
     * "candidates currently in the 'Interviewing' or 'AURA Task' stages"
     * Mapping: 'test' (AURA Task), '1st_interview', '2nd_interview'.
     */
    public function getActiveDesignations(): Collection
    {
        return Candidate::query()
            ->select(
                'designations.name as designation_name',
                DB::raw('count(*) as active_count')
            )
            ->join('designations', 'candidates.designation_id', '=', 'designations.id')
            ->whereIn('candidates.stage', ['shortlisted', 'test', 'test_sent', 'test_received', '1st_interview', '2nd_interview'])
            ->groupBy('designations.name')
            ->orderByDesc('active_count')
            ->get();
    }

    /**
     * Avg Time-to-Hire: The average difference between created_at and finalized_at per department.
     */
    public function getAverageTimeToHire(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Candidate::query()
            ->select(
                'departments.name as department_name',
                DB::raw('AVG(DATEDIFF(candidates.finalized_at, candidates.created_at)) as avg_days')
            )
            ->join('departments', 'candidates.department_id', '=', 'departments.id')
            ->whereNotNull('candidates.finalized_at')
            ->whereNotNull('candidates.created_at')
            ->whereIn('candidates.stage', ['joined', 'offer_accepted']);

        if ($startDate && $endDate) {
            $query->whereBetween('candidates.finalized_at', [$startDate, $endDate]);
        }

        return $query->groupBy('department_name')
            ->get();
    }

    /**
     * Summary Report Data
     * Department Name | Total Applicants | Total Hired | Avg Days to Hire
     */
    public function getSummaryReport(?string $startDate = null, ?string $endDate = null): Collection
    {
        // Get all stats via subqueries for better performance and to avoid duplicate rows.
        
        $subQueryApplicants = DB::table('candidates')
            ->select('department_id', DB::raw('count(*) as count'))
            ->when($startDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->groupBy('department_id');
            
        $subQueryHired = DB::table('candidates')
            ->select('department_id', DB::raw('count(*) as count'))
            ->whereIn('stage', ['joined', 'offer_accepted'])
            ->where('is_archived', false)
            ->when($startDate, function($q) use ($startDate, $endDate) {
                $q->where(function($sq) use ($startDate, $endDate) {
                    $sq->whereBetween('finalized_at', [$startDate, $endDate])
                      ->orWhere(function($ssq) use ($startDate, $endDate) {
                          $ssq->whereNull('finalized_at')
                             ->whereBetween('updated_at', [$startDate, $endDate]);
                      });
                });
            })
            ->groupBy('department_id');
            
        $subQueryAvg = DB::table('candidates')
            ->select('department_id', DB::raw('AVG(DATEDIFF(finalized_at, created_at)) as avg_days'))
            ->whereIn('stage', ['joined', 'offer_accepted'])
            ->where('is_archived', false)
            ->when($startDate, fn($q) => $q->whereBetween('finalized_at', [$startDate, $endDate]))
            ->groupBy('department_id');
             
        $results = DB::table('departments')
            ->leftJoinSub($subQueryApplicants, 'applicants', 'departments.id', '=', 'applicants.department_id')
            ->leftJoinSub($subQueryHired, 'hired', 'departments.id', '=', 'hired.department_id')
            ->leftJoinSub($subQueryAvg, 'avgs', 'departments.id', '=', 'avgs.department_id')
            ->select(
                'departments.name as department_name',
                DB::raw('COALESCE(applicants.count, 0) as total_applicants'),
                DB::raw('COALESCE(hired.count, 0) as total_hired'),
                DB::raw('COALESCE(avgs.avg_days, 0) as avg_days_to_hire')
            )
            ->get();
            
        return $results;
    }

    public function getDesignationBreakdown(?int $departmentId = null, ?string $startDate = null, ?string $endDate = null, string $filter = 'all'): Collection
    {
        // Get designations with application counts and assessment counts
        $designationsQuery = DB::table('designations')
            ->leftJoin('candidates', function($join) use ($startDate, $endDate) {
                $join->on('designations.id', '=', 'candidates.designation_id')
                    ->where('candidates.is_archived', false);
                if ($startDate) $join->where('candidates.created_at', '>=', $startDate);
                if ($endDate) $join->where('candidates.created_at', '<=', $endDate);
            })
            ->leftJoin('candidate_assessments', 'candidates.id', '=', 'candidate_assessments.candidate_id')
            ->join('departments', 'designations.department_id', '=', 'departments.id')
            ->select(
                'designations.id',
                'designations.name',
                'departments.name as department_name',
                DB::raw('COUNT(DISTINCT candidates.id) as total_applications'),
                DB::raw('COUNT(DISTINCT candidate_assessments.id) as total_tasks')
            );

        if ($filter === 'active') {
            $designationsQuery->where('designations.is_active', true);
        }

        if ($departmentId) {
            $designationsQuery->where('designations.department_id', $departmentId);
        }

        // Get avg time to hire per designation
        $avgTimeQuery = DB::table('candidates')
            ->select('designation_id', DB::raw('AVG(DATEDIFF(finalized_at, created_at)) as avg_days'))
            ->whereIn('stage', ['joined', 'offer_accepted'])
            ->whereNotNull('finalized_at')
            ->whereNotNull('created_at');

        if ($startDate) $avgTimeQuery->where('finalized_at', '>=', $startDate);
        if ($endDate) $avgTimeQuery->where('finalized_at', '<=', $endDate);
        if ($departmentId) $avgTimeQuery->where('department_id', $departmentId);

        $avgTimeToHireBreakdown = $avgTimeQuery->groupBy('designation_id')
            ->get()
            ->pluck('avg_days', 'designation_id');

        // Get avg expected salary per designation
        $avgSalaryQuery = DB::table('candidates')
            ->select('designation_id', DB::raw('AVG(CAST(REPLACE(expected_salary, ",", "") AS DECIMAL(15,2))) as avg_salary'))
            ->where('is_archived', false)
            ->whereNotNull('expected_salary')
            ->where('expected_salary', '>', 0);

        if ($startDate) $avgSalaryQuery->where('created_at', '>=', $startDate);
        if ($endDate) $avgSalaryQuery->where('created_at', '<=', $endDate);
        if ($departmentId) $avgSalaryQuery->where('department_id', $departmentId);

        $avgSalaryBreakdown = $avgSalaryQuery->groupBy('designation_id')
            ->get()
            ->pluck('avg_salary', 'designation_id');

        $designations = $designationsQuery->groupBy('designations.id', 'designations.name', 'departments.name')
            ->get();

        // Get stage breakdown per designation
        $stageQuery = DB::table('candidates')
            ->where('is_archived', false)
            ->select('designation_id', 'stage', DB::raw('count(*) as count'));

        if ($startDate) {
            $stageQuery->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $stageQuery->where('created_at', '<=', $endDate);
        }

        if ($departmentId) {
            $stageQuery->where('department_id', $departmentId);
        }

        $stageBreakdown = $stageQuery->groupBy('designation_id', 'stage')
            ->get()
            ->groupBy('designation_id');

        // Merge stage breakdown, avg time to hire, and avg salary into designations
        return $designations->map(function ($designation) use ($stageBreakdown, $avgTimeToHireBreakdown, $avgSalaryBreakdown) {
            $designation->stages = $stageBreakdown->get($designation->id, collect())->pluck('count', 'stage')->toArray();
            $designation->avg_time_to_hire = round($avgTimeToHireBreakdown->get($designation->id, 0), 1);
            $designation->avg_expected_salary = round($avgSalaryBreakdown->get($designation->id, 0), 0);
            return $designation;
        });
    }

    /**
     * Get Overview Stats for a given period and department.
     */
    public function getOverviewStats(?int $departmentId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        // Total Applications: based on created_at (new intake)
        $totalAppsQuery = Candidate::query()->where('is_archived', false);
        if ($departmentId) $totalAppsQuery->where('department_id', $departmentId);
        if ($startDate && $endDate) $totalAppsQuery->whereBetween('created_at', [$startDate, $endDate]);
        $totalApps = $totalAppsQuery->count();

        // Hired & Rejected: based on finalized_at (or updated_at as fallback)
        $terminalQuery = Candidate::query()->where('is_archived', false);
        if ($departmentId) $terminalQuery->where('department_id', $departmentId);
        
        if ($startDate && $endDate) {
            $terminalQuery->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('finalized_at', [$startDate, $endDate])
                  ->orWhere(function($sq) use ($startDate, $endDate) {
                      $sq->whereNull('finalized_at')
                         ->whereIn('stage', ['joined', 'offer_accepted', 'rejected'])
                         ->whereBetween('updated_at', [$startDate, $endDate]);
                  });
            });
        }

        $stats = $terminalQuery->selectRaw('sum(case when stage IN ("joined", "offer_accepted") then 1 else 0 end) as joined')
            ->selectRaw('sum(case when stage = "rejected" then 1 else 0 end) as rejected')
            ->first();

        $activeDesignationsCount = DB::table('designations')->where('is_active', true);
        if ($departmentId) $activeDesignationsCount->where('department_id', $departmentId);
        $activeDesignationsCount = $activeDesignationsCount->count();

        $avgTimeQuery = Candidate::query()
            ->whereIn('stage', ['joined', 'offer_accepted'])
            ->whereNotNull('finalized_at')
            ->whereNotNull('created_at');
        if ($departmentId) $avgTimeQuery->where('department_id', $departmentId);
        if ($startDate && $endDate) $avgTimeQuery->whereBetween('finalized_at', [$startDate, $endDate]);
        
        $avgTime = $avgTimeQuery->selectRaw('AVG(DATEDIFF(finalized_at, created_at)) as avg_days')->value('avg_days');

        return [
            'total_apps' => $totalApps,
            'hired' => $stats->joined ?? 0,
            'active_designations' => $activeDesignationsCount ?? 0,
            'avg_time_to_hire' => round($avgTime ?? 0, 1),
            'hire_rate' => $totalApps > 0 ? round(($stats->joined / $totalApps) * 100, 1) : 0
        ];
    }

    /**
     * Get Funnel Data for the recruitment pipeline.
     */
    public function getFunnelData(?int $departmentId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Candidate::query();
        if ($departmentId) $query->where('department_id', $departmentId);
        if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);

        $counts = $query->select('stage', DB::raw('count(*) as count'))
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        // Sequence: Applied (Total) -> Shortlisted -> Test -> Interviews -> Offer -> Joined
        $applied = array_sum($counts);
        $shortlisted = ($counts['shortlisted'] ?? 0) + ($counts['test'] ?? 0) + ($counts['test_sent'] ?? 0) + ($counts['test_received'] ?? 0) + ($counts['1st_interview'] ?? 0) + ($counts['2nd_interview'] ?? 0) + ($counts['offer_sent'] ?? 0) + ($counts['offer_accepted'] ?? 0) + ($counts['joined'] ?? 0);
        $test = ($counts['test'] ?? 0) + ($counts['test_sent'] ?? 0) + ($counts['test_received'] ?? 0) + ($counts['1st_interview'] ?? 0) + ($counts['2nd_interview'] ?? 0) + ($counts['offer_sent'] ?? 0) + ($counts['offer_accepted'] ?? 0) + ($counts['joined'] ?? 0);
        $interview = ($counts['1st_interview'] ?? 0) + ($counts['2nd_interview'] ?? 0) + ($counts['offer_sent'] ?? 0) + ($counts['offer_accepted'] ?? 0) + ($counts['joined'] ?? 0);
        $offer = ($counts['offer_sent'] ?? 0) + ($counts['offer_accepted'] ?? 0) + ($counts['joined'] ?? 0);
        $joined = $counts['joined'] ?? 0;

        return [
            ['stage' => 'Applied', 'count' => $applied, 'color' => '#14b8a6'],
            ['stage' => 'Shortlisted', 'count' => $shortlisted, 'color' => '#0d9488'],
            ['stage' => 'Test', 'count' => $test, 'color' => '#06b6d4'],
            ['stage' => 'Interview', 'count' => $interview, 'color' => '#3b82f6'],
            ['stage' => 'Offer', 'count' => $offer, 'color' => '#f59e0b'],
            ['stage' => 'Joined', 'count' => $joined, 'color' => '#10b981'],
        ];
    }

    /**
     * Get Application and Hiring trends over the last 6 months.
     */
    public function getRecruitmentTrends(?int $departmentId = null): array
    {
        $months = [];
        $apps = [];
        $hired = [];
        $velocity = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $startOfMonth = $date->startOfMonth()->toDateTimeString();
            $endOfMonth = $date->endOfMonth()->toDateTimeString();

            $months[] = $monthLabel;

            // Applications in this month
            $appQuery = Candidate::query()->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            if ($departmentId) $appQuery->where('department_id', $departmentId);
            $apps[] = $appQuery->count();

            // Hires in this month (finalized)
            $hireQuery = Candidate::query()->whereIn('stage', ['joined', 'offer_accepted'])->whereBetween('finalized_at', [$startOfMonth, $endOfMonth]);
            if ($departmentId) $hireQuery->where('department_id', $departmentId);
            $hired[] = $hireQuery->count();

            // Avg Time to Hire for those hired in this month
            $velQuery = Candidate::query()->whereIn('stage', ['joined', 'offer_accepted'])->whereBetween('finalized_at', [$startOfMonth, $endOfMonth]);
            if ($departmentId) $velQuery->where('department_id', $departmentId);
            $avgVel = $velQuery->selectRaw('AVG(DATEDIFF(finalized_at, created_at)) as avg_days')->value('avg_days');
            $velocity[] = round($avgVel ?? 0, 1);
        }

        return [
            'labels' => $months,
            'applications' => $apps,
            'hires' => $hired,
            'velocity' => $velocity
        ];
    }

    /**
     * Get Departmental Comparison for Volume and Speed.
     */
    public function getDepartmentalComparison(?string $startDate = null, ?string $endDate = null): array
    {
        $departments = Department::all();
        $labels = [];
        $volumes = [];
        $speeds = [];

        foreach ($departments as $dept) {
            $labels[] = $dept->name;

            $volQuery = Candidate::query()->where('department_id', $dept->id);
            if ($startDate && $endDate) $volQuery->whereBetween('created_at', [$startDate, $endDate]);
            $volumes[] = $volQuery->count();

            $speedQuery = Candidate::query()->where('department_id', $dept->id)->where('stage', 'joined');
            if ($startDate && $endDate) $speedQuery->whereBetween('finalized_at', [$startDate, $endDate]);
            $avgSpeed = $speedQuery->selectRaw('AVG(DATEDIFF(finalized_at, created_at)) as avg_days')->value('avg_days');
            $speeds[] = round($avgSpeed ?? 0, 1);
        }

        return [
            'labels' => $labels,
            'volumes' => $volumes,
            'speeds' => $speeds
        ];
    }
}
