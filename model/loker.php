<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once "database/koneksi_2.php";

class loker
{
   public function get_transaksi_loker($no_mhs = 0, $start = null, $length = null,  $search = null)
   {
      global $mysqli;
      $query = "SELECT * FROM recordtrx";

      $search_query = "";
      // jika pencarian berjalan
      if ($search != null) {
         $search_query = "and (no_loker like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
      }

      // untuk halaman pagination
      if ($no_mhs != 0) {
         $query .= " WHERE id_anggota='" . $no_mhs . "' " . $search_query . " Order by tgl_pinjam DESC ";
      }
      if ($start != null) {
         $query .= "LIMIT " . $length . " OFFSET " . $start;
      }

      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);

      while ($row = mysqli_fetch_object($result)) {
         $nestedData['no_loker'] = $row->no_loker;
         $nestedData['id_anggota'] = $row->id_anggota;
         $nestedData['nama'] = $row->nama;
         $nestedData['nim'] = $row->nim;
         $nestedData['fakultas'] = $row->fakultas;
         $nestedData['tgl_pinjam'] = $row->tgl_pinjam;
         $nestedData['tgl_kembali'] = $row->tgl_kembali;
         $data[] = $nestedData;
      }

      //query get total
      $query_total = "SELECT count(tgl_pinjam) as jumlah FROM recordtrx";
      // jika pencarian berjalan
      if ($no_mhs != 0) {
         $query_total .= " WHERE id_anggota='" . $no_mhs . "'";
      }
      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);

      // get total filtered
      $total_filtered = $total;
      if ($search != null) {
         $query_total_filtered = "SELECT count(tgl_pinjam) as jumlah FROM recordtrx  WHERE id_anggota=" . $no_mhs . " and (no_loker like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
         $result_total_filtered = $mysqli->query($query_total_filtered);
         $total_filtered = mysqli_fetch_object($result_total_filtered);
      }

      $response = array(
         'status' => 1,
         'message' => 'success',
         'data' => $data,
         'total_data' => (int)$total->jumlah,
         'total_filtered' => (int)$total_filtered->jumlah
      );

      header('Content-Type: application/json');
      echo json_encode($response);
   }

   public function get_total_transaksi_loker($no_mhs)
   {
      global $mysqli;
      $query_total = "SELECT count(tgl_pinjam) as jumlah FROM recordtrx";
      // jika pencarian berjalan
      if ($no_mhs != 0) {
         $query_total .= " WHERE id_anggota='" . $no_mhs . "'";
      }
      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);
      $data = (int)$total->jumlah;
      echo $data;
   }
}
