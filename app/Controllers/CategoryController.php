<?php

class CategoryController extends Controller
{
    public function index()
    {
        $category = $this->model('CategoryModel');
        $data = $category->index();
        $title = "Quản lí danh mục";
        $this->view('AdminCategory/index', [
            'category' => $data,
            'title' => $title
        ]);
    }

    public function create()
    {
        $title = "Thêm danh mục mới";
        $this->view('AdminCategory/create', [
            'title' => $title
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $icon = trim($_POST['icon']);

            if (!empty($name)) {
                $data = [
                    'name' => $name,
                    'description' => $description,
                    'icon' => $icon
                ];

                $this->model('CategoryModel')->create($data);
            }
        }

        header('Location: /category/index');
        exit();
    }

    public function edit($id)
    {
        $categoryModel = $this->model('CategoryModel');
        $data = $categoryModel->show($id);

        if (!$data) {
            header('Location: /category/index');
            exit();
        }

        $title = "Chỉnh sửa danh mục";
        $this->view('AdminCategory/edit', [
            'category' => $data, 
            'title' => $title
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $icon = trim($_POST['icon']);

            if (!empty($name)) {
                $data = [
                    'name' => $name,
                    'description' => $description,
                    'icon' => $icon
                ];

                $this->model('CategoryModel')->update($id, $data);
            }
        }

        header('Location: /category/index');
        exit();
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = $_POST['delete_id'];
            if (!empty($id)) {
                $this->model('CategoryModel')->destroy($id);
            }
        }

        header('Location: /category/index');
        exit();
    }
}
