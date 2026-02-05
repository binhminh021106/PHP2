<?php

class HomeController extends Controller
{
    public $productModel;
    public $categoryModel;

    public function __construct()
    {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index()
    {
        $products = $this->productModel->getAll();

        $categories = $this->categoryModel->index();

        $this->view('home/index', [
            'products' => $products,
            'categories' => $categories,
            'pageTitle' => 'Menswear - Thời trang nam cao cấp'
        ]);
    }
}