<?php

class ProductController extends Controller
{
    public function index() {
        $product = $this->model('ProductModel');
        $data = $product->index();
        $title = "Quản lí sản phẩm";
        $this->view('AdminProduct/index', 
        [
            'products' => $data,
            'title' => $title
        ]);
    }
    
    public function create() {
        $title = "Thêm sản phẩm";
        $this->view('AdminProduct/create', ['title' => $title]);
    }
}