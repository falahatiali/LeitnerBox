<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EmailVerificationRequest extends FormRequest
{
    public User $user;

    public function authorize()
    {
        $this->user = User::findOrFail($this->route('id'));

        if (!hash_equals((string) $this->user->getKey(), (string)$this->route('id'))) {
            return false;
        }

        if (!hash_equals(sha1($this->user->getEmailForVerification()), (string)$this->route('hash'))) {
            return false;
        }

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
            //
        ];
    }

    public function fulfill()
    {
        if (!$this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();

            event(new Verified($this->user));

            return true;
        }

        return false;
    }

    public function withValidator(Validator $validator)
    {
        return $validator;
    }
}
