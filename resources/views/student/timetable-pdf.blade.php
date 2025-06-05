<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #0d6efd;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .day-header {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 16px;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .class-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .class-item:last-child {
            border-bottom: none;
        }
        .class-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .class-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Class Schedule</h1>
        <p>Student: {{ $student->user->name }}</p>
        <p>Department: {{ $student->department->name ?? 'N/A' }}</p>
        <p>Level: {{ $student->level }}</p>
        <p>Generated on: {{ Carbon\Carbon::now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Course</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Lecturer</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                @if($weeklySchedule[$day]->count() > 0)
                    @foreach($weeklySchedule[$day] as $timetable)
                        <tr>
                            <td>{{ ucfirst($day) }}</td>
                            <td>
                                <strong>{{ $timetable->course->course_code ?? 'N/A' }}</strong><br>
                                {{ $timetable->course->course_title ?? 'N/A' }}
                            </td>
                            <td>{{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }} - {{ Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}</td>
                            <td>{{ $timetable->venue ?? 'N/A' }}</td>
                            <td>{{ $timetable->lecturer->user->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ ucfirst($day) }}</td>
                        <td colspan="4" style="text-align: center;">No classes scheduled</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This timetable is subject to change. Please check the online system regularly for updates.</p>
        <p>Affan Student Timetable System &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>