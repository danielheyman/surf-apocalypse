<?php

namespace App\Http\Requests;

use Auth;

class LoginRequest extends Request
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
            'email' => 'required', 'password' => 'required',
        ];
    }

    public function moreValidation($validator, $request)
    {
        if (Auth::validate($request->only('email', 'password'))) {
            return;
        }

        $validator->errors()->add('global', 'These credentials do not match any records.');
    }
}
