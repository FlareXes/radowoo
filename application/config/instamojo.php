<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* instamojo payment API v1 library for CodeIgniter
*
* @license Creative Commons Attribution 3.0 <http://creativecommons.org/licenses/by/3.0/>
* @version 1.0
* @author Rajeev bbqq <https://github.com/rajeevbbqq>
* @copyright Copyright (c) 2018, Rajeev bbqq
*/

/*
|--------------------------------------------------------------------------
| Mode
|--------------------------------------------------------------------------
|
| $config['mojo_mode'] = 'sandbox'; for testing
| $config['mojo_mode'] = 'live'   ; for production
|
*/


//$config['mojo_mode']  = 'sandbox' ;


/*
|--------------------------------------------------------------------------
| API_KEY
|--------------------------------------------------------------------------
| API_KEY are different for test and production !
| $config['mojo_apikey'] = '650f7eed3d900273d6fafd635a';
|
*/

//$config['mojo_apikey'] = get_admin_setting('instamojo_apikey');
// $config['mojo_apikey'] = 'd05021c78721ea84c6af21120971d21b' ;


/*
|--------------------------------------------------------------------------
| AUTH_TOKEN
|--------------------------------------------------------------------------
| AUTH_TOKEN are different for test and production !
| $config['mojo_token'] = '650f7eed3d900273d6fafd635a';
|
*/

//$config['mojo_token'] = get_admin_setting('instamojo_token');
// $config['mojo_token']  = '4438a012a569086c3a6811edd3e0260f' ;


/*
|--------------------------------------------------------------------------
| REDIRECT_URL
|--------------------------------------------------------------------------
| Set redirect url !
| $config['mojo_url'] = 'https://github.com/Instamojo/instamojo-php';
|
*/

// $config['mojo_url'] = 'mojo_url';
// $config['mojo_url'] = 'https://www.instamojo.com/@ashwch/d66cb29dd059482e8072999f995c4eef';
//$config['mojo_url'] = 'http://localhost/quiz-gboss/Instamojo_Controller/payment_status';


/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
| Creates a 'mojo' table and store all orders, if set to true
|
*/

$config['mojo_db']  = false;


/*
|--------------------------------------------------------------------------
| TABE NAME
|--------------------------------------------------------------------------
| Creates table if $config['mojo_db']  = true ;
|
*/

// $config['mojo_table']  = 'instamojo';


/* End of file instamojo.php */
/* Location: ./application/config/instamojo.php */
