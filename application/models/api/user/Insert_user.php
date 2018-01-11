<?php class Insert_user extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function addNewUser($insert){
		$insert['fuserregisterdate'] = time();
		$this->db->set($insert);
		$this->db->insert('t_users');
		return $this->db->insert_id();
	}
  
  public function addUserMeta($insert){
	  $this->db->set($insert);
	  $this->db->insert('t_usersmeta');
	  return $this->db->insert_id();
  }
  
  public function addUserDeviceToken($insert,$userID){
	 $this->db->set($insert);
	 $this->db->insert('t_userstoken');
	 $token_id = $this->db->insert_id();
	  
	  #update timestamp
	  $this->db->set('fusertimestamp',time());
      $this->db->where('fuserid',$userID);
	  $result = $this->db->update('t_users');
	  return $token_id;
  }
  
  public function addNewCoupon($insert){
	  $this->db->set($insert);
	  $this->db->insert('tcoupon');
	  return $this->db->insert_id();
  }
}
?>