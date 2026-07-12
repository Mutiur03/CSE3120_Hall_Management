<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dining Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #ca8a04; color: white; }
        h1 { color: #1e293b; }
        .meta { color: #64748b; font-size: 11px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Dining Report - {{ $month }}</h1>
    <p class="meta">Generated on: {{ now()->format('F d, Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Day</th><th>Date</th><th>Breakfast</th><th>Lunch</th><th>Dinner</th><th>Total</th></tr>
        </thead>
        <tbody>
            @foreach($dailyStats as $day => $stat)
            <tr>
                <td>{{ $day }}</td>
                <td>{{ $stat['date'] }}</td>
                <td>{{ $stat['breakfast'] }}</td>
                <td>{{ $stat['lunch'] }}</td>
                <td>{{ $stat['dinner'] }}</td>
                <td><strong>{{ $stat['breakfast'] + $stat['lunch'] + $stat['dinner'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 20px;"><strong>Total Breakfast:</strong> {{ $totalBreakfast }} | <strong>Total Lunch:</strong> {{ $totalLunch }} | <strong>Total Dinner:</strong> {{ $totalDinner }}</p>
</body>
</html>
