<?php

    require_once('model/WaterbottleStorage.php');
    require_once('model/WaterbottleBuilder.php');
    require_once('model/CommentStorage.php');
    require_once('controller/Controller.php');
    require_once('view/PublicView.php');
    require_once('view/PrivateView.php');
    require_once('Authentification/AccountStorage.php');
    require_once('Authentification/AccountBuilder.php');

    class Router{

        const HOME = 'home';
        const LIST = 'list';
        const CREATE = 'create';
        const EDIT = 'edit';
        const DELETE = 'delete';
        const REGISTER = 'register';
        const CONNECTION = 'connection';
        const DECONNECTION = 'deconnection';
        const ACCOUNT = 'account';
        const ABOUT = 'about';
        const PATH_DELIMITER = '/';

        public function main(WaterbottleStorage &$waterbottleStorage, CommentStorage &$commentStorage, AccountStorage &$accountStorage)
        {
            if(session_status() == PHP_SESSION_NONE){
                session_name('waterbottleSession');
                session_start();
            }

            $feedback = key_exists('feedback', $_SESSION) ? $_SESSION['feedback'] : '';
            $_SESSION['feedback'] = '';

            $authenticationManager = new AuthenticationManager($accountStorage);

            $view = new PublicView($this,$feedback);;
            if($authenticationManager->isConnected()){
                $account = $authenticationManager->getAccount();
                if($account !== null){
                    $view = new PrivateView($this,$feedback,$account);
                }
            }
            $controller = new Controller($this, $view, $waterbottleStorage, $commentStorage, $accountStorage, $authenticationManager);

            if(!key_exists('PATH_INFO', $_SERVER)){
                $_SERVER['PATH_INFO'] = '';
            }
            $path_infos = explode(Router::PATH_DELIMITER, $_SERVER['PATH_INFO']);

            $length = count($path_infos);
            $arg1 = ($length >= 2 && $path_infos[1] !== '') ? $path_infos[1] : Router::HOME;

            // Si l'utilisateur est authentifié, il a accès à ces pages
            if(key_exists('user',$_SESSION))
            {
                switch($arg1){
                    case Router::HOME:
                        $controller->showHome();
                        break;
                    case Router::LIST:
                        if($length >= 3){
                            $n = $path_infos[2];
                            if($length >= 4){
                                $i = $path_infos[3];
                                $controller->showList($n, $i);
                            }else{
                                $controller->showList($n);
                            }
                        }else{
                            $controller->showList();
                        }
                        break;
                    case Router::CREATE:
                        if($_SERVER['REQUEST_METHOD'] === 'GET'){
                            $controller->showWaterbottleCreationPage();
                        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            // var_dump($_FILES);
                            $controller->saveNewWaterbottle($_POST,$_FILES);
                        }
                        break;
                    case Router::REGISTER:
                        /* Faire également les vérifications qu'un compte n'est pas déjà connecté auquel cas
                        on effectue une redirection */
                        if($_SERVER['REQUEST_METHOD'] === 'GET'){
                            $controller->showRegisterPage();
                        } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            $controller->accountCreation($_POST);
                        }
                        break;
                    case Router::CONNECTION:
                        if($_SERVER['REQUEST_METHOD'] === 'GET'){
                            $controller->showConnectionPage();
                        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            $controller->connection($_POST['login'], $_POST['password']);
                        }
                        break;
                    case Router::DECONNECTION:
                        if($_SERVER['REQUEST_METHOD'] === 'GET'){
                            $controller->showDeconnectionPage();
                        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            $controller->deconnection();
                        }
                        break;
                    case Router::ABOUT:
                        $controller->showAbout();
                        break;
                    case Router::ACCOUNT:
                        if($length >= 3){
                            $login = $path_infos[2];
                            $controller->showAccountPage($login);
                        }else{
                            $controller->show404();
                        }
                        break;
                    default:
                        $action = ($length >= 3) ? $path_infos[2] : '';
                        switch($action){
                            case Router::DELETE:
                                if($_SERVER['REQUEST_METHOD'] === 'GET'){
                                    $controller->askWaterbottleDeletion($arg1);
                                }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                                    $controller->deleteWaterbottle($arg1);
                                }
                                break;
                            case Router::EDIT:
                                if($_SERVER['REQUEST_METHOD'] === 'GET'){
                                    $controller->editWaterbottle($arg1);
                                }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                                    $controller->updateWaterbottle($_POST, $arg1);
                                }
                                break;
                            default:
                                if($_SERVER['REQUEST_METHOD'] === 'GET'){
                                    $controller->showInformation($arg1);
                                }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                                    $controller->postComment($_POST, $arg1);
                                }
                                break;
                        }
                        break;
                }
            } else { // Sinon c'est un utilisateur anonyme, il a donc accès à ces pages
                switch($arg1)
                {
                    case Router::HOME:
                        $controller->showHome();
                        break;
                    case Router::LIST:
                        $controller->showList();
                        break;
                    case Router::REGISTER:
                        if($_SERVER['REQUEST_METHOD'] === 'GET'){
                            $controller->showRegisterPage();
                        } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            $controller->accountCreation($_POST);
                        }
                        break;
                    case Router::CONNECTION:
                        if($_SERVER['REQUEST_METHOD'] === 'GET'){
                            $controller->showConnectionPage();
                        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            $controller->connection($_POST['login'], $_POST['password']);
                        }
                        break;
                    case Router::ABOUT:
                        $controller->showAbout();
                        break;
                    default:
                        $controller->show404();
                }
            }
            $view->render();
        }

        public function getFileURL() : string{
            return $_SERVER['SCRIPT_NAME'];
        }

        public function getHomeURL() : string{
            return $this->getFileURL().Router::PATH_DELIMITER.Router::HOME;
        }

        public function getWaterbottleListURL() : string{
            return $this->getFileURL().Router::PATH_DELIMITER.Router::LIST;
        }

        public function getWaterbottlePartialListURL(int $n=10, int $i=0){
            return $this->getWaterbottleListURL().Router::PATH_DELIMITER.$n.Router::PATH_DELIMITER.$i;
        }

        public function getWaterbottleURL($id) : string{
            return $this->getFileURL().Router::PATH_DELIMITER.$id;
        }

        public function getPostCommentURL($waterbottleID) : string{
            return $this->getWaterbottleURL($waterbottleID);
        }

        public function getAccountURL($login) : string{
            return $this->getFileURL().Router::PATH_DELIMITER.Router::ACCOUNT.Router::PATH_DELIMITER.$login;
        }

        public function getWaterbottleCreationURL() : string{
            return $this->getFileURL().Router::PATH_DELIMITER.Router::CREATE;
        }

        public function getWaterbottleSaveURL() : string{
            return $this->getWaterbottleCreationURL();
        }

        public function getWaterbottleDeletionURL($id) : string{
            return $this->getWaterbottleURL($id).Router::PATH_DELIMITER.Router::DELETE;
        }

        public function getWaterbottleAskDeletionURL($id) : string{
            return $this->getWaterbottleDeletionURL($id);
        }

        public function getWaterbottleEditionURL($id) : string{
            return $this->getWaterbottleURL($id).Router::PATH_DELIMITER.Router::EDIT;
        }

        public function getWaterbottleUpdateURL($id) : string{
            return $this->getWaterbottleEditionURL($id);
        }

        public function getRegisterURL(): string
        {
            return $this->getFileURL().Router::PATH_DELIMITER.Router::REGISTER;
        }

        public function getConnectionAttemptURL() : string{
            return $this->getFileURL().Router::PATH_DELIMITER.Router::CONNECTION;
        }

        public function getConnectionURL() : string{
            return $this->getConnectionAttemptURL();
        }

        public function getAskDeconnectionURL() : string{
            return $this->getFileURL().Router::PATH_DELIMITER.Router::DECONNECTION;
        }

        public function getAboutURL() : string
        {
            return $this->getFileURL().Router::PATH_DELIMITER.Router::ABOUT;
        }

        public function getDeconnectionURL() : string{
            return $this->getAskDeconnectionURL();
        }

        public function POSTredirect($url, $feedback)
        {
            $_SESSION['feedback'] = $feedback;
            return header("Location: ".htmlspecialchars_decode($url), true, 303);
        }
    }
?>
