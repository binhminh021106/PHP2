<?php

class Controller
{
    /**
     * Hàm gọi View để hiển thị giao diện
     * @param string $viewPath Đường dẫn file view 
     * @param array $data Mảng dữ liệu muốn truyền ra view
     */
    public function view($viewPath, $data = [])
    {

        extract($data);

        $fullPath = VIEW_PATH . '/' . $viewPath . '.php';

        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            echo "View '$viewPath' not found!";
        }
    }

    public function model($modelName)
    {
        require_once MODEL_PATH . '/' . $modelName . '.php';
        return new $modelName;
    }
}