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
		//$this->CI->load->model('api/product/Delete_product', '', TRUE);
	}

	public function getProduct($type='product',$product=array(),$id=0)
	{
		switch($type) 
		{
			case 'detail':
				$return['status'] = 1;
				if(!$return['product'] = $this->getProductDetail($product,$id,true))
				{
					$return['product'] = null;
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
			case 'tag':
				switch($product)
				{
					case 'product':
						return $this->getList('tag');
						break;
					case 'album':
						return $this->getAlbumList('tag');
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
		$return['data']['products'] = null;
		$where = '';
	

		$offset = (int)$this->CI->input->post('page',TRUE);
		if(!$offset) $offset = 1;
		if($products = ($type!='search') ? $this->CI->Select_product->get($where,$offset) : $this->CI->Select_product->get_search($where,$offset))
		{
			$totalProducts = ($type!='search') ? $this->CI->Select_product->get_count($where) : $this->CI->Select_product->get_search_count($where);
			$totalPage = ceil($totalProducts/$this->limit);
			if($offset<$totalPage)
				$return['data']['next'] = $offset+1;
			$p = 0;
			foreach($products as $product)
			{
				$return['data']['products'][$p] = $this->getProductDetail($product,0);	
				$p++;
			}
		}
		
		return $return;
	}

	private function getProductDetail($product,$id,$detail=false)
	{
		$return = null;
		$userid = $this->CI->input->post('user_id',TRUE);
		$id = (!empty($this->CI->input->post('product_id',TRUE))) ? $this->CI->input->post('product_id',TRUE) : $id;

		if(empty($product))
		{
			$where['f_productid'] = $id;
			$product = $this->CI->Select_product->get_where($where);
		}
		if(!empty($product))
		{	
			//var_dump($product);
			
			$return	= array(
				'id' => $product['f_productid'],
				'name' => $product['f_productname'],
				'price' => $product['f_productprice'],
				'price-promo' => $product['f_productprice'],
				'stock' => $product['f_productquantity'],
				'category' => $product['f_kategoriname'],
				'image' => $product['f_productimage'],
				'description' => $product['f_productdescription'],
				'marchant' => array(
					'id' => $product['f_petaniid'],
					'name' => $product['f_petaniname'],
					'address' => $product['f_petaniaddress'],
					'description' => $product['f_petanidesc'],
					'image' => $product['f_productimage'],		
				),
			);
		}
		
		return $return;
	}
	
}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */