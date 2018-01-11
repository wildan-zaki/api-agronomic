<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * 
 * Master API Class
 */
 
class Master_api {
	private $CI;    
	var $limit = 30; 
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('string');
	}
	
	public function masterCategory(){
		$this->CI->load->model('api/master/Select_master', '', TRUE);
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($categories = $this->CI->Select_master->getCategory($offset)){
			$return['status'] = 1;
			$totalCat = $this->CI->Select_master->getCategory_count();
			$totalPage = ceil($totalCat/$this->limit);
			if($offset<$totalPage)
				$return['data']['next'] = $offset+1;
			$return['data']['category'] = array();
			foreach($categories as $category){
				$return['data']['category'][] = array(
					'id' => $category['f_kategoriid'],
					'category_name' => $category['f_kategoriname'],
				);	
			}
		}
		return $return;	
	}
	
	public function masterCountry(){
		$this->CI->load->model('api/master/Select_master', '', TRUE);
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($countries = $this->CI->Select_master->get_country($offset)){
			$return['status'] = 1;
			$totalCountry = $this->CI->Select_master->get_country(FALSE,true);
			$totalPage = ceil($totalCountry/$this->limit);
			if($offset<$totalPage)
				$return['data']['next'] = $offset+1;
			$return['data']['area_code'] = array();
			foreach($countries as $country){
				$return['data']['area_code'][] = array(
					'id' => $country['fcountryid'],
					'country_name' => $country['fcountryname'],
				);	
			}
		}
		return $return;	
	}

	public function masterProvince(){
		$this->CI->load->model('api/master/Select_master', '', TRUE);
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($categories = $this->CI->Select_master->getProvince($offset)){
			$return['status'] = 1;
			$totalCat = $this->CI->Select_master->getProvince_count();
			$totalPage = ceil($totalCat/$this->limit);
			if($offset<$totalPage)
				$return['data']['next'] = $offset+1;
			$return['data']['category'] = array();
			foreach($categories as $category){
				$return['data']['category'][] = array(
					'id' => $category['f_provinsiid'],
					'provinsi_name' => $category['f_provinsiname'],
				);	
			}
		}
		return $return;	
	}
	
	public function masterCity(){
		$this->CI->load->model('api/master/Select_master', '', TRUE);
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($cities = $this->CI->Select_master->getCity($offset)){
			$return['status'] = 1;
			$totalCity = $this->CI->Select_master->getCity_count(FALSE,true);
			$totalPage = ceil($totalCity/$this->limit);
			if($offset<$totalPage)
				$return['data']['next'] = $offset+1;
			$return['data']['city'] = array();
			foreach($cities as $city){
				$return['data']['city'][] = array(
					'id' => $city['f_kotaid'],
					'provinsi' => $city['f_provinsiname'],
					'name' => $city['f_kotaname']
				);	
			}
		}
		return $return;	
	}
	
	public function masterAddress(){
		$this->CI->load->model('api/master/Select_master', '', TRUE);
		$return = array();
		$lat = $this->CI->input->post('lat',TRUE);
		$long = $this->CI->input->post('lon',TRUE);
		$api_key = 'AIzaSyDYM33-VzfSKzeFIa2pqaNsPRg8ovVfyjk'; // dev
		//$api_key = 'AIzaSyA1eetFCf_RHzqxPBpTYXr9dpr2LDa7oJQ'; // prod
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$long.'&key='.$api_key.'&language=id';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json = curl_exec($ch);
		$err = curl_error($ch);
		if ($err) {
			echo "error #:" . $err;
		}else{
			$data = json_decode($json,true);
			$formatted_address = $data['results'][0]['formatted_address'];
			$spl = explode(',',$formatted_address);
			$current_address = $spl[0].', '.$spl[1];
			$addresses = $data['results'][0]['address_components'];
			$kecamatan = null;
			$postal = null;
			$country = null;
			$province = null;
			foreach($addresses as $address){
				if(in_array('administrative_area_level_1',$address['types'])){
					$where = 'fprovincegoogle like "%'.$address['long_name'].'%" OR fprovincename like "%'.$address['long_name'].'%"';
					if($prov = $this->CI->Select_master->get_province_where($where)){
						$return['status'] = 1;
						$return['data']['province'] = array(
							'id' => $prov['fprovinceid'],
							'name' => $prov['fprovincename'],
						);
						break;
					}
				}
				//elseif(in_array('administrative_area_level_3',$address['type']))
//					$kecamatan = $address['long_name'];
//				elseif(in_array('postal_code',$address['type']))
//					$postal = $address['long_name'];
//				elseif(in_array('country',$address['type']))
//					$country = $address['long_name'];
			}
		}
		return $return;	
	}
	
	public function get_option($where,$single=true){
		$this->db->select('*');	  
		$this->db->where($where);
		$this->db->where_in('foptionstatus',1);
		$query = $this->db->get('toption');
		
		if ($query->num_rows() > 0){
			if($single){
				$row = $query->row_array();
				return $row['foptionvalue'];
			}else
				return $query->result_array();
		}
	}
}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */