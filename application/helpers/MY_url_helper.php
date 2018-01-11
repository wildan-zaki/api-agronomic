<?php defined('BASEPATH') or exit('No direct script access allowed');

function backend_url($uri = '', $protocol = NULL)
{
	return get_instance()->config->site_url('backend/'.$uri, $protocol);
}

function backend_assets($uri = '', $protocol = NULL)
{
	return get_instance()->config->site_url('assets/backend/'.$uri, $protocol);
}