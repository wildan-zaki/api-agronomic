<?php class Select_product extends CI_Model {

	var $limit = 10;
	var $order_by = 'fproductcreateddate';
	var $order = 'ASC';
	
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
			case 'popular':
				$sort_by='(fproducttotalbuy+fproducttotalview)';
				break;
			case 'popular+latest':
				$sort_by='(fproducttotalbuy+fproducttotalview) '.$sort;
				$sort='fproductcreateddate '.$sort;
				break;
			case 'cheapest':
				$sort = 'ASC';
				$sort_by = '(f_productprice)';
				break;
			case 'expensive':
				$sort_by = '(f_productprice)';
				$sort = 'DESC';
				break;
			case 'latest':
				$sort_by=$this->order_by;
				break;
		}	
		$this->db->select('*');
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_petani', 't_petani.f_petaniid = t_product.f_petaniid');
		if(!empty($where)) $this->db->where($where);
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));

		$this->db->order_by($sort_by,$sort);
		$this->db->group_by('t_product.f_productid');
		
		if($offset!==FALSE) 
			$query = $this->db->get('t_product', $this->limit, $offset);
		else
			$query = $this->db->get('t_product');
		
		echo $this->db->last_query();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}

	public function get_count($where='')
	{		
		$this->db->select('*');
		
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_petani', 't_petani.f_petaniid = t_product.f_petaniid');
		if(!empty($where) && strpos($where,'fmetakey')!==FALSE) $this->db->join('t_productmeta', 't_productmeta.f_productid = t_product.f_productid');
		
		if(!empty($where)) $this->db->where($where);
		
		if(!empty($this->input->post('category_id',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category_id',TRUE));
		$this->db->group_by('t_product.f_productid');
		
		$query = $this->db->get('t_product');
		return $query->num_rows();
	}
	
	public function get_meta_where($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->join('t_product', 't_product.f_productid = t_productmeta.f_productid');
		$this->db->join('t_petani', 't_petani.f_petaniid = t_product.f_petaniid');
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
		$this->db->join('t_kategori', 't_product.f_kategoriid = t_kategori.f_kategoriid');
		$this->db->join('t_petani', 't_petani.f_petaniid = t_product.f_petaniid');
		$this->db->where($where);
		$query = $this->db->get('t_product');
		if(!empty($_REQUEST['debug']))echo $this->db->last_query();
		if ($query->num_rows() > 0){
			if($single)
				return $query->row_array();
			else
				return $query->result_array();
		}
	}
	
	public function get_search($offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		$lat = $this->input->post('lat',TRUE);
		$lon = $this->input->post('lon',TRUE);
		$sort_by = ($this->input->post('sort_by',TRUE)) ? $this->input->post('sort_by',TRUE) : $this->order_by;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : $this->order;
		$keyword = $this->input->post('keyword',TRUE);
		
		$this->db->select('*, 
(((ACOS(SIN('.$lat.' * PI() / 180) * SIN(`fproductlat` * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(`fproductlat` * PI() / 180) * COS(('.$lon.' - `fproductlon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)*1609.34) AS distance');
		
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_provinsi', 't_provinsi.f_provinceid = t_product.f_provinceid');
		$this->db->join('t_kota', 't_kota.f_kotaid = t_product.f_kotaid');
		$this->db->join('t_petani', 't_petani.f_petaniid = t_product.f_petaniid');
		$this->db->join('tmerchant', 'tmerchant.fmerchantid = t_product.fmerchantid');
		//$this->db->join('t_productmeta', 't_product.f_productid = t_productmeta.f_productid','left');
//		$this->db->join('tinterest', 't_productmeta.fmetavalue = tinterest.finterestid');
		//$this->db->where('fmetakey','interest');
		$this->db->where("(fproductenddate > UNIX_TIMESTAMP(NOW()) or fproductenddate = '' or fproductenddate is null)");
		$this->db->where('tmerchant.fmerchantstatus',1);
		if(!empty($this->input->post('province_id',TRUE)))	
			$this->db->where('t_product.f_provinceid',$this->input->post('province_id',TRUE));
		if(!empty($this->input->post('_kota_id',TRUE)))
			$this->db->where('t_product.f_kotaid',$this->input->post('_kota_id',TRUE));
		if(!empty($this->input->post('category',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category',TRUE));
		
		$this->db->where('fproductname like '.$this->db->escape('%'.$keyword.'%').' OR fproductdesc like '.$this->db->escape('%'.$keyword.'%').' OR fproductaddress like '.$this->db->escape('%'.$keyword.'%').' OR fcategoryname like '.$this->db->escape('%'.$keyword.'%').' OR fcategorydesc like '.$this->db->escape('%'.$keyword.'%')/*.' OR finterestname like '.$this->db->escape('%'.$keyword.'%').' OR finterestdesc like '.$this->db->escape('%'.$keyword.'%')*/);
		
		switch($sort_by){
			case 'rating':
				$this->db->order_by('fproductrating',$sort);
				break;
			default:
				$this->db->order_by($sort_by,$sort);
				break;	
		}
		
		//$this->db->having("distance <= '".$this->distance."'");
		
		$this->db->group_by('t_product.f_productid');
		
		if($offset!==FALSE) 
			$query = $this->db->get('t_product', $this->limit, $offset);
		else
			$query = $this->db->get('t_product');
		
	//	echo $this->db->last_query();
		
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	

	//adam
	public function get_current(){

	}

	public function get_search_count(){
		
		$lat = $this->input->post('lat',TRUE);
		$lon = $this->input->post('lon',TRUE);
		$sort_by = ($this->input->post('sort_by',TRUE)) ? $this->input->post('sort_by',TRUE) : $this->order_by;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : $this->order;
		$keyword = $this->input->post('keyword',TRUE);
		
		$this->db->select('*, 
(((ACOS(SIN('.$lat.' * PI() / 180) * SIN(`fproductlat` * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(`fproductlat` * PI() / 180) * COS(('.$lon.' - `fproductlon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)*1609.34) AS distance');
		
		$this->db->join('t_kategori', 't_kategori.f_kategoriid = t_product.f_kategoriid');
		$this->db->join('t_provinsi', 't_provinsi.f_provinceid = t_product.f_provinceid');
		$this->db->join('t_kota', 't_kota.f_kotaid = t_product.f_kotaid');
		$this->db->join('t_users', 't_users.fuserid = t_product.fuserid');
		$this->db->join('tmerchant', 'tmerchant.fmerchantid = t_product.fmerchantid');
		//$this->db->join('t_productmeta', 't_product.f_productid = t_productmeta.f_productid','left');
//		$this->db->join('tinterest', 't_productmeta.fmetavalue = tinterest.finterestid');
		//$this->db->where('fmetakey','interest');
		$this->db->where("(fproductenddate > UNIX_TIMESTAMP(NOW()) or fproductenddate = '' or fproductenddate is null)");
		$this->db->where('tmerchant.fmerchantstatus',1);
		if(!empty($this->input->post('province_id',TRUE)))	
			$this->db->where('t_product.f_provinceid',$this->input->post('province_id',TRUE));
		if(!empty($this->input->post('_kota_id',TRUE)))
			$this->db->where('t_product.f_kotaid',$this->input->post('_kota_id',TRUE));
		if(!empty($this->input->post('category',TRUE)))
			$this->db->where('t_product.f_kategoriid',$this->input->post('category',TRUE));
		
		$this->db->where('fproductname like '.$this->db->escape('%'.$keyword.'%').' OR fproductdesc like '.$this->db->escape('%'.$keyword.'%').' OR fproductaddress like '.$this->db->escape('%'.$keyword.'%').' OR fcategoryname like '.$this->db->escape('%'.$keyword.'%').' OR fcategorydesc like '.$this->db->escape('%'.$keyword.'%')/*.' OR finterestname like '.$this->db->escape('%'.$keyword.'%').' OR finterestdesc like '.$this->db->escape('%'.$keyword.'%')*/);
		
		switch($sort_by){
			case 'rating':
				$this->db->order_by('fproductrating',$sort);
				break;
			default:
				$this->db->order_by($sort_by,$sort);
				break;	
		}
		
		//$this->db->having("distance <= '".$this->distance."'");
		
		$this->db->group_by('t_product.f_productid');
		
		$query = $this->db->get('t_product');
		return $query->num_rows();
	}
	
	public function get_country($offset,$count=false){
		$this->limit = 30;
		if($offset) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');
		$this->db->where('fcountrynumber !=', 0);
		$this->db->order_by('fcountryname','ASC');
		if($offset!==FALSE) 
			$query = $this->db->get('tcountry', $this->limit, $offset);
		else
			$query = $this->db->get('tcountry');
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			else
				return $this->db->count_all_results();
		}	
	}
	
	public function get_country_where($where,$single=true){
		$this->db->select('*');
		$this->db->where($where);
		
		$query = $this->db->get('tcountry');
		if ($query->num_rows() > 0){
			if(!$single)
				return $query->result_array();
			else
				return $query->row_array();
		}	
	}
	
	public function get_province($offset,$count=false){
		$this->limit = 30;
		if($offset) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');
		$this->db->where('fcountrycode',$this->input->post('country_code',TRUE));
		$this->db->order_by('fprovincename','ASC');
		if($offset!==FALSE) 
			$query = $this->db->get('t_provinsi', $this->limit, $offset);
		else
			$query = $this->db->get('t_provinsi');
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			else
				return $this->db->count_all_results();
		}	
	}
	
	public function get_province_where($where,$single=true){
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('fprovincename','ASC');
			$query = $this->db->get('t_provinsi');
		if ($query->num_rows() > 0){
			if(!$single)
				return $query->result_array();
			else
				return $query->row_array();
		}	
	}
	
	public function get__kota($offset,$count=false){
		$this->limit = 30;
		if($offset) $offset = ($offset-1)*$this->limit;
		$this->db->select('*');
		$this->db->where('f_provinceid',$this->input->post('province_id',TRUE));
		$this->db->order_by('f_kotaname','ASC');
		if($offset!==FALSE) 
			$query = $this->db->get('t_kota', $this->limit, $offset);
		else
			$query = $this->db->get('t_kota');
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			else
				return $this->db->count_all_results();
		}	
	}
	
	public function get_tickets(){
		$this->db->select('*');	
		$this->db->where('f_productid',$this->input->post('product_id',TRUE));
		$this->db->where('fticketstock >= 1');
		if(!empty($this->input->post('schedule',TRUE)))
			$this->db->where('fscheduleid',$this->input->post('schedule',TRUE));
		$query = $this->db->get('tticket');
		
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	//adam
	public function get_ticket_where($where,$single=true){
		$this->db->select('*');	
		$this->db->where($where);
		$query = $this->db->get('tticket');
		if ($query->num_rows() > 0){
			if($single)
				return $query->row_array();	
			else
				return $query->result_array();	
		}
	}
	
	public function get_claimed($where,$count=false){		
		$this->db->select('*');	
		$this->db->where($where);
		$query = $this->db->get('tclaimedticket');
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			else
				return $query->num_rows();
		}
	}
	
	public function get_activity($where,$count=false){		
		$this->db->select('*');	
		$this->db->where($where);
		$query = $this->db->get('tactivity');
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			else
				return $query->num_rows();
		}
	}
	
	public function get_review($offset,$count=false){
		$this->limit = 30;
		if($offset) $offset = ($offset-1)*$this->limit;
		$sort = ($this->input->post('sort',TRUE)) ? $this->input->post('sort',TRUE) : 'DESC';
		switch($this->input->post('sort_by',TRUE)){
			case 'higher_rating':
				$sort_by = 'freviewrating';	
				$sort = 'DESC';
				break;
			case 'lowest_rating':
				$sort_by = 'freviewrating';	
				$sort = 'ASC';
				break;
			case 'rating':
				$sort_by = 'freviewrating';	
				break;
			default:
				$sort_by = 'freviewdate';	
				break;
		}
		$this->db->select('freviewid,freviewtext,freviewrating,freviewdate,treview.fuserid,fuserfullname,fuserbirthdate,fuseremail,fuserprofpic,fproductrating,fuserparentid,fuserstatus,fproductrating');
		$this->db->join('t_users', 't_users.fuserid = treview.fuserid');
		$this->db->join('t_product', 't_product.f_productid = treview.f_productid');
		$this->db->where('treview.f_productid',$this->input->post('product_id',TRUE));
		$this->db->where('freviewstatus',1);
		$this->db->order_by($sort_by,$sort);
		if($offset!==FALSE) 
			$query = $this->db->get('treview', $this->limit, $offset);
		else
			$query = $this->db->get('treview');
			
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			else
				return $query->num_rows();
		}	
	}
	
	public function get_review_where($where,$count=false){		
		$this->db->select('*');	
		$this->db->where($where);
		$this->db->where('freviewstatus',1);
		$this->db->join('t_users', 't_users.fuserid = treview.fuserid');
		$this->db->join('t_product', 't_product.f_productid = treview.f_productid');
		$query = $this->db->get('treview');
		
		if ($query->num_rows() > 0){
			if(!$count)
				return $query->result_array();
			elseif($count=='single')
				return $query->row_array();
			else
				return $query->num_rows();
		}
	}
}
?>