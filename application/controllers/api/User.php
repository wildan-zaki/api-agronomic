<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

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
	
	public function register(){		
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->userRegister();
		$json = json_encode($return);
		echo $json;
	}
	
	public function login(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->userLogin();
		$json = json_encode($return);
		echo $json;	
	}
	
	public function logout(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return = $this->user_api->userLogout();
		$json = json_encode($return);
		echo $json;	
	}
	
	public function socmedlogin(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->userLoginSocmed();
		$json = json_encode($return);
		echo $json;	
	}
	
	public function forgotpass(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->userForgotPass();
		$json = json_encode($return);
		echo $json;	
	}
	
	public function changepass(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return = $this->user_api->userChangePass();
		$json = json_encode($return);
		echo $json;	
		
	}
	
	public function profile($action='read'){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status']==1)
			$return = $this->user_api->userProfile($action);
		$json = json_encode($return);
		echo $json;			
	}
	
	public function email(){
		$this->load->library('third_party/m_pdf');
		$this->load->library('api/user_api');
		
		$data['fullname'] = 'Errol Widhavian';
		$data['password'] = 'x12flL1hJ';
		$message = $this->load->view('email/awaiting',$data,true);
		$attachments[0] = 'assets/media/events/etickets/test.pdf';
		
		$pdf .= $this->load->view('email/header-pdf','',true);
		$pdf .= $this->load->view('email/eticket-3','',true);//,$eticket,true);
		$pdf .= $this->load->view('email/footer-pdf','',true);
		
		echo $pdf;
		
		$this->m_pdf->load();
		//$this->m_pdf->debug = true;
	   //generate the PDF from the given html
		$this->m_pdf->pdf->WriteHTML($pdf);
 
		//download it.
		$this->m_pdf->pdf->Output($attachments[0], "F");
		$email = array(
			//'to' => array('errol.widhavian@gmail.com','annisa.kuntadi@googaga.co.id','maria.tambunan@googaga.co.id','novrizal@dcc.co.id','ramdhany.nugroho@gmail.com'),
			'to' => 'errol.widhavian@gmail.com',
			'subject' => 'awaiting to Go-Kleen',
			'message' => 'test pdf',
			'attachment' => $attachments
		);
		$this->user_api->userSendMail($email);
	}

	/*public function bookingList(){
		$return = $array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->getStatus(true);
		if($return['status'] == 1){
			$return = $this->order_api->getBookingList();
		}
		$json = json_encode($return);
		echo $json;
	}*/

	//API-CREW
	public function loginCrew(){
		$return = array();
		$return['data'] = null;
		$this->load->library('api/user_api');
		$return = $this->user_api->user_loginCrew();
		$json = json_encode($return);
		echo $json;
	}

}
