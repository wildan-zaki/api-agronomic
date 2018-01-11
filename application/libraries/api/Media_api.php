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
class Media_api {
	private $CI;    
	var $limit = 10; 
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('string');
		$this->CI->load->model('api/media/Insert_media', '', TRUE);
		$this->CI->load->model('api/media/Select_media', '', TRUE);
	}
	
	public function uploadPhoto(){
		$return = '';
		$config['upload_path'] = './assets/media/uploads';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name'] = TRUE;
		
		$this->CI->load->library('upload', $config);
		if ( ! $this->CI->upload->do_upload('picture'))
		{
			echo $this->CI->upload->display_errors('<p>', '</p>');
		}
		else
		{
			$uploads = $this->CI->upload->data();
			if( $where['fmediaid'] = $this->CI->Insert_media->addNewMedia($uploads) ){
				if( $photo = $this->CI->Select_media->get_where($where) ){
					$return['photo id'] = $photo['fmediaid'];	
					$return['url'] = $photo['fmediapath'];
					$return['caption'] = $photo['fmediacaption'];
				}
			}
		}
		return $return;
	}
}

/* End of file Unzip.php */
/* Location: ./system/libraries/Unzip.php */