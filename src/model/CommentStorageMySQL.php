<?php
    
    require_once('model/Comment.php');
    require_once('model/CommentStorage.php');
    require_once('AbstractDataBaseStorage.php');

    class CommentStorageMySQL extends AbstractDataBaseStorage implements CommentStorage{

        private $readAll;
        private $deleteAll;

        public function __construct(PDO &$db){
            parent::__construct($db, 'comments');
            $this->readAll = $db->prepare('SELECT * FROM comments WHERE '.Comment::WATERBOTTLE_REF.'=:id ORDER BY '.Comment::WRITING_DATE_REF);
            $this->deleteAll = $db->prepare('DELETE FROM comments WHERE '.Comment::WATERBOTTLE_REF.'=:id');
        }

        public function readAll($waterbottleID) : array{
            $this->readAll->execute(array(':id' => $waterbottleID));
            $commentsAttributes = $this->readAll->fetchAll(PDO::FETCH_ASSOC);
            $comments = [];
            foreach($commentsAttributes as $commentAttributes){
                $comments[] = $this->getObjectFromValues($commentAttributes);
            }
            return $comments;
        }

        public function create(Comment &$comment){
            $this->createObj($comment);
        }

        public function deleteAll($waterbottleID){
            $this->deleteAll->execute(array(':id' => $waterbottleID));
        }

        protected function getObjectFromValues(array &$attributes){
            return Comment::fromArray($attributes);
        }

        protected function getValuesToInsert(&$obj) : array{
            if($obj === null){
                $obj = new Comment('', '', '', '');
            }
            return $obj->toArray();
        }

    }
