<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Coupon extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('jquery.multi-select.min.js');
        $this->add_js_theme('coupon.js');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');

        // load the language files 
        // load the category model
        $this->load->model('CouponModel');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/coupon'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0); 
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");

        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }

    }

    function index() { 
        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('coupon_list'));
        
        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/coupon/coupon_list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add()
    {
        
        $this->form_validation->set_rules('coupon_for', lang('coupon_for'), 'required');
        $this->form_validation->set_rules('coupon_code', lang('coupon_code'), 'required|trim|is_unique[coupon.coupon_code]');
        $this->form_validation->set_rules('discount_type', lang('discount_type'), 'required');
        $this->form_validation->set_rules('no_time_used', lang('no_time_used'), 'required');
        $this->form_validation->set_rules('discount_value', lang('discount_value'), 'required');
        $this->form_validation->set_rules('expiry_date', lang('expiry_date'), 'required');

        if($this->input->post('coupon_for') != 'all')
        {
            $this->form_validation->set_rules('reference_data[]', lang('reference_data'), 'required');
        }
            
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $coupon_content = array();
            
            $coupon_content['coupon_for'] = $this->input->post('coupon_for',TRUE);
            $coupon_content['coupon_code'] = strtolower($this->input->post('coupon_code',TRUE));
            $coupon_content['description'] = $this->input->post('description',TRUE);
            $coupon_content['coupon_discount_type'] = $this->input->post('discount_type',TRUE);
            $coupon_content['discount_value'] = $this->input->post('discount_value',TRUE);
            $coupon_content['no_of_times_can_be_used'] = $this->input->post('no_time_used',TRUE);
            $coupon_content['expiry_date'] = $this->input->post('expiry_date',TRUE);
            $status = $this->input->post('status',TRUE) ? $this->input->post('status',TRUE) : 'Active';
            $coupon_content['status'] = $status;
            $coupon_content['created_at'] = date('Y-m-d H:i:s');
            $coupon_content['updated_at'] = date('Y-m-d H:i:s');
            $coupon_reference_data = $this->input->post('reference_data');
            if($this->input->post('coupon_for') == 'all')
            {
                $coupon_content['is_all'] = 'All';
            }
            
            $coupon_id = $this->CouponModel->insert_coupon($coupon_content);

            if($coupon_id && $this->input->post('coupon_for') != 'all')
            {
                $coupon_reference_data = $this->input->post('reference_data');
                if($coupon_reference_data)
                {
                    foreach($coupon_reference_data as $c_r_key => $c_r_value)
                    {
                        
                        if($c_r_value != 'All')
                        {
                            $coupon_related_ids = array();
                            $coupon_related_ids['coupon_id'] = $coupon_id;
                            $coupon_related_ids['relation_data_id'] = $c_r_value;
                            $coupon_related_status = $this->CouponModel->insert_coupon_related_data($coupon_related_ids);
                        }
                        else
                        {
                            $check_coupon_data_for_all = $this->CouponModel->check_coupon_data($coupon_id);
                            if($check_coupon_data_for_all)
                            {
                                $delete_related_coupon_data = $this->CouponModel->delete_related($coupon_id);
                            }
                            $coupon_all_array['is_all'] = $c_r_value;

                            $coupon_update = $this->CouponModel->update_coupon_related($coupon_id,$coupon_all_array);
                            break;
                        }
                    }
                }
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
            }
            elseif($coupon_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }

            redirect(base_url('admin/coupon'));
        }
        $this->set_title(lang('add_coupon'));
        $data = $this->includes;

        $content_data = array();
        // load views
        // $data['content'] = $this->load->view('admin/coupon/select2', $content_data);
        $data['content'] = $this->load->view('admin/coupon/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function get_coupon_for()
    {
        $response = array();
        $response['status'] = '';
        $response['message'] = '';
        $response['data'] = '';
        $coupon_for = $this->input->post('coupon_for');
        $search_text = $this->input->post('search_text');

        if($coupon_for)
        {   
            $all_obj = new stdClass;
            $all_obj->id = "All";
            $all_obj->title = "All";
            $get_coupon_wise_data = $this->CouponModel->get_coupon_for($coupon_for,$search_text);
            
            if($get_coupon_wise_data)
            {
               array_push($get_coupon_wise_data, $all_obj);  
            }
            else
            {
                $get_coupon_wise_data[] = $all_obj;  
            }
 
            $response['status'] = 'success'; 
            $response['data'] = $get_coupon_wise_data;  
        }

        echo json_encode($response);
    }

    function coupon_list() {
      
        $list = $this->CouponModel->get_coupon();
       
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $coupon) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $coupon->coupon_code;
            $row[] = ucfirst($coupon->coupon_for);
            $row[] = ucfirst($coupon->coupon_discount_type);
            $row[] = date('d-m-Y',strtotime($coupon->expiry_date));
            $row[] = ucfirst($coupon->status);

            $row[] = '<a href="' . base_url("admin/coupon/update/" . $coupon->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>

            <a href="' . base_url("admin/coupon/track_coupon/" . $coupon->id) . '" data-toggle="tooltip"  title="'.lang("admin_track_coupon").'" class="btn btn-warning btn-action mr-1 blog_cat_delete"><i class="fas fa-list"></i></a>

            <a href="' . base_url("admin/coupon/delete/" . $coupon->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 blog_cat_delete"><i class="fas fa-trash"></i></a>';
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->CouponModel->count_all(), "recordsFiltered" => $this->CouponModel->count_filtered(), "data" => $data);
        //output to json format
        echo json_encode($output);
    }

    function update($coupon_id = NULL)
    {
        if(empty($coupon_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('admin/coupon'));
        }

        $coupon_data = $this->CouponModel->get_coupon_by_id($coupon_id);
        
        if(empty($coupon_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('admin/coupon'));
        }
        $coupon_data = json_decode(json_encode($coupon_data),TRUE);
        $coupon_related_data = '';
        if($coupon_data['coupon_for'] != 'all')
        {
            $coupon_related_data = $this->CouponModel->get_coupon_related_data($coupon_id,$coupon_data['coupon_for']);
            $coupon_related_data = json_decode(json_encode($coupon_related_data),TRUE);
            $coupon_related_data = array_column($coupon_related_data,'title');
            if($coupon_related_data)
            {
                $coupon_related_data = $coupon_related_data;
            }
            else
            {
                $coupon_related_data = array('ALL');
            }
        }
        

        
        $title_unique = $this->input->post('coupon_code')  != $coupon_data['coupon_code'] ? '|is_unique[coupon.coupon_code]' : '';
        
        $this->form_validation->set_rules('coupon_code', lang('coupon_code'), 'required|trim'.$title_unique);
        $this->form_validation->set_rules('discount_type', lang('discount_type'), 'required');
        $this->form_validation->set_rules('no_time_used', lang('no_time_used'), 'required');
        $this->form_validation->set_rules('discount_value', lang('discount_value'), 'required');
        $this->form_validation->set_rules('expiry_date', lang('expiry_date'), 'required');

        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $coupon_content = array();
            $coupon_content['coupon_code'] = strtolower($this->input->post('coupon_code',TRUE));
            $coupon_content['description'] = $this->input->post('description',TRUE);
            $coupon_content['coupon_discount_type'] = $this->input->post('discount_type',TRUE);
            $coupon_content['discount_value'] = $this->input->post('discount_value',TRUE);
            $coupon_content['no_of_times_can_be_used'] = $this->input->post('no_time_used',TRUE);
            $coupon_content['expiry_date'] = $this->input->post('expiry_date',TRUE);
            $status = $this->input->post('status',TRUE) ? $this->input->post('status',TRUE) : 'Active';
            $coupon_content['status'] = $status;
            $coupon_content['updated_at'] = date('Y-m-d H:i:s');
            
            $update_status = $this->CouponModel->update_coupon($coupon_content,$coupon_id);

            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/coupon'));
        }

        $this->set_title(lang('edit_coupon'));
        $data = $this->includes;

        $content_data = array('coupon_data'=>$coupon_data,'coupon_id' => $coupon_id,'coupon_related_data'=>$coupon_related_data);
        // load views
        $data['content'] = $this->load->view('admin/coupon/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function delete($coupon_id = NULL)
    {
        action_not_permitted();
        $status = $this->CouponModel->delete_coupon($coupon_id); 

        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/coupon'));
    }

    function track_coupon($id = NULL)
    {
        $coupon_data = $this->CouponModel->get_coupon_by_id($id);
        
        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('coupon_track_list').' (Coupon Code: '.$coupon_data->coupon_code.') And (How many times used: '.$coupon_data->total_used_coupon.')');
        
        $data = $this->includes;
        $content_data = array('coupon_id' => $id,);
        // load views
        $data['content'] = $this->load->view('admin/coupon/coupon_track_list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function coupon_track_list($coupon_id)
    {
        $list = $this->CouponModel->get_coupon_user_wise_data($coupon_id);
       
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $track_list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $track_list->first_name.' '.$track_list->last_name;
            $row[] = ucfirst($track_list->item_name);
            $row[] = $track_list->discount_value;
            $row[] = ucfirst($track_list->no_of_use);
            $data[] = $row;
        }

        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->CouponModel->count_all_track($coupon_id), "recordsFiltered" => $this->CouponModel->count_filtered_track($coupon_id), "data" => $data);
        //output to json format
        echo json_encode($output);
    }
    
}