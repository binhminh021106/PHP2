<?php

class CategoryController extends Controller
{
    public function index() {
        $category = $this->model('CategoryModel');
        $data = $category->index();
        $title = "Quản lí danh mục";
        $this->view('AdminCategory/index', [
            'category' => $data,
            'title' => $title
        ]);
    }

    public function create() {
        
    }
}