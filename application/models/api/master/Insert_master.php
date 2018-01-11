<?php class Insert_master extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function addNewTag($insert){
		$this->db->set($insert);
		$this->db->insert('ttags');
		return $this->db->insert_id();
	}
}
?>