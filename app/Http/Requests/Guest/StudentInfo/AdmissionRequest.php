<?php

namespace App\Http\Requests\Guest\StudentInfo;

use App\SmStudent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // dd($this->id);

        $maxFileSize = generalSetting()->file_size * 1024;
        $student = null;
        if ($this->id) {
            $student = SmStudent::with('parents')->findOrFail($this->id);
        }
        $school_id = 1;
        $academic_id = getAcademicId();

        $rules = [
            'session' => 'required',
            'class' => 'required',
            'admission_number' => ['required', 'nullable', Rule::unique('sm_students', 'admission_no')],
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'middle_name' => 'nullable|max:100',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'religion' => 'required|integer',
            'email_address' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')],
            'blood_group' => 'nullable|integer',
            'photo' => "sometimes|nullable|file|mimes:jpg,jpeg,png|max:" . $maxFileSize,
            'relation_with_student' => 'required_without:parent_id',
            'fathers_name' => 'required_if:relation_with_student,parent,father|max:100',
            'fathers_occupation' => 'required_if:relation_with_student,parent,father|max:100',
            'fathers_phone' => 'required_if:relation_with_student,parent,father|max:100',
            'fathers_email_address' => 'required_if:relation_with_student,parent,father|max:100',
            'mothers_name' => 'required_if:relation_with_student,parent,mother|max:100',
            'mothers_occupation' => 'nullable|max:100',
            'mothers_phone' => 'required_if:relation_with_student,parent,mother|max:100',
            'mothers_email_address' => 'required_if:relation_with_student,parent,mother|max:100',
            'guardians_name' => 'required_if:relation_with_student,guardian|max:100',
            'relation' => 'nullable',
            'guardians_email' => "required_if:relation_with_student,guardian",
            'guardians_phone' => 'required_if:relation_with_student,guardian|max:100',
            'guardians_occupation' => 'required_if:relation_with_student,guardian|max:100',
            'guardians_address' => 'required_if:relation_with_student,guardian|max:200',
            'emergency_contact' => 'required_without:parent_id|max:200',
            'permission' => 'nullable',
            'current_address' => 'required|max:200',
            'permanent_address' => 'required|max:200',
            'doctor_name' => 'nullable',
            'doctor_phone' => 'nullable',
            'family_health_ins' => 'nullable',
            'health_condition' => 'required',
            'prescribed_medication' => 'required',
            'allowed_medical' => 'required_if:prescribed_medication,p_yes',
            'condition_info' => 'nullable',
            'route' => 'nullable|integer',
            'vehicle' => 'nullable|integer',
            'landmark' => 'nullable',
            'terms' => 'required',
        ];

        //added by abu nayem lead id number check replace of roll number

        if (moduleStatusCheck('Lead') == true) {
            $rules['roll_number'] = ['sometimes', 'nullable', Rule::unique('sm_students', 'roll_no')->ignore(optional($student)->id)->where('school_id', $school_id)->where('academic_id', $academic_id)->where('class_id', $this->class_id)];

            $rules['phone_number'] = ['sometimes', 'nullable', Rule::unique('sm_students', 'mobile')->ignore(optional($student)->id)];

        }

        return $rules;
    }
    public function attributes()
    {

        $attributes = [
            'session' => 'Academic',
        ];
        if (moduleStatusCheck('Lead') == true) {
            $attributes['roll_number'] = 'ID Number';
        }
        return $attributes;
    }
}
