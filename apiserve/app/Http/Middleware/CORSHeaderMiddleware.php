<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

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
            return $this->addHeaders(new Response('', 204), true);
        }
        /** @var Response $response */
        $response = $next($request);

        return $this->addHeaders($response);
    }

    protected function addHeaders($response, $preflight = false)
    {
        $headers = [
            'Access-Control-Allow-Origin' => 'http://w.tera.jp:4200',
            // server side credencial support eg. cookies
            'Access-Control-Allow-Credentials' => 'true'
        ];
        if ( $preflight )
        {
            $headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, x-xsrf-token';
            $headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
        }
        $response->headers->add($headers);
        return $response;
    }
}
