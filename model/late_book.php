<?php

require_once "database/koneksi.php";
require_once "hitung_denda.php";

class late_book
{
    public function get_data_mhs_telat()
    {
        $denda = new Hitung();
        global $mysqli;

        // $query = "SELECT no_mhs,tgl_kembali,tgl_dikembalikan, CURDATE() AS date_now FROM transaksi  WHERE  DATE(tgl_kembali) < CURDATE() AND DATE(tgl_dikembalikan) = '0000-00-00' ORDER BY tgl_kembali DESC";
        $query = "SELECT 
            a.no_mhs AS nim,
            b.nama AS nm,
            a.no_barcode,
            a.tgl_pinjam AS tp,
            a.tgl_kembali AS tk,
            c.judul AS j,
            TO_DAYS(CURRENT_DATE()) - TO_DAYS(a.tgl_kembali) AS jml_telat 
            FROM
            transaksi a,
            anggota b,
            buku c 
            WHERE a.tgl_dikembalikan = '0000-00-00'  
            AND a.tgl_kembali = DATE_FORMAT(CURRENT_DATE()-1,'%Y-%m-%d')
            -- AND (  TO_DAYS(CURRENT_DATE()) - TO_DAYS(a.tgl_kembali)) > 500 
            AND b.no_mhs = a.no_mhs 
            AND c.kd_buku = a.kd_buku 
            ORDER BY a.tgl_kembali DESC";

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
