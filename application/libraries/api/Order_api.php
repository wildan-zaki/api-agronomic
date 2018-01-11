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
class Order_api {
	private $CI;    
	var $limit = 10; 
	var $condition_message;
	var $condition_error;
	var $condition_code;
	
	function __construct() 
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('string');
		$this->CI->load->model('api/user/Select_user', '', TRUE);
		$this->CI->load->model('api/order/Select_order', '', TRUE);
		$this->CI->load->model('api/product/Select_product', '', TRUE);
		$this->CI->load->model('api/order/Insert_order', '', TRUE);
		$this->CI->load->model('api/order/Update_order', '', TRUE);
		$this->CI->load->model('api/order/Delete_order', '', TRUE);
	}
	
	public function createCartProduct($cart_id){  
		$return['status'] = 1;
		$return['data']['cart_token'] = false;
		$requested = json_decode($this->CI->input->post('requests',TRUE));
		$serialized_array = serialize($requested);
		$location = json_decode($this->CI->input->post('location',TRUE));		
		$userid = $this->CI->input->post('user_id',TRUE);
		$where['fuserid'] = $this->CI->input->post('user_id',true);
		$date = $this->CI->input->post('date',TRUE);
		$request_returns; 
		if(!empty($requested) && $userid){
			$option['foptionname'] = 'order_cart_expiry';
			if($cart_expiry = $this->CI->Select_order->order_option($option)){
				//delete old cart
				$del['forderid'] = $cart_id;
				$del['fcartsubmitted'] = 0;
				$this->CI->Delete_order->deleteClaim($del);

				$claim['fuserid'] = $userid;
				$claim['fcartsubmitted'] = 0;
				$claimed_products = $this->CI->Select_order->claimed_product_get_where($claim);

				if(!empty($claimed_products)) { 
					foreach ($claimed_products as $claimed_product) {
						$x = 0;
						$sub_total = 0;
						$sub_product_returns;
						foreach ($requested as $request) {
							$where_product['f_productid'] =  $request->product_id;
							$product = $this->CI->Select_order->get_where_product($where_product);
							$product = $this->return_product($product);
							$sub_total = ($product['price']*$request->quantity) + $sub_total;
							$data['forderid'] = $cart_id;
							$data['fclaimedproduct'] = $serialized_array;				
							$productData = $this->CI->Update_order->updateCartProduct($data, isset($claimed_product['fuserid']));
							$t_product['product'] = $product;
							$request_returns[$x] = $t_product;
							$x++;
						}				
					}

				} else { 

					//buat cart baru
					$x = 0; 
					$sub_total = 0;
					$sub_product_returns;
					foreach ($requested as $request) {
						$where_product['f_productid'] = $request->product_id;
						$product = $this->CI->Select_order->get_where_product($where_product);
						$product = $this->return_product($product);
						$sub_total = ($product['price']*$request->quantity) + $sub_total;
						$data['forderid'] = $cart_id;
						$data['fclaimedproduct'] = $serialized_array;
						
						$t_product['product'] = $product;
						$request_returns[$x] = $t_product;
						$x++;
					}
					$claimedproduct['forderid'] = $cart_id;
					$claimedproduct['fuserid'] = $userid;					
					$claimedproduct['flastactivity'] = time();
					$claimedproduct['fexpireddate'] = ($claimedproduct['flastactivity'] + $cart_expiry);
					$claimedproduct['fclaimedproduct'] = $data['fclaimedproduct'];					
					$claimedproduct['fcartsubmitted'] = 0;
					$claimeddata = $this->CI->Insert_order->insertClaimedProduct($claimedproduct);
				}
				
					$return['data']['cart_token'] = $cart_id;
					$return['data']['expiry'] = $cart_expiry;
					$return['data']['cart'] = array(
						'id' => null,
						'currency' => 'IDR',
						'total' => ($sub_total)
					);
					$return['data']['request'] = $request_returns;
			}											
		}

		return $return;
	}

	public function Order(){
		$return['status'] = 1;
		$return = array();
		$cart_token = $this->CI->input->post('cart_token',true);
		$paymentmethodid = $this->CI->input->post('payment_method_id',true);
		$xwhere['forderid'] = $cart_token;
		$notes = $this->CI->input->post('note',true);
		$locations = json_decode($this->CI->input->post('location',true));
		$where['fuserid'] = $insert['fuserid'] = $this->CI->input->post('user_id',TRUE);
		$unixNow = time();
		if($where && !empty($cart_token)){		
			$requestedcart = $this->CI->Select_order->get_request_cart($xwhere);
			$cekEpiredCart = $requestedcart['fexpireddate'];
			if($unixNow < $cekEpiredCart){
				$unserialized_array = unserialize($requestedcart['fclaimedproduct']);

				//var_dump($unserialized_array);
				$option['foptionname'] = 'order_cart_expiry';

				$claim['fuserid'] = $where['fuserid'];
				$claim['fcartsubmitted'] = 0;
				$claimed_products = $this->CI->Select_order->get_request($claim);
				$sub_total = 0;
				if(!empty($claimed_products)) {
					$x = 0;						
					$sub_product_returns;
					if (is_array($unserialized_array)){
						foreach ($unserialized_array as $request){ 
							$where_product['f_productid'] =  $request->product_id;
							$dataproduct = $this->CI->Select_order->get_where_product($where_product);
							$product = $this->return_product($dataproduct);
							$sub_total = ($product['price']*$request->quantity) + $sub_total;

							$data['forderid'] = $cart_token;
							$data['fclaimedproduct'] = $requestedcart;
		
							$t_product['product'] = $product;
							$request_returns[$x] = $t_product;
							$x++;
						}
					}				
				}
				if(!empty($claimed_products)) {
					$x = 0;						
					if (is_array($unserialized_array)){
						foreach ($unserialized_array as $request_serialize){ 
							$where_product_serialize['f_productid'] =  $request_serialize->product_id;
							$dataproduct_serialize = $this->CI->Select_order->get_where_product($where_product);
							$product_serialize = $this->return_product($dataproduct_serialize);

							$data['forderid'] = $cart_token;
							$data['fclaimedproduct'] = $requestedcart;
							
							$vehicle_serialize['product'] = $product_serialize;
							$insert_serialize[$x] = $vehicle_serialize;
							$x++;
						}
					}				
				}
										
				$total_price = $sub_total;					

				$dataPayment = $this->CI->Select_order->get_payment($paymentmethodid);
				//var_dump($dataPayment);
				$serialize_dataOrder = serialize($insert_serialize);
				$serialize_dataCart = serialize($request_returns);

				$insert['fordertotalprice'] = $total_price;
				$insert['fuserid'] = $where['fuserid'];
				$insert['fordergateway'] = $dataPayment['f_paymentname'];
				$insert['forderpaymentmethodid'] = $dataPayment['f_paymentid'];
				$insert['forderdate'] = time();
				$insert['fordernote'] = null;
				$option['foptionname'] = 'order_payment_expiry';
				$insert['forderlat'] = $locations->lat;
				$insert['forderlon'] = $locations->lon;
				$insert['forderaddressline'] = $locations->address_line_1;
				//$insert['forderaddressline2'] = $locations->address_line_2;
				$insert['forderdataproduct'] = $serialize_dataOrder;
				if(!empty($notes)){					
					$insert['fordernote'] = $notes;					
				}
				if($order_payment_expiry = $this->CI->Select_order->order_option($option)){
					$insert['forderexpired'] = $insert['forderdate'] + $order_payment_expiry;
				}
				if($set['forderid'] = $this->CI->Insert_order->createOrder($insert)){
					$set['fcartsubmitted'] = 1;
					$this->CI->Update_order->updateClaimOrder($set,$claim);

					$cart['fuserid'] = $where['fuserid'];
					$cart['forderid'] = $set['forderid'];
					//$cart['ffleetid'] = 0;
					$cart['fcartproduct'] = $serialize_dataCart;
					$cart['fproductstatus'] = 1;
					$cart['fcartcurrency'] = 'IDR';
					$cart['fcartsubtotal'] = $sub_total;
					$cart['fcarttotal'] = $total_price;
					$cart_id = $this->CI->Insert_order->createCart($cart);

					$return['status'] = 1;
					$return['data']['status'] = true;
					$return['data']['message'] = 'We have record your booking. Our staff will call you for confirmation';

				}
				if(!empty($vwhere['fcouponcode'])){					
					$cart['fcartvoucher'] = $vwhere['fcouponcode'];					
				}
				
			}
			else{
				$del['forderid'] = $requestedcart['forderid'];
				$del['fcartsubmitted'] = 0;
				$this->CI->Delete_order->deleteClaim($del);

				$return['status'] = 2;
				$return['message'] = 'Cart Expired.';
				$return['error'] = 'Cart Expired';
				$return['code'] = '1014';
			}
		}

		return $return;
	}
	
	public function getBookingList(){ 		 
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		$where['fuserid'] = $this->CI->input->post('user_id',true);
		if($orders = $this->CI->Select_order->get_all_order($where,$offset)){				
			$totalEvent = $this->CI->Select_order->get_count();
			$totalPage = ceil($totalEvent/$this->limit);
			if($offset<$totalPage)				
			$return['data']['next'] = $offset+1;						
			$e = 0;
			foreach($orders as $order){	
				$return['status'] = 1;
				$return['data']['orders'][$e] = $this->return_orders($order);															
				$e++;
			}										
		} 
		else{	
			$return['status'] = 1;	
			$return['data']['list'] = null;
		}
		
		return $return;
	}

	public function getHistoryList(){		
		$return = array();
		$offset = (int)$this->CI->input->post('page',true);
		if(!$offset) $offset = 1;
		$where['fuserid'] = $this->CI->input->post('user_id',true);
		//$status_order_process = 7;
		if ($orders = $this->CI->Select_order->get_all_order_history($where,$offset)) {
			//print_r($orders);
			$totalEvent = $this->CI->Select_order->get_count();
			$totalPage = ceil($totalEvent/$this->limit);
			if($offset<$totalPage)			
			$return['data']['next'] = $offset+1;
			$e = 0;
			foreach ($orders as $order) {
				$return['status'] = 1; 
				$return['data']['orders'][$e] = $this->return_history($order);
				$e++;
			}
		}
		else{	
			$return['status'] = 1;			
			$return['data']['history'] = null;
		}

		return $return;
	}

	public function getBookingDetail(){
		$return = array();
		$orderid = $this->CI->input->post('order_id',true);

		//$where['forderid'] = $orderid;
		if ($orders = $this->CI->Select_order->get_order($orderid)) {			
			$return['status'] = 1;
			$return['data']['order'] = $this->return_detail($orders);
		} 
		else{	
			$return['status'] = 1;			
			$return['data']['order'] = null;
		} 

		return $return;
	}

	public function deleteBooking(){
		$return = array();
		$orderid = $this->CI->input->post('order_id',true);
		$reasonType = $this->CI->input->post('reason_type',true);
		//$notes = $this->CI->input->post('note',true);
		$forderprocessed = 8;
		$fleetid = 0;
		$data = $this->CI->Select_order->DeleteBooking($orderid);
		if ($data['forderprocessed'] == 1 || $data['forderprocessed'] == 2) {
			$this->CI->Update_order->BookingStatus($forderprocessed,$orderid);
			$this->CI->Insert_order->BookingCancel($orderid,$reasonType);
			$this->CI->Delete_order->CancelBooking($orderid);
			$this->CI->Update_order->Update_fleet($fleetid,$orderid);
			$return['status'] = 1;
			$return['data']['success'] = true;
		}
		else{
			$return['status'] = 2;
			$return['message'] = 'You can not cancel booking';
			$return['error'] = 'You can not cancel booking';
			$return['code'] = '1020';
		}
			
		
		return $return;
	}

	public function return_history($order){
		$return = array();
		$unserialize_get_dataordercart = unserialize($order['forderdataproduct']);
		//print_r($unserialize_get_dataordercart);
		if(is_array($unserialize_get_dataordercart)){ 
			if($order['forderprocessed']==1){
			$process_type = 'received';
			}
			elseif($order['forderprocessed']==2){
				$process_type = 'matchingcrew';
			}
			elseif($order['forderprocessed']==3){
				$process_type = 'ontheway';
			}
			elseif($order['forderprocessed']==4){
				$process_type = 'arriving';
			}
			elseif($order['forderprocessed']==5){
				$process_type = 'checking';
			} 
			elseif($order['forderprocessed']==6){
				$process_type = 'cleaning';
			}
			elseif($order['forderprocessed']==7){
				$process_type = 'complete';
			}
			elseif($order['forderprocessed']==8){
				$process_type = 'cancel';
			}  
			if(!empty($order)){
			$return = array(
					'id' => $order['forderid'],
					'status' => $process_type,
					'requests' => $unserialize_get_dataordercart
					);
			}		
		}
		return $return;
	}

	public function return_orders($order){
		$return = array();
		$unserialize_get_dataordercart = unserialize($order['forderdataproduct']);
		//$date = strtotime($order['forderdate']);
		if(is_array($unserialize_get_dataordercart)){ 
			if($order['forderprocessed']==1){
			$process_type = 'received';
			}
			elseif($order['forderprocessed']==2){
				$process_type = 'matchingcrew';
			}
			elseif($order['forderprocessed']==3){
				$process_type = 'ontheway';
			}
			elseif($order['forderprocessed']==4){
				$process_type = 'arriving';
			}
			elseif($order['forderprocessed']==5){
				$process_type = 'checking';
			} 
			elseif($order['forderprocessed']==6){
				$process_type = 'cleaning';
			}
			elseif($order['forderprocessed']==7){
				$process_type = 'complete';
			}
			elseif($order['forderprocessed']==8){
				$process_type = 'cancel';
			} 
			if(!empty($order)){
			$return = array(
					'id' => $order['forderid'],
					'status' => $process_type,
					'date' => $order['forderdate'],
					'requests' => $unserialize_get_dataordercart
					);
			}		
		}
		return $return;
	}

	public function return_detail($order){
		$return = array();
		if ($order['ffleetid'] == 0) {
			$detailData = $this->CI->Select_order->get_detaildata_null($order['forderid']);
		//print_r($detailData);
		$unserialize_detailData = unserialize($detailData['fcartproduct']);

		$unserialize_voucher = unserialize($detailData['fcartvoucher']);
		
		$get_dataCart = $this->CI->Select_order->get_cartData($order['forderid']);
		$startHour = $this->CI->Select_order->get_cartData($get_dataCart['forderid']);
		$whereStartHour['fhourid'] = $startHour['fstarthour']; 
		$return_start_hour = $this->CI->Select_order->get_start_hour($whereStartHour);		
		
		$endHour = $this->CI->Select_order->get_cartData($get_dataCart['forderid']);
		$whereEndHour['fhourid'] = $endHour['fendhour'];
		$return_end_hour = $this->CI->Select_order->get_end_hour($whereEndHour);

		$dataPayment = $this->CI->Select_order->get_payment($detailData['forderpaymentmethodid']);
		$additional_fee = 0;
		$option['foptionname'] = 'additional_fee';
		$data_additional = unserialize($detailData['fcartadditional']);
		if($additional = $this->CI->Select_order->order_option($option)){
			$additionals = unserialize($additional);
			if(!empty($additionals)){
				foreach($additionals as $add){
					$additional_fee += $add['value'];
				}
			}
		}
		if($order['forderprocessed']==1){
			$process_type = 'received';
			}
			elseif($order['forderprocessed']==2){
				$process_type = 'matchingcrew';
			}
			elseif($order['forderprocessed']==3){
				$process_type = 'ontheway';
			}
			elseif($order['forderprocessed']==4){
				$process_type = 'arriving';
			}
			elseif($order['forderprocessed']==5){
				$process_type = 'checking';
			} 
			elseif($order['forderprocessed']==6){
				$process_type = 'cleaning';
			}
			elseif($order['forderprocessed']==7){
				$process_type = 'complete';
			}
			elseif($order['forderprocessed']==8){
				$process_type = 'cancel';
			} 
		

		$detailData_fleet = $this->CI->Select_order->get_detaildata_fleet($detailData['fcartid']);
		if($detailData_fleet['ffleetid']==0){
			$fleet_data = null;
		}
		else{
			$fleet_data = $this->return_fleet($detailData);
		}

		date_default_timezone_set('UTC');
		$time_stampe_fbookingdate = strtotime($order['fbookingdate']);

		if (!empty($order['fordervoucherdata'])) {
			$coupon = $this->CI->Select_order->get_coupons($detailData['fordervoucherdata'],true); 
			//print_r($coupon);
			if($coupon['fcoupontype']==1){
				$voucher_type = 'value';
				$voucher_value = $coupon['fcouponvalue'];
			}
			elseif($coupon['fcoupontype']==2){
				$voucher_type = 'percent';
				$voucher_value = ($coupon['fcouponvalue']/100)*$detailData['fcartsubtotal'];
			}
			
			if (!empty($order)) {
				$return = array(
						'id' => $order['forderid'],
						'status' => $process_type,
						'date' => $time_stampe_fbookingdate,
						'requests' => $unserialize_detailData,
						'location' => array(
								'lat' => $detailData['forderlat'],
								'lon' => $detailData['forderlon'],
								'address_line_1' => $detailData['forderaddressline1'],
								'address_line_2' => $detailData['forderaddressline2']
							),
						'schedules' => array(
								'start_hour' => array(
										'id' => $return_start_hour['fhourid'],
										'hour' => $return_start_hour['fhour']
									),
								'end_hour' => array(
										'id' => $return_end_hour['fhourid'],
										'hour' => $return_end_hour['fhour']
									)  
							),
						'payment_method' => array(
								'id' => $dataPayment['fpaymentid'],
								'type' => $dataPayment['fpaymentname']
							),
						'cart' => array(
								'id' => $detailData['fcartid'],
								'currency' => $detailData['fcartcurrency'],
								'sub_total' => $detailData['fcartsubtotal'],
								'vouchers' => array(
										'id' => $coupon['fcouponid'],
										'code' => $coupon['fcouponcode'],
										'type' => $voucher_type,
										'value' => $coupon['fcouponvalue']
									),
								'total' => $detailData['fcarttotal'],
								'additionals' => null
							),
						'fleet' => $fleet_data,
						'note' => $detailData['fordernote']
					);
					if(!empty($additionals) && !empty($additional_fee)){
						foreach($additionals as $add){
							$return['cart']['additionals'][] = array(
								'title' => $add['name'],
								'value' => $add['value']
							);
						}
					}
			}
		}
		else{
				$return = array(
						'id' => $order['forderid'],
						'status' => $process_type,
						'date' => $time_stampe_fbookingdate,
						'requests' => $unserialize_detailData,
						'location' => array(
								'lat' => $detailData['forderlat'],
								'lon' => $detailData['forderlon'],
								'address_line_1' => $detailData['forderaddressline1'],
								'address_line_2' => $detailData['forderaddressline2']
							),
						'schedules' => array(
								'start_hour' => array(
										'id' => $return_start_hour['fhourid'],
										'hour' => $return_start_hour['fhour']
									),
								'end_hour' => array(
										'id' => $return_end_hour['fhourid'],
										'hour' => $return_end_hour['fhour']
									) 
							),
						'payment_method' => array(
								'id' => $dataPayment['fpaymentid'],
								'type' => $dataPayment['fpaymentname']
							),
						'cart' => array(
								'id' => $detailData['fcartid'],
								'currency' => $detailData['fcartcurrency'],
								'sub_total' => $detailData['fcartsubtotal'],
								'vouchers' => null,
								'total' => $detailData['fcarttotal'],
								'additionals' => null
							),
						'fleet' => $fleet_data,
						'note' => $detailData['fordernote']
					);
					if(!empty($additionals) && !empty($additional_fee)){
						foreach($additionals as $add){
							$return['cart']['additionals'][] = array(
								'title' => $add['name'],
								'value' => $add['value']
							);
						}
					}
			}
		}
		else{
			$detailData = $this->CI->Select_order->get_detaildata($order['forderid']);
		//print_r($detailData);
		$unserialize_detailData = unserialize($detailData['fcartproduct']);

		$unserialize_voucher = unserialize($detailData['fcartvoucher']);
		
		$get_dataCart = $this->CI->Select_order->get_cartData($order['forderid']);
		$startHour = $this->CI->Select_order->get_cartData($get_dataCart['forderid']);
		$whereStartHour['fhourid'] = $startHour['fstarthour']; 
		$return_start_hour = $this->CI->Select_order->get_start_hour($whereStartHour);		
		
		$endHour = $this->CI->Select_order->get_cartData($get_dataCart['forderid']);
		$whereEndHour['fhourid'] = $endHour['fendhour'];
		$return_end_hour = $this->CI->Select_order->get_end_hour($whereEndHour);

		$dataPayment = $this->CI->Select_order->get_payment($detailData['forderpaymentmethodid']);
		$additional_fee = 0;
		$option['foptionname'] = 'additional_fee';
		$data_additional = unserialize($detailData['fcartadditional']);
		if($additional = $this->CI->Select_order->order_option($option)){
			$additionals = unserialize($additional);
			if(!empty($additionals)){
				foreach($additionals as $add){
					$additional_fee += $add['value'];
				}
			}
		}
		if($order['forderprocessed']==1){
			$process_type = 'received';
			}
			elseif($order['forderprocessed']==2){
				$process_type = 'matchingcrew';
			}
			elseif($order['forderprocessed']==3){
				$process_type = 'ontheway';
			}
			elseif($order['forderprocessed']==4){
				$process_type = 'arriving';
			}
			elseif($order['forderprocessed']==5){
				$process_type = 'checking';
			} 
			elseif($order['forderprocessed']==6){
				$process_type = 'cleaning';
			}
			elseif($order['forderprocessed']==7){
				$process_type = 'complete';
			}
			elseif($order['forderprocessed']==8){
				$process_type = 'cancel';
			} 
		

		$detailData_fleet = $this->CI->Select_order->get_detaildata_fleet($detailData['fcartid']);
		if($detailData_fleet['ffleetid']==0){
			$fleet_data = null;
		}
		else{
			$fleet_data = $this->return_fleet($detailData);
		}

		date_default_timezone_set('UTC');
		$time_stampe_fbookingdate = strtotime($order['fbookingdate']);

		if (!empty($order['fordervoucherdata'])) {
			$coupon = $this->CI->Select_order->get_coupons($detailData['fordervoucherdata'],true); 
			//print_r($coupon);
			if($coupon['fcoupontype']==1){
				$voucher_type = 'value';
				$voucher_value = $coupon['fcouponvalue'];
			}
			elseif($coupon['fcoupontype']==2){
				$voucher_type = 'percent';
				$voucher_value = ($coupon['fcouponvalue']/100)*$detailData['fcartsubtotal'];
			}
			
			if (!empty($order)) {
				$return = array(
						'id' => $order['forderid'],
						'status' => $process_type,
						'date' => $time_stampe_fbookingdate,
						'requests' => $unserialize_detailData,
						'location' => array(
								'lat' => $detailData['forderlat'],
								'lon' => $detailData['forderlon'],
								'address_line_1' => $detailData['forderaddressline1'],
								'address_line_2' => $detailData['forderaddressline2']
							),
						'schedules' => array(
								'start_hour' => array(
										'id' => $return_start_hour['fhourid'],
										'hour' => $return_start_hour['fhour']
									),
								'end_hour' => array(
										'id' => $return_end_hour['fhourid'],
										'hour' => $return_end_hour['fhour']
									) 
							),
						'payment_method' => array(
								'id' => $dataPayment['fpaymentid'],
								'type' => $dataPayment['fpaymentname']
							),
						'cart' => array(
								'id' => $detailData['fcartid'],
								'currency' => $detailData['fcartcurrency'],
								'sub_total' => $detailData['fcartsubtotal'],
								'vouchers' => array(
										'id' => $coupon['fcouponid'],
										'code' => $coupon['fcouponcode'],
										'type' => $voucher_type,
										'value' => $coupon['fcouponvalue']
									),
								'total' => $detailData['fcarttotal'],
								'additionals' => null
							),
						'fleet' => $fleet_data,
						'note' => $detailData['fordernote']
					);
					if(!empty($additionals) && !empty($additional_fee)){
						foreach($additionals as $add){
							$return['cart']['additionals'][] = array(
								'title' => $add['name'],
								'value' => $add['value']
							);
						}
					}
			}
		}
		else{
				$return = array(
						'id' => $order['forderid'],
						'status' => $process_type,
						'date' => $time_stampe_fbookingdate,
						'requests' => $unserialize_detailData,
						'location' => array(
								'lat' => $detailData['forderlat'],
								'lon' => $detailData['forderlon'],
								'address_line_1' => $detailData['forderaddressline1'],
								'address_line_2' => $detailData['forderaddressline2']
							),
						'schedules' => array(
								'start_hour' => array(
										'id' => $return_start_hour['fhourid'],
										'hour' => $return_start_hour['fhour']
									),
								'end_hour' => array(
										'id' => $return_end_hour['fhourid'],
										'hour' => $return_end_hour['fhour']
									) 
							),
						'payment_method' => array(
								'id' => $dataPayment['fpaymentid'],
								'type' => $dataPayment['fpaymentname']
							),
						'cart' => array(
								'id' => $detailData['fcartid'],
								'currency' => $detailData['fcartcurrency'],
								'sub_total' => $detailData['fcartsubtotal'],
								'vouchers' => null,
								'total' => $detailData['fcarttotal'],
								'additionals' => null
							),
						'fleet' => $fleet_data,
						'note' => $detailData['fordernote']
					);
					if(!empty($additionals) && !empty($additional_fee)){
						foreach($additionals as $add){
							$return['cart']['additionals'][] = array(
								'title' => $add['name'],
								'value' => $add['value']
							);
						}
					}
			}
		}
		

	

		return $return;
	}

	public function return_fleet($dataFleet){
		$return = array();
		$whereFleetid = $dataFleet['ffleetid'];
		$getCrew = $this->CI->Select_order->get_dataCrew($whereFleetid);
		$f = 0;
		foreach ($getCrew as $crew) {
			$data['crew'][$f] = $this->return_crew($crew);
			$f++;
		}
		if (!empty($dataFleet)) {
			$return = array(
				'id' => $dataFleet['ffleetid'],
				'name' => $dataFleet['ffleetname'],
				'vehicle' => array(
						'id' => $dataFleet['fvehicleid'],
						'name' => $dataFleet['fvehiclename'],
						'slug' => $dataFleet['fvehicleslug'],
						'image' => null,
						'transmission' => $dataFleet['ffleetvehicletransmission'],
						'year' => $dataFleet['ffleetvehicleyear'],
						'color' => $dataFleet['ffleetvehiclecolor']
					),
				'crews' => $data['crew']
			);
			if(!empty($dataFleet['fvehicleimage'])){
				$return['image'] = base_url($dataFleet['fvehicleimage']);
			}
		}
		return $return;
	}

	public function return_crew($dataCrew){
		$return = array();
		if(!empty($dataCrew)){
			$data = array(
				'crew_id' => $dataCrew['fcrewid'],
				'username' => $dataCrew['fcrewidentity'],
				'firstname' => $dataCrew['fcrewfirstname'],
				'lastname' => $dataCrew['fcrewlastname'],
				'phone' => $dataCrew['fcrewphone'],
				'picture' => null,
				'is_verified' => 'true',
				'reputation' => '100.0',
				'position' => 'crew'
			);
			if(!empty($dataCrew['fcrewimage'])){
				$return['picture'] = base_url($dataCrew['fcrewimage']);
			}
		}
		else
			$return['status'] = 1;
			$return['data']['addon'] = null;
		return $data;
	}

	public function return_addon($addon){
		$return = array();
		if(!empty($addon)){
			$return = array(
				'id' => $addon['faddonid'],
				'name' => $addon['faddonname'],
				'price' => $addon['faddonprice'],
				'description' => $addon['faddondesc'],
				'special_price' => $addon['faddonspecialprice']
				);
		}
		return $return;
	}

	public function return_subproduct($subproduct){
		$return = array();
		if(!empty($subproduct)){
			$return = array(
				'id' => $subproduct['fsubproductid'],
				'name' => $subproduct['fsubproductname'],
				'currency' => 'IDR',
				'image_url' => null,
				'price' => $subproduct['fsubproductprice'],
				'special_price' => $subproduct['fsubproductspecialprice'],
				'description' => $subproduct['fsubproductdesc']
				);
			if(!empty($subproduct['fsubproductimage'])){
				$return['image_url'] = base_url($subproduct['fsubproductimage']);
			}
		}
		return $return;
	}

	public function return_product($product){
		$return = array();
		if(!empty($product)){
			$return = array(
				'id' => $product['f_productid'],
				'name' => $product['f_productname'],
				'price' => $product['f_productprice'],
				'image_url' => null
				);
				if(!empty($product['fproductimage'])){
					$return['image_url'] = base_url($product['fproductimage']);
				}
		}
		return $return;
	}

	public function return_vehicle($vehicle=array()){
		$return = array();
		if(!empty($vehicle)){
			$return = array(
				'id' => $vehicle['fuservehicleid'],
				'name' => $vehicle['fvehiclename'],
				'slug' => $vehicle['fvehicleslug'],
				'image' => null,
				'transmission' => $vehicle['transmission'],
				'year' => $vehicle['year'],
				'color' => $vehicle['color'],
				'plat_no' => $vehicle['fvehicleplatno']
				);
			if(!empty($vehicle['fvehicleimage'])){
				$return['image'] = base_url($vehicle['fvehicleimage']);
			}
		}
		return $return;
	}

	public function return_data_vehicle($vehicle=array()){
		$return = array();
		if(!empty($vehicle)){
			$return = array(
				'id' => $vehicle['fuservehicleid'],
				'vehicle_id' => $vehicle['fvehicleid'],
				'name' => $vehicle['fvehiclename'],
				'slug' => $vehicle['fvehicleslug'],
				'brand' => array(
						'id' => $vehicle['fbrandid'], 
						'name' => $vehicle['fbrandname'],
						'slug' => $vehicle['fbrandslug'],
						'logo' => null
					),
				'image' => null,
				'transmission' => $vehicle['transmission'],
				'year' => $vehicle['year'],
				'color' => $vehicle['color'],
				'plat_no' => $vehicle['fvehicleplatno']
				);
			if(!empty($vehicle['fvehicleimage'])){
				$return['image'] = base_url($vehicle['fvehicleimage']);
			}
			if(!empty($vehicle['fbrandlogo'])){
				$return['logo'] = base_url($vehicle['fbrandlogo']);
			}
		}
		return $return; 
	}

	public function getDeviceId(){

	}

	public function getAddDevice(){
		$return = array();

		if (!empty($_POST)) {
			if (!empty($this->CI->input->post('is_crew'))) {
				$user['fuserid'] = $this->CI->input->post('user_id',true);
				$data['fdeviceinstanceid'] = $this->CI->input->post('instance_id',true);
				$data['fdeviceimei'] = $this->CI->input->post('device_id',true);
				$data['fdevicename'] = $this->CI->input->post('device_name',true);
				$data['fis_crew'] = 1;
				$data['fuserid'] = $user['fuserid'];
				$cek = $this->CI->Select_order->cek_device_id($data['fdeviceimei']);
				//print_r($cek); 
				$crewid = $this->CI->Select_order->get_tcrew($user['fuserid']);
				if ($data['fdeviceimei'] == $cek['fdeviceimei']) {
					if ($user['fuserid'] == $crewid['fcrewid']) {
						//$update['fdeviceinstanceid'] = $data['fdeviceinstanceid'];
						$update['fis_crew'] = $data['fis_crew'];
						$update['fuserid'] = $user['fuserid'];
						$this->CI->Update_order->update_device_id($update,$cek['fdeviceid']); 
					}
					
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}
				else{
					$this->CI->Insert_order->add_device_id($data);
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}	  	
			}
			else{
				$data['fuserid'] = $this->CI->input->post('user_id',true);
				$data['fdeviceinstanceid'] = $this->CI->input->post('instance_id',true); 
				$data['fdeviceimei'] = $this->CI->input->post('device_id',true);
				$data['fdevicename'] = $this->CI->input->post('device_name',true);
				$cek = $this->CI->Select_order->cek_device_id($data['fdeviceimei']);
				if ($data['fdeviceimei'] == $cek['fdeviceimei']) {
					//$crewid = $this->CI->Select_order->get_tcrew($data['fuserid']);
					//if ($data['fuserid'] == $crewid['fcrewid']) {
						//$update['fdeviceinstanceid'] = $data['fdeviceinstanceid'];
						$update['fuserid'] = $data['fuserid'];
						$update['fis_crew'] = 0;
						$this->CI->Update_order->update_device_id($update,$cek['fdeviceid']); 
					//}
					
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}
				else{
					$this->CI->Insert_order->add_device_id($data);
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}	
				
			}
		
		}
	
		return $return;

	}


	//CREW-API
	public function getBookingListCrew(){
		$return = array();
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		$where = $this->CI->input->post('user_id',true);
		$crew = $this->CI->Select_order->get_fleet_id_crew($where);
		//print_r($crew);
		if ($orders = $this->CI->Select_order->get_all_order_crew($crew['ffleetid'],$offset)) {	
			//print_r($orders);		
			$totalEvent = $this->CI->Select_order->get_count_crew();
			$totalPage = ceil($totalEvent/$this->limit);
			if($offset < $totalPage)
			$return['data']['next'] = $offset+1;
			$e = 0;
			foreach ($orders as $order) {
				$return['status'] = 1;
				$return['data']['orders'][$e] = $this->return_orders_crew($order); 
				$e++;
			}	
		}
		else{
			$return['status'] = 1;
			$return['data']['list'] = null;
		}

		return $return;
	}
 
	public function getBookingDetailCrew(){ 
		$return = array();
		$orderid = $this->CI->input->post('order_id',true); 
		$where = $this->CI->input->post('user_id',true); 
		$crew = $this->CI->Select_order->get_fleet_id_crew($where);

		if($order = $this->CI->Select_order->get_all_order_detail_crew($crew['ffleetid'],$orderid)){
			$return['status'] = 1;
			$return['data']['orders'] = $this->return_orders_detail_crew($order);
		}
		else{
			$return['status'] = 1;
			$return['data']['details'] = null;
		}

		return $return;
	}

	public function getBookingCangeStateCrew(){
		$return = array();

		$orderid = $this->CI->input->post('order_id',true); 
		$where = $this->CI->input->post('user_id',true);
		$state = $this->CI->input->post('state_id');

		$crew = $this->CI->Select_order->get_fleet_id_crew($where);
		$complete = 7;
		$matchingcrew = 2;

		if (!$this->CI->Select_order->get_all_order_matchingCrew($crew['ffleetid'],$matchingcrew,$orderid,$complete)) {
			//if ($this->CI->Select_order->get_today($orderid)) {
				$this->CI->Update_order->updateState($state,$orderid);
				if($orders = $this->CI->Select_order->get_all_order_detail_crew($crew['ffleetid'],$orderid)){	
					if ($state==3 || $state==4 || $state==5 || $state==6 || $state==7) {
						$message = $this->getMessages($state);
						$data = $this->getNotifData($orderid, 1);
						$title = 'GO KLEEN';
						if (!empty($registrationIds = $this->CI->Select_order->get_registrationIds($orderid))) {
							$x = 0;
							$instanceIds;
							foreach ($registrationIds as $ids) {
								foreach ($ids as $id){
									if (isset($id)) {
										$instanceIds[$x] = $id;
										$x++;
									}
								}								
							}

							$logtime = time();
							$datetime = date('Y-m-d H:i:s', $logtime);
							$this->sendPushNotif($title,$message,$instanceIds, $data);
							$this->CI->Insert_order->log_device_id($message,$datetime);
						}
						$return['status'] = 1;
						$return['data']['orders'] = $this->return_orders_detail_crew($orders);
					}
				}
		}

		else{
			$return['status'] = 2;
			$return['message'] = 'Silahkan selesaikan permintaan sebelumnya';
			$return['error'] = 'Silahkan selesaikan permintaan sebelumnya';
			$return['code'] = '1018';
		}

		return $return;

	}

	public function getNotifData($order, $type) {
		$data['order_id'] = $order;
		$data['type'] = $type;
		return $data;
	}

	public function getMessages($state){

		switch ($state) {
			case 3:
				return "Operator are coming to your destination";
				break;
			case 4:
				return "Operator are arriving";
				break;
			case 5:
				return "Operator are checking your order";
				break;
			case 6:
				return "Operator are kleening your order";
				break;
			
			default:
				return "Operator has finished your order";
				break;
		}

		return json_encode($message);
	}

	public function sendPushNotif($title, $message, $registrationIds, $data){
		#API access key from Google API's Console
	    define( 'API_ACCESS_KEY', 'AAAAL6MrmCQ:APA91bEp-wAlQXTOQOrZhkcSwXqZhaprmTsqhzRsGPHgpuXPezzLmrx06cSM7sJ4UnzuWUjIrVxN7Ui9pCpu8dKD4Ro9ugoR2ykRAGJZivdORxYn9YghNbSPHu7L95fyevBf--r28dO1' );

	    $data['body'] = $message;
	    $data['title'] = $title;
		$fields = array
				(
					'registration_ids'	=> $registrationIds ,
					'data'=> $data
				);
		
		$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY,
					'Content-Type: application/json'
				);

		#Send Reponse To FireBase Server	
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		#Echo Result Of FireBase Server
				
		return $result;
	}

	public function getUpdateUserVehicle(){
		$return = array();
		$where = $this->CI->input->post('user_id',true);
		$orderid = $this->CI->input->post('order_id',true); 
		$crew = $this->CI->Select_order->get_fleet_id_crew($where);
		//print_r($crew);
		$orders = $this->CI->Select_order->get_all_order_detail_crew($crew['ffleetid'],$orderid); 
		//print_r($orders);
		$model_id = $this->CI->input->post('model_id');		
		$getModel = $this->CI->Select_order->get_model($model_id);
		//print_r($getModel);
		$name = $getModel['fvehiclename'];
		$slug = $getModel['fvehicleslug'];
		$image = base_url($getModel['fvehicleimage']);
		$transmission = $this->CI->input->post('transmission');
		$year = $this->CI->input->post('year');
		$color = $this->CI->input->post('color');
		$plate_no = $this->CI->input->post('plate_no');
		$model = $this->CI->input->post('vehicle_id');
		//$vehicleid = $this->CI->input->post('vehicle_id');
		$brandid = $getModel['fbrandid'];
		$brandname = $getModel['fbrandname'];
		$brandslug = $getModel['fbrandslug'];
		$brandlogo = base_url($getModel['fbrandlogo']);

		//databaru
		$databaru['fvehicleid'] = $model;
		//$databaru['fvehiclename'] = $name;
		$databaru['transmission'] = $transmission;
		$databaru['year'] = $year;
		$databaru['color'] = $color;
		$databaru['fvehicleplatno'] = $plate_no;

		$dataVehicle = unserialize($orders['fcartproduct']);
	
			$datas;
			$e = 0;

			if(!empty($dataVehicle)){
				foreach ($dataVehicle AS $data) { 
			        //if (isset($data) && is_array($data)) {
					if($data['id'] == $model) {
			    			$data['vehicle_id'] = $model;
			    			$data['name'] = $name;
			        		$data['slug'] = $slug;
			        		$data['image'] = $image;
			        		$data['transmission'] = $transmission;
			        		$data['year'] = $year;
			        		$data['color'] = $color;
			        		$data['plat_no'] = $plate_no;
			        		$data['brand']['id'] = $brandid;
			        		$data['brand']['name'] = $brandname;
			        		$data['brand']['slug'] = $brandslug;
			        		$data['brand']['logo'] = $brandlogo;  

			        		  		
			    		}  
			    		/*else{
							$return['status'] = 2;
							$return['message'] = 'Silahkan masukkan model vehicle yang sesuai';
							$return['error'] = 'Silahkan masukkan model vehicle yang sesuai';
							$return['code'] = '1019';
						} */ 	
			        //}
			        $datas[$e] = $data; 
			        $e++;	
			        // echo '<pre>';       	
			        // print_r($data);
			        // echo '</pre>';
			        $serializedData = serialize($datas);
				    //print_r($serializedData);
				    $where_id = $dataVehicle[0]['id'];
				    $this->CI->Update_order->update_userVehicle($serializedData,$orderid);
				    $this->CI->Update_order->update_userVehicle_order($serializedData,$orderid);     
				    $this->CI->Update_order->update_data_user_vehicle($databaru,$where_id);    
			    }
			}

			$return['status'] = 1;
			$return['data']['success'] = true; 
			
				
		return $return;

	}

	

	public function return_orders_detail_crew($order){   
		$return = array();
		$unserialize_get_dataordercart = unserialize($order['fcartproduct']);
		if($order['forderprocessed']==1){
		$process_type = 'received';
		}
		elseif($order['forderprocessed']==2){
			$process_type = 'matchingcrew';
		}
		elseif($order['forderprocessed']==3){
			$process_type = 'ontheway';
		}
		elseif($order['forderprocessed']==4){
			$process_type = 'arriving';
		}
		elseif($order['forderprocessed']==5){
			$process_type = 'checking';
		} 
		elseif($order['forderprocessed']==6){
			$process_type = 'cleaning';
		}
		elseif($order['forderprocessed']==7){
			$process_type = 'complete';
		}
		elseif($order['forderprocessed']==8){
			$process_type = 'cancel';
		} 
		$user = $this->CI->Select_order->get_dataUser($order['fuserid']);
		$return_start_hour = $this->CI->Select_order->get_start_hour_crew($order['fstarthour']);
		$return_end_hour = $this->CI->Select_order->get_end_hour_crew($order['fendhour']);
		$dataPayment = $this->CI->Select_order->get_payment($order['forderpaymentmethodid']);

		date_default_timezone_set('UTC');
		$time_stampe_fbookingdate = strtotime($order['fbookingdate']);

		if (!empty($order['fordervoucherdata'])) {
			$coupon = $this->CI->Select_order->get_coupons($order['fordervoucherdata'],true); 
			//print_r($coupon);
			if($coupon['fcoupontype']==1){
				$voucher_type = 'value';
				$voucher_value = $coupon['fcouponvalue'];
			}
			elseif($coupon['fcoupontype']==2){
				$voucher_type = 'percent';
				$voucher_value = ($coupon['fcouponvalue']/100)*$order['fcartsubtotal'];
			}		

			if (!empty($order)) {
				$return = array(
						'id' => $order['forderid'],
						'date' => $time_stampe_fbookingdate,
						'status' => $process_type,
						'user' => array(
								'user_id' => $user['fuserid'],
								'firstname' => $user['fuserfirstname'],
								'lastname' => $user['fuserlastname'],
								'phone' => $user['fuserphone'],
								'birthdate' => $user['fuserbirthdate'],
								'email' => $user['fuseremail'],
								'picture' => $user['fuserprofpic'],
								'gender' => $user['fusergender'],
								'is_verified' => true,
								'referral_code' => $user['fuserreferral']
							),
						'requests' => $unserialize_get_dataordercart,
						'location' => array(
								'lat' => $order['forderlat'],
								'lon' => $order['forderlon'],
								'address_line_1' => $order['forderaddressline1'],
								'address_line_2' => $order['forderaddressline2']
							),
						'schedules' => array(
								'start_hour' => array(
										'id' => $order['fstarthour'],
										'hour' => $return_start_hour['fhour']
									),
								'end_hour' => array(
										'id' => $order['fendhour'],
										'hour' => $return_end_hour['fhour']
									)
							),
						'payment_method' => array(
								'id' => $dataPayment['fpaymentid'],
								'type' => $dataPayment['fpaymentname']
							),
						'cart' => array(
								'id' => $order['fcartid'],
								'currency' => $order['fcartcurrency'],
								'sub_total' => $order['fcartsubtotal'],
								'vouchers' => array(
										'id' => $coupon['fcouponid'],
										'code' => $coupon['fcouponcode'],
										'type' => $voucher_type,
										'value' => $coupon['fcouponvalue']
									),
								'total' => $order['fcarttotal'],
								'additionals' => null
							),
						'note' => $order['fordernote']
					);
			}
		}
		else{

				$return = array(
						'id' => $order['forderid'],
						'date' => $time_stampe_fbookingdate,
						'status' => $process_type,
						'user' => array(
								'user_id' => $user['fuserid'],
								'firstname' => $user['fuserfirstname'],
								'lastname' => $user['fuserlastname'],
								'phone' => $user['fuserphone'],
								'birthdate' => $user['fuserbirthdate'],
								'email' => $user['fuseremail'],
								'picture' => $user['fuserprofpic'],
								'gender' => $user['fusergender'],
								'is_verified' => true,
								'referral_code' => $user['fuserreferral']
							),
						'requests' => $unserialize_get_dataordercart,
						'location' => array(
								'lat' => $order['forderlat'],
								'lon' => $order['forderlon'],
								'address_line_1' => $order['forderaddressline1'],
								'address_line_2' => $order['forderaddressline2']
							),
						'schedules' => array(
								'start_hour' => array(
										'id' => $order['fstarthour'],
										'hour' => $return_start_hour['fhour']
									),
								'end_hour' => array(
										'id' => $order['fendhour'],
										'hour' => $return_end_hour['fhour']
									)
							),
						'payment_method' => array(
								'id' => $dataPayment['fpaymentid'],
								'type' => $dataPayment['fpaymentname']
							),
						'cart' => array(
								'id' => $order['fcartid'],
								'currency' => $order['fcartcurrency'],
								'sub_total' => $order['fcartsubtotal'],
								'vouchers' => null,
								'total' => $order['fcarttotal'],
								'additionals' => null
							),
						'note' => $order['fordernote']
					);
			}
		
		

		return $return;
	}

	public function return_orders_crew($order){
		$return = array();
		$return_start_hour = $this->CI->Select_order->get_start_hour_crew($order['fstarthour']);
		$return_end_hour = $this->CI->Select_order->get_end_hour_crew($order['fendhour']);
		$user = $this->CI->Select_order->get_dataUser($order['fuserid']);
		$unserialize_get_dataordercart = unserialize($order['forderdataproduct']);
		$time_stampe_fbookingdate = strtotime($order['fbookingdate']);
		//$date = strtotime($order['forderdate']);
		if(is_array($unserialize_get_dataordercart)){ 
			if($order['forderprocessed']!==7){
			$process_type = 'incomplete';
			}
			if (!empty($order)) {
				$return = array(
					'id' => $order['forderid'],
					'status' => $process_type,
					'date' => $time_stampe_fbookingdate,
					'location' => array(
							'lat' => $order['forderlat'],
							'lon' => $order['forderlon'],
							'address_line_1' => $order['forderaddressline1'],
							'address_line_2' => $order['forderaddressline2']
						),
					'schedules' => array(
							'start_hour' => array(
									'id' => $order['fstarthour'],
									'hour' => $return_start_hour['fhour']
								),
							'end_hour' => array(
									'id' => $order['fendhour'],
									'hour' => $return_end_hour['fhour']
								)
						),
					'user' => array(
							'user_id' => $user['fuserid'],
							'firstname' => $user['fuserfirstname'],
							'lastname' => $user['fuserlastname'],
							'phone' => $user['fuserphone'],
							'birthdate' => $user['fuserbirthdate'],
							'email' => $user['fuseremail'],
							'picture' => $user['fuserprofpic'],
							'gender' => $user['fusergender'],
							'is_verified' => true,
							'referral_code' => $user['fuserreferral']
						),
					'requests' => $unserialize_get_dataordercart
				);
			}
		}

		return $return;
	}

	public function getAddDeviceCrew(){
		$return = array();

		if (!empty($_POST)) {
			if (!empty($this->CI->input->post('is_crew'))) {
				$user['fuserid'] = $this->CI->input->post('user_id',true);
				$data['fdeviceinstanceid'] = $this->CI->input->post('instance_id',true);
				$data['fdeviceimei'] = $this->CI->input->post('device_id',true);
				$data['fdevicename'] = $this->CI->input->post('device_name',true);
				$data['fis_crew'] = 1;
				$data['fuserid'] = $user['fuserid'];
				$cek = $this->CI->Select_order->cek_device_id($data['fdeviceimei']);
				//print_r($cek); 
				$crewid = $this->CI->Select_order->get_tcrew($user['fuserid']);
				if ($data['fdeviceimei'] == $cek['fdeviceimei']) {
					if ($user['fuserid'] == $crewid['fcrewid']) {
						//$update['fdeviceinstanceid'] = $data['fdeviceinstanceid'];
						$update['fis_crew'] = $data['fis_crew'];
						$update['fuserid'] = $user['fuserid'];
						$this->CI->Update_order->update_device_id($update,$cek['fdeviceid']); 
					}
					
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}
				else{
					$this->CI->Insert_order->add_device_id($data);
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}	  	
			}
			else{
				$data['fuserid'] = $this->CI->input->post('user_id',true);
				$data['fdeviceinstanceid'] = $this->CI->input->post('instance_id',true); 
				$data['fdeviceimei'] = $this->CI->input->post('device_id',true);
				$data['fdevicename'] = $this->CI->input->post('device_name',true);
				$cek = $this->CI->Select_order->cek_device_id($data['fdeviceimei']);
				if ($data['fdeviceimei'] == $cek['fdeviceimei']) {
					//$crewid = $this->CI->Select_order->get_tcrew($data['fuserid']);
					//if ($data['fuserid'] == $crewid['fcrewid']) {
						//$update['fdeviceinstanceid'] = $data['fdeviceinstanceid'];
						$update['fuserid'] = $data['fuserid'];
						$update['fis_crew'] = 0;
						$this->CI->Update_order->update_device_id($update,$cek['fdeviceid']); 
					//}
					
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}
				else{
					$this->CI->Insert_order->add_device_id($data);
					$return['status'] = 1;
					$return['data']['success'] = true; 
				}	
				
			}
		
		}
	
		return $return;

	}

	// public function getCarCondition(){
	// 	$return = array();
	// 	$data['fchassisid'] = $this->CI->input->post('chassis_id');
	// 	$data['forderid'] = $this->CI->input->post('order_id');
	// 	$data['fuservehicleid'] = $this->CI->input->post('vehicle_user_id');
	// 	$data['fmessage'] = $this->CI->input->post('message');
	// 	if (!empty($data)) {
	// 		if ($DataChassis = $this->CI->Select_order->get_chassis($data['fchassisid'])) {
	// 			echo '<pre>';
	// 			print_r($DataChassis);
	// 			echo '</pre>';
	// 			$this->CI->Insert_order->createChassis($data);	
	// 			$e=0;
	// 			foreach ($DataChassis as $chassis) {
	// 				$return['status'] = 1;			
	// 				$return['data']['chassis'] = true;
	// 			}
				
	// 		}
			
	// 	}
	// 	else{
	// 		$return['status'] = 1;			
	// 		$return['data']['chassis'] = null;
	// 	}

	// 	return $return;
	// }

	//lanjutin besok//
	public function getCarCondition(){
		$return = array();
		$data['fchassisid'] = $this->CI->input->post('chassis_id');
		$data['forderid'] = $this->CI->input->post('order_id');
		$data['fuservehicleid'] = $this->CI->input->post('vehicle_user_id');
		$data['fmessage'] = $this->CI->input->post('message');
		if (!empty($data)) {
			
		
			if ($DataChassis = $this->CI->Select_order->get_chassis($data['fchassisid'])) {
				$this->CI->Insert_order->createChassis($data);
				//ambil data dari tcart
				$cart = $this->CI->Select_order->get_cart_product($data['forderid']);
				//ambil data dari torder
				$order = $this->CI->Select_order->get_torder_data($data['forderid']);

				$unserialize_data_cart = unserialize($cart['fcartproduct']);
				$return_start_hour = $this->CI->Select_order->get_start_hour_crew($cart['fstarthour']);
				$return_end_hour = $this->CI->Select_order->get_end_hour_crew($cart['fendhour']);
				$dataPayment = $this->CI->Select_order->get_payment($order['forderpaymentmethodid']);
				// echo '<pre>';
				// print_r($DataChassis);
				// echo '</pre>';
				if (!empty($order['fordervoucherdata'])) {
					$coupon = $this->CI->Select_order->get_coupons($order['fordervoucherdata'],true); 
					//print_r($coupon);
					if($coupon['fcoupontype']==1){
						$voucher_type = 'value';
						$voucher_value = $coupon['fcouponvalue'];
					}
					elseif($coupon['fcoupontype']==2){
						$voucher_type = 'percent';
						$voucher_value = ($coupon['fcouponvalue']/100)*$order['fcartsubtotal'];
					}

					
					$e=0;
					foreach ($DataChassis as $chassis) {
						$user = $this->CI->Select_order->get_dataUser($chassis['fuserid']);
						
						$return['status'] = 1;
						$return['data']['chassis'][$e] = $this->returnChassis($chassis);
						$return['data']['order'] = array(
							'id' => $chassis['forderid'],
							'status' => $chassis['forderprocessed'],
							'date' => $chassis['forderdate'],
							'user' => $this->getUser(0,$user),
							'request' => $unserialize_data_cart,
							'location' => array(
									'lat' => $order['forderlat'],
									'lon' => $order['forderlon'],
									'address_line_1' => $order['forderaddressline1'],
									'address_line_2' => $order['forderaddressline2']
								),
							'schedules' => array(
									'start_hour' => array(
											'id' => $cart['fstarthour'],
											'hour' => $return_start_hour['fhour']
										),
									'end_hour' => array(
											'id' => $cart['fendhour'],
											'hour' => $return_end_hour['fhour']
										)
								),
							'payment_method' => array(
									'id' => $dataPayment['fpaymentid'],
									'type' => $dataPayment['fpaymentname']
								),
							'cart' => array(
									'id' => $cart['fcartid'],
									'currency' => $cart['fcartcurrency'],
									'sub_total' => $cart['fcartsubtotal'],
									'vouchers' => array(
											'id' => $coupon['fcouponid'],
											'code' => $coupon['fcouponcode'],
											'type' => $voucher_type,
											'value' => $coupon['fcouponvalue']
										),
									'total' => $order['fcarttotal'],
									'additionals' => null
								),
							'note' => $order['fordernote']
						);
						
					}
				}else{
					$e=0;
					foreach ($DataChassis as $chassis) {
						$user = $this->CI->Select_order->get_dataUser($chassis['fuserid']);
						
						$return['status'] = 1;
						$return['data']['chassis'][$e] = $this->returnChassis($chassis);
						$return['data']['order'] = array(
							'id' => $chassis['forderid'],
							'status' => $chassis['forderprocessed'],
							'date' => $chassis['forderdate'],
							'user' => $this->getUser(0,$user),
							'request' => $unserialize_data_cart,
							'location' => array(
									'lat' => $order['forderlat'],
									'lon' => $order['forderlon'],
									'address_line_1' => $order['forderaddressline1'],
									'address_line_2' => $order['forderaddressline2']
								),
							'schedules' => array(
									'start_hour' => array(
											'id' => $cart['fstarthour'],
											'hour' => $return_start_hour['fhour']
										),
									'end_hour' => array(
											'id' => $cart['fendhour'],
											'hour' => $return_end_hour['fhour']
										)
								),
							'payment_method' => array(
									'id' => $dataPayment['fpaymentid'],
									'type' => $dataPayment['fpaymentname']
								),
							'cart' => array(
								'id' => $cart['fcartid'],
								'currency' => $cart['fcartcurrency'],
								'sub_total' => $cart['fcartsubtotal'],
								'vouchers' => null,
								'total' => $cart['fcarttotal'],
								'additionals' => null
							),
							'note' => $order['fordernote']
						);
						
					}
				}
				
			}
		}else{
			$return['status'] = 1;			
			$return['data']['model'] = null;
		}

		return $return;
	}

	public function getUser($id=0,$user=array()){
		$return = array();
		$where = "t_users.fuserid = " .$user['fuserid']." and fmetakey in ('socmed_type','socmed_id')";
		if(!empty($user)){
			$return = array(
				'id' => $user['fuserid'],
				'firstname' => $user['fuserfirstname'],
				'lastname' => $user['fuserlastname'],
				'phone' => $user['fuserphone'],
				'birthdate' => $user['fuserbirthdate'],
				'email' => $user['fuseremail'],
				'picture' => $user['fuserprofpic'],
				'gender' => $user['fusergender'],
				'is_verified' => true,
				'referral_code' => $user['fuserreferral'],
				'social_media' => null
			);
			if ($socmed = $this->CI->Select_user->get_meta_where($where,false)) {
				$return['social_media'] = array();
				$return['social_media'] = $this->getSocmedia(0,$socmed);
			}
		}

		return $return;
	}

	public function getSocmedia($id=0,$soc=array()){
		$return = array();
		$i=0;
		if (!empty($soc)) {
			foreach ($soc as $socm) {
				$return[$i][$socm['fmetakey']] = $socm['fmetavalue'];
				if ($socm['fmetakey'] == 'socmed_id') $i++;
			}
		}

		return $return;
	}

	public function returnChassis($chassis=array()){ 
		$return = array();
		if (!empty($chassis)) {
			$return = array(
				'code' => $chassis['fchassiscode'],
				'type_chassis' => $chassis['fchassisname']
			);
		}

		return $return;
	}

	

}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */