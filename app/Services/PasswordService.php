<?php

namespace App\Services;


use App\Services\Concerns\BaseService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordService extends BaseService
{
    /**
     * Send email include with password reset link
     */
    public function sendEmail($request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT;
    }

    /**
     * Reset password
     */
    public function reset($request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
                $user->save();
            }
        );
        return $status === Password::PASSWORD_RESET;
    }
}
