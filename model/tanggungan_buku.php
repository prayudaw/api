<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once "database/koneksi.php";
require_once "hitung_denda.php";

class tanggungan_buku
{

    public function get_tanggungan_buku($no_mhs = 0, $start = null, $length = null,  $search = null)
    {

        $denda = new Hitung();
        global $mysqli;
        $query = "SELECT a.* , b.judul FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku";

        $search_query = "";
        // jika pencarian berjalan
        if ($search != null) {
            $search_query = "and (judul like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
        }
        // untuk halaman pagination
        if ($no_mhs != 0) {
            $query .= " WHERE a.no_mhs='" . $no_mhs . "' and  a.tgl_dikembalikan =''  " . $search_query . " Order by a.tgl_pinjam DESC ";
        }
        if ($start != null) {
            $query .= "LIMIT " . $length . " OFFSET " . $start;
        }

        // var_dump($query);
        // die();
        $data = array();
        $result = $mysqli->query($query);
        $num_rows = mysqli_num_rows($result);

        while ($row = mysqli_fetch_object($result)) {
            $nestedData['kd_buku'] = $row->kd_buku;
            $nestedData['no_mhs'] = $row->no_mhs;
            $nestedData['kd_jns_buku'] = $row->kd_jns_buku;
            $nestedData['operator_pinjam'] = $row->operator_pinjam;
            $nestedData['operator_kembali'] = $row->operator_kembali;
            $nestedData['tgl_pinjam'] = $row->tgl_pinjam;
            $nestedData['tgl_kembali'] = $row->tgl_kembali;
            $nestedData['tgl_dikembalikan'] = $row->tgl_dikembalikan;
            $nestedData['denda'] = $denda->hitung_denda($row->tgl_kembali);
            $nestedData['lunas'] = $row->lunas;
            $nestedData['status'] = $row->status;
            $nestedData['kd_konfig'] = $row->kd_konfig;
            $nestedData['judul'] = $row->judul;
            $data[] = $nestedData;
        }

        //query get total
        $query_total = "SELECT count(b.judul) as jumlah FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku";
        // jika pencarian berjalan
        if ($no_mhs != 0) {
            $query_total .= " WHERE a.no_mhs='" . $no_mhs . "' and  a.tgl_dikembalikan =''";
        }
        $result_total = $mysqli->query($query_total);
        $total = mysqli_fetch_object($result_total);

        // get total filtered
        $total_filtered = $total;
        if ($search != null) {
            $query_total_filtered = "SELECT count(b.judul) as jumlah FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku  WHERE a.no_mhs=" . $no_mhs . " and (judul like  '%" . $search . "%' or tgl_pinjam like  '%" . $search . "%' or tgl_kembali like  '%" . $search . "%' ) ";
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

    public function get_total_tanggungan_buku($no_mhs)
    {
        global $mysqli;
        $query_total = "SELECT count(kd_buku) as jumlah FROM transaksi";
        // jika pencarian berjalan
        if ($no_mhs != 0) {
            $query_total .= " WHERE no_mhs='" . $no_mhs . "'and  tgl_dikembalikan ='000-00-00'";
        }

        // var_dump($query_total);
        // die();
        $result_total = $mysqli->query($query_total);
        $total = mysqli_fetch_object($result_total);
        $data = (int)$total->jumlah;
        echo $data;
    }
}
