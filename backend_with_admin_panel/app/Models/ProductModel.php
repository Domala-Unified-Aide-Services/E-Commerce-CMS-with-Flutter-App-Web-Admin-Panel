<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'description',
        'price',
        'category_id',
        'stock',
        'image_url',
        'created_at'
    ];

    /**
     * Custom list function with:
     * search
     * category filter
     * pagination
     */
    public function paginateList(?string $query, ?int $categoryId, int $page, int $limit)
    {
        $builder = $this->builder();

        //  Search by name
        if ($query) {
            $builder->like('name', $query);
        }

        //  Filter by category
        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        // Total count
        $total = $builder->countAllResults(false);

        // Data with pagination
        $items = $builder
            ->orderBy('id', 'DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->get()
            ->getResultArray();

        return [
            'total' => $total,
            'items' => $items
        ];
    }
}
