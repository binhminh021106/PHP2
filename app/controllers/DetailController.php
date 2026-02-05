<?php

class DetailController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = $this->model('ProductModel');
    }

    public function index($id = '')
    {
        if (empty($id)) {
            header('Location: /');
            return;
        }

        $product = $this->productModel->getById($id);

        if (!$product) {
            echo "Sản phẩm không tồn tại hoặc đã bị xóa!"; 
            return;
        }

        $relatedProducts = [];
        if (!empty($product['category_id'])) {
            $relatedProducts = $this->productModel->getRelated($product['category_id'], $id);
        }

        $this->view('home/detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'pageTitle' => $product['name']
        ]);
    }
}