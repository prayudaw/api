<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once "database/koneksi.php";
require_once "hitung_denda.php";

class tanggungan_skripsi
{

    public function get_tanggungan_skripsi($no_mhs = 0, $start = null, $length = null,  $search = null)
    {

        global $mysqli;
        $query = "SELECT * FROM skripsi";
        $search_query = "";
        // jika pencarian berjalan
        if ($search != null) {
            $search_query = "and (barcode like  '%" . $search . "%' or judul like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
        }
        // untuk halaman pagination
        if ($no_mhs != 0) {
            $query .= " WHERE nim='" . $no_mhs . "' and  tgl_kembali =''" . $search_query . " Order by tgl_pinjam DESC ";
        }

        if ($start != null) {
            $query .= "LIMIT " . $length . " OFFSET " . $start;
        }

        $data = array();
        $result = $mysqli->query($query);
        $num_rows = mysqli_num_rows($result);
        while ($row = mysqli_fetch_object($result)) {
            $nestedData['nim'] = $row->nim;
            $nestedData['barcode'] = $row->barcode;
            $nestedData['judul'] = $row->judul;
            $nestedData['tgl_kembali'] = $row->tgl_kembali;
            $nestedData['tgl_pinjam'] = $row->tgl_pinjam;
            $data[] = $nestedData;
        }

        //query get total
        $query_total = "SELECT count(judul) as jumlah FROM skripsi";
        // jika pencarian berjalan
        if ($no_mhs != 0) {
            $query_total .= " WHERE nim='" . $no_mhs . "'and  tgl_kembali =''";
        }
        $result_total = $mysqli->query($query_total);
        $total = mysqli_fetch_object($result_total);


        // get total filtered
        $total_filtered = $total;
        if ($search != null) {
            $query_total_filtered = "SELECT count(judul) as jumlah FROM skripsi  WHERE nim='" . $no_mhs . "' and (judul like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
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

    public function get_total_tanggungan_skripsi($no_mhs)
    {
        global $mysqli;
        $query_total = "SELECT count(judul) as jumlah FROM skripsi";
        // jika pencarian berjalan
        if ($no_mhs != 0) {
            $query_total .= " WHERE nim='" . $no_mhs . "'and  tgl_kembali =' '";
        }

        // var_dump($query_total);
        // die();
        $result_total = $mysqli->query($query_total);
        $total = mysqli_fetch_object($result_total);
        $data = (int)$total->jumlah;
        echo $data;
    }
}
