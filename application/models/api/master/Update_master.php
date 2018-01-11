<?php class Update_master extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function updateUser($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('tusers');
		return $result;
	}
	
	public function updateDeviceToken($update,$key='fusersmetaid',$metaID,$userID=null){
		$this->db->set($update);
		$this->db->where($key,$metaID);
		$result = $this->db->update('tuserstoken');
		
		if(!empty($userID)){
			#update timestamp
			$this->db->set('fusertimestamp',time());
			$this->db->where('fuserid',$userID);
			$result = $this->db->update('tusers');
		}
		return $result;
	}
	
	public function updateTimestamp($update){	  
		$this->db->set('fusertimestamp',time());
		$this->db->where('fuserid',$update['userID']);
		$result = $this->db->update('tusers');
	}
	
	public function updatePassword($update){	  
		$this->db->set('fuserpassword',$update['newPassword']);
		$this->db->where('fuserid',$update['userID']);
		$result = $this->db->update('tusers');
	}
	
	public function updateAPIInfo(){	
		$this->db->set('fuserapiver',$this->input->post('api_ver',true));
		$this->db->set('fuserappver',$this->input->post('app_ver',true));
		$this->db->set('fuserosver',$this->input->post('os_ver',true));
		$this->db->set('fuserdevice',$this->input->post('device',true));
		$this->db->set('fusertimestamp',$this->input->post('timestamp',true));
		$this->db->where('fuserid',$this->input->post('user_id',true));
		$result = $this->db->update('tusers');
	}
	
	//public function updateUser($value,$key,$userID){	
//		$this->db->set($key,$value);
//		$this->db->where('fuserid',$userID);
//		$result = $this->db->update('tusers');
//	}
}
?>