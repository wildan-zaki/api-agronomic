<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	//adam 
	public function vehicle(){
		$return = array();
		$return['vehicle'] = null;
		$this->load->library('api/master_api');
		//$this->load->library('api/user_api');
		//$return = $this->user_api->getStatus(true);
		//if($return['status']==1)
		$return = $this->master_api->masterVehicle();
		$json = json_encode($return);
		echo $json;
	}

	//adam
	public function model(){
		$return = array();
		$return['vehicle'] = null;
		$this->load->library('api/master_api');
		//$this->load->library('api/user_api');
		//$return = $this->user_api->getStatus(true);
		//if($return['status']==1)
			$return = $this->master_api->modelVehicle();
		$json = json_encode($return);
		echo $json;
	}

	//adam
	public function add(){
		$return = array();
		$return['vehicle'] = null;
		$this->load->library('api/master_api');
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return = $this->master_api->addVehicle();
		$json = json_encode($return);
		echo $json;
	}

	public function get(){
		$return = array();
		$return['vehicle'] = null;
		$this->load->library('api/master_api');
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return = $this->master_api->getVehicle();
		$json = json_encode($return);
		echo $json;
	}
	
	public function delete(){
		$return = array();
		$return['vehicle'] = null;
		$this->load->library('api/master_api');
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return = $this->master_api->deleteVehicle();
		$json = json_encode($return);
		echo $json;
	}

	public function categorys(){
		$return = array();
		$return['category'] = null;
		$this->load->library('api/master_api');
		//$this->load->library('api/user_api');
		//$return = $this->user_api->getStatus(true);
		//if($return['status'] == 1)
			$return = $this->master_api->categoryList();
		$json = json_encode($return);
		echo $json;
	}

	public function subService(){
		$return = array();
		$return['subService'] = null;
		$this->load->library('api/master_api');
		//$this->load->library('api/user_api');
		//$return = $this->user_api->getStatus(true);
		//if($return['status'] == 1)
			$return = $this->master_api->subServiceList();
		$json = json_encode($return);
		echo $json; 
	}

	public function slider(){
		$return = array();
		$return['slider'] = null;
		$this->load->library('api/master_api');
		$return = $this->master_api->sliderBanner();
		$json = json_encode($return);
		echo $json;
	}
	
}
 