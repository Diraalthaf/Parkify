<?php
require_once __DIR__ . '/../config/koneksi.php';

class UserModel {

    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function getAllUsers() {
        $result = $this->db->query("
            SELECT id, nama, username, level 
            FROM user 
            ORDER BY id DESC
        ");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT id, nama, username, password, level 
            FROM user 
            WHERE id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function usernameExists($username, $exclude_id = null) {

        if ($exclude_id) {
            $stmt = $this->db->prepare("
                SELECT id FROM user 
                WHERE username = ? AND id != ?
            ");
            $stmt->bind_param('si', $username, $exclude_id);
        } else {
            $stmt = $this->db->prepare("
                SELECT id FROM user 
                WHERE username = ?
            ");
            $stmt->bind_param('s', $username);
        }

        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createUser($nama, $username, $password, $level) {
        $stmt = $this->db->prepare("
            INSERT INTO user (nama, username, password, level)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('ssss', $nama, $username, $password, $level);
        return $stmt->execute();
    }

    public function updateUser($id, $nama, $username, $password, $level) {

        if (!empty($password)) {
            $stmt = $this->db->prepare("
                UPDATE user 
                SET nama=?, username=?, password=?, level=? 
                WHERE id=?
            ");
            $stmt->bind_param('ssssi', $nama, $username, $password, $level, $id);
        } else {
            $stmt = $this->db->prepare("
                UPDATE user 
                SET nama=?, username=?, level=? 
                WHERE id=?
            ");
            $stmt->bind_param('sssi', $nama, $username, $level, $id);
        }

        return $stmt->execute();
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}