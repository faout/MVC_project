<?php
    
    class Account{

        private $pseudo;
        private $login;
        private $pw;
        private $creationDate;
        private $status;
        private $lastConnectionDate;

        // do not change, used as field name in DB
        const LOGIN_REF = 'login'; // should be used as primary key in db
        const PSEUDO_REF = 'pseudo';
        const PASSWORD_REF = 'password';
        const CREATION_DATE_REF = 'creationDate';
        const STATUS_REF = 'status';
        const LAST_CONNECTION_DATE_REF = 'lastConnectionDate';

        // status account
        const CONNECTED = 'connected';
        const DISCONNECTED = 'disconnected';

        // status should be ACCOUNT::CONNECTED or ACCOUNT::DISCONNECTED
        public function __construct(string $login, string $pseudo, $pw, $creationDate, $status, $lastConnectionDate=null){
            $this->login = $login;
            $this->pseudo = $pseudo;
            $this->pw = $pw;
            $this->creationDate = $creationDate;
            $this->status = $status;
            $this->lastConnectionDate = $lastConnectionDate;
        }

        public function getPassword(){
            return $this->pw;
        }

        public function getPseudo() : string{
            return $this->pseudo;
        }

        public function getLogin() : string{
            return $this->login;
        }

        public function getStatus(){
            return $this->status;
        }

        public function setStatus($status){
            $this->status = $status;
            if($status === Account::CONNECTED){
                $this->lastConnectionDate = date('Y-m-d H:i:s');
            }
        }

        public function getCreationDate(){
            return $this->creationDate;
        }

        public function getLastConnectionDate(){
            return $this->lastConnectionDate;
        }

        public static function fromArray(array $attributes) : Account{
            return new Account($attributes[Account::LOGIN_REF], $attributes[Account::PSEUDO_REF], $attributes[Account::PASSWORD_REF], $attributes[Account::CREATION_DATE_REF], $attributes[Account::STATUS_REF], $attributes[Account::LAST_CONNECTION_DATE_REF]);
        }

        public function toArray() : array{
            return array(
                Account::LOGIN_REF => $this->login,
                Account::PSEUDO_REF => $this->pseudo,
                Account::PASSWORD_REF => $this->pw,
                Account::CREATION_DATE_REF => $this->creationDate,
                Account::STATUS_REF => $this->status,
                Account::LAST_CONNECTION_DATE_REF => $this->lastConnectionDate
            );
        }
    }
