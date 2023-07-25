<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Settings extends Admin_Controller {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }
    }
    /**
     * Settings Editor 
     */
    function index() { 

        $all_lang = $this->SettingsModel->all_lang_list();
        $lang_array = array();
        foreach ($all_lang as  $all_lang_array) 
        {
            $lang_array[$all_lang_array->lang] = $all_lang_array->lang;
        }
        $lang_array = $lang_array ? $lang_array : $lang_array['English'] = 'English';
        $lang_array = json_encode($lang_array);
        $this->SettingsModel->update_lang_options($lang_array);

        $get_pages_name = array();
        $get_pages = $this->db->select('title,slug')->get('pages')->result();
        foreach($get_pages as $page_key => $page_value)
        {
            $get_pages_name[''] = 'None';
            $get_pages_name[$page_value->slug] = $page_value->title; 
        }
        
        $get_pages_json['options'] = json_encode($get_pages_name);
        
        $this->db->where('name','pages_list')->update('settings',$get_pages_json);
           
        // get settings
        $settings = $this->SettingsModel->get_settings();

        //p($settings);    
        // form validations
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $settings_name_array = array();
        foreach ($settings as $setting) 
        {
            $settings_name_array[$setting['name']] = $setting['value'];
            if ($setting['validation']) 
            {
                if ($setting['translate']) 
                {
                    // setup a validation for each translation
                    foreach ($this->session->languages as $language_key => $language_name) 
                    {
                        $this->form_validation->set_rules($setting['name'] . "[" . $language_key . "]", $setting['label'] . " [" . $language_name . "]", $setting['validation']);
                    }
                } 
                else 
                {
                    // single validation
                    $this->form_validation->set_rules($setting['name'], $setting['label'], $setting['validation']);
                }
            }
            
        }

        if ($this->form_validation->run() == TRUE) 
        {
            action_not_permitted();
            $user = $this->session->userdata('logged_in');
            $form_data = $this->input->post();
            if (isset($_FILES['site_logo']['name']) && $_FILES['site_logo']['name']) {
                $status = NULL;
                $status = $this->do_upload($_FILES['site_logo'], 'site_logo');
                if ($status['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Site Logo ' . $status['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['site_logo'] = $status['file_name'];
                }
            }
            if (isset($_FILES['site_favicon']['name']) && $_FILES['site_favicon']['name']) {
                $site_favicon = NULL;
                $site_favicon = $this->do_upload($_FILES['site_favicon'], 'site_favicon');
                if ($site_favicon['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Site Favicon ' . $site_favicon['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['site_favicon'] = $site_favicon['file_name'];
                }
            }
            if (isset($_FILES['home_first_slide']['name']) && $_FILES['home_first_slide']['name']) {
                $home_first_slide = NULL;
                $home_first_slide = $this->do_upload($_FILES['home_first_slide'], 'home_first_slide');
                if ($home_first_slide['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Home First Slide ' . $home_first_slide['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['home_first_slide'] = $home_first_slide['file_name'];
                }
            }
            if (isset($_FILES['home_slide_second']['name']) && $_FILES['home_slide_second']['name']) {
                $home_slide_second = NULL;
                $home_slide_second = $this->do_upload($_FILES['home_slide_second'], 'home_slide_second');
                if ($home_slide_second['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Home Second Slide ' . $home_slide_second['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['home_slide_second'] = $home_slide_second['file_name'];
                }
            }
            if (isset($_FILES['home_third_slide']['name']) && $_FILES['home_third_slide']['name']) {
                $home_third_slide = NULL;
                $home_third_slide = $this->do_upload($_FILES['home_third_slide'], 'home_third_slide');
                if ($home_third_slide['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Home First Slide ' . $home_third_slide['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['home_third_slide'] = $home_third_slide['file_name'];
                }
            }

            if (isset($_FILES['home_fourth_slide']['name']) && $_FILES['home_fourth_slide']['name']) {
                $home_fourth_slide = NULL;
                $home_fourth_slide = $this->do_upload($_FILES['home_fourth_slide'], 'home_fourth_slide');
                if ($home_fourth_slide['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Home First Slide ' . $home_fourth_slide['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['home_fourth_slide'] = $home_fourth_slide['file_name'];
                }
            }

            if (isset($_FILES['quizzy_reward_animation_image']['name']) && $_FILES['quizzy_reward_animation_image']['name']) 
            {
                $quizzy_reward_animation_image = NULL;
                $quizzy_reward_animation_image = $this->do_upload($_FILES['quizzy_reward_animation_image'], 'quizzy_reward_animation_image');
                if ($quizzy_reward_animation_image['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Home First Slide ' . $quizzy_reward_animation_image['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['quizzy_reward_animation_image'] = $quizzy_reward_animation_image['file_name'];
                }
            }

            if (isset($_FILES['mail_template_header_image']['name']) && $_FILES['mail_template_header_image']['name']) 
            {
                $status = NULL;
                $status = $this->do_upload($_FILES['mail_template_header_image'], 'mail_template_header_image');
                if ($status['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Header Image ' . $status['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['mail_template_header_image'] = $status['file_name'];
                }
            }

            if (isset($_FILES['mail_template_footer_image']['name']) && $_FILES['mail_template_footer_image']['name']) 
            {
                $status = NULL;
                $status = $this->do_upload($_FILES['mail_template_footer_image'], 'mail_template_footer_image');
                if ($status['status'] == 'error') {
                    $this->session->set_flashdata('error', 'Footer Image ' . $status['response']);
                    return redirect(base_url('admin/settings'));
                } else {
                    $form_data['mail_template_footer_image'] = $status['file_name'];
                }
            }
            // save the settings

            if(isset($form_data['header_javascript']) && $form_data['header_javascript'])
            {
                $form_data['header_javascript'] = html_entity_decode($form_data['header_javascript']);
            }

            if(isset($form_data['footer_javascript']) && $form_data['footer_javascript'])
            {
                $form_data['footer_javascript'] = html_entity_decode($form_data['footer_javascript']);
            }
            
            if(isset($form_data['custom_css']) && $form_data['custom_css'])
            {
                $form_data['custom_css'] = html_entity_decode($form_data['custom_css']);
            }
            $last_site_update_token = (isset($settings_name_array['site_update_token']) && $settings_name_array['site_update_token']) ? $settings_name_array['site_update_token'] : NULL;
            $curent_site_update_token = (isset($form_data['site_update_token']) && $form_data['site_update_token']) ? $form_data['site_update_token'] : NULL;

            

            $this->SettingsModel->save_site_settings($curent_site_update_token,$last_site_update_token);
            
            $saved = $this->SettingsModel->save_settings($form_data, $user['id']);
            if ($saved) {

                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));
                // reload the new settings
                $settings = $this->SettingsModel->get_settings();
                foreach ($settings as $setting) {
                    $this->settings->{$setting['name']} = @json_decode($setting['value']);
                }
            } else {
                $this->session->set_flashdata('error', lang('admin_error_adding_record'));
            }
            // reload the page
            redirect('admin/settings');
        }
        else
        {
            if($this->input->post())
            {
                $this->session->set_flashdata('error',lang('please_fill_all_required_field'));
            }
        }
        // setup page header data
        
        $this->add_js_theme('settings_i18n.js', TRUE);
        $this->set_title(lang('admin_settings'));
        $data = $this->includes;
        // set content data
        $content_data = array('settings' => $settings, 'cancel_url' => "/admin",);
        // load views
        $data['content'] = $this->load->view('admin/settings/form', $content_data, TRUE); 
        $this->load->view($this->template, $data);
    }
    public function do_upload($file_data, $file_name) {
        $config['upload_path'] = "./assets/images/logo/";
        $config['allowed_types'] = 'gif|jpg|png|ico';
        $config['overwrite'] = TRUE;
        $new_name = time() . $file_data['name'];
        $config['file_name'] = $new_name;

        if (!is_dir($config['upload_path'])) 
        {
            mkdir($config['upload_path'], 0775, TRUE);
        }

        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($file_name)) {
            $response['response'] = $this->upload->display_errors();
            $response['status'] = 'error';
            return $response;
        } else {
            $response['success'] = $this->upload->data();
            $response['status'] = 'success';
            $response['file_name'] = $this->upload->data('file_name');
            return $response;
        }
    }



    function test_mail()
    {
        $user = $this->session->userdata('logged_in');
        $email = isset($user['email']) ? $user['email'] : $this->settings->site_email;

        $this->load->library('SendMail');
        // $email_msg = 'email with template header and footer';
        // $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => 'email template of quiz'),true);
        // $status = $this->sendmail->sendTo('test@gmail.com', '"Atn Technology Test"', 'ATN Technology', $mail_html);
        $status = $this->sendmail->sendTo($email, "Atn Technology Test", 'ATN Technology', 'test email');
         if($status)
         {
            $this->session->set_flashdata('message', "Mail Send Success");
         }
         else
         {
            $this->session->set_flashdata('error', "Mail Send Error");
         }
         return redirect(base_url('admin/settings'));
    }


    function menu_item()
    {
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('bootstrap-notify.min.js');      
        $this->add_js_theme('jquery-ui.min.js');
        $this->add_js_theme('settings_i18n.js');
        $this->set_title($this->settings->site_name." - ".lang('Arrange Menu Items'));
        $data = $this->includes;
        $content_data = array();
        // load views
        $content_data['menu_items'] = $this->db->where('status',1)->order_by('order')->get('front_menu_items')->result();

        $data['content'] = $this->load->view('admin/settings/menu_item', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function menu_list()
    {
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('bootstrap-notify.min.js');      
        $this->add_js_theme('jquery-ui.min.js');
        $this->add_js_theme('settings_i18n.js');
        $content_data = array();
        $content_data['menu_items'] = $this->db->order_by('order')->get('front_menu_items')->result();

        $this->set_title($this->settings->site_name." - ".lang('Menu Items'));
        $data = $this->includes;
        // load views
        $data['content'] = $this->load->view('admin/settings/menu_item_list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }





    function change_menu_position()
    {   
        $position_array = $this->input->post('position');
        $response['status'] = "error";
        $response['message'] = "Invalid Try";
        
        if(count($position_array))
        {
            foreach ($position_array as $items) 
            {
               $this->db->where('id',$items[1])->update('front_menu_items',array('order'=>$items[2]));
            }

            $response['status'] = "success";
            $response['message'] = "Menu Order Change Successfully !";
        }
        
        echo json_encode($response);
        return json_encode($response);
        exit;
    }

    function update_menu_status($id)
    {   
        $menu_id = $this->input->post('menu_id');
        $status = $this->input->post('status');
        $status = $status == 1 ? 1 : 0;
        $response['status'] = "error";
        $response['message'] = "Invalid Try";
        
        if($menu_id)
        {
            $this->db->where('id',$menu_id)->update('front_menu_items',array('status'=>$status));
            $response['status'] = "success";
            $response['message'] = "Menu Status Change Successfully !";
        }
        
        echo json_encode($response);
        return json_encode($response);
        exit;
    }

}
