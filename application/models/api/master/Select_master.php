<?php class Select_master extends CI_Model {

	 var $limit = 30;
	 var $sort = 'ASC';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function getCategory($offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		if($this->input->post('sort',TRUE))
			$this->sort = $this->input->post('sort',TRUE);
		$this->db->select('*');
		$this->db->order_by('f_kategoriid',$this->sort);
		if($offset!==FALSE) 
			$query = $this->db->get('t_kategori', $this->limit, $offset);
		else
			$query = $this->db->get('t_kategori');
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function getCategory_where($where,$field='*',$single=true){
		$this->db->select($field);	  
		$this->db->where($where);
		$query = $this->db->get('t_kategori');
		if ($query->num_rows() > 0){
			if($single) return $query->row_array();
			else return $query->result_array();
		}
	}

	public function getCategory_count()
	{
		$this->db->from('t_kategori');
		//$this->db->where('fcategorystatus',1);
		return $this->db->count_all_results();
	}
	
	public function getCity($offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		if($this->input->post('sort',TRUE))
			$this->sort = $this->input->post('sort',TRUE);
		$this->db->select('*');
		$this->db->join('t_provinsi','t_provinsi.f_provinsiid = t_kota.f_provinsiid');
		$this->db->order_by('f_kotaid',$this->sort);
		if($offset!==FALSE) 
			$query = $this->db->get('t_kota', $this->limit, $offset);
		else
			$query = $this->db->get('t_kota');
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function getCity_where($where,$field='*',$single=true){
		$this->db->select($field);	  
		$this->db->where($where);
		$query = $this->db->get('t_kategori');
		if ($query->num_rows() > 0){
			if($single) return $query->row_array();
			else return $query->result_array();
		}
	}

	public function getCity_count()
	{
		$this->db->from('t_kota');
		//$this->db->where('fcategorystatus',1);
		return $this->db->count_all_results();
	}

	public function getProvince($offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		if($this->input->post('sort',TRUE))
			$this->sort = $this->input->post('sort',TRUE);
		$this->db->select('*');
		$this->db->order_by('f_provinsiid',$this->sort);
		if($offset!==FALSE) 
			$query = $this->db->get('t_provinsi', $this->limit, $offset);
		else
			$query = $this->db->get('t_provinsi');
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function getProvice_where($where,$field='*',$single=true){
		$this->db->select($field);	  
		$this->db->where($where);
		$query = $this->db->get('t_provinsi');
		if ($query->num_rows() > 0){
			if($single) return $query->row_array();
			else return $query->result_array();
		}
	}

	public function getProvince_count()
	{
		$this->db->from('t_provinsi');
		//$this->db->where('fcategorystatus',1);
		return $this->db->count_all_results();
	}

	public function get_tag($offset = FALSE){
		if($offset) $offset = ($offset-1)*$this->limit;
		if($this->input->post('sort',TRUE))
			$this->sort = $this->input->post('sort',TRUE);
		$this->db->select('*');
		$this->db->order_by('ftagorder',$this->sort);
		if($offset!==FALSE) 
			$query = $this->db->get('ttags', $this->limit, $offset);
		else
			$query = $this->db->get('ttags');
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
	}
	
	public function get_tag_where($where,$field='*',$single=true){
		$this->db->select($field);	  
		$this->db->where($where);
		$query = $this->db->get('ttags');
		if ($query->num_rows() > 0){			
			if($single) return $query->row_array();
			else return $query->result_array();
		}
	}

	public function get_tag_count()
	{
		$this->db->from('ttags');
		$this->db->where('ttagstatus',1);
		return $this->db->count_all_results();
	}
}
?>