<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Contact extends Public_Controller {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        // load the model file
        $this->load->model('ContactModel');
        // load the captcha helper
        $this->load->helper('captcha');
        // $this->add_js_theme('api.js');
        $this->add_external_js('https://www.google.com/recaptcha/api.js');
    }
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    function index() 
    {
        $customfields = $this->db->where('form','contact')->order_by('field_order', 'asc')->get('custom_fields')->result();

        if($customfields && $_POST)
        {
            foreach ($customfields as  $customfield) 
            {
                if($customfield->is_required == 1 && empty($this->input->post($customfield->field_name)))
                {
                    if($customfield->field_type == 'file')
                    {
                        if(isset($_FILES[$customfield->field_name]['name']))
                        {
                            $response = $this->upload_file($customfield->field_name);
                            if($response['status'] == true)
                            {
                                $cf_files_name[$customfield->field_name] = $response['name'];
                            }
                            else
                            {
                                $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
                                $this->session->set_flashdata('error', $customfield->field_label.' '. $response['msg']);
                            }
                        }
                        else
                        {
                            $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
                            $this->session->set_flashdata('error', $customfield->field_label.' Is Required');
                        }
                    }
                    else
                    {
                        $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
                    }
                }
            }
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[64]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|min_length[10]|max_length[256]');
        $this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[128]');
        $this->form_validation->set_rules('message', 'Message', 'required|trim|min_length[10]');
        // $this->form_validation->set_rules('captcha', 'Captcha', 'required|trim|callback__check_captcha');


        if ($this->settings->recaptcha_secret_key && $this->settings->recaptcha_site_key && $this->settings->enable_captch_code_login == "YES")
        {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }


        if ($this->form_validation->run() == TRUE) {
            // attempt to save and send the message
            $post_data = $this->security->xss_clean($this->input->post());

            $saved_and_sent = $this->ContactModel->save_and_send_message($post_data, $this->settings);
            
            if ($saved_and_sent) 
            {
                // redirect to home page
                if($customfields)
                {
                    $customfield_val_array = [];
                    foreach ($customfields as  $customfield) 
                    {
                        $customfield_val = [];
                        if($customfield->field_type == 'checkbox')
                        {
                            $value = $this->input->post("$customfield->field_name") && is_array($this->input->post("$customfield->field_name")) ? $this->input->post("$customfield->field_name") : array();
                            $value = json_encode($value);
                        }
                        else if($customfield->field_type == 'file')
                        {
                            $value = isset($cf_files_name[$customfield->field_name]) ? $cf_files_name[$customfield->field_name] : NULL;
                        }
                        else
                        {
                            $value = $this->input->post("$customfield->field_name") ? $this->input->post("$customfield->field_name") : NULL;
                        }

                        $customfield_val['field_name'] = $customfield->field_name;
                        $customfield_val['rel_id'] = $c_f_id;
                        $customfield_val['value'] = $value;
                        $customfield_val_array[] = $customfield_val;
                    }

                    if($customfield_val_array)
                    {
                        $this->db->insert_batch('custom_field_values', $customfield_val_array); 
                    }
                }

                $this->session->set_flashdata('message', sprintf(lang('contact_msg_send_success'), $this->input->post('name', TRUE)));
                redirect(base_url());
            } 
            else 
            {
                // stay on contact page
                $this->error = sprintf(lang('contact_error_send_failed'), $this->input->post('name', TRUE));
            }
        }

        $this->set_title(lang('contact_title'));
        $data = $this->includes;
        // set content data
        
        
        $content_data = array('customfields'=> $customfields,);
        // load views
        $data['content'] = $this->load->view('contact/form', $content_data, TRUE);
        $this->load->view($this->template, $data); 
    }
    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/
    /**
     * Verifies correct CAPTCHA value
     *
     * @param  string $captcha
     * @return string|boolean
     */
    function _check_captcha($captcha) {
        $verified = $this->ContactModel->verify_captcha($captcha);
        if ($verified == FALSE) {
            $this->form_validation->set_message('_check_captcha', lang('error_captcha'));
            return FALSE;
        } else {
            return $captcha;
        }
    }


    public function recaptcha($str = '')
    {
        if(empty($str))
        {
            $this->form_validation->set_message('recaptcha', 'Please Check Captcha Code First');
            return false;
        }
        return do_recaptcha_validation($str);
    }
}
