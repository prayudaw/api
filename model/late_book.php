<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once "database/koneksi.php";
require_once "hitung_denda.php";

class late_book
{
    public function get_data_mhs_telat()
    {
        $denda = new Hitung();
        global $mysqli;

        // $query = "SELECT no_mhs,tgl_kembali,tgl_dikembalikan, CURDATE() AS date_now FROM transaksi  WHERE  DATE(tgl_kembali) < CURDATE() AND DATE(tgl_dikembalikan) = '0000-00-00' ORDER BY tgl_kembali DESC";
        $query = "SELECT a.no_mhs,b.`nama` FROM transaksi a LEFT JOIN anggota b ON a.`no_mhs` = b.`no_mhs` WHERE tgl_kembali < CURDATE() 
            AND tgl_dikembalikan = '0000-00-00' 
            GROUP BY no_mhs 
            ORDER BY tgl_kembali DESC 
            LIMIT 3";

        $data = array();
        $result = $mysqli->query($query);
        $num_rows = mysqli_num_rows($result);

        while ($row = mysqli_fetch_object($result)) {
            $nestedData['no_mhs'] = $row->no_mhs;
            $nestedData['nama'] = $row->nama;
            $buku_telat = $this->get_book_is_borrow($row->no_mhs);
            $nestedData['buku_telat'] = $buku_telat;
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

    private function get_book_is_borrow($no_mhs)
    {
        $denda = new Hitung();
        global $mysqli;
        $query = "SELECT a.*,b.* FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku  WHERE a.no_mhs='" . $no_mhs . "' AND a.tgl_dikembalikan = '0000-00-00' ";
        $data = array();
        $result = $mysqli->query($query);

        $num_rows = mysqli_num_rows($result);

        while ($row = mysqli_fetch_object($result)) {
            $nestedData['tgl_pinjam'] = $row->tgl_pinjam;
            $nestedData['tgl_kembali'] = $row->tgl_kembali;
            $nestedData['denda'] = $denda->hitung_denda($row->tgl_kembali);
            $nestedData['judul'] = $row->judul;
            $data[] = $nestedData;
        }
        return $data;
    }
}
