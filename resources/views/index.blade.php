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

    <style>

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
            <a href="{{route('index')}}" class="menu-item">
                <i class="fas fa-bus text-warning"></i>
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
                <span>{{$facultyName}}</span>
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
                            <button class="nav-link active" id="dash-bus-tab" data-bs-toggle="tab" data-bs-target="#dashboard"
                                type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
                                <i class="fa-solid fa-folder-open fa-bounce"></i>&nbsp;Dashboard
                            </button>
                        </div>
                    </li>

                    <!-- Assigned Task (Only for Principal (0), Management Heads (1), HOD (3)) -->
                    @if($specialStatus == 0 || $specialStatus == 1 || $specialStatus == 3 )
                    <li class="nav-item" role="presentation">
                        <div id="navref2">
                            <button class="nav-link" id="pend-bus-tab" data-bs-toggle="tab"
                                data-bs-target="#assignedtask" type="button" role="tab"
                                aria-controls="profile-tab-pane" aria-selected="false">
                                <i class="fa-solid fa-bell fa-shake "></i>&nbsp;Assigned Task
                            </button>
                        </div>
                    </li>
                    @endif

                    <!-- My Task (Only for Management Heads (1), Center Heads (2), HOD (3), Faculty (4)) -->
                    @if($specialStatus == 1 || $specialStatus == 2 || $specialStatus == 3 || $specialStatus == 4 )
                    <li class="nav-item" role="presentation">
                        <div id="navref3">
                            <button class="nav-link" id="mytask-bus-tab" data-bs-toggle="tab" data-bs-target="#mytask"
                                type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">
                                <i class="fa-solid fa-exclamation fa-beat-fade" style="--fa-beat-fade-opacity: 0.1; --fa-beat-fade-scale: 1.25;"></i>&nbsp;My Task
                            </button>
                        </div>
                    </li>
                    @endif

                    <!-- Completed Task (Visible to Everyone) -->
                    <li class="nav-item" role="presentation">
                        <div id="navref4">
                            <button class="nav-link" id="comp-bus-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button"
                                role="tab" aria-controls="disabled-tab-pane" aria-selected="false">
                                <i class="fa-solid fa-check fa-beat" style="--fa-animation-duration: 1.5s;"></i>&nbsp;Completed Task
                            </button>
                        </div>
                    </li>

                    <!-- History (Visible to Everyone) -->
                    <li class="nav-item" role="presentation">
                        <div id="navref5">
                            <button class="nav-link" id="rej-bus-tab" data-bs-toggle="tab"
                                data-bs-target="#history" type="button" role="tab"
                                aria-controls="disabled-tab-pane" aria-selected="false">
                                <i class="fa-solid fa fa-history fa-spin"
                                    style="--fa-flip-x: 1; --fa-flip-y: 0;"></i>&nbsp;History
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
                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color:rgb(252, 119, 71);">
                                                    <div class="text-center">
                                                        <i class="fas fa-bell fa-lg"></i>
                                                        <h1>{{$dashboard_assigned_task}}</h1>
                                                        <small>Assigned Tasks </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color:rgb(241, 74, 74);">
                                                    <div class="text-center">
                                                        <i class="fa fa-tasks fa-lg"></i>
                                                        <h1>{{$dashboardcombinedTasks}}</h1>
                                                        <small>My Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color:rgb(70, 160, 70);">
                                                    <div class="text-center">
                                                        <i class="fas fa-check fa-lg"></i>
                                                        <h1>{{$dashboard_completed_tasks}}</h1>
                                                        <small>Completed Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-12 col-md-3">
                                                <div class="circle-card" style="background-color: rgb(187, 187, 35);">
                                                    <div class="text-center">
                                                        <i class="fas fa-exclamation fa-lg"></i>
                                                        <h1>{{$dashboard_overdueTasks}}</h1>
                                                        <small>Overdue Tasks</small>
                                                    </div>
                                                </div>
                                            </div>
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
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addtask">Add Task</button>
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
                                    @php $sno=1; $currentTaskId = null; @endphp
                                    @foreach ($assigned_task as $at)
                                    @if ($currentTaskId !== $at->task_id)
                                    @php $currentTaskId = $at->task_id;
                                    $taskDeadline = \Carbon\Carbon::parse($at->deadline)->startOfDay();
                                    $isDeadlineExtended = $taskDeadline->lessThan($currentDate);
                                    $isToday = $taskDeadline->equalTo($currentDate);
                                    @endphp
                                    <tr class="{{ $isDeadlineExtended ? 'table-danger' : ($isToday ? 'table-warning' : '') }}">
                                        <td class="text-center">{{$sno++}}</td>
                                        <td class="text-center" rowspan="{{ $assigned_task->where('task_id', $currentTaskId)->count() }}">
                                            {{$at->task_id}}
                                        </td>
                                        <td class="text-center">{{$at->title}}</td>
                                        <td class="text-center">{{$at->description}}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info showAssignedFaculty" value="{{$at->task_id}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Click to approve">View</button>
                                        </td>
                                        <td class="text-center">{{\Carbon\Carbon::parse($at->deadline)->format('d-m-Y') }}</td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td class="text-center">{{$sno++}}</td>
                                        <td class="text-center">{{$at->title}}</td>
                                        <td class="text-center">{{$at->description}}</td>
                                        <td class="text-center">{{$at->assigned_to_name}}</td>
                                        <td class="text-center">{{$at->deadline}}</td>
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
                                        <input type="hidden" id="hidden_faculty_id" value="{{$facultyId}}" name="faculty_id">
                                        <input type="hidden" id="hidden_faculty_name" value="{{$facultyName}}" name="faculty_name">

                                        <div class="mb-3">
                                            <label>Select Role:</label><br>
                                            <select class="form-control" style="width: 100%; height:36px;" name="type" id="type" onchange="toggleSelection()">
                                                <option value="" disabled selected>Select</option>
                                                <option value="hod">HOD</option>
                                                <option value="faculty">Faculty</option>
                                            </select>
                                        </div>

                                        <!-- Multiple Departments for HOD -->
                                        <div id="hod-section" style="display: none;">
                                            <div class="form-group">
                                                <label for="hod-departments">Departments</label>
                                                <div class="dropdown" style="width: 100%; position: relative;">
                                                    <button type="button" class="form-control" id="hod-departments-btn" aria-expanded="false" onclick="toggleDropdown('hod-departments-dropdown', 'hod-departments-btn')" style="text-align : left;">Select</button>
                                                    <div class="dropdown-content" id="hod-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                        <label><input type="checkbox" id="hod-selectAll" value="all"> All</label><br>
                                                        @foreach ($dept as $d)
                                                        <label><input type="checkbox" name="hod-dname[]" value="{{ $d->dname }}" class="hod-checkbox-item"> {{ $d->dname }}</label><br>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Multiple Departments for Faculty -->
                                        <div id="faculty-section" style="display: none;">
                                            <div class="form-group">
                                                <label for="faculty-departments">Departments</label>
                                                <div class="dropdown" style="width: 100%; position: relative;">
                                                    <button type="button" class="form-control" id="faculty-departments-btn" aria-expanded="false" onclick="toggleDropdown('faculty-departments-dropdown', 'faculty-departments-btn')" style="text-align : left;">Select</button>
                                                    <div class="dropdown-content" id="faculty-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                        <label><input type="checkbox" id="faculty-selectAll" value="all"> All</label><br>
                                                        @foreach ($dept as $d)
                                                        <label><input type="checkbox" name="faculty-dname[]" value="{{ $d->dname }}" class="faculty-checkbox-item"> {{ $d->dname }}</label><br>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="department-faculty">Faculty</label>
                                                <div class="dropdown" style="width: 100%; position: relative;">
                                                    <button type="button" class="form-control" id="department-faculty-btn" aria-expanded="false" onclick="toggleDrop('departments-faculty-dropdown', 'department-faculty-btn')" style="text-align : left;">Select</button>
                                                    <div class="dropdown-content" id="departments-faculty-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input class="form-control" type="text" id="title"
                                                name="title" placeholder="Enter Title" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Task Description</label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                placeholder="Enter Description" required>
                                        </div>
                                        <input type="hidden" name="status" value="0">

                                        <div class="mb-3">
                                            <label for="deadline" class="form-label">Deadline</label>
                                            <input type="date" class="form-control" name="deadline"
                                                id="deadline" required>
                                        </div>

                                        <input type="hidden" class="form-control" name="assigned_date" id="assigned_date" required>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" id="submitDepartments">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!----------------------- My Task Starts ------------------------------------->
                    <div class="tab-pane fade" id="mytask" role="tabpanel" aria-labelledby="contact-tab"
                        tabindex="0">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @if($specialStatus == 1 || $specialStatus == 2 || $specialStatus == 3 || $specialStatus == 4 )
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="work-bus-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">My task</button>
                            </li>
                            @endif
                            @if($specialStatus == 3 || $specialStatus == 1||$specialStatus == 2)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="work-bus-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Forwarded task</button>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
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
                                                $task_Deadline = \Carbon\Carbon::parse($my->deadline)->startOfDay();
                                                $isToday = $task_Deadline->equalTo($currentDate);
                                                $reasonExist = !is_null($my->reason);
                                                @endphp
                                                <tr class="{{ $isToday ? 'table-warning' : ''}}">
                                                    <td class="text-center">{{$sno++}}</td>
                                                    <td class="text-center">{{$my->task_id }}</td>
                                                    <td class="text-center">{{$my->assigned_by_name}}</td>
                                                    <td class="text-center">{{$my->title}}</td>
                                                    <td class="text-center">{{$my->description}}</td>
                                                    <td class="text-center">
                                                        @if(($specialStatus == 3 && $my->status == 0) || ($specialStatus == 4 && $my->status == 0) || ($specialStatus === null))
                                                        <button type="button" class="btn btn-success btnaccept"
                                                            value="{{$my->task_id}}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        @elseif(($specialStatus == 1 && $my->status == 0) || ($specialStatus == 2 && $my->status == 0))
                                                        <button type="button"
                                                            class="btn btn-primary showImage"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#forwardModal"
                                                            data-task-id="{{ $my->task_id }}"
                                                            data-status="{{ $my->status }}"
                                                            data-deadline="{{ $my->deadline }}"> <!-- Add the deadline as a data attribute -->
                                                            <i class="fas fa-share"></i>
                                                        </button>
                                                        @elseif(($specialStatus == 3 && $my->status == 1) || ($specialStatus == 4 && $my->status == 1) || ($specialStatus === null))
                                                        <button type="button" class="btn btn-success btncomplete"
                                                            value="{{$my->task_id}}">
                                                            <i class="fas fa-circle"></i>
                                                        </button>
                                                        @elseif(($specialStatus == 3 && $my->status == 2) || ($specialStatus == 4 && $my->status == 2) || ($specialStatus === null))
                                                        <button type="button" class="btn btn-secondary "
                                                            value="{{$my->task_id}}" disabled>
                                                            <i class="fas fa-circle"></i>
                                                        </button>


                                                        @endif
                                                                <!-- Hide the button if reason does not exist -->
                                                        @if($reasonExist)
                                                        <button type="button" class="btn btn-secondary btnmyreason" value="{{$my->task_id}}">
                                                            <i class="fas fa-light fa-message"></i>
                                                        </button>
                                                        @endif

                                                    <td class="text-center">{{\Carbon\Carbon::parse($my->deadline)->format('d-m-Y') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
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
                                            @php $sno=1; $current_TaskId = null; @endphp
                                            @foreach ($forwarded_task as $for)
                                            @if ($current_TaskId !== $for->task_id)
                                            @php $current_TaskId = $for->task_id;
                                            $taskDeadline = \Carbon\Carbon::parse($for->deadline)->startOfDay();
                                            $isDeadlineExtended = $taskDeadline->lessThan($currentDate);
                                            $isToday = $taskDeadline->equalTo($currentDate);
                                            @endphp
                                            @endif
                                            <tr class="{{ $isDeadlineExtended ? 'table-danger' : ($isToday ? 'table-warning' : '') }}">
                                                <td class="text-center">{{$sno++}}</td>
                                                <td class="text-center">{{$for->task_id}}</td>
                                                <td class="text-center">{{$for->assigned_by_id}} - {{$for->assigned_by_name}} </td>
                                                <td class="text-center">{{$for->title}}</td>
                                                <td class="text-center">{{$for->description}}</td>
                                                <td class="text-center"><button type="button" class="btn btn-info showForwardedFaculty" value="{{$for->task_id}}-{{$for->assigned_by_id}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Click to approve">View</button></td>
                                                <td class="text-center">{{\Carbon\Carbon::parse($for->deadline)->format('d-m-Y') }}</td>
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
                    <div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Forward Task</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="forwardform" enctype="multipart/form-data">
                                        <input type="hidden" name="task_id" id="task_id" value="">
                                        <input type="hidden" name="status" id="status" value="">
                                        <input type="hidden" id="hidden_faculty_id" value="{{$facultyId}}" name="faculty_id">
                                        <input type="hidden" id="hidden_faculty_name" value="{{ $facultyName }}" name="faculty_name">
                                        <div class="mb-3">
                                            <label>Select Role:</label><br>
                                            <select class="form-control" style="width: 100%; height:36px;" name="forwardtype" id="forwardtype" onchange="toggleForwardTaskSelection()">
                                                <option value="" disabled selected>Select</option>
                                                <option value="hod">HOD</option>
                                                <option value="faculty">Faculty</option>
                                            </select>
                                        </div>

                                        <!-- Multiple Departments for HOD -->
                                        <div id="forward-hod-section" style="display: none;">
                                            <div class="form-group">
                                                <label for="forward-hod-departments">Departments</label>
                                                <div class="dropdown" style="width: 100%; position: relative;">
                                                    <button type="button" class="form-control" id="forward-hod-departments-btn" aria-expanded="false" onclick="toggleForwardTaskDropdown('forward-hod-departments-dropdown', 'forward-hod-departments-btn')" style="text-align : left;">Select</button>
                                                    <div class="dropdown-content" id="forward-hod-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                        <label><input type="checkbox" id="forward-hod-selectAll" value="all"> All</label><br>
                                                        @foreach ($dept as $d)
                                                        <label><input type="checkbox" name="forward-hod-dname[]" value="{{ $d->dname }}" class="forward-hod-checkbox-item"> {{ $d->dname }}</label><br>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Multiple Departments for Faculty -->
                                        <div id="forward-faculty-section" style="display: none;">
                                            <div class="form-group">
                                                <label for="forward-faculty-departments">Departments</label>
                                                <div class="dropdown" style="width: 100%; position: relative;">
                                                    <button type="button" class="form-control" id="forward-faculty-departments-btn" aria-expanded="false" onclick="toggleForwardTaskDropdown('forward-faculty-departments-dropdown', 'faculty-departments-btn')" style="text-align : left;">Select</button>
                                                    <div class="forward-dropdown-content" id="forward-faculty-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                        <label><input type="checkbox" id="forward-faculty-selectAll" value="all"> All</label><br>
                                                        @foreach ($dept as $d)
                                                        <label><input type="checkbox" name="forward-faculty-dname[]" value="{{ $d->dname }}" class="forward-faculty-checkbox-item"> {{ $d->dname }}</label><br>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="forward-department-faculty">Faculty</label>
                                                <div class="dropdown" style="width: 100%; position: relative;">
                                                    <button type="button" class="form-control" id="forward-department-faculty" aria-expanded="false" onclick="toggleForwardTaskDrop('forward-departments-faculty-dropdown', 'forward-department-faculty')" style="text-align : left;">Select</button>
                                                    <div class="dropdown-content" id="forward-departments-faculty-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="deadline" class="form-label">Deadline</label>
                                            <input type="date" class="form-control" name="forwarddeadline" id="forwarddeadline" required>
                                        </div>

                                        <input type="hidden" class="form-control" name="forwarded_date" id="forwarded_date" required>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" id="submitDepartments">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!----------------------------Completed Task starts ---------------------------------------------------->

                    <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="disabled-tab"
                        tabindex="0">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @if($specialStatus == 4 || $specialStatus == 5 )
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="completed-bus-tab" data-bs-toggle="tab" data-bs-target="#homeu-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">My tasks</button>
                            </li>
                            @endif
                            @if($specialStatus == 3 || $specialStatus == 0 || $specialStatus == 1  ||$specialStatus == 2)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link " id="completed-bus-tab" data-bs-toggle="tab" data-bs-target="#profileu-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Assigned task</button>
                            </li>
                            @endif
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="homeu-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
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

                            <div class="tab-pane fade" id="profileu-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
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
                                                <td class="text-center">{{$sno++}}</td>
                                                <td class="text-center">{{$at->task_id}}</td>
                                                <td class="text-center">{{$at->title}}</td>
                                                <td class="text-center">{{$at->description}}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info CshowAssignedFaculty" value="{{$at->task_id}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Click to approve">View</button>
                                                </td>
                                                <td class="text-center">{{\Carbon\Carbon::parse($at->deadline)->format('d-m-Y') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!----------------------------Completed Task Table Ends ------------------------------------->

                    <!----------------------------History Table starts ------------------------------------->
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
                        <div class="filter-container-inline">
                            <label for="start_date" class="filter-label-inline">Start Date:</label>
                            <input type="date" id="start_date" class="filter-input-inline">
                            <label for="end_date" class="filter-label-inline">End Date:</label>
                            <input type="date" id="end_date" class="filter-input-inline">
                            <button id="filter_button" class="filter-button-inline">Filter</button>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col">
                                <div id="pie_chart" style="max-width: 500px; margin-left: 50px;"></div>
                            </div>
                            <div class="col">
                                <center>
                                    <div class="date-card" id="date-card" style="display: none;">
                                        <div>
                                            Demerit Points<br><strong id="demerit-points">0</strong>
                                        </div>
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!------------------------------- Faculty details modal ----------------------------------->
            <div class="modal fade" id="viewDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="shadow-lg modal-content rounded-3">
                        <div class="text-white modal-header bg-primary">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Faculty Details</h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-2 mb-3 rounded d-flex justify-content-between bg-light " id="forwardfacultyDetailsHeader" style="color: #333; font-weight: bold;">
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
                                <tbody id="taskDetails">
                                    <!-- Task details will be appended here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btnfinish" id="finishTask" data-task-id="" disabled>Finish Task</button>
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
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-3 mb-3 rounded shadow-sm d-flex justify-content-between"
                                style=" color: #333; font-weight: bold;"
                                id="cassignedDetailsHeader">
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
                            <button type="button" class="text-white btn fw-bold" style="background: linear-gradient(135deg, #43cea2, #185a9d);" data-bs-dismiss="modal">Close</button>
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
            <div class="modal fade" id="fredoreasonModal" tabindex="-1" aria-labelledby="reasonfModalLabel" aria-hidden="true">
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
            <div class="modal fade" id="forwardviewDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="shadow-lg modal-content rounded-3">
                        <div class="text-white modal-header bg-primary">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Faculty Details</h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-2 mb-3 rounded d-flex justify-content-between bg-light " id="forwardassignedDetailsHeader"></div>
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
                            <button type="button" class="btn btn-primary btnfinish" id="forwardfinishTask" data-task-id="" disabled>Finish Task</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--------------------------------- reassigned faculty modal ----------------------------------->
            <div class="modal fade" id="reassignModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Forward Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="reassignForm">
                                <input type="hidden" id="reassigntaskId" name="task_id">
                                <h5>Faculty_id</h5>
                                <input type="text" id="faculty_id" class="form-control" placeholder="Enter">
                                <h5>Name</h5>
                                <input type="text" id="name" class="form-control" placeholder="Enter name">

                                {{-- <form id="reassignform" enctype="multipart/form-data">
                                <input type="hidden" name="task_id" id="task_id" value="">
                                <input type="hidden" name="status" id="status" value="">
                                <input type="hidden" id="hidden_faculty_id" value="{{$facultyId}}" name="faculty_id">
                                <input type="hidden" id="hidden_faculty_name" value="{{ $facultyName }}" name="faculty_name">
                                <div class="mb-3">
                                    <label>Select Role:</label><br>
                                    <select class="form-control" style="width: 100%; height:36px;" name="reassigntype" id="reassigntype" onchange="toggleForwardTaskSelection()">
                                        <option value="" disabled selected>Select</option>
                                        <option value="hod">HOD</option>
                                        <option value="faculty">Faculty</option>
                                    </select>
                                </div>

                                <!-- Multiple Departments for HOD -->
                                <div id="forward-hod-section" style="display: none;">
                                    <div class="form-group">
                                        <label for="forward-hod-departments">Departments</label>
                                        <div class="dropdown" style="width: 100%; position: relative;">
                                            <button type="button" class="form-control" id="forward-hod-departments-btn" aria-expanded="false" onclick="toggleForwardTaskDropdown('forward-hod-departments-dropdown', 'forward-hod-departments-btn')" style="text-align : left;">Select</button>
                                            <div class="dropdown-content" id="forward-hod-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                <label><input type="checkbox" id="forward-hod-selectAll" value="all"> All</label><br>
                                                @foreach ($dept as $d)
                                                <label><input type="checkbox" name="forward-hod-dname[]" value="{{ $d->dname }}" class="forward-hod-checkbox-item"> {{ $d->dname }}</label><br>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Departments for Faculty -->
                                <div id="forward-faculty-section" style="display: none;">
                                    <div class="form-group">
                                        <label for="forward-faculty-departments">Departments</label>
                                        <div class="dropdown" style="width: 100%; position: relative;">
                                            <button type="button" class="form-control" id="forward-faculty-departments-btn" aria-expanded="false" onclick="toggleForwardTaskDropdown('forward-faculty-departments-dropdown', 'faculty-departments-btn')" style="text-align : left;">Select</button>
                                            <div class="forward-dropdown-content" id="forward-faculty-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                <label><input type="checkbox" id="forward-faculty-selectAll" value="all"> All</label><br>
                                                @foreach ($dept as $d)
                                                <label><input type="checkbox" name="forward-faculty-dname[]" value="{{ $d->dname }}" class="forward-faculty-checkbox-item"> {{ $d->dname }}</label><br>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="forward-department-faculty">Faculty</label>
                                        <div class="dropdown" style="width: 100%; position: relative;">
                                            <button type="button" class="form-control" id="forward-department-faculty" aria-expanded="false" onclick="toggleForwardTaskDrop('forward-departments-faculty-dropdown', 'forward-department-faculty')" style="text-align : left;">Select</button>
                                            <div class="dropdown-content" id="forward-departments-faculty-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Deadline</label>
                                    <input type="date" class="form-control" name="forwarddeadline" id="forwarddeadline" required>
                                </div>
                                <input type="hidden" class="form-control" name="forwarded_date" id="forwarded_date" required> --}}
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary"
                                        id="reassignsubmit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- reassigned faculty modal --}}
            <div class="modal fade" id="reassignforwardModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Forward Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <form id="reassignforwardForm">
                                <input type="hidden" id="reassignforwardtaskId" name="task_id">
                                <h5>Faculty_id</h5>
                                <input type="text" id="ffaculty_id" class="form-control" placeholder="Enter">
                                <h5>Name</h5>
                                <input type="text" id="fname" class="form-control" placeholder="Enter name">

                                {{-- <form id="reassignform" enctype="multipart/form-data">
                                <input type="hidden" name="task_id" id="task_id" value="">
                                <input type="hidden" name="status" id="status" value="">
                                <input type="hidden" id="hidden_faculty_id" value="{{$facultyId}}" name="faculty_id">
                                <input type="hidden" id="hidden_faculty_name" value="{{ $facultyName }}" name="faculty_name">
                                <div class="mb-3">
                                    <label>Select Role:</label><br>
                                    <select class="form-control" style="width: 100%; height:36px;" name="reassigntype" id="reassigntype" onchange="toggleForwardTaskSelection()">
                                        <option value="" disabled selected>Select</option>
                                        <option value="hod">HOD</option>
                                        <option value="faculty">Faculty</option>
                                    </select>
                                </div>

                                <!-- Multiple Departments for HOD -->
                                <div id="forward-hod-section" style="display: none;">
                                    <div class="form-group">
                                        <label for="forward-hod-departments">Departments</label>
                                        <div class="dropdown" style="width: 100%; position: relative;">
                                            <button type="button" class="form-control" id="forward-hod-departments-btn" aria-expanded="false" onclick="toggleForwardTaskDropdown('forward-hod-departments-dropdown', 'forward-hod-departments-btn')" style="text-align : left;">Select</button>
                                            <div class="dropdown-content" id="forward-hod-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                <label><input type="checkbox" id="forward-hod-selectAll" value="all"> All</label><br>
                                                @foreach ($dept as $d)
                                                <label><input type="checkbox" name="forward-hod-dname[]" value="{{ $d->dname }}" class="forward-hod-checkbox-item"> {{ $d->dname }}</label><br>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Departments for Faculty -->
                                <div id="forward-faculty-section" style="display: none;">
                                    <div class="form-group">
                                        <label for="forward-faculty-departments">Departments</label>
                                        <div class="dropdown" style="width: 100%; position: relative;">
                                            <button type="button" class="form-control" id="forward-faculty-departments-btn" aria-expanded="false" onclick="toggleForwardTaskDropdown('forward-faculty-departments-dropdown', 'faculty-departments-btn')" style="text-align : left;">Select</button>
                                            <div class="forward-dropdown-content" id="forward-faculty-departments-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                                <label><input type="checkbox" id="forward-faculty-selectAll" value="all"> All</label><br>
                                                @foreach ($dept as $d)
                                                <label><input type="checkbox" name="forward-faculty-dname[]" value="{{ $d->dname }}" class="forward-faculty-checkbox-item"> {{ $d->dname }}</label><br>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="forward-department-faculty">Faculty</label>
                                        <div class="dropdown" style="width: 100%; position: relative;">
                                            <button type="button" class="form-control" id="forward-department-faculty" aria-expanded="false" onclick="toggleForwardTaskDrop('forward-departments-faculty-dropdown', 'forward-department-faculty')" style="text-align : left;">Select</button>
                                            <div class="dropdown-content" id="forward-departments-faculty-dropdown" style="display: none; background-color: #f9f9f9; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Deadline</label>
                                    <input type="date" class="form-control" name="forwarddeadline" id="forwarddeadline" required>
                                </div>
                                <input type="hidden" class="form-control" name="forwarded_date" id="forwarded_date" required> --}}
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="reassignforwardsubmit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
<<<<<<< HEAD
            {{-- extend deadline modal --}}
            <div class="modal fade" id="extendDeadlineModal" tabindex="-1" aria-labelledby="extendDeadlineLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
=======
            {{-- REASON DISPLAY MODAL--}}
            <div class="modal fade" id="reasonDisplayModal" tabindex="-1" aria-labelledby="reasonfModalLabel" aria-hidden="true">
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
>>>>>>> 706292d3b49543a0d9e8b16c0f04bd1b34536c16
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
                    <a href="https://www.linkedin.com/company/technology-innovation-hub-mkce/"><i class="fab fa-linkedin"></i></a>
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

        <!-- FOR HISTORY -->
        <script>
            function displayDates() {
                const fromDate = document.getElementById("start_date").value;
                const toDate = document.getElementById("end_date").value;
                if (fromDate && toDate) {
                    alert(`From Date: ${fromDate}\nTo Date: ${toDate}`);
                } else {
                    alert("Please select both dates!");
                }
            }
            document.getElementById('filter_button').addEventListener('click', function() {
                // Get the selected dates
                const fromDate = document.getElementById('start_date').value;
                const toDate = document.getElementById('end_date').value;

                if (!fromDate || !toDate) {
                    alert('Please select both dates.');
                    return;
                }
                const dateCard = document.getElementById('date-card');
                dateCard.style.display = 'block';
                setTimeout(() => {
                    dateCard.classList.add('visible'); // Trigger the animation
                }, 100); // Slight delay to ensure smooth transition
                // Make an AJAX request to the backend
                fetch('/get-demerit-points', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            start_date: fromDate,
                            end_date: toDate
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update the demerit points in the DOM
                        document.getElementById('demerit-points').textContent = data.demerit_points;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching demerit points.');
                    });
            });
        </script>

        <!--Pie chart---->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const renderPieChart = (data) => {
                    const {
                        assigned = 0,
                            forwarded = 0,
                            mytask = 0,
                            completed = 0,
                            overdue = 0,
                            compmt = 0,
                            compat = 0
                    } = data;

                    const options = {
                        chart: {
                            height: 350,
                            type: "pie",
                        },
                        series: [assigned, forwarded, mytask, completed, overdue],
                        labels: [
                            "Assigned Task",
                            "Forwarded Task",
                            "My Task",
                            "Completed Task",
                            "Overdue Task"
                        ],
                        colors: ["#0000FF", "#FFA500", "#c20d94", "#00FF00", "#FF0000"],
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: true,
                            custom: function({
                                seriesIndex,
                                w
                            }) {
                                // Custom tooltip for Completed Task
                                if (seriesIndex === 3) {
                                    return `<div style="padding: 5px; background-color: #00FF00; color: #fff; border-radius: 5px;">
                                        <span>Completed My Task: ${compmt}</span><br/>
                                        <span>Completed Assigned Task: ${compat}</span>
                                    </div>`;
                                } else {
                                    // Default tooltip for other tasks
                                    return `<div style="padding: 5px; background-color: ${w.config.colors[seriesIndex]}; color: #fff; border-radius: 5px;">
                                        <span>${w.config.labels[seriesIndex]}: ${w.globals.series[seriesIndex]}</span>
                                    </div>`;
                                }
                            }
                        },
                    };

                    const chart = new ApexCharts(
                        document.querySelector("#pie_chart"),
                        options
                    );
                    chart.render();
                };
                const fetchChartData = async () => {
                    const startDate = document.getElementById("start_date").value;
                    const endDate = document.getElementById("end_date").value;
                    if (!startDate || !endDate) {
                        alert("Please select both start and end dates.");
                        return;
                    }

                    try {
                        const response = await fetch(`chart-data?start_date=${startDate}&end_date=${endDate}`);

                        if (!response.ok) {
                            throw new Error(`Server Error: ${response.statusText}`);
                        }
                        const data = await response.json();
                        if (data.error) {
                            alert(`Error: ${data.error}`);
                            return;
                        }
                        // Clear the previous chart and render the new data
                        document.querySelector("#pie_chart").innerHTML = "";
                        renderPieChart(data);
                    } catch (error) {
                        console.error("Error fetching chart data:", error);
                        alert(
                            "An error occurred while fetching data. Check console for details."
                        );
                    }
                };
                document
                    .getElementById("filter_button")
                    .addEventListener("click", fetchChartData);
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
                document.getElementById('modal-title').innerHTML = `<img src="https://icon-library.com/images/description-icon/description-icon-26.jpg" alt="Task Icon" class="modal-icon"> Description`;
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

            // for dropdown
            function toggleSelection() {
                const type = document.getElementById('type').value;
                document.getElementById('hod-section').style.display = type === 'hod' ? 'block' : 'none';
                document.getElementById('faculty-section').style.display = type === 'faculty' ? 'block' : 'none';
            }

            // Common function for toggling dropdown visibility
            function toggleDropdown(dropdownId, buttonId) {
                const dropdown = document.getElementById(dropdownId);
                const button = document.getElementById(buttonId);
                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';
                button.setAttribute('aria-expanded', !isVisible);
            }

            // Com+mon function for updating button text based on selected items
            function updateButtonText(dropdownId, buttonId, checkboxClass) {
                const selectedCheckboxes = Array.from(document.querySelectorAll(`.${checkboxClass}:checked`))
                    .map(checkbox => checkbox.parentElement.textContent.trim());
                const button = document.getElementById(buttonId);
                if (selectedCheckboxes.length === 0) {
                    button.textContent = 'Select';
                } else if (selectedCheckboxes.length <= 2) {
                    button.textContent = selectedCheckboxes.join(', ');
                } else {
                    button.textContent = `${selectedCheckboxes.length} selected`;
                }
            }

            // Common function for handling "Select All" and individual checkboxes
            function handleCheckboxSelection(selectAllId, checkboxClass, buttonId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = document.querySelectorAll(`.${checkboxClass}`);

                // Update all checkboxes when "Select All" is checked/unchecked
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
                    updateButtonText(selectAllId, buttonId, checkboxClass);
                });

                // Update "Select All" and button text when individual checkboxes are changed
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        selectAll.checked = allChecked;
                        updateButtonText(selectAllId, buttonId, checkboxClass);
                    });
                });
            }
            document.getElementById('submitDepartments').addEventListener('click', function() {
                // Get all checked checkboxes and collect their department names
                const selectedCheckboxes = Array.from(document.querySelectorAll('.checkbox-item:checked'))
                    .map(checkbox => checkbox.value);
            });

            // Initialize dropdowns for both HOD and Faculty sections
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize HOD Section Dropdown
                handleCheckboxSelection('hod-selectAll', 'hod-checkbox-item', 'hod-departments-btn');

                // Initialize Faculty Section Dropdown
                handleCheckboxSelection('faculty-selectAll', 'faculty-checkbox-item', 'faculty-departments-btn');
                handleCheckboxSelection('.select-all-faculty', 'department-faculty-checkbox', 'department-faculty-btn');

                // Close dropdowns when clicking outside
                window.addEventListener('click', function(event) {
                    const hodDropdown = document.getElementById('hod-departments-dropdown');
                    const facultyDropdown = document.getElementById('faculty-departments-dropdown');
                    const deptfacultyDropdown = document.getElementById('departments-faculty-dropdown');
                    if (!hodDropdown.contains(event.target) && event.target.id !== 'hod-departments-btn') {
                        hodDropdown.style.display = 'none';
                    }
                    if (!facultyDropdown.contains(event.target) && event.target.id !== 'faculty-departments-btn') {
                        facultyDropdown.style.display = 'none';
                    }
                    if (!deptfacultyDropdown.contains(event.target) && event.target.id !== 'department-faculty-btn') {
                        deptfacultyDropdown.style.display = 'none';
                    }
                });

                // Close dropdowns on 'Escape' key press
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        document.getElementById('hod-departments-dropdown').style.display = 'none';
                        document.getElementById('faculty-departments-dropdown').style.display = 'none';
                        document.getElementById('departments-faculty-dropdown').style.display = 'none';
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });

            //Add  task
            $(document).on('submit', '#addtaskform', function(e) {
                e.preventDefault(); // Prevent form submission
                var selectedFaculties = getSelectedFaculties(); // Get the selected faculty array
                var formData = new FormData(this); // Get the form data
                formData.append('selectedFaculties', JSON.stringify(selectedFaculties)); // Append the selected faculties array to formData
                $.ajax({
                    type: "POST",
                    url: "/add/addtask", // Your endpoint
                    data: formData,
                    processData: false, // Don't process data
                    contentType: false, // Don't set content type
                    success: function(response) {
                        if (response.status === 200) {
                            alertify.success("Task added successfully!");
                            $("#addtaskform")[0].reset();
                            $("#addtaskform").find("#hod-departments-btn").text("Select");
                            $("#addtaskform").find("#faculty-departments-btn").text("Select");
                            $("#addtaskform").find("#department-faculty-btn").text("Select");
                            $("#addtask").modal("hide");
                            // Reset the form fields
                            $('#assignedtask1').load(location.href + ' #assignedtask1');
                            $('#mytask1').load(location.href + ' #mytask1');
                            $('#mytask2').load(location.href + ' #mytask2');
                            $('#overdue1').load(location.href + ' #overdue1');
                            $('#completed1').load(location.href + ' #completed1');
                            $('#completed2').load(location.href + ' #completed2');
                        } else {
                            alertify.error("Something went wrong. Please try again.");
                        }
                    },
                    error: function(xhr, status, error) {
                        alertify.error("An error occurred. Please try again.");
                        console.error(error);
                    }
                });
            });

            const facultyData = @json($deptfaculty); // Assuming $deptfaculty contains all faculty data with department info
            function toggleDrop(dropdownId, buttonId) {
                const dropdown = document.getElementById(dropdownId);
                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';
                document.addEventListener('click', (event) => {
                    if (!document.getElementById(buttonId).contains(event.target)) {
                        dropdown.style.display = 'none';
                    }
                }, {
                    once: true
                });
            }

            function filterFacultyByDepartments() {
                const selectedDepartments = [...document.querySelectorAll('input[name="faculty-dname[]"]:checked')].map(cb => cb.value);
                const facultyDropdown = document.getElementById('departments-faculty-dropdown');
                facultyDropdown.innerHTML = ''; // Clear existing options
                // Add the "Select All" checkbox label
                const selectAllLabel = document.createElement('label');
                const selectAllCheckbox = document.createElement('input');
                selectAllCheckbox.type = 'checkbox';
                selectAllCheckbox.id = 'select-all-faculty'; // Unique ID for the "Select All" checkbox
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.department-faculty-checkbox');
                    checkboxes.forEach(checkbox => checkbox.checked = this.checked); // Select or deselect all
                });

                selectAllLabel.appendChild(selectAllCheckbox);
                selectAllLabel.appendChild(document.createTextNode(' All'));
                facultyDropdown.appendChild(selectAllLabel);
                facultyDropdown.appendChild(document.createElement('br'));

                // Filter and add faculty checkboxes
                const filteredFaculty = facultyData.filter(faculty => selectedDepartments.includes(faculty.dept));
                filteredFaculty.forEach(faculty => {
                    const label = document.createElement('label');
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'facultyname[]';
                    checkbox.classList.add('department-faculty-checkbox');
                    checkbox.value = faculty.id;

                    label.appendChild(checkbox);
                    label.appendChild(document.createTextNode(` ${faculty.name} ${faculty.id} (${faculty.dept})`));
                    facultyDropdown.appendChild(label);
                    facultyDropdown.appendChild(document.createElement('br'));
                });
            }

            document.querySelectorAll('input[name="faculty-dname[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', filterFacultyByDepartments);
            });

            document.getElementById('faculty-selectAll').addEventListener('change', (event) => {
                const checkboxes = document.querySelectorAll('input[name="faculty-dname[]"]');
                checkboxes.forEach(cb => cb.checked = event.target.checked);
                filterFacultyByDepartments();
            });

            function getSelectedFaculties() {
                var selectedFaculties = []; // Initialize an empty array to store selected faculty

                // Iterate over the checked checkboxes and store the faculty values in the array
                $('input[name="facultyname[]"]:checked').each(function() {
                    selectedFaculties.push($(this).val()); // Push the faculty value (ID or name) to the array
                });

                // Return the array of selected faculties
                return selectedFaculties;
            }
            // Forward Task Selection
            function toggleForwardTaskSelection() {
                const type = document.getElementById('forwardtype').value;
                document.getElementById('forward-hod-section').style.display = type === 'hod' ? 'block' : 'none';
                document.getElementById('forward-faculty-section').style.display = type === 'faculty' ? 'block' : 'none';
            }

            // Common function for toggling dropdown visibility for forward task
            function toggleForwardTaskDropdown(dropdownId, buttonId) {
                const dropdown = document.getElementById(dropdownId);
                const button = document.getElementById(buttonId);
                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';
                button.setAttribute('aria-expanded', !isVisible);
            }

            // Common function for updating button text based on selected items for forward task
            function updateForwardTaskButtonText(dropdownId, buttonId, checkboxClass) {
                const selectedCheckboxes = Array.from(document.querySelectorAll(`.${checkboxClass}:checked`))
                    .map(checkbox => checkbox.parentElement.textContent.trim());
                const button = document.getElementById(buttonId);
                if (selectedCheckboxes.length === 0) {
                    button.textContent = 'Select';
                } else if (selectedCheckboxes.length <= 2) {
                    button.textContent = selectedCheckboxes.join(', ');
                } else {
                    button.textContent = `${selectedCheckboxes.length} selected`;
                }
            }

            // Common function for handling "Select All" and individual checkboxes for forward task
            function handleForwardTaskCheckboxSelection(selectAllId, checkboxClass, buttonId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = document.querySelectorAll(`.${checkboxClass}`);

                // Update all checkboxes when "Select All" is checked/unchecked
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
                    updateForwardTaskButtonText(selectAllId, buttonId, checkboxClass);
                });

                // Update "Select All" and button text when individual checkboxes are changed
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        selectAll.checked = allChecked;
                        updateForwardTaskButtonText(selectAllId, buttonId, checkboxClass);
                    });
                });
            }

            // Initialize dropdowns for both HOD and Faculty sections for forward task
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize HOD Section Dropdown for forward task
                handleForwardTaskCheckboxSelection('forward-hod-selectAll', 'forward-hod-checkbox-item', 'forward-hod-departments-btn');

                // Initialize Faculty Section Dropdown for forward task
                handleForwardTaskCheckboxSelection('forward-faculty-selectAll', 'forward-faculty-checkbox-item', 'forward-faculty-departments-btn');

                // Close dropdowns when clicking outside for forward task
                window.addEventListener('click', function(event) {
                    const hodDropdown = document.getElementById('forward-hod-departments-dropdown');
                    const facultyDropdown = document.getElementById('forward-faculty-departments-dropdown');
                    if (!hodDropdown.contains(event.target) && event.target.id !== 'forward-hod-departments-btn') {
                        hodDropdown.style.display = 'none';
                    }
                    if (!facultyDropdown.contains(event.target) && event.target.id !== 'forward-faculty-departments-btn') {
                        facultyDropdown.style.display = 'none';
                    }
                });

                // Close dropdowns on 'Escape' key press for forward task
                window.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        document.getElementById('forward-hod-departments-dropdown').style.display = 'none';
                        document.getElementById('forward-faculty-departments-dropdown').style.display = 'none';
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });
            $(document).on('click', '.showImage', function() {
                // Get the task_id and status from the button's data attributes
                var taskId = $(this).data('task-id');
                var status = $(this).data('status');
                var deadline = $(this).data('deadline'); // Get the deadline from the data attribute

                // Set the task_id and status into hidden inputs in the form
                $('#forwardform').find('input[name="task_id"]').val(taskId);
                $('#forwardform').find('input[name="status"]').val(status);

                // Set the current date (today's date) in the forwarded date field
                var currentDate = new Date().toISOString().split('T')[0]; // Format: yyyy-mm-dd
                $('#forwarded_date').val(currentDate); // Set today's date

                // Set the forward deadline date input's max attribute to the deadline
                var deadlineDate = new Date(deadline).toISOString().split('T')[0]; // Format: yyyy-mm-dd
                $('#forwarddeadline').attr('max', deadlineDate); // Disable dates after the deadline
            });
            // Add task for forward task
            $(document).on('submit', '#forwardform', function(e) {
                e.preventDefault(); // Prevent form submission

                var selectedFaculties = getSelectedFacultiesForForwardTask(); // Get the selected faculty array

                var formData = new FormData(this); // Get the form data
                formData.append('selectedFaculties', JSON.stringify(selectedFaculties)); // Append the selected faculties array to formData

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
                            $("#forwardModal").modal("hide");
                            // Reset the form fields
                            $('#mytask1').load(location.href + ' #mytask1');
                            $('#mytask2').load(location.href + ' #mytask2');
                        } else {
                            alertify.error("Something went wrong. Please try again.");
                        }
                    }
                });
            });

            const facultyDataForForwardTask = @json($deptfaculty); // Assuming $deptfaculty contains all faculty data with department info

            function toggleForwardTaskDrop(dropdownId, buttonId) {
                const dropdown = document.getElementById(dropdownId);
                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';

                document.addEventListener('click', (event) => {
                    if (!document.getElementById(buttonId).contains(event.target)) {
                        dropdown.style.display = 'none';
                    }
                }, {
                    once: true
                });
            }

            function filterForwardTaskFacultyByDepartments() {
                const selectedDepartments = [...document.querySelectorAll('input[name="forward-faculty-dname[]"]:checked')].map(cb => cb.value);
                const facultyDropdown = document.getElementById('forward-departments-faculty-dropdown');
                facultyDropdown.innerHTML = ''; // Clear existing options

                const filteredFaculty = facultyDataForForwardTask.filter(faculty => selectedDepartments.includes(faculty.dept));
                filteredFaculty.forEach(faculty => {
                    const label = document.createElement('label');
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'facultyname[]';
                    checkbox.value = faculty.id; // Assuming 'name' is the faculty name

                    label.appendChild(checkbox);
                    label.appendChild(document.createTextNode(` ${faculty.name} ${faculty.id} (${faculty.dept})`));
                    facultyDropdown.appendChild(label);
                    facultyDropdown.appendChild(document.createElement('br'));
                });
            }

            document.querySelectorAll('input[name="forward-faculty-dname[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', filterForwardTaskFacultyByDepartments);
            });

            document.getElementById('forward-faculty-selectAll').addEventListener('change', (event) => {
                const checkboxes = document.querySelectorAll('input[name="forward-faculty-dname[]"]');
                checkboxes.forEach(cb => cb.checked = event.target.checked);
                filterForwardTaskFacultyByDepartments();
            });

            function getSelectedFacultiesForForwardTask() {
                var selectedFaculties = []; // Initialize an empty array to store selected faculty

                // Iterate over the checked checkboxes and store the faculty values in the array
                $('input[name="facultyname[]"]:checked').each(function() {
                    selectedFaculties.push($(this).val()); // Push the faculty value (ID or name) to the array
                });

                // Return the array of selected faculties
                return selectedFaculties;
            }

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
<<<<<<< HEAD
            // Function to fetch task details
