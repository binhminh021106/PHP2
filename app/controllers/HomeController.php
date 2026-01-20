<?php 
class HomeController extends Controller {
    public function index() {
        $product = $this->model('ProductModel');
        $data = $product->index();
        $title = "Trang chủ";
        $this->view('home/index', ['title' => $title, 'products' => $data]);
    }
}