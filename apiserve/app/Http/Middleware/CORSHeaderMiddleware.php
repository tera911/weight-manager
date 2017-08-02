<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class CORSHeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->isMethod('OPTIONS')){
            return $this->addHeaders($request, new Response('', 204), true);
        }
        /** @var Response $response */
        $response = $next($request);

        return $this->addHeaders($request, $response);
    }

    protected function addHeaders(Request $request, $response, $preflight = false)
    {
        $headers = [
            'Access-Control-Allow-Origin' => $request->headers->get('Origin'),
            // server side credencial support eg. cookies
            'Access-Control-Allow-Credentials' => 'true',
        ];
        if ( $preflight )
        {
            $headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, x-xsrf-token, X-CSRF-Token';
            $headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
        }
        $response->headers->add($headers);
        return $response;
    }
}
