<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <?php
        $data['study_data']             = $study_data;
        $data['tab_study_material_id']  = $post_s_m_id;
        $data['contant_type']           = '';
        $this->load->view('admin/study/common_tab_list',$data);
      ?>
    </div>
    <hr>  

      <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
      <div class="row">

        <div class="col-6">
          <div class="form-group <?php echo form_error('s_m_id') ? ' has-error' : ''; ?>">
            <?php echo  form_label(lang('study_material'), 's_m_id'); ?>

            <span class="required">*</span>

            <?php 
            $populateData = $this->input->post('s_m_id') ? $this->input->post('s_m_id') : (isset($question_data['s_m_id']) ? $question_data['s_m_id'] :  '' ); 
            $populateData = $post_s_m_id ? $post_s_m_id : $populateData;

            echo form_dropdown('s_m_id', $all_study_material_name, $populateData, 'id="s_m_id" class="form-control select_dropdown"'); 
            ?> 
            <span class="small form-error"> <?php echo strip_tags(form_error('s_m_id')); ?> </span>  

          </div>
        </div>

        <!-- <div class="col-6">
          <div class="form-group <?php //echo form_error('s_m_section_id') ? ' has-error' : ''; ?>">
            <?php //echo  form_label(lang('study_material_section'), 's_m_section_id'); ?>

            <span class="required">*</span>

            <?php 
            //$populateData = $this->input->post('s_m_section_id') ? $this->input->post('s_m_section_id') : (isset($question_data['s_m_section_id']) ? $question_data['s_m_section_id'] :  '' ); 

            //echo form_dropdown('s_m_section_id', $all_study_section_name, $populateData, 'id="s_m_section_id" class="form-control select_dropdown"'); 
            ?> 
            <span class="small form-error"> <?php //echo strip_tags(form_error('s_m_section_id')); ?> </span>  

          </div>
        </div>

        <div class="col-6">
          <div class="form-group <?php //echo form_error('s_m_contant_type') ? ' has-error' : ''; ?>">
            <?php //echo  form_label(lang('study_material_contant_type'), 's_m_contant_type'); ?>
            <span class="required">*</span>
            <?php 
            // $populateData = $this->input->post('s_m_contant_type') ? $this->input->post('s_m_contant_type') : (isset($question_data['s_m_contant_type']) ? $question_data['s_m_contant_type'] :  '' ); 

            //echo form_dropdown('s_m_contant_type',$this->contant_types, $populateData, 'id="s_m_contant_type" class="form-control select_dropdown"'); 
            ?> 
            <span class="small form-error"> <?php //echo strip_tags(form_error('s_m_contant_type')); ?> </span>  

          </div>
        </div> -->

        <div class="col-6">
          <div class="form-group">
            <?php echo  form_label( lang('upload_excel_file'), 'excel_file'); ?> <span class="required text-danger">*</span>
            <input type="File" name="excel_file" class="form-control excel_file" id="excel_file">
            <span class="small text-danger form-error"> <?php echo strip_tags(form_error('excel_file')); ?> </span>  
          </div>
        </div>

        <?php 
        // $populateData = $this->input->post('over_write') == 1 ? 'checked' : (isset($quiz_data['over_write']) && $quiz_data['over_write'] == 1 ? 'checked' :  '' );
        ?>

        <!-- <div class="col-3">
          <div class="form-group togle_button">
            <?php //echo  form_label(lang('quiz_excel_question_over_write'), 'over_write'); ?>
            <label class="custom-switch form-control">
              <input type="checkbox" name="over_write" value="1" <?php //echo xss_clean($populateData); ?> class="custom-switch-input over_write"  data-size="sm">
              <span class="custom-switch-indicator"></span>
            </label>
          </div>
        </div> -->

        
        <div class="col-12 my-2">
          <a target="_blank" href="<?php echo base_url('assets/import-demo/study-data-demo.xlsx'); ?>"><?php echo lang('downlod_sample_file');?>  </a>
        </div>

        <div class="clearfix"></div>

        <div class="col-12 mt-5 text-right">
          <input type="Submit" class="btn btn-success mr-5" value="<?php echo lang('import');?>">
        </div>
      </div>
      <?php echo form_close();?>
  </div>
</div>