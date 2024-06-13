<?php
require_once "model/late_book.php";
$d = new late_book();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
       $d->get_data_mhs_telat();
      
      break;
  case 'POST':

   break;
   default:
      // Invalid Request Method
      header("HTTP/1.0 405 Method Not Allowed");
      break;
      break;
}
