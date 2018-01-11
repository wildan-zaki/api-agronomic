<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*api routes*/
$route['api/(:any)'] = 'api/$1';
$route['api/(:any)/(:any)'] = 'api/$1/$2';
$route['api/(:any)/(:any)/(:any)'] = 'api/$1/$2/$3';

/*corn routes*/
$route['cron'] = 'backend/cron';
$route['cron/(:any)'] = 'backend/cron/$1';
$route['cron/(:any)/(:any)'] = 'backend/cron/$1/$2';
$route['cron/(:any)/(:any)/(:any)'] = 'backend/cron/$1/$2/$3';

/*notif routes*/
$route['notification/(:any)'] = 'notification/$1';
$route['notification/(:any)/(:any)'] = 'notification/$1/$2';
$route['notification/(:any)/(:any)/(:any)'] = 'notification/$1/$2/$3';

/*admin routes*/
$route['backend'] = 'backend/login';
$route['backend/logout'] = 'backend/login/logout';
$route['backend/(:any)'] = 'backend/$1';
$route['backend/(:any)/page/(:num)'] = 'backend/$1/page/$2';
$route['backend/(:any)/(:any)'] = 'backend/$1/$2';
$route['backend/(:any)/(:any)/page/(:num)'] = 'backend/$1/$2/$3';
$route['backend/(:any)/(:any)/add'] = 'backend/$1/$2_add';
$route['backend/(:any)/(:any)/csv'] = 'backend/$1/csv/$2';
$route['backend/(:any)/(:any)/(:any)'] = 'backend/$1/$2/$3';
$route['backend/(:any)/(:any)/csv/(:any)'] = 'backend/$1/csv/$2/$3';
$route['backend/(:any)/(:any)/detail/(:num)'] = 'backend/$1/$2_detail/$3';

/*web routes*/
$route['(:any)/(:any)/(:any)'] = 'web/$1/$2/$3';
$route['(:any)/(:any)'] = 'web/$1/$2';
$route['(:any)'] = 'web/$1';