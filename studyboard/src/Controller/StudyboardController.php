<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use App\Services\DatabaseService;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class StudyboardController extends AbstractController {

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
     * @Route("/", name="login")
     */
    public function loginPage() {
        return $this->render('login.html.twig');
    }

    /**
     * @Route("/loginValidation", name="loginValidation")
     */
    public function loginValidation() {
        try {
            $databaseService = new DatabaseService($this->PDO);
            $userdata        = $databaseService->userLogin();
            if ($this->createNewSession($userdata)) {
                return $this->redirect('/home');
            }
            else {
                throw new Exception('Anmeldung gescheitert');
            }
        }
        catch (Exception $e) {
            $username = (key_exists('name', $_POST)) ? '?name=' . filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            return $this->redirect('/' . $username);
        }
    }

    /**
     * @Route("/newAccount", name="newAccount")
     */
    public function newAccount() {
        return $this->render('createNewUser.html.twig');
    }

    /**
     * @Route("/impressum", name="newAccount")
     */
    public function impressum() {
        return $this->render('impressum.html.twig');
    }

    /**
     * @Route("/createNewAccount", name="createNewAccount")
     */
    public function createNewAccount(DatabaseService $database) {
        if ($database->registerNewAccount()) {
            $this->addFlash('fb', 'Account wurde erstellt!');
        }
        else {
            $this->addFlash('er', 'Account konnte nicht erstellt werden!');
        }
        return $this->redirect('/');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {
        $session = $this->startSession();
        $this->userLogout($session);
        return $this->redirect('/');
    }

    /**
     * @Route("/home", name="home")
     */

    public function home() {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            return $this->render('home.html.twig');
        }
        else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/forumTable", name="emptyForum")
     */
    public function emptyForumTable() {
        return $this->render("blank.html.twig");
    }

    /**
     * @Route("/forumTable/{forumName}", name="forumTable")
     */
    public function forumTable($forumName, DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $currentUser = $session->get('userName');
            $messages    = $database->getMessagesByForum($forumName);
            $twigArray   = [
                'user'     => $currentUser,
                'messages' => $messages
            ];
            return $this->render("forum.html.twig", $twigArray);
        }
        else {
            
        }
    }

    /**
     * @Route("/forum", name="foren")
     */
    public function foren(DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $forumlist = $database->getAllForums();
            $twigArray = [
                'forums'       => $forumlist,
                'currentForum' => '',
            ];
            return $this->render('foren.html.twig', $twigArray);
        }
        else {
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/forum/{forumName}", name="forum")
     */
    public function forum($forumName, DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $forumlist = $database->getAllForums();
            $forumId   = $database->getForumIdByName($forumName);
            $twigArray = [
                'forums'       => $forumlist,
                'currentForum' => $forumId ? $forumName : '',
            ];
            return $this->render("foren.html.twig", $twigArray);
        }
        else {
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/createNewForum", name="createNewForum")
     */
    public function createNewForum(DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            if (key_exists('forumName', $_POST)) {
                $userId    = $session->get('userId');
                $forumName = filter_var($_POST['forumName'], FILTER_SANITIZE_SPECIAL_CHARS);
                if ($database->createNewForum($forumName, $userId)) {
                    return $this->redirect('/forum/'.$forumName);
                }
            }
            return $this->render("newForum.html.twig");
        }
        else {
            return $this->redirect("/home");
        }
    }

    /**
     * @Route("/forumApi/{forumName}", name="forumApi")
     */
    public function forumApi(String $forumName, DatabaseService $database) {
        $session = $this->startSession();
        try {
            if (!$this->userIsLoggedIn($session) || !in_array($forumName, $database->getAllForums()) || !isset($_POST['message'])) {
                throw new Exception();
            }
            $userId = $session->get('userId');
            if ($database->makeNewEntry($forumName, $_POST['message'], $userId)) {
                return new Response('', 200);
            }
            else {
                return new Response('', 400);
            }
        }
        catch (Exception $e) {
            return new Response('', 403);
        }
    }

    /**
     * @Route("/userList", name="userList")
     */
    public function userList() {
        
    }

    /**
     * @Route("/changeUserAccount/{userId}", name="changeUserAccount")
     */
    public function changeUserAccount($userId) {
        
    }

    public function startSession() {
        $session = new Session();
        return $session;
    }

    public function createNewSession(Array $userdata) {
        if ($userdata) {
            $session = $this->startSession();
            $session->set('userId', $userdata['studentId']);
            $session->set('userName', $userdata['studentName']);
            $session->set('userEmail', $userdata['email']);
            $session->set('userColor', $userdata['color']);
            $session->set('userAdmin', (2 == $userdata['status']) ? true : false);
            return $session;
        }
        else {
            return false;
        }
    }

    public function userLogout(Session $session) {
        $session->invalidate();
    }

    public function isAdmin(Session $session) {
        return ($session->get('userAdmin'));
    }

    public function userIsLoggedIn(Session $session) {
        return $session->has('userId');
    }

}
