<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HR Analytics Report - {{ $month }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0D1B2A; padding-bottom: 10px; }
        .header h1 { color: #0D1B2A; margin: 0; text-transform: uppercase; font-size: 24px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #0D1B2A; color: white; padding: 10px; text-transform: uppercase; font-size: 10px; text-align: left; }
        td { border-bottom: 1px solid #eee; padding: 8px; font-size: 11px; }
        .count { text-align: center; font-weight: bold; }
        .designation { font-weight: bold; color: #0D1B2A; }
    </style>
</head>
<body>
    <div class="header">
        <h1>HR {{ $reportType === 'annual' ? 'Annual' : 'Monthly' }} Analytics Report</h1>
        <p>{{ $reportType === 'annual' ? $year : date('F Y', strtotime($month . '-01')) }}</p>
    </div>

    <div class="info">
        <p><strong>Department:</strong> {{ $departmentName }}</p>
        <p><strong>Generated on:</strong> {{ date('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Designation</th>
                <th style="text-align:center">Total Applicants</th>
                <th style="text-align:center">Shortlisted</th>
                <th style="text-align:center">Tasks</th>
                <th style="text-align:center">Test Sent</th>
                <th style="text-align:center">Test Rec</th>
                <th style="text-align:center">1st Int</th>
                <th style="text-align:center">2nd Int</th>
                <th style="text-align:center">Offer</th>
                <th style="text-align:center">Accepted</th>
                <th style="text-align:center">Joined</th>
                <th style="text-align:center">Rej</th>
                <th style="text-align:center">Avg Speed</th>
                <th style="text-align:center">Avg Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->groupBy('department_name') as $departmentName => $designations)
                <!-- Department Header -->
                <tr>
                    <td colspan="13" style="background-color: #f0f0f0; font-weight: bold; color: #14b8a6; text-transform: uppercase;">
                        {{ $departmentName }}
                    </td>
                </tr>

                @foreach($designations as $row)
                <tr>
                    <td class="designation" style="padding-left: 15px;">{{ $row->name }}</td>
                    <td class="count">{{ $row->total_applications }}</td>
                    <td class="count">{{ $row->stages['shortlisted'] ?? 0 }}</td>
                    <td class="count">{{ $row->total_tasks }}</td>
                    <td class="count">{{ $row->stages['test_sent'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['test_received'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['1st_interview'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['2nd_interview'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['offer_sent'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['offer_accepted'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['joined'] ?? 0 }}</td>
                    <td class="count">{{ $row->stages['rejected'] ?? 0 }}</td>
                    <td class="count">{{ $row->avg_time_to_hire ?? 0 }}d</td>
                    <td class="count">{{ $row->avg_expected_salary > 0 ? number_format($row->avg_expected_salary) : '-' }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
