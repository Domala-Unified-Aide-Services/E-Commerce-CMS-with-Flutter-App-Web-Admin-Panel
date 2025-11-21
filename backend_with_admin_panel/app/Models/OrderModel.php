<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'total_price',
        'status',
        'created_at'
    ];

    /**
     * List orders belonging to a specific user.
     */
    public function listByUser(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    /**
     * Get order with computed totals or joined info if you want
     */
    public function getWithSummary(int $id)
    {
        $order = $this->find($id);
        if (!$order) return null;

        // optionally attach items count
        $ci = \Config\Database::connect();
        $builder = $ci->table('order_items')->select('SUM(quantity) as items_count')->where('order_id', $id);
        $row = $builder->get()->getRowArray();
        $order['items_count'] = $row ? (int)($row['items_count'] ?? 0) : 0;

        return $order;
    }
}
