<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use App\Services\DatabaseService;
use Exception;

class StudyboardControler extends AbstractController {

    protected $PDO;

    public function __construct(Connection $conection) {
        try {
            $this->PDO = $conection;
        }
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     *
     * @Route("/", name="login")
     */
    public function loginPage() {
        return $this->render('login.html.twig');
    }

    /**
     * 
     * @Route("/loginValidation", name="loginValidation")
     */
    public function loginValidation() {
        try {
            $databaseService = new DatabaseService($this->PDO);
            $userdata        = $databaseService->userLogin();
            if ($this->startNewSession($userdata)) {
                return $this->redirect('/forumTable');
            }
            else {
                throw new Exception('Anmeldung gescheitert');
            }
        }
        catch (Exception $e) {
            $usernameExtension = (key_exists('name', $_POST)) ? '&name=' . filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $this->redirect('/login?er=1' . $usernameExtension);
        }
    }

    /**
     * @Route("/logoutPage", name="logoutPage")
     */
    public function logoutPage() {
        if($this->userIsLoggedIn()) {
            $this->render('logout.html.twig');
        }
        else {
            $this->userLogout();
            $this->redirect('/');
        }
        
    }
    
    
    /**
     * @Route("/newAccount", name="newAccount")
     */
    public function newAccount() {
        return $this->render('createNewUser.html.twig');
    }

    /**
     * @Route("/createNewAccount", name="createNewAccount")
     */
    public function createNewAccount(DatabaseService $database) {
        $er = '';
        try {
            $name     = (key_exists('name', $_POST)) ? filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $email    = (key_exists('email', $_POST)) ? filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $password = (key_exists('password', $_POST)) ? filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $color    = (key_exists('color', $_POST)) ? filter_var($_POST['color'], FILTER_SANITIZE_SPECIAL_CHARS) : '#ffffff';
            if ($database->createNewAccount($name, $email, $password, $color)) {
                $this->addFlash('fb', 'Account wurde erstellt!');
                $er = 'succes';
            }
            else {
                throw new Exception('Account konnte nicht erstellt werden');
                $er = 'er';
            }
        }
        catch (Exception $e) {
            $this->addFlash('er', 'Account konnte nicht erstellt werden!');
        }
        return $this->redirect('/?er='.$er);
    }

    /**
     * 
     * @Route("/logout", name="logout")
     */
    public function logout() {
        session_start();
        $this->userLogout();
        $this->redirect('/');
    }

    /**
     * 
     * @Route("/forumTable", name="forumTable")
     */
    public function forumTable(DatabaseService $database) {
        if(!$this->userIsLoggedIn()) {
            $this->redirect("index.html.twig");
        }
        else {
            $forumlist = $database->getAllForums();
            $this->render('forums', ['forums' => $forumlist]);
        }
    }

    /**
     * 
     * @Route("/forum/{forumName}", name="forum")
     */
    public function forum($forumName) {
        
    }

    /**
     * 
     * @Route("/forumApi/{forumName}", name="forumApi")
     */
    public function forumApi($forumName) {
        
    }

    /**
     * 
     * @Route("/userList", name="userList")
     */
    public function userList($forumName) {
        
    }

    /**
     * 
     * @Route("/changeUserAccount/{userId}", name="changeUserAccount")
     */
    public function changeUserAccount($userId) {
        
    }

    public function startNewSession($userdata) {
        if ($userdata) {
            session_start();
            $_SESSION['userId']    = $userdata['studentId'];
            $_SESSION['userName']  = $userdata['studentName'];
            $_SESSION['userEmail'] = $userdata['email'];
            $_SESSION['userColor'] = $userdata['color'];
            $_SESSION['userAdmin'] = (2 == $userdata['status']) ? true : false;
            $this->updateSecurityToken();
            return true;
        }
        else {
            return false;
        }
    }

    public function userIsLoggedIn() {
        session_start();
        if (isset($_SESSION['cookie']) && isset($_COOKIE['studyboard']) && $_SESSION['cookie'] == filter_var($_COOKIE['studyboard'], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $this->updateSecurityToken();
            return true;
        }
        else {
            $this->userLogout();
            return False;
        }
    }

    public function userLogout() {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600);
        }
        if (isset($_COOKIE['studyboard'])) {
            setcookie('studyboard', '', time() - 3600);
        }
        $_SESSION = [];
        session_destroy();
    }

    public function updateSecurityToken() {
        $randString         = bin2hex(random_bytes(64));
        $cookieLifetime     = time() + 1200;
        setcookie('studyboard', $randString, $cookieLifetime);
        $_SESSION['cookie'] = $randString;
    }

    public function isAdmin() {
        return (2 == $_SESSION['userAdmin']);
    }

}
