<?php
require_once "model/transaksi.php";
$tr = new Transaksi();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
      if (!empty($_GET["no_mhs"]) && !empty($_GET["action"])) {
         $no_mhs = $_GET["no_mhs"];
         if ($_GET["action"] == 'get_total') {
            $tr->get_total_transaksi_buku($no_mhs);
         } elseif ($_GET["action"] == 'get_is_borrow') {
            $tr->get_is_borrow($no_mhs);
         }
      } else if (!empty($_GET["no_mhs"])) {
         $no_mhs = intval($_GET["no_mhs"]);
         $start = $_GET["start"];
         $length = $_GET["length"];
         $search = $_GET["search"];
         $tr->get_transaksi($no_mhs, $start, $length, $search);
      }
      break;
   case 'POST':
      $no_mhs = $_POST["no_mhs"];
      $start = $_POST["start"];
      $length = $_POST["length"];
      $search = $_POST["search"];
      $tr->get_transaksi($no_mhs, $start, $length, $search);
      break;
   default:
      // Invalid Request Method
      header("HTTP/1.0 405 Method Not Allowed");
      break;
      break;
}
