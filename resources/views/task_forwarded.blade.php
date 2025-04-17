<!DOCTYPE html>
<html>

<head>
    <title>New Task Assigned</title>
</head>

<body>

    <p>This task has been forwarded to you from {{ $forwardtaskDetails['assigned_by_name'] }}.</p>
    <p>Forwarded Date: {{ $forwardtaskDetails['forwarded_date'] }}</p>

    <p>Task Details:</p>
    <ul>
        <li>Task ID: {{ $forwardtaskDetails['task_id'] }}</li>
        <li>Assigned By: {{ $forwardtaskDetails['assigned_by_name'] }}</li>
        <li>Deadline: {{ $forwardtaskDetails['deadline'] }}</li>
        <li>Status: {{ $forwardtaskDetails['status'] }}</li>
    </ul>
</body>

</html>