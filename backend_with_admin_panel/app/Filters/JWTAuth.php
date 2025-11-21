<?php

namespace App\Filters;

use App\Libraries\JWT;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JWTAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Token must be provided as "Authorization: Bearer <token>"
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return service('response')->setJSON([
                'status'  => 'error',
                'message' => 'Authorization header missing or invalid.'
            ])->setStatusCode(401);
        }

        $token = trim(substr($authHeader, 7));

        $jwt = new JWT();
        $payload = $jwt->decode($token);

        if (!$payload) {
            return service('response')->setJSON([
                'status'  => 'error',
                'message' => 'Invalid or expired token.'
            ])->setStatusCode(401);
        }

        //  Attach user info to the request (accessible in controllers)
        $request->user = (object) $payload;

        // Role check ("auth:admin")
        if ($arguments && isset($arguments[0]) && $arguments[0] === 'admin') {
            if (($payload['role'] ?? '') !== 'admin') {
                return service('response')->setJSON([
                    'status'  => 'error',
                    'message' => 'Admin access required.'
                ])->setStatusCode(403);
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No changes needed
    }
}
