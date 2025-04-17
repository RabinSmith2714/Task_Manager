<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deadline Reminder</title>
</head>
<body>
    <h2>Reminder: Task Deadline is Approaching</h2>

    <p><strong>Task ID:</strong> {{ $details['task_id'] }}</p>
    <p><strong>Title:</strong> {{ $details['title'] }}</p>
    <p><strong>Description:</strong> {{ $details['description'] }}</p>
    <p><strong>Assigned Date:</strong> {{ $details['assigned_date'] }}</p>
    <p><strong>Deadline:</strong> {{ $details['deadline'] }}</p>
    <p><strong>Assigned By ID:</strong> {{ $details['assigned_by_id'] }}</p>

    <p>Kindly take the necessary actions before the deadline.</p>
</body>
</html>
