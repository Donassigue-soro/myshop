<?php
namespace WecodeGuy\ProjetMyShop\Models;

use PDO;

class Product extends Model
{
    private const SORTS = [
        'new'        => 'p.id DESC',
        'price_asc'  => 'p.price ASC, p.id DESC',
        'price_desc' => 'p.price DESC, p.id DESC',
        'name'       => 'p.name ASC',
    ];

    public static function sortOptions(): array
    {
        return ['new' => 'Nouveautés', 'price_asc' => 'Prix croissant', 'price_desc' => 'Prix décroissant', 'name' => 'Nom (A-Z)'];
    }

    /**
     * Recherche paginée.
     * Filtres : q, category, min, max, sort
     * @return array{items: array, total: int}
     */
    public function search(array $f, int $page, int $perPage): array
    {
        $where  = [];
        $params = [];

        if (!empty($f['q'])) {
            $like = '%' . addcslashes($f['q'], '%_\\') . '%';
            $where[] = '(p.name LIKE :q1 OR p.description LIKE :q2)';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
        }
        if (!empty($f['category'])) {
            // inclut les sous-catégories
            $where[] = '(p.category_id = :cat1 OR p.category_id IN (SELECT id FROM categories WHERE parent_id = :cat2))';
            $params[':cat1'] = (int)$f['category'];
            $params[':cat2'] = (int)$f['category'];
        }
        if (isset($f['min']) && $f['min'] !== null) {
            $where[] = 'p.price >= :min';
            $params[':min'] = $f['min'];
        }
        if (isset($f['max']) && $f['max'] !== null) {
            $where[] = 'p.price <= :max';
            $params[':max'] = $f['max'];
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $order = self::SORTS[$f['sort'] ?? 'new'] ?? self::SORTS['new'];

        $count = $this->db->prepare("SELECT COUNT(*) FROM products p $whereSql");
        foreach ($params as $k => $v) { $count->bindValue($k, $v); }
        $count->execute();
        $total = (int)$count->fetchColumn();

        $sql = "SELECT p.*, c.name AS category_name
                FROM products p LEFT JOIN categories c ON p.category_id = c.id
                $whereSql ORDER BY $order LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, ($page - 1) * $perPage), PDO::PARAM_INT);
        $stmt->execute();

        return ['items' => $stmt->fetchAll(), 'total' => $total];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findMany(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id IN ($in) ORDER BY p.id DESC");
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    public function maxPrice(): float
    {
        return (float)$this->db->query('SELECT COALESCE(MAX(price), 0) FROM products')->fetchColumn();
    }

    public function create(array $d, ?string $imagePath): int
    {
        $stmt = $this->db->prepare('INSERT INTO products (name, description, price, category_id, image_path) VALUES (:name, :description, :price, :cat, :img)');
        $this->bind($stmt, $d, $imagePath);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    /** $imagePath = null → on conserve l'image actuelle */
    public function update(int $id, array $d, ?string $imagePath = null): void
    {
        $sql = 'UPDATE products SET name = :name, description = :description, price = :price, category_id = :cat';
        if ($imagePath !== null) {
            $sql .= ', image_path = :img';
        }
        $stmt = $this->db->prepare($sql . ' WHERE id = :id');
        $this->bind($stmt, $d, $imagePath);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function bind(\PDOStatement $stmt, array $d, ?string $imagePath): void
    {
        $stmt->bindValue(':name', $d['name']);
        $stmt->bindValue(':description', $d['description']);
        $stmt->bindValue(':price', $d['price']);
        $cat = $d['category_id'] ?? null;
        $stmt->bindValue(':cat', $cat, $cat === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        if (str_contains($stmt->queryString, ':img')) {
            $stmt->bindValue(':img', $imagePath, $imagePath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
}
