<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Room Occupancy Report</title>
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
    <h1>Room Occupancy Report</h1>
    <p class="meta">Generated on: {{ now()->format('F d, Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Room</th><th>Floor</th><th>Capacity</th><th>Occupied</th><th>Available</th><th>Rate</th></tr>
        </thead>
        <tbody>
            @foreach($rooms as $room)
            <tr>
                <td>{{ $room->room_no }}</td>
                <td>{{ $room->floor }}</td>
                <td>{{ $room->total_seats }}</td>
                <td>{{ $room->occupied_seats }}</td>
                <td>{{ $room->available_seats }}</td>
                <td>{{ $room->occupancy_percentage }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
