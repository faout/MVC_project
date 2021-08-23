<?php

    require_once('model/Waterbottle.php');

    class WaterbottleBuilder{

        private $data;
        private $error;
        private $isValid;

        public function __construct(array &$data, array &$picture=null){
            $this->data = $data;
            $this->error = array();
            $this->computeValidation();
            if($picture !== null && key_exists(Waterbottle::PICTURE_REF, $picture))
            {
                // Récupérer le fichier image du formulaire et le déplacer dans le répertoire /www-dev/projet-inf5c-2020/upload/images
                // On récupère la chaine du chemin
                $tmp = $picture[Waterbottle::PICTURE_REF]['tmp_name'];
                // On récupère le type
                $type = basename($picture[Waterbottle::PICTURE_REF]['type']);
                $dir = htmlspecialchars(getcwd()."/upload/images"); // emplacement
                // On commence par vérifier si le fichier attendu est bien une image de type JPEG ou GIF
                if(exif_imagetype($tmp) == IMAGETYPE_GIF || exif_imagetype($tmp) == IMAGETYPE_JPEG)
                {
                    // On vérifie que l'image n'est pas trop volumineuse
                    if($picture[Waterbottle::PICTURE_REF]['tmp_name'] < 500000)
                    {
                        // On force un redimensionnement "fixe" pour conserver un aspect agréable dans la page de la liste des bouteilles
                        //$tmp = $this->resizedPicture($tmp,0.3); -- A revoir
                        // On génère une chaine aléatoire
                        $newName = uniqid();
                        move_uploaded_file($tmp, "$dir/".$newName.".$type");
                        $this->data[Waterbottle::PICTURE_REF] = htmlspecialchars("https://".$_SERVER['SERVER_NAME']."/projet-inf5c-2020/upload/images/$newName.$type");
                    } else {
                        $this->error[Waterbottle::PICTURE_REF] = 'L\'image est trop grande';
                    }
                } else {
                    $this->error[Waterbottle::PICTURE_REF] = 'Ce n\'est pas une image !';
                }
            } else {
                // On attribue une image par défaut
                $this->data[Waterbottle::PICTURE_REF] = htmlspecialchars("https://".$_SERVER['SERVER_NAME']."/projet-inf5c-2020/upload/images/evian.jpg");
            }
        }

        public function createWaterbottle() : Waterbottle{
            return new Waterbottle(htmlspecialchars($this->data[Waterbottle::NAME_REF]),
                htmlspecialchars($this->data[Waterbottle::PRICE_REF]),
                htmlspecialchars($this->data[Waterbottle::CATEGORY_REF]),
                htmlspecialchars($this->data[Waterbottle::PICTURE_REF], ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8'),
                htmlspecialchars($this->data[Waterbottle::COMPOSITION_REF]),
                htmlspecialchars($this->data[Waterbottle::CREATOR_REF])
            );
        }

        private function computeValidation(){
            $isNameValid = (key_exists(Waterbottle::NAME_REF, $this->data) && mb_strlen($this->data[Waterbottle::NAME_REF]) > 0);
            $isPriceValid = (key_exists(Waterbottle::PRICE_REF, $this->data) && mb_strlen($this->data[Waterbottle::PRICE_REF]) > 0 && (float)$this->data[Waterbottle::PRICE_REF] > 0);
            $isCategoryValid = (key_exists(Waterbottle::CATEGORY_REF, $this->data) && ($this->data[Waterbottle::CATEGORY_REF] === Waterbottle::SOURCE_REF || $this->data[Waterbottle::CATEGORY_REF] === Waterbottle::MINERALE_REF || $this->data[Waterbottle::CATEGORY_REF] === Waterbottle::GAZEUSE_REF));
            $this->isValid = ($isNameValid && $isPriceValid && $isCategoryValid);

            if(!$isNameValid){
                $this->error[Waterbottle::NAME_REF] = 'Aucun nom spécifié';
            }
            if(!$isPriceValid){
                $this->error[Waterbottle::PRICE_REF] = 'Prix négatif ou non spécifié';
            }
            if(!$isCategoryValid){
                $this->error[Waterbottle::CATEGORY_REF] = 'La catégorie doit être soit "Eau minérale", soit "Eau de source", soit "Eau gazeuse".';
            }
        }

        public function isValid() : bool{
            return $this->isValid;
        }

        public function getError() : array{
            return $this->error;
        }

        public function getData() : array{
            return $this->data;
        }

        public function setCreator($login){
            $this->data[Waterbottle::CREATOR_REF] = $login;
        }

        public function resizedPicture($picture,$percent)
        {
            list($width, $height) = getimagesize($picture);
            $new_width = $width * $percent;
            $new_height = $height * $percent;
            $image_p = imagecreatetruecolor($new_width, $new_height);
            $image = imagecreatefromstring(file_get_contents($picture));
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($image_p,null,100);
        }
    }
