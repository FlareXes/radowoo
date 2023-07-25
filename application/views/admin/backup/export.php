<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row page">

  <div class="col-12">
       <div class="card">
        <?php $this->load->view('admin/backup/tab_list',array()); ?>
      </div>
  </div>

  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body">
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?> 
        <div class="row">
          <input type="hidden" value="<?php echo $type; ?>" class="data_for" id="data_for">
          <div class="col-12">
            <div class="form-group <?php echo form_error('category_id') ? ' has-error' : ''; ?>">
              <?php echo form_label(lang('quiz_category_name'), 'category_id'); ?>

              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('category_id') ? $this->input->post('category_id') : ($category_id ? $category_id :  '' );                     
                echo form_dropdown('category_id', $category_data, $populateData, 'id="category_id" class="form-control select_dropdown"'); 
              ?> 
              <span class="small form-error"> <?php echo strip_tags(form_error('category_id')); ?> </span>  
            </div>
          </div>

          <div class="clearfix"> </div>
          
          <div class="col-12">
            <div class="form-group <?php echo form_error('name') ? ' has-error' : ''; ?>">
              <?php 
              if($type == 'quiz')
              {
                if($quiz_array)
                {
                  ?>
                  <div class="label-group">
                    <label class="h6 mb-5"> <?php echo lang('Record For ') ?> 
                      <span class="text-success"> <?php echo ucwords($category_detail->category_title); ?> </span>
                    </label>
                  </div>
                  <?php
                  foreach ($quiz_array as  $quiz_data) 
                  { 
                    ?>
                    <div class="custom-control custom-checkbox custom-control-inline">
                      <input type="checkbox" class="custom-control-input" name="quiz[]" id="quiz_id_<?php echo $quiz_data->id; ?>" value="<?php echo $quiz_data->id;?>">
                      <label class="custom-control-label" for="quiz_id_<?php echo $quiz_data->id; ?>"><?php echo $quiz_data->title; ?></label>
                    </div>                    
                    <?php
                  }
                }
                else
                {
                  if($category_detail)
                  {
                    ?>
                    <label class="text-center text-danger">No Quiz Added For <span class="text-info"> <?php echo ucwords($category_detail->category_title); ?>  </span>...!</label>
                    <?php
                  }
                }
              }

              if ($type == 'study-material') 
              {
                if($study_material_array)
                {
                  ?>
                  <div class="label-group">
                    <label class="h6 mb-5"> <?php echo lang('Record For ') ?> 
                      <span class="text-success"> <?php echo ucwords($category_detail->category_title); ?> </span>
                    </label>
                  </div>
                  <?php
                  foreach ($study_material_array as  $study_material_data) 
                  { 
                    ?>
                    <div class="custom-control custom-checkbox custom-control-inline">
                      <input type="checkbox" class="custom-control-input" name="study_material[]" id="study_material_id_<?php echo $study_material_data->id; ?>" value="<?php echo $study_material_data->id;?>">
                      <label class="custom-control-label" for="study_material_id_<?php echo $study_material_data->id; ?>"><?php echo $study_material_data->title; ?></label>
                    </div>                    
                    <?php
                  }
                }
                else
                {
                  if($category_detail)
                  {
                    ?>
                    <label class="text-center text-danger">No Study Material Added For <span class="text-info"> <?php echo ucwords($category_detail->category_title); ?>  </span>...!</label>
                    <?php
                  }
                }
              }


              ?>
              <div class="w-100 mt-3">
                <span class="small form-error text-danger "> <?php echo strip_tags(form_error('quiz')); ?> </span>
              </div>
              
            </div>
          </div>



          <div class="clearfix"></div>

          <hr />

          <div class="col-12">
            
              <input type="submit" name="submit" value="Export <?php echo ucwords($type); ?>" class="btn btn-primary px-5 float-right">
            
          </div>
          <div class="clearfix"></div>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>
