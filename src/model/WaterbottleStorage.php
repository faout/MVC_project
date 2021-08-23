<?php
    
    require_once("model/Waterbottle.php");

    interface WaterbottleStorage{

        public function create(Waterbottle &$waterbottle); //return $id

        public function read($id) : Waterbottle;

        public function readAll(int $length=-1, int $n=0) : array;

        public function update(Waterbottle &$waterbottle, $id);

        public function delete($id);
    }
