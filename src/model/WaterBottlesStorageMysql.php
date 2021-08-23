<?php

    require_once('AbstractDataBaseStorage.php');
    require_once('model/Waterbottle.php');
    require_once('model/WaterbottleStorage.php');

    class WaterbottleStorageMySQL extends AbstractDataBaseStorage implements WaterbottleStorage{

        public function __construct(PDO &$db){
            parent::__construct($db, 'WaterBottles', 'id');
        }

        public function read($id) : Waterbottle{
            $waterbottle = $this->readObj($id);
            if($waterbottle != null){
                return $waterbottle;
            }else{
                throw new Exception("No such waterbottle", 1);

            }
        }

        public function readAll(int $length=-1, int $n=0) : array{
            return $this->readALLObj($length, $n);
        }

        public function create(Waterbottle &$obj){
            return $this->createObj($obj);
        }

        public function update(Waterbottle &$waterbottle, $id){
            return parent::updateObj($waterbottle, $id);
        }

        protected function getValuesToInsert(&$obj) : array{
            if($obj === NULL){
                $obj = new Waterbottle('', 0.0, '', '', '', '','');
            }
            return $obj->toArray();
        }

        protected function getObjectFromValues(array &$waterbottleAttributes){
            return Waterbottle::fromArray($waterbottleAttributes);
        }
    }
