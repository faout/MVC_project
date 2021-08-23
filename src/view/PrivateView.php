<?php

require_once('Router.php');
    require_once('Authentification/Account.php');
    require_once('model/WaterbottleBuilder.php');
    require_once('model/CommentBuilder.php');
    require_once('model/Waterbottle.php');
    require_once('view/PublicView.php');

    class PrivateView extends PublicView{

        private $user;

        public function __construct(Router &$router, $feedback, Account &$user){
            $this->user = $user;
            parent::__construct($router,$feedback);
        }

        public function getMenu(): array
        {
            return array(
                "Accueil" => $this->router->getHomeURL(),
                "Liste des marques" => $this->router->getWaterbottlePartialListURL(),
                "Se déconnecter" => $this->router->getAskDeconnectionURL(),
                'Mon compte' => $this->router->getAccountURL($this->user->getLogin()),
                "À propos" => $this->router->getAboutURL()
            );
        }

        // ---- allowed pages ----

        public function makeHomePage(){
            $this->title = 'Accueil';
            $this->content = '<h1>Bienvenue sur le site qui référence les marques de bouteilles d\'eau française !</h1>';
            $this->content .= 'Connecté en tant que '.$this->user->getPseudo();
        }

        public function makeListPage(array &$waterbottles){
            $this->title = 'Liste des bouteilles :';
            $this->content = '<p>Liste des bouteilles : (voir par <a href="'.$this->router->getWaterbottlePartialListURL(5, 0).'">5</a>, <a href="'.$this->router->getWaterbottlePartialListURL(10, 0).'">10</a>, <a href="'.$this->router->getWaterbottlePartialListURL(15, 0).'">15</a>, ou <a href="'.$this->router->getWaterbottleListURL().'">toutes les bouteilles</a>)';
            $this->content .= '<ul class=list>';
            foreach($waterbottles as $id => $waterbottle){
                $this->content .= '<li><figure>';
                $url = $waterbottle->getURLPicture();
                if($url !== null && $url !== ''){
                    $this->content .= '<img src="'.$url.'" alt="'.$waterbottle->getName().'">';
                }
                $this->content .= '<figcaption><a href="'.$this->router->getWaterbottleURL($id).'">'.$waterbottle->getName().'</a></figcaption>';
                $this->content .= '</figure></li>';
            }
            $this->content .= '</ul><br><a href="'.$this->router->getWaterbottleCreationURL().'">Ajouter une nouvelle bouteille d\'eau</a>';
        }

        public function makePartialListPage(array &$waterbottles, int $n, int $i){
            $this->title = 'Liste des bouteilles';
            $this->content = '<p>Liste des bouteilles (page '.($i+1).' avec '.$n.' bouteilles) : (voir par <a href="'.$this->router->getWaterbottlePartialListURL(5, 0).'">5</a>, <a href="'.$this->router->getWaterbottlePartialListURL(10, 0).'">10</a>, <a href="'.$this->router->getWaterbottlePartialListURL(15, 0).'">15</a>, ou <a href="'.$this->router->getWaterbottleListURL().'">toutes les bouteilles</a>)';
            $this->content .= '<ul>';
            foreach($waterbottles as $id => $waterbottle){
                $this->content .= '<li><figure>';
                $url = $waterbottle->getURLPicture();
                if($url !== null && $url !== ''){
                    $this->content .= '<img src="'.$url.'" alt="'.$waterbottle->getName().'">';
                }
                $this->content .= '<figcaption><a href="'.$this->router->getWaterbottleURL($id).'">'.$waterbottle->getName().'</a></figcaption>';
                $this->content .= '</figure></li>';
            }
            $this->content .= '</ul><br><a href="'.$this->router->getWaterbottleCreationURL().'">Ajouter une nouvelle bouteille d\'eau</a>';
            $this->content .= '<footer class="pagination">';
            if($i > 0){
                $this->content .= '<a href="'.$this->router->getWaterbottlePartialListURL($n, $i-1).'">&lt;-- Page précédente</a>';
            }else{
                $this->content .= '<a></a>';
            }
            if(count($waterbottles) == $n){
                $this->content .= '<a href="'.$this->router->getWaterbottlePartialListURL($n, $i+1).'">Page suivante --&gt;</a>';
            }else{
                $this->content .= '<a></a>';
            }
            $this->content .= '</footer>';
        }

        public function makeAccountPage(Account &$account){
            $this->title = $account->getPseudo();
            $this->content  = '<p>Pseudonyme : '.$account->getPseudo().'</p>';
            $this->content .= '<p>Identifiant : '.$account->getLogin().'</p>';
            $this->content .= '<p>Status : '.$account->getStatus().'</p>';
            $this->content .= '<p>Date de création du compte : '.$account->getCreationDate().'</p>';
            $this->content .= '<p>Date de la dernière connexion : '.$account->getLastConnectionDate().'</p>';
        }

        public function makeWaterbottlePage(Waterbottle &$waterbottle, $id, array &$comments, CommentBuilder &$commentBuilder){
            $data = $commentBuilder->getData();
            $this->title = $waterbottle->getName();
            $this->content = '<div class="waterbottle">';
            $this->content .= '<div>';
            $this->content .= '<p>Nom : '.$waterbottle->getName().'</p>';
            $this->content .= '<p>Catégorie : '.$waterbottle->getWaterCategory().'</p>';
            $this->content .= '<p>Prix : '.$waterbottle->getPrice().'</p>';
            $this->content .= '<p>Composition : '.$waterbottle->getWaterComposition().'</p>';
            $creator = $waterbottle->getCreator();
            $this->content .= '<p>Créateur : <a href="'.$this->router->getAccountURL($creator).'">'.$creator.'</a></p>';
            $this->content .= '<p>Date de création : '.$waterbottle->getCreationDate().'</p>';
            if($waterbottle->getLastUpdateDate() !== null){
                $this->content .= '<p>Date de la dernière modification : '.$waterbottle->getLastUpdateDate().'</p>';
            }
            $this->content .= '</div>';
            $url = $waterbottle->getURLPicture();
            if($url !== null && $url != ''){
                $this->content .= '<figure>';
                $this->content .= '<img src="'.$url.'" alt="'.$waterbottle->getName().'">';
                $this->content .= '</figure>';
            }
            $this->content .= '</div>';
            $this->content .= '<p>'.
                '<a href="'.$this->router->getWaterbottleAskDeletionURL($id).'">Supprimer cette bouteille d\'eau ?</a><br>'.
                '<a href="'.$this->router->getWaterbottleEditionURL($id).'">Editer cette bouteille d\'eau</a></p>';
            $this->content .= '<footer>'.
            '<form action="'.$this->router->getPostCommentURL($id).'" method="post">'.
                '<textarea placeholder="Ecrivez votre commentaire ici..." rows="5" cols="100" name="'.Comment::COMMENT_REF.'" maxlength="255"'.(key_exists(Comment::COMMENT_REF, $data)?(' value="'.$data[Comment::COMMENT_REF].'"'):'').'></textarea><br>'.
                '<button type="submit">Poster le commentaire</button>'.
            '</form>';
            foreach($comments as $comment){
                $writer = $comment->getWriterLogin();
                $this->content .=
                    '<hr>'.
                    '<h3>De : <a href="'.$this->router->getAccountURL($writer).'">'.$writer.'</a> (le '.$comment->getWritingDate().')</h3>'.
                    '<p>'.$comment->getText().'</p>';
            }
            $this->content .= '</footer>';
        }

        public function makeUnknownWaterbottlePage(){
            $this->title = 'Invalid id';
            $this->content = 'Bouteille d\'eau inconnue';
        }

        public function makeWaterbottleCreationPage(WaterbottleBuilder &$waterbottleBuilder){
            $this->title = 'Nouvelle bouteille d\'eau';
            $waterbottleData = $waterbottleBuilder->getData();
            $error = $waterbottleBuilder->getError();
            $this->content =
                '<form enctype="multipart/form-data" action="'.$this->router->getWaterbottleSaveURL().'" method="post">'.
                    '<fieldset>'.
                    '<legend>Création de bouteille :</legend>'.
                    $this->getWaterbottleFormFields($waterbottleData, $error).
                    $this->content .= '<label>Fichier : <input type="file" name="'.Waterbottle::PICTURE_REF.'"></label><br>'.
                    '<label><button type="submit">Créer la bouteille d\'eau</button></label><br>'.
                    '</fieldset>'.
                '</form>';
        }

        public function makeWaterbottleEditionPage(WaterbottleBuilder &$waterbottleBuilder, $id){
            $this->title = 'Modifier la bouteille d\'eau';
            $waterbottleData = $waterbottleBuilder->getData();
            $error = $waterbottleBuilder->getError();
            $this->content =
                '<form enctype="multipart/form-data" action="'.$this->router->getWaterbottleUpdateURL($id).'" method="post">'.
                    '<fieldset>'.
                    '<legend>Création de bouteille :</legend>'.
                    $this->getWaterbottleFormFields($waterbottleData, $error).
                    '<label><button type="submit">Modifier la bouteille d\'eau</button></label><br>'.
                    '</fieldset>'.
                '</form>';
        }

        public function makeWaterbottleDeletionPage($id){
            $this->title = 'Confirmation de suppression';
            $this->content = 'Si vous souhaitez réellement supprimer cette bouteille d\'eau, appuyer sur "Confirmer"'.
                '<form action="'.$this->router->getWaterbottleDeletionURL($id).'", method="post">'.
                    '<label><button type="submit">Confirmer</button></label></form>';
        }

        public function makeLogoutPage(){
            $this->title = 'Déconnexion';
            $this->content = 'Si vous souhaitez réellement vous déconnecter, appuyer sur "Se déconnecter"'.
                '<form action="'.$this->router->getDeconnectionURL().'" method="post">'.
                    '<label><button type="submit">Se déconnecter</button></label></form>';
        }

        // ---- utility function ----

        protected function getWaterbottleFormFields(array &$waterbottleData, array &$error) : string{
            $fields  = '<label>Nom : <input type="text" name="'.Waterbottle::NAME_REF.'"' . (($waterbottleData !== null && key_exists(Waterbottle::NAME_REF, $waterbottleData))?(' value="'.htmlspecialchars($waterbottleData[Waterbottle::NAME_REF]).'"'):'') . '></label>' . ((key_exists(Waterbottle::NAME_REF, $error))?(' '.$error[Waterbottle::NAME_REF]):'') . '<br>';
            $fields .= '<label>Prix au litre (en euro) : <input type="text" pattern="^[0-9]+(\.[0-9][0-9]?)?$" name="'.Waterbottle::PRICE_REF.'"' . (($waterbottleData !== null && key_exists(Waterbottle::PRICE_REF, $waterbottleData))?(' value="'.htmlspecialchars($waterbottleData[Waterbottle::PRICE_REF]).'"'):' placeholder="1.87"') . '></label>' . ((key_exists(Waterbottle::PRICE_REF, $error))?(' '.$error[Waterbottle::PRICE_REF]):'') . '<br>';
            $fields .= '<label>Composition : <input type="text" name="'.Waterbottle::COMPOSITION_REF.'"'.(key_exists(Waterbottle::COMPOSITION_REF, $waterbottleData)?(' value="'.$waterbottleData[Waterbottle::COMPOSITION_REF].'"'):'').'></label><br>';
            $fields .= '<label for="'.Waterbottle::CATEGORY_REF.'">Catégorie :</label>'.
                        '<select name="'.Waterbottle::CATEGORY_REF.'">'.
                            '<option value="'.Waterbottle::MINERALE_REF.'"'.((key_exists(Waterbottle::CATEGORY_REF, $waterbottleData) && $waterbottleData[Waterbottle::CATEGORY_REF]===Waterbottle::MINERALE_REF)?' selected':'').'>Eau minérale</option>'.
                            '<option value="'.Waterbottle::SOURCE_REF.'"'.((key_exists(Waterbottle::CATEGORY_REF, $waterbottleData) && $waterbottleData[Waterbottle::CATEGORY_REF]===Waterbottle::SOURCE_REF)?' selected':'').'>Eau de source</option>'.
                            '<option value="'.Waterbottle::GAZEUSE_REF.'"'.((key_exists(Waterbottle::CATEGORY_REF, $waterbottleData) && $waterbottleData[Waterbottle::CATEGORY_REF]===Waterbottle::GAZEUSE_REF)?' selected':'').'>Eau gazeuse</option>'.
                        '</select><br>';
            return $fields;
        }

        public function makeLogoutRedirect()
        {
            $this->router->POSTredirect($this->router->getHomeURL(),"Déconnexion du compte");
        }

        public function makeWaterbottleCreatedRedirect($id){
            $this->router->POSTredirect($this->router->getWaterbottleURL($id),"La bouteille a bien été créée.");
        }

        public function makeWaterbottleDeletedRedirect(){
            $this->router->POSTredirect($this->router->getWaterbottleListURL(),"La bouteille a bien été supprimée.");
        }

        public function makeWaterbottleUpdatedRedirect($id){
            $this->router->POSTredirect($this->router->getWaterbottleURL($id),'La bouteille a bien été mise à jour.');
        }

        public function makeCommentPostedRedirect($id){
            $this->router->POSTredirect($this->router->getWaterbottleURL($id),'Commentaire posté avec succès !');
        }
    }
