<?php

class HomeController extends \Controller
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

    public function contact()
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
        }
        unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

        $this->view('home/contact', [
            'errors' => $errors,
            'old' => $old,
            'success_msg' => $successMsg
        ]);
    }

    public function contactSubmit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /home/contact');
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        $errors = [];
        if ($name === '') {
            $errors['name'] = 'Vui lòng nhập họ tên.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Vui lòng nhập email hợp lệ.';
        }
        if ($subject === '') {
            $errors['subject'] = 'Vui lòng chọn chủ đề.';
        }
        if ($message === '') {
            $errors['message'] = 'Vui lòng nhập nội dung tin nhắn.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: /home/contact');
            exit();
        }

        if (class_exists('ContactModel')) {
            $this->model('ContactModel')->create([
                'title' => $subject,
                'content' => $message,
                'email' => $email,
                'fullname' => $name,
                'phone' => $phone
            ]);
        }

        $_SESSION['success'] = 'Thêm liên hệ thành công. Chúng tôi sẽ liên lạc lại với bạn sớm nhất có thể!';

        header('Location: /home/contact');
        exit();
    }

    public function detail($id)
    {
        $productModel = $this->model('ProductModel');
        $product = $productModel->getById($id);

        if (!$product) {
            header("Location: /");
            exit();
        }

        $colors = [];
        $sizes = [];
        $variantsData = []; 

        if (!empty($product['variants'])) {
            foreach ($product['variants'] as &$variant) {
                // Parse attributes
                $attr = is_string($variant['attributes']) ? json_decode($variant['attributes'], true) : $variant['attributes'];
                $variant['parsed_attr'] = $attr; 

                $color = $attr['Color'] ?? '';
                $size = $attr['Size'] ?? '';

                if (!empty($color) && !in_array($color, $colors)) {
                    $colors[] = $color;
                }
                if (!empty($size) && !in_array($size, $sizes)) {
                    $sizes[] = $size;
                }

                $variantsData[] = [
                    'id' => $variant['id'],
                    'color' => $color,
                    'size' => $size,
                    'price' => $variant['price'],
                    'stock' => $variant['quantity'] ?? 0,
                    'image' => $variant['image'] 
                ];
            }
        }

        // Lấy danh sách ảnh gallery đã được Model gán sẵn
        $gallery = $product['gallery'] ?? [];

        $relatedProducts = $productModel->getRelated($product['category_id'], $id, 4);

        $this->view('home/detail', [
            'title' => $product['name'],
            'product' => $product,
            'gallery' => $gallery,
            'colors' => $colors,
            'sizes' => $sizes,
            'variantsJson' => json_encode($variantsData), 
            'relatedProducts' => $relatedProducts
        ]);
    }
}
