<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use Exception;

class DatabaseService {

    protected $pdo;

    public function __construct(Connection $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllForums() {
        $nameArray = [];
        $query     = "SELECT forumName FROM forum";
        $stm       = $this->pdo->prepare($query);
        if ($stm && $stm->execute() && $result    = $stm->fetch(PDO::FETCH_ASSOC)) {
            foreach ($result as $dataset) {
                $nameArray[] = $dataset['forumName'];
            }
        }
        return $nameArray;
    }

    public function userLogin() {
        $username = $_POST['name'];
        $password = $_POST['password'];
        $query    = "SELECT * FROM student WHERE name=:name and status != 0";
        $stm      = $this->pdo->prepare($query);
        if ($stm && $stm->execute([':name' => $username]) && $result   = $stm->fetch(PDO::FETCH_ASSOC) && $result && password_verify($password, $result['password'])) {
            return $result;
        }
        else {
            throw new Exception('Anmeldung fehlgeschlagen!');
        }
    }

    public function createNewAccount($name, $email, $password, $color) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query          = 'INSERT INTO student (studentName, password, email, color, status) VALUES(:name, :password, :email ,:color, 0)';
            $filterdEmail   = filter_var($email, FILTER_VALIDATE_EMAIL);
            $valueArray     = [
                ':name'     => $name,
                ':password' => $hashedPassword,
                ':email'    => $filterdEmail,
                ':color'    => $color
            ];
            dump($this->pdo);
            dump($stm            = $this->pdo->prepare($query));
            dump($stm);
            if ($name && $this->isHexColor($color) && $filterdEmail && $stm && $stm->execute($valueArray)) {
                return true;
            }
            else {
                
                throw new Exception('Anmeldung fehlgeschlagen!');
            }
        }
        catch (Exception $e) {
            return false;
        }
    }

    public function getUserByName($name) {
        $query = 'SELECT * FROM student WHERE name=:name';
        $stm   = $this->pdo->prepare($query);
        if ($stm && $stm->execute([':name' => $name])) {
            return $stm->fetch(PDO::FETCH_ASSOC);
        }
        else {
            throw new Exception('User-Daten konnten nicht gefunden werden');
        }
    }

    public function isHexColor($color) {
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
    }

}
