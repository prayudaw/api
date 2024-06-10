<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once "model/loker.php";
$loker = new loker();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if(!empty($_GET["no_mhs"]) && !empty($_GET["action"])  ){
            $no_mhs= $_GET["no_mhs"];
            if ($_GET["action"] == 'get_total') {
               $loker->get_total_transaksi_loker($no_mhs);
                 
             }   
         }
         else if (!empty($_GET["no_mhs"])) {
            $no_mhs = intval($_GET["no_mhs"]);
            $loker->get_transaksi_loker($no_mhs);            
         }
        
        break;
        case 'POST':
            $no_mhs = $_POST["no_mhs"];
            $start = $_POST["start"];
            $length = $_POST["length"];
            $search = $_POST["search"];
            $loker->get_transaksi_loker($no_mhs,$start,$length,$search);
            break;
    
    default:
        # code...
        break;
}