<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\Department;
use App\Models\LeaveInformation;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use \Illuminate\Support\Facades\Log;


class HRController extends Controller
{
    /** employee list */
    public function employeeList()
    {
        // Retrieve all employees
        $employeeList = User::all();

        // Get the latest user ID and generate the next employee ID
        $latestUser = User::orderBy('id', 'DESC')->first();
        $userId = $latestUser ? (int) substr($latestUser->user_id, 4) + 1 : 1;
        $employeeId = 'KH_' . str_pad($userId, 3, '0', STR_PAD_LEFT); // Zero pad to always show 3 digits

        // Retrieve necessary data for the view
        $roleName = DB::table('role_type_users')->get();
        $position = DB::table('position_types')->get();
        $department = DB::table('departments')->get();
        $statusUser = DB::table('user_types')->get();

        return view('HR.employee', compact('employeeList', 'employeeId', 'roleName', 'position', 'department', 'statusUser'));
    }

    /** save record employee */
    public function employeeSaveRecord(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'position' => 'required|string',
            'department' => 'required|string',
            'role_name' => 'required|string',
            'status' => 'required|string',
            'phone_number' => 'required|numeric',
            'location' => 'required|string',
            'join_date' => 'required|string',
            'experience' => 'required|string',
            'designation' => 'required|string',
        ]);

        try {
            // Generate the photo file name
            $photo = $request->name . '.' . $request->photo->extension();
            $request->photo->move(public_path('assets/images/user'), $photo);

            // Create a new user instance and populate fields
            $register = new User();
            $register->fill([
                'name' => $request->name,
                'email' => $request->email,
                'position' => $request->position,
                'department' => $request->department,
                'role_name' => $request->role_name,
                'status' => $request->status,
                'phone_number' => $request->phone_number,
                'location' => $request->location,
                'join_date' => $request->join_date,
                'experience' => $request->experience,
                'designation' => $request->designation,
                'avatar' => $photo,
                'password' => Hash::make('Hello@123'),
            ]);
            $register->save();

            Toastr()->success('Record added successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error($e); // Log the error
            Toastr()->error('Failed to add record :)', 'Error');
            return redirect()->back();
        }
    }

    /** update record employee */
    public function employeeUpdateRecord(Request $request)
    {
        try {

            $user = User::find($request->id);

            if (!empty($request->photo)) { // ! empty image upload file name
                $photo = $request->name . '-' . time() . '.' . $request->photo->extension();
                $request->photo->move(public_path('assets/images/user'), $photo);
                if (!empty($user->avatar)) { // ! empty image in path user
                    unlink(public_path('assets/images/user/' . $user->avatar));
                }
            } else {
                $photo = $user->avatar; // get image name from databases
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->position = $request->position;
            $user->department = $request->department;
            $user->role_name = $request->role_name;
            $user->status = $request->status;
            $user->phone_number = $request->phone_number;
            $user->location = $request->location;
            $user->join_date = $request->join_date;
            $user->experience = $request->experience;
            $user->designation = $request->designation;
            $user->avatar = $photo;
            $user->save();

            Toastr()->success('Update record successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            Toastr()->error('Update record fail :)', 'Error');
            return redirect()->back();
        }
    }

    /** delete record employee */
    public function employeeDeleteRecord(Request $request)
    {
        try {

            $deleteRecord = User::findOrFail($request->id_delete);
            $deleteRecord->delete();
            if (!empty($request->del_photo)) {
                unlink(public_path('assets/images/user/' . $request->del_photo));
            }

            Toastr()->success('Delete record successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            Toastr()->error('Delete record fail :)', 'Error');
            return redirect()->back();
        }
    }


    /** get information leave */
    public function getInformationLeave(Request $request)
    {
        try {

            $numberOfDay = $request->number_of_day;
            $leaveType = $request->leave_type;

            $leaveDay = LeaveInformation::where('leave_type', $leaveType)->first();

            if ($leaveDay) {
                $days = $leaveDay->leave_days - ($numberOfDay ?? 0);
            } else {
                $days = 0; // Handle case if leave type doesn't exist
            }

            $data = [
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Get success',
                'leave_type' => $days,
                'number_of_day' => $numberOfDay,
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            // Log the exception and return an appropriate response
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /** leave Employee */
    public function leaveEmployee()
    {
        $leave = Leave::where('staff_id', Session::get('user_id'))->get();
        return view('HR.LeavesManage.leave-employee', compact('leave'));
    }

    /** create Leave Employee */
    public function createLeaveEmployee()
    {
        $leaveInformation = LeaveInformation::all();
        return view('HR.LeavesManage.create-leave-employee', compact('leaveInformation'));
    }

    /** save record leave */
    public function saveRecordLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'date_from' => 'required',
            'date_to' => 'required',
            'reason' => 'required',
        ]);

        try {

            $save = new Leave;
            $save->staff_id = Session::get('user_id');
            $save->employee_name = Session::get('name');
            $save->leave_type = $request->leave_type;
            $save->remaining_leave = $request->remaining_leave;
            $save->date_from = $request->date_from;
            $save->date_to = $request->date_to;
            $save->number_of_day = $request->number_of_day;
            $save->leave_date = json_encode($request->leave_date);
            $save->leave_day = json_encode($request->select_leave_day);
            $save->status = 'Pending';
            $save->reason = $request->reason;
            $save->save();

            Toastr()->success('Apply Leave successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error($e); // Log the error
            Toastr()->error('Failed Apply Leave :)', 'Error');
            return redirect()->back();
        }
    }

    /** view detail leave employee */
    public function viewDetailLeave($staff_id)
    {
        $leaveInformation = LeaveInformation::all();
        $leaveDetail = Leave::where('staff_id', $staff_id)->first();
        $leaveDate = json_decode($leaveDetail->leave_date, true); // Decode JSON to array
        $leaveDay = json_decode($leaveDetail->leave_day, true); // Decode JSON to array

        return view('HR.LeavesManage.view-detail-leave', compact('leaveInformation', 'leaveDetail', 'leaveDate', 'leaveDay'));
    }

    /** leave HR */
    public function leaveHR()
    {
        return view('HR.LeavesManage.leave-hr');
    }

    /** attendance */
    public function attendance()
    {
        return view('HR.Attendance.attendance');
    }

    /** create Leave HR */
    public function createLeaveHR()
    {
        $users = User::all();
        $leaveInformation = LeaveInformation::all();
        return view('HR.LeavesManage.create-leave-hr', compact('users', 'leaveInformation'));
    }

    /** attendance Main */
    public function attendanceMain()
    {
        return view('HR.Attendance.attendance-main');
    }
}
