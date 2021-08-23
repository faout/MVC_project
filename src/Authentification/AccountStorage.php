<?php

    require_once('Authentification/Account.php');

    interface AccountStorage{

        public function checkAuth(string $login, $password) : Account;

        public function create(Account &$account);

        public function delete($id);

        public function update(Account &$account) : bool;

    }
