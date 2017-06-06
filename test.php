<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=homestead';
$user = 'homestead';
$password = 'secret';

try
{
    $dbh = new PDO($dsn, $user, $password, array('0', '2', '0', false, false));

     var_dump($dbh) ;

}
catch (PDOException $e)
{
    echo 'Connection failed: ' . $e->getMessage();
}
