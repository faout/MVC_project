<?php
    
    require_once('model/Waterbottle.php');
    require_once('model/WaterbottleStorage.php');

    class StaticWaterbottleStorage implements WaterbottleStorage{

        private $waterbottles;

        public function __construct(){
            $this->waterbottles = array(
                'Evian' => new Waterbottle('Evian', 2.3,'Eau minérale naturelle', '', ''),
                'Vitel' => new Waterbottle('Vitel', 1.5, 'Eau minérale naturelle', '', ''));
        }

        public function read($id) : Waterbottle{
            if(key_exists($id, $this->waterbottles)){
                return $this->waterbottles[$id];
            }else{
                throw new Exception('No such id to read', 1);
            }
        }

        public function readAll() : array{
            return $this->waterbottles;
        }

        public function create(Waterbottle $waterbottle){
            $this->waterbottles[$waterbottle->getName()] = $waterbottle;
            return $waterbottle->getName();
        }

        public function delete($id){
            if(key_exists($id, $this->watebottles)){
                unset($this->waterbottles[$id]);
            }else{
                throw new Exception('No such id to delete', 1);
            }
        }
    }
