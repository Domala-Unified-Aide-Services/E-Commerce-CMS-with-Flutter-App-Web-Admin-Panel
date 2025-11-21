<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (! $authHeader || ! preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['error' => 'Missing or malformed Authorization header']);
        }

        $token = $matches[1];
        $key = getenv('JWT_SECRET') ?: (defined('JWT_SECRET') ? JWT_SECRET : null);
        if (empty($key)) {
            return Services::response()
                ->setStatusCode(500)
                ->setJSON(['error' => 'Server configuration error: JWT secret not set']);
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // attach decoded token to request for controllers (compatible approach)
            $request->user = $decoded;
            return null;
        } catch (\Exception $e) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid token', 'message' => $e->getMessage()]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing to do after
    }
}
