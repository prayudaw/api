<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once "database/koneksi_3.php";

class pintumasuk
{
   public function get_transaksi_pintumasuk($no_mhs = 0 ,$start = null,$length = null,  $search = null)
   { 
      global $mysqli;
      $query = "SELECT * FROM pengunjung";
       
      $search_query ="";
      // jika pencarian berjalan
      if($search != null){
         $search_query = "and (waktu_kunjung like  '%".$search."%')";
      }

      // untuk halaman pagination
      if ($no_mhs != 0) {
         $query .= " WHERE no_mhs=" . $no_mhs ." ".$search_query." Order by waktu_kunjung DESC ";
      } 
      if($start != null ){
         $query .= "LIMIT ".$length." OFFSET ".$start;
      }


      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);
      
         while ($row = mysqli_fetch_object($result)) {
            $nestedData['waktu_kunjung']=$row->waktu_kunjung; 
            $data[] = $nestedData;
          }

         //query get total
           $query_total="SELECT count(waktu_kunjung) as jumlah FROM pengunjung"; 
              // jika pencarian berjalan
            if ($no_mhs != 0) {
               $query_total .= " WHERE no_mhs=" . $no_mhs;
            } 
            $result_total = $mysqli->query($query_total);
            $total = mysqli_fetch_object($result_total);
  
            // get total filtered
            $total_filtered =$total;
            if($search != null ){
               $query_total_filtered ="SELECT count(waktu_kunjung) as jumlah FROM pengunjung   WHERE no_mhs=" . $no_mhs." and (waktu_kunjung LIKE  '%".$search."%') "; 
               $result_total_filtered = $mysqli->query($query_total_filtered);
               $total_filtered = mysqli_fetch_object($result_total_filtered);
            } 

          $response = array(
             'status' => 1,
             'message' => 'success',
             'data' => $data,
             'total_data'=>(int)$total->jumlah,
             'total_filtered'=>(int)$total_filtered->jumlah
          );

      header('Content-Type: application/json');
      echo json_encode($response);
   }
   
   public function get_total_transaksi_pintumasuk($no_mhs){
      global $mysqli;
      $query_total="SELECT count(waktu_kunjung) as jumlah FROM pengunjung"; 
      // jika pencarian berjalan
      if ($no_mhs != 0) {
         $query_total .= " WHERE no_mhs=" . $no_mhs;
      } 
      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);
      $data=(int)$total->jumlah;
      echo $data;
   }
}