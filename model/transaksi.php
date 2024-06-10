<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once "database/koneksi.php";

class transaksi
{
   public function get_transaksi($no_mhs = 0 ,$start = null,$length = null,  $search = null)
   { 
      global $mysqli;
      $query = "SELECT a.* , b.judul FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku";
       
      $search_query ="";
      // jika pencarian berjalan
      if($search != null){
         $search_query = "and (judul like  '%".$search."%' or tgl_pinjam like  '%".$search."%' or tgl_kembali like  '%".$search."%' ) ";
      }
      // untuk halaman pagination
      if ($no_mhs != 0) {
         $query .= " WHERE a.no_mhs=" . $no_mhs ." ".$search_query." Order by a.tgl_pinjam DESC ";
      } 
      if($start != null ){
         $query .= "LIMIT ".$length." OFFSET ".$start;
      }

      //var_dump($query);die();
      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);
      
         while ($row = mysqli_fetch_object($result)) {
            $nestedData['kd_buku']=$row->kd_buku;
            $nestedData['no_mhs']=$row->no_mhs;
            $nestedData['kd_jns_buku']=$row->kd_jns_buku; 
            $nestedData['operator_pinjam']=$row->operator_pinjam; 
            $nestedData['operator_kembali']=$row->operator_kembali;
            $nestedData['tgl_pinjam']=$row->tgl_pinjam;  
            $nestedData['tgl_kembali']=$row->tgl_kembali; 
            $nestedData['tgl_dikembalikan']=$row->tgl_dikembalikan;
            $nestedData['denda']=$this->hitung_denda($row->tgl_kembali); 
            $nestedData['lunas']=$row->lunas; 
            $nestedData['status']=$row->status;
            $nestedData['kd_konfig']=$row->kd_konfig; 
            $nestedData['judul']=$row->judul;           
             $data[] = $nestedData;
          }

         //query get total
           $query_total="SELECT count(b.judul) as jumlah FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku"; 
              // jika pencarian berjalan
            if ($no_mhs != 0) {
               $query_total .= " WHERE a.no_mhs=" . $no_mhs;
            } 
            $result_total = $mysqli->query($query_total);
            $total = mysqli_fetch_object($result_total);
  
            // get total filtered
            $total_filtered =$total;
            if($search != null ){
               $query_total_filtered ="SELECT count(b.judul) as jumlah FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku  WHERE a.no_mhs=" . $no_mhs." and (judul like  '%".$search."%' or tgl_pinjam like  '%".$search."%' or tgl_kembali like  '%".$search."%' ) "; 
               $result_total_filtered = $mysqli->query($query_total_filtered);
               $total_filtered = mysqli_fetch_object($result_total_filtered);
            } 

          $response = array(
             'status' => 1,
             'message' => 'Sukses',
             'data' => $data,
             'total_data'=>(int)$total->jumlah,
             'total_filtered'=>(int)$total_filtered->jumlah
          );

