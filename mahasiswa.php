<?php
require_once "method.php";
$mhs = new Mahasiswa();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
      if (!empty($_GET["no_mhs"])) {
         $no_mhs = $_GET["no_mhs"];
         $mhs->get_mhs($no_mhs);
      } else {
         $mhs->get_mhss();
      }
      break;
   default:
      // Invalid Request Method
      header("HTTP/1.0 405 Method Not Allowed");
      break;
      break;
}
