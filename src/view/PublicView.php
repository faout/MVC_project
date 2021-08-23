<?php
    require_once('model/Waterbottle.php');
    require_once('model/WaterbottleBuilder.php');
    require_once('Authentification/AccountBuilder.php');
    require_once('Router.php');

    class PublicView
    {
        protected $router;
        protected $feedback;
        protected $title;
        protected $menu;
        protected $content;

        public function __construct(Router &$router, $feedback){
            $this->router = $router;
            $this->feedback = $feedback;
            $this->menu = $this->getMenu();
        }

        public function getMenu(): array
        {
            return array(
                "Accueil" => $this->router->getHomeURL(),
                "Liste des bouteilles" => $this->router->getWaterbottlePartialListURL(),
                "Créer un compte" => $this->router->getRegisterURL(),
                "Se connecter" => $this->router->getConnectionAttemptURL(),
                "À propos" => $this->router->getAboutURL()
            );
        }

        // ---- allowed pages ----

        public function makeHomePage(){
            $this->title = 'Accueil';
            $this->content = '<h1>Bienvenue sur le site qui référence les marques de bouteilles d\'eau française !</h1>';
        }

        public function makeListPage(array &$waterbottles){
            $this->title = 'Liste des bouteilles :';
            $this->content = '<p>Liste des bouteilles : (voir par <a href="'.$this->router->getWaterbottlePartialListURL(5, 0).'">5</a>, <a href="'.$this->router->getWaterbottlePartialListURL(10, 0).'">10</a>, <a href="'.$this->router->getWaterbottlePartialListURL(15, 0).'">15</a>, ou <a href="'.$this->router->getWaterbottleListURL().'">toutes les bouteilles</a>)';
            $this->content = '<ul class=list>';
            foreach($waterbottles as $id => $waterbottle){
                $this->content .= '<li><figure>';
                $url = $waterbottle->getURLPicture();
                if($url !== null && $url !== ''){
                    $this->content .= '<img src="'.$url.'" alt="'.$waterbottle->getName().'">';
                }
                $this->content .= '<figcaption>'.$waterbottle->getName().'</figcaption>';
                $this->content .= '</figure></li>';
            }
            $this->content .= '</ul><p>';
        }

        public function makePartialListPage(array &$waterbottles, int $n, int $i){
            $this->title = 'Liste des bouteilles';
            $this->content = '<p>Liste des bouteilles (page '.($i+1).' avec '.$n.' bouteilles) : (voir par <a href="'.$this->router->getWaterbottlePartialListURL(5, 0).'">5</a>, <a href="'.$this->router->getWaterbottlePartialListURL(10, 0).'">10</a>, <a href="'.$this->router->getWaterbottlePartialListURL(15, 0).'">15</a>, ou <a href="'.$this->router->getWaterbottleListURL().'">toutes les bouteilles</a>)';
            $this->content .= '<ul=list>';
            foreach($waterbottles as $id => $waterbottle){
                $this->content .= '<li><figure>';
                $url = $waterbottle->getURLPicture();
                if($url !== null && $url !== ''){
                    $this->content .= '<img src="'.$url.'" alt="'.$waterbottle->getName().'">';
                }
                $this->content .= '<figcaption>'.$waterbottle->getName().'</figcaption>';
                $this->content .= '</figure></li>';
            }
            $this->content .= '</ul></p>';
            $this->content .= '<footer class="pagination">';
            if($i > 0){
                $this->content .= '<a href="'.$this->router->getWaterbottlePartialListURL($n, $i-1).'">Page précédente</a>';
            }
            if(count($waterbottles) == $n){
                $this->content .= '<a href="'.$this->router->getWaterbottlePartialListURL($n, $i+1).'">Page suivante</a>';
            }
            $this->content .= '</footer>';
        }

        public function makeRegisterPage(AccountBuilder &$accountBuilder){
            $this->title = 'Créer un compte :';
            $this->content = '<form action="'.$this->router->getRegisterURL().'" method="POST">';
            $this->content .= '<label>Identifiant (doit être unique) :';
            $this->content .= '<input type="text" required name="'.Account::LOGIN_REF.'"></label><br>';
            $this->content .= '<label>Pseudonyme :';
            $this->content .= '<input type="text" required name="'.Account::PSEUDO_REF.'"></label><br>';
            $this->content .= '<label>Mot de passe :';
            $this->content .= '<input type="password" required name="'.Account::PASSWORD_REF.'"></label><br>';
            $this->content .= '<button type="submit">Créer le compte</button>';
            $this->content .= '</form>';
        }

        public function makeLoginPage(){
            $this->title = 'Connexion';
            $this->content = '<form action="'.$this->router->getConnectionURL().'" method="post">'.
                '<label>Identifiant : <input type="text" required name="'.Account::LOGIN_REF.'"></label><br>'.
                '<label>Mot de passe : <input type="password" required name="'.Account::PASSWORD_REF.'"></label><br>'.
                '<label><button type="submit">Se connecter</button></label>
                </form>';
        }

        public function makeAboutPage(){
            $this->title = 'À propos';
            $this->content = '';
            $this->content .= '<h1>Groupe 43</h1>';
            $this->content .= '<h2>Numéros étudiants des membres du groupe :</h2>';
            $this->content .=
                '<ul>
                    <li>21806034</li>
                    <li>21702286</li>
                    <li>21706663</li>
                    <li>22010821</li>
                </ul>';
            $this->content .= '<h2>Liste des compléments réalisés :</h2>';
            $this->content .=
                '<ul>
                    <li>Un objet peut être illustré par une image (et une seule, non modifiable) uploadée par le créateur de l\'objet.</li>
                    <li>Pagination de la liste (avec N objets par page)</li>
                    <li>Commentaires sur un objet</li>
                </ul>';
        }

        // ---- not allowed pages ----

        public function makeWaterbottlePage(Waterbottle &$waterbottle, $id, array &$comments, CommentBuilder &$commentBuilder){
            $this->makeNotAllowedPage();
        }

        public function makeWaterbottleCreationPage(WaterbottleBuilder &$waterbottleBuilder){
            $this->makeNotAllowedPage();
        }

        public function makeLogoutPage(){
            $this->makeNotAllowedPage();
        }

        public function makeUnknownWaterbottlePage(){
            $this->makeNotAllowedPage();
        }

        public function makeWaterbottleDeletionPage($id){
            $this->makeNotAllowedPage();
        }

        public function makeLogoutRedirect()
        {
            $this->makeNotAllowedPage();
        }

        public function makeWaterbottleUpdatedRedirect($id){
            $this->makeNotAllowedPage();
        }

        public function makeWaterbottleCreatedRedirect($id){
            $this->makeNotAllowedPage();
        }

        public function makeWaterbottleDeletedRedirect(){
            $this->makeNotAllowedPage();
        }

        public function makeNotAllowedPage(){
            // $emptyArray = array();
            // $accountBuilder = new AccountBuilder($emptyArray);
            // $this->makeRegisterPage($accountBuilder);
            $this->title = 'Contenu restreint';
            $this->content = '<p>Ce contenu est réservé aux membres connectés.</p>';
        }

        // ---- utility functions ----

        public function makeAccessDeniedPage(){
            $this->title = 'Accès refusé';
            $this->content = '<p>Vous ne pouvez pas accéder à cette page. <u>&gt;&lt;</u></p>';
        }

        public function make404()
        {
            $this->title = "Ressource inconnue";
            $this->content = "<h1>Erreur 404 :</h1>";
            $this->content .= "<p>La ressource demandée n'existe pas</p>";
        }

        public function makeDebugPage($variable) {
            $this->title = 'Debug';
            $this->content = '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
        }

        public function makeErrorPage(Exception $e){
            $this->title = 'Erreur';
            $this->content = $e->getMessage();
        }

        public function makeRegisteredRedirect(){
            $this->router->POSTredirect($this->router->getConnectionAttemptURL(),'Le compte a bien été créé, vous pouvez vous connecter.');
        }

        public function makeLoginAlreadyUsedRedirect(){
            $this->router->POSTredirect($this->router->getRegisterURL(),'Cet identifiant est déjà utilisé, désolé.');
        }

        public function makeLoginRedirect(){
            $this->router->POSTredirect($this->router->getHomeURL(),'Connexion réussie');
        }

        public function makeWrongPasswordRedirect(){
            $this->router->POSTredirect($this->router->getConnectionAttemptURL(), 'Mot de passe/identifiant incorrect');
        }

        public function render(){
            ob_start();
            include("base.html");
            $page = ob_get_contents();
            ob_end_clean();
            echo $page;
        }
}