function handleTaskDetails(taskId) {
    $.ajax({
        type: 'POST',
        url: `user/fetchdet/${taskId}`,
        success: function(response) {
            if (response.status === 200 && response.data.length > 0) {
                let taskDetails = '';
                let updata = response.updata;
                let deadline = updata[0].deadline.split("T")[0];
                let assigned_date = updata[0].assigned_date.split("T")[0];
=======
            function handleTaskDetails(taskId) {
                $.ajax({
                    type: 'POST',
                    url: `user/fetchdet/${taskId}`,
                    success: function(response) {
                        if (response.status === 200 && response.data.length > 0) {
                            let taskDetails = '';
                            let updata = response.updata;
                            let reasons = response.reason;
                            let deadline = updata[0].deadline.split("T")[0];
                            let assigned_date = updata[0].assigned_date.split("T")[0];
>>>>>>> 706292d3b49543a0d9e8b16c0f04bd1b34536c16

                $('#forwardfacultyDetailsHeader').html(`
                    <div class="deadline-header">
                        <strong id="assignedDate">Assigned Date:</strong> ${assigned_date}
                        &emsp;&emsp;
                        <strong id="deadlineDate">Deadline:</strong> ${deadline}
                        <br>
                    </div>
                `);

                const currentDate = new Date();

                response.data.forEach((task, index) => {
                    let assignedDate = new Date(task.assigned_date);
                    let timeDiff = currentDate - assignedDate;
                    let hourDiff = timeDiff / (1000 * 60 * 60); // Convert ms to hours
                    let isDisabled = task.status !== 0 || hourDiff >= 48; // Disable if status is 1,2,3 or 48 hours passed
                    
                    let formattedCompletedDate = task.completed_date ?
                        new Date(task.completed_date).toLocaleDateString('en-GB') :
                        'N/A';

<<<<<<< HEAD
                    taskDetails += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${task.assigned_to_name}</td>
                            <td>
                                <span class="badge ${task.status === 3 ? 'bg-success' : 'bg-secondary'}">
                                    ${task.status === 0 ? 'Assigned' :
                                    task.status === 1 ? 'Accepted' :
                                    task.status === 2 ? 'Completed' :
                                    task.status === 3 ? 'Approved' : 'Unknown'}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btnapprove" value="${task.id}" title="Approve Task" ${task.status === 3 || task.status === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-circle-check"></i>
                                </button>
                                <button type="button" class="btn btn-danger btnredo" value="${task.id}" title="Redo Task" ${task.status === 3 || task.status === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                                <button type="button" class="btn btn-primary btnreassign" data-id="${task.id}" value="${task.id}" data-status="${task.status}" title="Reassign Task" ${isDisabled ||task.status===1||task.status===2||task.status===3 ? 'disabled' : ''}>
                                    <i class="fa-solid fa-arrows-turn-to-dots"></i>
                                </button>
                                <button type="button" class="btn btn-secondary btnedeadline" data-id="${task.id}"value ="${task.id}" data-status="${task.status}" title="Extend deadline">
                                                <i class="fa-solid fa-calendar-week"></i>
                                            </button>
                            </td>
                            <td>${formattedCompletedDate}</td>
                        </tr>`;

                    // If the button should be disabled due to time constraint, update the database
                    if (hourDiff >= 48 && task.status === 0) {
                        $.ajax({
                            url: `/tasks/update-status/${task.id}`,
                            type: "POST",
                            data: {
                                _token: csrfToken,
                                status: 1, // Change status to 1 (Accepted)
                            },
                            success: function(response) {
                                console.log(`Task ${task.id} status updated to 1 due to time limit.`);
                            }
                        });
=======
                                let formattedCompletedDate = task.completed_date ?
                                    new Date(task.completed_date).toLocaleDateString('en-GB') :
                                    'N/A';
                                let hasReason = reasons.some(reason => reason.task_id === task.id);
                                let reasonExist = task.reason !== null && task.status === 0;

                                taskDetails += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${task.assigned_to_name}</td>
                                        <td>
                                            <span class="badge ${task.status === 3 ? 'bg-success' : 'bg-secondary'}">
                                                ${task.status === 0 ? 'Submitted' :
                                                task.status === 2 ? 'Completed' :
                                                task.status === 3 ? 'Approved' : 'Unknown'}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success btnapprove" value="${task.id}" title="Approve Task" ${task.status === 3 || task.status === 0 ? 'disabled' : ''}>
                                                <i class="fas fa-circle-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btnredo" value="${task.id}" title="Redo Task" ${task.status === 3 || task.status === 0 ? 'disabled' : ''}>
                                                <i class="fas fa-arrows-rotate"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btnreassign" data-id="${task.id}"value ="${task.id}" data-status="${task.status}" title="Reassign Task" ${isDisabled ? 'disabled' : ''}>
                                                <i class="fa-solid fa-arrows-turn-to-dots"></i>
                                            </button>
                                             ${reasonExist ? `
            <button type="button" class="btn btn-secondary btnreason" data-id="${task.id}" value="${task.id}" data-status="${task.status}" title="Reason">
                <i class="fas fa-light fa-message"></i>
            </button>` : ''}

                                        </td>
                                        <td>${formattedCompletedDate}</td>
                                    </tr>`;
                                // If the button should be disabled, update the database
                                if (isDisabled) {
                                    $.ajax({
                                        url: `/tasks/update-status/${task.id}`,
                                        type: "POST",
                                        data: {
                                            _token: csrfToken,
                                            status: status,
                                        },
                                        success: function(response) {
                                            console.log(`Task ${task.id} status updated to 1`);
                                        }
                                    });
                                }
                            });
                            $('#taskDetails').html(taskDetails); // Populate table
                        } else {
                            alert(response.message || 'No task details found.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching task details:', error);
                        alertify.error('An error occurred while fetching task details. Please try again.');
>>>>>>> 706292d3b49543a0d9e8b16c0f04bd1b34536c16
                    }
                });

                $('#taskDetails').html(taskDetails); // Populate table
            } else {
                alert(response.message || 'No task details found.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching task details:', error);
            alertify.error('An error occurred while fetching task details. Please try again.');
        }
    });
}

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

                $.ajax({
                    type: 'POST',
                    url: '/update-main-task',
                    data: {
                        task_id: taskId,
                        completed_date: finishDate
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#finishTask').prop('disabled', true);
                            $("#viewDetails").modal('hide');
                            $('#assignedtask1').load(location.href + ' #assignedtask1', function() {
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
                console.log(reason);
                $.ajax({
                    type: 'POST',
                    url: `/store-reason/${taskId}`, // Route for storing reason
                    data: {
                        task_id: taskId,
                        reason: reason,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            alertify.success('Reason saved successfully!');
                            $('#reasonModal').modal('hide');
                            $("#reasonForm")[0].reset();

                        } else {
                            alertify.error(response.message || 'Failed to save the reason.');
                        }
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
                const assigned_by_id = values[1]; // Second part is assigned_by_id
                // Log the values to verify
                console.log("Task ID:", id);
                console.log("Assigned By ID:", assigned_by_id);
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
                            let deadline = updata[0].deadline.split("T")[0];
                            let forwarded_date = updata[0].forwarded_date.split("T")[0]; // Assume deadline is the same for all tasks

                            // Display the deadline at the top
                            $('#forwardassignedDetailsHeader').html(`
                                <div class="deadline-header">
                                    <strong>Assigned Date:</strong> ${forwarded_date} &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                                    <strong>Deadline:</strong> ${deadline}
                                    <br>
                                </div>
                            `);
                            const current_Date = new Date().toISOString().split("T")[0];

                            response.data.forEach((task, index) => {
                                const isDeadline_Crossed = task.completed_date === null && deadline < current_Date;
                                let formatted_CompletedDate = task.completed_date ?
                                    new Date(task.completed_date).toLocaleDateString('en-GB') :
                                    'N/A';
                                forwardfacultyDetails += `
                                    <tr class="${isDeadline_Crossed ? 'table-danger' : ''}">
                                        <td>${index + 1}</td>
                                        <td>${task.assigned_to_name}</td>
                                        <td>
                                            <span class="badge ${task.status === 3 ? 'bg-success' : 'bg-secondary'}">
                                            ${task.status === 0 ? 'Submitted' :
                                            task.status === 2 ? 'Completed' :
                                            task.status === 3 ? 'Approved' : 'Unknown'}
                                            </span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-success btnforwardapprove" value="${task.sid}" title="Approve Task" ${task.status === 0 || task.status === 3 ? 'disabled' : ''}>
                                            <i class="fas fa-circle-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btnforwardredo" value="${task.sid}" title="Redo Task" ${task.status === 0 || task.status === 3 ? 'disabled' : ''}>
                                            <i class="fas fa-arrows-rotate"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btnforwardreassign" value="${task.sid}" title="Reassign Task" ${task.status === 1 ? 'disabled' : ''}>
                                                <i class="fa-solid fa-arrows-turn-to-dots"></i>
                                            </button>
                                        </td>
                                        <td>${formatted_CompletedDate}</td>
                                    </tr>`;
                            });

                            $('#forwardfacultyDetails').html(forwardfacultyDetails); // Populate the modal with task details
                            forwardcheckTaskStatus(taskId); // Check if all tasks are completed
                        } else {
                            alertify.error(response.message || 'No task details found.');
                        }
                    }
                });
            }

            // Function to check if all tasks are completed
            function forwardcheckTaskStatus(taskId) {
                $.ajax({
                    type: 'POST',
                    url: '/check-forwardtask-status',
                    data: {
                        task_id: taskId
                    },
                    success: function(response) {
                        if (response.allCompleted) {
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
                                    row.find('td:nth-child(3) span')
                                        .removeClass('bg-secondary')
                                        .addClass('bg-success')
                                        .text('Approved');
                                    button.prop('disabled', true);
                                    row.find('.btnredo').prop('disabled', true);
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
                            $('#mytask2').load(location.href + ' #mytask2', function() {
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
                            $('#fredoreasonModal').modal('hide');
                            $("#fredoreasonForm")[0].reset();
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
                            let assigned_date = updata[0].assigned_date.split("T")[0]; // Assume deadline is the same for all tasks

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
                                    new Date(task.completed_date).toISOString().split('T')[0].split('-').reverse().join('/') :
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
                $('#reassignModal').find('#reassigntaskId').val(taskId);
                $('#reassignModal').modal('show');
            });
            $(document).on('click', '#reassignsubmit', function(e) {
                e.preventDefault();
                var taskId = $('#reassigntaskId').val();
                var facultyId = $('#faculty_id').val();
                var name = $('#name').val();
                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'POST',
                    url: `/store-reassign`,
                    data: {
                        task_id: taskId,
                        faculty_id: facultyId,
                        name: name,
                        _token: token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            alertify.success('Task reassigned successfully!');
                            $("#reassignForm")[0].reset();
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
                $('#reassignforwardModal').find('#reassignforwardtaskId').val(taskId);
                $('#reassignforwardModal').modal('show');
            });
            $(document).on('click', '#reassignforwardsubmit', function(e) {
                e.preventDefault();

                var taskId = $('#reassignforwardtaskId').val();
                var facultyId = $('#ffaculty_id').val();
                var name = $('#fname').val();
                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'POST',
                    url: `/store-reassignforward`,
                    data: {
                        task_id: taskId,
                        ffaculty_id: facultyId,
                        fname: name,
                        _token: token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            alertify.success('Task reassigned successfully!');
                            $("#reassignforwardForm")[0].reset();
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
<<<<<<< HEAD
            // $(document).on('click', '.btnedeadline', function(e) {
            //     e.preventDefault();
            //     var taskId = $(this).val();
            //     $('#extendDeadlineModal').find('#extenddeadlinetaskId').val(taskId);
            //     $('#extendDeadlineModal').modal('show');
            // });
            $(document).on('click', '.btnedeadline', function(e) {
e.preventDefault();
        let taskId = $(this).data("id");
        console.log(taskId);
        $.ajax({
            url:`/task/${taskId}`,
            type: "GET",
            success: function(response) {
                $("#oldDeadline").val(response.deadline);
                $("#reason").val(response.feedback);
                $("#extenddeadlinetaskId").val(taskId);
                $("#extendDeadlineModal").modal("show");
            }
        });
    });
$(document).on("submit", "#extendDeadlineForm", function (e) {
    e.preventDefault(); // Prevent page refresh
    let taskId = $("#extenddeadlinetaskId").val();
    let oldDeadline = $("#oldDeadline").val();
    let newDeadline = $("#newDeadline").val();
    let reason = $("#reason").val();
    // Converts back to "YYYY-MM-DD"


    // Determine values for feedback and deadline
    let feedback = reason;
let status = 1; 
let deadline = oldDeadline;
let oldDate = new Date(oldDeadline);
let newDate = new Date(newDeadline);

// Extract only the year, month, and day
let oldDateOnly = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
let newDateOnly = new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate());

// Compare only the date part (ignoring time)
if (newDateOnly > oldDateOnly) {
    status = 4;
    deadline = newDeadline;
}



    $.ajax({
        url: "/update-deadline",
        type: "POST",
        data: {
            task_id: taskId,
            deadline: deadline,
            feedback: feedback,
            status: status,
            _token: $('meta[name="csrf-token"]').attr("content"), // Include CSRF token
        },
        success: function (response) {
            if (response.success) {
                alertify.success("Deadline updated successfully!");
                $("#extendDeadlineModal").modal("hide"); // Close modal
            } else {
                alertify.error("Failed to update deadline!");
            }
        },
        error: function (xhr) {
            alert("Error: " + xhr.responseJSON.error);
        },
=======
            //Reason display for assigned tab
            $(document).on('click', '.btnreason', function () {
                let taskId = $(this).data('id'); // Get task ID from button

                $.ajax({
                    type: 'POST',
                    url: `/user/fetchdet/${taskId}`, // Fetch reason from backend
                    success: function (response) {
                        if (response.status === 200 && response.reason.length > 0) {
                            let reasonText = response.reason[0].reason; // Get first reason

                            $('#reasonDisplayText').text(reasonText); // Display reason in modal
                            $('#forwardtaskId').val(taskId); // Set hidden input value

                            $('#reasonDisplayModal').modal('show'); // Open modal
                        } else {
                            alertify.error('No reason found for this task.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching reason:', error);
                        alertify.error('An error occurred while fetching the reason.');
                    }
                });
            });

            //Reason for mytask tab
            $(document).on('click', '.btnmyreason', function() {
    var taskId = $(this).val();  // Get task_id from button value

    // Make an AJAX request to fetch the reason
    $.ajax({
        url: '/fetch-reason/' + taskId,  // The route to fetch reason
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
>>>>>>> 706292d3b49543a0d9e8b16c0f04bd1b34536c16
    });
});


<<<<<<< HEAD
=======

>>>>>>> 706292d3b49543a0d9e8b16c0f04bd1b34536c16
        </script>
</body>

</html>
