<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	var $title = 'Login';
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

	public function __construct()
	{
		parent::__construct();
		$this->load->library('backend/auth_lib');
	}
	
	public function index()
	{
		$data = array();
		if ( $this->auth_lib->is_signed_in() )
			redirect('backend/dashboard');
		else{
			$data['login'] = true;
			if(!empty($this->session->flashdata('error'))) $data['error'] = $this->session->flashdata('error');			
			$data['title'] = $this->title;
			$this->load->view('backend/login',$data);
		}
	}
	
	public function process(){
		$this->auth_lib->sign_in();
	}
	
	public function logout(){
		$this->auth_lib->sign_out();
	}
}
