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

    public function getById($id)
    {
        $conn = $this->connect();

        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id AND p.deleted_at IS NULL";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) return null;

        $stmtVar = $conn->prepare("SELECT * FROM product_variants WHERE product_id = :id");
        $stmtVar->execute(['id' => $id]);
        $product['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

        $stmtImg = $conn->prepare("SELECT * FROM product_images WHERE product_id = :id");
        $stmtImg->execute(['id' => $id]);
        $product['gallery'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

     public function getRelated($categoryId, $excludeId, $limit = 4)
    {
        $conn = $this->connect();
        $sql = "SELECT * FROM products 
                WHERE category_id = :cat_id 
                AND id != :id 
                AND deleted_at IS NULL 
                LIMIT :limit";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':cat_id', $categoryId);
        $stmt->bindValue(':id', $excludeId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProduct($data, $variants = [], $gallery = [])
    {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

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
                        'attr' => $var['attributes'],
                        'img' => $var['image']
                    ]);
                }
            }

            if (!empty($gallery)) {
                $sqlGal = "INSERT INTO product_images (product_id, image_path) VALUES (:pid, :path)";
                $stmtGal = $conn->prepare($sqlGal);
                foreach ($gallery as $path) {
                    $stmtGal->execute(['pid' => $productId, 'path' => $path]);
                }
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Create Product Error: " . $e->getMessage());
            return false;
        }
    }
    public function updateProduct($id, $data, $newVariants = [], $newGallery = [], $deletedGalleryIds = [])
    {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            $sql = "UPDATE products SET name=:name, slug=:slug, category_id=:cat_id, 
                    price_regular=:price, price_sale=:sale, description=:desc, 
                    content=:content, status=:status, updated_at=NOW()";

            if (!empty($data['img_thumbnail'])) {
                $sql .= ", img_thumbnail=:thumb";
            }
            $sql .= " WHERE id=:id";

            $params = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'cat_id' => $data['category_id'],
                'price' => $data['price_regular'],
                'sale' => $data['price_sale'],
                'desc' => $data['description'],
                'content' => $data['content'],
                'status' => $data['status'],
                'id' => $id
            ];
            if (!empty($data['img_thumbnail'])) {
                $params['thumb'] = $data['img_thumbnail'];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

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

            if (!empty($newGallery)) {
                $sqlGal = "INSERT INTO product_images (product_id, image_path) VALUES (:pid, :path)";
                $stmtGal = $conn->prepare($sqlGal);
                foreach ($newGallery as $path) {
                    $stmtGal->execute(['pid' => $id, 'path' => $path]);
                }
            }
            if (!empty($deletedGalleryIds)) {
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

    public function softDelete($id)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("UPDATE products SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
