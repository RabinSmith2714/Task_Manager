<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TASK MANAGER</title>
    <link rel="icon" type="image/png" sizes="32x32" href="image/icons/mkce_s.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css" />
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Pie chart -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

    <style>
    .scrollable-menu {
        max-height: 250px;
        overflow-y: auto;
        width: 100%;
    }

    .dropdown {
        position: relative;
        width: 100%;
        /* Ensure full width */
    }

    .dropdown-menu {
        width: 100%;
        /* Same width as button */
        max-height: 250px;
        overflow-y: auto;
    }

    .dropdown-btn {
        width: 100%;
        text-align: left;
        border: 1px solid #ccc;
        padding: 8px;
        cursor: pointer;
        background-color: #fff;
    }

    .dropdown-menu.show {
        display: block !important;
    }

    /* Ensure that dropdown is initially hidden */
    #deptDropdownList,
    #facultyDropdownList {
        display: none;
    }

    /* Show dropdown when .show class is added */
    #deptDropdownList.show,
    #facultyDropdownList.show {
        display: block;
    }

    #deptDropdownBtn {
        white-space: normal;
        /* Allow text to break onto multiple lines */
        word-wrap: break-word;
        max-width: 100%;
    }

    .btn-lightgreen {
        background-color: rgba(11, 196, 125, 0.99);
        /* Light green color */
        border-color: rgba(45, 211, 137, 0.81);
        color: rgb(0, 0, 0);
    }


    .btn-lightgreen:hover {
        background-color: rgba(17, 199, 144, 0.99);
        /* Slightly darker green on hover */
        border-color: rgba(17, 199, 138, 0.99);
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="image/mkce.png" alt="College Logo">
            <img class='s_logo' src="image/mkce_s.png" alt="College Logo">
        </div>
        <div class="menu">
            <a href="{{ route('index') }}" class="menu-item">
                <i class="fas fa-light fa-list-check" style="color: #FFD43B;"></i>
                <span>Task Manager</span>
            </a>
        </div>
    </div>
    <!-- Main Content -->
    <div class="content">
        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
        </div>
        <!-- Topbar -->
        <div class="topbar">
            <div class="hamburger" id="hamburger">
                <i class="fas fa-bars"></i>
            </div>
            <div class="user-profile">
                <div class="user-menu" id="userMenu">
                    <div class="user-avatar">
                        <img src="image/icons/mkce_s.png" alt="User">
                        <div class="online-indicator"></div>
                    </div>
                    <div class="dropdown-menu">
                        <a class="dropdown-item">
                            <i class="fas fa-key"></i>
                            Change Password
                        </a>
                        <a class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
                <span>{{ $facultyName }}</span>
            </div>
        </div>
        <!-- Breadcrumb -->
        <div class="breadcrumb-area">
            <nav aria-label="breadcrumb">
                <ol class="mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Task Manager</li>
                </ol>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="container-fluid">
            <!-- Sample Table -->
            <div id="navref">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <!-- Dashboard (Always Visible) -->
                    <li class="nav-item" role="presentation">
                        <div id="navref1">
                            <button class="nav-link active" id="dash-bus-tab" data-bs-toggle="tab"
                                data-bs-target="#dashboard" type="button" role="tab" aria-controls="home-tab-pane"
                                aria-selected="true">
                                Dashboard
                            </button>
                        </div>
                    </li>

                    <!-- Assigned Task (Only for Principal (0), Management Heads (1), HOD (3)) -->
                    @if ($specialStatus == 0 || $specialStatus == 1 || $specialStatus == 3)
                    <li class="nav-item" role="presentation">
                        <div id="navref2">
                            <button class="nav-link" id="pend-bus-tab" data-bs-toggle="tab"
                                data-bs-target="#assignedtask" type="button" role="tab" aria-controls="profile-tab-pane"
                                aria-selected="false">
                                Assigned Task
                            </button>
                        </div>
                    </li>
                    @endif

                    <!-- My Task (Only for Management Heads (1), Center Heads (2), HOD (3), Faculty (4)) -->
                    @if ($specialStatus == 1 || $specialStatus == 2 || $specialStatus == 3 || $specialStatus == 4)
                    <li class="nav-item" role="presentation">
                        <div id="navref3">
                            <button class="nav-link" id="mytask-bus-tab" data-bs-toggle="tab" data-bs-target="#mytask"
                                type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">
                                My
                                Task
                            </button>
                        </div>
                    </li>
                    @endif

                    <!-- Completed Task (Visible to Everyone) -->
                    <li class="nav-item" role="presentation">
                        <div id="navref4">
                            <button class="nav-link" id="comp-bus-tab" data-bs-toggle="tab" data-bs-target="#completed"
                                type="button" role="tab" aria-controls="disabled-tab-pane" aria-selected="false">
                                Completed Task
                            </button>
                        </div>
                    </li>

                    <!-- History (Visible to Everyone) -->
                    <li class="nav-item" role="presentation">
                        <div id="navref5">
                            <button class="nav-link" id="rej-bus-tab" data-bs-toggle="tab" data-bs-target="#history"
                                type="button" role="tab" aria-controls="disabled-tab-pane" aria-selected="false">
                                History
                            </button>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="container-fluid">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="home-tab"
                        tabindex="0">
                        <div class="p-3 tab-pane active show" id="dashboard" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div id="dashref">
                                        <div class="row">
                                            @if ($specialStatus != 0 && $specialStatus != 4)
                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color:rgb(252, 119, 71);">
                                                    <div class="text-center">
                                                        <i class="fas fa-bell fa-lg"></i>
                                                        <h1>{{ $dashboard_assigned_task }}</h1>
                                                        <small>Assigned Tasks </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color:rgb(241, 74, 74);">
                                                    <div class="text-center">
                                                        <i class="fa fa-tasks fa-lg"></i>
                                                        <h1>{{ $dashboardcombinedTasks }}</h1>
                                                        <small>My Tasks</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color:rgb(70, 160, 70);">
                                                    <div class="text-center">
                                                        <i class="fas fa-check fa-lg"></i>
                                                        <h1>{{ $dashboard_completed_tasks }}</h1>
                                                        <small>Completed Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color: rgb(187, 187, 35);">
                                                    <div class="text-center">
                                                        <i class="fas fa-exclamation fa-lg"></i>
                                                        <h1>{{ $dashboard_overdueTasks }}</h1>
                                                        <small>Overdue Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @elseif($specialStatus == 0)
                                            <div class="mb-4 col-12 col-md-4">
                                                <div class="circle-card" style="background-color:rgb(252, 119, 71);">
                                                    <div class="text-center">
                                                        <i class="fas fa-bell fa-lg"></i>
                                                        <h1>{{ $dashboard_assigned_task }}</h1>
                                                        <small>Assigned Tasks </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-4 col-12 col-md-4">
                                                <div class="circle-card" style="background-color:rgb(70, 160, 70);">
                                                    <div class="text-center">
                                                        <i class="fas fa-check fa-lg"></i>
                                                        <h1>{{ $dashboard_completed_tasks }}</h1>
                                                        <small>Completed Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-4 col-12 col-md-4">
                                                <div class="circle-card" style="background-color: rgb(187, 187, 35);">
                                                    <div class="text-center">
                                                        <i class="fas fa-exclamation fa-lg"></i>
                                                        <h1>{{ $dashboard_overdueTasks }}</h1>
                                                        <small>Overdue Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @elseif($specialStatus == 4)
                                            <div class="mb-4 col-12 col-md-4">
                                                <div class="circle-card" style="background-color:rgb(252, 119, 71);">
                                                    <div class="text-center">
                                                        <i class="fas fa-bell fa-lg"></i>
                                                        <h1>{{ $dashboardcombinedTasks }}</h1>
                                                        <small>My Tasks </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-4 col-12 col-md-4">
                                                <div class="circle-card" style="background-color:rgb(70, 160, 70);">
                                                    <div class="text-center">
                                                        <i class="fas fa-check fa-lg"></i>
                                                        <h1>{{ $dashboard_completed_tasks }}</h1>
                                                        <small>Completed Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-4 col-12 col-md-4">
                                                <div class="circle-card" style="background-color: rgb(187, 187, 35);">
                                                    <div class="text-center">
                                                        <i class="fas fa-exclamation fa-lg"></i>
                                                        <h1>{{ $dashboard_overdueTasks }}</h1>
                                                        <small>Overdue Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div> <!-- Row -->
                                    </div> <!-- #dashref -->
                                </div> <!-- Card Body -->
                            </div> <!-- Card -->
                        </div> <!-- Tab Pane -->
                    </div> <!-- Dashboard -->


                    <!----------Assigned Table -------------------------------------------------------------->
                    <div class="tab-pane fade" id="assignedtask" role="tabpanel" aria-labelledby="contact-tab"
                        tabindex="0">
                        <div class="mb-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addtask">Add
                                Task</button>
                        </div>
                        <div class="custom-table table-responsive">
                            <table class="table mb-0 table-hover " id="assignedtask1">
                                <thead class="gradient-header">
                                    <tr>
                                        <th class="text-center">S No</th>
                                        <th class="text-center">Task ID</th>
                                        <th class="text-center">Title</th>
                                        <th class="text-center">Task description</th>
                                        <th class="text-center">Assigned Faculty</th>
                                        <th class="text-center">Deadline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $sno = 1;
                                    $currentTaskId = null;
                                    @endphp
                                    @foreach ($assigned_task as $at)
                                    @if ($currentTaskId !== $at->task_id)
                                    @php
                                    $currentTaskId = $at->task_id;
                                    $taskDeadline = \Carbon\Carbon::parse($at->deadline)->startOfDay();
                                    $isDeadlineExtended = $taskDeadline->lessThan($currentDate);
                                    $isToday = $taskDeadline->equalTo($currentDate);
                                    @endphp
                                    <tr
                                        class="{{ $isDeadlineExtended ? 'table-danger' : ($isToday ? 'table-warning' : '') }}">
                                        <td class="text-center">{{ $sno++ }}</td>
                                        <td class="text-center"
                                            rowspan="{{ $assigned_task->where('task_id', $currentTaskId)->count() }}">
                                            {{ $at->task_id }}
                                        </td>
                                        <td class="text-center">{{ $at->title }}</td>
                                        <td class="text-center">{{ $at->description }}</td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn {{ $at->faculty_count > 0 ? 'btn-lightgreen' : 'btn-info' }} showAssignedFaculty"
                                                value="{{ $at->task_id }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-title="Click to view">
                                                View @if ($at->faculty_count > 0)
                                                ({{ $at->faculty_count }})
                                                @endif
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($at->deadline)->format('d-m-Y') }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--------------------------- Add Task Modal ----------------------------------------------->
                    <div class="modal fade" id="addtask" tabindex="-1" aria-labelledby="exampleModalLabel">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Task</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addtaskform" enctype="multipart/form-data">
                                        <input type="hidden" id="hidden_faculty_id" value="{{ $facultyId }}"
                                            name="faculty_id">
                                        <input type="hidden" id="hidden_faculty_name" value="{{ $facultyName }}"
                                            name="faculty_name">
                                        <input type="hidden" id="Role" value="{{ $Role }}" name="Role">
                                        <input type="hidden" id="specialStatus" value="{{ $specialStatus }}"
                                            name="specialStatus">
                                        @if ($specialStatus == 0 && $Role == 'Principal')

                                        <div class="mb-3">
                                            <label for="workType" class="form-label">Type of Role</label>
                                            <select class="form-control" id="workType" name="workType"
                                                onchange="showDropdown()" required>
                                                <option value="">Select</option>
                                                <option value="Management">Management</option>
                                                <option value="center of head">Center of Head</option>
                                                <option value="hod">Head of the Department</option>
                                                <option value="faculty">Faculty</option>
                                            </select>
                                        </div>

                                        <div class="mb-3" id="managementDropdown" style="display: none;">
                                            <label for="researchType" class="form-label">Management</label>
                                            <select class="form-control" id="researchType" name="researchType">
                                                <option value="">Select</option>
                                                @foreach ($management as $m)
                                                <option value="{{ $m->id }}">{{ $m->Role }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class=" mb-3" id="cohDropdown" style="display: none;">
                                            <label for="teachingSubject" class="form-label">Center of
                                                Heads</label>
                                            <select class="form-control" id="teachingSubject" name="teachingSubject">
                                                <option value="">Select</option>
                                                @foreach ($centerofheads as $c)
                                                <option value="{{ $c->id }}">{{ $c->Role }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3" id="departmentDropdown" style="display: none;">
                                            <label for="department" class="form-label">Department</label>
                                            <div class="dropdown">
                                                <button class="form-control text-start dropdown-toggle" type="button"
                                                    id="deptDropdownBtn" data-bs-toggle="dropdown">
                                                    Select Department
                                                </button>
                                                <ul class="dropdown-menu scrollable-menu" id="deptDropdownList">
                                                    @foreach ($dept as $d)
                                                    <li>
                                                        <label class="dropdown-item">
                                                            <input type="checkbox" class="dept-checkbox"
                                                                value="{{ $d->dname }}">
                                                            {{ $d->dname }}
                                                        </label>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <input type="hidden" name="selectedDepartments" id="selectedDepartments">
                                        </div>

                                        <div class="mb-3" id="facultyDropdown" style="display: none;">
                                            <label for="faculty" class="form-label">Faculty</label>
                                            <div class="dropdown">
                                                <button class="form-control text-start dropdown-toggle" type="button"
                                                    id="facultyDropdownBtn" data-bs-toggle="dropdown">
                                                    Select Faculty
                                                </button>
                                                <ul class="dropdown-menu scrollable-menu" id="facultyDropdownList">
                                                    <li><label class="dropdown-item">Select Faculty</label></li>
                                                </ul>
                                            </div>
                                            <input type="hidden" name="selectedFaculties" id="selectedFaculties">
                                        </div>
                                        @elseif($specialStatus == 1 && $Role == 'student affiars')
                                        <div class="mb-3">
                                            <label for="studentaffiars" class="form-label">select Head</label>
                                            <select class="form-control" id="studentaffiars" name="studentaffiars"
                                                required>
                                                <option value="">Select</option>
                                                @foreach ($studentaffiars as $sa)
                                                <option value="{{ $sa->id }}">{{ $sa->Role }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @elseif($specialStatus == 3 && $Role == 'head of department')
                                        <div class="mb-3">
                                            <label for="newFaculty" class="form-label">Select Faculty</label>
                                            <button type="button" class="form-control text-start dropdown-toggle"
                                                data-bs-toggle="dropdown" id="newFacultyBtn">Select</button>
                                            <ul class="dropdown-menu" id="newFacultyDropdown">
                                                @foreach ($departmentfaculties as $df)
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" class="deptfaculty-checkbox"
                                                            value="{{ $df->id }}"
                                                            onchange="updateSelecteddepartmentFaculties()">
                                                        {{ $df->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                            <input type="hidden" name="selecteddeptFaculties"
                                                id="selecteddeptFaculties">
                                        </div>
                                        @endif
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input class="form-control" type="text" id="title" name="title"
                                                placeholder="Enter Title" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Task Description</label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                placeholder="Enter Description" required>
                                        </div>

                                        <input type="hidden" name="status" value="0">

                                        <div class="mb-3">
                                            <label for="level" class="form-label">Complexity Level</label>
                                            <select class="form-control" id="level" name="level" required>
                                                <option value="">Select</option>
                                                <option value="Easy">Easy</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Hard">Hard</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="deadline" class="form-label">Deadline</label>
                                            <input type="date" class="form-control" name="deadline" id="deadline"
                                                required>
                                        </div>

                                        <input type="hidden" class="form-control" name="assigned_date"
                                            id="assigned_date" required>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary"
                                                id="submitDepartments">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!----------------------- My Task Starts ------------------------------------->
                    <div class="tab-pane fade" id="mytask" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @if ($specialStatus != 4)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="work-bus-tab" data-bs-toggle="tab"
                                    data-bs-target="#home-tab-pane" type="button" role="tab"
                                    aria-controls="home-tab-pane" aria-selected="true">My task</button>
                            </li>
                            @endif
                            @if ($specialStatus != 4)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="work-bus-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-tab-pane" type="button" role="tab"
                                    aria-controls="profile-tab-pane" aria-selected="false">Forwarded
                                    task</button>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel"
                                aria-labelledby="home-tab" tabindex="0">
                                <div class="custom-table table-responsive">
                                    <div class="custom-table table-responsive">
                                        <table class="table mb-0 table-hover " id="mytask1">
                                            <thead class="gradient-header">
                                                <tr>
                                                    <th class="text-center">S.No</th>
                                                    <th class="text-center">Task ID</th>
                                                    <th class="text-center">Assigned by Faculty</th>
                                                    <th class="text-center">Title</th>
                                                    <th class="text-center">Task Description</th>
                                                    <th class="text-center">Action ahead</th>
                                                    <th class="text-center">Deadline</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $sno = 1 @endphp
                                                @foreach ($combinedTasks as $my)
                                                @php
                                                $task_Deadline = \Carbon\Carbon::parse(
                                                $my->deadline,
                                                )->startOfDay();
                                                $isToday = $task_Deadline->equalTo($currentDate);
                                                $isDeadlineExtended = $task_Deadline->lessThan($currentDate);
                                                $reasonExist = !is_null($my->reason);
                                                $feedbackExist = isset($my->feedback);
                                                $currentDate = \Carbon\Carbon::now()->toDateString();
                                                @endphp
                                                <tr
                                                    class="{{ $isDeadlineExtended ? 'table-danger' : ($isToday ? 'table-warning' : '') }}">
                                                    <td class="text-center">{{ $sno++ }}</td>
                                                    <td class="text-center">{{ $my->task_id }}</td>
                                                    <td class="text-center">{{ $my->assigned_by_name }}</td>
                                                    <td class="text-center">{{ $my->title }}</td>
                                                    <td class="text-center">{{ $my->description }}</td>
                                                    <td class="text-center">
                                                        @if (($specialStatus == 3 && $my->status == 0) ||
                                                        ($specialStatus == 4 && $my->status == 0))
                                                        <button type="button" class="btn btn-success btnaccept"
                                                            value="{{ $my->task_id }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>

                                                        @elseif(

                                                        ($specialStatus == 1 && ($my->status == 0 ||$my->status == 4) )
                                                        ||
                                                        ($specialStatus == 2 && ($my->status == 0 ||$my->status == 4))
                                                        ||
                                                        ($specialStatus == 3 && ($my->status == 1||$my->status == 4))&&
                                                        $my->deadline >= $currentDate)
                                                        <button type="button" class="btn btn-primary showImage"
                                                            data-bs-toggle="modal" data-bs-target="#forwardModal"
                                                            data-task-id="{{ $my->task_id }}"
                                                            data-status="{{ $my->status }}"
                                                            data-deadline="{{ $my->deadline }}">
                                                            <!-- Add the deadline as a data attribute -->
                                                            <i class="fas fa-share"></i>
                                                        </button>
                                                        @endif
                                                        @if (($specialStatus == 3 && ($my->status == 1 ||$my->status ==
                                                        4)) || ($specialStatus == 4 && ($my->status == 1 ||$my->status
                                                        == 4)))
                                                        <button type="button" class="btn btn-success btncomplete"
                                                            value="{{ $my->task_id }}">
                                                            <i class="fas fa-solid fa-thumbs-up"></i>
                                                        </button>
                                                        @endif
                                                        @if (($specialStatus == 3 && $my->status == 2) ||
                                                        ($specialStatus == 4 && $my->status == 2))
                                                        <button type="button" class="btn btn-secondary "
                                                            value="{{ $my->task_id }}" disabled>
                                                            <i class="fas fa-solid fa-thumbs-up"></i>
                                                        </button>
                                                        @endif
                                                        @if ($reasonExist)
                                                        <button type="button" class="btn btn-secondary btnmyreason"
                                                            value="{{ $my->task_id }}">
                                                            <i class="fas fa-light fa-message"></i>
                                                        </button>
                                                        @endif
                                                        @if ($my->status == 1||$my->status == 4)
                                                        <button type="button" class="btn btn-danger btnextend"
                                                            value="{{ $my->task_id }}" @disabled(!empty($my->feedback))>
                                                            <i class="fas fa-solid fa-calendar-days"></i>
                                                        </button>
                                                        @endif



                                                    </td>
                                                    <td class="text-center">
                                                        {{ \Carbon\Carbon::parse($my->deadline)->format('d-m-Y') }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel"
                                aria-labelledby="profile-tab" tabindex="0">
                                <div class="custom-table table-responsive">
                                    <table class="table mb-0 table-hover " id="mytask2">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by </th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Task Description</th>
                                                <th class="text-center">Assigned to</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $sno = 1;
                                            $current_TaskId = null;
                                            @endphp
                                            @foreach ($forwarded_task as $for)
                                            @if ($current_TaskId !== $for->task_id)
                                            @php $current_TaskId = $for->task_id;
                                            $taskDeadline = \Carbon\Carbon::parse(
                                            $for->deadline,
                                            )->startOfDay();
                                            $isDeadlineExtended = $taskDeadline->lessThan($currentDate);
                                            $isToday = $taskDeadline->equalTo($currentDate);
                                            @endphp
                                            @endif
                                            @php
                                            $reasonExist = !is_null($for->reason);
                                            @endphp
                                            <tr
                                                class="{{ $isDeadlineExtended ? 'table-danger' : ($isToday ? 'table-warning' : '') }}">
                                                <td class="text-center">{{ $sno++ }}</td>
                                                <td class="text-center">{{ $for->task_id }}</td>
                                                <td class="text-center">{{ $for->assigned_by_id }} -
                                                    {{ $for->assigned_by_name }} </td>
                                                <td class="text-center">{{ $for->title }}</td>
                                                <td class="text-center">{{ $for->description }}</td>
                                                <td class="text-center"><button type="button"
                                                        class="btn btn-info showForwardedFaculty"
                                                        value="{{ $for->task_id }}-{{ $for->assigned_by_id }}-{{ $for->deadline}}->format('d-m-y')"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-title="Click to approve">View</button>
                                                    @if ($reasonExist)
                                                    <button type="button" class="btn btn-secondary btnforwardmyreason"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#forwardreasonDisplayModal"
                                                        value="{{ $for->reason }}">
                                                        <i class="fas fa-light fa-message"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($for->deadline)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!----------------------- My Task Ends ------------------------------------->

                    <!----------------------- forward modal ------------------------------------->
                    <div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Forward Task</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="forwardform" enctype="multipart/form-data">
                                        <input type="hidden" name="task_id" id="task_id" value="">
                                        <input type="hidden" name="status" id="status" value="">
                                        <input type="hidden" id="hidden_faculty_id" value="{{ $facultyId }}"
                                            name="faculty_id">
                                        <input type="hidden" id="hidden_faculty_name" value="{{ $facultyName }}"
                                            name="faculty_name">
                                        <input type="hidden" id="type" value="{{ $Type }}" name="type">
                                        <input type="hidden" id="role" value="{{ $Role }}" name="role">


                                        @if ($specialStatus == 1 && $Role == 'student affiars')
                                        <div class="mb-3">
                                            <label for="fstudentaffiars" class="form-label">select Head</label>
                                            <select class="form-control" id="fstudentaffiars" name="fstudentaffiars"
                                                required>
                                                <option value="">Select</option>
                                                @foreach ($studentaffiars as $sa)
                                                <option value="{{ $sa->id }}">{{ $sa->Role }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @elseif($specialStatus == 3 && $Role == 'head of department')
                                        <div class="mb-3">
                                            <label for="fnewFaculty" class="form-label">Select Faculty</label>
                                            <button type="button" class="form-control text-start dropdown-toggle"
                                                data-bs-toggle="dropdown" id="fnewFacultyBtn">Select</button>
                                            <ul class="dropdown-menu" id="fnewFacultyDropdown">
                                                @foreach ($departmentfaculties as $df)
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" class="fdeptfaculty-checkbox"
                                                            value="{{ $df->id }}"
                                                            onchange="updateSelectedforwarddepartmentFaculties()">
                                                        {{ $df->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                            <input type="hidden" name="selectedforwarddeptFaculties"
                                                id="selectedforwarddeptFaculties">
                                        </div>
                                        @elseif($specialStatus == 2 && $Type == 'center of heads')
                                        <div class="mb-3">
                                            <label for="coordinators" class="form-label">Select Faculty</label>
                                            <button type="button" class="form-control text-start dropdown-toggle"
                                                data-bs-toggle="dropdown" id="coordinatorBtn">Select</button>
                                            <ul class="dropdown-menu" id="coordinatorDropdown">
                                                @foreach ($coordinators as $c)
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" class="coordinator-checkbox"
                                                            value="{{ $c->id }}"
                                                            onchange="updateSelectedcoordinators()">
                                                        {{ $c->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                            <input type="hidden" name="selectedcoordinators" id="selectedcoordinators">
                                        </div>


                                        @endif
                                        <div class="mb-3">
                                            <label for="deadline" class="form-label">Deadline</label>
                                            <input type="date" class="form-control" name="forwarddeadline"
                                                id="forwarddeadline" required>
                                        </div>

                                        <input type="hidden" class="form-control" name="forwarded_date"
                                            id="forwarded_date" required>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary"
                                                id="submitDepartments">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!----------------------------Completed Task starts ---------------------------------------------------->

                    @if ($specialStatus == 3 || $specialStatus == 4)
                    <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="disabled-tab"
                        tabindex="0">

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @if ($specialStatus != 4)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="completed-bus-tab" data-bs-toggle="tab"
                                    data-bs-target="#homeu-tab-pane" type="button" role="tab"
                                    aria-controls="home-tab-pane" aria-selected="true">My tasks</button>
                                @endif
                            </li>
                            @if ($specialStatus == 3)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-bus-tab" data-bs-toggle="tab"
                                    data-bs-target="#profileu-tab-pane" type="button" role="tab"
                                    aria-controls="profile-tab-pane" aria-selected="false">Assigned
                                    task</button>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content" id="myTabContent">

                            <div class="tab-pane fade show active" id="homeu-tab-pane" role="tabpanel"
                                aria-labelledby="home-tab" tabindex="0">
                                <div class="custom-table table-responsive">
                                    <table class="table mb-0 table-hover " id="completed1">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by Faculty</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Task Description</th>
                                                <th class="text-center">Date of completion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $sno = 1 @endphp
                                            @foreach ($completed_my_task as $ct)
                                            <tr>
                                                <td class="text-center">{{ $sno++ }}</td>
                                                <td class="text-center">{{ $ct->task_id }}</td>
                                                <td class="text-center">{{ $ct->assigned_by_name }}</td>
                                                <td class="text-center">{{ $ct->title }}</td>
                                                <td class="text-center">{{ $ct->description }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($ct->completed_date)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if ($specialStatus == 3)
                            <div class="tab-pane fade " id="profileu-tab-pane" role="tabpanel"
                                aria-labelledby="profile-tab" tabindex="0">
                                <div class="custom-table table-responsive">
                                    <table class="table mb-0 table-hover " id="completed2">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Task description</th>
                                                <th class="text-center">Assigned Faculty</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $sno = 1 @endphp
                                            @foreach ($completed_assigntask as $at)
                                            <tr>
                                                <td class="text-center">{{ $sno++ }}</td>
                                                <td class="text-center">{{ $at->task_id }}</td>
                                                <td class="text-center">{{ $at->title }}</td>
                                                <td class="text-center">{{ $at->description }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info CshowAssignedFaculty"
                                                        value="{{ $at->task_id }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        data-bs-title="Click to approve">View</button>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($at->deadline)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @elseif ($specialStatus == 0 || $specialStatus == 1 || $specialStatus == 2)
                    <div class="tab-pane fade " id="completed" role="tabpanel" aria-labelledby="disabled-tab"
                        tabindex="0">

                        <div class="custom-table table-responsive">
                            <table class="table mb-0 table-hover " id="completed2">
                                <thead class="gradient-header">
                                    <tr>
                                        <th class="text-center">S No</th>
                                        <th class="text-center">Task ID</th>
                                        <th class="text-center">Title</th>
                                        <th class="text-center">Task description</th>
                                        <th class="text-center">Assigned Faculty</th>
                                        <th class="text-center">Deadline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sno = 1 @endphp
                                    @foreach ($completed_assigntask as $at)
                                    <tr>
                                        <td class="text-center">{{ $sno++ }}</td>
                                        <td class="text-center">{{ $at->task_id }}</td>
                                        <td class="text-center">{{ $at->title }}</td>
                                        <td class="text-center">{{ $at->description }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info CshowAssignedFaculty"
                                                value="{{ $at->task_id }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-title="Click to approve">View</button>
                                        </td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($at->deadline)->format('d-m-Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                    @endif



                    <!----------------------------Completed Task Table Ends ------------------------------------->

                    <!----------------------------History Table starts ------------------------------------->

                    @if($specialStatus==0)
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
                        <div class="filter-container-inline">
                            <label for="start_date" class="filter-label-inline">Start Date:</label>
                            <input type="date" id="start_date" class="filter-input-inline">

                            <label for="end_date" class="filter-label-inline">End Date:</label>
                            <input type="date" id="end_date" class="filter-input-inline">

                            <label for="select1" class="filter-label-inline">Select Type/Role:</label>
                            <select id="select1" class="filter-input-inline">
                                <option value="">--Select--</option> <!-- Placeholder -->
                            </select>

                            <div id="select2-container" style="display: none;">
                                <label id="label2" for="select2" class="filter-label-inline"></label>
                                <select id="select2" class="filter-input-inline">
                                    <option value="">--Select--</option>
                                </select>
                            </div>

                            <div id="faculty-container" style="display: none;">
                                <label for="faculty_name" class="filter-label-inline">Select Faculty:</label>
                                <select id="faculty_name" class="filter-input-inline">
                                    <option value="">--Select--</option>
                                </select>
                            </div>

                            <button id="filter_button" class="filter-button-inline">Filter</button>
                        </div>


                        <div id="tabsContainer" class="hidden mt-4">
                            <ul class="nav nav-tabs custom-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#ongoingTab">Ongoing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#overdueTab">Overdue</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#demeritsTab"> Demerits</a>
                                </li>

                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3">
                                <div class="tab-pane fade show active" id="ongoingTab">
                                    <table class="table mb-0 table-hover" id="hist1">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="overdueTab">
                                    <table class="table mb-0 table-hover" id="hist2">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>


                                <!-- Tab Content -->

                                <div class="tab-pane fade" id="demeritsTab">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="card custom-card" style="max-width: 20rem;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Demerit</h5>
                                                <p class="card-text display-4 fw-bold"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Demerit Table -->
                                    <table class="table mb-0 table-hover" id="hist3">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Level of Completion</th>
                                                <th class="text-center">Demerit Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                        </div>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const select1 = document.getElementById("select1");
                        const select2 = document.getElementById("select2");
                        const facultySelect = document.getElementById("faculty_name");
                        const tabsContainer = document.getElementById("tabsContainer");
                        const filterButton = document.getElementById("filter_button");

                        const select2Container = document.getElementById("select2-container");
                        const facultyContainer = document.getElementById("faculty-container");

                        select2Container.style.display = "none";
                        facultyContainer.style.display = "none";
                        tabsContainer.style.display = "none";

                        fetch("/get-types")
                            .then(response => response.json())
                            .then(data => {
                                select1.innerHTML = '<option value="">--Select--</option>';
                                [...data.types, ...data.roles].forEach(optionValue => {
                                    if (optionValue.trim()) {
                                        let option = document.createElement("option");
                                        option.value = optionValue;
                                        option.textContent = optionValue;
                                        select1.appendChild(option);
                                    }
                                });
                            })
                            .catch(error => console.error("Error fetching types/roles:", error));

                        select1.addEventListener("change", () => {
                            const role = select1.value;
                            updateLabel(role);
                            select2Container.style.display = role ? "block" : "none";
                            facultyContainer.style.display = "none";

                            if (!role) return;

                            fetch(`/get-roles?type=${role}`)
                                .then(response => response.json())
                                .then(data => {
                                    select2.innerHTML = '<option value="">--Select--</option>';
                                    data.options.forEach(optionValue => {
                                        let option = document.createElement("option");
                                        option.value = optionValue;
                                        option.textContent = optionValue;
                                        select2.appendChild(option);
                                    });
                                })
                                .catch(error => console.error("Error fetching roles/departments:",
                                    error));
                        });

                        select2.addEventListener("change", () => {
                            const department = select2.value;
                            const role = select1.value;

                            facultyContainer.style.display = (role.toLowerCase() === "faculty" &&
                                department) ? "block" : "none";

                            if (!department || !role) {
                                facultySelect.innerHTML = '<option value="">Select Faculty</option>';
                                return;
                            }

                            fetch(`/faculty-data?department=${department}&role=${role}`)
                                .then(response => response.json())
                                .then(data => {
                                    facultySelect.innerHTML =
                                        '<option value="">--Select--</option>';
                                    if (["HOD", "management", "center of heads"].includes(role) &&
                                        data.faculty.length > 0) {
                                        facultySelect.innerHTML =
                                            `<option value="${data.faculty[0].id}" selected>${data.faculty[0].name}</option>`;
                                    } else {
                                        data.faculty.forEach(faculty => {
                                            let option = document.createElement("option");
                                            option.value = faculty.id;
                                            option.textContent = faculty.name;
                                            facultySelect.appendChild(option);
                                        });
                                    }
                                })
                                .catch(error => console.error("Error fetching faculty data:", error));
                        });


                        filterButton.addEventListener("click", () => {
                            const startDate = document.getElementById("start_date").value;
                            const endDate = document.getElementById("end_date").value;
                            const department = select2.value;
                            const role = select1.value;
                            const facultyId = facultySelect.value;

                            if (!startDate || !endDate) {
                                alertify.error("Please select a valid date range.");
                                return;
                            }

                            if (!department || !role || !facultyId) {
                                alertify.error("Please select all details before proceeding.");
                                return;
                            }

                            tabsContainer.style.display = "block";
                            document.querySelector('.nav-link[href="#ongoingTab"]').click();

                            fetch(
                                    `/filter-tasks?start_date=${startDate}&end_date=${endDate}&department=${department}&role=${role}&faculty_id=${facultyId}`
                                )
                                .then(response => response.json())
                                .then(data => {
                                    const hasOngoing = populateTable("hist1", data.ongoingTasks, 7);
                                    const hasOverdue = populateTable("hist2", data.overdueTasks, 7);
                                    const hasDemerit = populateTable("hist3", data.demeritTasks, 6);
                                    document.querySelector(".card-text.display-4").textContent =
                                        data.totalDemeritPoints || "0";

                                    tabsContainer.style.display = "block";

                                })
                                .catch(error => console.error("Error fetching filtered tasks:", error));
                        });

                        function updateLabel(role) {
                            const s2Label = document.getElementById("label2");
                            if (["management", "center of heads"].includes(role)) {
                                s2Label.textContent = role;
                                select2Container.style.display = "block";
                                facultyContainer.style.display = "none";
                            } else if (["Faculty", "HOD"].includes(role)) {
                                s2Label.textContent = "Select Department:";
                                select2Container.style.display = "block";
                            } else {
                                select2Container.style.display = "none";
                                facultyContainer.style.display = "none";
                            }
                        }

                        function populateTable(tableId, data, columns) {
                            const table = $(`#${tableId}`).DataTable();
                            table.clear().draw();

                            if (!data || data.length === 0) return false;

                            const seenTaskIds = new Set();

                            data.forEach((item, index) => {
                                if (seenTaskIds.has(item.task_id)) return;
                                seenTaskIds.add(item.task_id);

                                const rowData = [
                                    index + 1,
                                    item.task_id || "-",
                                    ...(columns === 7 ? [item.assigned_by_name || "-"] : []),
                                    item.title || "-",
                                    item.description || "-",
                                    item.complexity_level || "-",
                                    columns === 7 ? (item.deadline ? formatDate(item.deadline) :
                                        "-") : (item.demerit_points || "0")
                                ];

                                table.row.add(rowData);
                            });

                            table.draw();
                            return true;
                        }

                        function initializeDataTable(tableId) {
                            $(`#${tableId}`).DataTable({
                                destroy: true,
                                paging: true,
                                searching: true,
                                info: true,
                                lengthChange: true
                            });
                        }

                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            return `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
                        }

                        initializeDataTable("hist1");
                        initializeDataTable("hist2");
                        initializeDataTable("hist3");
                    });
                    </script>



                    @elseif($specialStatus == 1 && $Role == 'student affairs')
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
                        <div class="filter-container-inline">
                            <label for="start_date" class="filter-label-inline">Start Date:</label>
                            <input type="date" id="start_date" class="filter-input-inline">

                            <label for="end_date" class="filter-label-inline">End Date:</label>
                            <input type="date" id="end_date" class="filter-input-inline">
                            <label for="studentaffiars" class="filter-label-inline">Select Head</label>
                            <select class="filter-input-inline" id="hstudentaffiars" name="studentaffiars" required>
                                <option value="">Select</option>
                                @foreach($studentaffiars as $sa)
                                <option value="{{ $sa->id }}">{{ $sa->Role }}</option>
                                @endforeach
                            </select>
                            <button id="filter_button" class="filter-button-inline">Filter</button>
                        </div>

                        <div id="tabsContainer" class="hidden mt-4">
                            <ul class="nav nav-tabs custom-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#ongoingTab">Ongoing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#overdueTab">Overdue</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#demeritTab">Demerit</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <div class="tab-pane fade show active" id="ongoingTab">
                                    <table class="table mb-0 table-hover" id="hist1">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity Level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="overdueTab">
                                    <table class="table mb-0 table-hover" id="hist2">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity Level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade " id="demeritTab">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="card custom-card" style="max-width: 20rem;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Demerit</h5>
                                                <p class="card-text display-4 fw-bold">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table mb-0 table-hover" id="hist3">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity Level</th>
                                                <th class="text-center">Demerit points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        // Hide tabs initially
                        document.getElementById("tabsContainer").style.display = "none";
                    });

                    document.getElementById("filter_button").addEventListener("click", () => {
                        const startDate = document.getElementById("start_date").value;
                        const endDate = document.getElementById("end_date").value;
                        const studentAffairsId = document.getElementById("hstudentaffiars").value;
                        const tabsContainer = document.getElementById("tabsContainer");

                        if (!startDate || !endDate) {
                            alertify.error("Please select a valid date range.");
                            return;
                        }

                        if (!studentAffairsId) {
                            alertify.error("Please select a head.");
                            return;
                        }

                        // Hide tabs while fetching data
                        tabsContainer.style.display = "none";
                        document.querySelector('.nav-link[href="#ongoingTab"]').click();

                        fetch(
                                `/fetch-tasks?start_date=${startDate}&end_date=${endDate}&student_affairs_id=${studentAffairsId}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                console.log("Fetched Data:", data); // Debugging

                                const hasOngoing = populateTable("hist1", data.ongoingTasks, 7);
                                const hasOverdue = populateTable("hist2", data.overdueTasks, 7);
                                const hasDemerit = populateTable("hist3", data.demeritTasks, 6);

                                document.querySelector(".card-text.display-4").textContent = data
                                    .totalDemeritPoints || "0";

                                tabsContainer.style.display = "block";

                            })
                            .catch(error => {
                                console.error("Error fetching tasks:", error);
                                alertify.error("Failed to fetch data. Please try again.");
                            });
                    });

                    function populateTable(tableId, data, columns) {
                        const table = $(`#${tableId}`).DataTable();
                        table.clear().draw();

                        if (!data || data.length === 0) {
                            return false; // No data available
                        }

                        const seenTaskIds = new Set();

                        data.forEach((item, index) => {
                            if (seenTaskIds.has(item.task_id)) return;
                            seenTaskIds.add(item.task_id);

                            const rowData = [
                                index + 1,
                                item.task_id || "-",
                                ...(columns === 7 ? [item.assigned_by_name || "-"] : []),
                                item.title || "-",
                                item.description || "-",
                                item.complexity_level || "-",
                                columns === 7 ? (item.deadline ? formatDate(item.deadline) : "-") : (item
                                    .demerit_points || "0")
                            ];

                            table.row.add(rowData);
                        });

                        table.draw();
                        return true;
                    }

                    function initializeDataTable(tableId) {
                        $(`#${tableId}`).DataTable({
                            destroy: true,
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true
                        });
                    }

                    function formatDate(dateString) {
                        const date = new Date(dateString);
                        return `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
                    }
                    </script>




                    @elseif($specialStatus == 2)
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
                        <div class="filter-container-inline">
                            <label for="start_date" class="filter-label-inline">Start Date:</label>
                            <input type="date" id="start_date" class="filter-input-inline">

                            <label for="end_date" class="filter-label-inline">End Date:</label>
                            <input type="date" id="end_date" class="filter-input-inline">

                            <div id="select2-container">
                                <label id="label2" for="select2" class="filter-label-inline">Select Faculty</label>
                                <select id="select2" class="filter-input-inline">
                                    <option value="">--Select--</option>
                                    @foreach($heads as $head)
                                    <option value="{{ $head->id }}">{{ $head->name }} ({{ $head->dept }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <button id="filter_button" class="filter-button-inline">Filter</button>
                        </div>

                        <div id="tabsContainer" style="display: none;" class="mt-4">
                            <ul class="nav nav-tabs custom-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#ongoingTab">Ongoing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#overdueTab">Overdue</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#demeritsTab">Demerits</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <div class="tab-pane fade show active" id="ongoingTab">
                                    <table class="table mb-0 table-hover" id="hist1">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="overdueTab">
                                    <table class="table mb-0 table-hover" id="hist2">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <div class="tab-pane fade" id="demeritsTab">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="card custom-card" style="max-width: 20rem;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Demerit</h5>
                                                <p class="card-text display-4 fw-bold"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table mb-0 table-hover" id="hist3">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Level of Completion</th>
                                                <th class="text-center">Demerit Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        // Hide tabs initially
                        document.getElementById("tabsContainer").style.display = "none";
                    });

                    document.getElementById("filter_button").addEventListener("click", () => {
                        const startDate = document.getElementById("start_date").value;
                        const endDate = document.getElementById("end_date").value;
                        const facultyId = document.getElementById("select2").value;
                        const tabsContainer = document.getElementById("tabsContainer");

                        if (!startDate || !endDate) {
                            alertify.error("Please select a valid date range.");
                            return;
                        }

                        if (!facultyId) {
                            alertify.error("Please select a Center of Head.");
                            return;
                        }

                        // Hide tabs while fetching data
                        tabsContainer.style.display = "none";
                        document.querySelector('.nav-link[href="#ongoingTab"]').click();

                        fetch(
                                `/filter-tasks?start_date=${startDate}&end_date=${endDate}&faculty_id=${facultyId}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                console.log("Fetched Data:", data); // Debugging

                                const hasOngoing = populateTable("hist1", data.ongoingTasks, 7);
                                const hasOverdue = populateTable("hist2", data.overdueTasks, 7);
                                const hasDemerit = populateTable("hist3", data.demeritTasks, 6);

                                document.querySelector(".card-text.display-4").textContent = data
                                    .totalDemeritPoints || "0";

                                tabsContainer.style.display = "block";

                            })
                            .catch(error => {
                                console.error("Error fetching tasks:", error);
                                alertify.error("An error occurred while fetching data.");
                            });
                    });

                    function populateTable(tableId, data, columns) {
                        const table = $(`#${tableId}`).DataTable();
                        table.clear().draw();

                        if (!data || data.length === 0) {
                            return false; // No data available
                        }

                        const seenTaskIds = new Set();

                        data.forEach((item, index) => {
                            if (seenTaskIds.has(item.task_id)) return;
                            seenTaskIds.add(item.task_id);

                            const rowData = [
                                index + 1,
                                item.task_id || "-",
                                ...(columns === 7 ? [item.assigned_by_name || "-"] : []),
                                item.title || "-",
                                item.description || "-",
                                item.complexity_level || "-",
                                columns === 7 ? (item.deadline ? formatDate(item.deadline) : "-") : (item
                                    .demerit_points || "0")
                            ];

                            table.row.add(rowData);
                        });

                        table.draw();
                        return true;
                    }

                    function initializeDataTable(tableId) {
                        $(`#${tableId}`).DataTable({
                            destroy: true,
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true
                        });
                    }

                    function formatDate(dateString) {
                        const date = new Date(dateString);
                        return `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
                    }
                    </script>




                    @elseif($specialStatus == 3)
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
                        <div class="filter-container-inline">
                            <label for="start_date" class="filter-label-inline">Start Date:</label>
                            <input type="date" id="start_date" class="filter-input-inline">

                            <label for="end_date" class="filter-label-inline">End Date:</label>
                            <input type="date" id="end_date" class="filter-input-inline">

                            <label for="faculty_select" class="filter-label-inline">Select Faculty:</label>
                            <select id="faculty_select" class="filter-input-inline">
                                <option value="">Select Faculty</option>
                                @foreach($facultyList as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>

                            <button id="filter_button" class="filter-button-inline">Filter</button>
                        </div>

                        <div id="tabsContainer" class="hidden mt-4">
                            <ul class="nav nav-tabs custom-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#ongoingTab">Ongoing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#overdueTab">Overdue</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#demeritTab">Demerit</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <div class="tab-pane fade show active" id="ongoingTab">
                                    <table class="table mb-0 table-hover" id="hist1">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity Level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="overdueTab">
                                    <table class="table mb-0 table-hover" id="hist2">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Assigned by</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity Level</th>
                                                <th class="text-center">Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade " id="demeritTab">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="card custom-card" style="max-width: 20rem;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Demerit</h5>
                                                <p class="card-text display-4 fw-bold">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table mb-0 table-hover" id="hist3">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Complexity Level</th>
                                                <th class="text-center">Demerit points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        // Hide the tabs container initially
                        document.getElementById("tabsContainer").style.display = "none";
                    });

                    document.getElementById("filter_button").addEventListener("click", () => {
                        const startDate = document.getElementById("start_date").value;
                        const endDate = document.getElementById("end_date").value;
                        const facultyId = document.getElementById("faculty_select").value;
                        const tabsContainer = document.getElementById("tabsContainer");

                        if (!startDate || !endDate) {
                            alertify.error("Please select a valid date range.");
                            return;
                        }

                        if (!facultyId) {
                            alertify.error("Please select a faculty member.");
                            return;
                        }

                        // Hide container before fetching new data
                        tabsContainer.style.display = "none";
                        document.querySelector('.nav-link[href="#ongoingTab"]').click();

                        fetch(
                                `/filter-tasks?start_date=${startDate}&end_date=${endDate}&faculty_id=${facultyId}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                const hasOngoingData = populateTable("hist1", data.ongoingTasks, 7);
                                const hasOverdueData = populateTable("hist2", data.overdueTasks, 7);
                                const hasDemeritData = populateTable("hist3", data.demeritTasks, 6);
                                document.querySelector(".card-text.display-4").textContent = data
                                    .totalDemeritPoints || "0";

                                tabsContainer.style.display = "block";
                            })
                            .catch(error => console.error("Error fetching tasks:", error));
                    });

                    function populateTable(tableId, data, columns) {
                        const table = $(`#${tableId}`).DataTable();
                        table.clear().draw(); // Clear existing table data

                        if (!data || data.length === 0) {
                            return false; // No data available
                        }

                        const seenTaskIds = new Set();
                        data.forEach((item, index) => {
                            if (seenTaskIds.has(item.task_id)) return;
                            seenTaskIds.add(item.task_id);

                            const rowData = [
                                index + 1,
                                item.task_id || "-",
                                ...(columns === 7 ? [item.assigned_by_name || "-"] : []),
                                item.title || "-",
                                item.description || "-",
                                item.complexity_level || "-",
                                columns === 7 ? (item.deadline ? formatDate(item.deadline) : "-") : (item
                                    .demerit_points || "0")
                            ];

                            table.row.add(rowData); // Add row dynamically
                        });

                        table.draw();
                        return true;
                    }

                    function initializeDataTable(tableId) {
                        $(`#${tableId}`).DataTable({
                            destroy: true,
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true
                        });
                    }

                    function formatDate(dateString) {
                        const date = new Date(dateString);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    }
                    </script>

                    @elseif($specialStatus == 4)

                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
                        <div class="filter-container-inline d-flex gap-3">
                            <label for="start_date" class="filter-label-inline">Start Date:</label>
                            <input type="date" id="start_date" class="filter-input-inline">

                            <label for="end_date" class="filter-label-inline">End Date:</label>
                            <input type="date" id="end_date" class="filter-input-inline">
                            <div class="align-self-end">
                                <button class="btn btn-primary" onclick="submitForm()">Filter</button>
                            </div>
                        </div>


                        <!-- Hidden Tabs (will be shown after submit) -->
                        <div id="tabsContainer" class="d-none mt-4">
                            <ul class="nav nav-tabs custom-tabs">
                                <li class="nav-item ">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#demeritsTab"> Demerits</a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3">
                                <div class="tab-pane fade show active" id="demeritsTab">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="card custom-card" style="max-width: 20rem;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Demerit</h5>
                                                <p class="card-text display-4 fw-bold"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Demerit Table -->
                                    <table class="table mb-0 table-hover" id="hist3">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th class="text-center">S.No</th>
                                                <th class="text-center">Task ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Level of Completion</th>
                                                <th class="text-center">Demerit Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    function submitForm() {
                        let fromDate = document.getElementById("start_date").value;
                        let toDate = document.getElementById("end_date").value;

                        if (fromDate && toDate) {
                            fetchFilteredData(fromDate, toDate);
                        } else {
                            alertify.error("Please fill in all the details");
                        }
                    }

                    function fetchFilteredData(fromDate, toDate) {
                        fetch("{{ route('filter.demerits') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({
                                    start_date: fromDate,
                                    end_date: toDate
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById("tabsContainer").classList.remove(
                                    "d-none"); // Show table container
                                updateDemeritTable(data.demerits, data.totalDemeritPoints);
                            })
                            .catch(error => console.error("Error fetching data:", error));
                    }

                    function updateDemeritTable(demerits, totalDemeritPoints) {
                        // Destroy previous instance to avoid errors
                        if ($.fn.DataTable.isDataTable("#hist3")) {
                            $("#hist3").DataTable().clear().destroy();
                        }

                        let table = $("#hist3").DataTable({
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true
                        });

                        table.clear();

                        if (demerits.length === 0) {
                            document.querySelector(".card-text.display-4").innerText = totalDemeritPoints;
                            return false;
                        } else {
                            demerits.forEach((data, index) => {
                                table.row.add([
                                    index + 1,
                                    data.task_id || "-",
                                    data.title || "-",
                                    data.description || "-",
                                    data.complexity_level || "-",
                                    data.demerit_points || "0"
                                ]);
                            });
                            table.draw();
                        }

                        // Update Total Demerit Points
                        document.querySelector(".card-text.display-4").innerText = totalDemeritPoints;
                    }

                    $(document).ready(function() {
                        // Initialize DataTable on page load (if needed)
                        $("#hist3").DataTable({
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true
                        });
                    });
                    </script>

                    @endif
                </div>
            </div>
        </div>




        <!------------------------------- Faculty details modal ----------------------------------->
        <div class="modal fade" id="viewDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="shadow-lg modal-content rounded-3">
                    <div class="text-white modal-header bg-primary">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Faculty Details</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-2 mb-3 rounded d-flex justify-content-between bg-light " id="facultyDetailsHeader"
                            style="color: #333; font-weight: bold;">
                        </div>

                        <table class="table text-center align-middle rounded shadow-sm table-hover table-bordered">
                            <thead class="text-white bg-dark">
                                <tr>
                                    <th scope="col">S No</th>
                                    <th scope="col">Faculty</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Completed Date</th>
                                </tr>
                            </thead>
                            <tbody id="taskDetails">
                                <!-- Task details will be appended here dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btnfinish" id="finishTask" data-task-id=""
                            disabled>Finish
                            Task</button>
                    </div>
                </div>
            </div>
        </div>

        <!------------------------------- Completed Faculty details modal ----------------------------------->
        <div class="modal fade" id="CviewDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="shadow-lg modal-content rounded-3">
                    <div class="text-white modal-header bg-primary">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Faculty Details</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-3 mb-3 rounded shadow-sm d-flex justify-content-between"
                            style=" color: #333; font-weight: bold;" id="cassignedDetailsHeader">
                        </div>
                        <table class="table text-center align-middle rounded shadow table-hover table-bordered"
                            style="border: 2px solidrgb(12, 113, 43);">
                            <thead style="background: linear-gradient(135deg, #6a11cb, #2575fc); color: #fff;">
                                <tr>
                                    <th scope="col">S No</th>
                                    <th scope="col">Faculty</th>
                                    <th scope="col">Completed Date</th>
                                </tr>
                            </thead>
                            <tbody id="CtaskDetails" style="background: #fff;">
                                <!-- Task details will be appended here dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="text-white btn fw-bold" style="background-color:gray;"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-------------------------------- Reason Modal ------------------>
        <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="reasonModalLabel">Provide Reason</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reasonForm">
                            <input type="hidden" id="taskId" name="task_id">
                            <div class="mb-3">
                                <label for="reasonText" class="form-label">Reason</label>
                                <textarea class="form-control" id="reasonText" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitReason">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!--forward redo reason modal-->
        <div class="modal fade" id="fredoreasonModal" tabindex="-1" aria-labelledby="reasonfModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="reasonfModalLabel">Provide Reason</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="fredoreasonForm">
                            <input type="hidden" id="forwardtaskId" name="task_id">
                            <div class="mb-3">
                                <label for="fredoreasonText" class="form-label">Reason</label>
                                <textarea class="form-control" id="fredoreasonText" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitfredoReason">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-------------------------------Forward Faculty details modal ----------------------------------->
        <div class="modal fade" id="forwardviewDetails" tabindex="-1" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="shadow-lg modal-content rounded-3">
                    <div class="text-white modal-header bg-primary">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Faculty Details</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-2 mb-3 rounded d-flex justify-content-between bg-light "
                            id="forwardassignedDetailsHeader">
                        </div>
                        <table class="table text-center align-middle rounded shadow-sm table-hover table-bordered">
                            <thead class="text-white bg-dark">
                                <tr>
                                    <th scope="col">S No</th>
                                    <th scope="col">Faculty</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Approval</th>
                                    <th scope="col">Completed Date</th>
                                </tr>
                            </thead>
                            <tbody id="forwardfacultyDetails">
                                <!-- Task details will be appended here dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btnfinish" id="forwardfinishTask" data-task-id=""
                            disabled>Finish
                            Task</button>
                    </div>
                </div>
            </div>
        </div>

        <!--------------------------------- reassigned faculty modal ----------------------------------->

        <div class="modal fade" id="reassignModal" tabindex="-1" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Reassign Task</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reassignform" enctype="multipart/form-data">
                            <input type="hidden" id="Reassign_hidden_faculty_id" value="{{ $facultyId }}"
                                name="Reassign_faculty_id">
                            <input type="hidden" id="Reassign_hidden_faculty_name" value="{{ $facultyName }}"
                                name="Reassign_faculty_name">
                            <input type="hidden" id="Reassign_Role" value="{{ $Role }}" name="Reassign_Role">
                            <input type="hidden" id="Reassign_specialStatus" value="{{ $specialStatus }}"
                                name="Reassign_specialStatus">
                            <input type="hidden" id="Reassign_task_id" name="Reassign_task_id">

                            @if ($specialStatus == 0 && $Role == 'Principal')

                            <div class="mb-3">
                                <label for="workType" class="form-label">Type of Role</label>
                                <select class="form-control" id="Reassign_workType" name="Reassign_workType"
                                    onchange="Reassign_showDropdown()" required>
                                    <option value="">Select</option>
                                    <option value="hod">Head of the Department</option>
                                    <option value="faculty">Faculty</option>
                                </select>
                            </div>
                            <div class="mb-3" id="Reassign_departmentDropdown" style="display: none;">
                                <label for="Reassign_selectedDepartment" class="form-label">Department</label>
                                <select class="form-control" name="Reassign_selectedDepartment"
                                    id="Reassign_selectedDepartment" onchange="Reassign_updateSelecteddepartment()">
                                    <option value="">Select Department</option>
                                    @foreach ($dept as $d)
                                    <option value="{{ $d->dname }}">
                                        {{ $d->dname }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>

                            <input type="hidden" name="Reassign_selectedDepartments" id="Reassign_selectedDepartments">
                            <div class="mb-3" id="Reassign_facultyDropdown" style="display: none;">
                                <label for="Reassign_selectedFaculty" class="form-label">Faculty</label>
                                <select class="form-control" name="Reassign_selectedFaculty"
                                    id="Reassign_selectedFaculty" onchange="Reassign_updateSelectedFaculties()">
                                    <option value="">Select Faculty</option>
                                    <!-- Faculty options will be dynamically populated here -->
                                </select>

                            </div>


                            <input type="hidden" name="Reassign_selectedFaculties" id="Reassign_selectedFaculties">
                            @elseif($specialStatus == 3 && $Role == 'head of department')
                            <div class="mb-3">
                                <label for="Reassign_newFaculty" class="form-label">Select Faculty</label>
                                <select class="form-control" name="Reassign_selecteddeptFaculty"
                                    id="Reassign_selecteddeptFaculty">
                                    <option value="">Select</option>
                                    @foreach ($departmentfaculties as $df)
                                    <option value="{{ $df->id }}">{{ $df->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @endif
                            <input type="hidden" name="status" value="0">

                            <input type="hidden" class="form-control" name="Reassign_assigned_date"
                                id="Reassign_assigned_date" required>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="reassignsubmit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!----------- reassigned forward faculty modal ---------------------------->
        <div class="modal fade" id="reassignforwardModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Forward Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="forward_reassignform" enctype="multipart/form-data">
                            <input type="hidden" name="forward_reassign_task_id" id="forward_reassign_task_id" value="">
                            <input type="hidden" name="forward_reassign_status" id="forward_reassign_status" value="">
                            <input type="hidden" id="forward_reassign_hidden_faculty_id" value="{{ $facultyId }}"
                                name="forward_reassign_faculty_id">
                            <input type="hidden" id="forward_reassign_hidden_faculty_name" value="{{ $facultyName }}"
                                name="forward_reassign_faculty_name">

                            <div class="mb-3">
                                <label for="forward_reassign_newFaculty" class="form-label">Select Faculty</label>
                                <button type="button" class="form-control text-start dropdown-toggle"
                                    data-bs-toggle="dropdown" id="forward_reassign_newFacultyBtn">Select</button>
                                <ul class="dropdown-menu" id="forward_reassign_newFacultyDropdown">
                                    @foreach ($departmentfaculties as $df)
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            onclick="forward_reassign_updateSelectedforwarddepartmentFaculties('{{ $df->id }}', '{{ $df->name }}')">
                                            {{ $df->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="forward_reassign_selectedforwarddeptFaculties"
                                    id="forward_reassign_selectedforwarddeptFaculties">
                            </div>
                            <input type="hidden" class="form-control" name="forward_reassign_forwarded_date"
                                id="forward_reassign_forwarded_date" required>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="reassignforwardsubmit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!----------- REASON DISPLAY MODAL---------------------------->
        <div class="modal fade" id="reasonDisplayModal" tabindex="-1" aria-labelledby="reasonfModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="reasonfModalLabel">Reason</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="fredoreasonForm">
                            <input type="hidden" id="forwardtaskId" name="task_id">
                            <div class="mb-3">
                                <label for="fredoreasonText" class="form-label">Reason</label>
                                <label class="form-control" id="reasonDisplayText" rows="3" required></label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!----------- Forward redo REASON DISPLAY MODAL---------------------------->
        <div class="modal fade" id="forwardreasonDisplayModal" tabindex="-1" aria-labelledby="reasonforwardModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="reasonforwardModalLabel">Reason</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="forwardredoreasonForm">
                            <input type="hidden" id="forwardreasontaskId" name="task_id">
                            <div class="mb-3">
                                <label for="forwardredoreasonText" class="form-label">Reason</label>
                                <label class="form-control" id="forwardreasonDisplayText" rows="3" required></label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!------------------------------Assignedla irukara extend deadline modal --------------------->
        <div class="modal fade" id="extendDeadlineModal" tabindex="-1" aria-labelledby="extendDeadlineLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="extendDeadlineLabel">Extend Deadline</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="extendDeadlineForm">
                            <input type="hidden" id="extenddeadlinetaskId" name="task_id">
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Extension</label>
                                <input type="text" class="form-control" id="reason" name="reason" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="oldDeadline" class="form-label">Current Deadline</label>
                                <input type="text" class="form-control" id="oldDeadline" name="oldDeadline" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="newDeadline" class="form-label">Extend Deadline</label>
                                <input type="date" class="form-control" id="newDeadline" name="newDeadline">
                            </div>
                            <input type="hidden" id="taskId" name="taskId">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" id="rejectDeadline">Reject</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!------------------------------ Forward la iruku extend deadline modal --------------------->
        <div class="modal fade" id="forwardextendDeadlineModal" tabindex="-1"
            aria-labelledby="forwardextendDeadlineLabel" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forwardextendDeadlineLabel">Extend Deadline</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="forwardextendDeadlineForm">
                            <input type="hidden" id="forwardextenddeadlinetaskId" name="task_id">
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Extension</label>
                                <input type="text" class="form-control" id="forwardreason" name="forwardreason" required
                                    disabled>
                            </div>
                            <div class="mb-3">
                                <label for="forwardoldDeadline" class="form-label">Current Deadline</label>
                                <input type="text" class="form-control" id="forwardoldDeadline"
                                    name="forwardoldDeadline" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="forwardnewDeadline" class="form-label">Extend Deadline</label>
                                <input type="date" class="form-control" id="forwardnewDeadline"
                                    name="forwardnewDeadline">
                            </div>
                            <input type="hidden" id="taskId" name="taskId">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger btnrejectforwardeadline">Reject</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary btnupdateforwardeadline">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- REASON DISPLAY MODAL -->
        <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="reasonModalLabel">Provide Reason</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reasonForm">
                            <input type="hidden" id="taskId" name="task_id">
                            <div class="mb-3">
                                <label for="reasonText" class="form-label">Reason</label>
                                <textarea class="form-control" id="reasonText" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitReason">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- extend deadline modal --}}
        <div class="modal fade" id="requestdeadlineModal" tabindex="-1" aria-labelledby="reasonfModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="reasonfModalLabel">Reason for Deadline Extension</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="extndDeadlineForm">
                            <input type="hidden" id="task_id" name="task_id">
                            <div class="mb-3">
                                <label for="reasonInput" class="form-label">Enter Reason</label>
                                <textarea id="reasonInput" name="reason" class="form-control" rows="3"
                                    required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>




        <!-------------------- Footer -------------------------------->
        <footer class="footer">
            <div class="footer-copyright" style="text-align: center;">
                <p>Copyright © 2024 Designed by
                    <b><i>Technology Innovation Hub - MKCE. </i> </b>All rights reserved.
                </p>
            </div>
            <div class="footer-links">
                <a href="https://www.linkedin.com/company/technology-innovation-hub-mkce/"><i
                        class="fab fa-linkedin"></i></a>
            </div>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- CSRF link -->
    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    </script>



    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date();
        var formattedDate = today.getFullYear() + '-' +
            String(today.getMonth() + 1).padStart(2, '0') + '-' +
            String(today.getDate()).padStart(2, '0'); // Format as YYYY-MM-DD
        var dateInput = document.getElementById('assigned_date');
        if (dateInput) {
            dateInput.value = formattedDate; // Set today's date
        }
        var date_Input = document.getElementById('forwarded_date');
        if (date_Input) {
            date_Input.value = formattedDate; // Set today's date
        }
    });
    </script>
    <!-- Dashboard Remainder -->
    <script>
    // Fetch tasks using AJAX
    function fetchTasks() {
        $.ajax({
            url: '/tasks', // Ensure the correct route or endpoint
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const tasksContainer = $('#tasks-container');
                tasksContainer.empty(); // Clear existing tasks

                // Loop through the tasks and append them to the marquee
                response.tasks.forEach(task => {
                    tasksContainer.append(`
                        <div class="task" onclick="openModal('${task.title}', '${task.deadline}', '${task.description}')">
                            <div class="task-title">${task.title}</div>
                            <div class="task-deadline">Deadline: ${task.deadline}</div>
                            <div class="task-description">Description: ${task.description}</div>
                        </div>
                    `);
                });
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch tasks:', error);
            }
        });
    }

    // Function to open the modal and populate its contents
    function openModal(title, deadline, description) {
        const modal = document.getElementById('task-modal');
        document.getElementById('modal-title').innerHTML =
            `<img src="https://icon-library.com/images/description-icon/description-icon-26.jpg" alt="Task Icon" class="modal-icon"> Description`;
        document.getElementById('modal-description').innerText = `Description:${description}`;
        modal.style.display = 'flex'; // Show the modal
    }

    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('task-modal');
        modal.style.display = 'none'; // Hide the modal
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('task-modal');
        if (event.target === modal) {
            closeModal();
        }
    };
    </script>

    <script>
    function validateSize(input) {
        const file = input.files[0];
        if (file.size > 2048 * 1024) { // 2MB
            alert('File size must be less than 2MB.');
            input.value = ''; // Clear the input
        }
    }
    </script>

    <script>
    const loaderContainer = document.getElementById('loaderContainer');

    function showLoader() {
        loaderContainer.classList.add('show');
    }

    function hideLoader() {
        loaderContainer.classList.remove('show');
    }
    //    automatic loader
    document.addEventListener('DOMContentLoaded', function() {
        const loaderContainer = document.getElementById('loaderContainer');
        const contentWrapper = document.getElementById('contentWrapper');
        let loadingTimeout;

        function hideLoader() {
            loaderContainer.classList.add('hide');
            contentWrapper.classList.add('show');
        }

        function showError() {
            console.error('Page load took too long or encountered an error');
            // You can add custom error handling here
        }
        // Set a maximum loading time (10 seconds)
        loadingTimeout = setTimeout(showError, 10000);
        // Hide loader when everything is loaded
        window.onload = function() {
            clearTimeout(loadingTimeout);
            // Add a small delay to ensure smooth transition
            setTimeout(hideLoader, 500);
        };
        // Error handling
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            clearTimeout(loadingTimeout);
            showError();
            return false;
        };
    });
    // Toggle Sidebar
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    const body = document.body;
    const mobileOverlay = document.getElementById('mobileOverlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('mobile-show');
            mobileOverlay.classList.toggle('show');
            body.classList.toggle('sidebar-open');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    }
    hamburger.addEventListener('click', toggleSidebar);
    mobileOverlay.addEventListener('click', toggleSidebar);
    // Toggle User Menu
    const userMenu = document.getElementById('userMenu');
    const dropdownMenu = userMenu.querySelector('.dropdown-menu');
    userMenu.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });
    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
        dropdownMenu.classList.remove('show');
    });
    // Toggle Submenu
    const menuItems = document.querySelectorAll('.has-submenu');
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            const submenu = item.nextElementSibling;
            item.classList.toggle('active');
            submenu.classList.toggle('active');
        });
    });
    // Handle responsive behavior
    window.addEventListener('resize', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('mobile-show');
            mobileOverlay.classList.remove('show');
            body.classList.remove('sidebar-open');
        } else {
            sidebar.style.transform = '';
            mobileOverlay.classList.remove('show');
            body.classList.remove('sidebar-open');
        }
    });

    alertify.defaults.notifier.position = 'top-right';
    //Datatable
    new DataTable('#assignedtask1');
    new DataTable('#mytask1');
    new DataTable('#mytask2');
    new DataTable('#completed1');
    new DataTable('#completed2');
    new DataTable('#overdue1');
    new DataTable('#history1');
    new DataTable('#hist1');
    new DataTable('#hist2');
    new DataTable('#hist3');

    $(document).ready(function() {
        var today = new Date().toISOString().split('T')[0];
        $('#deadline').attr('min', today);
    });

    //Add  task
    $(document).on('submit', '#addtaskform', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Get the form data
        var workType = $("#workType").val(); // Get the selected work type

        // Clear any previously appended data
        formData.delete("researchType");
        formData.delete("teachingSubject");
        formData.delete("department_data");
        formData.delete("faculty_data");

        // ✅ Conditional Data Appending Based on Work Type
        if (workType === "Management") {
            formData.append("researchType", $("#researchType").val());
            formData.append("teachingSubject", null);
            formData.append("selectedDepartments", null);
            formData.append("selectedFaculties", null);
        } else if (workType === "center of head") {
            formData.append("researchType", null);
            formData.append("teachingSubject", $("#teachingSubject").val());
            formData.append("selectedDepartments", null);
            formData.append("selectedFaculties", null);
        } else if (workType === "hod") {
            formData.append("researchType", null);
            formData.append("teachingSubject", null);
            formData.append("selectedDepartments", $("#selectedDepartments").val());
            formData.append("selectedFaculties", null);
        } else if (workType === "faculty") {
            formData.append("researchType", null);
            formData.append("teachingSubject", null);
            formData.append("selectedDepartments", $("#selectedDepartments").val());
            formData.append("selectedFaculties", $("#selectedFaculties").val());
        }

        // 🚀 AJAX Request
        $.ajax({
            type: "POST",
            url: "/add/addtask", // Your API endpoint
            data: formData,
            processData: false, // Don't process data automatically
            contentType: false, // Let the browser set content type
            success: function(response) {
                if (response.status === 200) {
                    alertify.success(" Task added successfully!");

                    // Reset form fields
                    $("#addtaskform")[0].reset();
                    $("#worktype").val("");
                    $('#researchType').val("");
                    $('#teachingSubject').val("");
                    $("#selectedDepartments").val("");
                    $("#selectedFaculties").val("");

                    // Reset dropdown button texts
                    $("#deptDropdownBtn").text("Select Department");
                    $("#facultyDropdownBtn").text("Select Faculty");
                    $("#newFacultyBtn").text("Select Faculty");


                    // Hide the modal after submission
                    $("#addtask").modal("hide");



                    setTimeout(function() {
                        $('#assignedtask1').load(location.href + ' #assignedtask1',
                            function() {
                                console.log("Table refreshed successfully!");
                            });
                    }, 100);

                    $('#mytask1').load(location.href + ' #mytask1');
                    $('#mytask2').load(location.href + ' #mytask2');
                    $('#overdue1').load(location.href + ' #overdue1');
                    $('#completed1').load(location.href + ' #completed1');
                    $('#completed2').load(location.href + ' #completed2');
                } else if (response.status == 500) {
                    alertify.error("ulla varala da");
                } else if (response.status == 400) {
                    alertify.error("400");
                } else {
                    alertify.error(" Something went wrong. Please try again.");
                }
            },
            error: function(xhr, status, error) {
                alertify.error(" An error occurred. Please try again.");
                console.error("Error details:", error); // Debugging info
            }
        });
    });
    $(document).on('click', '.showImage', function() {
        var taskId = $(this).data('task-id');
        var status = $(this).data('status');
        var deadline = $(this).data('deadline');

        $('#forwardform').find('input[name="task_id"]').val(taskId);
        $('#forwardform').find('input[name="status"]').val(status);

        var currentDate = new Date().toISOString().split('T')[0]; // Format: yyyy-mm-dd
        $('#forwarded_date').val(currentDate);

        var deadlineDate = new Date(deadline).toISOString().split('T')[0];

        // Set min and max attributes to enforce valid date range
        $('#forwarddeadline').attr('min', currentDate); // Prevent selecting past dates
        $('#forwarddeadline').attr('max', deadlineDate);
    });

    // Add task for forward task
    $(document).on('submit', '#forwardform', function(e) {
        e.preventDefault(); // Prevent form submission

        var selectedDeadline = $('#forwarddeadline').val();
        var currentDate = new Date().toISOString().split('T')[0]; // Get today's date

        // Validate that the selected deadline is not before today
        if (selectedDeadline < currentDate) {
            alertify.error("The deadline must be today or a future date.");
            return;
        }

        var formData = new FormData(this); // Get the form data

        $.ajax({
            type: "POST",
            url: "/forward/forwardtask", // Ensure this is the correct endpoint
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token
            },
            data: formData,
            processData: false, // Don't process data
            contentType: false, // Don't set content type
            success: function(response) {
                if (response.status === 200) {
                    alertify.success("Task forwarded successfully!");

                    $("#forwardform").find("#forward-hod-departments-btn").text("Select");
                    $("#forwardform").find("#forward-faculty-departments-btn").text("Select");
                    $("#forwardform").find("#forward-department-faculty").text("Select");
                    $("#forwardform")[0].reset();
                    $("fstudentaffiars").val("");

                    $("#fnewFacultyBtn").text("Select Faculty");
                    $("#coordinatorBtn").text("Select Coordinator");

                    $("#forwardModal").modal("hide");

                    // Reset the form fields
                    $('#mytask1').load(location.href + ' #mytask1');
                    $('#mytask2').load(location.href + ' #mytask2');
                } else if (response.status === 400) {
                    alertify.error("Data error");
                } else {
                    alertify.error("Something went wrong. Please try again.");
                }
            }
        });
    });


    $(document).on('click', '.showAssignedFaculty', function(e) {
        e.preventDefault();

        const id = $(this).val(); // Get the task ID
        const finishButton = $('#finishTask'); // Reference to the Finish Task button

        if (!id) {
            console.error('Task ID not provided');
            alertify.error('Task ID is missing. Please try again.');
            return;
        }

        // Reset the modal content before showing
        $('#taskDetails').empty(); // Clear old task details
        finishButton.prop('disabled', true).attr('data-task-id', id); // Reset Finish Task button

        // Fetch task details and render in the modal
        handleTaskDetails(id);

        // Show the modal
        $('#viewDetails').modal('show');
    });

    // Function to fetch task details
    // Function to fetch task details
    async function handleTaskDetails(taskId) {
        try {
            let response = await $.ajax({
                type: 'POST',
                url: `user/fetchdet/${taskId}`
            });

            if (response.status === 200 && response.data.length > 0) {
                let taskDetails = '';
                let updata = response.updata;
                let reasons = response.reason;
                let deadline = updata[0].deadline.split("T")[0];
                let assigned_date = updata[0].assigned_date.split("T")[0];

                $('#facultyDetailsHeader').html(`
                <div class="deadline-header">
                    <strong id="assignedDate">Assigned Date:</strong> ${assigned_date}
                    &emsp;&emsp;
                    <strong id="deadlineDate">Deadline:</strong> ${deadline}
                    <br>
                </div>
            `);

                const currentDate = new Date();

                for (let task of response.data) {
                    let assignedDate = new Date(task.assigned_date);
                    let timeDiff = currentDate - assignedDate;
                    let hourDiff = timeDiff / (1000 * 60 * 60); // Convert ms to hours
                    let isDisabled = task.status !== 0 || hourDiff >= 48;

                    let formattedCompletedDate = task.completed_date ?
                        new Date(task.completed_date).toLocaleDateString('en-GB') : 'N/A';
                    let reasonExist = task.reason !== null;
                    let feedback = task.feedback !== null && task.status === 4;
                    let reassign = task.status === 0 && await checkforwardreassign(task.assigned_to_id);

                    taskDetails += `
                    <tr>
                        <td>${response.data.indexOf(task) + 1}</td>
                        <td>${task.assigned_to_name}</td>
                        <td>
                            <span class="badge ${task.status === 3 ? 'bg-success' : 'bg-secondary'}">
                                ${task.status === 0 ? 'Assigned' :
                                task.status === 1 ? 'Accepted' :
                                task.status === 2 ? 'Completed' :
                                task.status === 4 ? 'Extension Request for Deadline' :
                                task.status === 3 ? 'Approved' : 'Unknown'}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btnapprove" value="${task.id}" title="Approve Task" ${task.status === 3 || task.status === 1 || task.status === 0 || task.status === 4? 'disabled' : ''}>
                                <i class="fas fa-circle-check"></i>
                            </button>
                            <button type="button" class="btn btn-danger btnredo" value="${task.id}" title="Redo Task" ${task.status === 3 || task.status === 1 || task.status === 0 || task.status === 4 ? 'disabled' : ''}>
                                <i class="fas fa-arrows-rotate"></i>
                            </button>
                            ${reassign ? `
                                <button type="button" class="btn btn-primary btnreassign" data-id="${task.id}" value="${task.id}" data-status="${task.status}" title="Reassign Task" ${isDisabled || task.status === 1 || task.status === 2 || task.status === 3 ? 'disabled' : ''}>
                                    <i class="fa-solid fa-arrows-turn-to-dots"></i>
                                </button>` : ''}
                            ${feedback ? `
                                <button type="button" class="btn btn-secondary btnedeadline" data-id="${task.id}" value="${task.id}" data-status="${task.status}" title="Extend deadline">
                                    <i class="fa-solid fa-calendar-week"></i>
                                </button>` : ''}
                            ${reasonExist ? `
                                <button type="button" class="btn btn-secondary btnreason" data-id="${task.id}" value="${task.id}" data-status="${task.status}" title="Reason">
                                    <i class="fas fa-light fa-message"></i>
                                </button>` : ''}
                        </td>
                        <td>${formattedCompletedDate}</td>
                    </tr>`;

                    // If the task should be auto-updated due to time constraint
                    if (hourDiff >= 48 && task.status === 0) {
                        $.ajax({
                            url: `/tasks/update-status/${task.id}`,
                            type: "POST",
                            data: {
                                _token: csrfToken,
                                status: 1
                            }, // Change status to 1 (Accepted)
                            success: function() {
                                console.log(`Task ${task.id} status updated to 1 due to time limit.`);
                            }
                        });
                    }
                }

                $('#taskDetails').html(taskDetails);
                checkTaskStatus(taskId); // Populate table
            } else {
                alertify.error(response.message || 'No task details found.');
            }
        } catch (error) {
            console.error('Error fetching task details:', error);
            alertify.error('An error occurred while fetching task details. Please try again.');
        }
    }
    //Reason display for assigned tab
    $(document).on('click', '.btnreason', function() {
        let taskId = $(this).data('id'); // Get task ID from button

        $.ajax({
            type: 'POST',
            url: `/user/fetchdet/${taskId}`, // Fetch reason from backend
            success: function(response) {
                if (response.status === 200 && response.reason.length > 0) {
                    let reasonText = response.reason[0].reason; // Get first reason

                    $('#reasonDisplayText').text(reasonText); // Display reason in modal
                    $('#forwardtaskId').val(taskId); // Set hidden input value

                    $('#reasonDisplayModal').modal('show'); // Open modal
                } else {
                    alertify.error('No reason found for this task.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching reason:', error);
                alertify.error('An error occurred while fetching the reason.');
            }
        });
    });

    // Function to check if all tasks are completed
    function checkTaskStatus(taskId) {
        $.ajax({
            type: 'POST',
            url: '/check-task-status',
            data: {
                task_id: taskId
            },
            success: function(response) {
                if (response.allCompleted) {
                    $('#finishTask').prop('disabled', false); // Enable Finish Task button
                } else {
                    $('#finishTask').prop('disabled', true); // Disable Finish Task button
                }
            }
        });
    }

    // Event listener for the Approve button
    $(document).on('click', '.btnapprove', function(e) {
        e.preventDefault();

        const approveId = $(this).val();
        const button = $(this);
        const row = button.closest('tr');

        alertify.confirm(
            'Confirmation',
            'Are you sure you want to approve this task?',
            function() {
                $.ajax({
                    type: 'POST',
                    url: `/user/approve/${approveId}`,
                    success: function(response) {
                        if (response.status === 500) {
                            alertify.error(response.message);

                        } else {
                            alertify.success('Task Approved successfully!');
                            row.find('td:nth-child(3) span')
                                .removeClass('bg-secondary')
                                .addClass('bg-success')
                                .text('Approved');
                            button.prop('disabled', true);
                            row.find('.btnredo').prop('disabled', true);
                            checkTaskStatus($('#finishTask').data('task-id'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error approving the task:', error);
                        alertify.error('An error occurred. Please try again.');
                    }
                });
            },
            function() {
                alertify.error('Approval canceled');
            }
        );
    });

    // Event listener for the Finish Task button
    $('#finishTask').on('click', function() {
        const taskId = $(this).data('task-id');
        var finishDate = new Date().toISOString().split('T')[0];

        if (!taskId) {
            alert('Invalid Task ID. Please try again.');
            return;
        }

        // Show confirmation dialog
        alertify.confirm("Confirmation", "Are you sure you want to finish this task?",
            function() { // If user clicks "Yes"
                $.ajax({
                    type: 'POST',
                    url: '/update-main-task',
                    data: {
                        task_id: taskId,
                        completed_date: finishDate
                    },
                    success: function(response) {
                        if (response.success) {
                            alertify.success(response.message); // Success message
                            $("#viewDetails").modal('hide');

                            // Reload assigned and completed task lists
                            $('#assignedtask1').load(location.href + ' #assignedtask1',
                                function() {
                                    console.log("Table reloaded successfully.");
                                });

                            $('#completed2').load(location.href + ' #completed2', function() {
                                console.log("Table reloaded successfully.");
                            });

                        } else {
                            alertify.error(response.message || 'Failed to update the task.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating main task:', error);
                        alertify.error('Error updating the main task. Please try again.');
                    }
                });
            },
            function() { // If user clicks "No"
                alertify.error('Task completion cancelled.');
            }
        );
    });


    //redo reason modal
    $(document).on('click', '.btnredo', function(e) {
        e.preventDefault();

        var taskId = $(this).val(); // Get the task ID from the button value
        $('#reasonModal').find('#taskId').val(taskId); // Set task ID in hidden field
        $('#reasonModal').modal('show'); // Show the modal
    });

    // Submit reason to server
    $(document).on('click', '#submitReason', function(e) {
        e.preventDefault();

        var reason = $('#reasonText').val(); // Get the reason text
        var taskId = $('#taskId').val(); // Get the task ID from the hidden field

        if (!reason) {
            alertify.error("Please enter a reason!");
            return;
        }

        console.log("Submitting reason:", reason);
        console.log("Task ID:", taskId);

        $.ajax({
            type: 'POST',
            url: `/store-reason/${taskId}`, // Fix template literal syntax
            data: {
                task_id: taskId,
                reason: reason,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(response) {
                if (response.status === 200) {
                    alertify.success('Reason saved successfully!');

                    $('#reasonModal').modal('hide');
                    $('#viewDetails').modal('hide');
                    $("#reasonForm")[0].reset();


                    // Find the row related to this task
                    var row = $(`button.btnapprove[value="${taskId}"]`).closest('tr');

                    console.log("Found row:", row.length); // Debugging

                    if (row.length) {
                        // Disable both approve and redo buttons
                        row.find('.btnapprove, .btnredo').prop('disabled', true);
                        console.log("Approve and Redo buttons disabled.");
                    } else {
                        console.log("Error: Row not found for taskId", taskId);
                    }
                } else {
                    alertify.error(response.message || 'Failed to save the reason.');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    //click to accept button in mytasks
    $(document).on('click', '.btnaccept', function(e) {
        e.preventDefault();

        var acceptId = $(this).val();
        console.log(acceptId);
        alertify.confirm(
            'Confirmation',
            'Are you sure you want to accept this task?',
            function() {
                $.ajax({
                    type: 'POST',
                    url: `/user/accept/${acceptId}`,
                    data: {
                        id: acceptId,
                    },
                    success: function(response) {
                        if (response.status === 500) {
                            alertify.error(response.message);

                        } else {
                            $('#mytask1').load(location.href + ' #mytask1');
                            alertify.success('Task Accepted successfully!');
                        }
                    }
                });
            },
            function() {
                alertify.error('Acception canceled');
            }
        );
    });

    //click to complete button in mytasks
    $(document).on('click', '.btncomplete', function(e) {
        e.preventDefault();

        var completeId = $(this).val();
        var completedDate = new Date().toISOString().split('T')[0];
        console.log(completeId);

        alertify.confirm(
            'Confirmation',
            'Are you sure you want to complete this task?',
            function() {
                $.ajax({
                    type: 'POST',
                    url: `/user/complete/${completeId}`,
                    data: {
                        id: completeId,
                        completed_date: completedDate,
                    },
                    success: function(response) {
                        if (response.status === 500) {
                            alertify.error(response.message);
                        } else {
                            $('#mytask1').load(location.href + ' #mytask1');
                            alertify.success('Task Completed successfully!');
                        }
                    }
                });
            },
            function() {
                alertify.error('Completion canceled');
            }
        );
    });

    //forwarded faculty details
    $(document).on('click', '.showForwardedFaculty', function(e) {
        e.preventDefault();


        const fullValue = $(this).val(); // Get the concatenated value
        const values = fullValue.split('-'); // Split the string using the hyphen as a delimiter
        const id = values[0]; // First part is task_id
        const assigned_by_id = values[1];
        const deadline = values[2]; // Second part is assigned_by_id
        // Log the values to verify
        console.log("Task ID:", id);
        console.log("Assigned By ID:", assigned_by_id);
        console.log("Deadline:", deadline);
        const finishButton = $('#forwardfinishTask'); // Reference to the Finish Task button

        if (!id) {
            console.error('Task ID not provided');
            alertify.error('Task ID is missing. Please try again.');
            return;
        }

        // Reset the modal content before showing
        $('#forwardfacultyDetails').empty(); // Clear old task details
        finishButton.prop('disabled', true).attr('data-task-id', id); // Reset Finish Task button

        // Fetch task details and render in the modal
        handleForwardTaskDetails(id);

        // Show the modal
        $('#forwardviewDetails').modal('show');
    });

    // Function to fetch task details
    function handleForwardTaskDetails(taskId) {
        $.ajax({
            type: 'POST',
            url: `user/forwardfetchdet/${taskId}`,
            success: function(response) {
                if (response.status === 200 && response.data.length > 0) {
                    let forwardfacultyDetails = '';
                    let updata = response.updata;
                    console.log(updata);
                    let reasons = response.reason;
                    let maindeadline = response.maindeadline;
                    let deadline = updata[0].deadline.split("T")[0];
                    let forwarded_date = updata[0].forwarded_date.split("T")[0];
                    let specialStatus = response.specialStatus;


                    // Display the deadline at the top
                    $('#forwardassignedDetailsHeader').html(`
                   <div class="deadline-header">
                       <strong>Assigned Date:</strong> ${forwarded_date} &emsp;&emsp;&emsp;&emsp;
                       <strong>Deadline:</strong> ${deadline} <br>
                   </div>
               `);

                    const current_Date = new Date().toISOString().split("T")[0];

                    response.data.forEach((task, index) => {
                        const isDeadline_Crossed = task.completed_date === null && deadline <
                            current_Date;
                        let formatted_CompletedDate = task.completed_date ?
                            new Date(task.completed_date).toLocaleDateString('en-GB') :
                            'N/A';
                        let formatted_maindeadline = maindeadline.deadline;
                        let ffeedbackExist = task.feedback !== null && task.status === 4;
                        let reassign = task.status === 0 && checkforwardreassign(task
                            .assigned_to_id) && specialStatus === 3;
                        let reasonexist = task.reason !== null;

                        forwardfacultyDetails += `
                       <tr class="${isDeadline_Crossed ? 'table-danger' : ''}">
                           <td>${index + 1}</td>
                           <td>${task.assigned_to_name}</td>
                           <td>
                               <span class="badge ${task.status === 3 ? 'bg-success' : 'bg-secondary'}">
                                   ${task.status === 0 ? 'Assigned' :
                                       task.status === 1 ? 'Accepted' :
                                       task.status === 2 ? 'Completed' :
                                       task.status === 4 ? 'Extension Request for Deadline' :
                                       task.status === 3 ? 'Approved' : 'Unknown'}
                               </span>
                           </td>
                           <td>
                               <button type="button" class="btn btn-success btnforwardapprove"
                                   value="${task.sid}" title="Approve Task"
                                   ${task.status === 0 || task.status === 3 || task.status === 1 || task.status === 4 ? 'disabled' : ''}>
                                   <i class="fas fa-circle-check"></i>
                               </button>
                               <button type="button" class="btn btn-danger btnforwardredo"
                                   value="${task.sid}" title="Redo Task"
                                   ${task.status === 0 || task.status === 3 || task.status === 1 || task.status === 4 ? 'disabled' : ''}>
                                   <i class="fas fa-arrows-rotate"></i>
                               </button>
                               ${reassign ? `
                                   <button type="button" class="btn btn-primary btnforwardreassign"
                                       value="${task.sid}" title="Reassign Task"
                                       ${task.status !== 0 ? 'hide' : ''}>
                                       <i class="fa-solid fa-arrows-turn-to-dots"></i>
                                   </button>`:''}
                               ${reasonexist ? `
                                       <button type="button" class="btn btn-secondary btnforwardreason"
                                           data-id="${task.sid}" value="${task.id}"
                                           data-status="${task.status}" title="Reason">
                                           <i class="fas fa-light fa-message"></i>
                                       </button>
                                   ` : ''}
                                ${ffeedbackExist ? `
                                <button type="button" class="btn btn-secondary btnforwardextenddeadline" data-id="${task.sid}" value="${task.id}" data-status="${task.status}" data-deadline="${task.deadline}" data-feedback="${task.feedback}" data-maindeadline="${formatted_maindeadline}" title="Extend deadline">
                                    <i class="fa-solid fa-calendar-week"></i>
                                </button>` : ''}
                                
                           </td>
                           <td>${formatted_CompletedDate}</td>
                       </tr>
                   `;
                    });

                    $('#forwardfacultyDetails').html(forwardfacultyDetails);
                    forwardcheckTaskStatus(taskId);

                } else {
                    alertify.error(response.message || 'No task details found.');
                }
            }
        });
    }

    // Attach click event to Approve button to disable buttons dynamically





    // Function to check if all tasks are completed
    function forwardcheckTaskStatus(taskId) {
        $.ajax({
            type: 'POST',
            url: '/check-forwardtask-status',
            data: {
                task_id: taskId
            },
            success: function(response) {
                if (response.hide) {
                    $('#forwardfinishTask').hide();
                } else if (response.allCompleted) {
                    $('#forwardfinishTask').prop('disabled', false); // Enable Finish Task button
                } else {
                    $('#forwardfinishTask').prop('disabled', true); // Disable Finish Task button
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking task status:', error);
                alertify.error('Error checking task status. Please try again.');
                $('#forwardfinishTask').prop('disabled', true); // Disable Finish Task button on error
            }
        });
    }

    // Event listener for the Approve button
    $(document).on('click', '.btnforwardapprove', function(e) {
        e.preventDefault();
        const approveId = $(this).val();
        const button = $(this);
        const row = button.closest('tr');

        alertify.confirm(
            'Confirmation',
            'Are you sure you want to approve this task?',
            function() {
                $.ajax({
                    type: 'POST',
                    url: `/user/forwardapprove/${approveId}`,
                    success: function(response) {
                        if (response.status === 500) {
                            alertify.error(response.message);
                        } else {
                            alertify.success('Task Approved successfully!');

                            // Update status display
                            row.find('td:nth-child(3) span')
                                .removeClass('bg-secondary')
                                .addClass('bg-success')
                                .text('Approved');

                            // Disable approve and redo buttons
                            button.prop('disabled', true);
                            row.find('.btnredo, .btnforwardredo').prop('disabled', true);

                            // Check task status
                            forwardcheckTaskStatus($('#forwardfinishTask').data('task-id'));
                        }
                    }
                });
            },
            function() {
                alertify.error('Approval canceled');
            }
        );
    });

    // Event listener for the Finish Task button
    $('#forwardfinishTask').on('click', function() {
        var finishDate = new Date().toISOString().split('T')[0];
        const taskId = $(this).data('task-id');

        if (!taskId) {
            alertify.error('Invalid Task ID. Please try again.');
            return;
        }

        // Show confirmation dialog
        alertify.confirm("Confirmation", "Are you sure you want to finish this forwarded task?",
            function() { // If user clicks "Yes"
                $.ajax({
                    type: 'POST',
                    url: '/update-forward-task',
                    data: {
                        task_id: taskId,
                        completed_date: finishDate
                    },
                    success: function(response) {
                        if (response.success) {
                            alertify.success(response.message);
                            $('#forwardfinishTask').prop('disabled', true);
                            $("#forwardviewDetails").modal('hide');
                            $('#forwardfinishTask').hide();

                            // Reload the forwarded task table
                            $('#mytask2').load(location.href + ' #mytask2', function() {
                                console.log("Table reloaded successfully.");
                            });
                        } else {
                            alertify.error(response.message || 'Failed to update the task.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating forwarded task:', error);
                        alertify.error('Error updating the forwarded task. Please try again.');
                    }
                });
            },
            function() { // If user clicks "No"
                alertify.error('Task completion cancelled.');
            }
        );
    });

    //redo reason for forward
    $(document).on('click', '.btnforwardredo', function(e) {
        e.preventDefault();

        var taskId = $(this).val(); // Get the task ID from the button value
        $('#fredoreasonModal').find('#forwardtaskId').val(taskId); // Set task ID in hidden field
        $('#fredoreasonModal').modal('show'); // Show the modal
    });

    // Submit reason to server
    $(document).on('click', '#submitfredoReason', function(e) {
        e.preventDefault();
        var reason = $('#fredoreasonText').val(); // Get the reason text
        var taskId = $('#forwardtaskId').val(); // Get the task ID from the hidden field

        if (!reason) {
            alertify.error("Please enter a reason!");
            return;
        }
        console.log(reason);
        $.ajax({
            type: 'POST',
            url: `/store-fredoreason/${taskId}`, // Route for storing reason
            data: {
                task_id: taskId,
                reason: reason,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(response) {
                if (response.status === 200) {
                    alertify.success('Reason saved successfully!');
                    // Hide modal and reset form
                    $('#fredoreasonModal').modal('hide');
                    $("#fredoreasonForm")[0].reset();
                    $('#forwardviewDetails').modal('hide');

                    // Find the row related to this task and disable buttons
                    var row = $(`button.btnforwardredo[value="${taskId}"]`).closest('tr');
                    row.find('.btnforwardapprove, .btnforwardredo').prop('disabled', true);
                } else {
                    alertify.error(response.message || 'Failed to save the reason.');
                }
            }
        });
    });
    //completed view button det
    $(document).on('click', '.CshowAssignedFaculty', function(e) {
        e.preventDefault();

        const id = $(this).val(); // Get the task ID

        if (!id) {
            console.error('Task ID not provided');
            alertify.error('Task ID is missing. Please try again.');
            return;
        }

        // Reset the modal content before showing
        $('#CtaskDetails').empty(); // Clear old task details

        // Fetch task details and render in the modal
        completedhandleTaskDetails(id);

        // Show the modal
        $('#CviewDetails').modal('show');
    });

    function completedhandleTaskDetails(taskId) {
        $.ajax({
            type: 'POST',
            url: `user/cassignedfetchdet/${taskId}`,
            success: function(response) {
                if (response.status === 200 && response.data.length > 0) {
                    let CtaskDetails = '';
                    let updata = response.updata;
                    console.log(updata);
                    let deadline = updata[0].deadline.split("T")[0];
                    let assigned_date = updata[0].assigned_date.split("T")[
                        0]; // Assume deadline is the same for all tasks

                    // Display the deadline at the top
                    $('#cassignedDetailsHeader').html(`
                                <div class="deadline-header">
                                    <strong>Assigned Date:</strong> ${assigned_date} &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                                    <strong>Deadline:</strong> ${deadline}
                                    <br>
                                </div>
                            `);
                    response.data.forEach((task, index) => {
                        let completedDate = task.completed_date ?
                            new Date(task.completed_date).toISOString().split('T')[0].split('-')
                            .reverse().join('/') :
                            '-';
                        CtaskDetails += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${task.assigned_to_name}</td>
                                        <td>${completedDate}</td>
                                    </tr>`;
                    });

                    $('#CtaskDetails').html(CtaskDetails); // Populate the modal with task details
                } else {
                    alertify.error(response.message || 'No task details found.');
                }
            }
        });
    }

    $(document).on('click', '.btnovercomplete', function(e) {
        e.preventDefault();
        var overcompleteId = $(this).val();
        var overcompletedDate = new Date().toISOString().split('T')[0];
        console.log(overcompleteId);

        alertify.confirm(
            'Confirmation',
            'Are you sure you want to complete this task?',
            function() {
                $.ajax({
                    type: 'POST',
                    url: `/overdue/complete/${overcompleteId}`,
                    data: {
                        id: overcompleteId,
                        completed_date: overcompletedDate,
                    },
                    success: function(response) {
                        if (response.status === 500) {
                            alertify.error(response.message);
                            $('#overdue1').load(location.href + ' #overdue1');
                            $('#completed1').load(location.href + ' #completed1');
                        } else {
                            alertify.success('Task Completed successfully!');
                        }
                    }
                });
            },
            function() {
                alertify.error('Completion canceled');
            }
        );
    });
    //reassign modal
    $(document).on('click', '.btnreassign', function(e) {
        e.preventDefault();
        var taskId = $(this).val();
        $('#reassignModal').find('#Reassign_task_id').val(taskId);
        $('#reassignModal').modal('show');
    });
    $(document).on('click', '#reassignsubmit', function(e) {
        e.preventDefault();
        var formData = new FormData($('#reassignform')[0]);


        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type: 'POST',
            url: `/store-reassign`,

            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    alertify.success('Task reassigned successfully!');
                    $("#reassignform")[0].reset();
                    $('#reassignModal').modal('hide');
                    $('#viewDetails').modal('hide');
                } else {
                    alertify.error(response.message || 'Failed to reassign the task.');
                }
            },
            error: function(xhr) {
                alertify.error('An error occurred: ' + xhr.responseText);
            }
        });
    });

    //reassign forward modal
    $(document).on('click', '.btnforwardreassign', function(e) {
        e.preventDefault();
        var taskId = $(this).val();
        console.log("Selected Task ID:", taskId);
        $('#forward_reassign_task_id').val(taskId); // Corrected ID
        $('#reassignforwardModal').modal('show');
    });
    $(document).on('click', '#reassignforwardsubmit', function(e) {
        e.preventDefault();

        var form = document.getElementById("forward_reassignform");
        var formData = new FormData(form);
        var token = $('meta[name="csrf-token"]').attr('content');

        formData.append('_token', token);

        var taskId = $('#forward_reassign_task_id').val(); // Get the correct task ID

        $.ajax({
            type: 'POST',
            url: `/store-reassignforward/${taskId}`,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    alertify.success('Task reassigned successfully!');
                    $("#forward_reassignform")[0].reset();
                    $('#reassignforwardModal').modal('hide');
                    $('#forwardviewDetails').modal('hide');
                } else {
                    alertify.error(response.message || 'Failed to reassign the task.');
                }
            },
            error: function(xhr) {
                alertify.error('An error occurred: ' + xhr.responseText);
            }
        });
    });


    $(document).on('click', '.btnedeadline', function(e) {
        e.preventDefault();
        let taskId = $(this).data("id");
        console.log(taskId);

        $.ajax({
            url: `/task/${taskId}`,
            type: "GET",
            success: function(response) {
                let deadline = response.deadline; // Assuming format is YYYY-MM-DD

                // Convert deadline to Date object and add one day
                let minDate = new Date(deadline);
                minDate.setDate(minDate.getDate() + 1);

                // Format date to YYYY-MM-DD for input field
                let formattedMinDate = minDate.toISOString().split('T')[0];
                let formattedolddeadline = new Date(deadline).toISOString().split('T')[0];
                $("#oldDeadline").val(formattedolddeadline);
                $("#reason").val(response.feedback);
                $("#extenddeadlinetaskId").val(taskId);

                // Set min date for the date input field
                $("#newDeadline").attr("min", formattedMinDate);

                $("#extendDeadlineModal").modal("show");
            }
        });
    });


    //extend deadline save button for assigned task
    $(document).on("submit", "#extendDeadlineForm", function(e) {
        e.preventDefault();
        let taskId = $("#extenddeadlinetaskId").val();
        let oldDeadline = $("#oldDeadline").val();
        let newDeadline = $("#newDeadline").val();
        let reason = $("#reason").val();

        let feedback = newDeadline && newDeadline > oldDeadline ? reason : null;
        let status = 1;

        let deadline = newDeadline ? newDeadline : oldDeadline;

        $.ajax({
            url: "/update-deadline",
            type: "POST",
            data: {
                task_id: taskId,
                deadline: deadline,
                feedback: feedback,
                status: status,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    alertify.success("Deadline Extended Successfully");

                    $("#extendDeadlineModal").modal("hide");
                    $(".btnedeadline").hide(); // Close modal

                } else {
                    alertify.error("Failed to update deadline!");
                }
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.error);
            },
        });
    });
    //extend deadline reject ajax for assigned task
    $(document).on("click", "#rejectDeadline", function(e) {
        e.preventDefault();

        let taskId = $("#extenddeadlinetaskId").val();

        $.ajax({
            url: "/reject-deadline",
            type: "POST",
            data: {
                task_id: taskId,
                status: 1, // Reject action
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function(response) {
                if (response.success) {
                    alertify.success("Deadline Request Rejected");
                    $("#extendDeadlineModal").modal("hide");
                    $(".btnedeadline").hide();
                } else {
                    alertify.error("Failed to reject deadline!");
                }
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.error);
            },
        });
    });
    $(document).on('click', '.btnforwardextenddeadline', function() {
        let taskId = $(this).data("id");
        let deadline = $(this).data('deadline'); // Current deadline from DB
        let mainDeadline = $(this).data('maindeadline'); // Deadline stored in the main branch
        let feedback = $(this).data('feedback');

        console.log(taskId, deadline, mainDeadline, feedback);

        let formattedOldDeadline = deadline ? new Date(deadline).toISOString().split('T')[0] : '';
        let formattedMaxDeadline = mainDeadline ? new Date(mainDeadline).toISOString().split('T')[0] : '';

        $('#forwardextendDeadlineModal').modal('show');
        $('#forwardreason').val(feedback);
        $('#forwardoldDeadline').val(formattedOldDeadline);
        $('#forwardextenddeadlinetaskId').val(taskId);

        // Set the min and max values for the date picker
        $('#forwardnewDeadline').attr({
            'min': formattedOldDeadline, // Min date is the current deadline
            'max': formattedMaxDeadline // Max date is the main branch deadline
        });
    });




    //Reason for mytask tab
    $(document).on('click', '.btnmyreason', function() {
        var taskId = $(this).val(); // Get task_id from button value

        // Make an AJAX request to fetch the reason
        $.ajax({
            url: '/fetch-reason/' + taskId, // The route to fetch reason
            type: 'GET',
            success: function(response) {
                // Check if the task exists
                if (response.tasks.length > 0) {
                    var reasonText = '';
                    // Loop through the tasks and find the reason
                    response.tasks.forEach(function(task) {
                        if (task.task_id == taskId) {
                            reasonText = task.reason;
                        }
                    });

                    // Populate the modal with the reason
                    $('#reasonDisplayText').text(reasonText);
                    $('#task_id').val(taskId); // Set the task_id in the hidden input field

                    // Open the modal
                    $('#reasonDisplayModal').modal('show');
                } else {
                    alert('No reason found for this task.');
                }
            },
            error: function(xhr, status, error) {
                console.error("There was an error fetching the reason: ", error);
                alert('Unable to fetch the reason. Please try again.');
            }
        });
    });
    //reason for forward task
    $(document).on('click', '.btnforwardreason', function() {
        let taskId = $(this).data('id'); // Get task ID from button

        $.ajax({
            type: 'POST',
            url: `/user/forwardreasonfetchdet/${taskId}`, // Fetch reason from backend
            success: function(response) {
                if (response.status === 200 && response.reason.length > 0) {
                    let reasonText = response.reason[0].reason; // Get first reason
                    $('#reasonDisplayText').text(reasonText); // Display reason in modal
                    $('#forwardtaskId').val(taskId); // Set hidden input value
                    $('#reasonDisplayModal').modal('show'); // Open modal
                } else {
                    alertify.error('No reason found for this task.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching reason:', error);
                alertify.error('An error occurred while fetching the reason.');
            }
        });
    });

    //extend deadline my-task
    $(document).on('click', '.btnextend', function() {
        var taskId = $(this).val(); // Get task_id from button value

        $('#task_id').val(taskId); // Set task ID in hidden field
        $('#reasonInput').val(''); // Clear previous reason
        $('#requestdeadlineModal').modal('show'); // Show modal
    });

    // Handle form submission
    $('#extndDeadlineForm').submit(function(e) {
        e.preventDefault();

        var taskId = $('#task_id').val();
        var reason = $('#reasonInput').val();
        var button = $('.btnextend[value="' + taskId + '"]'); // Select the button using task ID

        $.ajax({
            url: '/save-feedback',
            type: 'POST',
            data: {
                task_id: taskId,
                reason: reason, // Get reason value from input field
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                if (response.success) {
                    alertify.success("Deadline requested successfully!");
                    $("#requestdeadlineModal").modal("hide");
                    $('#extndDeadlineForm')[0].reset();
                    button.prop('disabled', true); // Close modal

                } else {
                    alertify.error("Failed to request deadline!");
                }
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.error);
            },

        });
    });


    // NEW ADD TASK
    function showDropdown() {
        let workType = document.getElementById("workType").value;

        document.getElementById("managementDropdown").style.display = "none";
        document.getElementById("cohDropdown").style.display = "none";
        document.getElementById("departmentDropdown").style.display = "none";
        document.getElementById("facultyDropdown").style.display = "none";

        if (workType === "Management") {
            document.getElementById("managementDropdown").style.display = "block";
        } else if (workType === "center of head") {
            document.getElementById("cohDropdown").style.display = "block";
        } else if (workType === "hod" || workType === "faculty") {
            document.getElementById("departmentDropdown").style.display = "block";
            if (workType === "faculty") {
                document.getElementById("facultyDropdown").style.display = "block";
            }
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        const deptDropdownBtn = document.getElementById("deptDropdownBtn");
        const deptDropdownList = document.getElementById("deptDropdownList");
        const facultyDropdownBtn = document.getElementById("facultyDropdownBtn");
        const facultyDropdownList = document.getElementById("facultyDropdownList");

        function updateSelectedDepartments() {
            const selected = Array.from(document.querySelectorAll(".dept-checkbox:checked")).map(cb => cb
                .value);
            let displayText = selected.length > 2 ? `${selected.length} selected` : selected.join(", ");

            if (displayText.length > 55) displayText = displayText.substring(0, 55) + "...";
            deptDropdownBtn.innerText = displayText || "Select Department";
            document.getElementById("selectedDepartments").value = selected.join(",");

            if (selected.length > 0) fetchFaculties(selected);
        }

        function fetchFaculties(departments) {
            let workType = document.getElementById("workType").value;
            if (workType !== "faculty") {
                return;
            }
            fetch(`/getFaculties?departments=${encodeURIComponent(departments.join(","))}`)
                .then(response => response.json())
                .then(data => {
                    facultyDropdownList.innerHTML = data.length ?
                        data.map(faculty =>
                            `<li><label><input type="checkbox" class="faculty-checkbox" value="${faculty.id}">${faculty.name} (${faculty.dept})</label></li>`
                        ).join("") :
                        "<li>No faculties found</li>";
                })
                .catch(error => console.error("Error fetching faculties:", error));
        }

        function updateSelectedFaculties() {
            let workType = document.getElementById("workType").value;
            if (workType !== "faculty") {
                return;
            }

            const selectedFaculties = Array.from(document.querySelectorAll('.faculty-checkbox:checked')).map(
                cb => cb
                .value);
            const selectedFacultyNames = Array.from(document.querySelectorAll('.faculty-checkbox:checked')).map(
                cb =>
                cb
                .parentElement.textContent.trim());

            // Display logic: if more than 2 selected, show count; otherwise, show names
            let displayText = selectedFaculties.length > 1 ?
                `${selectedFaculties.length} selected` :
                selectedFacultyNames.join(", ");

            // Limit display text to 55 characters for better UI
            if (displayText.length > 55) {
                displayText = displayText.substring(0, 55) + "...";
            }

            facultyDropdownBtn.innerText = displayText || "Select Faculty";
            document.getElementById("selectedFaculties").value = selectedFaculties.join(",");
        }


        deptDropdownBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            deptDropdownList.style.display = deptDropdownList.style.display === "block" ? "none" :
                "block";
        });

        facultyDropdownBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            facultyDropdownList.style.display = facultyDropdownList.style.display === "block" ? "none" :
                "block";
        });

        document.addEventListener("click", (e) => {

            if (!deptDropdownBtn.contains(e.target)) deptDropdownList.style.display = "none";
            if (!facultyDropdownBtn.contains(e.target)) facultyDropdownList.style.display = "none";
        });

        document.querySelectorAll(".dept-checkbox").forEach(checkbox => {
            checkbox.addEventListener("change", updateSelectedDepartments);
        });
        deptDropdownList.addEventListener("click", (e) => e.stopPropagation());
        facultyDropdownList.addEventListener("click", (e) => e.stopPropagation());

        document.addEventListener("change", (e) => {
            if (e.target.classList.contains("faculty-checkbox")) {
                updateSelectedFaculties();
            }
        });
    });





    $(document).on('click', '.btnforwardextenddeadline', function() {
        let taskId = $(this).data("id");
        let deadline = $(this).data('deadline'); // Existing deadline (format should be YYYY-MM-DD)
        let feedback = $(this).data('feedback');

        console.log(taskId, deadline, feedback);
        let formattedoldDeadline = new Date(deadline).toISOString().split('T')[0];
        $('#forwardextendDeadlineModal').modal('show');
        $('#forwardreason').val(feedback);
        $('#formattedoldDeadline').val(deadline);
        $('#forwardextenddeadlinetaskId').val(taskId);

        // Set minDate in the datepicker
        $('#forwardnewDeadline').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: new Date(deadline), // Minimum date is the existing deadline
        });
        console.log(typeof $.fn.datepicker);
    });

    // save extend deadline in forward tasks
    $(document).on("submit", "#forwardextendDeadlineForm", function(e) {
        e.preventDefault();
        let taskId = $("#forwardextenddeadlinetaskId").val();
        let foldDeadline = $("#forwardoldDeadline").val();
        let fnewDeadline = $("#forwardnewDeadline").val();
        let freason = $("#forwardreason").val();

        let feedback = fnewDeadline && fnewDeadline > foldDeadline ? freason : null;
        let status = 1;

        let deadline = fnewDeadline ? fnewDeadline : foldDeadline;

        $.ajax({
            url: "/fupdate-deadline",
            type: "POST",
            data: {
                task_id: taskId,
                deadline: deadline,
                feedback: feedback,
                status: status,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    alertify.success("Deadline Extended Successfully")

                    $("#forwardextendDeadlineModal").modal("hide");
                    $('#btnforwardextenddeadline').prop('disabled', true);
                    $("#forwardviewDetails").modal("hide");
                    $('#forwardextendDeadlineForm')[0].reset();

                    // Close modal

                } else {
                    alertify.error("Failed to update deadline!");
                }
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.error);
            },
        });
    });
    //reject extend deadline in forward task
    $(document).on("click", ".btnrejectforwardeadline", function(e) {
        e.preventDefault();
        let taskId = $("#forwardextenddeadlinetaskId").val();

        $.ajax({
            url: "/reject-fupdate-deadline",
            type: "POST",
            data: {
                task_id: taskId,
                status: 1, // Set status as rejected
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    alertify.success("Deadline Request Rejected");

                    $("#forwardextendDeadlineModal").modal("hide");
                    $('#btnforwardextenddeadline').prop('disabled', true);
                    $("#forwardviewDetails").modal("hide");
                    $('#forwardextendDeadlineForm')[0].reset();
                } else {
                    alertify.error("Failed to reject task!");
                }
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.error);
            },
        });
    });




    function updateSelecteddepartmentFaculties() {
        const selectedIds = [];
        const selectedNames = [];

        document.querySelectorAll('.deptfaculty-checkbox:checked').forEach(checkbox => {
            selectedIds.push(checkbox.value); // Store ID
            selectedNames.push(checkbox.parentNode.textContent.trim()); // Store name
        });

        const button = document.getElementById('newFacultyBtn');
        if (selectedNames.length === 0) {
            button.innerText = 'Select Faculty';
        } else if (selectedNames.length <= 2) {
            button.innerText = selectedNames.join(', ');
        } else {
            button.innerText = `${selectedNames.length} selected`;
        }

        document.getElementById('selecteddeptFaculties').value = selectedIds.join(','); // Store IDs
    }

    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    function updateSelectedforwarddepartmentFaculties() {
        const selectedIds = [];
        const selectedNames = [];

        document.querySelectorAll('.fdeptfaculty-checkbox:checked').forEach(checkbox => {
            selectedIds.push(checkbox.value); // Store selected faculty IDs
            selectedNames.push(checkbox.parentNode.textContent.trim()); // Store selected faculty names
        });

        const button = document.getElementById('fnewFacultyBtn');

        if (selectedNames.length === 0) {
            button.innerText = 'Select Faculty';
        } else if (selectedNames.length <= 2) {
            button.innerText = selectedNames.join(',');
        } else {
            button.innerText = `${selectedNames.length} selected`;
        }

        document.getElementById('selectedforwarddeptFaculties').value = selectedIds.join(','); // Store selected IDs
    }

    // forward coh
    function updateSelectedcoordinators() {
        let checkboxes = document.querySelectorAll('.coordinator-checkbox:checked');
        let selectedValues = [];
        let selectedNames = [];

        checkboxes.forEach((checkbox) => {
            selectedValues.push(checkbox.value);
            selectedNames.push(checkbox.parentNode.textContent.trim());
        });

        let button = document.getElementById('coordinatorBtn');

        if (selectedNames.length === 0) {
            button.textContent = "Select";
        } else if (selectedNames.length <= 2) {
            button.textContent = selectedNames.join(', ');
        } else {
            button.textContent = selectedNames.length + " Selected";
        }
        document.getElementById('selectedcoordinators').value = selectedValues.join(',');
    }
    // REASSIGN TASK
    function Reassign_showDropdown() {
        var workType = document.getElementById("Reassign_workType").value;
        var departmentDropdown = document.getElementById("Reassign_departmentDropdown");
        var facultyDropdown = document.getElementById("Reassign_facultyDropdown");
        if (workType === "hod") {
            departmentDropdown.style.display = "block";
            facultyDropdown.style.display = "none";
        } else if (workType === "faculty") {
            departmentDropdown.style.display = "block";
            facultyDropdown.style.display = "block";
        } else {
            departmentDropdown.style.display = "none";
            facultyDropdown.style.display = "none";
        }
    }

    function Reassign_updateSelecteddepartment() {
        var selectedDepartment = document.getElementById("Reassign_selectedDepartment").value;
        document.getElementById("Reassign_selectedDepartments").value = selectedDepartment;
        if (selectedDepartment) {
            fetchFacultyOptions(selectedDepartment);
        } else {
            document.getElementById("Reassign_selectedFaculty").innerHTML = '<option value="">Select Faculty</option>';
        }
    }

    function fetchFacultyOptions(department) {
        fetch(`/get-faculty-by-department/${department}`)
            .then(response => response.json())
            .then(data => {
                var facultyDropdown = document.getElementById("Reassign_selectedFaculty");
                facultyDropdown.innerHTML = '<option value="">Select Faculty</option>';
                data.forEach(faculty => {
                    facultyDropdown.innerHTML += `<option value="${faculty.id}">${faculty.name}</option>`;
                });
            })
            .catch(error => console.error("Error fetching faculty:", error));
    }

    function Reassign_updateSelectedFaculties() {
        var selectedFaculty = document.getElementById("Reassign_selectedFaculty").value;
        document.getElementById("Reassign_selectedFaculties").value = selectedFaculty;
    }



    function forward_reassign_updateSelectedforwarddepartmentFaculties(selectedId, selectedName) {
        let facultyButton = document.getElementById('forward_reassign_newFacultyBtn');
        let facultyInput = document.getElementById('forward_reassign_selectedforwarddeptFaculties');

        if (facultyButton && facultyInput) {
            // Update button text with selected faculty name
            facultyButton.innerText = selectedName;

            // Store selected faculty ID in hidden input field
            facultyInput.value = selectedId;

            // Close the dropdown menu
            let dropdownMenu = document.getElementById('forward_reassign_newFacultyDropdown');
            let dropdownInstance = bootstrap.Dropdown.getInstance(facultyButton);
            if (dropdownInstance) {
                dropdownInstance.hide();
            }
        } else {
            console.error("Error: Required elements not found in DOM.");
        }
    }
    </script>



    <script>
    async function checkforwardreassign(assigned_to_id) {
        try {
            let response = await $.ajax({
                url: "btnshow/forwardreassign/",
                type: "POST",
                data: {
                    assigned_to_id: assigned_to_id
                }
            });
            return response.success;
        } catch (error) {
            console.error("Error in checkforwardreassign:", error);
            return false;
        }
    }
    $(document).ready(function() {
        $('#forwardreasonDisplayModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var reasonText = button.val();

            $('#forwardreasonDisplayText').text(reasonText);
        });
    });
    </script>
</body>

</html>