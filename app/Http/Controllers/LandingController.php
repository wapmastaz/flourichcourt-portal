<?php

namespace App\Http\Controllers;

use App\SmAcademicYear;
use App\SmBaseSetup;
use App\SmClass;
use App\SmDormitoryList;
use App\SmParent;
use App\SmRoute;
use App\SmStaff;
use App\SmStudent;
use App\SmStudentCategory;
use App\SmStudentGroup;
use App\SmVehicle;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM')->except(['admission', 'admissionSuccess']);
        // User::checkAuth();
    }
    public function landing()
    {

        try {
            return view('frontEnd.landing.index');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function index()
    {

        try {
            return view('frontEnd.landing.index');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // Admission
    public function admission()
    {
        // try {
        $data = $this->loadData();
        $data['max_admission_id'] = SmStudent::max('admission_no');
        return view('frontEnd.admission.index', $data);
        // } catch (\Exception$e) {
        //     Toastr::error('Operation Failed', 'Failed');
        //     return redirect()->back();
        // }
    }

    public function admissionSuccess(Request $request)
    {

        $student = SmStudent::where('user_id', $request->student)->first();
        return view("frontEnd.admission.success", compact('student'));

    }

    public static function loadData()
    {
        $data['classes'] = SmClass::get(['id', 'class_name']);
        $data['religions'] = SmBaseSetup::where('base_group_id', '=', '2')->get(['id', 'base_setup_name']);
        $data['blood_groups'] = SmBaseSetup::where('base_group_id', '=', '3')->get(['id', 'base_setup_name']);
        $data['genders'] = SmBaseSetup::where('base_group_id', '=', '1')->get(['id', 'base_setup_name']);
        $data['route_lists'] = SmRoute::get(['id', 'title']);
        $data['dormitory_lists'] = SmDormitoryList::get(['id', 'dormitory_name']);
        $data['categories'] = SmStudentCategory::get(['id', 'category_name']);
        $data['groups'] = SmStudentGroup::get(['id', 'group']);
        $data['sessions'] = SmAcademicYear::get(['id', 'year', 'title', 'starting_date', 'ending_date']);
        $data['driver_lists'] = SmStaff::where([['active_status', '=', '1'], ['role_id', 9]])->get();
        $data['vehicles'] = SmVehicle::get();
        return $data;
    }

    public function admissionProgress(Request $request)
    {
        if (Auth::check()) {
            $parent = SmParent::where('user_id', Auth::user()->id)->first();
            // check if parent has child that has ongoing admission
            $student = SmStudent::where('parent_id', $parent->id)->first();
            return view("frontEnd.admission.admission-progress", compact('student', 'parent'));
        }

    }
}
