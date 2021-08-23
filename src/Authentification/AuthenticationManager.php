<?php

    require_once('Authentification/Account.php');
    require_once('Authentification/AccountStorage.php');

    class AuthenticationManager{

        const USER_REF = 'user';

        private $accountStorage;

        public function __construct(AccountStorage $accountStorage){
            $this->accountStorage = $accountStorage;
        }

        public function authenticate($login, $pw) : bool{
            try{
                $account = $this->accountStorage->checkAuth($login, $pw);
                $_SESSION[AuthenticationManager::USER_REF] = $account;
                return true;
            }catch(Exception $e){
                return false;
            }
        }

        public function disconnect(){
            $account = $_SESSION[AuthenticationManager::USER_REF];
            $account->setStatus(Account::DISCONNECTED);
            unset($_SESSION[AuthenticationManager::USER_REF]);
            $this->accountStorage->update($account);
        }

        public function isConnected() : bool{
            if(key_exists(AuthenticationManager::USER_REF, $_SESSION) && $_SESSION[AuthenticationManager::USER_REF] !== null){
                try{
                    $this->accountStorage->read($_SESSION[AuthenticationManager::USER_REF]->getLogin());
                    return true;
                }catch(Exception $e){
                    return false;
                }
            }else{
                return false;
            }
        }

        public function getLogin(){
            $account = $_SESSION[AuthenticationManager::USER_REF];
            return $account->getLogin();
        }

        // raise an exception if no user is connected
        public function getAccount() : Account{
            return $_SESSION[AuthenticationManager::USER_REF];
        }
    }
