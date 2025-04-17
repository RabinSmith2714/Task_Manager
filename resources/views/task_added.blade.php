<!DOCTYPE html>
<html>
<head>
    <title>New Task Assigned</title>
</head>
<body>
    <h1>New Task Assigned</h1>
    <p><strong>Title:</strong> {{ $taskDetails['title'] }}</p>
    <p><strong>Description:</strong> {{ $taskDetails['description'] }}</p>
    <p><strong>Assigned Date:</strong> {{ $taskDetails['assigned_date'] }}</p>
    <p><strong>Deadline:</strong> {{ $taskDetails['deadline'] }}</p>
    <p>Kindly check your dashboard for more details.</p>
</body>
</html>
