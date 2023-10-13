<?php

namespace App\Http\Requests\Admin\Kitchen;

use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Password;

class CreateKitchenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->can('create', Kitchen::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'language' => 'required|in:en,nl',
        ];
    }

    public function commit(){
        $kitchen = new Kitchen;
        $user = new User;
        $user->email = $this->input('email');
        $user->name = $this->input('name');
        $user->language = $this->input('language');
        $user->password = '';
        $kitchen->note = $this->note;
        $kitchen->save();
        $kitchen->user()->save($user);
        Password::broker()->sendResetLink(
            ['email' => $user->email]
        );

        return [
            'id' => $kitchen->id,
            'name' => $this->input('name'),
            'email' => $this->input('email')
        ];
    }
}
