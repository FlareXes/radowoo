<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Media extends Tutor_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->add_external_css(base_url("/assets/themes/admin/css/all.css"));
        $this->load->library('form_validation');
        $this->load->helper('url');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('tutor/media/upload'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");

        $this->add_external_js("//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"); 
        $this->add_external_js(base_url("/assets/themes/admin/js/mediaupload.js")); 

    }



    public function index()
    {
       

        $this->load->helper('url');
        $this->set_title(lang('upload_media'));
        $data = $this->includes;
        $connector = site_url() .'tutor/media/upload';
        $content_data = array('connector' => $connector );
        $data['content'] = $this->load->view('tutor/media/elfinder', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
        

    public function upload()
    {
        $contant_type_arr = array('image', 'text/plain', 'application/pdf','audio','video','document');
        $uploadDeny = array('all');


        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $username = isset($this->user['username']) ? $this->user['username'] : rand();

        $path =  FCPATH . '/media/'.$user_id."_".$username."/";
        $url =  base_url().'media/'.$user_id."_".$username."/";
        if(!is_dir($path)) 
        {
            mkdir($path, 0775, TRUE);
        }

        $this->load->helper('url');

        $opts = array(
                'roots' => array(
                    array( 
                        'driver'        => 'LocalFileSystem',
                        'path'          => $path,
                        'URL'           => $url,
                        'uploadDeny'    => $uploadDeny,                  // All Mimetypes not allowed to upload
                        'uploadAllow'   => $contant_type_arr,
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


    public function select()
    {
        // p($this->input->post());
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $username = isset($this->user['username']) ? $this->user['username'] : rand();
        $path =  FCPATH . '/media/'.$user_id."_".$username."/";
        $url =  base_url().'media/'.$user_id."_".$username."/";
        $this->load->helper('url');
        $this->set_title(lang('select_media'));
        $data = $this->includes;
        $connector = site_url() .'tutor/media/upload';
        $content_data = array('connector' => $connector,'path' => $path,'url'=> $url );
        $data['content'] = $this->load->view('tutor/media/select_media', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }
        
  
}
