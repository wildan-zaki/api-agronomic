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
		
		//echo 'die here bro';die();
		$this->load->view('welcome_message');
	}

	public function category(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/master_api');
		$return = $this->master_api->masterCategory();
		$json = json_encode($return);
		echo $json;
	}
	
	public function country(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/master_api');
		$return = $this->master_api->masterCountry();
		$json = json_encode($return);
		echo $json;
	}
	
	public function province(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/master_api');
		$return = $this->master_api->masterProvince();
		$json = json_encode($return);
		echo $json;
	}
	
	public function city(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/master_api');
		$return = $this->master_api->masterCity();
		$json = json_encode($return);
		echo $json;
	}
	
	public function address(){	
		$return = array();
		$return['data'] = null;
		$this->load->library('api/master_api');
		$return = $this->master_api->masterAddress();
		$json = json_encode($return);
		echo $json;
	}
}
