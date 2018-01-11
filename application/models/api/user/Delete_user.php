<?php class Delete_user extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
   
	public function deleteUser($where){
		$this->db->where($where);
		$this->db->delete('t_users'); 
	}
   
	public function deleteUserMeta($where){
		$this->db->where($where);
		$this->db->delete('t_usersmeta'); 
	}

	public function deleteDeviceIntanceid($where){
		$this->db->where($where);
		$this->db->delete('tdevice');
	}
}
?>