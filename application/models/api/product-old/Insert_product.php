<?php class Insert_product extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
  public function addProduct($insert){
	  //$insert['freviewdate'] = time();
	  $this->db->set($insert);
	  $this->db->insert('tproduct');
	  return $this->db->insert_id();
  }
}
?>