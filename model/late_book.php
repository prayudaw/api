<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once "database/koneksi.php";

class late_book
{
    public function get_data_mhs_telat(){
         global $mysqli;

         // $query = "SELECT no_mhs,tgl_kembali,tgl_dikembalikan, CURDATE() AS date_now FROM transaksi  WHERE  DATE(tgl_kembali) < CURDATE() AND DATE(tgl_dikembalikan) = '0000-00-00' ORDER BY tgl_kembali DESC";
         $query = "SELECT kd_buku,no_mhs, tgl_kembali , tgl_dikembalikan FROM transaksi WHERE tgl_kembali < CURDATE() AND tgl_dikembalikan = '0000-00-00'";
         $data = array();
         $result = $mysqli->query($query);
         $num_rows = mysqli_num_rows($result);

          while ($row = mysqli_fetch_object($result)) {
            $nestedData['kd_buku']= $row->kd_buku;
            $nestedData['no_mhs']= $row->no_mhs;
            $nestedData['tgl_kembali']= $row->tgl_kembali;
            $nestedData['tgl_dikembalikan']= $row->tgl_dikembalikan; 
             $data[] = $nestedData;
          }
          
           $response = array(
            'status' => 1,
            'message' => 'success',
            'data' => $data,
         );
     header('Content-Type: application/json');
     echo json_encode($response);
    }

}