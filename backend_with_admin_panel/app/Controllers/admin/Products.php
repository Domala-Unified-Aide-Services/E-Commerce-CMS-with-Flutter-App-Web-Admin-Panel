<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    // -------------------------------------------------------
    // LIST PRODUCTS
    // -------------------------------------------------------
    public function index()
    {
        $data['products']   = $this->productModel->findAll();
        $data['categories'] = $this->categoryModel->findAll();

        echo view('admin/header');
        echo view('admin/products_list', $data);
        echo view('admin/footer');
    }

    // -------------------------------------------------------
    // CREATE PRODUCT FORM
    // -------------------------------------------------------
    public function create()
    {
        $data['categories'] = $this->categoryModel->findAll();

        echo view('admin/header');
        echo view('admin/product_form', $data);
        echo view('admin/footer');
    }

    // -------------------------------------------------------
    // STORE PRODUCT
    // -------------------------------------------------------
    public function store()
    {
        $post = $this->request->getPost();

        // Validate name, category, price
        if (!isset($post['name']) || trim($post['name']) === '') {
            return redirect()->back()->with('error', 'Product name is required.');
        }
        if (empty($post['category_id'])) {
            return redirect()->back()->with('error', 'Please select a category.');
        }

        $this->productModel->insert([
            'name'        => $post['name'],
            'description' => $post['description'] ?? '',
            'price'       => $post['price'] ?? 0,
            'category_id' => $post['category_id'],
            'stock'       => $post['stock'] ?? 0,
            'image_url'   => $post['image_url'] ?? null,
        ]);

        return redirect()->to(base_url('admin/products'))->with('success', 'Product created successfully.');
    }

    // -------------------------------------------------------
    // EDIT PRODUCT FORM
    // -------------------------------------------------------
    public function edit($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Product not found.');
        }

        $data['product']    = $product;
        $data['categories'] = $this->categoryModel->findAll();

        echo view('admin/header');
        echo view('admin/product_form', $data);
        echo view('admin/footer');
    }

    // -------------------------------------------------------
    // UPDATE PRODUCT
    // -------------------------------------------------------
    public function update($id)
    {
        $post = $this->request->getPost();

        if (!$this->productModel->find($id)) {
            return redirect()->to('/admin/products')->with('error', 'Product not found.');
        }
        if (empty($post['category_id'])) {
            return redirect()->back()->with('error', 'Please select a category.');
        }

        $this->productModel->update($id, [
            'name'        => $post['name'],
            'description' => $post['description'] ?? '',
            'price'       => $post['price'] ?? 0,
            'category_id' => $post['category_id'],
            'stock'       => $post['stock'] ?? 0,
            'image_url'   => $post['image_url'] ?? null,
        ]);

        return redirect()->to(base_url('admin/products'))->with('success', 'Product updated.');
    }

    // -------------------------------------------------------
    // DELETE PRODUCT
    // -------------------------------------------------------
    public function delete($id)
    {
        $this->productModel->delete($id);
        return redirect()->back()->with('success', 'Product deleted.');
    }
}
