<?php

namespace App\Http\Controllers\Student;

use App\ApiBaseMethod;
use App\Http\Controllers\Controller;
use App\Notifications\StudentHomeworkSubmitNotification;
use App\Scopes\ActiveStatusSchoolScope;
use App\SmAcademicYear;
use App\SmAssignSubject;
use App\SmAssignVehicle;
use App\SmBankAccount;
use App\SmBaseSetup;
use App\SmBook;
use App\SmBookIssue;
use App\SmClass;
use App\SmClassOptionalSubject;
use App\SmClassTime;
use App\SmDormitoryList;
use App\SmEvent;
use App\SmExam;
use App\SmExamSchedule;
use App\SmExamScheduleSubject;
use App\SmExamType;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;
use App\SmGeneralSettings;
use App\SmHoliday;
use App\SmHomework;
use App\SmLeaveDefine;
use App\SmLeaveRequest;
use App\SmLeaveType;
use App\SmLibraryMember;
use App\SmMarksGrade;
use App\SmNoticeBoard;
use App\SmNotification;
use App\SmOnlineExam;
use App\SmOptionalSubjectAssign;
use App\SmPaymentMethhod;
use App\SmRoomList;
use App\SmRoomType;
use App\SmRoute;
use App\SmSection;
use App\SmStaff;
use App\SmStudent;
use App\SmStudentAttendance;
use App\SmStudentCategory;
use App\SmStudentDocument;
use App\SmStudentGroup;
use App\SmStudentTakeOnlineExam;
use App\SmStudentTimeline;
use App\SmTeacherUploadContent;
use App\SmUploadHomeworkContent;
use App\SmVehicle;
use App\SmWeekend;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Brian2694\Toastr\Facades\Toastr;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\OnlineExam\Entities\InfixOnlineExam;
use Modules\RolePermission\Entities\InfixRole;
use Modules\Wallet\Entities\WalletTransaction;
use ZipArchive;

