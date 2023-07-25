<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Admin Class - used for all administration pages
 */

class Tutor_Controller extends MY_Controller 
{
    
    /**
     * Constructor
     */

    function __construct() 
    {

        parent::__construct();
        // must be logged in 
        if (!$this->user) 
        {
            if (current_url() != base_url()) 
            {
                //store requested URL to session - will load once logged in
                $data = array('redirect' => current_url());
                $this->session->set_userdata($data);
            }
            redirect('login');
        }
        // make sure this user is setup as admin

        if($this->user['is_admin'] == 1) 
        {
            redirect(base_url('admin'));
        }
        
        if($this->user['role'] != "tutor") 
        {
            redirect(base_url());
        }
        
        // prepare theme name
        $this->settings->theme = strtolower($this->config->item('tutor_theme'));
        // set up global header data



        $this->add_external_css(
            array(
                base_url("/assets/themes/admin/css/bootstrap.min.css"), 
                base_url("/assets/themes/admin/css/all.min.css"), 
                base_url("/assets/themes/admin/css/dataTables.bootstrap4.min.css"), 
                base_url("/assets/themes/admin/css/select2.min.css"), 
                base_url("/assets/themes/admin/css/custom.css"),
                base_url("/assets/themes/admin/css/noty.css"),
            )
        );

        $this->add_external_js(
            array(
                base_url("/assets/themes/admin/js/popper.js"), 
                base_url("/assets/themes/admin/js/bootstrap.min.js"), 
                base_url("/assets/themes/admin/js/jquery.nicescroll.min.js"),
                base_url("/assets/themes/admin/js/jquery.dataTables.min.js"), 
                base_url("/assets/themes/admin/js/dataTables.bootstrap4.min.js"), 
                base_url("/assets/themes/admin/js/select2.min.js"),
                base_url("/assets/themes/admin/js/noty.min.js"),
                base_url("/assets/themes/tutor/js/tutorcommon.js"),
            )
        );


        $this->add_css_theme("style.css", "admin");
        $this->add_js_theme("scripts.js");
        $this->add_js_theme("custom.js");

        // $this->add_js_theme("admin_i18n.js", TRUE,TRUE);

        $this->load->helper("my_admin_setting_helper");
        $this->load->model("AdminSettingModel");
        // declare main template
        $this->template = "../../{$this->settings->themes_folder}/{$this->settings->theme}/template.php";
        $this->template = "../../{$this->settings->themes_folder}/{$this->settings->theme}/template.php";
    }
}