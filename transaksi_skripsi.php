<?php
require_once "model/transaksi.php";
$tr = new Transaksi();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
      if(!empty($_GET["no_mhs"]) && !empty($_GET["action"])  ){
         $no_mhs= $_GET["no_mhs"];
         if ($_GET["action"] == 'get_total') {
            $tr->get_total_transaksi_skripsi($no_mhs);            
          }

      }
      else if (!empty($_GET["no_mhs"])) {
         $no_mhs = intval($_GET["no_mhs"]);
         $start = $_GET["start"];
         $length = $_GET["length"];
         $tr->get_transaksi_skripsi($no_mhs,$start,$length);
      }
      break;
   default:
      // Invalid Request Method
      header("HTTP/1.0 405 Method Not Allowed");
      break;
      break;
}
