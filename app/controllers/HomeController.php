<?php

class HomeController extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index()
    {
        $products = $this->productModel->getAll(); 

        $categories = $this->categoryModel->index();

        $this->view('home.index', [
            'products' => $products,
            'categories' => $categories,
            'title' => 'Trang Chủ - MyShop'
        ]);
    }
}