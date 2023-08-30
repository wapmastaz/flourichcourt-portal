<?php

namespace App\Http\Requests\Admin\StudentInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmStudentCategoryRequest extends FormRequest
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
        $school_id = auth()->user()->school_id;
        $academicYears = academicYears();
        return [
            'category' => ['required', Rule::unique('sm_student_categories', 'category_name')->where('academic_id', $academicYears->id)->ignore($this->id)],
        ];
    }

}
