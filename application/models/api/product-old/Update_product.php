<?php class Update_product extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//adam
	public function updateDate($update,$id){
		$this->db->set($update);
		$this->db->where($id);
		$result = $this->db->update('t_users');
		return $result;
	}
	
	public function updateProduct($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('tproductmeta');
		return $result;
	}
	
	public function updateReview($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('treview');
		return $result;
	}
}
?>