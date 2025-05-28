<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - {{ $lecturer->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .header h1 {
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .header p {
            margin-top: 0;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .day-header {
            background-color: #3498db;
            color: white;
            font-size: 18px;
            padding: 10px 15px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .course-code {
            font-weight: bold;
        }

        .course-title {
            font-size: 0.9em;
            color: #555;
        }

        .time-slot {
            white-space: nowrap;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.8em;
            color: #7f8c8d;
        }

        .no-classes {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
            color: #6c757d;
        }

        @media print {
            body {
                padding: 0;
                font-size: 12pt;
            }

            .no-print {
                display: none;
            }

            .container {
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Weekly Class Schedule</h1>
            <p>Lecturer: {{ $lecturer->user->name }}</p>
            <p>Department: {{ $lecturer->department->name ?? 'Not Assigned' }}</p>
            <p>Generated on: {{ now()->format('F d, Y') }}</p>
        </div>

        <div class="no-print" style="margin-bottom: 20px; text-align: center;">
            <button onclick="window.print();"
                style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print Schedule
            </button>
            <a href="{{ route('lecturer.timetable.index') }}"
                style="margin-left: 10px; padding: 10px 20px; background-color: #7f8c8d; color: white; border: none; border-radius: 4px; text-decoration: none;">
                Back to Timetable
            </a>
        </div>

        @php
            $days = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
            ];
        @endphp

        @foreach ($days as $day => $dayName)
            <div class="day-header">{{ $dayName }}</div>

            @if (isset($weeklySchedule[$day]) && count($weeklySchedule[$day]) > 0)
                <table>
                    <thead>
                        <tr>
                            <th width="15%">Time</th>
                            <th width="25%">Course</th>
                            <th width="20%">Department</th>
                            <th width="10%">Level</th>
                            <th width="15%">Venue</th>
                            <th width="15%">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weeklySchedule[$day] as $timetable)
                            <tr>
                                <td class="time-slot">
                                    {{ \Carbon\Carbon::parse($timetable->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($timetable->end_time)->format('H:i') }}
                                </td>
                                <td>
                                    <div class="course-code">{{ $timetable->course->course_code }}</div>
                                    <div class="course-title">{{ $timetable->course->course_title }}</div>
                                </td>
                                <td>{{ $timetable->department->name }}</td>
                                <td>{{ $timetable->level }}</td>
                                <td>{{ $timetable->venue ?? 'N/A' }}</td>
                                <td>{{ $timetable->notes ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-classes">
                    No classes scheduled for {{ $dayName }}
                </div>
            @endif
        @endforeach

        <div class="footer">
            <p>This timetable is subject to change. Please check regularly for updates.</p>
            <p>© {{ date('Y') }} Affan Student Timetable System</p>
        </div>
    </div>
</body>

</html>
