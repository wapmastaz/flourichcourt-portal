<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guest\StudentInfo\AdmissionRequest;
use App\Models\User;
use App\SmAcademicYear;
use App\SmNotification;
use App\SmParent;
use App\SmSection;
use App\SmStudent;
use App\SmVehicle;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(AdmissionRequest $request)
    {

        // dd($request->all());

        $destination = 'public/uploads/student/document/';
        $student_file_destination = 'public/uploads/student/';

        // if ($request->relation == 'Father') {
        //     $guardians_photo = fileUpload($request->file('fathers_photo'), $student_file_destination);
        // } elseif ($request->relation == 'Mother') {
        //     $guardians_photo = fileUpload($request->file('mothers_photo'), $student_file_destination);
        // } elseif ($request->relation == 'Other') {
        //     $guardians_photo = fileUpload($request->file('guardians_photo'), $student_file_destination);
        // }

        DB::beginTransaction();
        $academic_year = SmAcademicYear::find($request->session);
        $section = SmSection::where('academic_id', $academic_year->id)
            ->where('section_name', 'admission')
            ->orWhere('section_name', 'Admission')
            ->first();

        // dd($section);

        $studentPassword = 123456; //uniqid();
        $parentPassword = 123456; //uniqid();

        $user_stu = new User();
        $user_stu->role_id = 2;
        $user_stu->full_name = $request->first_name . ' ' . $request->last_name . ' ' . $request->middle_name;
        $user_stu->username = $request->admission_number;
        $user_stu->email = $request->email_address;
        $user_stu->password = Hash::make(trim($studentPassword));
        $user_stu->school_id = 1;
        $user_stu->created_at = $academic_year->year . '-01-01 12:00:00';
        $user_stu->save();
        $user_stu->toArray();

        if ($request->parent_id == "") {

            switch ($request->relation_with_student) {
                case 'father':
                    $parentFullName = $request->fathers_name;
                    $parentEmail = $request->fathers_email_address;
                    break;
                case 'mother':
                    $parentFullName = $request->mothers_name;
                    $parentEmail = $request->mothers_email_address;
                    break;
                case 'guardian':
                    $parentFullName = $request->guardian_name;
                    $parentEmail = $request->guardians_email;
                    break;
                default:
                    $parentFullName = $request->fathers_name;
                    $parentEmail = $request->fathers_email_address;
                    break;
            }
            $user_parent = new User();
            $user_parent->role_id = 3;
            $user_parent->full_name = $parentFullName;
            $user_parent->email = $parentEmail;
            $user_parent->password = Hash::make(trim($parentPassword));
            $user_parent->school_id = 1;
            $user_parent->created_at = $academic_year->year . '-01-01 12:00:00';
            $user_parent->save();
            $user_parent->toArray();

            // if (!empty($request->guardians_email)) {
            //     $data_parent['email'] = $request->guardians_email;
            //     $user_parent->username = $request->guardians_email;
            // }

            $parent = new SmParent();
            $parent->user_id = $user_parent->id;
            $parent->fathers_name = $request->fathers_name;
            $parent->fathers_mobile = $request->fathers_phone;
            $parent->fathers_occupation = $request->fathers_occupation;
            $parent->fathers_email_address = $request->fathers_email_address;
            $parent->fathers_photo = $request->file('fathers_photo') ? fileUpload($request->file('fathers_photo'), $student_file_destination) : null;

            $parent->mothers_name = $request->mothers_name;
            $parent->mothers_mobile = $request->mothers_phone;
            $parent->mothers_occupation = $request->mothers_occupation;
            $parent->mothers_email_address = $request->mothers_email_address;
            $parent->mothers_photo = $request->file('mothers_photo') ? fileUpload($request->file('mothers_photo'), $student_file_destination) : null;

            $parent->guardians_name = $request->guardians_name;
            $parent->guardians_mobile = $request->guardians_phone;
            $parent->guardians_email = $request->guardians_email;
            $parent->guardians_occupation = $request->guardians_occupation;
            $parent->guardians_relation = $request->relation;
            $parent->relation = $request->relationButton;

            $parent->guardians_photo = null;
            $parent->guardians_address = $request->guardians_address;
            $parent->is_guardian = $request->is_guardian;
            $parent->school_id = 1;

            $parent->academic_id = $request->session;
            $parent->created_at = $academic_year->year . '-01-01 12:00:00';

            $parent->save();
            // dd($parent);
            $parent->toArray();
            // dd($parent);
        } else {
            $parent = SmParent::find($request->parent_id);
        }

        $student = new SmStudent();
        $student->class_id = $request->class;
        $student->session_id = $request->session;
        $student->user_id = $user_stu->id;
        $student->parent_id = $request->parent_id == "" ? $parent->id : $request->parent_id;
        $student->role_id = 2;
        $student->admission_no = $request->admission_number;
        $student->roll_no = $request->roll_number;
        $student->first_name = $request->first_name;
        $student->last_name = $request->last_name;
        $student->middle_name = $request->middle_name;
        $student->full_name = $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name;
        $student->gender_id = $request->gender;
        $student->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
        $student->caste = null;
        $student->email = $request->email_address;
        $student->mobile = $request->phone_number;
        $student->admission_date = date('Y-m-d', strtotime(date('Y-m-d')));
        $student->student_photo = fileUpload($request->photo, $student_file_destination);
        $student->bloodgroup_id = $request->blood_group;
        $student->religion_id = $request->religion;
        $student->height = null;
        $student->weight = null;
        $student->current_address = $request->current_address;
        $student->permanent_address = $request->permanent_address;
        $student->route_list_id = $request->route;
        $student->dormitory_id = null;
        $student->room_id = null;
        $student->section_id = ($section) ? $section->id : '';
        if (!empty($request->vehicle)) {
            $driver = SmVehicle::where('id', '=', $request->vehicle)
                ->select('driver_id')
                ->first();
            if (!empty($driver)) {
                $student->vechile_id = $request->vehicle;
                $student->driver_id = $driver->driver_id;
            }
        }

        $student->national_id_no = null;
        $student->local_id_no = null;
        $student->bank_account_no = null;
        $student->bank_name = null;
        $student->previous_school_details = null;
        $student->aditional_notes = null;
        $student->ifsc_code = null;
        $student->document_title_1 = null;
        $student->school_id = 1;
        $student->academic_id = $request->session;
        $student->created_at = $academic_year->year . '-01-01 12:00:00';

        $student->doctor_name = $request->doctor_name;
        $student->doctor_phone = $request->doctor_phone;
        $student->family_health_ins = $request->family_health_ins;
        $student->health_condition = $request->health_condition;
        $student->prescribed_medication = ($request->prescribed_medication == "p_yes") ? true : false;
        $student->allowed_medical = ($request->allowed_medical == "a_yes") ? true : false;
        $student->condition_info = $request->condition_info;
        $student->landmark = $request->landmark;

        if ($request->customF) {
            $dataImage = $request->customF;
            foreach ($dataImage as $label => $field) {
                if (is_object($field) && $field != "") {
                    $dataImage[$label] = fileUpload($field, 'public/uploads/customFields/');
                }
            }

            //Custom Field Start
            $student->custom_field_form_name = "student_registration";
            $student->custom_field = json_encode($dataImage, true);
            //Custom Field End
        }
        //add by abu nayem for lead convert to student
        if (moduleStatusCheck('Lead') == true) {
            $student->lead_id = $request->lead_id;
        }
        //end lead convert to student

        $student->save();
        $student->toArray();

        if ($student) {
            $compact['user_email'] = $request->email_address;
            $compact['slug'] = 'student';
            $compact['id'] = $student->id;
            $compact['password'] = $studentPassword;
            @send_mail($request->email_address, $request->first_name . ' ' . $request->last_name, "student_login_credentials", $compact);
            //@send_sms($request->phone_number, 'student_admission', $compact);
        }

        if ($parent && $request->parent_id == "") {
            // [student_name], [father_name], [school_name], [username], [password]
            $compact['slug'] = 'parent';
            $compact['id'] = $parent->id;
            $compact['password'] = $parentPassword;
            $compact['relation'] = $request->relation_with_student;
            switch ($request->relation_with_student) {

                case 'parent':
                    $compact['user_email'] = $request->fathers_email_address;
                    @send_mail($parent->fathers_email_address, $request->fathers_name, "parent_login_credentials", $compact);
                    @send_mail($parent->mothers_email_address, $request->mothers_name, "parent_login_credentials", $compact);
                    break;
                case 'father':
                    $compact['user_email'] = $request->fathers_email_address;
                    @send_mail($request->fathers_email_address, $request->fathers_name, "parent_login_credentials", $compact);
                    break;
                case 'mother':
                    $compact['user_email'] = $request->mothers_email_address;
                    @send_mail($request->mothers_email_address, $request->mothers_name, "parent_login_credentials", $compact);
                    break;
                case 'guardian':
                    $compact['user_email'] = $request->guardians_email;
                    @send_mail($request->guardians_email, $request->guardian_name, "parent_login_credentials", $compact);
                    break;
                default:
                    # code...
                    break;
            }
        }

        // send email to admin
        $body['content'] = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Blanditiis iusto possimus minus, cum quasi veniam minima amet, repellendus reiciendis, cupiditate nulla numquam quaerat non cumque nemo eum perferendis ut? Commodi.";
        $body['slug'] = 'admin';

        @send_mail("info@florishcourt.com", "Admin", "admission_request", $body);
        // send notification
        $notification = new SmNotification;
        $notification->user_id = 1;
        $notification->role_id = 1;
        $notification->date = date('Y-m-d');
        $notification->message = 'New Admission Request';
        $notification->url = 'student-admissions-list';
        $notification->school_id = 1;
        $notification->academic_id = getAcademicId();
        $notification->save();

        //add by abu nayem for lead convert to student
        if (moduleStatusCheck('Lead') == true && $request->lead_id) {
            $lead = \Modules\Lead\Entities\Lead::find($request->lead_id);
            $lead->class_id = $request->class;
            $lead->section_id = $request->section;
            $lead->save();
        }
        //end lead convert to student
        DB::commit();
        if (moduleStatusCheck('Lead') == true && $request->lead_id) {
            Toastr::success('Admission Application Submitted Successfully, Please Check Your Mail For Next Step', 'Success');
            return redirect()->route('admission.success', [
                'student' => $user_stu,
            ]);
        } else {
            Toastr::success('Admission Application Submitted Successfully, Please Check Your Mail For Next Step', 'Success');
            return redirect()->route('admission.success', [
                'student' => $user_stu,
            ]);
        }
        // try {

        // } catch (\Exception $e) {
        //     DB::rollback();

        //     Toastr::error('Operation Failed', 'Failed');
        //     return redirect()->back();
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
