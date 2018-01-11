<?php class Update_order extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function Update_fleet($ffleetid,$where){
		$this->db->set('ffleetid',$ffleetid);
		$this->db->where('forderid',$where);
		$result = $this->db->update('t_cart');
		//echo $this->db->last_query();
		return $result;
	}

	public function BookingStatus($update,$where){
		$this->db->set('forderprocessed',$update);
		$this->db->where('forderid',$where);
		$result = $this->db->update('t_order');
		//echo $this->db->last_query();
		return $result;
	}

	public function update_device_id($update,$where){
		$this->db->set($update);
		$this->db->where('fdeviceid',$where);
		$result = $this->db->update('t_device');
		//echo $this->db->last_query();
		return $result;
	}

	public function update_userVehicle_order($update,$where){
		$this->db->set('forderdataproduct',$update);
		$this->db->where('forderid',$where);
		$result = $this->db->update('t_order');
		//echo $this->db->last_query();
		return $result;
	}

	public function update_userVehicle($update,$where){
		$this->db->set('fcartproduct',$update);
		$this->db->where('forderid',$where);
		$result = $this->db->update('t_cart');
		//echo $this->db->last_query();
		return $result;
	}

	public function updateState($update,$where){
		$this->db->set('forderprocessed',$update);
		$this->db->where('forderid',$where);
		$result = $this->db->update('t_order');
		//echo $this->db->last_query();
		return $result;
	}
	
	public function updateClaimOrder($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('t_claimedproduct');
		//echo $this->db->last_query();
		return $result;
	}
	
	public function updateOrder($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('t_order');
		//echo $this->db->last_query();
		return $result;
	}
	
	public function updateCart($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('t_cart');
		//echo $this->db->last_query();
		return $result;
	}
	
	public function updateProduct($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('t_product');
		//echo $this->db->last_query();
		return $result;
	}
	
	public function updateActivity($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('t_activity');
		//echo $this->db->last_query();
		return $result;
	}

	//adam
	public function updateCartProduct($update,$where){
		$this->db->set($update);
		$this->db->where('fuserid',$where);
		$result = $this->db->update('t_claimedproduct');
		//echo $this->db->last_query();
		return $result;
	}

	//adam
	public function updateCartSchedule($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('t_claimedproduct');
		//echo $this->db->last_query();
		return $result;
	}

	public function update_data_user_vehicle($update,$where){
		$this->db->set($update);
		$this->db->where('fuservehicleid',$where);
		$result = $this->db->update('t_uservehicle');
		//echo $this->db->last_query();
		return $result;
	}


}
?>