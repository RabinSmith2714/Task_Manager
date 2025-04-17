<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card bg-secondary">
                    <div class="card-header text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <div id="error-message" class="alert alert-danger d-none"></div>
                        <form id="loginForm">
                            @csrf
                            <div class="mb-3">
                                <label for="faculty_id" class="form-label">Faculty ID</label>
                                <input type="text" name="faculty_id" id="faculty_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();

                const facultyId = $('#faculty_id').val();
                const password = $('#password').val();

                $.ajax({
                    url: "{{ url('/') }}",
                    method: "POST",
                    data: {
                        faculty_id: facultyId,
                        password: password,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        // Redirect to the dashboard or handle success
                        window.location.href = "{{ route('index') }}";
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON?.message || "An error occurred. Please try again.";
                        $('#error-message').text(errorMessage).removeClass('d-none');
                    }
                });
            });
        });
    </script>
</body>
</html>