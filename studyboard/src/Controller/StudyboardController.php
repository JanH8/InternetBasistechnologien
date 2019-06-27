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
        } catch (PDOException $e) {
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
            $userdata = $databaseService->userLogin();
            if ($this->createNewSession($userdata)) {
                return $this->redirect('/home');
            } else {
                throw new Exception('Anmeldung gescheitert');
            }
        } catch (Exception $e) {
            $username = (key_exists('name', $_POST)) ? '?name=' . filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            return $this->redirect('/' . $username);
        }
    }

    /**
     * @Route("/deletePost/{forumId}/{postId}", name="deletePost")
     */
    public function deletePost($postId, $forumId, Session $session, DatabaseService $database)
    {
        $userId = $session->get('userId');
        $userAdmin = $session->get('userAdmin');
        $forum = $database->getForumById($forumId);
        $post = $database->getPostById($postId);
        if (isset($post)) {
            if ($post["author"] == $userId || $userAdmin || $forum["creator"] == $userId) {
                $database->deletePostById($postId);
                return $this->render('back.html.twig');
            } else {
                return $this->redirect('/logout');
            }
        }
    }

    /**
     * @Route("/newAccount", name="newAccount")
     */
    public function newAccount() {
        return $this->render('createNewUser.html.twig');
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function settings(DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            if($this->isAdmin($session)) {
                return $this->redirect('/userList');
            }
            $username = $session->get("userName");
            $user = $database->getUserByName($username);
            $twigArray = [
                'user' => $user,
            ];
            return $this->render('settings.html.twig', $twigArray);
        } else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }



    /**
     * @Route("/settings/{id}", name="adminSettings")
     */
    public function adminSettings(DatabaseService $database, $id) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session) && $this->isAdmin($session)) {
            $user = $database->getUserById($id);
            $twigArray = [
                'user' => $user,
            ];
            return $this->render('adminSettings.html.twig', $twigArray);
        } else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }


    /**
     * @Route("/deleteAccount", name="deleteAccount")
     */
    public function deleteAccount(DatabaseService $database)
    {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $userId = $session->get('userId');
            $database->alterUserStatus($userId,0);
            $this->addFlash('fd','Konto wurde erfolgreich deaktiviert!');
        }
        $this->userLogout($session);
        return $this->redirect("/");
    }


    /**
     * @Route("/alterUserPassword", name="alterUserPassword")
     */
    public function alterUserPassword(DatabaseService $database)
    {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session) &&
            $this->isAdmin($session) &&
            isset($_POST['userId']) &&
            isset($_POST['newPassword']) &&
            preg_match('/^[0-9]+$/',$_POST['userId'])
        ) {
            $userPassword = intval($_POST['newPassword']);
            $userId = intval($_POST['userId']);
            $database->updatePassword($userId, $userPassword);
            return $this->redirect('/userList');
        }
        else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }


    /**
     * @Route("/alterUserStatus", name="alterUserStatus")
     */
    public function alterUserStatus(DatabaseService $database)
    {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session) &&
            $this->isAdmin($session) &&
            isset($_POST['userId']) &&
            isset($_POST['userStatus']) &&
            preg_match('/^[0-9]+$/',$_POST['userId']) &&
            preg_match('/^[0-2]{1}$/',$_POST['userStatus'])
        ) {
            $userStatus = intval($_POST['userStatus']);
            $userId = intval($_POST['userId']);
            $database->alterUserStatus($userId, $userStatus);
            return $this->redirect('/userList');
        }
        else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }


    /**
     * @Route("/impressum", name="impressum")
     */
    public function impressum() {
        return $this->render('impressum.html.twig');
    }

    /**
     * @Route("/changeColor", name="changeColor")
     */
    public function changeColor(DatabaseService $database) {
        $session = $this->startSession();
        $userId = $session->get('userId');
        if($this->isAdmin($session) && isset($_POST['userId']) && preg_match('/^[0-9]+$/', $_POST['userId'])) {
            $userId = intval($_POST['userId']);
        }
        if ($database->changeColor($userId)) {
            $this->addFlash('fb', 'Farbe wurde geändert!');
        } else {
            $this->addFlash('er', 'Farbe konnte nicht geändert werden!');
        }
        if($this->isAdmin($session)) {
            return $this->redirect('/userList');
        }
        return $this->redirect('/home');
    }

    /**
     * @Route("/changeEmail", name="changeEmail")
     */
    public function changeEmail(DatabaseService $database) {
        $session = $this->startSession();
        $userId = $session->get('userId');
        if($this->isAdmin($session) && isset($_POST['userId']) && preg_match('/^[0-9]+$/', $_POST['userId'])) {
            $userId = intval($_POST['userId']);
        }
        if ($database->changeEmail($userId)) {
            $this->addFlash('fb', 'Account wurde erstellt!');
        } else {
            $this->addFlash('er', 'Account konnte nicht erstellt werden!');
        }
        if($this->isAdmin($session)) {
            return $this->redirect('/userList');
        }
        return $this->redirect('/home');
    }

    /**
     * @Route("/changePassword", name="/changePassword")
     */
    public function changePassword(DatabaseService $database) {
        $session = $this->startSession();
        $userId = $session->get('userId');
        if ($database->changePassword($userId)) {
            $this->addFlash('fb', 'Account wurde erstellt!');
        } else {
            $this->addFlash('er', 'Account konnte nicht erstellt werden!');
        }
        return $this->redirect('/');
    }

    /**
     * @Route("/createNewAccount", name="createNewAccount")
     */
    public function createNewAccount(DatabaseService $database) {
        if ($database->registerNewAccount()) {
            $this->addFlash('fb', 'Account wurde erstellt!');
        } else {
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
    public function home(DatabaseService $database)
    {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $userId = $session->get('userId');
            $abos = $database->getAbosByUser($userId);
            $notifications = $database->getNotificationsForUser($userId);
            $twigArray = [
                'abos' => $abos,
                'notifications' => $notifications
            ];
            return $this->render('home.html.twig', $twigArray);
        } else {
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
     * @Route("/forumTable/{forumId}", name="forumTable")
     */
    public function forumTable($forumId, DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $currentUser = $session->get('userName');
            $currentUserId = $session->get('userId');
            $abos = $database->getAbosByUser($currentUserId);
            $database->createTimestamp($forumId, $currentUserId);
            $messages = $database->getMessagesByForum($forumId);
            $id = $session->get('userId');
            $isAdmin = $session->get('userAdmin');
            $twigArray = [
                'id' => $id,
                'isAdmin' => $isAdmin,
                'messages' => $messages,
                'user' => $currentUser,
                'messages' => $messages,
                'abos'=> $abos,
                'forumId'=>$forumId
            ];
            return $this->render("forum.html.twig", $twigArray);
        } else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/forum", name="foren")
     */
    public function foren(DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $forumlist = $database->getAllForums();
            if(sizeof($forumlist)>0){
                var_dump($forumlist);
                $forum = $forumlist[0];
                var_dump($forum);
                $url = "/forum/".$forum['forumId'];
                var_dump($url);
                return $this->redirect($url);
            }
            $userId = $session->get('userId');
            $notifications = $database->getNotificationsForUser($userId);
            $twigArray = [
                'forums' => $forumlist,
                'currentForum' => '',
                'notifications' => $notifications
            ];
            return $this->render('foren.html.twig', $twigArray);
        } else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/newAbo/{forumId}", name="newAbo")
     */
    public function newAbo($forumId, DatabaseService $database)
    {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $id = $session->get('userId');
            $database->createNewAbo($forumId, $id);
        }
        return $this->render('back.html.twig');
    }
    /**
     * @Route("/deleteAbo/{forumId}", name="deleteAbo")
     */
    public function deleteAbo($forumId, DatabaseService $database)
    {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $id = $session->get('userId');
            $db = $database->deleteAbo($forumId, $id);
        }
        return $this->render('back.html.twig');
    }

    /**
     * @Route("/forum/{forumId}", name="forum")
     */
    public function forum($forumId, DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session)) {
            $forumlist = $database->getAllForums();
            $userId = $session->get('userId');
            $notifications = $database->getNotificationsForUser($userId);
            $twigArray = [
                'forums' => $forumlist,
                'currentForum' => $forumId,
                'notifications' => $notifications
            ];
            return $this->render("foren.html.twig", $twigArray);
        } else {
            $this->userLogout($session);
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
                $userId = $session->get('userId');
                $forumName = filter_var($_POST['forumName'], FILTER_SANITIZE_SPECIAL_CHARS);
                if ($database->createNewForum($forumName, $userId)) {
                    return $this->redirect('/forum/');
                }
            }
            return $this->render("newForum.html.twig");
        } else {
            $this->userLogout($session);
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/forumApi/{forumId}", name="forumApi")
     */
    public function forumApi(String $forumId, DatabaseService $database) {
        $session = $this->startSession();
        try {
            if (!$this->userIsLoggedIn($session)  || !isset($_POST['message'])) {
                throw new Exception();
            }
            $userId = $session->get('userId');
            if ($database->makeNewEntry($forumId, $_POST['message'], $userId)) {
                return new Response('', 200);
            } else {
                return new Response('', 400);
            }
        } catch (Exception $e) {
            return new Response('', 403);
        }
    }

    /**
     * @Route("/userList", name="userList")
     */
    public function userList(DatabaseService $database) {
        $session = $this->startSession();
        if ($this->userIsLoggedIn($session) && $this->isAdmin($session)) {
            $usersArray = $database->getAllUsers();
            $twigArray = [
                'users' => $usersArray
            ];
            return $this->render("userList.html.twig", $twigArray);
        } else {
            return $this->redirect("/home");
        }
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
        } else {
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
