<?php class Insert_order extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function BookingCancel($forderid,$reason){
		$this->db->set('forderid',$forderid);
		$this->db->set('freason',$reason);
		$this->db->insert('t_bookingcancel');
		return $this->db->insert_id();
	}

	public function createChassis($insert){
		$this->db->set($insert);
		$this->db->insert('t_condition');
		return $this->db->insert_id();
	}

	public function log_device_id($message,$logtime){
		$this->db->set('flogmessage',$message);
		$this->db->set('flogtime',$logtime);
		$this->db->insert('t_logdevice');
		return $this->db->insert_id();
	}

	public function add_device_id($insert){
		$insert['fdevicecreatetime'] = time();
		$this->db->set($insert);
		$this->db->insert('t_device');
		return $this->db->insert_id();
	}
	
	public function claimOrder($insert){
		$this->db->replace('t_claimedticket', $insert);
		return true;
	}
	
	public function createOrder($insert){
		//$insert['forderdate2'] = time();
		$this->db->insert('t_order', $insert);
		return $this->db->insert_id();
	}
	
	public function createCart($insert){
		//$insert['fbookingdate'] = date("Y-m-d");
		$this->db->insert('t_cart', $insert);
		return $this->db->insert_id();
	}
	
	public function createContact($insert){
		$this->db->insert('t_ordercontact', $insert);
		return $this->db->insert_id();
	}
  
	public function createActivity($insert){
		$this->db->set($insert);
		$this->db->insert('t_activity');
		return $this->db->insert_id();
	}

	//adam
	public function insertCartProduct($insert){
		$this->db->set($insert);
		$this->db->insert('t_cartproduct');
		return $this->db->insert_id();
	}

	public function insertCartHour($insert){
		$this->db->set($insert);
		$this->db->insert('t_carthour');
		return $this->db->insert_id();
	}

	public function insertClaimedProduct($insert){
		$this->db->set($insert);
		$this->db->insert('t_claimedproduct');
		return $this->db->insert_id();
	}

	  
}
?>