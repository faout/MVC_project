<?php

    require_once('Authentification/Account.php');
    require_once('Authentification/AccountStorage.php');
    require_once('AbstractDataBaseStorage.php');

    class AccountStorageMySQL extends AbstractDataBaseStorage implements AccountStorage{

        public function __construct(PDO &$db){
            parent::__construct($db, 'accounts', 'login');
        }

        public function checkAuth(string $login, $password) : Account{
            $account = $this->readObj($login);
            if($account){
                if(password_verify($password,$account->getPassword())){
                    return $account;
                }
            }
            throw new Exception("No such account", 1);
        }

        public function read($login) : Account{
            $account = $this->readObj($login);
            if($account === null){
                throw new Exception('No such account', 1);
            }else{
                return $account;
            }
        }

        public function readAll() : array{
            return $this->readAllObj();
        }

        public function create(Account &$obj){
            return $this->createObj($obj);
        }
        public function update(Account &$account) : bool{
            return $this->updateObj($account, $account->getLogin());
        }
        protected function getValuesToInsert(&$obj) : array{
            if($obj === NULL){
                $obj = new Account('', '', '','','');
            }
            return $obj->toArray();
        }

        protected function getObjectFromValues(array &$accountAttributes){
            return Account::fromArray($accountAttributes);
        }
    }
