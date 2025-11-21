<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Libraries\JWT;
use CodeIgniter\API\ResponseTrait;


class Orders extends BaseController
{
    use ResponseTrait;
    protected $orderModel;
    protected $orderItemModel;
    protected $productModel;
    protected $jwt;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel = new ProductModel();
        $this->jwt = new JWT();
    }

    // -----------------------------------------------------------
    // 🔒 AUTH CHECK — extracts email from token
    // -----------------------------------------------------------
    private function checkAuth()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($authHeader, 7));

        try {
            return $this->jwt->decode($token);
        } catch (\Exception $e) {
            return null;
        }
    }

    // -----------------------------------------------------------
    // 🟩 GET USER ID FROM EMAIL IN TOKEN
    // -----------------------------------------------------------
    private function getUserIdFromEmail($email)
    {
        $db = \Config\Database::connect();
        $row = $db->table('users')->where('email', $email)->get()->getRow();
        return $row ? $row->id : null;
    }

    // -----------------------------------------------------------
    // 🟦 POST /api/orders  → Place Order
    // -----------------------------------------------------------
    public function create()
    {
        // Step 1 — Validate Token
        $auth = $this->checkAuth();
        if (!$auth || empty($auth->email)) {
            return $this->failUnauthorized("Missing or invalid token");
        }

        // Step 2 — Convert email -> user_id
        $user_id = $this->getUserIdFromEmail($auth->email);
        if (!$user_id) {
            return $this->failUnauthorized("User not found");
        }

        // Step 3 — Validate Request Body
        $payload = $this->request->getJSON(true);

        if (!isset($payload['items']) || !is_array($payload['items']) || count($payload['items']) === 0) {
            return $this->failValidationErrors("Order items are required");
        }

        $total_price = $payload['total_price'] ?? 0;

        // Step 4 — Insert Order
        $orderData = [
            'user_id'     => $user_id,
            'total_price' => $total_price,
            'status'      => 'pending',
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $order_id = $this->orderModel->insert($orderData);

        if (!$order_id) {
            return $this->fail("Failed to create order");
        }

        // Step 5 — Insert Order Items
        foreach ($payload['items'] as $item) {
            $this->orderItemModel->insert([
                'order_id'   => $order_id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Order placed successfully',
            'order_id' => $order_id
        ], 200);
    }

    // -----------------------------------------------------------
    // 🟧 GET /api/orders → List all user orders
    // -----------------------------------------------------------
    public function index()
    {
        $auth = $this->checkAuth();
        if (!$auth || empty($auth->email)) {
            return $this->failUnauthorized("Invalid token");
        }

        $user_id = $this->getUserIdFromEmail($auth->email);

        $orders = $this->orderModel
            ->where('user_id', $user_id)
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $orders
        ]);
    }

    // -----------------------------------------------------------
    // 🟨 GET /api/orders/{id} → Order details with items
    // -----------------------------------------------------------
    public function show($id = null)
    {
        $auth = $this->checkAuth();
        if (!$auth) {
            return $this->failUnauthorized("Invalid token");
        }

        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->failNotFound("Order not found");
        }

        $items = $this->orderItemModel->where('order_id', $id)->findAll();

        return $this->respond([
            'status' => 'success',
            'order'  => $order,
            'items'  => $items
        ]);
    }
}
