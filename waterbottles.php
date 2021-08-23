<?php

  //require('../../private/mysql_config.php');

    set_include_path('./src');

    require_once('Router.php');
    require_once('model/WaterBottlesStorageMysql.php');
    require_once('model/CommentStorageMySQL.php');
    require_once('Authentification/AccountStoragemysql.php');
    $dsn ="mysql:host=mysql.info.unicaen.fr;port=3306;dbname=22010821_bd;charset=utf8";
    $MYSQL_USER= '22010821';
    $MYSQL_PASSWORD ='pekeixi7wi9oF1ai';

    //$dsn = 'mysql:host='.$MYSQL_HOST.';port=.'.$MYSQL_PORT.';dbname='.$MYSQL_DB.';charset=utf8mb4';
    $db = new PDO($dsn, $MYSQL_USER, $MYSQL_PASSWORD);

    $router = new Router();
    $waterbottleStorage = new WaterbottleStorageMySQL($db);
    $commentStorage = new CommentStorageMySQL($db);
    $accountStorage = new AccountStorageMySQL($db);
    $router->main($waterbottleStorage, $commentStorage, $accountStorage);
