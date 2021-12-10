<?php

namespace App\Http\Middleware;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\User;
use Closure;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($request->hasHeader('Authorization')) {
            $user = User::where('twitch_token', $request->bearerToken())->first();
            if(!$user) {
                throw new ValidationException('Invalid data!');
            }
            $currentDate = new \DateTime();
            $userDate = new \DateTime($user->created_at);
            $diff = $userDate->diff($currentDate);
//            if($diff->h >=1) {
//                throw new ValidationException('Expired Token!');
//            }
        } else {
            throw new ValidationException('Missing Authorization!');
        }
        return $next($request);
    }
}
