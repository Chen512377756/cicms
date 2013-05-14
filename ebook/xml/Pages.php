<?php 
error_reporting(0);
//$q=(isset($_GET['id'])?intval($_GET['id']):-1);
//file_put_contents(dirname(dirname(__FILE__)).'/test.txt',$_SERVER['QUERY_STRING']);
$q=intval($_SERVER['QUERY_STRING']);
header('location: ../index.php?welcome/xml/'.$q);