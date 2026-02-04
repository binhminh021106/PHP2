<?php

class ProductController extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->checkAdminRole();
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index()
    {
        $products = $this->productModel->getAll();

        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        $this->view('AdminProduct.index', [
            'products' => $products,
            'title' => 'Quản lý sản phẩm',
            'success_msg' => $successMsg
        ]);
    }

    public function show($id)
    {
        $product = $this->productModel->getById($id);
        if ($product) {
            if (!empty($product['variants'])) {
                foreach ($product['variants'] as &$variant) {
                    $variant['attributes'] = json_decode($variant['attributes'], true);
                }
            }
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm']);
        }
        exit();
    }

    public function create()
    {
        $categories = $this->categoryModel->index();

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('AdminProduct.create', [
            'categories' => $categories,
            'title' => 'Thêm mới sản phẩm',
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $price_regular = $_POST['price_regular'];
            $price_sale = $_POST['price_sale'];
            $category_id = $_POST['category_id'];

            $errors = [];

            if (empty($name)) {
                $errors['name'] = "Tên sản phẩm không được để trống.";
            }

            if ($price_regular == '' || $price_regular < 0) {
                $errors['price_regular'] = "Giá gốc phải là số dương.";
            }

            if ($price_sale != '' && $price_sale < 0) {
                $errors['price_sale'] = "Giá khuyến mãi không được âm.";
            }

            if (empty($category_id)) {
                $errors['category_id'] = "Vui lòng chọn danh mục.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header("Location: /product/create");
                exit();
            }

            $data = [
                'name' => $name,
                'slug' => $this->createSlug($name),
                'category_id' => $category_id,
                'price_regular' => $price_regular,
                'price_sale' => $price_sale,
                'description' => $_POST['description'],
                'content' => $_POST['content'],
                'status' => $_POST['status'] ?? 'inactive',
                'img_thumbnail' => null
            ];

            if (!empty($_FILES['img_thumbnail']['name'])) {
                $data['img_thumbnail'] = $this->uploadFile($_FILES['img_thumbnail'], 'products');
            }

            $variants = [];
            if (isset($_POST['variant_sku'])) {
                foreach ($_POST['variant_sku'] as $key => $sku) {
                    if (empty($sku)) continue;

                    $varImg = null;
                    if (!empty($_FILES['variant_image']['name'][$key])) {
                        $file = [
                            'name' => $_FILES['variant_image']['name'][$key],
                            'type' => $_FILES['variant_image']['type'][$key],
                            'tmp_name' => $_FILES['variant_image']['tmp_name'][$key],
                            'error' => $_FILES['variant_image']['error'][$key],
                            'size' => $_FILES['variant_image']['size'][$key],
                        ];
                        $varImg = $this->uploadFile($file, 'variants');
                    }

                    $attributes = [
                        'Color' => $_POST['variant_color'][$key] ?? '',
                        'Size' => $_POST['variant_size'][$key] ?? ''
                    ];

                    $variants[] = [
                        'sku' => $sku,
                        'price' => $_POST['variant_price'][$key] ?? 0,
                        'quantity' => $_POST['variant_qty'][$key] ?? 0,
                        'attributes' => json_encode($attributes, JSON_UNESCAPED_UNICODE),
                        'image' => $varImg
                    ];
                }
            }

            $gallery = [];
            if (!empty($_FILES['gallery']['name'][0])) {
                $count = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $file = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'type' => $_FILES['gallery']['type'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                        'error' => $_FILES['gallery']['error'][$i],
                        'size' => $_FILES['gallery']['size'][$i],
                    ];
                    $uploaded = $this->uploadFile($file, 'gallery');
                    if ($uploaded) $gallery[] = $uploaded;
                }
            }

            if ($this->productModel->createProduct($data, $variants, $gallery)) {
                $_SESSION['success'] = "Thêm sản phẩm thành công!";
                header("Location: /product/index");
            } else {
                $_SESSION['error'] = "Lỗi hệ thống khi lưu sản phẩm!";
                header("Location: /product/create");
            }
            exit();
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->getById($id);
        if (!$product) $this->redirect('/product/index');

        $categories = $this->categoryModel->index();

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $this->view('AdminProduct.edit', [
            'product' => $product,
            'categories' => $categories,
            'title' => 'Cập nhật sản phẩm',
            'errors' => $errors
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $price_regular = $_POST['price_regular'];
            $price_sale = $_POST['price_sale'];

            $errors = [];
            if (empty($name)) {
                $errors['name'] = "Tên sản phẩm không được để trống.";
            }
            if ($price_regular == '' || $price_regular < 0) {
                $errors['price_regular'] = "Giá gốc không hợp lệ.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: /product/edit/$id");
                exit();
            }

            $data = [
                'name' => $name,
                'slug' => $this->createSlug($name),
                'category_id' => $_POST['category_id'],
                'price_regular' => $price_regular,
                'price_sale' => $price_sale,
                'description' => $_POST['description'],
                'content' => $_POST['content'],
                'status' => $_POST['status'],
                'img_thumbnail' => null
            ];

            if (!empty($_FILES['img_thumbnail']['name'])) {
                $data['img_thumbnail'] = $this->uploadFile($_FILES['img_thumbnail'], 'products');
            }

            $variants = [];
            if (isset($_POST['variant_sku'])) {
                foreach ($_POST['variant_sku'] as $key => $sku) {
                    if (empty($sku)) continue;

                    $varImg = $_POST['existing_variant_image'][$key] ?? null;

                    if (!empty($_FILES['variant_image']['name'][$key])) {
                        $file = [
                            'name' => $_FILES['variant_image']['name'][$key],
                            'type' => $_FILES['variant_image']['type'][$key],
                            'tmp_name' => $_FILES['variant_image']['tmp_name'][$key],
                            'error' => $_FILES['variant_image']['error'][$key],
                            'size' => $_FILES['variant_image']['size'][$key],
                        ];
                        $varImg = $this->uploadFile($file, 'variants');
                    }

                    $attributes = [
                        'Color' => $_POST['variant_color'][$key] ?? '',
                        'Size' => $_POST['variant_size'][$key] ?? ''
                    ];

                    $variants[] = [
                        'sku' => $sku,
                        'price' => $_POST['variant_price'][$key],
                        'quantity' => $_POST['variant_qty'][$key],
                        'attributes' => json_encode($attributes, JSON_UNESCAPED_UNICODE),
                        'image' => $varImg
                    ];
                }
            }

            $newGallery = [];
            if (!empty($_FILES['gallery']['name'][0])) {
                $count = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $file = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'type' => $_FILES['gallery']['type'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                        'error' => $_FILES['gallery']['error'][$i],
                        'size' => $_FILES['gallery']['size'][$i],
                    ];
                    $uploaded = $this->uploadFile($file, 'gallery');
                    if ($uploaded) $newGallery[] = $uploaded;
                }
            }

            $deletedGalleryIds = $_POST['delete_gallery_ids'] ?? [];

            if ($this->productModel->updateProduct($id, $data, $variants, $newGallery, $deletedGalleryIds)) {
                $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
                header("Location: /product/index");
            } else {
                $_SESSION['error'] = "Cập nhật thất bại!";
                header("Location: /product/edit/$id");
            }
            exit();
        }
    }

    public function delete()
    {
        if (isset($_POST['delete_id'])) {
            $this->productModel->softDelete($_POST['delete_id']);
            $_SESSION['success'] = "Đã xóa sản phẩm thành công!";
            header("Location: /product/index");
            exit();
        }
    }

    private function uploadFile($file, $folder)
    {
        $targetDir = "storage/uploads/$folder/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($file["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $fileName;
        }
        return null;
    }

    private function createSlug($string)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }
}
