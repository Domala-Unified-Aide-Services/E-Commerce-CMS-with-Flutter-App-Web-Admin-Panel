<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $orderModel = new OrderModel();

        // TOTAL SALES (delivered orders only)
        $totalSales = $orderModel
            ->where('status', 'delivered')
            ->selectSum('total_price')
            ->first()['total_price'] ?? 0;

        // COUNTS
        $totalCategories = $categoryModel->countAllResults();
        $totalUsers = $userModel->countAllResults();
        $totalOrders = $orderModel->countAllResults();

        // RECENT ORDERS (limit 5)
        $recentOrders = $orderModel
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->findAll();

        return view('admin/dashboard', [
            'totalSales'      => $totalSales,
            'totalCategories' => $totalCategories,
            'totalUsers'      => $totalUsers,
            'totalOrders'     => $totalOrders,
            'recentOrders'    => $recentOrders,
        ]);
    }
}
