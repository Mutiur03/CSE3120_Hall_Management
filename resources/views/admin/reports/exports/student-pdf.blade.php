<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #2563eb; color: white; }
        h1 { color: #1e293b; }
        .meta { color: #64748b; font-size: 11px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Student Report</h1>
    <p class="meta">Generated on: {{ now()->format('F d, Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Department</th><th>Session</th><th>Batch</th><th>Gender</th><th>Phone</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->student_id }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->department }}</td>
                <td>{{ $student->session }}</td>
                <td>{{ $student->batch }}</td>
                <td>{{ ucfirst($student->gender) }}</td>
                <td>{{ $student->phone }}</td>
                <td>{{ ucfirst($student->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
