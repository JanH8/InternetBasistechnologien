<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;

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
        $result = $this->getUserByName($username);
        if ($result && $result['status'] && password_verify($password, $result['password'])) {
            return $result;
        }
        else {
            dump($result);
            dump(password_verify($password, $result['password']));
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
            $stm            = $this->pdo->prepare($query);
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

    public function registerNewAccount() {
        $name     = (key_exists('name', $_POST)) ? filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
        $email    = (key_exists('email', $_POST)) ? filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
        $password = (key_exists('password', $_POST)) ? filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
        $color    = (key_exists('color', $_POST)) ? filter_var($_POST['color'], FILTER_SANITIZE_SPECIAL_CHARS) : '#ffffff';
        return $this->createNewAccount($name, $email, $password, $color);
    }

    public function getUserByName($name) {
        $query = 'SELECT * FROM student WHERE studentName=:name';
        $stm   = $this->pdo->prepare($query);
        if ($stm && $stm->execute([':name' => $name])) {
            return $stm->fetch(PDO::FETCH_ASSOC);
        }
        else {
            return FALSE;
        }
    }

    public function isHexColor($color) {
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
    }
    
    public function makeNewEntry($forum, $message, $userId) {
        $forumId = $this->getForumIdByName($forum);
        $query = 'INSERT INTO post (tstmp, author, forumId, contend) '
                . 'VALUES (:tstmp, :author, :forumId, :content)';
        $valueArray = [
            ':tstmp' => time(),
            ':author' => $userId,
            ':forumId' => $forumId,
            ':content' => $message
        ];
        $stm = $this->pdo->prepare($query);
        if($forumId && $stm && $stm->execute($valueArray)) {
            return true;
        }
        else {
            return False;
        }
    }

    public function getForumIdByName($name) {
        $query = "SELECT forumId FROM forum WHERE forumName=:name";
        $stm = $this->pdo->prepare($query);
        $valueArray = [':name' => $name];
        if($stm && $stm->execute($valueArray)) {
            $result = $stm->fetch(PDO::FETCH_ASSOC);
            return (isset($result['forumId']))?$result['forumId']:False;
        }
        else {
            return False;
        }
    }
    
}
