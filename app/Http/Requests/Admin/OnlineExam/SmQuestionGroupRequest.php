<?php

namespace App\Http\Requests\Admin\OnlineExam;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmQuestionGroupRequest extends FormRequest
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

    public function rules()
    {
        return [
            'title' => ['required', Rule::unique('sm_question_groups', 'title')->where(function ($query) {
                return $query->where('academic_id', getAcademicId());
            })],
            // |unique:sm_question_groups,title,academic_id," . $this->id,
        ];
    }
}
