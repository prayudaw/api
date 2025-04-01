<?php
require_once "model/buku.php";
$buku = new Buku();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if (!empty($_GET["type"])) {
            $buku->get_data_buku($_GET["type"]);
        } else {
            $buku->get_data_buku();
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
        break;
}
