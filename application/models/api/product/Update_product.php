<?php class Update_product extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function updateProduct($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('tproducts');
		
		return $result;
	}
	
	public function updateProductMeta($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('tproductsmeta');
		return $result;
	}
	
	public function updateProductDevice($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('tdevice');
		return $result;
	}
	
	public function replaceProductMeta($replace){
		$this->db->replace('tproductsmeta', $replace);
	}
	
	public function updateDeviceToken($update,$key='fproductsmetaid',$metaID,$productID=null){
		$this->db->set($update);
		$this->db->where($key,$metaID);
		$result = $this->db->update('tproductstoken');
		
		if(!empty($productID)){
			#update timestamp
			$this->db->set('fproducttimestamp',time());
			$this->db->where('fproductid',$productID);
			$result = $this->db->update('tproducts');
		}
		return $result;
	}
	
	public function updateTimestamp($update){	  
		$this->db->set('fproducttimestamp',time());
		$this->db->where('fproductid',$update['productID']);
		$result = $this->db->update('tproducts');
	}
	
	public function updatePassword($update){	  
		$this->db->set('fproductpassword',$update['newPassword']);
		$this->db->where('fproductid',$update['productID']);
		$result = $this->db->update('tproducts');
	}
	
	public function updateAPIInfo(){	
		$this->db->set('fproductapiver',$this->input->post('api_ver',true));
		$this->db->set('fproductappver',$this->input->post('app_ver',true));
		$this->db->set('fproductosver',$this->input->post('os_ver',true));
		$this->db->set('fproductdevice',$this->input->post('device',true));
		$this->db->set('fproducttimestamp',$this->input->post('timestamp',true));
		$this->db->where('fproductid',$this->input->post('product_id',true));
		$result = $this->db->update('tproducts');
		
		$cart_id = md5($this->input->post('product_id',true).$this->input->post('app_ver',true).$this->input->post('device',true));
		return $cart_id;
	}
	
	public function updateMessage($update,$where){
		$this->db->set($update);
		$this->db->where($where);
		$result = $this->db->update('tmessage');
		
		//check total unread
		$this->db->from('tmessage');  
		$this->db->where(array('fproductid' => $where['fproductid'],'fmessagestatus' => 1));	
		$updateu['fproducttotalmessageunread'] = $this->db->count_all_results();
		
		$upd['fproductid'] = $where['fproductid'];
		$this->db->set($updateu);
		$this->db->where($upd);
		$this->db->update('tproducts');
		
		return $result;
	}
	
	//public function updateProduct($value,$key,$productID){	
//		$this->db->set($key,$value);
//		$this->db->where('fproductid',$productID);
//		$result = $this->db->update('tproducts');
//	}
}
?>