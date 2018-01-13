<?php class Delete_order extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
   
	public function deleteClaim($where){
		$this->db->where($where);
		$this->db->delete('t_claimedproduct'); 
	}

	public function deleteBooking($where){
		$this->db->where('forderid',$where);
		$this->db->delete('t_order');
	}

	public function CancelBooking($where){
		$this->db->where('forderid',$where);
		$this->db->delete('t_claimedproduct');
	} 
}
?>