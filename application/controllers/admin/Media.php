<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Media extends Admin_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');

        $this->load->model('AdvertismentModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/media/upload'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");

        if(is_loged_in_user_is_subadmin() == TRUE || $this->settings->file_uploader != 'media manager')
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }

        $this->add_external_js("//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"); 
        $this->add_external_js(base_url("/assets/themes/admin/js/mediaupload.js")); 

    }



    public function index()
    {
       

        $this->load->helper('url');
        $this->set_title(lang('upload_media'));
        $data = $this->includes;
        $connector = site_url() .'admin/media/upload';
        $content_data = array('connector' => $connector );
        $data['content'] = $this->load->view('admin/media/elfinder', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
        

    public function upload()
    {
        $path =  FCPATH . '/media';
        if (!is_dir($path)) 
        {
            mkdir($path, 0775, TRUE);
        }

        $this->load->helper('url');
        $opts = array(
            'roots' => array(
                array( 
                    'driver'        => 'LocalFileSystem',
                    'path'          => $path,
                    'URL'           => base_url('media'),
                    'uploadDeny'    => array('all'),                  // All Mimetypes not allowed to upload
                    'uploadAllow'   => array('image', 'text/plain', 'application/pdf'),// Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder'   => array('deny', 'allow'),        // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => array($this, 'elfinderAccess'),// disable and hide dot starting files (OPTIONAL)
                    // more elFinder options here
                ) 
            ),
        );
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }
        
    public function elfinderAccess($attr, $path, $data, $volume, $isDir, $relpath)
    {
        $basename = basename($path);
        return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
                 && strlen($relpath) !== 1           // but with out volume root
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            :  null;                                 // else elFinder decide it itself
    }
  
}
