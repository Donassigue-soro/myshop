<?php
namespace WecodeGuy\ProjetMyShop\Models;

class Category extends Model
{
    /** Toutes les catégories avec le nom du parent (auto-jointure) */
    public function all(): array
    {
        $sql = 'SELECT c.id, c.name, c.parent_id, p.name AS parent_name,
                       (SELECT COUNT(*) FROM products pr WHERE pr.category_id = c.id) AS product_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                ORDER BY COALESCE(c.parent_id, c.id), c.parent_id IS NOT NULL, c.name';
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, ?int $parentId = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name, parent_id) VALUES (:name, :parent)');
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':parent', $parentId, $parentId === null ? \PDO::PARAM_NULL : \PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    /** Les produits de la catégorie passent à "sans catégorie" (ON DELETE SET NULL) */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    }
}
