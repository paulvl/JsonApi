<?php

namespace PaulVL\JsonApi\Middleware;

use Closure;

class AccessControlAllow {

    protected $allowed_origin = 'http://localhost:8000';

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: '. $this->allowed_origin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With, *');
        header('Access-Control-Allow-Credentials: true');

        if (!$request->isMethod('options')) {
                return $next($request);
        }
    }

}