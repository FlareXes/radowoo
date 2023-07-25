<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Level extends Admin_Controller 
{

    function __construct() 
    {
        parent::__construct();
        $this->session->set_flashdata('error', lang('Invalid Uri Request... !'));
        return redirect(base_url('admin'));
    }

}