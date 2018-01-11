<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * UnZip Class
 *
 * This class is based on a library I found at PHPClasses:
 * http://phpclasses.org/package/2495-PHP-Pack-and-unpack-files-packed-in-ZIP-archives.html
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Phil Sturgeon
 *
 * This class requires extension ZLib Enabled.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		Alexandre Tedeschi
 * @author		Phil Sturgeon
 * @author		Don Myers
 * @link		http://bitbucket.org/philsturgeon/codeigniter-unzip
 * @license     
 * @version     1.0.0
 */
class Master_api {
	private $CI;    
	var $limit = 10; 
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('string');
	}

	//adam
	public function masterVehicle(){
		$this->CI->load->model('api/interest/Select_interest', '', TRUE);
	    $return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($events = $this->CI->Select_interest->get($offset)){	
			//print_r($events);
			$totalEvent = $this->CI->Select_interest->getBrand_count();
			$totalPage = ceil($totalEvent/$this->limit);			
			if($offset<$totalPage)
				$return['next'] = $offset+1;
				$e = 0;
				foreach($events as $event){
					$return['status'] = 1;
					$return['data']['vehicle'][$e] = $this->vehicleDetail($event);				
					$e++;
				}
		}else				
			{	
			$return['status'] = 1;			
			$return['data']['model'] = null;
		}
		return $return;	
	}

	//adam
	public function modelVehicle(){
		$this->CI->load->model('api/interest/Select_interest', '', TRUE);
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($events = $this->CI->Select_interest->getModel($offset)){	
			$totalEvent = $this->CI->Select_interest->getModel_count();
			$totalPage = ceil($totalEvent/$this->limit);			
			if($offset<$totalPage)
				$return['next'] = $offset+1;
				$e = 0;
				foreach($events as $event){
					$return['status'] = 1;
					$return['data']['model'][$e] = $this->modelDetail($event['fvehicleid'],$event);				
					$e++;
				}
		}else{	
			$return['status'] = 1;			
			$return['data']['model'] = null;
		}
		return $return;		
	}

	public function addVehicle(){
		$this->CI->load->model('api/interest/Insert_interest', '', TRUE);
		$this->CI->load->model('api/interest/Select_interest', '', TRUE);
		$return = array();
		if(!empty($_POST)){
			$insert['fvehicleid'] = $this->CI->input->post('model',TRUE);
			$insert['transmission'] = $this->CI->input->post('transmission',TRUE);
			$insert['year'] = $this->CI->input->post('year',TRUE);
			$insert['color'] = $this->CI->input->post('color',TRUE);
			$insert['fvehicleplatno'] = $this->CI->input->post('plate_no',TRUE);
			$insert['fuserid'] = $this->CI->input->post('user_id',TRUE);
			$offset = (int)$this->CI->input->post('page',TRUE);
			$page = $this->CI->input->post('user_id',TRUE);
			if( $insert['fuservehicleid'] = $this->CI->Insert_interest->addNewVehicle($insert)){	
				$where = $insert['fuserid'];	
                if($events = $this->CI->Select_interest->getUserVehicle($where,$offset,$page)){	
              	     
        		$totalEvent = $this->CI->Select_interest->get_count();
        			//print_r($totalEvent);
					$totalPage = ceil($totalEvent/$this->limit);			
					if($offset<$totalPage)
						$return['next'] = $offset+1;
						$e = 0;
						foreach($events as $event){	
							$return['status'] = 1;	
							$return['data']['vehicle'] = $this->detailVehicle($event);	
							$e++;
						}
				}		
			}			
		}	
		return $return;
	}

	//adam
	public function getVehicle(){
		$this->CI->load->model('api/interest/Select_interest', '', TRUE);
	    $return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		$page = $this->CI->input->post('user_id',TRUE);
			if(!$offset) $offset = 1;
			if($events = $this->CI->Select_interest->getVehicle($offset,$page)){			
				$totalEvent = $this->CI->Select_interest->get_count();
				$totalPage = ceil($totalEvent/$this->limit);			
				if($offset<$totalPage)
					$return['next'] = $offset+1;
					$e = 0;
					foreach($events as $event){	
						$return['status'] = 1;	
						$return['data']['vehicle'][$e] = $this->detailVehicle($event);	
						$e++;
					}
			}
			else{	
			$return['status'] = 1;			
			$return['data']['model'] = null;
		}				
				
			return $return;	
	}

	public function deleteVehicle(){
		$this->CI->load->model('api/interest/Delete_interest', '',TRUE);
		$return['status'] = 1;
		$return['data']['success'] = true;		
		$where['fuservehicleid'] = $this->CI->input->post('my_vehicle_id',TRUE);
		$id = $this->CI->input->post('user_id',TRUE);		
		if($this->CI->Delete_interest->deleteVehicles($where,$id)){
			$return['success'] = true;
		}
		return $return;
	}

	public function categoryList(){
		$this->CI->load->model('api/interest/Select_interest', '',TRUE);
		$return = array();
		if($events = $this->CI->Select_interest->getCategory()){
			$e = 0;
			foreach ($events as $event) {
				$return['status'] = 1;
				$return['data']['categorys'][$e] = $this->categoryDetail($event);
				$e++;
			}
		}
		else {	
			$return['status'] = 1;			
			$return['data']['model'] = null;
		}
		return $return;
	}

	public function subServiceList(){
		$this->CI->load->model('api/interest/Select_interest', '',true);
		$return = array();
		$where = $this->CI->input->post('service_id',true);
		if($events = $this->CI->Select_interest->getSubService($where)){
			$e = 0;
			foreach ($events as $event) {
				$return['status'] = 1;
				$return['data']['subservices'][$e] = $this->detail($event['fsubserviceid'],$event);
				$e++;
			}
		}
		else {	
			$return['status'] = 1;			
			$return['data']['subservices'] = null;
		}
		//print_r($events);
		return $return;
	}

	public function sliderBanner(){
		$this->CI->load->model('api/interest/Select_interest', '',true);
		$return = array();
		if ($events = $this->CI->Select_interest->getBanner()) {
			$e = 0;
			foreach ($events as $event) {
				$return['status'] = 1;
				$return['data']['banner'][$e] = $this->data_banner($event);
				$e++;
			}
		}
		else{
			$return['status'] = 1;
			$return['data']['banner'] = null;
		}
		
		return $return;
	}

	public function data_banner($event=array()){
		$return = array();
		if (!empty($event)) {
			$return = array(
				'id' => $event['fsliderid'],
				'image_url' => base_url($event['fimagepath']),
				'caption' => $event['fimagedesc'],
				'url' => 'https://bitbucket.org/account/signin/?next=/danis-teknologi-maju/api-cms/'
			);
		}

		return $return;
	}

	public function vehicleDetail($event=array()){  
		$return = array();
		if(!empty($event)){
			$return = array(
				'id' => $event['fbrandid'],
				'name' => trim($event['fbrandname']),  
				'slug' => trim($event['fbrandslug']),
				'order' => $event['fbrandorder']
			);			
		}
		return $return;	 
	}
	
	//adam begin
	public function modelDetail($id=0,$event=array()){
		$return = array();
		$where_trans = "tvehicle.fvehicleid = " .$event['fvehicleid']." and fmetakey in ('transmission')";
		$where_year = "tvehicle.fvehicleid = " .$event['fvehicleid']." and fmetakey in ('year')";
		$where_color = "tvehicle.fvehicleid = " .$event['fvehicleid']." and fmetakey in ('color')";	    
		if(!empty($event)){
			$return = array(
				'id' => $event['fvehicleid'],
				'name' => trim($event['fvehiclename']),
				'slug' => trim($event['fvehicleslug']),
				'image' => null,
				'transmission' => null,
				'year' => null,
				'color' => null
			);	
			/*if(strpos($event['fvehicleimage'],'/assets/media/data/vehicles/')!==FALSE){
				$return['image'] = base_url($event['fvehicleimage']);
			}*/
			if(!empty($event['fvehicleimage'])){
				$return['image'] = base_url($event['fvehicleimage']);
			}	
			if($trans = $this->CI->Select_interest->get_meta_where($where_trans,false)){
				$return['transmission'] = array();
				$return['transmission'] = $this->getTrans(0,$trans);
			}	
			if($year = $this->CI->Select_interest->get_meta_where($where_year,false)){
				$return['year'] = array();
				$return['year'] = $this->getYear(0,$year);
			}
			if($color = $this->CI->Select_interest->get_meta_where($where_color,false)){
				$return['color'] = array();
				$return['color'] = $this->getColor(0,$color);
			}		
		}
		return $return;	
	}

	public function getTrans($id=0,$tra){
	    $return = array(); 
	    $i=0;
	    if(!empty($tra)){
		    foreach($tra as $transm){ 
		    	//echo $i.' '.$transm['fmetakey']/*.'</br>'*/;		    	
		    	$return[$i] = $transm['fmetavalue'];
		    	if($transm['fmetakey'] == 'transmission') 
		    	$i++;
		    } 
		}	    
	    return $return; 
	}

	public function getYear($id=0,$year){
	    $return = array(); 
	    $i=0;
	    if(!empty($year)){
		    foreach($year as $y){ 
		    	//echo $i.' '.$y['fmetakey']/*.'</br>'*/;		    	
		    	$return[$i] = $y['fmetavalue'];
		    	if($y['fmetakey'] == 'year') 
		    	$i++;
		    } 
		}	    
	    return $return; 
	}

	public function getColor($id=0,$color){
	    $return = array(); 
	    $i=0;
	    if(!empty($color)){
		    foreach($color as $colors){ 
		    	//echo $i.' '.$colors['fmetakey']/*.'</br>'*/;		    	
		    	$return[$i] = $colors['fmetavalue'];
		    	if($colors['fmetakey'] == 'color') 
		    	$i++;
		    } 
		}	    
	    return $return; 
	}
	//adam end

	public function detailVehicle($event=array()){
		$return = array();
		if(!empty($event)){
			$return = array(
				'id' => $event['fuservehicleid'],
				'model id' => $event['fvehicleid'],
				'name' => trim($event['fvehiclename']),
				'slug' => trim($event['fvehicleslug']),
				'image' => base_url($event['fvehicleimage']),
				'brand' => array(
					'id' => $event['fbrandid'],
					'name' => $event['fbrandname'],
					'slug' => $event['fbrandslug'],
					'logo' => base_url($event['fbrandlogo'])
				),
				'transmission' => $event['transmission'],
				'year' => $event['year'],
				'color' => null,
				'plate_no' => null,
				'created date' => $event['fvehiclecreateddate'],
				'modified date' => $event['fvehiclemodifieddate']
			);
			/*if(strpos($event['fvehicleimage'],'/assets/media/data/vehicles/')!==FALSE){
				$return['image'] = base_url($event['fvehicleimage']);
			}*/
			/*if(!empty($event['fvehicleimage'])){
				$return['image'] = base_url($event['fvehicleimage']);
			}*/
			/*if(!empty($event['fbrandlogo'])){
				$return['logo'] = base_url($event['fbrandlogo']);
			}*/
			if(!empty($event['fvehicleplatno'])){
				$return['plate_no'] = $event['fvehicleplatno'];
			}
			if(!empty($event['color'])){
				$return['color'] = $event['color'];
			}			
		}
		return $return;	
	}
	//ini tambah vehicle akhir1

	public function detail($id=0,$event=array()){
		$this->CI->load->model('api/interest/Select_interest', '',true);
		$return = array();
		$where['fsubserviceid'] = $id;
		//print_r($where);
		$addons = $this->CI->Select_interest->getAddon($where, false);
		$f = 0;
		foreach ($addons as $addon) {
			$data['add'][$f] = $this->detailAddon($addon['faddonid'],$addon);
			$f++;
		}
		//print_r($data);
		if(!empty($event)){
			$return = array(
				'id' => $event['fsubserviceid'],
				'name' => $event['fsubservicename'],
				'image_url' => null,
				'currency' => 'IDR',
				'price' => $event['fsubserviceprice'],
				'special_price' => $event['fsubservicespecialprice'],
				'description' => $event['fsubservicedesc'],
				'addons' => null
			);
			if(!empty($event['fsubserviceimage'])){
				$return['image_url'] = base_url($event['fsubserviceimage']);
			}
			if(!empty($data['add'])){
				$return['addons'] = $data['add'];
			}
		}
			
		return $return;
	}

	public function detailAddon($id=0,$event=array()){
		$this->CI->load->model('api/interest/Select_interest', '',true); 
		$return = array();
		if(!empty($event)){
			$data = array(
				'id' => $event['faddonid'],
				'name' => $event['faddonname'],
				'price' => $event['faddonprice'],
				'description' => $event['faddondesc'],
				'special_price' => $event['faddonspecialprice']
			);
		}
		else
			$return['addon'] = null;
		return $data;
	}

	public function serviceDetail($event=array()){
		$return = array();
		if(!empty($event)){
			$return = array(
				'id' => $event['fserviceid'],
				'name' => $event['fservicename'],
				'image_url' => null,
				'status' => $event['fservicestatus']
			);
			if(!empty($event['fserviceimage'])){
				$return['image_url'] = base_url($event['fserviceimage']);
			}
		}
		return $return;
	}

	
}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */