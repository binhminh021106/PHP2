<?php

class ProductController extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index()
    {
        $products = $this->productModel->getAll();
        $this->view('AdminProduct.index', [
            'products' => $products,
            'title' => 'Quản lý sản phẩm'
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->index(); // Lấy danh mục để select
        $this->view('AdminProduct.create', [
            'categories' => $categories,
            'title' => 'Thêm mới sản phẩm'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Dữ liệu cơ bản
            $data = [
                'name' => $_POST['name'],
                'slug' => $this->createSlug($_POST['name']),
                'category_id' => $_POST['category_id'],
                'price_regular' => $_POST['price_regular'] ?? 0,
                'price_sale' => $_POST['price_sale'] ?? 0,
                'description' => $_POST['description'],
                'content' => $_POST['content'],
                'status' => $_POST['status'] ?? 'inactive',
                'img_thumbnail' => null
            ];

            // 2. Upload Thumbnail
            if (!empty($_FILES['img_thumbnail']['name'])) {
                $data['img_thumbnail'] = $this->uploadFile($_FILES['img_thumbnail'], 'products');
            }

            // 3. Xử lý Variants
            $variants = [];
            if (isset($_POST['variant_sku'])) {
                foreach ($_POST['variant_sku'] as $key => $sku) {
                    $varImg = null;
                    // Upload ảnh riêng cho variant nếu có
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

                    // Gộp thuộc tính thành JSON (Ví dụ: Màu:Đỏ, Size:XL)
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

            // 4. Xử lý Gallery (Nhiều ảnh)
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

            // Gọi Model
            if ($this->productModel->createProduct($data, $variants, $gallery)) {
                $_SESSION['success'] = "Thêm sản phẩm thành công!";
                header("Location: /product/index");
            } else {
                $_SESSION['error'] = "Lỗi hệ thống!";
                header("Location: /product/create");
            }
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->getById($id);
        if (!$product) $this->redirect('/product/index');

        $categories = $this->categoryModel->index();

        $this->view('AdminProduct.edit', [
            'product' => $product,
            'categories' => $categories,
            'title' => 'Cập nhật sản phẩm'
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy data cũ để check ảnh
            $oldProduct = $this->productModel->getById($id);

            $data = [
                'name' => $_POST['name'],
                'slug' => $this->createSlug($_POST['name']),
                'category_id' => $_POST['category_id'],
                'price_regular' => $_POST['price_regular'],
                'price_sale' => $_POST['price_sale'],
                'description' => $_POST['description'],
                'content' => $_POST['content'],
                'status' => $_POST['status'],
                'img_thumbnail' => null
            ];

            // Nếu upload ảnh mới thì lấy, không thì null (model sẽ check)
            if (!empty($_FILES['img_thumbnail']['name'])) {
                $data['img_thumbnail'] = $this->uploadFile($_FILES['img_thumbnail'], 'products');
            }

            // Xử lý Variants (Logic tương tự create)
            $variants = [];
            if (isset($_POST['variant_sku'])) {
                foreach ($_POST['variant_sku'] as $key => $sku) {
                    $varImg = $_POST['existing_variant_image'][$key] ?? null; // Giữ ảnh cũ

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

            // Gallery Mới
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

            // Ảnh gallery cần xóa
            $deletedGalleryIds = $_POST['delete_gallery_ids'] ?? [];

            if ($this->productModel->updateProduct($id, $data, $variants, $newGallery, $deletedGalleryIds)) {
                $_SESSION['success'] = "Cập nhật thành công!";
                header("Location: /product/index");
            } else {
                $_SESSION['error'] = "Cập nhật thất bại!";
                header("Location: /product/edit/$id");
            }
        }
    }

    public function delete()
    {
        if (isset($_POST['delete_id'])) {
            $this->productModel->softDelete($_POST['delete_id']);
            $_SESSION['success'] = "Đã xóa sản phẩm!";
            header("Location: /product/index");
        }
    }

    // --- Helpers ---
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
