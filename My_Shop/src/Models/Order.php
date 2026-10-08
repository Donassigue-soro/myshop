<?php
namespace WecodeGuy\ProjetMyShop\Models;

class Order extends Model
{
    public const STATUSES = [
        'pending'   => 'En attente',
        'paid'      => 'Payée',
        'shipped'   => 'Expédiée',
        'cancelled' => 'Annulée',
    ];

    /**
     * Crée la commande et ses lignes dans UNE transaction.
     * @param array $lines [['product' => array, 'qty' => int], ...]
     */
    public function create(int $userId, array $lines): int
    {
        $total = 0.0;
        foreach ($lines as $l) {
            $total += (float)$l['product']['price'] * (int)$l['qty'];
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('INSERT INTO orders (user_id, total, status) VALUES (:u, :t, :s)');
            $stmt->execute(['u' => $userId, 't' => number_format($total, 2, '.', ''), 's' => 'pending']);
            $orderId = (int)$this->db->lastInsertId();

            $item = $this->db->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (:o, :p, :n, :price, :q)');
            foreach ($lines as $l) {
                $item->execute([
                    'o'     => $orderId,
                    'p'     => $l['product']['id'],
                    'n'     => $l['product']['name'],
                    'price' => $l['product']['price'],
                    'q'     => (int)$l['qty'],
                ]);
            }
            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = :u ORDER BY id DESC');
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT o.*, u.username, u.email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function items(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :id ORDER BY id');
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT o.*, u.username FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.id DESC')->fetchAll();
    }

    public function setStatus(int $id, string $status): void
    {
        if (!isset(self::STATUSES[$status])) {
            throw new \InvalidArgumentException('Statut invalide');
        }
        $stmt = $this->db->prepare('UPDATE orders SET status = :s WHERE id = :id');
        $stmt->execute(['s' => $status, 'id' => $id]);
    }

    public function stats(): array
    {
        $row = $this->db->query("SELECT COUNT(*) AS n, COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN total END), 0) AS revenue FROM orders")->fetch();
        return ['count' => (int)$row['n'], 'revenue' => (float)$row['revenue']];
    }
}
