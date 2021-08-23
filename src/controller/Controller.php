<?php

    require_once('Router.php');
    require_once('view/PublicView.php');
    require_once('model/WaterbottleStorage.php');
    require_once('model/WaterbottleBuilder.php');
    require_once('model/CommentStorage.php');
    require_once('Authentification/Account.php');
    require_once('Authentification/AuthenticationManager.php');

    class Controller{

        private $view;
        private $waterbottleStorage;
        private $commentStorage;
        private $accountStorage;
        private $authenticationManager;

        public function __construct(Router &$router, PublicView $view, WaterbottleStorage &$waterbottleStorage, CommentStorage &$commentStorage, AccountStorage &$accountStorage, AuthenticationManager &$authenticationManager){
            $this->view = $view;
            $this->waterbottleStorage = $waterbottleStorage;
            $this->commentStorage = $commentStorage;
            $this->accountStorage = $accountStorage;
            $this->authenticationManager = $authenticationManager;
        }

        public function showHome(){
            $this->view->makeHomePage();
        }

        public function showList(int $n=-1, $i=0){
            if($n > 0){
                if($i < 0){
                    $i = 0;
                }
                do{
                    $waterbottles = $this->waterbottleStorage->readAll($n, $i);
                    $i--;
                }while(count($waterbottles) == 0 && $i >= 0);
                $i++;
                $this->view->makePartialListPage($waterbottles, $n, $i);
            }else{
                $waterbottles = $this->waterbottleStorage->readAll();
                $this->view->makeListPage($waterbottles);
            }
        }

        public function showInformation($id){
            $waterbottle = null;
            try{
                $waterbottle = $this->waterbottleStorage->read($id);
                $comments = $this->commentStorage->readAll($id);
                $emptyArray = [];
                $commentBuilder = new CommentBuilder($emptyArray);
                $this->view->makeWaterbottlePage($waterbottle, $id, $comments, $commentBuilder);
            }catch(Exception $e){
                $this->view->makeUnknownWaterbottlePage();
            }
        }

        public function postComment(array $post, $id){
            $commentBuilder = new CommentBuilder($post);
            if($commentBuilder->isValid()){
                $commentBuilder->setWriter($this->authenticationManager->getLogin());
                $commentBuilder->setWaterbottle($id);
                $comment = $commentBuilder->createComment();
                $this->commentStorage->create($comment);
                $this->view->makeCommentPostedRedirect($id);
            }else{
                try{
                    $comments = $this->commentStorage->readAll($id);
                    $this->view->makeWaterbottlePage($waterbottle, $id, $comments, $commentBuilder);
                }catch(Exception $e){
                    $this->view->makeErrorPage($e);
                }
            }
        }

        public function showWaterbottleCreationPage(){
            $emptyArray = [];
            $waterbottleBuilder = new WaterbottleBuilder($emptyArray);
            $this->view->makeWaterbottleCreationPage($waterbottleBuilder);
        }

        public function saveNewWaterbottle(array $post, array $files){
            $waterbottleBuilder = new WaterbottleBuilder($post,$files);
            if($waterbottleBuilder->isValid()){
                $waterbottleBuilder->setCreator($this->authenticationManager->getLogin());
                $waterbottle = $waterbottleBuilder->createWaterbottle();
                $id = $this->waterbottleStorage->create($waterbottle);
                $this->view->makeWaterbottleCreatedRedirect($id);
            }else{
                $this->view->makeWaterbottleCreationPage($waterbottleBuilder);
            }
        }

        public function askWaterbottleDeletion($id){
            try{
                $waterbottle = $this->waterbottleStorage->read($id);
                if($this->authenticationManager->getLogin() === $waterbottle->getCreator()){
                    $this->view->makeWaterbottleDeletionPage($id);
                }else{
                    $this->view->makeAccessDeniedPage();
                }
            }catch(Exception $e){
                $this->view->makeErrorPage($e);
            }
        }

        public function deleteWaterbottle($id){
            try{
                $waterbottle = $this->waterbottleStorage->read($id);
                if($this->authenticationManager->getLogin() === $waterbottle->getCreator()){
                    if($this->waterbottleStorage->delete($id)){
                        $this->commentStorage->deleteAll($id);
                        $this->view->makeWaterbottleDeletedRedirect();
                    }else{
                        $this->showInformation($id);
                    }
                }else{
                    $this->view->makeAccessDeniedPage();
                }
            }catch(Exception $e){
                $this->view->makeErrorPage($e);
            }
        }

        public function editWaterbottle($id){
            $waterbottle = $this->waterbottleStorage->read($id);
            if($this->authenticationManager->getLogin() === $waterbottle->getCreator()){
                $waterbottleData = $waterbottle->toArray();
                $waterbottleBuilder = new WaterbottleBuilder($waterbottleData);
                $this->view->makeWaterbottleEditionPage($waterbottleBuilder, $id);
            }else{
                $this->view->makeAccessDeniedPage();
            }
        }

        public function updateWaterbottle(array &$post, $id){
            $waterbottleBuilder = new WaterbottleBuilder($post);
            if($waterbottleBuilder->isValid()){
                $waterbottle = $this->waterbottleStorage->read($id);
                if($waterbottle->getCreator() === $this->authenticationManager->getLogin()){
                    $waterbottleBuilder->updateWaterbottle($waterbottle);
                    $this->waterbottleStorage->update($waterbottle, $id);
                    $this->view->makeWaterbottleUpdatedRedirect($id);
                }else{
                    $this->view->makeAccessDeniedPage();
                }
            }else{
                $this->view->makeWaterbottleEditionPage($waterbottleBuilder, $id);
            }
        }

        public function showRegisterPage(){
            $emptyArray = [];
            $accountBuilder = new AccountBuilder($emptyArray);
            $this->view->makeRegisterPage($accountBuilder);
        }

        public function showConnectionPage(){
            $this->view->makeLoginPage();
        }

        public function showDeconnectionPage(){
            $this->view->makeLogoutPage();
        }

        public function showAbout(){
            $this->view->makeAboutPage();
        }

        public function showAccountPage($login){
            try{
                $account = $this->accountStorage->read($login);
                $this->view->makeAccountPage($account);
            }catch(Exception $e){
                $this->view->makeErrorPage($e);
            }
        }

        public function show404()
        {
            $this->view->make404();
        }

        public function accountCreation(array $post)
        {
            $accountBuilder = new AccountBuilder($post);
            if($accountBuilder->isValid()){
                $account = $accountBuilder->createAccount();
                $alreadyUsed = true;
                try{
                    $this->accountStorage->read($account->getLogin());
                }catch(Exception $e){
                    $alreadyUsed = false;
                }
                if($alreadyUsed){
                    $this->view->makeLoginAlreadyUsedRedirect();
                }else{
                    $this->accountStorage->create($account);
                    $this->view->makeRegisteredRedirect();
                }
            }else{
                $this->view->makeRegisterPage($accountBuilder);
            }
        }

        public function connection($login, $password){
            if($this->authenticationManager->authenticate($login, $password)){
                $this->view->makeLoginRedirect();
            }else{
                $this->view->makeWrongPasswordRedirect();
            }
        }

        public function deconnection(){
            $this->authenticationManager->disconnect();
            $this->view->makeLogoutRedirect();
        }
    }
