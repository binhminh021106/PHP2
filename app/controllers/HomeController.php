<?php
class HomeController extends Controller
{
    public function index()
    {
        $product = $this->model('ProductModel');
        $data = $product->index();
        $this->view('home.index', ['products' => $data]);
    }
}
