<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once "database/koneksi.php";
class hitung
{
  public function hitung_denda($tgl_kembali)
  {
    global $mysqli;
    $cdate = date('Y-m-d');

    //cek jika tgl kembalian di bawah bulan febuari
    $batas = date("2024-01-31");
    if ($batas >= $tgl_kembali) {
      $data = $this->hitung_denda_baru_dan_lama($tgl_kembali, $batas);
      return $data;
    }

    //hitung jumlah hari keterlambatan
    $jumlah_hari = $mysqli->query("SELECT (TO_DAYS('" . $cdate . "') - TO_DAYS('" . $tgl_kembali . "')) as intv");
    $j_hari = mysqli_fetch_assoc($jumlah_hari);
    $j_hari = (int)$j_hari['intv'];


    //hitung hari keterlambatan dikurangi hari libur(actual days)	
    $jumlah_hari_libur = $mysqli->query("SELECT count(*) AS jl FROM libur WHERE tgl_libur BETWEEN '" . $tgl_kembali . "' AND CURRENT_DATE()");
    $j_libur = mysqli_fetch_assoc($jumlah_hari_libur);
    $j_libur = (int)$j_libur['jl'];


    $jh_denda = $j_hari - $j_libur;
    //jika terlambat, hitung jumlah denda sesuai konfigurasi masing-masing buku				
    if ($jh_denda > 0) {
      //$denda = $jh_denda * $buku_config['denda'];
      $denda = $jh_denda * 1000;
      $jhd = $jh_denda;
    } else {
      $denda = 0;
      $jhd = 0;
    }

    $data = array(
      'denda' => $denda,
      'jhd' => $jhd
    );

    return $data;
  }

  public function hitung_denda_baru_dan_lama($tgl_kembali, $batas)
  {
    global $mysqli;

    //-----------------hitung jumlah hari denda 1---------------------
    //hitung jumlah hari keterlambatan 
    $jumlah_hari1 = $mysqli->query("SELECT (TO_DAYS('" . $batas . "') - TO_DAYS('" . $tgl_kembali . "')) as intv");
    $j_hari_1 = mysqli_fetch_assoc($jumlah_hari1);
    $j_hari_1 = (int)$j_hari_1['intv'];


    //hitung hari keterlambatan dikurangi hari libur(actual days)	
    $jumlah_hari_libur = $mysqli->query("SELECT count(*) AS jl FROM libur WHERE tgl_libur BETWEEN '" . $tgl_kembali . "' AND '" . $batas . "'");
    $j_libur_1 = mysqli_fetch_assoc($jumlah_hari_libur);
    $j_libur_1 = (int)$j_libur_1['jl'];


    //jumlah hari 
    $jh_denda_1 = $j_hari_1 - $j_libur_1;
    //-- --------------end ------------------------------

    //-----------------hitung jumlah hari denda 2--------------------
    $cdate = date('Y-m-d');
    //hitung jumlah hari keterlambatan 
    $jumlah_hari_2 = $mysqli->query("SELECT (TO_DAYS('" . $cdate . "') - TO_DAYS('" . $batas . "')) as intv");
    $j_hari_2 = mysqli_fetch_assoc($jumlah_hari_2);
    $j_hari_2 = (int)$j_hari_2['intv'];

    $batas2 = "2024-02-01";
    //hitung hari keterlambatan dikurangi hari libur(actual days)	
    $jumlah_hari_libur_2 = $mysqli->query("SELECT count(*) AS jl FROM libur WHERE tgl_libur BETWEEN '" . $batas2 . "' AND '" . $cdate . "'");
    $j_libur_2 = mysqli_fetch_assoc($jumlah_hari_libur_2);
    $j_libur_2 = (int)$j_libur_2['jl'];

    //jumlah hari 
    $jh_denda_2 = $j_hari_2 - $j_libur_2;
    //-----------------end hitung jumlah hari denda 2--------------------

    $jh_denda_tot = $jh_denda_1 + $jh_denda_2;

    if ($jh_denda_tot > 0) {
      $denda_1 = $jh_denda_1 * 500;
      if ($jh_denda_1 == -1) {
        $denda_1 = $jh_denda_1 * 1000;
      }
      $denda_2 = $jh_denda_2 * 1000;
      $denda = $denda_1 + $denda_2;
      $jhd = $jh_denda_tot;
    } else {
      $denda = 0;
      $jhd = 0;
    }

    $data = array(
      'denda' => $denda,
      'jhd' => $jhd
    );
    return $data;
  }
}
