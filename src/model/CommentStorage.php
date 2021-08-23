<?php
    
    require_once('model/Comment.php');

    interface CommentStorage{

        public function readAll($waterbottleID) : array;

        public function create(Comment &$comment);

        public function deleteAll($waterbottleID);

    }
