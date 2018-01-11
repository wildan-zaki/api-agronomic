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
class User_api {
	private $CI;    
	var $limit = 30; 
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('string');
		$this->CI->load->model('api/user/Select_user', '', TRUE);
		$this->CI->load->model('api/user/Insert_user', '', TRUE);
		$this->CI->load->model('api/user/Update_user', '', TRUE);
		$this->CI->load->model('api/user/Delete_user', '', TRUE);
		$this->CI->load->model('api/order/Select_order', '', TRUE);
	}
	
	public function getStatus($basicAuthRequest=true){
		$return = array();
		$return['status'] = 1;
		if($basicAuthRequest){
			
			if ($this->CI->input->post('is_crew')) {
				$crew['fcrewid'] = $this->CI->input->post('user_id',TRUE);
				$crew['fcrewtoken'] = $this->CI->input->post('user_token',TRUE);
				if(!$this->CI->Select_order->get_where($crew)){
					$return['status'] = 3;
					$return['message'] = "Your session has been expired. Please re-login.";
					$return['error'] = "Invalid gmt_token";
					$return['code'] = 1;
				}
			}	
			else{

				$user['fuserid'] = $this->CI->input->post('user_id',TRUE);
				$user['fuserstoken'] = $this->CI->input->post('user_token',TRUE);
				if(!$this->CI->Select_user->get_where($user)){
					$return['status'] = 3;
					$return['message'] = "Your session has been expired. Please re-login.";
					$return['error'] = "Invalid gmt_token";
					$return['code'] = 1;
				}
				$return['cart_id'] = $this->CI->Update_user->updateTimestamp(0);
			}
			
		}
		return $return;
	}
	
	public function userSendMail($email){
		$emailConfig = array(
           'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'devgouserid@gmail.com',
            'smtp_pass' => 'user123',
            'mailtype'  => 'html', 
            'charset'   => 'utf-8',
            'starttls'  => true,
            //'newline'    => "\r\n"
        );
        $emailConfig['newline'] = "\r\n";
		
		$from = array('email' => 'devgouserid@gmail.com', 'name' => 'Go-Kleen');
		$to = $email['to'];
		$subject = $email['subject'];
		 
		$message = $this->CI->load->view('email/header','',true);
		$message .= $email['message'];
		$message .= $this->CI->load->view('email/footer','',true);
		
		// Load CodeIgniter Email library
		$this->CI->load->library('email', $emailConfig);
		$this->CI->email->set_newline("\r\n");

		//$this->CI->email->set_newline("\r\n");
		$this->CI->email->from($from['email'], $from['name']);
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		$this->CI->email->message($message);
		
		if(!empty($email['attachment'])){
			foreach	($email['attachment'] as $attachment){
				$this->CI->email->attach($attachment);	
			}
		}
		
		if (!$this->CI->email->send()) {
			// Raise error message
			echo '<pre>';print_r($this->CI->email->print_debugger());echo '</pre>';
		}else {
			return 1;
		}
	}
	
	public function userRegister(){
		$return = array();
		if(!empty($_POST)){
			$insert['fuseremail'] = $this->CI->input->post('email',TRUE);			
			$insert['fuserfirstname'] = $this->CI->input->post('firstname',TRUE);									
			$insert['fuserlastname'] = $this->CI->input->post('lastname',TRUE);	
			$insert['fuserphone'] = $this->CI->input->post('phone',TRUE);
			$password = $this->CI->input->post('password',TRUE);
			$insert['fuserpassword'] = md5($password);
			$insert['fuserbirthdate'] = $this->CI->input->post('birthdate',TRUE);
			$insert['fusergender'] = $this->CI->input->post('gender',TRUE);				
			// $insert['fuserapiver'] = $this->CI->input->post('api_ver',TRUE);
			// $insert['fuserappver'] = $this->CI->input->post('app_ver',TRUE);
			// $insert['fuserosver'] = $this->CI->input->post('os_ver',TRUE);
			// $insert['fuserdevice'] = $this->CI->input->post('device',TRUE);
			// $insert['fusertimestamp'] = $this->CI->input->post('timestamp',TRUE);			
			// $insert['fusercode'] = random_string('alnum', 20);		
			// $soc['socmed_type'] = $this->CI->input->post('socmed_type',TRUE);
			// $soc['socmed_id'] = $this->CI->input->post('socmed_id',TRUE);
			// $soc['socmed_access_token'] = $this->CI->input->post('socmed_access_token',TRUE);
			$soc['picture'] = $this->CI->input->post('picture',TRUE);
			if(!$this->CI->Select_user->checkEmail($insert)){	//kondisi jika email ada		
				if( $insert['fuserid'] = $this->CI->Insert_user->addNewUser($insert)){	
					$coder = 'KLEEN'.$insert['fuserid'];
					#generate referral
					// do{
					// 	//$reff['fuserreferral'] = $coder.strtoupper(random_string('alnum', (10-strlen($coder))));
					// }while($reffexist = $this->CI->Select_user->get_where($reff));
					
					$where['fuserid'] = $insert['fuserid'];
					//$update['fuserstoken'] = md5($insert['fuserid'].$insert['fuseremail'].time());
					//$update['fuserreferral'] = $reff['fuserreferral'];

					// if(!empty($soc['socmed_type']) && !empty($soc['socmed_id']) && !empty($soc['socmed_access_token'])){
		   //              $meta['fuserid'] = $insert['fuserid'];
		   //              if(!empty($soc['socmed_type'])){
		   //                $meta['fmetakey'] = 'socmed_type';
		   //                $meta['fmetavalue'] = $soc['socmed_type'];
		   //                $this->CI->Insert_user->addUserMeta($meta);
		   //              }
		   //              if(!empty($soc['socmed_id'])){
		   //                $meta['fmetakey'] = 'socmed_id';
		   //                $meta['fmetavalue'] = $soc['socmed_id'];
		   //                $this->CI->Insert_user->addUserMeta($meta);
		   //              }
		   //              if(!empty($soc['socmed_access_token'])){
		   //                $meta['fmetakey'] = 'socmed_access_token';
		   //                $meta['fmetavalue'] = $soc['socmed_access_token'];
		   //                $this->CI->Insert_user->addUserMeta($meta);
		   //              }
		   //           }
																
					// if($this->CI->Update_user->updateUser($update,$where)){
						if($user = $this->CI->Select_user->get_where($where)){
					// 		$data['url'] = site_url().'account/?action=confirm&key='.$insert['fusercode'].'&auth='. rawurlencode($insert['fuseremail']);
					// 		$email['to'] = $user['fuseremail'];
					// 		$email['subject'] = '[Go-Kleen] Verify Your Email';
					// 		$email['message'] = $this->CI->load->view('email/confirmation',$data,true);
					// 		if($this->userSendMail($email)){
					// 		// Show success notification or other things here				
					// 			$return['status'] = 1;
					// 			$return['data']['token'] = $user['fuserstoken'];
					 			$return['data']['user'] = $this->getUser(0,$user);
					// 		}							
					 	}
					// }
				}
			// }else{
			// 	if(!empty($soc['socmed_type']) && !empty($soc['socmed_id']) && !empty($soc['socmed_access_token'])){
			// 		if($login = $this->CI->Select_user->checkLoginSocmed($soc)){
			// 			$where['fuserid'] = $login['fuserid'];
			// 			if($user = $this->CI->Select_user->get_where($where)){
			// 				$return['status'] = 1;
			// 				$return['data']['token'] = $user['fuserstoken'];
			// 				$return['data']['user'] = $this->getUser(0,$user);
			// 			}
			// 		}
					
			// 		else{
			// 			$where['fuseremail'] = $insert['fuseremail'];
			// 			if(!$this->CI->Select_user->checkEmail($where)){
			// 				if($user = $this->CI->Select_user->get_where($where)){
			// 					$meta['fuserid'] = $user['fuserid'];
			// 					if(!empty($soc['socmed_type'])){
			// 						$meta['fmetakey'] = 'socmed_type';
			// 						$meta['fmetavalue'] = $soc['socmed_type'];
			// 						$this->CI->Insert_user->addUserMeta($meta);
			// 					}
			// 					if(!empty($soc['socmed_id'])){
			// 						$meta['fmetakey'] = 'socmed_id';
			// 						$meta['fmetavalue'] = $soc['socmed_id'];
			// 						$this->CI->Insert_user->addUserMeta($meta);
			// 					}
			// 					if(!empty($soc['socmed_access_token'])){
			// 						$meta['fmetakey'] = 'socmed_access_token';
			// 						$meta['fmetavalue'] = $soc['socmed_access_token'];
			// 						$this->CI->Insert_user->addUserMeta($meta);
			// 					}
								
			// 					unset($where);
			// 					$where['fuserid'] = $user['fuserid'];
			// 					$update['fuserstoken'] = md5($user['fuserid'].$user['fuseremail'].time());
			// 					if($this->CI->Update_user->updateUser($update,$where)){							
			// 						$return['status'] = 1;
			// 						$return['data']['token'] = $user['fuserstoken'];
			// 						$return['data']['user'] = $this->getUser(0,$user);
			// 					}
			// 				}
			// 			}
			// 			else{
			// 				if($user = $this->CI->Select_user->get_where($where)){
			// 					$meta['fuserid'] = $user['fuserid'];
			// 					if(!empty($soc['socmed_type'])){
			// 						$meta['fmetakey'] = 'socmed_type';
			// 						$meta['fmetavalue'] = $soc['socmed_type'];
			// 						$this->CI->Insert_user->addUserMeta($meta);
			// 					}
			// 					if(!empty($soc['socmed_id'])){
			// 						$meta['fmetakey'] = 'socmed_id';
			// 						$meta['fmetavalue'] = $soc['socmed_id'];
			// 						$this->CI->Insert_user->addUserMeta($meta);
			// 					}
			// 					if(!empty($soc['socmed_access_token'])){
			// 						$meta['fmetakey'] = 'socmed_access_token';
			// 						$meta['fmetavalue'] = $soc['socmed_access_token'];
			// 						$this->CI->Insert_user->addUserMeta($meta);
			// 					}
								
			// 					unset($where);
			// 					$where['fuserid'] = $user['fuserid'];
			// 					$update['fuserstoken'] = md5($user['fuserid'].$user['fuseremail'].time());
			// 					if($user = $this->CI->Select_user->get_where($where)){							
			// 						$return['status'] = 1;
			// 						$return['data']['token'] = $user['fuserstoken'];
			// 						$return['data']['user'] = $this->getUser(0,$user);
			// 					}
			// 				}
			// 			}
			// 		}
				}
				
				else{
					$return['status'] = 2;
					$return['message'] = 'email already exist.';
					$return['error'] = 'email already exist.';
					$return['code'] = '1001';	
				}
			// }	
		}else{
			$return['status'] = 2;
			$return['message'] = 'No registration data found.';
			$return['error'] = 'no post registration data.';
			$return['code'] = '1001';	
		}	
		return $return;
	}
	
	public function userLogin(){
		$return = array();
		$login['fuseremail'] = $this->CI->input->post('email');
		$login['fuserpassword'] = $this->CI->input->post('password');
		if($this->CI->Select_user->checkEmail($login)){	
			if($where['fuserid'] = $this->CI->Select_user->checkLogin($login)){
				$update['fuserstoken'] = md5($where['fuserid'].$login['fuseremail'].time());
				if($this->CI->Update_user->updateUser($update,$where)){
					if($user = $this->CI->Select_user->get_where($where)){
						$return['status'] = 1;
						$return['data']['token'] = $user['fuserstoken'];
						$return['data']['user'] = $this->getUser(0,$user);
					}
				}
			}else{
				$return['status'] = 2;
				$return['message'] = 'invalid username or password';
				$return['error'] = 'invalid username or password';
				$return['code'] = '1004';
			}
		}else{
			$return['status'] = 2;
			$return['message'] = 'email not found';
			$return['error'] = 'mail not found';
			$return['code'] = '1003';
		}
		return $return;
	}
	
	public function userLoginSocmed(){
		$return = array();
		$soc['socmed_type'] = $this->CI->input->post('type',TRUE);
		$soc['socmed_id'] = $this->CI->input->post('id',TRUE);
		$soc['socmed_access_token'] = $this->CI->input->post('access_token',TRUE);
		if($login = $this->CI->Select_user->checkLoginSocmed($soc)){
			$where['fuserid'] = $login['fuserid'];
			$update['fuserstoken'] = md5($where['fuserid'].$login['fuseremail'].time());
			if($this->CI->Update_user->updateUser($update,$where)){
				if($user = $this->CI->Select_user->get_where($where)){
					$return['status'] = 1;
					$return['data']['token'] = $user['fuserstoken'];
					$return['data']['user'] = $this->getUser(0,$user);
					$data['fmetakey'] = $where['fuserid'];					
				}
			}
		}else{
			$connect = false;
			$this->CI->load->model('api/user/Insert_user', '', TRUE);
			if($this->CI->input->post('user_id',TRUE)){
				$login['fuserid'] = $this->CI->input->post('user_id',TRUE);
				if($user = $this->CI->Select_user->get_where(array('fuserid'=>$login['fuserid']))){
					$login['fmetakey'] = 'socmed_type';
					$login['fmetavalue'] = $soc['socmed_type']; 
					if(!$this->CI->Select_user->get_count_meta($login)){
						$meta['fuserid'] = $where['fuserid'] = $login['fuserid'];
						if(!empty($soc['socmed_type'])){
							$meta['fmetakey'] = 'socmed_type';
							$meta['fmetavalue'] = $soc['socmed_type'];
							$this->CI->Insert_user->addUserMeta($meta);
						}
						if(!empty($soc['socmed_id'])){
							$meta['fmetakey'] = 'socmed_id';
							$meta['fmetavalue'] = $soc['socmed_id'];
							$this->CI->Insert_user->addUserMeta($meta);
						}
						if(!empty($soc['socmed_access_token'])){
							$meta['fmetakey'] = 'socmed_access_token';
							$meta['fmetavalue'] = $soc['socmed_access_token'];
							$this->CI->Insert_user->addUserMeta($meta);
						}
						if(!empty($soc['picture'])){
							$user['fuserprofpic'] = $soc['picture'];
							$this->CI->Update_user->updateUser($user,$where);
						}
						
						$update['fuserstoken'] = md5($user['fuserid'].$user['fuseremail'].time());
						if($this->CI->Update_user->updateUser($update,$where)){	
							if($user = $this->CI->Select_user->get_where(array('fuserid'=>$login['fuserid']))){
								$return['status'] = 1;
								$return['data']['token'] = $user['fuserstoken'];
								$return['data']['user'] = $this->getUser(0,$user);
								$connect = true;
							}
						}
					}
				}
			}
			if(!$connect){
				$return['status'] = 2;
				$return['message'] = 'social media id not found';
				$return['error'] = 'social media id not found';
				$return['code'] = '1005';
			}
		}
		return $return;
	}
	
	public function userForgotPass(){
		$return = array();
		if($where['fuseremail'] = $this->CI->input->post('email',TRUE)){
			$email = $where['fuseremail'];
			if($user = $this->CI->Select_user->get_where($where)){
						
				$this->CI->load->model('user_model/Update_user', '', TRUE);
				$this->CI->load->helper('string');	
				$user['fusercode'] = random_string('alnum', 20);
				$this->CI->Update_user->updateUser($user,$where);
								
				$data['fullname'] = ucwords($user['fuserfirstname']);
				$data['url'] = site_url().'account/?action=forgotpassword&key='.$user['fusercode'].'&auth='. rawurlencode($email);
				$emails['to'] = $email;
				$emails['subject'] = '[Go-Kleen] Reset Your Password';
				$emails['message'] = $this->CI->load->view('email/reset',$data,true);
				if($this->userSendMail($emails)){
					$return['status'] = 1;
					$return['data']['exist'] = true;
					//$return['data']['url'] = $data['url'];
				}
			}else{
				$return['status'] = 2;
				$return['message'] = 'email not found';
				$return['error'] = 'email id not found';
				$return['code'] = '1002';	
			}
		}
		return $return;
	}
	
	public function userChangePass(){
		$return = array();
		$current_pass = $this->CI->input->post('current_password',TRUE);		
		$new_pass = $this->CI->input->post('new_password',TRUE);
		$where['fuserpassword'] = $current_pass;
		$return['status'] = 1;
		if($user = $this->CI->Select_user->get_where($where)){
			$update['fuserpassword'] = $new_pass;
			$where['fuserid'] = $this->CI->input->post('user_id',TRUE);
			if($this->CI->Update_user->updateUser($update,$where))
				$return['data']['changed'] = true;
		}else{
			$return['data']['changed'] = false;
			$return['data']['changed'] = 'invalid current password.';
		}
		return $return;
	}
	
	public function userLogout(){
		$return['status'] = 1;
		$return['data']['status']['success'] = false;
		$update['fuserstoken'] = '';
		$where['fuserid'] = $this->CI->input->post('user_id',TRUE);
		if($this->CI->Update_user->updateUser($update,$where))
			$this->CI->Delete_user->deleteDeviceIntanceid($where);
			$return['data']['status']['success'] = true;
		return $return;
	}
	
	public function userProfile($action='read'){
		$return = array();
		switch($action){
			case 'edit':
				$return = $this->editUser();
				break;
			default:
				$login['fuserid'] = $this->CI->input->post('profile_id');
				if($where['fuserid'] = $this->CI->Select_user->checkId($login)){
					if($user = $this->CI->Select_user->get_where($where)){
						$return['status'] = 1;
						$return['data']['token'] = $user['fuserstoken'];
						$return['data']['user'] = $this->getUser(0,$user);
					}
				}
				break;
		}
		return $return;
	}
	
	public function getUser($id=0,$user=array()){
		$return = array();
		$where = "t_users.fuserid = " .$user['fuserid']." and fmetakey in ('socmed_type','socmed_id')";
	    
		if(!empty($user)){
			$return = array(
				'user id' => $user['fuserid'],
				'firstname' => $user['fuserfirstname'],
				'lastname' => null,
				'phone' => $user['fuserphone'],
				'birthdate' => null,
				'email' => $user['fuseremail'],
				'picture' => $user['fuserprofpic'],
				'gender' => $user['fusergender']
				//'is_verified' => true,
				//'referral_code' => $user['fuserreferral'],
				//'social_media' => null	
			);
			if(!empty($user['fuserlastname'])){
				$return['lastname'] = $user['fuserlastname'];
			}
			if(!empty($user['fuserbirthdate'])){
				$return['birthdate'] = $user['fuserbirthdate'];
			}	
			if(!empty($user['fuserprofpic'])){
				$return['picture'] = $user['fuserprofpic'];
			}
			if(!empty($user['fusergender'])){
				$return['gender'] = $user['fusergender'];
			}
			// if($socmed = $this->CI->Select_user->get_meta_where($where,false)){
			// 	$return['social_media'] = array();
			// 	$return['social_media'] = $this->getSocmedia(0,$socmed);	
			// }		
		}
		return $return;	
	}

	public function getSocmedia($id=0,$soc=array()){
	    $return = array(); 
	    $i=0;
	    if(!empty($soc)){
	    	foreach($soc as $socm){ 
	    	//echo $i.' '.$socm['fmetakey']/*.'</br>'*/;    	
	    	$return[$i][$socm['fmetakey']] = $socm['fmetavalue'];
	    	if($socm['fmetakey'] == 'socmed_id') $i++;
	    	} 
		}
	    
	    return $return; 
	}
	
	public function editUser($id=0){
		$return = array();
		$user_id = $this->CI->input->post('user_id',TRUE);
		$where['fuserid'] = (empty($id)) ? $user_id : $id;
		if($user = $this->CI->Select_user->get_where($where)){
			$this->CI->load->library('api/media_api');
			$fullname = '';
			$update['fuserfirstname'] = $this->CI->input->post('firstname',TRUE);									
			$update['fuserlastname'] = $this->CI->input->post('lastname',TRUE);	
			$update['fuserbirthdate'] =$this->CI->input->post('birthdate',TRUE);
			$update['fusergender'] = ( $this->CI->input->post('gender',TRUE) == 1 ) ? 'Male' : 'Female';
			$update['fuserphone'] = $this->CI->input->post('phone',TRUE);
			if(!empty($_FILES['picture'])){
				if($media = $this->CI->media_api->uploadPhoto()){
					$update['fuserprofpic'] = base_url($media['url']);
				}
			}
			if($this->CI->Update_user->updateUser($update,$where)){							
				if($user = $this->CI->Select_user->get_where(array('fuserid'=>$user_id))){
					$return['status'] = 1;
					$return['data']['token'] = $user['fuserstoken'];
					$return['data']['user'] = $this->getUser(0,$user);	
				}
			}
		}
		return $return;	
	}

	/*public function getBookingList(){
		$return['status'] = 1;
		
	}*/
	
	//API-CREW

	public function getStatusCrew($basicAuthRequest=true){
		$return = array();
		$return['status'] = 1;
		if($basicAuthRequest){
			$crew['fcrewid'] = $this->CI->input->post('user_id',true);
			$crew['fcrewtoken'] = $this->CI->input->post('user_token',true);
			if (!$this->CI->Select_user->get_where_crew_check($crew)) {
				$return['status'] = 3;
				$return['message'] = 'your session has been expired. Please re-login';
				$return['error'] =  'invalid user_token';
				$return['code'] = 1;
			}
		}
		return $return;
	}

	public function user_loginCrew(){
		$return = array();
		$login['fcrewname'] = $this->CI->input->post('username');
		$login['fcrewuserpassword'] = $this->CI->input->post('password');
		if($where = $this->CI->Select_user->checkLoginCrew($login)){
			//print_r($where['fcrewid']);
			$update['fcrewtoken'] = md5($login['fcrewuserpassword'].$login['fcrewname'].time());
			if($this->CI->Update_user->updateUserCrew($update,$where['fcrewid'])){
				if($userCrew = $this->CI->Select_user->get_where_crew($where['fcrewid'])){
					//print_r($userCrew);
					$return['status'] = 1;
					$return['data']['token'] = $userCrew['fcrewtoken'];
					$return['data']['crew'] = $this->getUserCrew($userCrew);
				}
			}
		}
		else{
			$return['status'] = 2;
			$return['message'] = 'username atau kata sandi salah mohon diperbaiki';
			$return['error'] = 'username atau kata sandi salah mohon diperbaiki';
			$return['code'] = 1003;
		}

		return $return;
	}

	public function getUserCrew($userCrew){
		$return = array();
		$fleetData = $this->CI->Select_user->get_fleetData($userCrew['ffleetid']);
		if (!empty($userCrew)) {
			$return = array(
				'crew_id' => $userCrew['fcrewid'],
				'username' => $userCrew['fcrewname'],
				'identity' => $userCrew['fcrewidentity'],
				'firstname' => $userCrew['fcrewfirstname'],
				'lastname' => $userCrew['fcrewlastname'],
				'phone' => $userCrew['fcrewphone'],
				'picture' => $userCrew['fcrewimage'],
				'is_verified' => true,
				'last_earning' => '',
				'reputation' => $userCrew['fcrewrating'],
				'position' => 'crew',
				'fleet' => array(
					'fleet_id' => $userCrew['ffleetid'],
					'vehicle' => array(
						'id' => $fleetData['fvehicleid'],
						'name' => $fleetData['ffleetvehiclename'],
						'slug' => $fleetData['ffleetvehicleslug'],
						'image' => $fleetData['ffleetvehicleimage'],
						'transmission' => $fleetData['ffleetvehicletransmission'],
						'year' => $fleetData['ffleetvehicleyear'],
						'color' => $fleetData['ffleetvehiclecolor']
					)
				)
			);
		}

		return $return;
	}
}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */