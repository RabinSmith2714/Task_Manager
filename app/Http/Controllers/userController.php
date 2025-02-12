<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Maintask;
use App\Models\Department;
use App\Models\Mainbranch;
use App\Models\Subtask;
use App\Models\Specialrole;
use App\Models\Point;
use Carbon\Carbon;
use App\Mail\TaskAddedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForwardMail;
use App\Models\role;

class userController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required',
            'password' => 'required',
        ]);

        // Find the user in the Faculty model
        $faculty = Faculty::where('id', $request->faculty_id)->first();
        $specialrole = specialrole::where('id', $request->faculty_id)->first();
        if ($faculty && $faculty->pass === $request->password) {
            session([
                'faculty_id' => $faculty->id,
                'role' => $faculty->role,
                'faculty_name' => $faculty->name,
                'department' => $faculty->dept,
                'specialrole' => $specialrole->dept,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful!',
                'redirect_url' => route('index'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials!',
            ], 401);
        }
    }
    public function index()
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $facultyId = session('faculty_id');
        $role = session('role');
        $facultyName = session('faculty_name');
        $department = session('department');
        $specialrole = session('specialrole');
        $dept = Department::all();
        $deptfaculty = Faculty::where('status', '1')->where('id', '!=', $facultyId)->get();
        $assigned_task = Maintask::select('task_id', 'title', 'description', 'deadline')->where('status', 0)->where('assigned_by_id', $facultyId)->groupBy('task_id', 'title', 'description', 'deadline')->get();
        $dashboard_assigned_task = Maintask::where('status', 0)
            ->where('assigned_by_id', $facultyId)
            ->count();


        $management =  Specialrole::select('Specialrole.Role', 'Specialrole.id', 'faculty.name')
            ->where('Specialrole.type', 'Management')
            ->where('specialrole.Role', '!=' ,'Principal',$facultyId)
            ->join('faculty', 'faculty.id', '=', 'specialrole.id')
            ->distinct()
            ->get();
        $centerofheads = Specialrole::select('Specialrole.Role', 'Specialrole.id', 'faculty.name')
            ->where('Specialrole.type', 'center of heads')
            ->where('Specialrole.status', '2')
            ->join('faculty', 'faculty.id', '=', 'specialrole.id')
            ->distinct()
            ->get();
        //Special Role
        $specialStatus = Specialrole::where('id', $facultyId)->value('Status');
        if (is_null($specialStatus)) {
            $faculty = Faculty::where('id', $facultyId)
                ->where('role', 'faculty')
                ->first();
            $specialStatus = $faculty ? 4 : 3;
        }
        $Role = Specialrole::where('id', $facultyId)->value('Role');
        $Type = Specialrole::where('id', $facultyId)->value('type');
        if (is_null($Role)) {
            $faculty = Faculty::where('id', $facultyId)
                ->where('role', 'faculty')
                ->first();
            $Role = $faculty ? 'faculty' : 'head of department';
        }
        $studentaffiars = Specialrole::where('type', 'center of heads')
            ->where('status', '2')->distinct()->get();

        //My tasks queries
        $my_det1 = Mainbranch::where('assigned_to_id', $facultyId)
            ->whereIn('status', ['0', '1', '2'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask')
                    ->whereColumn('Subtask.task_id', 'Mainbranch.task_id')
                    ->whereColumn('Subtask.assigned_by_id', 'Mainbranch.assigned_to_id');
            })
            ->select('task_id')
            ->get();

        $my_det2 = Subtask::where('assigned_to_id', $facultyId)
            ->whereIn('status', ['0', '1', '2'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask as sub')
                    ->whereColumn('sub.task_id', 'Subtask.task_id')
                    ->whereColumn('sub.assigned_by_id', 'Subtask.assigned_to_id')
                    ->whereColumn('sub.sid', '<>', 'Subtask.sid'); // Ensures no self-check
            })
            ->select('task_id')
            ->get();

        // Query sub tasks and join with main tasks to fetch title and description
        $subTasks = Subtask::whereIn('subtask.task_id', $my_det2)->where('assigned_to_id', $facultyId)
            ->join('maintask', 'subtask.task_id', '=', 'maintask.task_id')
            // Join Maintask table
            ->select(
                'maintask.title',
                'maintask.description',
                'subtask.assigned_by_name',
                'subtask.deadline',
                'subtask.task_id',
                'subtask.status',
                'subtask.reason'
            )->get();

        // Fetch main tasks
        $mainTasks = Maintask::whereIn('Maintask.task_id', $my_det1)->join('Mainbranch', 'Mainbranch.task_id', '=', 'Maintask.task_id')
            ->where('Mainbranch.assigned_to_id', $facultyId)
            ->select('Maintask.title', 'Maintask.description', 'Maintask.assigned_by_name', 'Mainbranch.deadline', 'Maintask.task_id', 'Mainbranch.status', 'Mainbranch.reason')
            ->get();
        // Combine results
        $combinedTasks = $mainTasks->concat($subTasks);
        $dashboardcombinedTasks = $mainTasks->concat($subTasks)->count();

        //overdue tasks
        $overdue_MainTasks = Maintask::whereIn('task_id', $my_det1) // Match tasks assigned to the faculty
            ->where('maintask.deadline', '<', $currentDate) // Deadline has passed
            ->select('maintask.task_id', 'maintask.title', 'maintask.description', 'maintask.assigned_by_name', 'maintask.deadline') // Select required columns
            ->get();
        $overdue_subTasks = Subtask::whereIn('subtask.task_id', $my_det2)->where('assigned_to_id', $facultyId)
            ->join('maintask', 'subtask.task_id', '=', 'maintask.task_id')
            ->where('subtask.deadline', '<', $currentDate) // Join Maintask table
            ->select(
                'maintask.title',
                'maintask.description',
                'subtask.assigned_by_name',
                'subtask.deadline',
                'subtask.task_id',
                'subtask.status'
            )->get();
        $overdueTasks = $overdue_MainTasks->concat($overdue_subTasks);
        $dashboard_overdueTasks = $overdue_MainTasks->concat($overdue_subTasks)->count();


        $forwarded_task_mainbranch = Mainbranch::where('Mainbranch.assigned_to_id', $facultyId)->select(
            'Maintask.task_id',
            'Maintask.title',
            'Maintask.description',
            'Maintask.assigned_by_id',
            'Maintask.assigned_by_name',
            'Mainbranch.deadline'
        )
            ->whereExists(function ($query) use ($facultyId) {
                $query->select(DB::raw(1))
                    ->from('Subtask')
                    ->whereColumn('Subtask.task_id', 'Mainbranch.task_id')
                    ->where('Subtask.assigned_by_id', $facultyId);
            })
            ->join('Maintask', 'Maintask.task_id', '=', 'Mainbranch.task_id')
            ->join('Subtask', 'Subtask.task_id', '=', 'Mainbranch.task_id')
            ->where('Mainbranch.assigned_to_id', $facultyId)
            ->whereIn('Mainbranch.status', ['0', '1', '2'])

            ->groupBy(
                'Maintask.task_id',
                'Maintask.title',
                'Maintask.description',
                'Maintask.assigned_by_id',
                'Maintask.assigned_by_name',
                'Mainbranch.deadline'
            )
            ->get();
        $forwarded_task_subtask = Subtask::where('Subtask.assigned_to_id', $facultyId)
            ->whereExists(function ($query) use ($facultyId) {
                $query->select(DB::raw(1))
                    ->from('Subtask as Sub')
                    ->whereColumn('Sub.task_id', 'Subtask.task_id')
                    ->where('Sub.assigned_by_id', $facultyId)
                    ->whereColumn('Sub.sid', '<>', 'Subtask.sid');
            })
            ->join('Maintask', 'Maintask.task_id', '=', 'Subtask.task_id')
            ->where('Subtask.assigned_to_id', $facultyId)
            ->whereIn('Subtask.status', ['0', '1', '2'])
            ->select(
                'Subtask.task_id',
                'Maintask.title',
                'Maintask.description',
                'Subtask.assigned_by_id',
                'Subtask.assigned_by_name',
                'Subtask.deadline'
            )
            ->groupBy(
                'Subtask.task_id',
                'Maintask.title',
                'Maintask.description',
                'Subtask.assigned_by_id',
                'Subtask.assigned_by_name',
                'Subtask.deadline'
            )
            ->get();
        // Combine the results from both queries
        $forwarded_task = $forwarded_task_mainbranch->concat($forwarded_task_subtask);
        // $forwarded_task =Subtask::where('Subtask.assigned_by_id', $facultyId)
        //     ->whereIn('Subtask.status', ['0','2','3'])
        //     ->whereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('Subtask as sub')
        //             ->whereColumn('sub.task_id', 'Subtask.task_id')
        //             ->whereColumn('sub.assigned_to_id', 'Subtask.assigned_by_id')
        //             ->where('sub.status','3')
        //             ->whereColumn('sub.sid', '<>', 'Subtask.sid');

        //     })
        //     ->orWhereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('Mainbranch')
        //             ->whereColumn('Mainbranch.task_id', 'Subtask.task_id')
        //             ->whereColumn('Mainbranch.assigned_to_id', 'Subtask.assigned_by_id')
        //             ->where('Mainbranch.status','3');
        //     })
        //     ->join('Maintask', 'Subtask.task_id', '=', 'Maintask.task_id')
        //     ->whereIn('Subtask.status', ['0','2'])
        //     ->where('Subtask.assigned_to_id', $facultyId)
        //     ->select(
        //         'Subtask.task_id',
        //         'Maintask.title',
        //         'Maintask.description',
        //         'Subtask.assigned_by_id',
        //         'Subtask.assigned_by_name',
        //         'Subtask.deadline'
        //     )
        //     ->groupBy(
        //         'Subtask.task_id',
        //         'Maintask.title',
        //         'Maintask.description',
        //         'Subtask.assigned_by_id',
        //         'Subtask.assigned_by_name',
        //         'Subtask.deadline'
        //     )
        //     ->get();



        //finish tasks
        $tasks = Mainbranch::select('task_id')->get();
        // Render the index view with session and task data

        // Completed my task
        $my_det3 = Mainbranch::where('assigned_to_id', $facultyId)
            ->where('status', '3')

            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask')
                    ->whereColumn('Subtask.task_id', 'Mainbranch.task_id')
                    ->whereColumn('Subtask.assigned_by_id', 'Mainbranch.assigned_to_id');
            })
            ->select('task_id')
            ->get();

        $my_det4 = Subtask::where('assigned_to_id', $facultyId)
            ->where('status', '3')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask as sub')
                    ->whereColumn('sub.task_id', 'Subtask.task_id')
                    ->whereColumn('sub.assigned_by_id', 'Subtask.assigned_to_id')
                    ->whereColumn('sub.sid', '<>', 'Subtask.sid'); // Ensures no self-check
            })
            ->select('task_id')
            ->get();
        // Fetch completed main tasks with completed_date
        $completedMainTasks = Maintask::whereIn('maintask.task_id', $my_det3)->where('assigned_to_id', $facultyId)->join('mainbranch', 'maintask.task_id', '=', 'mainbranch.task_id')
            ->select('maintask.title', 'maintask.description', 'maintask.assigned_by_name', 'mainbranch.completed_date', 'mainbranch.task_id')
            ->get();
        // Fetch completed subtasks with completed_date
        $completedSubtasks = Subtask::whereIn('subtask.task_id', $my_det4)->where('assigned_to_id', $facultyId)
            ->join('maintask', 'subtask.task_id', '=', 'maintask.task_id') // Join Maintask table
            ->select(
                'maintask.title',
                'maintask.description',
                'subtask.assigned_by_name',
                'subtask.completed_date',
                'subtask.task_id'

            )->get();
        // Merge the results
        $completed_my_task = $completedMainTasks->concat($completedSubtasks);

        //completed assigned task
        $completedassignMaintask = Maintask::where('assigned_by_id', $facultyId)
            ->where('status', '2')
            ->select('task_id', 'title', 'description', 'deadline')
            ->get();
        $completedassignSubtask = Subtask::where('Subtask.assigned_by_id', $facultyId)
            ->where('Subtask.status', '3')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask as sub')
                    ->whereColumn('sub.task_id', 'Subtask.task_id')
                    ->whereColumn('sub.assigned_to_id', 'Subtask.assigned_by_id')
                    ->where('sub.status', '3')
                    ->whereColumn('sub.sid', '<>', 'Subtask.sid');
            })
            ->orWhereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Mainbranch')
                    ->whereColumn('Mainbranch.task_id', 'Subtask.task_id')
                    ->whereColumn('Mainbranch.assigned_to_id', 'Subtask.assigned_by_id')
                    ->where('Mainbranch.status', '3');
            })
            ->join('Maintask', 'Subtask.task_id', '=', 'Maintask.task_id')
            ->where('Subtask.status', '3')
            ->where('Subtask.assigned_by_id', $facultyId)
            ->select(
                'Subtask.task_id',
                'Maintask.title',
                'Maintask.description',
                'Subtask.assigned_by_id',
                'Subtask.assigned_by_name',
                'Subtask.deadline'
            )
            ->groupBy(
                'Subtask.task_id',
                'Maintask.title',
                'Maintask.description',
                'Subtask.assigned_by_id',
                'Subtask.assigned_by_name',
                'Subtask.deadline'
            )
            ->get();

        $completed_assigntask = $completedassignMaintask->concat($completedassignSubtask);
        $dashcompleted_my_task = $completedMainTasks->concat($completedSubtasks)->count();
        $dashcompleted_assigntask = $completedassignMaintask->concat($completedassignSubtask)->count();
        $dashboard_completed_tasks = $dashcompleted_my_task + $dashcompleted_assigntask;
        //Dashboard tasks near deadline
        $statuses = [0, 1];
        $today = now();
        $tomorrow = now()->addDay();
        $disclaimertasks = DB::table('maintask')
            ->join('mainbranch', 'maintask.task_id', '=', 'mainbranch.task_id')  // Updated join condition using task_id
            // Check if assigned_to_id matches facultyId
            ->join('subtask', function ($join) {
                $join->on('subtask.task_id', '=', 'mainbranch.task_id')
                    ->orOn('subtask.task_id', '=', 'mainbranch.task_id');
            })
            ->whereIn('maintask.status', $statuses)
            ->whereBetween('maintask.deadline', [$today->startOfDay(), $tomorrow->endOfDay()])
            ->select('maintask.title', 'maintask.deadline', 'maintask.description')
            ->orderBy('maintask.deadline', 'asc')
            ->distinct()
            ->get();
        $departmentfaculties = Faculty::where('dept', $department)
            ->where('role', 'Faculty')
            ->where('status', '1')
            ->get();
        $coordinators = Specialrole::where('Specialrole.Role', $Role)
            ->where('Specialrole.type', $Type)
            ->where('Specialrole.status', 4)
            ->join('faculty', 'faculty.id', '=', 'specialrole.id')
            ->select('faculty.id', 'faculty.name', 'faculty.dept')
            ->get();



        return view('index', compact(
            'facultyId',
            'facultyName',
            'role',
            'department',
            'dept',
            'deptfaculty',
            'assigned_task',
            'my_det1',
            'my_det2',
            'combinedTasks',
            'forwarded_task',
            'tasks',
            'mainTasks',
            'completed_my_task',
            'completed_assigntask',
            'overdueTasks',
            'currentDate',
            'disclaimertasks',
            'dashboard_assigned_task',
            'dashboardcombinedTasks',
            'dashboard_completed_tasks',
            'dashboard_overdueTasks',
            'specialStatus',
            'management',
            'centerofheads',
            'Role',
            'departmentfaculties',
            'studentaffiars',
            'Type',
            'coordinators'
        ));
    }

    //Add task
    public function addtask(Request $request)
    {
        $request->validate([
            'assigned_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:assigned_date',
        ]);

        $specialStatus = $request->input('specialStatus');
        $Role = $request->input('Role');
        $workType = $request->input('workType');

        // Create a single Maintask record
        $maintask = Maintask::create([
            'assigned_by_id' => $request->faculty_id,
            'assigned_by_name' => $request->faculty_name,
            'title' => $request->title,
            'description' => $request->description,
            'assigned_date' => $request->assigned_date,
            'complexity_level' => $request->level,
            'deadline' => $request->deadline,
        ]);

        // Principal Condition
        if ($specialStatus == 0 && $Role == "Principal") {
            if ($workType == "Management") {
                $fac1 = $request->input('researchType');
                $facultyData = Faculty::where('id', '=', $fac1)->value('name');
                Mainbranch::create([
                    'task_id' => $maintask->task_id,
                    'assigned_to_id' => $fac1,
                    'assigned_to_name' => $facultyData,
                    'deadline' => $request->deadline,
                    'status' => '0',
                ]);
                return response()->json([
                    'status' => 200,
                ]);
            } else if ($workType == "center of head") {
                $fac1 = $request->input('teachingSubject');
                $facultyData = Faculty::where('id', '=', $fac1)->value('name');
                Mainbranch::create([
                    'task_id' => $maintask->task_id,
                    'assigned_to_id' => $fac1,
                    'assigned_to_name' => $facultyData,
                    'deadline' => $request->deadline,
                    'status' => '0',
                ]);
                return response()->json([
                    'status' => 200,
                ]);
            } else if ($workType == "hod") {
                $selectedDept = $request->input('selectedDepartments');
                $selectedDepartments = explode(',', $selectedDept);

                if (is_array($selectedDepartments) && count($selectedDepartments) > 0) {
                    $hods = Faculty::whereIn('dept', $selectedDepartments)
                        ->where('role', 'hod')
                        ->where('status', '1')
                        ->select('id', 'name')
                        ->get();

                    foreach ($hods as $hod) {
                        Mainbranch::create([
                            'task_id' => $maintask->task_id,
                            'assigned_to_id' => $hod->id,
                            'assigned_to_name' => $hod->name,
                            'deadline' => $request->deadline,
                            'status' => '0',
                        ]);
                    }
                    return response()->json([
                        'status' => 200,
                    ]);
                }
            } else if ($workType == "faculty") {
                $selectedfac = $request->input('selectedFaculties');
                $selectedFaculty = explode(',', $selectedfac);

                if (is_array($selectedFaculty) && count($selectedFaculty) > 0) {
                    $faculties = Faculty::whereIn('id', $selectedFaculty)
                        ->where('role', 'faculty')
                        ->select('id', 'name')
                        ->get();

                    foreach ($faculties as $faculty) {
                        Mainbranch::create([
                            'task_id' => $maintask->task_id,
                            'assigned_to_id' => $faculty->id,
                            'assigned_to_name' => $faculty->name,
                            'deadline' => $request->deadline,
                            'status' => '0',
                        ]);
                    }
                    return response()->json([
                        'status' => 200,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                ]);
            }
        } else if ($specialStatus == 3 && $Role == "head of department") {
            $selectedfac = $request->input('selecteddeptFaculties');
            $selectedFaculty = explode(',', $selectedfac);

            if (is_array($selectedFaculty) && count($selectedFaculty) > 0) {
                $faculties = Faculty::whereIn('id', $selectedFaculty)
                    ->where('role', 'faculty')
                    ->select('id', 'name')
                    ->get();

                foreach ($faculties as $faculty) {
                    Mainbranch::create([
                        'task_id' => $maintask->task_id,
                        'assigned_to_id' => $faculty->id,
                        'assigned_to_name' => $faculty->name,
                        'deadline' => $request->deadline,
                        'status' => '0',
                    ]);
                }
                return response()->json([
                    'status' => 200,
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                ]);
            }
        } else if ($specialStatus == 1 && $Role == "student affiars") {
            $studentAff = $request->input('studentaffiars');
            $facultyData = Faculty::where('id', '=', $studentAff)->value('name');
            Mainbranch::create([
                'task_id' => $maintask->task_id,
                'assigned_to_id' => $studentAff,
                'assigned_to_name' => $facultyData,
                'deadline' => $request->deadline,
                'status' => '0',
            ]);
            return response()->json([
                'status' => 200,
            ]);
        } else {
            return response()->json([
                'status' => 400,
            ]);
        }
    }





    public function forwardtask(Request $request)
    {
        $type = $request->input('type');
        $Role = $request->input('role');
        
        if ($type == 'center of heads') {
            $coordinators = $request->input('selectedcoordinators');
            $coordinatorfac = explode(',', $coordinators);
            if (is_array($coordinatorfac) && count($coordinatorfac) > 0) {
                $data = Faculty::whereIn('id', $coordinatorfac)
                    ->where('role', 'Faculty')
                    ->where('status', '1')
                    ->select('id', 'name')
                    ->get();
            } else {
                return response()->json([
                    'status' => 400,
                ]);
            }
        } else if ($Role == 'head of department') {
            $selectedfac = $request->input('selectedforwarddeptFaculties');
            $selectedFaculty = explode(',', $selectedfac);

            if (is_array($selectedFaculty) && count($selectedFaculty) > 0) {
                $data = Faculty::whereIn('id', $selectedFaculty)
                    ->where('role', 'faculty')
                    ->select('id', 'name')
                    ->where('status', '1')
                    ->get();
            } else {
                return response()->json([
                    'status' => 400,
                ]);
            }
        } else if($Role == 'student affiars'){
            $coh = $request->input('fstudentaffiars');
            $data = Faculty::where('id', $coh)
                    ->where('role', 'faculty')
                    ->select('id', 'name')
                    ->where('status', '1')
                    ->get();
        } else {
            return response()->json([
                'status' => 500,
            ]);
        }

        foreach ($data as $facdata) {
            Subtask::create([
                'task_id' => $request->input('task_id'),
                'assigned_by_id' => $request->input('faculty_id'),
                'assigned_by_name' => $request->input('faculty_name'),
                'assigned_to_id' => $facdata->id,
                'assigned_to_name' => $facdata->name,
                'deadline' => $request->input('forwarddeadline'),
                'status' => '0',
                'forwarded_date' => $request->input('forwarded_date'),
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Task Forwarded successfully',
        ]);
    }








    public function approve($id)
    {
        $task = Mainbranch::findOrFail($id);
        $task->status = '3'; // Approved status
        $task->save();
    }

    public function fetchdet($id)
    {
        $tasks = Mainbranch::where('task_id', $id)->get(); // Get the tasks related to the given id
        $uptasks = Maintask::where('task_id', $id)->get();
        $reas = Mainbranch::where('task_id', $id)
            ->where('status', '0')
            ->whereNotNull('reason')
            ->get();


        return response()->json([
            'status' => 200,
            'data' => $tasks,
            'updata' => $uptasks,
            'reason' => $reas
        ]);
    }



    public function checkTaskStatus(Request $request)
    {
        $taskId = $request->input('task_id');

        // Check if all tasks with the same task_id have status = 9
        $allCompleted = DB::table('mainbranch')
            ->where('task_id', $taskId)
            ->where('status', '!=', 3)
            ->doesntExist();

        return response()->json(['allCompleted' => $allCompleted]);
    }
    public function updateMainTask(Request $request)
    {
        $taskId = $request->input('task_id');

        try {
            // Update the maintask table's status to 9 for the given task_id
            DB::table('maintask') // Replace with your maintask table name
                ->where('task_id', $taskId)
                ->update(
                    [
                        'status' => 2,
                        'completed_date' => $request->completed_date
                    ]
                );

            return response()->json(['success' => true, 'message' => 'Task completed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function storeReason(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            // Update the reason in the mainbranch table for the given ID
            $reasonEntry = Mainbranch::where('id', $id)->update([
                'reason' => $request->reason,
                'status' => 0,
                'completed_date' => null
            ]);

            if ($reasonEntry) {
                return response()->json(['status' => 200, 'message' => 'Reason saved successfully!', 'data' => $reasonEntry]);
            } else {
                return response()->json(['status' => 404, 'message' => 'No record found for the given ID.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to save reason: ' . $e->getMessage()]);
        }
    }

    public function accepted(Request $request, $id)
    {
        $facultyId = session('faculty_id');
        $ctask1 = Subtask::where('task_id', $id)->where('assigned_to_id', $facultyId)->update([
            'status' => 1,

        ]);
        if ($ctask1 === 0) {
            $ctask1 = Mainbranch::where('task_id', $id)->update([
                'status' => 1,

            ]);
        }
    }
    public function completed(Request $request, $id)
    {
        $facultyId = session('faculty_id');
        $ctask1 = Subtask::where('task_id', $id)->where('assigned_to_id', $facultyId)->update([
            'status' => 2,
            'completed_date' => $request->completed_date,
        ]);
        if ($ctask1 === 0) {
            $ctask1 = Mainbranch::where('task_id', $id)->update([
                'status' => 2,
                'completed_date' => $request->completed_date,
            ]);
        }
    }

    //forwrede approve
    public function forwardapprove($sid)
    {
        $tasks = Subtask::findOrFail($sid);
        $tasks->status = '3'; // Approved status
        $tasks->save();
    }

    public function forwardfetchdet($id)
    {
        $facultyId = session('faculty_id'); // Retrieve the faculty_id from session
        $uptasks = Subtask::where('task_id', $id)->where('assigned_by_id', $facultyId)->get();
        // Query to get tasks where task_id matches and assigned_to_id is the current faculty ID
        $tasks = Subtask::where('task_id', $id)
            ->where('assigned_by_id', $facultyId)
            ->get();
        return response()->json([
            'status' => 200,
            'data' => $tasks,
            'updata' => $uptasks

        ]);
    }


    public function forwardcheckTaskStatus(Request $request)
    {
        $taskId = $request->input('task_id');
        $assigned_by_id = session('faculty_id');
        // Check if all tasks with the same task_id have status = 9
        $allCompleted = DB::table('subtask')
            ->where('task_id', $taskId)
            ->where('assigned_by_id', $assigned_by_id)
            ->where('status', '!=', 3)
            ->doesntExist();

        return response()->json(['allCompleted' => $allCompleted]);
    }
    public function forwardupdateMainTask(Request $request)
    {
        $taskId = $request->input('task_id');
        $assigned_to_id = session('faculty_id');

        try {
            $updateMaintask = DB::table('subtask')
                ->where('task_id', $taskId)
                ->where('assigned_to_id', $assigned_to_id)
                ->update([
                    'status' => 2,
                    'completed_date' => $request->completed_date
                ]);

            if ($updateMaintask === 0) { // If no rows were affected in the first query
                // Attempt to update the mainbranch table
                $updateMainbranch = DB::table('mainbranch')
                    ->where('task_id', $taskId)
                    ->where('assigned_to_id', $assigned_to_id)
                    ->update([
                        'status' => 2,
                        'completed_date' => $request->completed_date
                    ]);

                if ($updateMainbranch != 0) {
                    return response()->json(['success' => true, 'message' => 'Task finished successfully']);
                }
            }

            return response()->json(['success' => true, 'message' => 'Task finished successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function forwardstoreReason(Request $request, $sid)
    {
        // Validate the input
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            // Update the reason in the mainbranch table for the given ID
            $reasonEntry = Subtask::where('sid', $sid)->update([
                'reason' => $request->reason,
                'status' => 0,
            ]);

            if ($reasonEntry) {
                return response()->json(['status' => 200, 'message' => 'Reason saved successfully!', 'data' => $reasonEntry]);
            } else {
                return response()->json(['status' => 404, 'message' => 'No record found for the given ID.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to save reason: ' . $e->getMessage()]);
        }
    }


    //completed fectdet
    public function cassignedfetchdet($id)
    {
        $uptasks = Maintask::where('task_id', $id)->get();
        $facultyId = session('faculty_id');
        $task = Subtask::where('task_id', $id)->where('assigned_by_id', $facultyId)->get();

        if ($task->isEmpty()) {
            $task = Mainbranch::where('task_id', $id)->get();
        }

        return response()->json([
            'status' => 200,
            'data' => $task,
            'updata' => $uptasks
        ]);
    }
    public function overduecomplete(Request $request, $id)
    {
        $facultyId = session('faculty_id');
        $ctask1 = Subtask::where('task_id', $id)->where('assigned_to_id', $facultyId)->update([
            'status' => 2,
            'completed_date' => $request->completed_date,
        ]);
        if ($ctask1 === 0) {
            $ctask1 = Mainbranch::where('task_id', $id)->update([
                'status' => 2,
                'completed_date' => $request->completed_date,
            ]);
        }
    }
    // History Tab



    public function getChartData(Request $request)
    {
        // Validate request input
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Fetch the required data

        // Assigned Task
        $facultyId = session('faculty_id'); // Retrieve faculty ID from session

        $assigned = Maintask::where('assigned_by_id', $facultyId) // Ensure the column name exists
            ->where('status', 0)
            ->whereBetween('assigned_date', [$startDate, $endDate]) // Use the correct column name without alias
            ->count();



        // Forwarded Task
        $forwarded = Subtask::join('Maintask', 'Subtask.task_id', '=', 'Maintask.task_id')
            ->whereIn('Subtask.status', ['0', '2'])
            ->where('Subtask.assigned_by_id', $facultyId)
            ->whereBetween('forwarded_date', [$startDate, $endDate]) // Use the correct column name without alias
            ->count();


        // My Task
        $mytask = DB::table('Mainbranch')
            ->where('assigned_to_id', $facultyId)
            ->where('status', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask')
                    ->whereColumn('Subtask.task_id', 'Mainbranch.task_id')
                    ->whereColumn('Subtask.assigned_by_id', 'Mainbranch.assigned_to_id');
            })
            ->select(DB::raw(1)) // Selecting a constant to unify the structure
            ->unionAll(
                DB::table('Subtask')
                    ->where('assigned_to_id', $facultyId)
                    ->whereBetween('forwarded_date', [$startDate, $endDate])
                    ->where('status', 0)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('Subtask as sub')
                            ->whereColumn('sub.task_id', 'Subtask.task_id')
                            ->whereColumn('sub.assigned_by_id', 'Subtask.assigned_to_id')
                            ->whereColumn('sub.sid', '<>', 'Subtask.sid'); // Ensures no self-check
                    })
                    ->select(DB::raw(1)) // Selecting a constant to unify the structure
            )
            ->count();






        // Completed Task
        $completed_my_task = DB::table('Mainbranch')
            ->where('assigned_to_id', $facultyId)
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->where('status', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask')
                    ->whereColumn('Subtask.task_id', 'Mainbranch.task_id')
                    ->whereColumn('Subtask.assigned_by_id', 'Mainbranch.assigned_to_id');
            })
            ->select(DB::raw(1)) // Selecting a constant to unify the structure
            ->unionAll(
                DB::table('Subtask')
                    ->where('assigned_to_id', $facultyId)
                    ->whereBetween('completed_date', [$startDate, $endDate])
                    ->where('status', 3)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('Subtask as sub')
                            ->whereColumn('sub.task_id', 'Subtask.task_id')
                            ->whereColumn('sub.assigned_by_id', 'Subtask.assigned_to_id')
                            ->whereColumn('sub.sid', '<>', 'Subtask.sid'); // Ensures no self-check
                    })
                    ->select(DB::raw(1)) // Selecting a constant to unify the structure
            )
            ->count();
        $completed_assigntask_count = DB::table('Maintask')
            ->where('assigned_by_id', $facultyId)
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->where('status', '2')
            ->select(DB::raw(1)) // Placeholder column for consistency in union
            ->unionAll(
                DB::table('Subtask')
                    ->where('Subtask.assigned_by_id', $facultyId)
                    ->where('Subtask.status', '3')
                    ->join('Maintask', 'Subtask.task_id', '=', 'Maintask.task_id')
                    ->select(DB::raw(1)) // Placeholder column for consistency in union
            )
            ->count();

        $completed = $completed_my_task + $completed_assigntask_count;


        // Overdue Task
        $overdue = DB::table('Subtask as st')
            ->where('st.assigned_to_id', $facultyId)
            ->whereIn('st.status', ['0', '2'])
            ->whereBetween('st.deadline', [$startDate, $endDate]) // Ensure the query is filtered by the date range
            ->where(function ($query) {
                $query->whereNull('st.completed_date') // Condition 1a: If completed_date is NULL
                    ->where('st.deadline', '<', now()) // And deadline is less than today
                    ->orWhereColumn('st.deadline', '<', 'st.completed_date'); // Condition 1b: Deadline is less than completed_date
            })
            ->select(DB::raw(1)) // Select constant for union structure
            ->unionAll(
                DB::table('Mainbranch as mb')
                    ->where('mb.assigned_to_id', $facultyId) // Ensures the query is filtered by the faculty ID
                    ->whereIn('mb.status', ['0', '2'])
                    ->join('Maintask as mt', 'mb.task_id', '=', 'mt.task_id') // Join mainbranch and maintask tables
                    ->whereBetween('mt.deadline', [$startDate, $endDate]) // Ensures the query is filtered by the date range
                    ->where(function ($query) {
                        $query->whereNull('mb.completed_date') // If completed_date is NULL
                            ->where('mt.deadline', '<', now()) // And deadline is less than today
                            ->orWhereColumn('mb.completed_date', '>', 'mt.deadline'); // Or completed_date is greater than deadline
                    })
                    ->select(DB::raw(1)) // Select constant for union structure
            )
            ->count();



        // Return the data as JSON
        return response()->json([
            'assigned' => $assigned,
            'forwarded' => $forwarded,
            'mytask' => $mytask,
            'completed' => $completed,
            'overdue' => $overdue,
            'compmt' => $completed_my_task,
            'compat' => $completed_assigntask_count
        ]);
    }
    public function getDemeritPoints(Request $request)
    {
        $facultyId = session('faculty_id');
        $fromDate = $request->input('start_date');
        $toDate = $request->input('end_date');

        // Validate the date range
        if (!$fromDate || !$toDate) {
            return response()->json(['error' => 'Invalid date range'], 400);
        }

        // Query to calculate the total demerit points within the date range
        $totalDemeritPoints = DB::table('point')
            ->where('assigned_to_id', $facultyId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->sum('demerit_points');

        // Return the total demerit points
        return response()->json(['demerit_points' => $totalDemeritPoints]);
    }
    public function storeReassign(request $request)
    {
        $reassign = Mainbranch::find($request->task_id);
        if ($reassign) {
            $reassign->assigned_to_id = $request->faculty_id;
            $reassign->assigned_to_name = $request->name;
            $reassign->status = '0';
            $reassign->save();

            return response()->json([
                'status' => 200,
                'message' => 'Task reassigned successfully!'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Task not found!'
            ]);
        }
    }
    public function storeReassignforward(request $request)
    {
        $reassign = Subtask::find($request->task_id);
        if ($reassign) {
            $reassign->assigned_to_id = $request->ffaculty_id;
            $reassign->assigned_to_name = $request->fname;
            $reassign->status = '0';
            $reassign->save();

            return response()->json([
                'status' => 200,
                'message' => 'Task reassigned successfully!'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Task not found!'
            ]);
        }
    }
    public function ReassignDate(request $request, $id)
    {
        $Redate = Mainbranch::findorFail($id);
        $task = $request->status;
        if ($task->status == '0') {
            $task->status = '1';
            $task->save();
        }
    }
    public function getedeadline($id)
    {
        $details = Mainbranch::where('id', $id)->first(); // Fetch the record

        if ($details) {
            return response()->json([
                'deadline' => Carbon::parse($details->deadline)->format('d-m-Y'), // Format to YYYY-MM-DD
                'feedback' => $details->feedback
            ]);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }
    public function updateDeadline(Request $request)
    {
        $task = Mainbranch::find($request->task_id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Update values based on newDeadline presence
        $task->deadline = $request->deadline;

        $task->status = $request->status;

        $task->save();

        return response()->json(['success' => true]);
    }
    public function MyReason($id)
    {
        $facultyId = session('faculty_id');

        // Fetch task IDs from Mainbranch
        $my_det1 = Mainbranch::where('assigned_to_id', $facultyId)
            ->whereIn('status', ['0', '1', '2'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask')
                    ->whereColumn('Subtask.task_id', 'Mainbranch.task_id')
                    ->whereColumn('Subtask.assigned_by_id', 'Mainbranch.assigned_to_id');
            })
            ->select('task_id')
            ->get();

        // Fetch task IDs from Subtask
        $my_det2 = Subtask::where('assigned_to_id', $facultyId)
            ->whereIn('status', ['0', '1', '2'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Subtask as sub')
                    ->whereColumn('sub.task_id', 'Subtask.task_id')
                    ->whereColumn('sub.assigned_by_id', 'Subtask.assigned_to_id')
                    ->whereColumn('sub.sid', '<>', 'Subtask.sid'); // Ensures no self-check
            })
            ->select('task_id')
            ->get();

        // Query sub tasks and join with main tasks to fetch task_id and reason
        $subTasks = Subtask::whereIn('subtask.task_id', $my_det2)
            ->where('assigned_to_id', $facultyId)
            ->join('maintask', 'subtask.task_id', '=', 'maintask.task_id')
            ->select('subtask.task_id', 'subtask.reason')
            ->get();

        // Fetch main tasks and their reasons
        $mainTasks = Maintask::whereIn('Maintask.task_id', $my_det1)
            ->join('Mainbranch', 'Mainbranch.task_id', '=', 'Maintask.task_id')
            ->select('Maintask.task_id', 'Mainbranch.reason')
            ->get();

        // Combine the main tasks and sub tasks results
        $combinedTasks = $mainTasks->merge($subTasks);

        // Return the combined tasks as a JSON response
        return response()->json([
            'tasks' => $combinedTasks
        ]);
    }
    public function saveFeedback(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'reason' => 'required|string'
        ]);

        // Check in mainbranch table first
        $task = MainBranch::where('task_id', $request->task_id)->first();

        // If not found, check in subtask table
        if (!$task) {
            $task = SubTask::where('task_id', $request->task_id)->first();
        }

        if ($task) {
            $task->feedback = $request->reason; // Store the user input in feedback column
            $task->save();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['message' => 'Task not found in either table.'], 404);
        }
    }
    public function getFaculties(Request $request)
    {
        if (!$request->has('departments') || empty($request->departments)) {
            return response()->json(['error' => 'No departments selected'], 400);
        }

        $departments = explode(",", $request->departments);

        $faculties = DB::table('faculty')
            ->whereIn('dept', $departments)
            ->select('id', 'name', 'dept')
            ->get();

        if ($faculties->isEmpty()) {
            return response()->json(['error' => 'No faculty found for selected departments'], 404);
        }

        return response()->json($faculties);
    }
}