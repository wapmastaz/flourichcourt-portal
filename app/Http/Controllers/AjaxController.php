<?php

namespace App\Http\Controllers;

use App\SmAssignVehicle;
use App\SmClass;
use App\SmClassSection;
use App\SmSection;
use App\SmStudent;
use App\SmVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    public function ajaxGetClass(Request $request)
    {
        $classes = SmClass::where('academic_id', 'LIKE', $request->year . '%')->get();

        $sections = SmSection::where('academic_id', 'LIKE', $request->year . '%')->get();

        return response()->json(['classes' => $classes, 'sections' => $sections]);
    }

    public function ajaxGetSection(Request $request)
    {
        $sectionIds = SmClassSection::where('class_id', '=', $request->id)
            ->where('school_id', 1)
            ->get();
        $sections = [];
        foreach ($sectionIds as $sectionId) {
            $sections[] = SmSection::where('id', $sectionId->section_id)->select('id', 'section_name')->first();
        }
        return response()->json([$sections]);
    }

    public function ajaxGetVehicle(Request $request)
    {
        try {
            $school_id = 1;
            if (Auth::check()) {
                $school_id = Auth::user()->school_id;
            } else if (app()->bound('school')) {
                $school_id = app('school')->id;
            }
            $vehicle_detail = SmAssignVehicle::where('route_id', $request->id)->where('school_id', $school_id)->first();
            $vehicles = explode(',', $vehicle_detail->vehicle_id);
            $vehicle_info = [];
            foreach ($vehicles as $vehicle) {
                $vehicle_info[] = SmVehicle::find($vehicle[0]);
            }
            return response()->json([$vehicle_info]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxSectionSibling(Request $request)
    {
        try {
            $sectionIds = SmClassSection::where('class_id', '=', $request->id)
                ->where('academic_id', $request->academic_year)
            // ->where('school_id', Auth::user()->school_id)
                ->get();

            $sibling_sections = [];
            foreach ($sectionIds as $sectionId) {
                $sibling_sections[] = SmSection::find($sectionId->section_id);
            }
            return response()->json([$sibling_sections]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxSiblingInfo(Request $request)
    {

        // return $request->all();

        try {

            $siblings = SmStudent::query();

            if ($request->id != "") {
                $siblings->where('id', '!=', $request->id);
            }
            $siblings = $siblings->where('active_status', 1)
                ->where('status', 1)
                ->where('academic_id', $request->academic_year)
                ->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->get();

            return response()->json($siblings);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxSiblingInfoDetail(Request $request)
    {
        try {
            $sibling_detail = SmStudent::find($request->id);
            $parent_detail = $sibling_detail->parents;
            return response()->json([$sibling_detail, $parent_detail]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }
}
