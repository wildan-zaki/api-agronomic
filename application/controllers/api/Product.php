<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

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
	
	public function get($type = 'product',$post=array())
	{
		$return = array();
		$return['data'] = null;
		//$this->load->library('api/user_api');
		$this->load->library('api/product_api');
		//$return = $this->user_api->getStatus(true);
		//if($return['status']==1)
		$return = $this->product_api->getProduct($type,$post);
		$json = json_encode($return);
		echo $json;		
	}

	public function category(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/product_api');
		$return = $this->product_api->getProduct();
		$json = json_encode($return);
		echo $json;
	}
	
	public function featured(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/product_api');
		$return = $this->product_api->productByFeatured();
		$json = json_encode($return);
		echo $json;
	}

	public function search(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/product_api');
		$return = $this->product_api->productBySearch();
		$json = json_encode($return);
		echo $json;	
		
	}
	
	public function detail(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/product_api');
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return['data']['product'] = $this->product_api->productDetail($this->input->post('product_id',TRUE));
		$json = json_encode($return);
		echo $json;
	}
}
