<!DOCTYPE html>
<html>
<head>
    <title>Worker Export Started</title>
</head>
<body>

    <h2>Worker Export Started</h2>

    <hr>

    <p>
        Your export process has started successfully.
    </p>

    <p>
        <strong>Export Name:</strong>
        {{ $exportData['export_name'] }}
    </p>

    <p>
        <strong>Status:</strong>
        {{ $exportData['export_status'] }}
    </p>

    <p>
        <strong>Started At:</strong>
        {{ $exportData['started_at'] }}
    </p>

</body>
</html>