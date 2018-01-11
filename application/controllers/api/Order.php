<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

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
	public function createCart(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		/*var_dump($return);*/
		if($return['status'] == 1 && !empty($return['cart_id'])){
			$this->load->library('api/order_api');
			$cart_id = $return['cart_id'];
			$return = $this->order_api->createCartProduct($cart_id);
		}
		$json = json_encode($return);
		echo $json;

	}

	public function Voucher(){
		$return = array();
		$retunr['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status'] == 1){
			$this->load->library('api/order_api');
			$return = $this->order_api->addVoucher();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function createOrder(){
		$return = array();
		$return['data']  = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status'] == 1){
			$this->load->library('api/order_api');
			$return = $this->order_api->Order(); 
		}
		$json = json_encode($return);
		echo $json;
	}

	public function bookingList(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status'] == 1){
			$this->load->library('api/order_api');
			$return = $this->order_api->getBookingList();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function historyList(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getHistoryList();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function bookingDetail(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if ($return['status'] ==1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getBookingDetail();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function delete(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->deleteBooking();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function addDeviceUser(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getAddDevice();
		}
		$json = json_encode($return);
		echo $json;
	}


	//CREW-API
	public function bookingListCrew(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true); 
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getBookingListCrew(); 
		}
		$json = json_encode($return);
		echo $json;
	}

	public function bookingDetailCrew(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getBookingDetailCrew();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function bookingCangeStateCrew(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getBookingCangeStateCrew();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function updateUserVehicle(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getUpdateUserVehicle();
		}
		$json = json_encode($return);
		echo $json;
	}

	/*public function tes(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getTes();
		}
		$json = json_encode($return);
		echo $json;
	}*/

	public function addDeviceCrew(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getAddDeviceCrew();
		}
		$json = json_encode($return);
		echo $json;
	}

	public function carCondition(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatusCrew(true);
		if ($return['status'] == 1) {
			$this->load->library('api/order_api');
			$return = $this->order_api->getCarCondition();
		}
		$json = json_encode($return);
		echo $json;
	}

}
