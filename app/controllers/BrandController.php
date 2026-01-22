<?php

class BrandController extends Controller
{

    public function index() 
    {
        $brand = $this->model('BrandModel');
        $data = $brand->index();
        $title = "Quản lí Brand";
        $this->view('AdminBrand/index', [
            'brands' => $data,
            'title' => $title
        ]);
    }
}