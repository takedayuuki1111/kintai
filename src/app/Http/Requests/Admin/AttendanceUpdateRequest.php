<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => ['nullable', 'integer'],
            'month' => ['nullable', 'integer'],
            'day' => ['nullable', 'integer'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'rests' => ['nullable', 'array'],
            'rests.*.start_time' => ['nullable', 'date_format:H:i'],
            'rests.*.end_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時刻を入力してください。',
            'start_time.date_format' => '出勤時刻の形式が正しくありません。',
            'end_time.date_format' => '退勤時刻の形式が正しくありません。',
            'rests.*.start_time.date_format' => '休憩開始時刻の形式が正しくありません。',
            'rests.*.end_time.date_format' => '休憩終了時刻の形式が正しくありません。',
        ];
    }
}
