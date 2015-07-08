<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

abstract class Request extends FormRequest
{
    public function validator($factory)
    {
        $validator = $factory->make(
            $this->all(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
        );

        if (method_exists($this, 'moreValidation')) {
            $validator->after(function ($validator) {
                $this->moreValidation($validator, $this);
            });
        }

        return $validator;
    }
}
