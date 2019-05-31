<?php

date_default_timezone_set('America/New_York');

ob_start();
session_start();

Flight::set('flight.views.path', 'views');

if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
include(__DIR__.'/../../db_config.php');
require_once(__DIR__.'/../models/Users.php');
require_once(__DIR__.'/../models/Character.php');

//Flight::register('db', 'PDO', array('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS ), function($db){
//    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//});

//if(ENVIRONMENT == 'development') {
//    error_reporting(E_ALL);
//    ini_set('display_errors', 1);
//}

Flight::register('users', 'Users');
Flight::register('character', 'Character');