      header('Content-Type: application/json');
      echo json_encode($response);
   }


   private function hitung_denda($tgl_kembali){
      global $mysqli;
      $cdate=date('Y-m-d');

      //cek jika tgl kembalian di bawah bulan febuari
      $batas=date("2024-01-31");
      if ($batas >= $tgl_kembali ) {
         $data=$this->hitung_denda_baru_dan_lama($tgl_kembali,$batas);
         return $data; 
      }

            //hitung jumlah hari keterlambatan
      $jumlah_hari = $mysqli->query("SELECT (TO_DAYS('" . $cdate . "') - TO_DAYS('" . $tgl_kembali . "')) as intv");
      $j_hari=mysqli_fetch_assoc($jumlah_hari);
      $j_hari=(int)$j_hari['intv'];

      
       //hitung hari keterlambatan dikurangi hari libur(actual days)	
      $jumlah_hari_libur = $mysqli->query("SELECT count(*) AS jl FROM libur WHERE tgl_libur BETWEEN '" . $tgl_kembali . "' AND CURRENT_DATE()");
      $j_libur=mysqli_fetch_assoc($jumlah_hari_libur);
      $j_libur=(int)$j_libur['jl'];


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
         'denda'=>$denda,
         'jhd'=>$jhd
      );

      return $data;
    
   }

   public function hitung_denda_baru_dan_lama($tgl_kembali,$batas){
      global $mysqli;   
         
         //-----------------hitung jumlah hari denda 1---------------------
               //hitung jumlah hari keterlambatan 
             $jumlah_hari1 = $mysqli->query("SELECT (TO_DAYS('" . $batas . "') - TO_DAYS('" . $tgl_kembali . "')) as intv");
             $j_hari_1=mysqli_fetch_assoc($jumlah_hari1);
             $j_hari_1=(int)$j_hari_1['intv'];

                   
              //hitung hari keterlambatan dikurangi hari libur(actual days)	
             $jumlah_hari_libur = $mysqli->query("SELECT count(*) AS jl FROM libur WHERE tgl_libur BETWEEN '" . $tgl_kembali . "' AND '" .$batas."'");
             $j_libur_1=mysqli_fetch_assoc($jumlah_hari_libur);
             $j_libur_1=(int)$j_libur_1['jl'];


               //jumlah hari 
              $jh_denda_1 = $j_hari_1-$j_libur_1;
      //-- --------------end ------------------------------
                  
       //-----------------hitung jumlah hari denda 2--------------------
            $cdate=date('Y-m-d');   
            //hitung jumlah hari keterlambatan 
             $jumlah_hari_2 = $mysqli->query("SELECT (TO_DAYS('" . $cdate . "') - TO_DAYS('" . $batas . "')) as intv");
             $j_hari_2=mysqli_fetch_assoc($jumlah_hari_2);
             $j_hari_2=(int)$j_hari_2['intv'];

             $batas2 = "2024-02-01";
              //hitung hari keterlambatan dikurangi hari libur(actual days)	
             $jumlah_hari_libur_2 = $mysqli->query("SELECT count(*) AS jl FROM libur WHERE tgl_libur BETWEEN '" . $batas2 . "' AND '" .$cdate."'");
             $j_libur_2=mysqli_fetch_assoc($jumlah_hari_libur_2);
             $j_libur_2=(int)$j_libur_2['jl'];

              //jumlah hari 
              $jh_denda_2 = $j_hari_2-$j_libur_2;
       //-----------------end hitung jumlah hari denda 2--------------------
        
       $jh_denda_tot=$jh_denda_1+$jh_denda_2;

       if ($jh_denda_tot > 0) { 
         $denda_1=$jh_denda_1 * 500;
         if($jh_denda_1 == -1){
             $denda_1=$jh_denda_1 * 1000;
           }
          $denda_2=$jh_denda_2 * 1000;
          $denda = $denda_1 + $denda_2 ;
          $jhd = $jh_denda_tot;


       }
       else {
         $denda = 0;
         $jhd = 0;
       }

       $data = array(
         'denda'=>$denda,
         'jhd'=>$jhd
      );
      return $data;
    }

   public function get_transaksi_skripsi($no_mhs = 0,$start = null,$length = null)
   { 
      global $mysqli;
      $query = "SELECT * FROM skripsi";
      if ($no_mhs != 0) {
         $query .= " WHERE nim=" . $no_mhs . " Order by tgl_pinjam DESC ";
      }

      if($start != null ){
         $query .= "LIMIT ".$length." OFFSET ".$start;
      }
      //var_dump($query);die();
      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);
         while ($row = mysqli_fetch_object($result)) {
            $nestedData['nim']=$row->nim;
            $nestedData['judul']=$row->judul;
            $nestedData['tgl_kembali']=$row->tgl_kembali; 
            $nestedData['tgl_pinjam']=$row->tgl_pinjam; 
             $data[] = $nestedData;
          }
          
          $response = array(
             'status' => 1,
             'message' => 'data Transaksi Sukses.',
             'data' => $data
          );
 
      header('Content-Type: application/json');
      echo json_encode($response);
   }
   
   public function get_total_transaksi_buku($no_mhs){
      global $mysqli;
      $query_total="SELECT count(b.judul) as jumlah FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku"; 
      // jika pencarian berjalan
      if ($no_mhs != 0) {
         $query_total .= " WHERE a.no_mhs=" . $no_mhs;
      } 
      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);
      $data=(int)$total->jumlah;
      echo $data;
   }

    public function get_total_transaksi_skripsi($no_mhs){
      global $mysqli;
      $query_total="SELECT count(judul) as jumlah FROM skripsi"; 
      // jika pencarian berjalan
      if ($no_mhs != 0) {
         $query_total .= " WHERE nim=" . $no_mhs;
      } 
      $result_total = $mysqli->query($query_total);
      $total = mysqli_fetch_object($result_total);
      $data=(int)$total->jumlah;
      echo $data;
   }

   public function get_is_borrow($no_mhs){
      global $mysqli;
      $query="SELECT a.*,b.judul FROM transaksi a LEFT JOIN  buku b ON a.`kd_buku` = b.kd_buku  WHERE no_mhs=" . $no_mhs ." AND tgl_dikembalikan = '0000-00-00' "; 
      $data = array();
      $result = $mysqli->query($query);
      $num_rows = mysqli_num_rows($result);

      if($num_rows > 0){
         while ($row = mysqli_fetch_object($result)) {
            $nestedData['kd_buku']=$row->kd_buku;
            $nestedData['no_mhs']=$row->no_mhs;
            $nestedData['kd_jns_buku']=$row->kd_jns_buku; 
            $nestedData['operator_pinjam']=$row->operator_pinjam; 
            $nestedData['operator_kembali']=$row->operator_kembali;
            $nestedData['tgl_pinjam']=$row->tgl_pinjam;  
            $nestedData['tgl_kembali']=$row->tgl_kembali; 
            $nestedData['list_denda']=$this->hitung_denda($row->tgl_kembali);  
            $nestedData['judul']=$row->judul;           
             $data[] = $nestedData;
          }
   
          $response = array(
            'status' => 1,
            'message' => 'Data Transaksi ada',
            'data' => $data
         );

      }
      else {
            
          $response = array(
            'status' => 0,
            'message' => 'Data Tidak ada',
         );
      }



         header('Content-Type: application/json');
         echo json_encode($response);
   }


   
}