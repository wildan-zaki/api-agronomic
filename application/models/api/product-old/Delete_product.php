<?php class Delete_product extends CI_Model {

	 public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
   
	public function deleteProduct($where){
		$this->db->where($where);
		$this->db->delete('tproducts'); 
	}
   
	public function deleteUserMeta($where){
		$this->db->where($where);
		$this->db->delete('tproductsmeta'); 
	}
	
	public function deleteUserFollow($where)
	{
		$this->db->where($where);
		$this->db->delete('tfollow'); 
		
		//get following update
		$this->db->select('fprofileid');
		$this->db->where('fproductid',$where['fproductid']);	
		$qfollowing = $this->db->get('tfollow');
		
		// update total following
		$update['fproducttotalfollowing'] = $qfollowing->num_rows();
		$upd['fproductid'] = $where['fproductid'];
		$this->db->set($update);
		$this->db->where($upd);
		$this->db->update('tproducts');
		
		//update following meta
		$metavalue = array();
		foreach($qfollowing->result_array() as $row)
		{
			array_push($metavalue,$row['fprofileid']);	
		}
		
		$updatemeta['fmetavalue'] = (!empty($metavalue)) ? serialize($metavalue) : '';
		$upd['fmetakey'] = 'following';
		$this->db->set($updatemeta);
		$this->db->where($upd);
		$this->db->update('tproductsmeta');
		
		//get follower update
		$this->db->select('fproductid');
		$this->db->where('fprofileid',$where['fprofileid']);	
		$qfollower = $this->db->get('tfollow');
		unset($update);
		unset($upd);
		// update total follower
		$update['fproducttotalfollower'] = $qfollower->num_rows();
		$upd['fproductid'] = $where['fprofileid'];
		$this->db->set($update);
		$this->db->where($upd);
		$this->db->update('tproducts');
		
		return true;
	}
}
?>