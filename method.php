<?php
require_once "database/koneksi.php";
class Mahasiswa
{
   public function get_mhss()
   {
      global $mysqli;
      $query = "SELECT * FROM anggota  ORDER BY tgl_daftar DESC LIMIT 1000";
      $data = array();
      $result = $mysqli->query($query);
      while ($row = mysqli_fetch_object($result)) {
         $nestedData['no_mhs'] = $row->no_mhs;
         $nestedData['nama'] = $row->nama;
         $nestedData['status'] = $row->status;
         $nestedData['alamat'] = $row->alamat;
         $nestedData['tgl_daftar'] = $row->tgl_daftar;
         $nestedData['tgl_aktif'] = $row->tgl_aktif;
         $nestedData['angkatan'] = $row->angkatan;

         $data[] = $nestedData;
      }
      $response = array(
         'status' => 1,
         'message' => 'get_data_mahasiswa',
         'data' => $data
      );
      header('Content-Type: application/json');
      echo json_encode($response);
   }

   public function get_mhs($no_mhs = 0)
   {
      global $mysqli;
      $query = "SELECT  a.*,b.`jurusan`, b.`fakultas` FROM anggota a LEFT JOIN fakultas b  ON a.`kd_fakultas` = b.`kd_fakultas`  ";
      if ($no_mhs != 0) {
         $query .= "    WHERE a.no_mhs='" . $no_mhs . "' ORDER BY tgl_daftar DESC  LIMIT 1";
      }

      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);
      if ($num_rows > 0) {
         while ($row = mysqli_fetch_object($result)) {
            $nestedData['no_mhs'] = $row->no_mhs;
            $nestedData['nama'] = $row->nama;
            $nestedData['status'] = $row->status;
            $nestedData['alamat'] = $row->alamat;
            $nestedData['tgl_daftar'] = $row->tgl_daftar;
            $nestedData['tgl_aktif'] = $row->tgl_aktif;
            $nestedData['angkatan'] = $row->angkatan;
            $nestedData['jurusan'] = $row->jurusan;
            $nestedData['fakultas'] = $row->fakultas;
            $data[] = $nestedData;
         }
         $response = array(
            'status' => 1,
            'message' => 'Get Mahasiswa Successfully.',
            'data' => $data
         );
      } else {
         $response = array(
            'status' => 2,
            'message' => 'data tidak ditemukan'
         );
      }

      header('Content-Type: application/json');
      echo json_encode($response);
   }
}
