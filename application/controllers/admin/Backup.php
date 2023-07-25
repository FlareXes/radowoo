<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Backup extends Admin_Controller {
    function __construct() 
    {
        parent::__construct();
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_js_theme('backup.js');


        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_url'));
            return redirect(base_url('admin/dashboard'));
        }
    }

    function index() 
    {        
        $this->set_title(lang('course'));
        $data = $this->includes;
        $path = './assets/db_backup/';
        $backups_array = directory_map($path);

        $content_data = array('backups_array' => $backups_array);
        $data['content'] = $this->load->view('admin/backup/db-list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function remove_backup($file_name)
    {
        $path = './assets/db_backup/';
        if(is_file($path.$file_name))
        {
            unlink($path.$file_name);
            $this->session->set_flashdata('message', lang("db_backup_file_unlink_successfuly"));
        } 
        else
        {
            $this->session->set_flashdata('error', lang("error_during_unlink_db_backup_file...!"));
        }
        redirect(base_url('admin/db-backup-list')); 
    }


    function download_backup($file_name)
    {
        $this->load->helper('download');
        $path = './assets/db_backup/';
        if(is_file($path.$file_name))
        {
            force_download($path.$file_name,NULL);
        } 
        else
        {
            $this->session->set_flashdata('error', lang("db_backup_file_dose_not_exist...!"));
            redirect(base_url('admin/db-backup-list')); 

        }
    }


    public function db_backup()
    {
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->library('zip');
        $this->load->dbutil();
        $db_format = array();
        $db_format['format'] = 'zip'; 
        $db_format['filename'] = 'my_db_backup.sql';
        
        $backup = @$this->dbutil->backup($db_format);
        $dbname ='backup-on-'.date('Y-m-d-H-i-s').'.zip';
        
        $path = './assets/db_backup/';
       
        if (!is_dir($path)) 
        {
            mkdir($path, 0775, TRUE);
        }

        $save= $path.$dbname;
        write_file($save,$backup);
        // force_download($dbname,$backup);
        $this->session->set_flashdata('message', lang("db_backup_successfuly...!"));
        redirect(base_url('admin/db-backup-list'));
    }
}