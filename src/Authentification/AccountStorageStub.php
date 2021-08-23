<?php

    require_once('Authentification/Account.php');
    require_once('Authentification/AccountStorage.php');

    class AccountStorageStub implements AccountStorage{

        private $accounts;

        public function __construct(){
            $this->accounts = array(
                'totoDu53' => new Account('toto', 'totoDu53', 'helloToto'),
                'tatata' => new Account('titu', 'tatata', '1234'));
        }

        public function checkAuth($login, $password) : Account{
            if(key_exists($login, $this->accounts)){
                $account = $this->accounts[$login];
                if($password === $account->getPassword()){
                    return $account;
                }
            }
            throw new Exception('Pas de tel utilisateur', 1);
        }

    }
