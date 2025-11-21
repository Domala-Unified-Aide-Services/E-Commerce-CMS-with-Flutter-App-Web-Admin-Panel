<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table      = 'order_items';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    /**
     * Get items for an order, optionally with product joins (simple)
     */
    public function findByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }
}
