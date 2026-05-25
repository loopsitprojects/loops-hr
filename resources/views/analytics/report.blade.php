<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HR Analytics Report - {{ $month }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            color: #1e293b; 
            font-size: 10px; 
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #0f172a; 
            padding-bottom: 15px; 
        }
        .header h1 { 
            color: #0f172a; 
            margin: 0; 
            text-transform: uppercase; 
            font-size: 22px; 
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 16px;
            color: #64748b;
            font-weight: bold;
        }
        .info-grid {
            margin-bottom: 20px;
            width: 100%;
        }
        .info-item {
            display: inline-block;
            width: 48%;
            font-size: 11px;
        }
        .info-label {
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            margin-right: 5px;
        }
        .info-value {
            color: #0f172a;
            font-weight: bold;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            table-layout: fixed; /* Force fixed layout to prevent overflow */
        }
        th { 
            background-color: #0f172a; 
            color: #ffffff; 
            padding: 8px 4px; 
            text-transform: uppercase; 
            font-size: 8px; 
            text-align: center;
            border: 1px solid #1e293b;
        }
        th.text-left { text-align: left; padding-left: 8px; }
        
        td { 
            border-bottom: 1px solid #e2e8f0; 
            border-right: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            padding: 6px 4px; 
            font-size: 10px; 
            text-align: center;
        }
        .designation-cell { 
            text-align: left; 
            padding-left: 10px;
            font-weight: 500;
            color: #0f172a;
            width: 18%; /* Give more space to designation */
        }
        .dept-row {
            background-color: #f8fafc;
        }
        .dept-cell {
            padding: 10px;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
            font-size: 11px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .even-row { background-color: #ffffff; }
        .odd-row { background-color: #f1f5f9; }
        
        .footer {
            margin-top: 30px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HR {{ $reportType === 'annual' ? 'Annual' : ($reportType === 'custom' ? 'Custom Period' : 'Monthly') }} Analytics Report</h1>
        <p>
            @if($reportType === 'annual')
                {{ $year }}
            @elseif($reportType === 'custom')
                {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}
            @else
                {{ date('F Y', strtotime($month . '-01')) }}
            @endif
        </p>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Department:</span>
            <span class="info-value">{{ $departmentName }}</span>
        </div>
        <div class="info-item" style="text-align: right;">
            <span class="info-label">Generated on:</span>
            <span class="info-value">{{ date('d M Y, h:i A') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left" style="width: 18%;">Designation</th>
                <th style="width: 7%;">Total App</th>
                <th style="width: 7%;">Shortlisted</th>
                <th style="width: 7%;">Test Sent</th>
                <th style="width: 7%;">Test Rec</th>
                <th style="width: 7%;">1st Int</th>
                <th style="width: 7%;">2nd Int</th>
                <th style="width: 7%;">Offer</th>
                <th style="width: 7%;">Accepted</th>
                <th style="width: 7%;">Joined</th>
                <th style="width: 7%;">Rej</th>
                <th style="width: 6%;">Avg Speed</th>
                <th style="width: 9%;">Avg Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->groupBy('department_name') as $deptName => $designations)
                <tr class="dept-row">
                    <td colspan="13" class="dept-cell">
                        {{ $deptName }}
                    </td>
                </tr>

                @foreach($designations as $index => $row)
                <tr class="{{ $index % 2 == 0 ? 'even-row' : 'odd-row' }}">
                    <td class="designation-cell">{{ $row->name }}</td>
                    <td>{{ $row->total_applications }}</td>
                    <td>{{ $row->stages['shortlisted'] ?? 0 }}</td>
                    <td>{{ $row->stages['test_sent'] ?? 0 }}</td>
                    <td>{{ $row->stages['test_received'] ?? 0 }}</td>
                    <td>{{ $row->stages['1st_interview'] ?? 0 }}</td>
                    <td>{{ $row->stages['2nd_interview'] ?? 0 }}</td>
                    <td>{{ $row->stages['offer_sent'] ?? 0 }}</td>
                    <td>{{ $row->stages['offer_accepted'] ?? 0 }}</td>
                    <td>{{ $row->stages['joined'] ?? 0 }}</td>
                    <td>{{ $row->stages['rejected'] ?? 0 }}</td>
                    <td>{{ $row->avg_time_to_hire ?? 0 }}d</td>
                    <td style="font-weight: bold;">{{ $row->avg_expected_salary > 0 ? number_format($row->avg_expected_salary) : '-' }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} Loops HR Management System. All rights reserved.
    </div>
</body>
</html>

