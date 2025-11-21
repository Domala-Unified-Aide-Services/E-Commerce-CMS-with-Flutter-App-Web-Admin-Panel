<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;

class Orders extends BaseController
{
    protected $orderModel;
    protected $itemModel;
    protected $productModel;

    public function __construct()
    {
        $this->orderModel   = new OrderModel();
        $this->itemModel    = new OrderItemModel();
        $this->productModel = new ProductModel();
    }

    // ---------------------------------------------------
    // LIST ORDERS
    // ---------------------------------------------------
    public function index()
    {
        $status = $this->request->getGet('status');

        if ($status) {
            $orders = $this->orderModel->where('status', $status)->orderBy('id', 'DESC')->findAll();
        } else {
            $orders = $this->orderModel->orderBy('id', 'DESC')->findAll();
        }

        // Defensive: ensure missing keys don’t break UI
        foreach ($orders as &$o) {
            $o['user_id']    = $o['user_id']    ?? null;
            $o['total_price'] = $o['total_price'] ?? 0;
            $o['status']     = $o['status']     ?? 'pending';
            $o['created_at'] = $o['created_at'] ?? null;
        }
        unset($o);

        return view('admin/orders_list', [
            'orders' => $orders,
            'status' => $status
        ]);
    }
    // ---------------------------------------------------
// UPDATE ORDER STATUS
// ---------------------------------------------------
public function changeStatus($id)
{
    $order = $this->orderModel->find($id);

    if (!$order) {
        return redirect()->to('/admin/orders')->with('error', 'Order not found');
    }

    $newStatus = $this->request->getPost('status');

    if (!in_array($newStatus, ['pending', 'shipped', 'delivered', 'cancelled'])) {

        return redirect()->back()->with('error', 'Invalid status value');
    }

    $this->orderModel->update($id, [
        'status' => $newStatus,
    ]);

    return redirect()->to('/admin/orders/view/' . $id)->with('success', 'Order status updated');
}


    // ---------------------------------------------------
    // VIEW ORDER DETAILS
    // ---------------------------------------------------
    public function view($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Order not found.');
        }

        $items = $this->itemModel->where('order_id', $id)->findAll();

        // Attach product names
        foreach ($items as &$item) {
            $product = $this->productModel->find($item['product_id']);
            $item['product_name'] = $product['name'] ?? 'Unknown';
        }
        unset($item);

        return view('admin/order_detail', [
            'order' => $order,
            'items' => $items,
        ]);
    }
}

