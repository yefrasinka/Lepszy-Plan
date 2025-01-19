<?php

namespace App\Model;

use App\Service\Database;
use PDO;

abstract class Model {
    protected $db;
    protected static $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function all() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save(array $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, array $data) {
        $setClause = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));

        $sql = "UPDATE " . static::$table . " SET $setClause WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
