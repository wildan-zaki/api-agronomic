<?php class Select_user extends CI_Model {

	var $limit = 10;
	var $order = 'ASC';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function checkId($user,$offset = FALSE){
		$this->db->select('*');
		$this->db->where('fuserid', $user['fuserid']);
		$this->db->where_in('fuserstatus',array(0,1));
		$query = $this->db->get('t_users', $this->limit, $offset);
		if ($query->num_rows() > 0){
			$data = $query->row_array();
			return $data['fuserid'];
		}
	}
	
	public function checkEmail($user){
		$this->db->from('t_users');  
		$this->db->where('fuseremail',$user['fuseremail']);
		$this->db->where_in('fuserstatus',array(0,1));
		return $this->db->count_all_results();
	}

	//adam
	public function checkEmailExist($user){
		$this->db->from('t_users');
		$this->db->where('fuseremail',$user['fuseremail']);
		$this->db->where_in('fuserstatus',array(0,1));
		return $this->db->row_array();
	}
	
	public function checkUsername($user){
		$this->db->from('t_users');  
		$this->db->where('fusername',$user['username']);
		$this->db->where_in('fuserstatus',array(0,1));
		return $this->db->count_all_results();
	}
	
	public function checkPass($user){
		$this->db->from('t_users');  
		$this->db->where('fuserid',$user['userID']);
		$this->db->where('fuserpassword',$user['currentPassword']);
		$this->db->where_in('fuserstatus',array(0,1));
		return $this->db->count_all_results();
	}
	
	public function checkLogin($user,$offset = FALSE){
		$this->limit = 1;
		$this->db->select('fuserid');
		$this->db->where('fuseremail',$user['fuseremail']);
		$this->db->where('fuserpassword',$user['fuserpassword']);
		$this->db->where_in('fuserstatus',array(0,1));
		$query = $this->db->get('t_users', $this->limit, $offset);
		if ($query->num_rows() > 0){
			$data = $query->row_array();
			return $data['fuserid'];
		}
	}
	
	public function checkLoginSocmed($user,$offset = FALSE){
		$sql = "SELECT * FROM `t_usersmeta` m1, `t_usersmeta` m2, `t_usersmeta` m3, `t_users` u
			WHERE m1.fuserid = m2.fuserid
			and m1.fuserid = m3.fuserid
			and m1.fmetakey = 'socmed_type'
			and m1.fmetavalue = ".$this->db->escape($user['socmed_type'])."
			and m2.fmetakey = 'socmed_id'
			and m2.fmetavalue = ".$this->db->escape($user['socmed_id'])."
			and m1.fuserid = u.fuserid
			LIMIT 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->row_array();
		}
	}

	//adam
	public function getSocmed($user,$offset = FALSE){
		$sql = "SELECT *,m1.fmetavalue as socmed_type, m2.fmetavalue as socmed_id FROM `t_usersmeta` m1, `t_usersmeta` m2, `t_usersmeta` m3, `t_users` u
			WHERE m1.fuserid = m2.fuserid
			and m1.fuserid = m3.fuserid			
			and m1.fmetakey = 'socmed_type'
			
			and m2.fmetakey = 'socmed_id'
			and u.fuserid = ".$this->db->escape($user['fuserid'])."
			and m1.fuserid = u.fuserid
			LIMIT 1";
		$query = $this->db->query($sql);
		echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->row_array();
		}
	}
	
	public function get_user_activity($where,$offset,$single=false){
		$this->limit = 10;
		$offset = (int)$this->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if(!$single) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');	  
		$this->db->where($where);
		if($single)
			$query = $this->db->get('tactivity');
		else
			$query = $this->db->get('tactivity', $this->limit, $offset);
		
		//echo $this->db->last_query();
		
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_count_user_activity($where){
		$this->db->select('*');	  
		$this->db->where($where);
		$query = $this->db->get('tactivity');	
		
		return $this->db->count_all_results();
	}
	
	public function get_coupons($where,$single=false){
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->where('fcouponstatus',1);
		$query = $this->db->get('tcoupon');
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_coupon_where($where,$single=true){
		
		$this->db->select('*');
		
		$this->db->where($where);	
			  
		$query = $this->db->get('tcoupon');
		
		if ($query->num_rows() > 0){
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_where($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->where_in('fuserstatus',array(0,1));
		$query = $this->db->get('t_users');
		
		if ($query->num_rows() > 0){
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_meta_where($where,$single=true){
		$this->limit = 10;
		$offset = (int)$this->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if(!$single) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->where_in('fuserstatus',array(0,1));
		$this->db->join('t_users', 't_users.fuserid = t_usersmeta.fuserid');
		//if($single)
			$query = $this->db->get('t_usersmeta');
		//else
			//$query = $this->db->get('t_usersmeta', $this->limit, $offset);
		//echo $this->db->last_query();
		
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	//adam
	public function get_meta_where2($where,$single=true){
		$this->limit = 10;
		$offset = (int)$this->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if(!$single) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');	  
		$this->db->where($where);
		//$this->db->where_in('fuserstatus',array(0,1));
		//$this->db->join('t_users', 't_users.fuserid = t_usersmeta.fuserid');
		if($single)
			$query = $this->db->get('t_usersmeta');
		else
			$query = $this->db->get('t_usersmeta', $this->limit, $offset);
		
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
  
	public function get_count_meta($meta){
		$this->db->from('t_usersmeta');  
		$this->db->where($meta);	
		return $this->db->count_all_results();
	}

	//API-CREW
	public function checkLoginCrew($user){
		$this->db->select('*');
		$this->db->where('fcrewname',$user['fcrewname']);  
		$this->db->where('fcrewuserpassword',$user['fcrewuserpassword']);
		$this->db->where_in('fcrewstatus',array(0,1));
		$query = $this->db->get('tcrew');
		return $query->row_array();
	}

	public function get_fleetData($fleet){
		$this->db->select('*');
		$this->db->where('ffleetid',$fleet);
		$this->db->where_in('ffleetstatus',array(0,1));
		$query = $this->db->get('tfleet');
		return $query->row_array();
	}

	public function get_where_crew($where,$single=true){
		$this->db->select('*');
		$this->db->where('fcrewid',$where);
		$this->db->where_in('fcrewstatus',array(0,1));
		$query = $this->db->get('tcrew');

		if($query->num_rows() > 0){
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	public function get_where_crew_check($where,$single=true){
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where_in('fcrewstatus',array(0,1));
		$query = $this->db->get('tcrew');

		if($query->num_rows() > 0){
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

}
?>