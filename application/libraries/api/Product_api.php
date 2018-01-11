<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Product API Class
 */
  
class Product_api {
	private $CI;    
	var $limit = 10; 
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('string');
		$this->CI->load->model('api/product/Select_product', '', TRUE);
		$this->CI->load->model('api/product/Insert_product', '', TRUE);
		$this->CI->load->model('api/product/Update_product', '', TRUE);
		$this->CI->load->model('api/product/Delete_product', '', TRUE);
		//$this->CI->load->model('api/media/Select_media', '', TRUE);
	}

	public function getProduct($type='product',$product=array(),$id=0)
	{
		switch($type)
		{
			case 'detail':
				$return['status'] = 1;
				if(!$return['modules']['product'] = $this->getProductDetail($product,$id,true))
				{
					$return['modules']['product'] = null;
				}
				return $return;
				break;
			case 'search':
				switch($product)
				{
					case 'product':
						return $this->getList('search');
						break;
					case 'tags':
						return $this->getTags('search');
						break;						
				}
				break;
			default:
				return $this->getList($type);
				break;	
		}
	}

	private function getList($type)
	{
		$this->CI->load->library('api/product_api');
		$return['status'] = 1;
		$continue = false;
		$where = '';
		
		$min = $this->CI->input->post('price_min');
		$max = $this->CI->input->post('price_max');
		if($min && $max){
			$where = "t_product.f_productprice BETWEEN ".$min." AND ".$max."";
		}
		else{
			
			if($min){
				$where = "t_product.f_productprice > ".$min."";
			}
			if($max){
				$where = "t_product.f_productprice < ".$max."";
			}
		}
		
		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		$category = array();
		$category = $this->CI->input->post('category',TRUE);

		if(!empty($category)){
			$where = "t_product.f_kategoriid in ('".implode("','",$category)."')";
			$continue = true;
		}

		if(!empty($sort_by = $this->CI->input->post('sort_by',TRUE))){
			$continue = true;
		}

		if(!$continue){
			$mainproduct = array('Popular','Promo');
			$y = 0;

			foreach ($mainproduct as $main) {
				$return['modules'][$y]['module_name'] = $main;
				if($main=='Popular'){
					$type_product = 'popular';
				}else if($main=='Promo'){
					$type_product = 'promo'; 
					$where = 'f_metakey = "featured" AND f_metavalue = 1';
				}
				$p=0;
				$featured = $this->CI->Select_product->get_featured_product($where,$type_product);
				foreach($featured as $product)
				{
					$return['modules'][$y]['product'][$p] = $this->getProductDetail($product,0);	
					$p++;
				}
				$y++;
			}	
		}else{
			$return['modules']['product'] = null;
			if($products = ($type!='search') ? $this->CI->Select_product->get($where,$offset) : $this->CI->Select_product->get_search($where,$offset))
			{
				//var_dump($products);
				$totalProducts = ($type!='search') ? $this->CI->Select_product->get_count($where) : $this->CI->Select_product->get_search_count($where);
				$totalPage = ceil($totalProducts/$this->limit);
				if($offset<$totalPage)
					$return['modules']['next'] = $offset+1;
				$p=0;
				foreach($products as $product)
				{
					$return['modules']['product'][$p] = $this->getProductDetail($product,0);	
					$p++;
				}
			}
		}
		
		
		return $return;
	}

	private function getProductDetail($product,$id,$detail=false)
	{
		$return = null;
		$id = (!empty($this->CI->input->post('product_id',TRUE))) ? $this->CI->input->post('product_id',TRUE) : $id;
		$file = null;
		if(empty($product))
		{
			$where['f_productid'] = $id;
			$product = $this->CI->Select_product->get_where($where);
		}
		if(!empty($product))
		{	
			$where['f_productid'] = $product['f_productid'];
			$product = $this->CI->Select_product->get_where($where);
			$return	= array(
				'id' => $product['f_productid'],
				'name' => $product['f_productname'],
				'category' => array(
					'id' => $product['f_kategoriid'],
					'categoryname' => $product['f_kategoriname']),
				'images'	=> $product['f_productimage'],
				'customer_price' => $product['f_productprice'],
				'special_price' => $product['f_productspecialprice'],
				'currenty_format' => 'Rp',
			);
		}
		
		return $return;
	}
	
}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */