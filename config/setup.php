<?php
require '../vendor/autoload.php';
date_default_timezone_set('America/New_York');

ob_start();
session_start();

Flight::set('flight.views.path', 'views');

if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
include(__DIR__.'/var/www/osrsgtconfig/db_config.php');
//require_once(__DIR__ . '/var/www/osrsgt/html/models/Users.php');

Flight::register('db', 'PDO', array('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS ), function($db){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
});

Flight::register('users', 'Users');