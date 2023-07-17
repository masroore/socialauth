<?php

namespace Masroore\SocialAuth\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Masroore\SocialAuth\RedirectPath;

class LoginResponse implements Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     */
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended(RedirectPath::for('login', 'login'));
    }
}