class SmStudentPanelController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }

    public function studentMyAttendanceSearchAPI(Request $request, $id = null)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'month' => "required",
            'year' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $student_detail = SmStudent::where('user_id', $id)->first();

            $year = $request->year;
            $month = $request->month;
            if ($month < 10) {
                $month = '0' . $month;
            }
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $month, $request->year);
            $days2 = '';
            if ($month != 1) {
                $days2 = cal_days_in_month(CAL_GREGORIAN, $month - 1, $request->year);
            } else {
                $days2 = cal_days_in_month(CAL_GREGORIAN, $month, $request->year);
            }
            // return  $days2;
            $previous_month = $month - 1;
            $previous_date = $year . '-' . $previous_month . '-' . $days2;
            $previousMonthDetails['date'] = $previous_date;
            $previousMonthDetails['day'] = $days2;
            $previousMonthDetails['week_name'] = date('D', strtotime($previous_date));
            $attendances = SmStudentAttendance::where('student_id', $student_detail->id)
                ->where('attendance_date', 'like', '%' . $request->year . '-' . $month . '%')
                ->select('attendance_type', 'attendance_date')
                ->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data['attendances'] = $attendances;
                $data['previousMonthDetails'] = $previousMonthDetails;
                $data['days'] = $days;
                $data['year'] = $year;
                $data['month'] = $month;
                $data['current_day'] = $current_day;
                $data['status'] = 'Present: P, Late: L, Absent: A, Holiday: H, Half Day: F';
                return ApiBaseMethod::sendResponse($data, null);
            }
            //Test
            return view('backEnd.studentPanel.student_attendance', compact('attendances', 'days', 'year', 'month', 'current_day'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentMyAttendanceSearch(Request $request, $id = null)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'month' => "required",
            'year' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $login_id = $id;
            } else {
                $login_id = Auth::user()->id;
            }
            $student_detail = SmStudent::where('user_id', $login_id)->first();

            $year = $request->year;
            $month = $request->month;
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

            $attendances = SmStudentAttendance::where('student_id', $student_detail->id)->where('academic_id', getAcademicId())->where('attendance_date', 'like', $request->year . '-' . $request->month . '%')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $academic_years = SmAcademicYear::where('active_status', '=', 1)->where('school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data['attendances'] = $attendances;
                $data['days'] = $days;
                $data['year'] = $year;
                $data['month'] = $month;
                $data['current_day'] = $current_day;
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.studentPanel.student_attendance', compact('attendances', 'days', 'year', 'month', 'current_day', 'student_detail', 'academic_years'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentMyAttendancePrint($month, $year)
    {
        try {
            $login_id = Auth::user()->id;
            $student_detail = SmStudent::where('user_id', $login_id)->first();
            $current_day = date('d');
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $attendances = SmStudentAttendance::where('student_id', $student_detail->id)->where('academic_id', getAcademicId())->where('attendance_date', 'like', $year . '-' . $month . '%')->where('school_id', Auth::user()->school_id)->get();
            $customPaper = array(0, 0, 700.00, 1000.80);
            $pdf = PDF::loadView(
                'backEnd.studentPanel.my_attendance_print',
                [
                    'attendances' => $attendances,
                    'days' => $days,
                    'year' => $year,
                    'month' => $month,
                    'current_day' => $current_day,
                    'student_detail' => $student_detail,
                ]
            )->setPaper('A4', 'landscape');
            return $pdf->stream('my_attendance.pdf');
            //return view('backEnd.studentPanel.student_attendance', compact('attendances', 'days', 'year', 'month', 'current_day', 'student_detail'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentDashboard(Request $request, $id = null)
    {

        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $user_id = $id;
            } else {
                $user = Auth::user();

                if ($user) {
                    $user_id = $user->id;
                } else {
                    $user_id = $request->user_id;
                }
            }

            $student_detail = SmStudent::where('user_id', $user_id)->first();

            $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $student_detail->class_id)->first();

            $student_optional_subject = SmOptionalSubjectAssign::where('student_id', $student_detail->id)
                ->where('session_id', '=', $student_detail->session_id)
                ->first();

            $driver = SmVehicle::where('sm_vehicles.id', '=', $student_detail->vechile_id)
                ->join('sm_staffs', 'sm_staffs.id', '=', 'sm_vehicles.driver_id')
                ->first();

            $siblings = SmStudent::where('parent_id', $student_detail->parent_id)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $fees_assigneds = SmFeesAssign::where('student_id', $student_detail->id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $fees_discounts = SmFeesAssignDiscount::where('student_id', $student_detail->id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $documents = SmStudentDocument::where('student_staff_id', $student_detail->id)
                ->where('type', 'stu')
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $timelines = SmStudentTimeline::where('staff_student_id', $student_detail->id)
                ->where('type', 'stu')
                ->where('visible_to_student', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $exams = SmExamSchedule::where('class_id', $student_detail->class_id)
                ->where('section_id', $student_detail->section_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $grades = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $maxgpa = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->max('gpa');

            $failgpa = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->min('gpa');

            $failgpaname = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where('gpa', $failgpa)
                ->first();

            $exam_terms = SmExamType::with('examSettings')->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $leave_details = SmLeaveRequest::where('staff_id', Auth::user()->id)
                ->where('role_id', Auth::user()->role_id)
                ->where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $result_views = SmStudentTakeOnlineExam::where('active_status', 1)
                ->where('status', 2)
                ->where('academic_id', getAcademicId())
                ->where('student_id', @Auth::user()->student->id)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $paymentMethods = SmPaymentMethhod::whereNotIn('method', ["Cash", "Wallet"])
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $bankAccounts = SmBankAccount::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            if (moduleStatusCheck('Wallet')) {
                $walletAmounts = WalletTransaction::where('user_id', Auth::user()->id)
                    ->where('school_id', Auth::user()->school_id)
                    ->get();

            } else {
                $walletAmounts = 0;

            }

            $custom_field_data = $student_detail->custom_field;

            if (!is_null($custom_field_data)) {
                $custom_field_values = json_decode($custom_field_data);
            } else {
                $custom_field_values = null;
            }

            $academic_year = SmAcademicYear::find($student_detail->session_id);

            $custom_field_data = $student_detail->custom_field;

            if (!is_null($custom_field_data)) {
                $custom_field_values = json_decode($custom_field_data);
            } else {
                $custom_field_values = null;
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['student_detail'] = $student_detail->toArray();
                $data['fees_assigneds'] = $fees_assigneds->toArray();
                $data['fees_discounts'] = $fees_discounts->toArray();
                $data['exams'] = $exams->toArray();
                $data['documents'] = $documents->toArray();
                $data['timelines'] = $timelines->toArray();
                $data['siblings'] = $siblings->toArray();
                $data['grades'] = $grades->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentPanel.my_profile',
                compact(
                    'driver',
                    'academic_year',
                    'student_detail',
                    'fees_assigneds',
                    'fees_discounts',
                    'exams',
                    'documents',
                    'timelines',
                    'siblings',
                    'grades',
                    'exam_terms',
                    'result_views',
                    'leave_details',
                    'optional_subject_setup',
                    'student_optional_subject',
                    'maxgpa',
                    'failgpaname',
                    'custom_field_values',
                    'paymentMethods',
                    'walletAmounts',
                    'bankAccounts'));

        } catch (\Exception $e) {

            return $e->getMessage();
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function studentUpdate(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::beginTransaction();
        try {

            $student = SmStudent::find($request->id);
            if ($request->photo) {
                $file = $request->file('photo');
                $images = Image::make($file)->insert($file);
                if (file_exists($student->photo)) {
                    unlink($student->photo);
                }
                $name = md5($file->getClientOriginalName() . time()) . "." . "png";
                $images->save('public/uploads/student/' . $name);
                $imageName = 'public/uploads/student/' . $name;
                $student->student_photo = $imageName;
            }
            $student->mobile = $request->phone_number;
            $student->bloodgroup_id = $request->blood_group;
            $student->religion_id = $request->religion;
            $student->height = $request->height;
            $student->weight = $request->weight;
            $student->current_address = $request->current_address;
            $student->save();
            DB::commit();

            // session null
            $update_stud = SmStudent::where('user_id', $student->user_id)->first('student_photo');
            Session::put('profile', $update_stud->student_photo);
            Session::put('fathers_photo', '');
            Session::put('mothers_photo', '');
            Session::put('guardians_photo', '');

            Toastr::success('Operation successful', 'Success');
            return redirect('student-profile');
        } catch (\Exception $e) {
            return $e->getMessage();
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentProfileUpdate(Request $request, $id = null)
    {
        try {
            $student = SmStudent::find($id);

            $classes = SmClass::where('active_status', '=', '1')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $religions = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '2')->get();
            $blood_groups = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '3')->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
            $route_lists = SmRoute::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $vehicles = SmVehicle::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $dormitory_lists = SmDormitoryList::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $driver_lists = SmStaff::where([['active_status', '=', '1'], ['role_id', 9]])->where('school_id', Auth::user()->school_id)->get();
            $categories = SmStudentCategory::where('school_id', Auth::user()->school_id)->get();
            $groups = SmStudentGroup::where('school_id', Auth::user()->school_id)->get();
            $sessions = SmAcademicYear::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $siblings = SmStudent::where('parent_id', $student->parent_id)->where('school_id', Auth::user()->school_id)->get();

            return view('backEnd.studentPanel.my_profile_update', compact('student', 'classes', 'religions', 'blood_groups', 'genders', 'route_lists', 'vehicles', 'dormitory_lists', 'categories', 'groups', 'sessions', 'siblings', 'driver_lists'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function studentProfile(Request $request, $id = null)
    {

        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $user_id = $id;
            } else {
                $user = Auth::user();

                if ($user) {
                    $user_id = $user->id;
                } else {
                    $user_id = $request->user_id;
                }
            }

            $student_detail = SmStudent::withOutGlobalScope(ActiveStatusSchoolScope::class)->where('user_id', $user_id)->first();

            $driver = SmVehicle::where('sm_vehicles.id', '=', $student_detail->vechile_id)
                ->join('sm_staffs', 'sm_staffs.id', '=', 'sm_vehicles.driver_id')
                ->first();
            $siblings = SmStudent::where('parent_id', $student_detail->parent_id)->where('school_id', Auth::user()->school_id)->get();
            $fees_assigneds = SmFeesAssign::where('student_id', $student_detail->id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $fees_discounts = SmFeesAssignDiscount::where('student_id', $student_detail->id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $documents = SmStudentDocument::where('student_staff_id', $student_detail->id)->where('type', 'stu')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $timelines = SmStudentTimeline::where('staff_student_id', $student_detail->id)->where('type', 'stu')->where('visible_to_student', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $exams = SmExamSchedule::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $grades = SmMarksGrade::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $totalSubjects = SmAssignSubject::where('class_id', '=', $student_detail->class_id)->where('section_id', '=', $student_detail->section_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $totalNotices = SmNoticeBoard::where('active_status', 1)->where('inform_to', 'LIKE', '%2%')
                ->orderBy('id', 'DESC')
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $time_zone_setup = SmGeneralSettings::join('sm_time_zones', 'sm_time_zones.id', '=', 'sm_general_settings.time_zone_id')
                ->where('school_id', Auth::user()->school_id)->first();
            date_default_timezone_set($time_zone_setup->time_zone);

            $now = date('Y-m-d');

            if (moduleStatusCheck('OnlineExam') == true) {
                $online_exams = InfixOnlineExam::where('active_status', 1)
                    ->where('status', 1)
                    ->where('class_id', $student_detail->class_id)
                    ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id);
                if ($student_detail->status != 0) {
                    $online_exams->where('section_id', $student_detail->section_id);
                }
                $online_exams = $online_exams->get();
            } else {
                $online_exams = SmOnlineExam::where('active_status', 1)
                    ->where('status', 1)
                    ->where('class_id', $student_detail->class_id)
                    ->where('category_type', '=', $student_detail->status)
                // ->where('date', 'like', date('Y-m-d'))->where('start_time', '<', $now)->where('end_time', '>', $now)
                    ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id);

                // if ($student_detail->status==) {
                $online_exams->where('section_id', $student_detail->section_id);
                // }

                $online_exams = $online_exams->get();
            }

            // dd($online_exams);

            $teachers = SmAssignSubject::select('teacher_id')->where('class_id', $student_detail->class_id)
                ->where('section_id', $student_detail->section_id)->distinct('teacher_id')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $issueBooks = SmBookIssue::where('member_id', $student_detail->user_id)->where('issue_status', 'I')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $homeworkLists = SmHomework::where('class_id', $student_detail->class_id)
                ->where('section_id', $student_detail->section_id)
                ->where('evaluation_date', '=', null)
                ->where('submission_date', '>', $now)
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $month = date('m');
            $year = date('Y');
            // return $year;

            $attendances = SmStudentAttendance::where('student_id', $student_detail->id)
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('attendance_type', '=', 'P')->where('school_id', Auth::user()->school_id)->get();
            // return $attendances;

            $holidays = SmHoliday::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $events = SmEvent::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where(function ($q) {
                    $q->where('for_whom', 'All')->orWhere('for_whom', 'Student');
                })
                ->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['student_detail'] = $student_detail->toArray();
                $data['fees_assigneds'] = $fees_assigneds->toArray();
                $data['fees_discounts'] = $fees_discounts->toArray();
                $data['exams'] = $exams->toArray();
                $data['documents'] = $documents->toArray();
                $data['timelines'] = $timelines->toArray();
                $data['siblings'] = $siblings->toArray();
                $data['grades'] = $grades->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.studentPanel.studentProfile', compact('totalSubjects', 'totalNotices', 'online_exams', 'teachers', 'issueBooks', 'homeworkLists', 'attendances', 'driver', 'student_detail', 'fees_assigneds', 'fees_discounts', 'exams', 'documents', 'timelines', 'siblings', 'grades', 'events', 'holidays'));
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentsDocumentApi(Request $request, $id)
    {
        try {
            $student_detail = SmStudent::where('user_id', $id)->first();
            $documents = SmStudentDocument::where('student_staff_id', $student_detail->id)->where('type', 'stu')
                ->select('title', 'file')
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['student_detail'] = $student_detail->toArray();
                $data['documents'] = $documents->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function classRoutine(Request $request, $id = null)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $user_id = $id;
            } else {
                $user = Auth::user();

                if ($user) {
                    $user_id = $user->id;
                } else {
                    $user_id = $request->user_id;
                }
            }

            $student_detail = SmStudent::where('user_id', $user_id)->first();
            //return $student_detail;
            $class_id = $student_detail->class_id;
            $section_id = $student_detail->section_id;

            $sm_weekends = SmWeekend::with('studentClassRoutine', 'studentClassRoutine.subject', 'studentClassRoutine.teacherDetail')->orderBy('order', 'ASC')->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();

            $class_times = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['student_detail'] = $student_detail->toArray();
                // $data['class_id'] = $class_id;
                // $data['section_id'] = $section_id;
                // $data['sm_weekends'] = $sm_weekends->toArray();
                // $data['class_times'] = $class_times->toArray();

                $weekenD = SmWeekend::all();
                foreach ($weekenD as $row) {
                    $data[$row->name] = DB::table('sm_class_routine_updates')
                        ->select('sm_class_times.period', 'sm_class_times.start_time', 'sm_class_times.end_time', 'sm_subjects.subject_name', 'sm_class_rooms.room_no')
                        ->join('sm_classes', 'sm_classes.id', '=', 'sm_class_routine_updates.class_id')
                        ->join('sm_sections', 'sm_sections.id', '=', 'sm_class_routine_updates.section_id')
                        ->join('sm_class_times', 'sm_class_times.id', '=', 'sm_class_routine_updates.class_period_id')
                        ->join('sm_subjects', 'sm_subjects.id', '=', 'sm_class_routine_updates.subject_id')
                        ->join('sm_class_rooms', 'sm_class_rooms.id', '=', 'sm_class_routine_updates.room_id')

                        ->where([
                            ['sm_class_routine_updates.class_id', $class_id], ['sm_class_routine_updates.section_id', $section_id], ['sm_class_routine_updates.day', $row->id],
                        ])->where('sm_class_routine_updates.academic_id', getAcademicId())->where('sm_classesschool_id', Auth::user()->school_id)->get();
                }

                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.studentPanel.class_routine', compact('class_times', 'class_id', 'section_id', 'sm_weekends'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentResult()
    {
        try {
            $student_detail = Auth::user()->student;
            $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $student_detail->class_id)->first();

            $student_optional_subject = SmOptionalSubjectAssign::where('student_id', $student_detail->id)
                ->where('session_id', '=', $student_detail->session_id)
                ->first();

            $exams = SmExamSchedule::where('class_id', $student_detail->class_id)
                ->where('section_id', $student_detail->section_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $grades = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $failgpa = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->min('gpa');

            $failgpaname = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where('gpa', $failgpa)
                ->first();
            $maxgpa = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->max('gpa');

            $exam_terms = SmExamType::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('backEnd.studentPanel.student_result', compact('student_detail', 'exams', 'grades', 'exam_terms', 'failgpaname', 'optional_subject_setup', 'student_optional_subject', 'maxgpa'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentExamSchedule()
    {
        try {
            $student_detail = Auth::user()->student;
            $exam_types = SmExamType::where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->where('active_status', 1)->get();
            return view('backEnd.studentPanel.exam_schedule', compact('exam_types'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentExamScheduleSearch(Request $request)
    {
        $request->validate([
            'exam' => 'required',
        ]);

        try {
            $student_detail = Auth::user()->student;

            $assign_subjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            if ($assign_subjects->count() == 0) {
                Toastr::error('No Subject Assigned.', 'Failed');
                return redirect('student-exam-schedule');
            }

            $exams = SmExam::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $class_id = $student_detail->class_id;
            $section_id = $student_detail->section_id;
            $exam_id = $request->exam;
            $exam_type_id = $request->exam;

            $exam_types = SmExamType::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->where('active_status', 1)->get();
            $exam_periods = SmClassTime::where('type', 'exam')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $exam_schedule_subjects = "";
            $assign_subject_check = "";

            $exam_routines = SmExamSchedule::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('exam_term_id', $request->exam)->orderBy('date', 'ASC')->get();

            return view('backEnd.studentPanel.exam_schedule', compact('exams', 'assign_subjects', 'class_id', 'section_id', 'exam_id', 'exam_schedule_subjects', 'assign_subject_check', 'exam_types', 'exam_type_id', 'exam_periods', 'exam_routines'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function examRoutinePrint($class_id, $section_id, $exam_term_id)
    {

        try {

            $exam_type_id = $exam_term_id;
            $exam_type = SmExamType::find($exam_type_id)->title;
            $academic_id = SmExamType::find($exam_type_id)->academic_id;
            $academic_year = SmAcademicYear::find($academic_id);
            $class_name = SmClass::find($class_id)->class_name;
            $section_name = SmSection::find($section_id)->section_name;

            $exam_schedules = SmExamSchedule::where('class_id', $class_id)->where('section_id', $section_id)
                ->where('exam_term_id', $exam_type_id)->orderBy('date', 'ASC')->get();

            $pdf = PDF::loadView(
                'backEnd.examination.exam_schedule_print',
                [
                    'exam_schedules' => $exam_schedules,
                    'exam_type' => $exam_type,
                    'class_name' => $class_name,
                    'academic_year' => $academic_year,
                    'section_name' => $section_name,

                ]
            )->setPaper('A4', 'landscape');
            return $pdf->stream('EXAM_SCHEDULE.pdf');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentExamScheduleApi(Request $request, $id)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $student_detail = SmStudent::where('user_id', $id)->first();
                // $assign_subjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
                $exam_schedule = DB::table('sm_exam_schedules')
                    ->join('sm_students', 'sm_students.class_id', '=', 'sm_exam_schedules.class_id')
                    ->join('sm_exam_types', 'sm_exam_types.id', '=', 'sm_exam_schedules.exam_term_id')
                    ->join('sm_exam_schedule_subjects', 'sm_exam_schedule_subjects.exam_schedule_id', '=', 'sm_exam_schedules.id')
                    ->join('sm_subjects', 'sm_subjects.id', '=', 'sm_exam_schedules.subject_id')
                    ->select('sm_subjects.subject_name', 'sm_exam_schedule_subjects.start_time', 'sm_exam_schedule_subjects.end_time', 'sm_exam_schedule_subjects.date', 'sm_exam_schedule_subjects.room', 'sm_exam_schedules.class_id', 'sm_exam_schedules.section_id')
                //->where('sm_students.class_id', '=', 'sm_exam_schedules.class_id')

                    ->where('sm_exam_schedules.section_id', '=', $student_detail->section_id)
                    ->where('sm_exam_schedulesacademic_id', getAcademicId())->where('sm_exam_schedules.school_id', Auth::user()->school_id)->get();
                return ApiBaseMethod::sendResponse($exam_schedule, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentViewExamSchedule($id)
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            $class = SmClass::find($student_detail->class_id);
            $section = SmSection::find($student_detail->section_id);
            $assign_subjects = SmExamScheduleSubject::where('exam_schedule_id', $id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            return view('backEnd.examination.view_exam_schedule_modal', compact('class', 'section', 'assign_subjects'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentMyAttendance()
    {
        try {
            $academic_years = SmAcademicYear::where('active_status', '=', 1)->where('school_id', Auth::user()->school_id)->get();
            // return $academic_years;
            return view('backEnd.studentPanel.student_attendance', compact('academic_years'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentHomework(Request $request, $id = null)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $student_detail = SmStudent::where('user_id', $id)->first();

                $class_id = $student_detail->class->id;
                $subject_list = SmAssignSubject::where([['class_id', $class_id], ['section_id', $student_detail->section_id]])->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

                $i = 0;
                foreach ($subject_list as $subject) {
                    $homework_subject_list[$subject->subject->subject_name] = $subject->subject->subject_name;
                    $allList[$subject->subject->subject_name] = DB::table('sm_homeworks')
                        ->leftjoin('sm_subjects', 'sm_subjects.id', '=', 'sm_homeworks.subject_id')
                        ->where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)
                        ->where('subject_id', $subject->subject_id)->where('academic_id', getAcademicId())->where('sm_homeworks.school_id', Auth::user()->school_id)->get()->toArray();
                }

                foreach ($allList as $single) {
                    foreach ($single as $singleHw) {
                        $std_homework = DB::table('sm_homework_students')
                            ->select('homework_id', 'complete_status')
                            ->where('homework_id', '=', $singleHw->id)
                            ->where('student_id', '=', $student_detail->id)
                            ->where('complete_status', 'C')
                            ->where('academic_id', getAcademicId())
                            ->where('sm_homework_students.school_id', Auth::user()->school_id)
                            ->first();

                        $d['description'] = $singleHw->description;
                        $d['subject_name'] = $singleHw->subject_name;
                        $d['homework_date'] = $singleHw->homework_date;
                        $d['submission_date'] = $singleHw->submission_date;
                        $d['evaluation_date'] = $singleHw->evaluation_date;
                        $d['file'] = $singleHw->file;
                        $d['marks'] = $singleHw->marks;

                        if (!empty($std_homework)) {
                            $d['status'] = 'C';
                        } else {
                            $d['status'] = 'I';
                        }
                        $kijanidibo[] = $d;
                    }
                }
                // return $kijanidibo;

                $homeworkLists = SmHomework::with('classes', 'sections', 'subjects')->where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)
                    ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            } else {
                $user = Auth::user();
                $student_detail = SmStudent::where('user_id', $user->id)->first();
                $homeworkLists = SmHomework::with('classes', 'sections', 'subjects')->where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)
                    ->where('sm_homeworks.academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            }
            $data = [];

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                $data = $kijanidibo;
                return ApiBaseMethod::sendResponse($data, null);
            }
            // return getAcademicId();
            return view('backEnd.studentPanel.student_homework', compact('homeworkLists', 'student_detail'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentHomeworkView($class_id, $section_id, $homework_id)
    {
        try {
            $homeworkDetails = SmHomework::where('class_id', '=', $class_id)->where('section_id', '=', $section_id)->where('id', '=', $homework_id)->first();
            return view('backEnd.studentPanel.studentHomeworkView', compact('homeworkDetails', 'homework_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addHomeworkContent($homework_id)
    {
        try {
            return view('backEnd.studentPanel.addHomeworkContent', compact('homework_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteViewHomeworkContent($homework_id)
    {
        try {

            return view('backEnd.studentPanel.deleteHomeworkContent', compact('homework_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteHomeworkContent($homework_id)
    {
        try {
            //    return $homework_id;
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            $contents = SmUploadHomeworkContent::where('student_id', $student_detail->id)->where('homework_id', $homework_id)->get();
            foreach ($contents as $key => $content) {
                if ($content->file != "") {
                    if (file_exists($content->file)) {
                        unlink($content->file);
                    }
                }
                $content->delete();
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function uploadHomeworkContent(Request $request)
    {
        // $input = $request->all();
        // $validator = Validator::make($input, [
        //     'files' => "mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3,txt",
        // ]);

        // if ($validator->fails()) {
        //     Toastr::warning('Unsupported file upload', 'Failed');
        //     return redirect()->back();
        // }

        if ($request->file('files') == "") {
            Toastr::error('No file uploaded', 'Failed');
            return redirect()->back();
        }
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            $data = [];
            foreach ($request->file('files') as $key => $file) {
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/homeworkcontent/', $fileName);
                $fileName = 'public/uploads/homeworkcontent/' . $fileName;
                $data[$key] = $fileName;
            }
            $all_filename = json_encode($data);
            $content = new SmUploadHomeworkContent();
            $content->file = $all_filename;
            $content->student_id = $student_detail->id;
            $content->homework_id = $request->id;
            $content->school_id = Auth::user()->school_id;
            $content->academic_id = getAcademicId();
            $content->save();

            $homework_info = SmHomeWork::find($request->id);
            $teacher_info = $teacher_info = User::find($homework_info->created_by);

            $notification = new SmNotification;
            $notification->user_id = $teacher_info->id;
            $notification->role_id = $teacher_info->role_id;
            $notification->date = date('Y-m-d');
            $notification->message = Auth::user()->student->full_name . ' ' . app('translator')->get('homework.submitted_homework');
            $notification->school_id = Auth::user()->school_id;
            $notification->academic_id = getAcademicId();
            $notification->save();

            try {
                $user = User::find($teacher_info->id);
                Notification::send($user, new StudentHomeworkSubmitNotification($notification));
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function uploadContentView(Request $request, $id)
    {
        try {
            $ContentDetails = SmTeacherUploadContent::where('id', $id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->first();
            return view('backEnd.studentPanel.uploadContentDetails', compact('ContentDetails'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentAssignment()
    {
        try {
            $user = Auth::user();

            $student_detail = SmStudent::where('user_id', $user->id)->first();

            $uploadContents = SmTeacherUploadContent::where('content_type', 'as')
                ->where(function ($query) use ($student_detail) {
                    $query->where('available_for_all_classes', 1)
                        ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
                })->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            if (Auth()->user()->role_id != 1) {
                if ($user->role_id == 2) {
                    SmNotification::where('user_id', $user->student->id)->where('role_id', 2)->update(['is_read' => 1]);
                }
            }

            $uploadContents2 = SmTeacherUploadContent::where('content_type', 'as')
                ->where('class', $student_detail->class_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.studentPanel.assignmentList', compact('uploadContents', 'uploadContents2'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentAssignmentApi(Request $request, $id)
    {
        try {
            $student_detail = SmStudent::where('user_id', $id)->first();
            $uploadContents = SmTeacherUploadContent::where('content_type', 'as')
                ->select('content_title', 'upload_date', 'description', 'upload_file')
                ->where(function ($query) use ($student_detail) {
                    $query->where('available_for_all_classes', 1)
                        ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
                })->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['student_detail'] = $student_detail->toArray();
                $data['uploadContents'] = $uploadContents->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentStudyMaterial()
    {

        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();

            $uploadContents = SmTeacherUploadContent::where('content_type', 'st')
                ->where(function ($query) use ($student_detail) {
                    $query->where('available_for_all_classes', 1)
                        ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
                })->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            return view('backEnd.studentPanel.studyMetarialList', compact('uploadContents'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentSyllabus()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();

            $uploadContents = SmTeacherUploadContent::where('content_type', 'sy')
                ->where(function ($query) use ($student_detail) {
                    $query->where('available_for_all_classes', 1)
                        ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
                })->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $uploadContents2 = SmTeacherUploadContent::where('content_type', 'ot')
                ->where('class', $student_detail->class_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.studentPanel.studentSyllabus', compact('uploadContents', 'uploadContents2'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function othersDownload()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            $uploadContents = SmTeacherUploadContent::where('content_type', 'ot')
                ->where(function ($query) use ($student_detail) {
                    $query->where('available_for_all_classes', 1)
                        ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
                })->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $uploadContents2 = SmTeacherUploadContent::where('content_type', 'ot')
                ->where('class', $student_detail->class_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.studentPanel.othersDownload', compact('uploadContents', 'uploadContents2'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentSubject()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            $assignSubjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            return view('backEnd.studentPanel.student_subject', compact('assignSubjects'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //Student Subject API
    public function studentSubjectApi(Request $request, $id)
    {
        try {
            $student = SmStudent::where('user_id', $id)->first();
            $assignSubjects = DB::table('sm_assign_subjects')
                ->leftjoin('sm_subjects', 'sm_subjects.id', '=', 'sm_assign_subjects.subject_id')
                ->leftjoin('sm_staffs', 'sm_staffs.id', '=', 'sm_assign_subjects.teacher_id')
                ->select('sm_subjects.subject_name', 'sm_subjects.subject_code', 'sm_subjects.subject_type', 'sm_staffs.full_name as teacher_name')
                ->where('sm_assign_subjects.class_id', '=', $student->class_id)
                ->where('sm_assign_subjects.section_id', '=', $student->section_id)
                ->where('sm_assign_subjects.academic_id', getAcademicId())->where('sm_assign_subjects.school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['student_subjects'] = $assignSubjects->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //student panel Transport
    public function studentTransport()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();

            // $routes = SmAssignVehicle::where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            $routes = SmAssignVehicle::join('sm_vehicles', 'sm_assign_vehicles.vehicle_id', 'sm_vehicles.id')
                ->join('sm_students', 'sm_vehicles.id', 'sm_students.vechile_id')
                ->where('sm_assign_vehicles.active_status', 1)
                ->where('sm_students.user_id', Auth::user()->id)
                ->where('sm_assign_vehicles.school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.studentPanel.student_transport', compact('routes', 'student_detail'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentTransportViewModal($r_id, $v_id)
    {
        try {
            $vehicle = SmVehicle::find($v_id);
            $route = SmRoute::find($r_id);
            return view('backEnd.studentPanel.student_transport_view_modal', compact('route', 'vehicle'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentDormitory()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            // $room_lists = SmRoomList::where('school_id',Auth::user()->school_id)->get();
            // $room_lists = SmRoomList::join('sm_students','sm_students.room_id','sm_room_lists.id')
            // ->where('sm_room_lists.active_status', 1)->where('sm_room_lists.id', $student_detail->room_id)->where('sm_room_lists.school_id',Auth::user()->school_id)->get();
            $room_lists = SmRoomList::where('active_status', 1)->where('id', $student_detail->room_id)->where('school_id', Auth::user()->school_id)->get();

            $room_lists = $room_lists->groupBy('dormitory_id');
            $room_types = SmRoomType::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            $dormitory_lists = SmDormitoryList::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            return view('backEnd.studentPanel.student_dormitory', compact('room_lists', 'room_types', 'dormitory_lists', 'student_detail'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentBookList()
    {
        try {
            $books = SmBook::where('active_status', 1)
                ->orderBy('id', 'DESC')
                ->where('school_id', Auth::user()->school_id)->get();
            return view('backEnd.studentPanel.studentBookList', compact('books'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentBookIssue()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            // $books = SmBook::select('id', 'book_title')->where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            // $subjects = SmSubject::select('id', 'subject_name')->where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            $library_member = SmLibraryMember::where('member_type', 2)->where('student_staff_id', $student_detail->user_id)->first();
            if (empty($library_member)) {
                Toastr::error('You are not library member ! Please contact with librarian', 'Failed');
                return redirect()->back();
                // return redirect()->back()->with('message-danger', 'You are not library member ! Please contact with librarian');
            }
            $issueBooks = SmBookIssue::where('member_id', $library_member->student_staff_id)
                ->leftjoin('sm_books', 'sm_books.id', 'sm_book_issues.book_id')
                ->leftjoin('library_subjects', 'library_subjects.id', 'sm_books.book_subject_id')
                ->where('sm_book_issues.issue_status', 'I')
                ->where('sm_book_issues.school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.studentPanel.studentBookIssue', compact('issueBooks'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentNoticeboard(Request $request)
    {
        try {
            $data = [];
            $allNotices = SmNoticeBoard::where('active_status', 1)->where('inform_to', 'LIKE', '%2%')
                ->orderBy('id', 'DESC')
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data['allNotices'] = $allNotices->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentPanel.studentNoticeboard', compact('allNotices'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentTeacher()
    {
        try {
            $user = Auth::user();
            $student_detail = SmStudent::where('user_id', $user->id)->first();
            $teachers = SmAssignSubject::select('teacher_id')->where('class_id', $student_detail->class_id)
                ->where('section_id', $student_detail->section_id)->distinct('teacher_id')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            return view('backEnd.studentPanel.studentTeacher', compact('teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function studentTeacherApi(Request $request, $id)
    {
        try {
            $student = SmStudent::where('user_id', $id)->first();

            $assignTeacher = DB::table('sm_assign_subjects')
                ->leftjoin('sm_subjects', 'sm_subjects.id', '=', 'sm_assign_subjects.subject_id')
                ->leftjoin('sm_staffs', 'sm_staffs.id', '=', 'sm_assign_subjects.teacher_id')
            //->select('sm_subjects.subject_name', 'sm_subjects.subject_code', 'sm_subjects.subject_type', 'sm_staffs.full_name')
                ->select('sm_staffs.full_name', 'sm_staffs.email', 'sm_staffs.mobile')
                ->where('sm_assign_subjects.class_id', '=', $student->class_id)
                ->where('sm_assign_subjects.section_id', '=', $student->section_id)
                ->where('sm_assign_subjects.school_id', Auth::user()->school_id)->get();

            $class_teacher = DB::table('sm_class_teachers')
                ->join('sm_assign_class_teachers', 'sm_assign_class_teachers.id', '=', 'sm_class_teachers.assign_class_teacher_id')
                ->join('sm_staffs', 'sm_class_teachers.teacher_id', '=', 'sm_staffs.id')
                ->where('sm_assign_class_teachers.class_id', '=', $student->class_id)
                ->where('sm_assign_class_teachers.section_id', '=', $student->section_id)
                ->where('sm_assign_class_teachers.active_status', '=', 1)
                ->select('full_name')
                ->first();
            $settings = SmGeneralSettings::find(1);
            if (@$settings->phone_number_privacy == 1) {
                $permission = 1;
            } else {
                $permission = 0;
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['teacher_list'] = $assignTeacher->toArray();
                $data['class_teacher'] = $class_teacher;
                $data['permission'] = $permission;
                return ApiBaseMethod::sendResponse($data, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentLibrary(Request $request, $id)
    {
        try {
            $student = SmStudent::where('user_id', $id)->first();
            $issueBooks = DB::table('sm_book_issues')
                ->leftjoin('sm_books', 'sm_books.id', '=', 'sm_book_issues.book_id')
                ->where('sm_book_issues.member_id', '=', $student->user_id)
                ->where('sm_book_issues.school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['issueBooks'] = $issueBooks->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function studentDormitoryApi(Request $request)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $studentDormitory = DB::table('sm_room_lists')
                    ->join('sm_dormitory_lists', 'sm_room_lists.dormitory_id', '=', 'sm_dormitory_lists.id')
                    ->join('sm_room_types', 'sm_room_lists.room_type_id', '=', 'sm_room_types.id')
                    ->select('sm_dormitory_lists.dormitory_name', 'sm_room_lists.name as room_number', 'sm_room_lists.number_of_bed', 'sm_room_lists.cost_per_bed', 'sm_room_lists.active_status')->where('sm_room_lists.school_id', Auth::user()->school_id)->get();

                return ApiBaseMethod::sendResponse($studentDormitory, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function studentTimelineApi(Request $request, $id)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                //$timelines = SmStudentTimeline::where('staff_student_id', $id)->first();
                $timelines = DB::table('sm_student_timelines')
                    ->leftjoin('sm_students', 'sm_students.id', '=', 'sm_student_timelines.staff_student_id')
                    ->where('sm_student_timelines.type', '=', 'stu')
                    ->where('sm_student_timelines.active_status', '=', 1)
                    ->where('sm_students.user_id', '=', $id)
                    ->select('title', 'date', 'description', 'file', 'sm_student_timelines.active_status')
                    ->where('sm_student_timelines.academic_id', getAcademicId())->where('sm_students.school_id', Auth::user()->school_id)->get();
                return ApiBaseMethod::sendResponse($timelines, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function examListApi(Request $request, $id)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                $student = SmStudent::where('user_id', $id)->first();
                // return  $student;
                $exam_List = DB::table('sm_exam_types')
                    ->join('sm_exams', 'sm_exams.exam_type_id', '=', 'sm_exam_types.id')
                    ->where('sm_exams.class_id', '=', $student->class_id)
                    ->where('sm_exams.section_id', '=', $student->section_id)
                    ->distinct()
                    ->select('sm_exam_types.id as exam_id', 'sm_exam_types.title as exam_name')
                    ->where('sm_exam_types.school_id', Auth::user()->school_id)->get();
                return ApiBaseMethod::sendResponse($exam_List, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function examScheduleApi(Request $request, $id, $exam_id)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $student = SmStudent::where('user_id', $id)->first();
                $exam_schedule = DB::table('sm_exam_schedules')
                    ->join('sm_exam_types', 'sm_exam_types.id', '=', 'sm_exam_schedules.exam_term_id')
                // ->join('sm_exam_types','sm_exam_types.id','=','sm_exam_schedules.exam_term_id' )
                    ->join('sm_subjects', 'sm_subjects.id', '=', 'sm_exam_schedules.subject_id')
                    ->join('sm_class_rooms', 'sm_class_rooms.id', '=', 'sm_exam_schedules.room_id')
                    ->join('sm_class_times', 'sm_class_times.id', '=', 'sm_exam_schedules.exam_period_id')
                    ->where('sm_exam_schedules.exam_term_id', '=', $exam_id)
                    ->where('sm_exam_schedules.school_id', '=', $student->school_id)
                    ->where('sm_exam_schedules.class_id', '=', $student->class_id)
                    ->where('sm_exam_schedules.section_id', '=', $student->section_id)
                    ->where('sm_exam_schedules.active_status', '=', 1)
                    ->select('sm_exam_types.id', 'sm_exam_types.title as exam_name', 'sm_subjects.subject_name', 'date', 'sm_class_rooms.room_no', 'sm_class_times.start_time', 'sm_class_times.end_time')
                    ->where('sm_exam_schedules.school_id', Auth::user()->school_id)->get();
                return ApiBaseMethod::sendResponse($exam_schedule, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function examResultApi(Request $request, $id, $exam_id)
    {
        try {
            $data = [];

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $student = SmStudent::where('user_id', $id)->first();
                $exam_result = DB::table('sm_result_stores')
                    ->join('sm_exam_types', 'sm_exam_types.id', '=', 'sm_result_stores.exam_type_id')
                    ->join('sm_exams', 'sm_exams.id', '=', 'sm_exam_types.id')
                    ->join('sm_subjects', 'sm_subjects.id', '=', 'sm_result_stores.subject_id')
                    ->where('sm_exams.id', '=', $exam_id)
                    ->where('sm_result_stores.school_id', '=', $student->school_id)
                    ->where('sm_result_stores.class_id', '=', $student->class_id)
                    ->where('sm_result_stores.section_id', '=', $student->section_id)
                    ->where('sm_result_stores.student_id', '=', $student->id)
                    ->select('sm_exams.id', 'sm_exam_types.title as exam_name', 'sm_subjects.subject_name', 'sm_result_stores.total_marks as obtained_marks', 'sm_exams.exam_mark as total_marks', 'sm_result_stores.total_gpa_grade as grade')
                    ->where('sm_exams.school_id', Auth::user()->school_id)->get();

                $data['exam_result'] = $exam_result->toArray();
                $data['pass_marks'] = 0;

                return ApiBaseMethod::sendResponse($data, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function updatePassowrdStoreApi(Request $request)
    {
        try {
            $user = User::find($request->id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                if (Hash::check($request->current_password, $user->password)) {

                    $user->password = Hash::make($request->new_password);
                    $result = $user->save();
                    $msg = "Password Changed Successfully ";
                    return ApiBaseMethod::sendResponse(null, $msg);
                } else {
                    $msg = "You Entered Wrong Current Password";
                    return ApiBaseMethod::sendError(null, $msg);
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function leaveApply(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                $my_leaves = SmLeaveDefine::where('role_id', $user->role_id)->where('user_id', $user->id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $apply_leaves = SmLeaveRequest::where('staff_id', $user->id)->where('role_id', $user->role_id)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                // return $apply_leaves;
                $leave_types = SmLeaveDefine::where('role_id', $user->role_id)->where('user_id', $user->id)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            } else {
                $my_leaves = SmLeaveDefine::where('role_id', $request->role_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $apply_leaves = SmLeaveRequest::where('role_id', $request->role_id)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $leave_types = SmLeaveDefine::where('role_id', $request->role_id)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            }

            return view('backEnd.student_leave.apply_leave', compact('apply_leaves', 'leave_types', 'my_leaves'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function leaveStore(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $request->validate([
            'apply_date' => "required",
            'leave_type' => "required",
            'leave_from' => 'required|before_or_equal:leave_to',
            'leave_to' => "required",
            'attach_file' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt",
        ]);
        try {
            $maxFileSize = SmGeneralSettings::first('file_size')->file_size;
            $file = $request->file('attach_file');
            $fileSize = filesize($file);
            $fileSizeKb = ($fileSize / 1000000);
            if ($fileSizeKb >= $maxFileSize) {
                Toastr::error('Max upload file size ' . $maxFileSize . ' Mb is set in system', 'Failed');
                return redirect()->back();
            }
            $input = $request->all();
            $fileName = "";
            if ($request->file('attach_file') != "") {
                $file = $request->file('attach_file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/leave_request/', $fileName);
                $fileName = 'public/uploads/leave_request/' . $fileName;
            }
            $user = Auth()->user();
            if ($user) {
                $login_id = $user->id;
                $role_id = $user->role_id;
            } else {
                $login_id = $request->login_id;
                $role_id = $request->role_id;
            }
            $apply_leave = new SmLeaveRequest();
            $apply_leave->staff_id = $login_id;
            $apply_leave->role_id = $role_id;
            $apply_leave->apply_date = date('Y-m-d', strtotime($request->apply_date));
            $apply_leave->leave_define_id = $request->leave_type;
            $apply_leave->type_id = $request->leave_type;
            $apply_leave->leave_from = date('Y-m-d', strtotime($request->leave_from));
            $apply_leave->leave_to = date('Y-m-d', strtotime($request->leave_to));
            $apply_leave->approve_status = 'P';
            $apply_leave->reason = $request->reason;
            $apply_leave->file = $fileName;
            $apply_leave->academic_id = getAcademicId();
            $apply_leave->school_id = Auth::user()->school_id;
            $result = $apply_leave->save();

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function pendingLeave(Request $request)
    {
        try {
            $apply_leaves = SmLeaveRequest::with('leaveDefine', 'student')->where([['active_status', 1], ['approve_status', 'P']])->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $leave_types = SmLeaveType::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $roles = InfixRole::where('id', 2)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
            $pendingRequest = SmLeaveRequest::where('sm_leave_requests.active_status', 1)
                ->select('sm_leave_requests.id', 'full_name', 'apply_date', 'leave_from', 'leave_to', 'reason', 'file', 'sm_leave_types.type', 'approve_status')
                ->join('sm_leave_defines', 'sm_leave_requests.leave_define_id', '=', 'sm_leave_defines.id')
                ->join('sm_staffs', 'sm_leave_requests.staff_id', '=', 'sm_staffs.id')
                ->leftjoin('sm_leave_types', 'sm_leave_requests.type_id', '=', 'sm_leave_types.id')
                ->where('sm_leave_requests.approve_status', '=', 'P')
                ->where('sm_leave_requests.academic_id', getAcademicId())->where('sm_leave_requests.school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['pending_request'] = $pendingRequest->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.student_leave.pending_leave', compact('apply_leaves', 'leave_types', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentLeaveEdit(request $request, $id)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $my_leaves = SmLeaveDefine::where('role_id', $user->role_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $apply_leaves = SmLeaveRequest::where('role_id', $user->role_id)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $leave_types = SmLeaveDefine::where('role_id', $user->role_id)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            } else {
                $my_leaves = SmLeaveDefine::where('role_id', $request->role_id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $apply_leaves = SmLeaveRequest::where('role_id', $request->role_id)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
                $leave_types = SmLeaveDefine::where('role_id', $request->role_id)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            }
            $apply_leave = SmLeaveRequest::find($id);
            return view('backEnd.student_leave.apply_leave', compact('apply_leave', 'apply_leaves', 'leave_types', 'my_leaves'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $request->validate([
            'apply_date' => "required",
            'leave_type' => "required",
            'leave_from' => 'required|before_or_equal:leave_to',
            'leave_to' => "required",
            'file' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png",
        ]);
        try {
            $maxFileSize = SmGeneralSettings::first('file_size')->file_size;
            $file = $request->file('attach_file');
            $fileSize = filesize($file);
            $fileSizeKb = ($fileSize / 1000000);
            if ($fileSizeKb >= $maxFileSize) {
                Toastr::error('Max upload file size ' . $maxFileSize . ' Mb is set in system', 'Failed');
                return redirect()->back();
            }
            $fileName = "";
            if ($request->file('attach_file') != "") {
                $apply_leave = SmLeaveRequest::find($request->id);
                if (file_exists($apply_leave->file)) {
                    unlink($apply_leave->file);
                }

                $file = $request->file('attach_file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/leave_request/', $fileName);
                $fileName = 'public/uploads/leave_request/' . $fileName;
            }

            $user = Auth()->user();

            if ($user) {
                $login_id = $user->id;
                $role_id = $user->role_id;
            } else {
                $login_id = $request->login_id;
                $role_id = $request->role_id;
            }

            $apply_leave = SmLeaveRequest::find($request->id);
            $apply_leave->staff_id = $login_id;
            $apply_leave->role_id = $role_id;
            $apply_leave->apply_date = date('Y-m-d', strtotime($request->apply_date));
            $apply_leave->leave_define_id = $request->leave_type;
            $apply_leave->leave_from = date('Y-m-d', strtotime($request->leave_from));
            $apply_leave->leave_to = date('Y-m-d', strtotime($request->leave_to));
            $apply_leave->approve_status = 'P';
            $apply_leave->reason = $request->reason;
            if ($fileName != "") {
                $apply_leave->file = $fileName;
            }
            $result = $apply_leave->save();
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('student-apply-leave');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function DownlodTimeline($file_name)
    {

        try {
            $file = public_path() . '/uploads/student/timeline/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            } else {
                Toastr::error('File not found', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function DownlodDocument($file_name)
    {

        try {
            $file = public_path() . '/uploads/homework/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function DownlodContent($file_name)
    {
        try {
            $file = public_path() . '/uploads/upload_contents/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function DownlodStudentDocument($file_name)
    {
        try {
            $file = public_path() . '/uploads/student/document/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function downloadHomeWorkContent($id, $student_id)
    {
        try {
            $student = SmStudent::where('id', $student_id)->first();
            if (Auth::user()->role_id == 2) {
                $student = SmStudent::where('user_id', $student_id)->first();
            }
            $hwContent = SmUploadHomeworkContent::where('student_id', $student->id)->where('homework_id', $id)->get();
            // $file_array= json_decode($hwContent->file, true);
            // $files = $file_array;
            // $zipname = 'Homework_Content_'.time().'.zip';
            // $zip = new ZipArchive;
            // $zip->open($zipname, ZipArchive::CREATE);
            //     foreach ($files as $file) {
            //         $zip->addFile($file);
            //     }
            // $zip->close();
            // header('Content-Type: application/zip');
            // header('Content-disposition: attachment; filename='.$zipname);
            // header('Content-Length: ' . filesize($zipname));
            // readfile($zipname);
            // File::delete($zipname);

            $file_paths = [];
            foreach ($hwContent as $key => $files_row) {
                $only_files = json_decode($files_row->file);
                foreach ($only_files as $second_key => $upload_file_path) {
                    $file_paths[] = $upload_file_path;
                }
            }
            $zip_file_name = str_replace(' ', '_', time() . '.zip'); // Name of our archive to download

            $new_file_array = [];
            foreach ($file_paths as $key => $file) {

                $file_name_array = explode('/', $file);
                $file_original = $file_name_array[array_key_last($file_name_array)];
                $new_file_array[$key]['path'] = $file;
                $new_file_array[$key]['name'] = $file_original;

            }
            $public_dir = public_path('uploads/homeworkcontent');
            $zip = new ZipArchive;
            if ($zip->open($public_dir . '/' . $zip_file_name, ZipArchive::CREATE) === true) {
                // Add Multiple file
                foreach ($new_file_array as $key => $file) {
                    $zip->addFile($file['path'], @$file['name']);
                }
                $zip->close();
            }

            $zip_file_url = asset('public/uploads/homeworkcontent/' . $zip_file_name);
            session()->put('homework_zip_file', $zip_file_name);

            return Redirect::to($zip_file_url);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
