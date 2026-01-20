<?php
class ProductModel extends Model
{
    private $table = 'products';

    public function index()
    {
        $sql = "SELECT * FROM $this->table";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data = [], $variant = [], $galleryImages = [])
    {
        $conn = $this->connect();

        try {

            $sqlProduct = "INSERT INTO $this->table (name, category_id, description, is_active, slug, author, publisher, publication_year, img_thumbnail, price_regular, price_sale, content) 
                                        VALUES (:name, :category_id, :description, :is_active, :slug, :author, :publisher, :publication_year, :img_thumbnail, :price_regular, :price_sale, :content)";

            $stmt = $conn->prepare($sqlProduct);
            $stmt->execute([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'description' => $data['description'],
                'is_active' => $data['is_active'],
                'slug' => $data['slug'],
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'publication_year' => $data['publication_year'],
                'img_thumbnail' => $data['img_thumbnail'],
                'price_regular' => $data['price_regular'],
                'price_sale' => $data['price_sale'],
                'content' => $data['content'],
            ]);

            $productId = $conn->lastInsertId();

            if (!empty($variants)) {
            $sqlVariant = "INSERT INTO product_variants 
                (product_id, sku, price, regular_price, quantity, attributes, image) 
                VALUES 
                (:pid, :sku, :price, :reg_price, :qty, :attr, :img)";
                
            $stmtVar = $conn->prepare($sqlVariant);

            foreach ($variants as $variant) {
                $stmtVar->execute([
                    'pid'       => $productId,
                    'sku'       => $variant['sku'] ?? null,
                    'price'     => $variant['price'],
                    'reg_price' => $variant['regular_price'] ?? null,
                    'qty'       => $variant['quantity'] ?? 0,
                    'attr'      => json_encode($variant['attributes'], JSON_UNESCAPED_UNICODE),
                    'img'       => $variant['image'] ?? null 
                ]);
            }
        }

        if (!empty($galleryImages)) {
            $sqlImg = "INSERT INTO product_images (product_id, image_path) VALUES (:pid, :path)";
            $stmtImg = $conn->prepare($sqlImg);

            foreach ($galleryImages as $path) {
                $stmtImg->execute([
                    'pid'  => $productId,
                    'path' => $path
                ]);
            }
        }

        $conn->commit();
        return true;
        } catch (Exception $e) {
            $conn->rollBack();

            error_log("Lỗi tạo sản phẩm: " . $e->getMessage());
            return false;
        }
    }

    public function show($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data = [])
    {
        $sql = "UPDATE $this->table SET name = :name, price = :price WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
            'id' => $data['id'],
        ]);
    }

    public function destroy($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
