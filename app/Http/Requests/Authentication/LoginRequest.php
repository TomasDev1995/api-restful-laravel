<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use App\Contracts\ValidatesRequest;
use App\Contracts\ValidatesRequestInterface;

class LoginRequest extends FormRequest implements ValidatesRequestInterface
{
    public function authorize()
    {
        return true; // Cambia esto si necesitas autorización adicional
    }

    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}
