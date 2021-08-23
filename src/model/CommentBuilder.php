<?php

    require_once('model/Comment.php');
    
    class CommentBuilder{

        private $data;
        private $error;
        private $isValid;

        public function __construct(array &$data){
            $this->data = $data;
            $this->error = array();
            $this->computeValidation();
        }

        private function computeValidation(){
            $this->isValid = (key_exists(Comment::COMMENT_REF, $this->data) &&
            mb_strlen($this->data[Comment::COMMENT_REF]) > 0 &&
            mb_strlen($this->data[Comment::COMMENT_REF]) <= 255);
            if(!$this->isValid){
                $this->error[Comment::COMMENT_REF] = 'Le commentaire ne doit pas dépasser les 255 caractères. Postez 2 commentaires au besoin.';
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

        public function setWriter($login){
            $this->data[Comment::WRITER_REF] = $login;
        }

        public function setWaterbottle($id){
            $this->data[Comment::WATERBOTTLE_REF] = $id;
        }

        public function createComment() : Comment{
            return new Comment(
                htmlspecialchars($this->data[Comment::WRITER_REF]),
                htmlspecialchars($this->data[Comment::WATERBOTTLE_REF]),
                date('Y-m-d H:i:s'),
                htmlspecialchars($this->data[Comment::COMMENT_REF])
            );
        }
    }
