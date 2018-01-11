<?php class Select_order extends CI_Model {
	var $limit = 10;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_count()
	{
		$this->db->select('*');		
		$query = $this->db->get('t_order');
		return $query->num_rows();
	}

	public function get_payment($where){
		$this->db->select('*');
		$this->db->where('f_paymentid',$where);
		$query = $this->db->get('t_payment');
		//echo $this->db->last_query();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_cart($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('t_cart');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_cartData($where){
		$this->db->select('*');
		$this->db->where('forderid',$where);
		$query = $this->db->get('t_cart');
		//echo $this->db->last_query();
		return $query->row_array(); 
	}

	public function get_dataCrew($where){
		$this->db->select('*');
		$this->db->where('ffleetid',$where);
		$query = $this->db->get('tcrew');
		return $query->result_array();
	}

	public function get_vehicle_data($where,$single=true){
		$this->db->select('*');
		$this->db->where('tuservehicle.fuservehicleid',$where);
		$this->db->join('tvehicle', 'tvehicle.fvehicleid = tuservehicle.fvehicleid');
		$this->db->join('tbrand','tbrand.fbrandid=tvehicle.fbrandid');
		$query = $this->db->get('tuservehicle');
		if ($query->num_rows() > 0){
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	public function get_detaildata_null($where){
		$this->db->select('*');
		$this->db->where('t_cart.forderid',$where);
		$this->db->join('t_cart', 't_cart.forderid=t_order.forderid');
		$this->db->join('thour', 't_cart.fstarthour=thour.fhourid');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_detaildata($where){
		$this->db->select('*');
		$this->db->where('t_cart.forderid',$where);
		$this->db->join('t_cart', 't_cart.forderid=t_order.forderid');
		$this->db->join('thour', 't_cart.fstarthour=thour.fhourid');
		$this->db->join('tfleet','tfleet.ffleetid=t_cart.ffleetid');
		$this->db->join('tvehicle','tvehicle.fvehicleid=tfleet.fvehicleid');
		$this->db->join('tbrand','tbrand.fbrandid=tvehicle.fbrandid');
		$this->db->join('tcrew','tcrew.ffleetid=tfleet.ffleetid');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_detaildata_fleet($where){
		$this->db->select('*');
		$this->db->where('t_cart.fcartid',$where);
		$this->db->join('tfleet','tfleet.ffleetid=t_cart.ffleetid');
		$query = $this->db->get('t_cart');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_dataordercart(){
		$this->db->select('*');
		//$this->db->where('fuserid',$where);
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_orderjoinclaim($where){
		$this->db->select('*');
		$this->db->where($where);
		$this->db->join('t_claimedproduct', 't_claimedproduct.fuserid=t_order.fuserid');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_list_order($where){
		$this->db->limit(1); 
		$this->db->select('*');
		$this->db->where('t_cart.forderid',$where);
		$this->db->join('t_order', 't_cart.fuserid = t_order.fuserid');
		$this->db->join('t_claimedproduct', 't_cart.fuserid = t_claimedproduct.fuserid');
		$query = $this->db->get('t_cart');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_all_order($where,$offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		//$this->db->limit(1); 
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where('t_order.forderprocessed < ',7,FALSE);
		$this->db->order_by('forderid', 'desc');
		//$this->db->join('t_cart', 't_cart.forderid = t_order.forderid');
		if($offset!==FALSE)		
			$query = $this->db->get('t_order',$this->limit, $offset);
		else
			$query = $this->db->get('t_order');
		
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	public function get_all_order_history($where,$offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		//$this->db->limit(1); 
		$this->db->select('*');
		//$this->db->where('forderprocessed',$prosess);
		$this->db->where('forderprocessed >= ',7,FALSE);
		$this->db->where($where);
		$this->db->order_by('forderid', 'desc');
		//$this->db->join('tuservehicle','t_order.fuserid = tuservehicle.fuserid');
		if($offset!==FALSE)		
			$query = $this->db->get('t_order',$this->limit, $offset);
		else
			$query = $this->db->get('t_order');
		
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	public function get_order($where){
		$this->db->select('*');
		$this->db->where('t_cart.forderid',$where);
		$this->db->join('t_cart','t_cart.forderid=t_order.forderid');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_request_cart($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('t_claimedproduct');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_dataClaimdeproduct($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('t_claimedproduct');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_schedule($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('t_claimedproduct');
		return $query->result_array();
	}

	public function get_request($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('t_claimedproduct');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_start_hour($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('thour');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_end_hour($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('thour');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_coupon($where){
		$this->db->select('fcouponid');
		$this->db->select('fcouponcode');
		$this->db->select('fcoupontype');
		$this->db->select('fcouponvalue');
		$this->db->where($where);
		$query = $this->db->get('tcoupon');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	//adam
	public function get_user_vehicle_data($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('tuservehicle');
		//if ($query->num_rows() > 0){
			//return $query->result_array();
		//}
			//echo $this->db->last_query();
			return $query->row_array();
	}

	//adam
	public function get_vehicle($where){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('tvehicle');
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	//adam
	public function get_fleet_stock(){
		$this->db->select('*');
		$query = $this->db->get('tfleet');
		return $query->num_rows();
	}
	
	public function get_claimed_where($where,$single=true,$exp=true){
		$this->db->select('*');	  
		$this->db->where($where);
		if($exp) $this->db->where('fexpireddate > UNIX_TIMESTAMP(NOW())');
		$query = $this->db->get('tclaimedticket');
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_cart_where($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		$query = $this->db->get('t_cart');
//		echo $this->db->last_query();
		if ($query->num_rows() > 0){
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	
	public function get_user_order($where,$offset,$single=false){
		$this->limit = 10;
		$offset = (int)$this->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if(!$single) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->join('t_ordercontact', 't_ordercontact.forderid = t_order.forderid');	
		$this->db->join('t_cart', 't_cart.forderid = t_order.forderid');		
		$this->db->join('tticket', 't_cart.fticketid = tticket.fticketid');	
		$this->db->group_by('t_order.forderid,t_cart.fticketid');
		$this->db->order_by('t_order.forderid','DESC');
		if($single)
			$query = $this->db->get('t_order');
		else
			$query = $this->db->get('t_order', $this->limit, $offset);
		
		//echo $this->db->last_query();
		
		if ($query->num_rows() > 0){			
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_count_user_order($where){
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->join('t_ordercontact', 't_ordercontact.forderid = t_order.forderid');	
		$this->db->join('t_cart', 't_cart.forderid = t_order.forderid');		
		$this->db->join('tticket', 't_cart.fticketid = tticket.fticketid');	
		$this->db->group_by('t_order.forderid,t_cart.fticketid');
		$query = $this->db->get('t_order');	
		
		return $this->db->count_all_results();
	}

	//adam
	public function claimed_product_get_where($where,$exp=true){
		$this->db->select('*');
		$this->db->where($where);
		if($exp) $this->db->where('fexpireddate > UNIX_TIMESTAMP(NOW())');
		$query = $this->db->get('t_claimedproduct');

		//if ($query->num_rows() > 0){
		//	if($single)
				return $query->result_array();
		//	else
		//		//echo $this->db->last_query();
				return $query->row_array();
		//}
	}

	public function get_where_product($where,$single=true){
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('t_product');

		if ($query->num_rows() > 0){
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	//adam
	public function get_subproduct($where){
		$this->db->select('fsubproductprice');
		$this->db->where($where);
		$query = $this->db->get('tsubproduct');

		if($query->num_rows() > 0){
			return $query->result_array();
		}
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
	public function order_get_where($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		//$this->db->join('t_ordercontact', 't_ordercontact.forderid = t_order.forderid');
		$query = $this->db->get('t_order');
		
		if ($query->num_rows() > 0){
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	//adam
	public function order_option($where,$single=true){
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where_in('foptionstatus',1);
		$query =  $this->db->get('t_option');

		if($query->num_rows() > 0){
			if($single){
				$row = $query->row_array();
				return $row['foptionvalue'];
			}
			else
				return $query->result_array();
		}
	}
	
	public function order_status($status){
		$text = '';
		switch($status){
			case 4:$text = 'validated';break;
			case 3:$text = 'purchased';break;
			case 2:$text = 'pending';break;
			default:$text = 'incomplete';break;
		}
		return $text;
	}

	

	public function DeleteBooking($where){
		$this->db->select('*');
		$this->db->where('forderid',$where);
		//$this->db->join('t_cart','t_cart.forderid = t_order.forderid');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}



	//API-CREW

	public function get_where($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		//$this->db->where_in('fuserstatus',array(0,1));
		$query = $this->db->get('tcrew');
		
		if ($query->num_rows() > 0){
			if($single)
				//echo $this->db->last_query();
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	public function get_all_order_crew($where,$offset){
		$date = new DateTime("now");
		$curr_date = $date->format('Y-m-d ');
		if($offset) $offset = ($offset-1)*$this->limit;
		//$this->db->limit(1); 
		$this->db->select('*');
		$this->db->where('ffleetid',$where); 
		$this->db->where('t_order.forderprocessed < ',7,FALSE);
		$this->db->where('DATE(fbookingdate)',$curr_date);
		$this->db->join('t_cart','t_cart.forderid=t_order.forderid');
		$this->db->order_by('fstarthour', 'asc');

		if($offset!==FALSE)		
			$query = $this->db->get('t_order',$this->limit, $offset);
		else
			$query = $this->db->get('t_order');
		
			return $query->result_array();
	}

	public function get_count_crew()
	{
		$this->db->select('*');		
		$query = $this->db->get('t_order');
		return $query->num_rows();
	}

	public function get_fleet_id_crew($where){
		$this->db->select('*');
		$this->db->where('fcrewid',$where);
		$query = $this->db->get('tcrew');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_start_hour_crew($where){
		$this->db->select('*');
		$this->db->where('fhourid',$where);
		//$this->db->order_by('fhourid','ASC');
		$query = $this->db->get('thour');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_end_hour_crew($where){
		$this->db->select('*');
		$this->db->where('fhourid',$where);
		$query = $this->db->get('thour');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_dataUser($where){
		$this->db->select('*');
		$this->db->where('fuserid',$where);
		$query = $this->db->get('tusers');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_all_order_detail_crew($where,$orderid){
		$this->db->select('*');
		$this->db->where('ffleetid',$where);
		$this->db->where('t_order.forderid',$orderid);
		$this->db->where('DATE(FROM_UNIXTIME(forderdate)) = CURDATE()');
		$this->db->join('t_cart','t_cart.forderid=t_order.forderid');
		//$this->db->join('tcoupon','t_order.fordervoucherdata=tcoupon.fcouponcode');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_order_data_crew($where){
		$this->db->select('*');
		$this->db->where('ffleetid',$where);
		$this->db->join('t_cart','t_cart.forderid=t_order.forderid');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_order_data_process($where,$complete,$orderid){
		$this->db->select('*');
		$this->db->where('ffleetid',$where);
		$this->db->where('forderprocessed',$complete);
		$this->db->where('t_order.forderid != "$orderid"');
		$this->db->join('t_cart','t_cart.forderid=t_order.forderid');
		$query = $this->db->get('t_order');
		return $query->result_array();
	}

	public function get_order_data($where){
		$this->db->select('*');
		$this->db->where('forderid',$where);
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	//cek state
	public function get_all_order_matchingCrew($where,$matchingcrew,$orderid,$complete){
		$this->db->select('*');
		$this->db->where('ffleetid',$where);
		$this->db->where('forderprocessed != ',$matchingcrew);
		$this->db->where('t_order.forderid != ',$orderid);
		$this->db->where('forderprocessed != ',$complete);
		$this->db->where('DATE(forderdate) = CURDATE()');
		$this->db->join('t_cart','t_cart.forderid=t_order.forderid');
		$query = $this->db->get('t_order');
		return $query->result_array();
	}

	public function get_today($where){
		$this->db->select('*');
		$this->db->where('forderid',$where);
		$this->db->where('DATE(FROM_UNIXTIME(forderdate)) = CURDATE()');
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}
 
	public function get_model($where){
		$this->db->select('*');
		$this->db->where('fvehicleid',$where);
		$this->db->join('tbrand','tbrand.fbrandid=tvehicle.fbrandid');  
		$query = $this->db->get('tvehicle'); 
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function cek_device_id($where){
		$this->db->select('*');
		$this->db->where('fdeviceimei',$where);
		$query = $this->db->get('tdevice');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_registrationIds($where){
		$this->db->select('fdeviceinstanceid');
		$this->db->where('forderid',$where);
		$this->db->where('fis_crew',0);
		$this->db->join('tdevice','tdevice.fuserid=t_order.fuserid');
		$query = $this->db->get('t_order');
		return $query->result_array();
	}

	public function get_tcrew($where){
		$this->db->select('*');
		$this->db->where('fcrewid',$where);
		$query = $this->db->get('tcrew');
		//echo $this->db->last_query();
		return $query->row_array();
	}

	public function get_chassis($where){
		$this->db->select('*');
		$this->db->where('tchassis.fchassisid',$where);
		$this->db->join('tcondition','tcondition.fchassisid=tchassis.fchassisid');
		$this->db->join('tuservehicle','tuservehicle.fuservehicleid=tcondition.fuservehicleid');
		$this->db->join('t_order','t_order.forderid=tcondition.forderid');
		$query = $this->db->get('tchassis');
		return $query->result_array();
	}

	public function get_cart_product($where){
		$this->db->select('*');
		$this->db->where('forderid',$where);
		$query = $this->db->get('t_cart');
		//echo $this->db->last_query();
		return $query->row_array();	
	}

	public function get_torder_data($where){
		$this->db->select('*');
		$this->db->where('forderid',$where);
		$query = $this->db->get('t_order');
		//echo $this->db->last_query();
		return $query->row_array();
	}

}
?>