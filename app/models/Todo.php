<?php
class Todo {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM todos ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM todos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        // generate UUID
        $uuid = $this->generateUuid();

        $stmt = $this->db->prepare("INSERT INTO todos (id, title, description, is_done, created_at)
                                    VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $uuid,
            $data['title'],
            $data['description'] ?? '',
            $data['is_done'] ?? 0
        ]);

        // kembalikan ID yang dibuat
        return $uuid;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE todos SET title=?, description=?, is_done=? WHERE id=?");
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['is_done'] ?? 0,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM todos WHERE id=?");
        return $stmt->execute([$id]);
    }

    // helper untuk membuat UUID v4 (tanpa ekstensi tambahan)
    private function generateUuid() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
