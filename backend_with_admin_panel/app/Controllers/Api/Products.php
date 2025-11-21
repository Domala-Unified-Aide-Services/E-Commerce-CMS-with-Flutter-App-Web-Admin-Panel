<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Libraries\JWT;
use CodeIgniter\HTTP\ResponseInterface;

class Products extends BaseController
{
    protected $productModel;
    protected $jwt;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->jwt = new JWT();
    }

    // ✅ Token validation function
    private function checkAuth()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader) {
            // Return a proper Response object on auth failure
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Missing Authorization header'
                ]);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = $this->jwt->validate($token);

        if (!$decoded) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid or expired token'
                ]);
        }

        // On success return the decoded payload (could be stdClass or array)
        return $decoded;
    }

    // ✅ Protected route: get all products
    public function index()
    {
        $auth = $this->checkAuth();
        // If checkAuth returned a Response (i.e. an error), forward it.
        if ($auth instanceof ResponseInterface) {
            return $auth;
        }

        $products = $this->productModel->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $products
        ]);
    }

    // ✅ Protected route: create a new product
    public function create()
    {
        $auth = $this->checkAuth();
        if ($auth instanceof ResponseInterface) {
            return $auth;
        }

        // Read JSON payload as associative array if possible
        $data = $this->request->getJSON(true);

        // Validate payload is an array
        if (!is_array($data) || empty($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid or empty JSON payload'
                ]);
        }

        // Optional: ensure numeric types are well-formed
        if (isset($data['price'])) {
            $data['price'] = is_numeric($data['price']) ? (float)$data['price'] : $data['price'];
        }
        if (isset($data['category_id'])) {
            $data['category_id'] = is_numeric($data['category_id']) ? (int)$data['category_id'] : $data['category_id'];
        }
        if (isset($data['stock'])) {
            $data['stock'] = is_numeric($data['stock']) ? (int)$data['stock'] : $data['stock'];
        }

        try {
            $this->productModel->insert($data);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Product added successfully'
            ]);
        } catch (\Throwable $e) {
            // Return a friendly JSON error (no stack trace)
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Server error while inserting product: ' . $e->getMessage()
                ]);
        }
    }
}
