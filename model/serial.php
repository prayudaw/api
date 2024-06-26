<?php 
require_once "database/koneksi.php";

class serial 
{ 
    public function get_pengunjung($no_mhs = 0, $start = null, $length = null,  $search = null)
   {

    //   $denda = new Hitung();
      global $mysqli;
      $query = "SELECT * FROM visit ";

      $search_query = "";
      // jika pencarian berjalan
      if ($search != null) {
         $search_query = "and (judul like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
      }
      // untuk halaman pagination
      if ($no_mhs != 0) {
         $query .= " WHERE nim='" . $no_mhs . "' AND lokasi = 'serial' " . $search_query . " Order by waktu DESC ";
      }
      if ($start != null) {
         $query .= "LIMIT " . $length . " OFFSET " . $start;
      }

      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);

      while ($row = mysqli_fetch_object($result)) {
         $nestedData['waktu'] = $row->waktu;
         $nestedData['lokasi'] = $row->waktu;
         $data[] = $nestedData;
      }

      //query get total
      $query_total = "SELECT count(nim) as jumlah FROM visit WHERE lokasi= 'serial' ";
      // jika pencarian berjalan
      if ($no_mhs != 0) {
         $query_total .= " AND nim ='" . $no_mhs . "'";
      }
      
      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);

      // get total filtered
      $total_filtered = $total;
      if ($search != null) {
         $query_total_filtered = "SELECT count(nim) as jumlah FROM visit WHERE nim ='" . $no_mhs . "' AND lokasi= 'serial' AND ( waktu like  '%" . $search . "%') ";
         $result_total_filtered = $mysqli->query($query_total_filtered);
         $total_filtered = mysqli_fetch_object($result_total_filtered);
      }

      $response = array(
         'status' => 1,
         'message' => 'Sukses',
         'data' => $data,
         'total_data' => (int)$total->jumlah,
         'total_filtered' => (int)$total_filtered->jumlah
      );

      header('Content-Type: application/json');
      echo json_encode($response);
   }

   public function get_total_pengunjung_serial($no_mhs)
   {
      global $mysqli;
      $query_total = "SELECT count(nim) as jumlah FROM visit WHERE lokasi = 'serial'";
      // jika pencarian berjalan
    
      if ($no_mhs != 0) {
         $query_total .= " AND nim ='" . $no_mhs . "'";
      }

      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);
      $data = (int)$total->jumlah;
      echo $data;
   }



    
}

?>
