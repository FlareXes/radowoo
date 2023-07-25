<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Advertisment extends Admin_Controller 
{
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');
        $this->add_js_theme('addon_add.js');

        $this->load->model('AdvertismentModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('Resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/advertisment'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");

        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }
    }
    function index() 
    {
        
        $this->set_title(lang('advertisment_list'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/advertisment/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add() 
    {
        $is_goole_adsense = $this->input->post('is_goole_adsense',TRUE)  == 1 ? 1 : 0;

        $this->form_validation->set_rules('title', lang('title'), 'required|trim');
        $this->form_validation->set_rules('position', lang('position'), 'required|trim');
        $this->form_validation->set_rules('status', lang('status'), 'required|trim');



        $uploaded_image = NULL; 
        $uploaded_file_link = NULL;

        if($is_goole_adsense == 0)
        {
            $this->form_validation->set_rules('url', lang('link'), 'required|trim');
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
            {   
                $path = "./assets/images/advertisment";
                $allowed_formate = 'gif|jpg|png|bmp|jpeg';
                $response_file_upload = $this->do_upload_file('image',$path,$allowed_formate);
                if($response_file_upload['status'] == 'success')
                {
                    $uploaded_image = $response_file_upload['upload_data']['file_name'];
                    $uploaded_file_link = $path.$uploaded_image;
                }
                else
                {
                    $this->session->set_flashdata('error', $response_file_upload['error']); 
                    $this->form_validation->set_rules('image_upload_error', 'Image', 'required');
                }
            }
            else
            { 
                $this->form_validation->set_rules('image_upload_error', 'Image', 'required');
            }
        }
        else
        {
            $this->form_validation->set_rules('google_ad_code', lang('google_ad_code'), 'required|trim');
        }

        if ($this->form_validation->run() == false) 
        {
            $errors = $this->form_validation->error_array();
            if($uploaded_image && file_exists($uploaded_file_link))
            {
                unlink($uploaded_file_link);
            }
        } 
        else 
        {
            $google_ad_code = isset($_POST['google_ad_code']) ? $_POST['google_ad_code'] : "";
            $url = $this->input->post('url',TRUE) ? $this->input->post('url',TRUE) : "";
            $ad_order = $this->input->post('ad_order',TRUE) > 0 ? $this->input->post('ad_order',TRUE) : 0;
            action_not_permitted();
            $advertisment_content = array();
            $advertisment_content['title'] = $this->input->post('title',TRUE);
            $advertisment_content['url'] = $url;
            $advertisment_content['status'] = $this->input->post('status',TRUE);
            $advertisment_content['position'] = $this->input->post('position',TRUE);

            $advertisment_content['is_goole_adsense'] = $is_goole_adsense;
            $advertisment_content['google_ad_code'] = $google_ad_code;
            $advertisment_content['ad_order'] = $ad_order;

            $advertisment_content['image'] = $uploaded_image;
            $advertisment_content['added'] =  date('Y-m-d H:i:s');

            $advertisment_id = $this->AdvertismentModel->insert_advertisment($advertisment_content);

            if($advertisment_id)
            {                
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));                  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
            redirect(base_url('admin/advertisment'));
        }

        $position_array = array();
        $position_array['common_under_menu'] = lang('common_under_menu');
        $position_array['common_before_footer'] = lang('common_before_footer');
        $position_array['home_page_below_category'] = lang('home_page_below_category');
        $position_array['home_page_below_testimonials'] = lang('home_page_below_testimonials');
        $position_array['blog_list_page_below_sidebar'] = lang('blog_list_page_below_sidebar');
        $position_array['blog_detail_page_below_sidebar'] = lang('blog_detail_page_below_sidebar');


        $this->set_title(lang('advertisment_add'));
        $data = $this->includes;

        $content_data = array();
        $content_data['position_array'] = $position_array;
        // load views
        $data['content'] = $this->load->view('admin/advertisment/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($advertisment_id = NULL) 
    {
        if(empty($advertisment_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/advertisment'));
        }

        $advertisment_data = $this->AdvertismentModel->get_advertisment_by_id($advertisment_id);

        if(empty($advertisment_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/advertisment'));
        }

        $is_goole_adsense = $this->input->post('is_goole_adsense',TRUE)  == 1 ? 1 : 0;

       	$this->form_validation->set_rules('title', lang('title'), 'required|trim');
        $this->form_validation->set_rules('position', lang('position'), 'required|trim');
        $this->form_validation->set_rules('status', lang('status'), 'required|trim');
        
        $uploaded_image = NULL; 
        $uploaded_file_link = NULL;


        if($is_goole_adsense == 0)
        {

            $this->form_validation->set_rules('url', lang('link'), 'required|trim');

            if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
            {   
                $path = "./assets/images/advertisment";
                $allowed_formate = 'gif|jpg|png|bmp|jpeg';
                $response_file_upload = $this->do_upload_file('image',$path,$allowed_formate);
                if($response_file_upload['status'] == 'success')
                {
                    $uploaded_image = $response_file_upload['upload_data']['file_name'];
                    $uploaded_file_link = $path.$uploaded_image;
                }
                else
                {
                    $this->session->set_flashdata('error', $response_file_upload['error']); 
                    $this->form_validation->set_rules('image_upload_error', 'Image', 'required');
                }
            }
            else if(empty($advertisment_data->image))
            { 
                $this->form_validation->set_rules('image_upload_error', 'Image', 'required');
            }
        }
        else
        {
            $this->form_validation->set_rules('google_ad_code', lang('google_ad_code'), 'required|trim');
        }

        if ($this->form_validation->run() == false) 
        {
            if($uploaded_image && file_exists($uploaded_file_link))
            {
                unlink($question_file_link);
            }

            $this->form_validation->error_array();
        }
        else 
        {

            $google_ad_code = isset($_POST['google_ad_code']) ? $_POST['google_ad_code'] : "";
            $url = $this->input->post('url',TRUE) ? $this->input->post('url',TRUE) : "";
            $ad_order = $this->input->post('ad_order',TRUE) > 0 ? $this->input->post('ad_order',TRUE) : 0;

            action_not_permitted();
            $advertisment_content = array();
            $advertisment_content['title'] = $this->input->post('title',TRUE);
            $advertisment_content['url'] = $url;
            $advertisment_content['status'] = $this->input->post('status',TRUE);
            $advertisment_content['position'] = $this->input->post('position',TRUE);

            $advertisment_content['is_goole_adsense'] = $is_goole_adsense;
            $advertisment_content['google_ad_code'] = $google_ad_code;
            $advertisment_content['ad_order'] = $ad_order;

            $advertisment_content['updated'] =  date('Y-m-d H:i:s');

            if($uploaded_image)
            {                
                $advertisment_content['image'] = $uploaded_image;
            }

            $page_update_status = $this->AdvertismentModel->update_advertisment($advertisment_id, $advertisment_content);

            if($page_update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/advertisment'));
        }

        
        $this->set_title(lang('advertisment_update'));
        $data = $this->includes;

        $position_array = array();
        $position_array['common_under_menu'] = lang('common_under_menu');
        $position_array['common_before_footer'] = lang('common_before_footer');
        $position_array['home_page_below_category'] = lang('home_page_below_category');
        $position_array['home_page_below_testimonials'] = lang('home_page_below_testimonials');
        $position_array['blog_list_page_below_sidebar'] = lang('blog_list_page_below_sidebar');
        $position_array['blog_detail_page_below_sidebar'] = lang('blog_detail_page_below_sidebar');


        $content_data = array('advertisment_id' => $advertisment_id, 'advertisment_data' => $advertisment_data,'position_array' => $position_array);
        // load views
        $data['content'] = $this->load->view('admin/advertisment/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function delete($advertisment_id = NULL)
    {
        action_not_permitted();
        $status = $this->AdvertismentModel->delete_advertisment($advertisment_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/advertisment'));
    }

    function list() 
    {
        $data = array();
        $path = base_url('/assets/images/advertisment/');
        $list = $this->AdvertismentModel->get_advertisment();

        $no = $_POST['start'];
        foreach ($list as $advertisment_data) 
        {
            $advertisment_logo = $advertisment_data->image ? $path.$advertisment_data->image : 'assets/default/default.png';
            $ads_url = $advertisment_data->url ? $advertisment_data->url : "#";
            $ads_link = "<a target='_blank' href='".$ads_url."' class='btn btn-link'><i class='fas fa-link'></i></a>";
            
            $ads_status = $advertisment_data->status == 1 ? "<span class='rounded badge badge-success'> Active </span>" : "<span class='rounded badge badge-danger'> Disable </span>";


            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($advertisment_data->title);
            $row[] = $ads_link;
            
            if($advertisment_data->is_goole_adsense == 0)
            {
                $row[] = '<img class="testimonial_list_profile" src="'.$advertisment_logo.'">';
            }
            else
            {
                $row[] = '<i class="fab fa-google text-info "></i>';
            }

            $row[] = ucfirst(lang($advertisment_data->position));
            $row[] = $ads_status;

            $button = '<a href="' . base_url("admin/advertisment/update/". $advertisment_data->id) . '" data-toggle="tooltip"  title="'.lang('advertisment_update').'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';

            $button.= '<a href="' . base_url("admin/advertisment/delete/" . $advertisment_data->id) . '" data-toggle="tooltip"  title="'.lang('advertisment_delete').'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->AdvertismentModel->count_all(), "recordsFiltered" => $this->AdvertismentModel->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }


    public function do_upload_file($filename, $path, $allowed_formate)
    {
        if (!is_dir($path)) 
        {
            mkdir($path, 0777, TRUE);
        }
             
        $new_name = time().$_FILES[$filename]['name'];
        $config['upload_path']          = $path;
        $config['allowed_types']        = $allowed_formate;
        $config['max_size']             = 50000000;
        $config['max_width']            = 400000;
        $config['max_height']           = 300000;
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($filename))
        {
            $respons = array(   'status' => 'error','error' => $this->upload->display_errors());
        }
        else
        {
            $respons = array('status' => 'success','upload_data' => $this->upload->data());
        }
        return $respons;
    }
}
