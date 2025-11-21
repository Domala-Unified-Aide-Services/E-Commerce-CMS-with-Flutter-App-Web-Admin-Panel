<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if ($session->get('is_admin')) {
            return; // allow
        }

        // Detect AJAX (X-Requested-With) OR CI's isAJAX
        $isAjax = false;
        if (method_exists($request, 'isAJAX')) {
            $isAjax = $request->isAJAX();
        }
        if (!$isAjax && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }

        if ($isAjax) {
            $response = service('response');
            return $response->setJSON(['error' => 'unauthenticated', 'message' => 'Authentication required'])->setStatusCode(401);
        }

        // Not Ajax: redirect to login page
        return redirect()->to(base_url('admin/login'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing to do here
    }
}
