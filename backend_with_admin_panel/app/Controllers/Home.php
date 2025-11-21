<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;

class Home extends BaseController
{
    public function index()
    {
        return view('index');
    }

    // 🔹 Display all products
    public function products()
    {
        $productModel = new ProductModel();
        $data['products'] = $productModel->orderBy('id', 'DESC')->findAll();
        return view('products_list', $data);
    }

    // 🔹 Display all categories
    public function categories()
    {
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->orderBy('id', 'ASC')->findAll();
        return view('categories_list', $data);
    }

    // 🔹 Display all orders
    public function orders()
    {
        $orderModel = new OrderModel();
        $data['orders'] = $orderModel->orderBy('created_at', 'DESC')->findAll();
        return view('orders_list', $data);
    }

    // 🔹 API endpoint to add a new order (No HTML form)
    public function addOrderAPI()
    {
        $orderModel = new OrderModel();
        $json = $this->request->getJSON(true); // read JSON body

        if (!$json || empty($json['user_id']) || empty($json['total_price'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing required fields: user_id, total_price.'
            ])->setStatusCode(400);
        }

        $data = [
            'user_id'     => $json['user_id'],
            'total_price' => $json['total_price'],
            'status'      => 'pending',
            'created_at'  => date('Y-m-d H:i:s')
        ];

        $orderModel->insert($data);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Order added successfully!',
            'order_id' => $orderModel->getInsertID()
        ]);
    }
}
