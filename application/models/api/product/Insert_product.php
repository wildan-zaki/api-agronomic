<?php class Insert_product extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function addNewProduct($insert){
		$insert['fproductregisterdate'] = time();
		$this->db->set($insert);
		$this->db->insert('tproducts');
		$product_id = $this->db->insert_id();		
		
		$this->db->set('fproductid',$product_id);
		$this->db->set('fmetakey','following');
		$this->db->set('fmetavalue','');
		$this->db->insert('tproductsmeta');
		
		return $product_id;
	}
  
  public function addProductMeta($insert){
	  $this->db->set($insert);
	  $this->db->insert('tproductsmeta');
	  return $this->db->insert_id();
  }
  
  public function addProductDeviceToken($insert,$productID){
	 $this->db->set($insert);
	 $this->db->insert('tproductstoken');
	 $token_id = $this->db->insert_id();
	  
	  #update timestamp
	  $this->db->set('fproducttimestamp',time());
      $this->db->where('fproductid',$productID);
	  $result = $this->db->update('tproducts');
	  return $token_id;
  }
  
  public function addNewFollow($insert){
		$this->db->set($insert);
		$this->db->insert('tfollow');
		$follow_id = $this->db->insert_id();
		
		//get following update
		$this->db->select('fprofileid');
		$this->db->where('fproductid',$insert['fproductid']);	
		$qfollowing = $this->db->get('tfollow');
		
		if(!empty($qfollowing->num_rows()))
		{
			// update total following
			$update['fproducttotalfollowing'] = $qfollowing->num_rows();
			$upd['fproductid'] = $insert['fproductid'];
			$this->db->set($update);
			$this->db->where($upd);
			$this->db->update('tproducts');
			
			//update following meta
			$metavalue = array();
			foreach($qfollowing->result_array() as $row)
			{
				array_push($metavalue,$row['fprofileid']);	
			}
			
			if(!empty($metavalue))
			{
				$this->db->select('fmetavalue');
				$this->db->where('fproductid',$insert['fproductid']);	
				$this->db->where('fmetakey','following');	
				$following_meta = $this->db->get('tproductsmeta');
				
				$updatemeta['fmetavalue'] = serialize($metavalue);
				if($following_meta->num_rows())
				{
					$upd['fmetakey'] = 'following';
					$this->db->set($updatemeta);
					$this->db->where($upd);
					$this->db->update('tproductsmeta');
				}
				else
				{
					$updatemeta['fproductid'] = $insert['fproductid'];
					$updatemeta['fmetakey'] = 'following';
					$this->db->set($updatemeta);
					$this->db->insert('tproductsmeta');
				}
			}
		}
		
		//get follower update
		$this->db->select('fproductid');
		$this->db->where('fprofileid',$insert['fprofileid']);	
		$qfollower = $this->db->get('tfollow');
		if(!empty($qfollower->num_rows()))
		{
			unset($update);
			unset($upd);
			// update total follower
			$update['fproducttotalfollower'] = $qfollower->num_rows();
			$upd['fproductid'] = $insert['fprofileid'];
			$this->db->set($update);
			$this->db->where($upd);
			$this->db->update('tproducts');
		}
		return $follow_id;
  }
  
 	public function addNewMessage($insert)
	{		
		$this->db->set($insert);
		$this->db->insert('tmessage');
		$message_id = $this->db->insert_id();
		
		//check total message
		$this->db->from('tmessage');  
		$this->db->where(array('fproductid' => $insert['fproductid']));	
		$update['fproducttotalmessage'] = $this->db->count_all_results();
		
		//check total unread
		$this->db->from('tmessage');  
		$this->db->where(array('fproductid' => $insert['fproductid'],'fmessagestatus' => 1));	
		$update['fproducttotalmessageunread'] = $this->db->count_all_results();
		
		$upd['fproductid'] = $insert['fproductid'];
		$this->db->set($update);
		$this->db->where($upd);
		$this->db->update('tproducts');
			
		return $message_id;
	}
  
 	public function addNewDevice($insert)
	{		
		$this->db->set($insert);
		$this->db->insert('tdevice');
		$device_id = $this->db->insert_id();
			
		return $device_id;
	}

	public function addLogDevice($insert){
		$this->db->set($insert);
		$this->db->insert('tlogdevice');
		return $this->db->insert_id();
	}
}
?>