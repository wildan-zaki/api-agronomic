<?php class Select_product extends CI_Model {

	var $limit = 10;
	var $order_by = '(t_product.f_productid)';
	var $order = 'DESC';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function get($where='',$offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		
		$sort_by = ($this->input->post('sort_by',TRUE)) ? $this->input->post('sort_by',TRUE) : $this->order_by;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : $this->order;
		switch($sort_by)
		{
			case 'cheapest':
				$sort = 'ASC';
				$sort_by = '(f_productprice)';
				break;
			case 'expensive':
				$sort = 'DESC';
				$sort_by = '(f_productprice)';
				break;
			case 'latest':
				$sort_by=$this->order_by;
				break;
			default:
				$sort_by='(f_productcreatedate)';
				break;
		}	
		$this->db->select('*');
		$this->db->join('t_petani', 't_product.f_petaniid = t_petani.f_petaniid');
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		if(!empty($where) && strpos($where,'fmetakey')!==FALSE) $this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		
		if(!empty($where)) $this->db->where($where);
		
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
			
		$this->db->order_by($sort_by,$sort);
		$this->db->group_by('t_product.f_productid');
		
		if($offset!==FALSE) 
			$query = $this->db->get('t_product', $this->limit, $offset);
		else
			$query = $this->db->get('t_product');
		
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	public function get_count($where='')
	{		
		$this->db->select('*');
		$this->db->join('t_petani', 't_product.f_petaniid = t_petani.f_petaniid');
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		if(!empty($where) && strpos($where,'fmetakey')!==FALSE) $this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		
		if(!empty($where)) $this->db->where($where);
				
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
			
		$this->db->group_by('t_product.f_productid');
		
		$query = $this->db->get('t_product');
		return $query->num_rows();
	}


	public function get_popular_product($where='')
	{
		$sort_by  = '(f_producttotalbuy)';
		$sort ='DESC';
		$this->db->select('*');
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		if(!empty($where) && strpos($where,'fmetakey')!==FALSE) $this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		
		if(!empty($where)) $this->db->where($where);
		
		
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
			
		$this->db->order_by($sort_by,$sort);
		$this->db->limit(5);
		
		$query = $this->db->get('t_product');
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	public function get_featured_product($where='',$type)
	{
		$sort_by = ($this->input->post('sort_by',TRUE)) ? $this->input->post('sort_by',TRUE) : $this->order_by;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : $this->order;

		$this->db->select('*');
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		if(!empty($where)) $this->db->where($where);
		$this->db->where('t_product.f_productstatus',1);
		switch ($type) {
			case 'popular':
				$sort_by  = '(f_producttotalbuy)';
				break;
			case 'featured':
				# code...
				break;
			case 'bestSell':
				$sort_by  = 'f_producttotalbuy';
				$sort = 'DESC';
				break;
			default:
				$sort_by  = 'f_productcreatedate';
				$sort = 'ASC';
				break;
		}
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
			
		$this->db->group_by('t_product.f_productid');
		$this->db->order_by($sort_by,$sort);
		$this->db->limit(4);
		$query = $this->db->get('t_product');
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	public function get_promo_product($where='')
	{
		$sort_by = ($this->input->post('sort_by',TRUE)) ? $this->input->post('sort_by',TRUE) : $this->order_by;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : $this->order;

		$this->db->select('*');
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		if(!empty($where)) $this->db->where($where);
		
		
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
			
		$this->db->group_by('t_product.f_productid');
		$this->db->order_by($sort_by,$sort);

		$query = $this->db->get('t_product');
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function get_meta_where($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->join('t_product', 't_product.f_productid = t_productmeta.f_productid');
		$query = $this->db->get('t_productmeta');
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_where($where,$single=true){
		$this->db->select('*');
		
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		
		$this->db->where($where);
		
		$this->db->group_by('t_product.f_productid');
		$query = $this->db->get('t_product');
		
		//echo $this->db->last_query();
		
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_search($where='',$offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		$keyword = $this->input->post('keyword',TRUE);
		$sort_by = ($this->input->post('sort_by',TRUE)) ? $this->input->post('sort_by',TRUE) : $this->order_by;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : $this->order;
		
		switch($sort_by)
		{
			case 'cheapest':
				$sort = 'ASC';
				$sort_by = '(f_productprice)';
				break;
			case 'expensive':
				$sort = 'DESC';
				$sort_by = '(f_productprice)';
				break;
			case 'latest':
				$sort_by=$this->order_by;
				break;
			default:
				$sort_by='(f_producttotalbuy)';
				break;
		}		
		$this->db->select('*');
		
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		if(!empty($where) && strpos($where,'fmetakey')!==FALSE) $this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		
		if(!empty($where)) $this->db->where($where);
		
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));

		$this->db->where('(f_productname like '.$this->db->escape('%'.$keyword.'%').' OR f_productdescription like '.$this->db->escape('%'.$keyword.'%').' OR f_kategoriname like '.$this->db->escape('%'.$keyword.'%').')');
			
		$this->db->order_by($sort_by,$sort);
		$this->db->group_by('t_product.f_productid');
		
		if($offset!==FALSE) 
			$query = $this->db->get('t_product', $this->limit, $offset);
		else
			$query = $this->db->get('t_product');
		
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function get_search_count($where=''){
		$this->db->select('*');
		$keyword = $this->input->post('keyword',TRUE);
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		if(!empty($where) && strpos($where,'fmetakey')!==FALSE) $this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		
		if(!empty($where)) $this->db->where($where);
		
		
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
			
		$this->db->group_by('t_product.f_productid');
		$this->db->where('(f_productname like '.$this->db->escape('%'.$keyword.'%').' OR f_productdescription like '.$this->db->escape('%'.$keyword.'%').' OR f_kategoriname like '.$this->db->escape('%'.$keyword.'%').')');
		
		$query = $this->db->get('t_product');
		return $query->num_rows();
	}
	
	public function get_event($where,$offset=FALSE,$single=true,$limit=''){
		$limit = (!empty($limit)) ? $limit : $this->limit;
		if($offset) $offset = ($offset-1)*$limit;
		$this->db->select('*');
		
		//$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid AND t_productmeta.fmetakey = "date"');
		
		$this->db->where('t_product.fproducttype','event');
		//
		$this->db->where($where);
		
		//$this->db->having("distance <= '".$this->distance."'");
		
		$this->db->group_by('t_product.f_productid');
		
		if($offset!==FALSE) 
			$query = $this->db->get('t_product', $this->limit, $offset);
		else
			$query = $this->db->get('t_product');
		
		//echo $this->db->last_query();
		
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	
	public function get_tags_search($where ='',$offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		
		$keyword = $this->input->post('keyword',TRUE);

		$this->db->select('*');
		
		if(!empty($where)) $this->db->where($where);
		$this->db->where('ttags.ftagstatus',1);
		
		$this->db->where('(ftagname like '.$this->db->escape('%'.$keyword.'%').' OR ftagdesc like '.$this->db->escape('%'.$keyword.'%').')');
		
		if($offset!==FALSE) 
			$query = $this->db->get('ttags', $this->limit, $offset);
		else
			$query = $this->db->get('ttags');
		
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function get_tags_search_count($where=''){
		$this->db->select('*');
		$keyword = $this->input->post('keyword',TRUE);
		
		if(!empty($where)) $this->db->where($where);
		$this->db->where('ttags.ftagstatus',1);
		
		$this->db->where('(ftagname like '.$this->db->escape('%'.$keyword.'%').' OR ftagdesc like '.$this->db->escape('%'.$keyword.'%').')');
		
		$query = $this->db->get('ttags');
		return $query->num_rows();
	}

	public function get_tags($where,$offset=FALSE,$single=true,$limit=''){
		$limit = (!empty($limit)) ? $limit : $this->limit;
		if($offset) $offset = ($offset-1)*$limit;
		$this->db->select('*');
		
		//$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid AND t_productmeta.fmetakey = "date"');
		
		$this->db->where('t_product.fproducttype','event');
		//
		$this->db->where($where);
		
		//$this->db->having("distance <= '".$this->distance."'");
		
		$this->db->group_by('t_product.f_productid');
		
		if($offset!==FALSE) 
			$query = $this->db->get('t_product', $this->limit, $offset);
		else
			$query = $this->db->get('t_product');
		
		//echo $this->db->last_query();
		
		if ($query->num_rows() > 0){			
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}

	public function get_tags_where($where='',$offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		$keyword = $this->input->post('tag',TRUE);
		
		$this->db->select('*');
		
		if(!empty($where)) $this->db->where($where);
		$this->db->where('ttags.ftagstatus',1);
		
		$this->db->where('(ftagname like '.$this->db->escape('%'.$keyword.'%').')');
		
		if($offset!==FALSE) 
			$query = $this->db->get('ttags', $this->limit, $offset);
		else
			$query = $this->db->get('ttags');
		
		//echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
}
?>