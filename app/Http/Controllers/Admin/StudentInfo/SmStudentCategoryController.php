<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentInfo\SmStudentCategoryRequest;
use App\SmStudentCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmStudentCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try {
            $student_types = SmStudentCategory::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)
                ->get();
            return view('backEnd.studentInformation.student_category', compact('student_types'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function store(SmStudentCategoryRequest $request)
    {
        try {
            $student_type = new SmStudentCategory();
            $student_type->category_name = $request->category;
            $student_type->school_id = Auth::user()->school_id;
            $student_type->academic_id = getAcademicId();
            $student_type->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $student_type = SmStudentCategory::find($id);
            $student_types = SmStudentCategory::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)
                ->get();
            return view('backEnd.studentInformation.student_category', compact('student_types', 'student_type'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function update(SmStudentCategoryRequest $request)
    {
        try {
            $student_type = SmStudentCategory::find($request->id);
            $student_type->category_name = $request->category;
            $student_type->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('student-category');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $tables = \App\tableList::getTableList('student_category_id', $id);
            try {
                if ($tables == null) {
                    SmStudentCategory::find($id)->delete();
                } else {
                    $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                    Toastr::error($msg, 'Failed');
                    return redirect()->back();
                }
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } catch (\Illuminate\Database\QueryException $e) {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
