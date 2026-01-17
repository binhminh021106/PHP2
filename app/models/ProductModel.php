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

    public function create($data = [])
    {
        $sql = "INSERT INTO $this->table (name, category_id, description, is_active, slug, author, publisher, publication_year, img_thumbnail, price_regular, price_sale, content) 
                                        VALUES (:name, :category_id, :description, :is_active, :slug, :author, :publisher, :publication_year, :img_thumbnail, :price_regular, :price_sale, :content)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
        ]);
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
