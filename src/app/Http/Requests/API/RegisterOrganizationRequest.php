<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterOrganizationRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'phone_number' => 'required|string',

            'sei' => 'required|string',
            'mei' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '組織名が指定されていません',
            'name.string' => '組織名は文字列でなければなりません',
            'phone_number.required' => '電話番号が指定されていません',
            'phone_number.string' => '電話番号は文字列でなければなりません',
            'sei.required' => '姓が指定されていません',
            'sei.string' => '姓は文字列でなければなりません',
            'mei.required' => '名が指定されていません',
            'mei.string' => '名は文字列でなければなりません',
            'email.required' => 'メールアドレスが指定されていません',
            'email.email' => 'メールアドレスは正しい形式でなければなりません',
            'password.required' => 'パスワードが指定されていません',
            'password.string' => 'パスワードは文字列でなければなりません',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 404);

        throw new HttpResponseException($response);
    }

    /**
     * Handle a failed authorization attempt.
     *
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Unauthorized'
        ], 401));
    }
}