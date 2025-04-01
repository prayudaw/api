<?php
require_once "model/tanggungan_tandon.php";
$tr = new tanggungan_tandon();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if (!empty($_GET["no_mhs"]) && !empty($_GET["action"])) {
            $no_mhs = $_GET["no_mhs"];
            if ($_GET["action"] == 'get_total') {
                $tr->get_total_tanggungan_tandon($no_mhs);
            }
        }
        break;
    case 'POST':
        $no_mhs = $_POST["no_mhs"];
        $start = $_POST["start"];
        $length = $_POST["length"];
        $search = $_POST["search"];
        $tr->get_tanggungan_tandon($no_mhs, $start, $length, $search);
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
        break;
}
