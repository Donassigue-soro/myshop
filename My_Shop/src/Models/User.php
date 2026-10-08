<?php
namespace WecodeGuy\ProjetMyShop\Models;

class User extends Model
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, username, email, password, admin, created_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Recherche par nom d'utilisateur OU email */
    public function findByLogin(string $login): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :u OR email = :e LIMIT 1');
        $stmt->execute(['u' => $login, 'e' => $login]);
        return $stmt->fetch() ?: null;
    }

    /** Retourne la liste des champs déjà pris (username / email) */
    public function conflicts(string $username, string $email, ?int $ignoreId = null): array
    {
        $stmt = $this->db->prepare('SELECT id, username, email FROM users WHERE (username = :u OR email = :e) AND id <> :id');
        $stmt->execute(['u' => $username, 'e' => $email, 'id' => $ignoreId ?? 0]);
        $found = [];
        foreach ($stmt->fetchAll() as $row) {
            if (strcasecmp($row['username'], $username) === 0) { $found['username'] = true; }
            if (strcasecmp($row['email'], $email) === 0)       { $found['email'] = true; }
        }
        return array_keys($found);
    }

    public function create(string $username, string $email, string $passwordHash, bool $admin = false): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, admin) VALUES (:u, :e, :p, :a)');
        $stmt->bindValue(':u', $username);
        $stmt->bindValue(':e', $email);
        $stmt->bindValue(':p', $passwordHash);
        $stmt->bindValue(':a', $admin ? 1 : 0, \PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $username, string $email): void
    {
        $stmt = $this->db->prepare('UPDATE users SET username = :u, email = :e WHERE id = :id');
        $stmt->execute(['u' => $username, 'e' => $email, 'id' => $id]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :p WHERE id = :id');
        $stmt->execute(['p' => $hash, 'id' => $id]);
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, username, email, admin, created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public function setAdmin(int $id, bool $admin): void
    {
        $stmt = $this->db->prepare('UPDATE users SET admin = :a WHERE id = :id');
        $stmt->bindValue(':a', $admin ? 1 : 0, \PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
