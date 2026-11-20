<?php

namespace App\Http\Responses;

use App\Http\Responses\Concerns\RedirectsToCurrentTenant;
use Illuminate\Http\JsonResponse;
use Laravel\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasskeyLoginResponse implements PasskeyLoginResponseContract
{
    use RedirectsToCurrentTenant;

    public function toResponse($request): Response
    {
        $redirect = $this->redirectPathForCurrentTenant($request);

        return $request->wantsJson()
            ? new JsonResponse(['redirect' => $redirect], 200)
            : redirect()->intended($redirect);
    }
}
