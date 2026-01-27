<?php

class ProductModel extends Model
{
    // Lấy danh sách sản phẩm kèm danh mục
    public function getAll()
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NULL 
                ORDER BY p.id DESC";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 sản phẩm
    public function getById($id)
    {
        $conn = $this->connect();
        
        // 1. Lấy thông tin chung
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) return null;

        // 2. Lấy biến thể
        $stmtVar = $conn->prepare("SELECT * FROM product_variants WHERE product_id = :id");
        $stmtVar->execute(['id' => $id]);
        $product['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

        // 3. Lấy thư viện ảnh
        $stmtImg = $conn->prepare("SELECT * FROM product_images WHERE product_id = :id");
        $stmtImg->execute(['id' => $id]);
        $product['gallery'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

    // Thêm mới Full (Dùng Transaction)
    public function createProduct($data, $variants = [], $gallery = [])
    {
        $conn = $this->connect();
        try {
            $conn->beginTransaction(); // Bắt đầu transaction

            // 1. Insert Product
            $sql = "INSERT INTO products (name, slug, category_id, price_regular, price_sale, 
                    description, content, img_thumbnail, status, created_at) 
                    VALUES (:name, :slug, :cat_id, :price, :sale, :desc, :content, :thumb, :status, NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'cat_id' => $data['category_id'],
                'price' => $data['price_regular'],
                'sale' => $data['price_sale'],
                'desc' => $data['description'],
                'content' => $data['content'],
                'thumb' => $data['img_thumbnail'],
                'status' => $data['status']
            ]);
            
            $productId = $conn->lastInsertId();

            // 2. Insert Variants
            if (!empty($variants)) {
                $sqlVar = "INSERT INTO product_variants (product_id, sku, price, quantity, attributes, image) 
                           VALUES (:pid, :sku, :price, :qty, :attr, :img)";
                $stmtVar = $conn->prepare($sqlVar);
                
                foreach ($variants as $var) {
                    $stmtVar->execute([
                        'pid' => $productId,
                        'sku' => $var['sku'],
                        'price' => $var['price'],
                        'qty' => $var['quantity'],
                        'attr' => $var['attributes'], // JSON string
                        'img' => $var['image']
                    ]);
                }
            }

            // 3. Insert Gallery
            if (!empty($gallery)) {
                $sqlGal = "INSERT INTO product_images (product_id, image_path) VALUES (:pid, :path)";
                $stmtGal = $conn->prepare($sqlGal);
                foreach ($gallery as $path) {
                    $stmtGal->execute(['pid' => $productId, 'path' => $path]);
                }
            }

            $conn->commit(); // Lưu thành công
            return true;

        } catch (Exception $e) {
            $conn->rollBack(); // Hoàn tác nếu lỗi
            error_log("Create Product Error: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật Full
    public function updateProduct($id, $data, $newVariants = [], $newGallery = [], $deletedGalleryIds = [])
    {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            // 1. Update Product Info
            $sql = "UPDATE products SET name=:name, slug=:slug, category_id=:cat_id, 
                    price_regular=:price, price_sale=:sale, description=:desc, 
                    content=:content, status=:status, updated_at=NOW()";
            
            // Nếu có ảnh mới thì update, không thì giữ nguyên
            if (!empty($data['img_thumbnail'])) {
                $sql .= ", img_thumbnail=:thumb";
            }
            $sql .= " WHERE id=:id";

            $params = [
                'name' => $data['name'], 'slug' => $data['slug'], 'cat_id' => $data['category_id'],
                'price' => $data['price_regular'], 'sale' => $data['price_sale'],
                'desc' => $data['description'], 'content' => $data['content'],
                'status' => $data['status'], 'id' => $id
            ];
            if (!empty($data['img_thumbnail'])) {
                $params['thumb'] = $data['img_thumbnail'];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // 2. Xử lý Variants (Xóa cũ thêm mới cho đơn giản, hoặc update nếu làm kỹ hơn)
            // Ở đây tôi chọn cách xóa hết biến thể cũ của sản phẩm này và thêm lại danh sách mới để tránh phức tạp logic ID
            // Lưu ý: Cách này sẽ làm mất ID cũ của variant. Nếu cần giữ ID thì phải code logic check update/insert riêng.
            $conn->exec("DELETE FROM product_variants WHERE product_id = $id");

            if (!empty($newVariants)) {
                $sqlVar = "INSERT INTO product_variants (product_id, sku, price, quantity, attributes, image) 
                           VALUES (:pid, :sku, :price, :qty, :attr, :img)";
                $stmtVar = $conn->prepare($sqlVar);
                
                foreach ($newVariants as $var) {
                    $stmtVar->execute([
                        'pid' => $id,
                        'sku' => $var['sku'],
                        'price' => $var['price'],
                        'qty' => $var['quantity'],
                        'attr' => $var['attributes'],
                        'img' => $var['image']
                    ]);
                }
            }

            // 3. Xử lý Gallery
            // Thêm ảnh mới
            if (!empty($newGallery)) {
                $sqlGal = "INSERT INTO product_images (product_id, image_path) VALUES (:pid, :path)";
                $stmtGal = $conn->prepare($sqlGal);
                foreach ($newGallery as $path) {
                    $stmtGal->execute(['pid' => $id, 'path' => $path]);
                }
            }
            // Xóa ảnh cũ được yêu cầu
            if (!empty($deletedGalleryIds)) {
                // Chuyển mảng ID thành chuỗi an toàn
                $ids = implode(',', array_map('intval', $deletedGalleryIds));
                $conn->exec("DELETE FROM product_images WHERE id IN ($ids)");
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Update Product Error: " . $e->getMessage());
            return false;
        }
    }

    // Xóa mềm (Soft Delete)
    public function softDelete($id)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("UPDATE products SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}