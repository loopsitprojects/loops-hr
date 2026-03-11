<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;
// use PDF; // Will be available after dompdf install
// use Maatwebsite\Excel\Facades\Excel; // Will be available after excel install

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isHR() && !$user->isHOD() && !$user->isManager() && !$user->isManagers()) {
            abort(403, 'Unauthorized access.');
        }

        // Department scoping for HOD and Manager roles
        $user = auth()->user();
        $departmentId = $request->input('department_id');
        
        // If user is HOD or Manager, force their department
        if ($user->isHOD() || $user->isManager() || $user->isManagers()) {
            $departmentId = $user->department_id;
        }

        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));
        $reportType = $request->input('report_type', 'annual');
        $designationFilter = $request->input('designation_filter', 'active');
        
        if ($reportType === 'annual') {
            $startDate = $year . '-01-01 00:00:00';
            $endDate = $year . '-12-31 23:59:59';
        } else {
            $startDate = $month . '-01 00:00:00';
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
        }

        $designationBreakdown = $this->analyticsService->getDesignationBreakdown($departmentId, $startDate, $endDate, $designationFilter);
        $overviewStats = $this->analyticsService->getOverviewStats($departmentId, $startDate, $endDate);
        $departments = Department::all();

        // Specific metric for Departmental Comparison
        $deptComparison = $this->analyticsService->getDepartmentalComparison($startDate, $endDate);

        return view('analytics.index', compact(
            'designationBreakdown', 
            'departments', 
            'departmentId', 
            'month', 
            'year',
            'reportType',
            'overviewStats', 
            'designationFilter',
            'deptComparison'
        ));
    }

    /**
     * Export the analytics report.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isHR() && !$user->isHOD() && !$user->isManager() && !$user->isManagers()) {
            abort(403, 'Unauthorized access.');
        }

        // Department scoping for HOD and Manager roles
        $user = auth()->user();
        $departmentId = $request->input('department_id');
        
        // If user is HOD or Manager, force their department
        if ($user->isHOD() || $user->isManager() || $user->isManagers()) {
            $departmentId = $user->department_id;
        }

        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));
        $reportType = $request->input('report_type', 'annual');
        $designationFilter = $request->input('designation_filter', 'active');
        $format = $request->input('format', 'pdf'); // Accept format from request, default to PDF

        if ($reportType === 'annual') {
            $startDate = $year . '-01-01 00:00:00';
            $endDate = $year . '-12-31 23:59:59';
        } else {
            $startDate = $month . '-01 00:00:00';
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
        }

        $data = $this->analyticsService->getDesignationBreakdown($departmentId, $startDate, $endDate, $designationFilter);

        // handle CSV export
        if ($format === 'csv') {
            $filename = "hr_analytics_report_{$startDate}_to_{$endDate}.csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                // BOM for Excel
                fputs($file, "\xEF\xBB\xBF"); 
                fputcsv($file, [
                    'Designation', 'Total Applicants', 'Shortlisted', 'Test Sent', 'Test Rec', '1st Int', '2nd Int', 'Offer', 'Accepted', 'Joined', 'Rej', 'Avg Speed', 'Avg Salary'
                ]);

                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->name,
                        $row->total_applications,
                        $row->stages['shortlisted'] ?? 0,
                        $row->stages['test_sent'] ?? 0,
                        $row->stages['test_received'] ?? 0,
                        $row->stages['1st_interview'] ?? 0,
                        $row->stages['2nd_interview'] ?? 0,
                        $row->stages['offer_sent'] ?? 0,
                        $row->stages['offer_accepted'] ?? 0,
                        $row->stages['joined'] ?? 0,
                        $row->stages['rejected'] ?? 0,
                        $row->avg_time_to_hire . 'd',
                        $row->avg_expected_salary > 0 ? $row->avg_expected_salary : '-'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
        
        // Handle PDF
        if ($format === 'pdf') {
            $departmentName = $departmentId 
                ? (Department::find($departmentId)->name ?? 'All Departments')
                : 'All Departments';

            $pdf = Pdf::loadView('analytics.report', [
                'data' => $data,
                'month' => $month,
                'year' => $year,
                'reportType' => $reportType,
                'departmentName' => $departmentName
            ])->setPaper('a4', 'landscape');

            $filename = $reportType === 'annual' 
                ? "hr_annual_analytics_report_{$year}.pdf" 
                : "hr_analytics_report_{$month}.pdf";
            return $pdf->download($filename);
        }

        return redirect()->back()->with('error', 'Unsupported export format.');
    }
}
