<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #3d524c; color: white; }
        h1 { color: #1e2228; }
        .meta { color: #656b75; font-size: 11px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Student Report</h1>
    <p class="meta">Generated on: {{ now()->format('F d, Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Roll</th><th>Name</th><th>Department</th><th>Session</th><th>Phone</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->roll }}</td>
                <td>{{ $student->user->name }}</td>
                <td>{{ $student->department }}</td>
                <td>{{ $student->academic_session }}</td>
                <td>{{ $student->phone }}</td>
                <td>{{ ucfirst($student->status->value) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
