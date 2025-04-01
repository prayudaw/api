<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once "database/koneksi.php";

class buku
{
    public function get_data_buku($type = null)
    {
        global $mysqli;
        $query_total = "SELECT count(kd_buku) as jumlah FROM buku";
        if ($type) {
            if ($type == 'RF') {
                $query_total .= " WHERE kd_jns_buku = '" . $type . "'";
            } elseif ($type == 'SK') {
                $query_total .= " WHERE kd_jns_buku = '" . $type . " '";
            }
        }
        $result_total = $mysqli->query($query_total);
        $total = mysqli_fetch_object($result_total);
        $data = (int)$total->jumlah;
        echo $data;
    }
}
