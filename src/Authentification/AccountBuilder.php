<?php
    class AccountBuilder
    {
        private $data;
        private $error;
        private $isValid;

        public function __construct(array &$data)
        {
            $this->data = $data;
            $this->error = array();
            $this->computeValidation();
        }

        public function createAccount(): Account{
            return new Account(
                htmlspecialchars($this->data[Account::LOGIN_REF]),
                htmlspecialchars($this->data[Account::PSEUDO_REF]),
                password_hash($this->data[Account::PASSWORD_REF], PASSWORD_BCRYPT),
                date('Y-m-d H:i:s'),
                Account::DISCONNECTED
            );
        }

        public function computeValidation()
        {
            $isLoginValid = (key_exists(Account::LOGIN_REF, $this->data) && mb_strlen($this->data[Account::LOGIN_REF]) > 0 && mb_strlen($this->data[Account::LOGIN_REF] <= 191));
            $isPseudoValid = (key_exists(Account::PSEUDO_REF, $this->data) && mb_strlen($this->data[Account::PSEUDO_REF]) > 0 && mb_strlen($this->data[Account::PSEUDO_REF]) <= 255);
            $isPasswordValid = (key_exists(Account::PASSWORD_REF, $this->data) && mb_strlen($this->data[Account::PASSWORD_REF]) >= 4); // 8 if professors didn't ask for their pw to be 'toto'.
            $this->isValid = ($isLoginValid && $isPseudoValid && $isPasswordValid);

            if(!$isLoginValid){
                $this->error[Account::LOGIN_REF] = 'Aucun identifiant valide spécifié. L\'identifiant ne peut pas contenir plus de 191 caractères.';
            }
            if(!$isPseudoValid){
                $this->error[Account::PSEUDO_REF] = 'Pas de pseudo valide spécifié. Le pseudonyme doit au plus être long de 255 caractères.';
            }
            if(!$isPasswordValid){
                $this->error[Account::PASSWORD_REF] = 'Aucun mot de passe valide spécifié. Le mot de passe doit contenir 8 caractère au moins.';
                $this->data[Account::PASSWORD_REF] = '';
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
    }
?>
