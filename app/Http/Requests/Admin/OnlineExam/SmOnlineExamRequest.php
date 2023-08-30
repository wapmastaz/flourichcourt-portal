<?php

namespace App\Http\Requests\Admin\OnlineExam;

use Illuminate\Foundation\Http\FormRequest;

class SmOnlineExamRequest extends FormRequest
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

        $rules = [
            'title' => 'required',
            'class' => 'required',
            'section' => 'required_if:category_type,student',
            'subject' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'percentage' => 'required',
            'instruction' => 'required',
            'category_type' => 'required',
        ];
        if ($this->id) {
            $rules['section'] = 'required_if:category_type,student';
        }
        return $rules;
    }
}
