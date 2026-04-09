<?php

require_once __DIR__ . '/../config/koneksi.php';

class CekLogin_model {

    private $koneksi;

    public function __construct() {
        global $koneksi;
        $this->koneksi = $koneksi;
    }

    // Ambil user berdasarkan username
    public function getUserByUsername($username) {

        $stmt = $this->koneksi->prepare(
            "SELECT id, nama, username, password, level 
             FROM user 
             WHERE username = ? 
             LIMIT 1"
        );

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }
}