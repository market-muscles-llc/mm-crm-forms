<?php

namespace App\Http\Controllers\Auth;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class OneTimePasswordController extends LoginController
{
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'external_user_id' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $passwordAttempt = Cache::get(key: "{$request->get('external_user_id')}-one-time-password");

        if (!is_array($passwordAttempt)) {
            return false;
        }

        // Does the code match the request?
        if ($request->get('code') !== Arr::get($passwordAttempt, 'code')) {
            // Bad! Forget the code and reject
            $this->forgetOtp($request->get('external_user_id'));

            return false;
        }

        $workspace = Workspace::query()->with('owners')->findOrFail(Arr::get($passwordAttempt, 'workspace_id'));

        $token = $this->guard()->login($workspace->owners->first());

        if (! $token) {
            return false;
        }

        $this->forgetOtp($request->get('external_user_id'));

        $this->guard()->setToken($token);

        return true;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            'code' => $request->code,
        ];
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @return void
     */
    protected function forgetOtp(string $id): void
    {
        Cache::forget(
            key: "{$id}-one-time-password",
        );
    }
}
