<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;

class DatabaseService
{

    protected $pdo;

    public function __construct(Connection $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllForums()
    {
        $nameArray = [];
        $query = "SELECT forumName FROM forum "
            . "ORDER BY forumId DESC";
        $stm = $this->pdo->prepare($query);
        if ($stm && $stm->execute() && $result = $stm->fetchAll(PDO::FETCH_ASSOC)) {
            foreach ($result as $dataset) {
                $nameArray[] = $dataset['forumName'];
            }
        }
        return $nameArray;
    }


    public function getAllNormalUsers()
    {
        $query = "SELECT * FROM student WHERE status != 2";
        $stm = $this->pdo->prepare($query);
        if ($stm && $stm->execute() && $result = $stm->fetchAll(PDO::FETCH_ASSOC)) {
            return $result;
        }
        return [];
    }

    public function getMessagesByForum($forumName)
    {
        $messagesArray = [];
        $query = "SELECT * FROM post "
            . "INNER JOIN forum on post.forumId = forum.forumId "
            . "INNER JOIN student on post.author = student.studentId "
            . "WHERE forumName=:name "
            . "ORDER BY post.postId";

        $stm = $this->pdo->prepare($query);
        if ($stm && $stm->execute([':name' => $forumName]) && $result = $stm->fetchAll(PDO::FETCH_ASSOC)) {
            $messagesArray = $result;
        }
        return $messagesArray;
    }

    public function userLogin()
    {
        $username = $_POST['name'];
        $password = $_POST['password'];
        $result = $this->getUserByName($username);
        if ($result && $result['status'] && password_verify($password, $result['password'])) {
            return $result;
        } else {
            throw new Exception('Anmeldung fehlgeschlagen!');
        }
    }

    public function createNewAccount($name, $email, $password, $color)
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = 'INSERT INTO student (studentName, password, email, color, status) VALUES(:name, :password, :email ,:color, 0)';
            $filterdEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            $valueArray = [
                ':name' => $name,
                ':password' => $hashedPassword,
                ':email' => $filterdEmail,
                ':color' => $color
            ];
            $stm = $this->pdo->prepare($query);
            if ($name && $this->isHexColor($color) && $filterdEmail && $stm && $stm->execute($valueArray)) {
                return true;
            } else {
                throw new Exception('Anmeldung fehlgeschlagen!');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateEmail($userID, $email)
    {
        try {
            $query = 'UPDATE student SET email =:email WHERE studentId = :id';

            $stm = $this->pdo->prepare($query);
            if ($userID && $stm && $stm->execute([':email' => $email, ':id' => $userID])) {
                return true;
            } else {
                throw new Exception('Anmeldung fehlgeschlagen!');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function changeEmail($userID)
    {
        $email = (key_exists('email', $_POST)) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        return ($email)?$this->updateEmail($userID, $email):false;
    }

    public function updatePassword($userID, $password)
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = 'UPDATE student SET password =:password WHERE studentId = :id';
            $stm = $this->pdo->prepare($query);
            if ($userID && $stm && $stm->execute([':password' => $hashedPassword, ':id' => $userID])) {
                return true;
            } else {
                throw new Exception('Anmeldung fehlgeschlagen!');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function changePassword($userID)
    {
        $password = (key_exists('newPassword', $_POST)) ? $_POST['newPassword'] : '';
        $oldPassword = (key_exists('pwOld', $_POST)) ? $_POST['pwOld'] : '';
        if($password && $oldPassword && $this->verifyOldPassword($userID,$oldPassword)) {
            return $this->updatePassword($userID, $password);
        }
        else {
            return false;
        }
    }

    public function updateColor($userID, $color)
    {
        try {
            $query = 'UPDATE student SET color =:color WHERE studentId = :id';

            $stm = $this->pdo->prepare($query);
            if ($userID && $this->isHexColor($color) && $stm && $stm->execute([':color' => $color, ':id' => $userID])) {
                return true;
            } else {
                throw new Exception('Anmeldung fehlgeschlagen!');
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function changeColor($userID)
    {
        $color = (key_exists('color', $_POST)) ? filter_var($_POST['color'], FILTER_SANITIZE_SPECIAL_CHARS) : '#ffffff';
        return $this->updateColor($userID, $color);
    }

    public function getCreatorByForumId($forumId)
    {
        $queryForId = "SELECT * FROM forum INNER JOIN student on forum.creator = student.studentId WHERE forumId=:id";
        $stm = $this->pdo->prepare($queryForId);
        if ($stm && $stm->execute([':id' => $forumId]) && $result = $stm->fetch(PDO::FETCH_ASSOC)) {
            return $result['studentName'];
        }
        return null;
    }

    public function registerNewAccount()
    {
        $name = (key_exists('name', $_POST)) ? filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
        $email = (key_exists('email', $_POST)) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $password = (key_exists('password', $_POST)) ? $_POST['password'] : '';
        $color = (key_exists('color', $_POST)) ? filter_var($_POST['color'], FILTER_SANITIZE_SPECIAL_CHARS) : '#ffffff';
        return $this->createNewAccount($name, $email, $password, $color);
    }

    public function getUserByName($name)
    {
        $query = 'SELECT * FROM student WHERE studentName=:name';
        $stm = $this->pdo->prepare($query);
        if ($stm && $stm->execute([':name' => $name])) {
            return $stm->fetch(PDO::FETCH_ASSOC);
        } else {
            return FALSE;
        }
    }

    public function getUserById($id)
    {
        $query = 'SELECT * FROM student WHERE studentId=:studentId';
        $stm = $this->pdo->prepare($query);
        if ($stm && $stm->execute([':studentId' => $id])) {
            return $stm->fetch(PDO::FETCH_ASSOC);
        } else {
            return FALSE;
        }
    }

    public function isHexColor($color)
    {
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
    }

    public function createNewForum($forumName, $userId)
    {
        $forumNameLength = strlen($forumName);
        $forumId = $this->getForumIdByName($forumName);
        $query = 'INSERT INTO forum (creator, forumName) '
            . 'VALUES (:creator, :forumName)';
        $valueArray = [
            ':creator' => $userId,
            ':forumName' => $forumName
        ];
        $stm = $this->pdo->prepare($query);
        if (!$forumId && $forumNameLength && 30 > $forumNameLength && $stm && $stm->execute($valueArray)) {
            return true;
        } else {
            return False;
        }
    }

    public function makeNewEntry($forum, $message, $userId)
    {
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
        if ($forumId && $stm && $stm->execute($valueArray)) {
            return true;
        } else {
            return False;
        }
    }

    public function getForumIdByName($name)
    {
        $query = "SELECT forumId FROM forum WHERE forumName=:name";
        $stm = $this->pdo->prepare($query);
        $valueArray = [':name' => $name];
        if ($stm && $stm->execute($valueArray)) {
            $result = $stm->fetch(PDO::FETCH_ASSOC);
            return (isset($result['forumId'])) ? $result['forumId'] : False;
        } else {
            return False;
        }
    }

    public function getPostById($id)
    {
        $query = "SELECT * FROM post INNER JOIN student on student.studentId = post.author WHERE postId=:id ";
        $stm = $this->pdo->prepare($query);
        $valueArray = [':id' => $id];
        if ($stm && $stm->execute($valueArray)) {
            $result = $stm->fetch(PDO::FETCH_ASSOC);
            return (isset($result)) ? $result : False;
        } else {
            return False;
        }
    }

    public function deletePostById($id)
    {
        $query = 'DELETE FROM post WHERE postId=:id';
        $stm = $this->pdo->prepare($query);
        $valueArray = [':id' => $id];
        if ($id && $stm && $stm->execute($valueArray)) {
            return true;
        } else {
            return False;
        }
    }

    public function alterUserStatus(int $id, int $status):bool
    {
        $query = 'UPDATE student SET status=:status WHERE studentId=:id';
        $stm = $this->pdo->prepare($query);
        $valueArray = [':id' => $id,':status' => $status];
        if ($stm && $stm->execute($valueArray)) {
            return true;
        } else {
            return False;
        }
    }


    public function verifyOldPassword($id, $oldPassword):bool
    {
        $result = $this->getUserById($id);
        if ($result &&  password_verify($oldPassword, $result['password'])) {
            return true;
        } else {
            return false;
        }
    }

}
