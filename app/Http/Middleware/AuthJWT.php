<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header();
        

        if (isset($token['authorization'])) {

            $bareToken = substr($token['authorization'][0], 7);
            $parts = explode('.', $bareToken);
            $payload = json_decode(base64_decode($parts[1]));
            $now = time();
            $signature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], env('JWT_SECRET'), true);
            $computedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            if ($computedBase64UrlSignature != $parts[2]) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'token invalid'
                ],498);

            }
            if ($now < $payload->exp) {
                return $next($request);

            } else {
                $id = $payload->id;

                $user = User::find($id);
                if ($now < $user->token_expired) {
                    return $next($request);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'refresh token expire, please login'
                    ],401);
                }
            }
        } else {

            return response()->json([
                'status' => 'unauthorized',
                'message' => 'please login'
            ],401);
        }
    }
}
