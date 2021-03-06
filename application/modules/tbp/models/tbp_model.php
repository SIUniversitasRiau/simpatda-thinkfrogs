<?php
class Tbp_model extends CI_Model {
	function get_daftartbp(){
		$sql = " SELECT distinct NOMOR_TBP,TGL_BAYAR, TOTAL_BAYAR FROM TBP ";
		$result = $this->db->query($sql)->result();
		return $result;
	}
	
	function getSPT($id_wp){
		$sql = " select c.id_spt,c.nomor_spt as skpd,c.periode_akhir as jatuh_tempo,d.KODE_REKENING as kode_akun
,d.NAMA_REKENING as nama_akun,c.jumlah_pajak as nominal_ketetapan,coalesce(e.NOMINAL_BAYAR,0) total_bayarlalu
,coalesce(e.NOMINAL_BAYAR,0) nominal_bayar,coalesce(e.KURANG_BAYAR,0) as kurang_bayar,coalesce(e.DENDA,0) as denda
from  wajib_pajak a
inner join jenis_usaha b on a.ID_JENIS_USAHA = b.ID_JENIS_USAHA
inner join spt c on a.ID_WAJIB_PAJAK = c.ID_WAJIB_PAJAK
inner join REKENING d on c.ID_REKENING = d.ID_REKENING
left join TBP e on c.ID_SPT = e.ID_SPT
where a.id_wajib_pajak = $id_wp ";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	function getPAJAKOA($id_rekening){
		$sql = " SELECT coalesce(sum(b.JUMLAH_PAJAK),0) as NOMINAL_BAYAR,a.NAMA_REKENING as NAMA_AKUN, a.ID_REKENING as KODE_AKUN,a.KODE_REKENING
FROM REKENING a
inner join spt b on a.ID_REKENING = b.ID_REKENING
where (a.objek = '04' OR a.objek = '08') and b.id_rekening = '$id_rekening'
group by a.NAMA_REKENING,a.ID_REKENING,a.KODE_REKENING";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	function save_data(){
		$data_oa = json_decode($this->input->post('rincian'),true) ;
		$data_sa = json_decode($this->input->post('rincian2'),true) ;
		$idskpd = $this->input->post('idskpd');
		$nomor_tbp = $this->input->post('nomor_tbp');
		$tgl_bayar = $this->input->post('tgl_bayar');
		$id_bendahara = $this->input->post('id_bendahara');
		$id_jurnal = $this->input->post('id_jurnal');
		$id_wp = $this->input->post('id_wp');
		$keterangan = $this->input->post('keterangan');
		$total_setor = $this->input->post('total_setor');
		
		$this->db->trans_start();
		for($i=0;$i < count($data_sa);$i++){
			
			$result = $this->db->query(" INSERT INTO TBP (ID_SKPD,NOMOR_TBP,TGL_BAYAR,ID_BENDAHARA,ID_JURNAL,ID_NPWPD,TOTAL_BAYAR,ID_SPT,KETERANGAN,NOMINAL_BAYAR,KURANG_BAYAR,DENDA) VALUES (".$idskpd.",'".$nomor_tbp."','".prepare_date($tgl_bayar)."',".$id_bendahara.",".$id_jurnal.",".$id_wp.",'".$total_setor."',".$data_sa[$i]["idspt"].",'".$keterangan."',".$data_sa[$i]["nominal_bayar"].",".$data_sa[$i]["kurang_bayar"].",".$data_sa[$i]["denda"].") ");
		}
		
		for($y=0;$y < count($data_oa);$y++){
			
			$result = $this->db->query(" INSERT INTO TBP (ID_SKPD,NOMOR_TBP,TGL_BAYAR,ID_BENDAHARA,ID_JURNAL,ID_NPWPD,TOTAL_BAYAR,ID_REKENING,KETERANGAN) VALUES (".$idskpd.",'".$nomor_tbp."','".prepare_date($tgl_bayar)."',".$id_bendahara.",".$id_jurnal.",".$id_wp.",'".$total_setor."',".$data_oa[$y]["id_rekening"].",'".$keterangan."') ");
		}
		
		$this->db->trans_complete();
		
	}
	
	function delete_data($id){
		$this->db->trans_start();
		$result = $this->db->query(" DELETE FROM TBP WHERE NOMOR_TBP = '".$id."' ");
		$this->db->trans_complete();
	}
	
}

