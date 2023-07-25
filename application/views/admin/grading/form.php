<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($quiz_id) && $quiz_id ? '_update' : '' ?>  
<div class="row"> 
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body">
          
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
        <div class="row mt-3">

          <div class="col-12">
            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_title'), 'title'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($grading_data['title']) ? $grading_data['title'] :  '' );
              ?>
              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>


          <?php 
          $first_row_name = isset($level_names_array[1]) ? $level_names_array[1] : "NO-LEVEL";
          $first_row_marks = isset($level_marks_array[1]) ?  $level_marks_array[1] : NULL;
          $no_of_level_row = $no_of_level_row > 1 ? $no_of_level_row : 1;
          ?>


          <div class="col-12 section_quiz_levels ">
            
            <div class="row parent_section_of_level_row">
              
              <div class="col-5">
                
                <div class="form-group <?php echo form_error('quiz_level_name[]') && empty($first_row_name) ? ' has-error' : ''; ?> choice_block">
                  <label class="control-label"> <?php echo lang('Quiz Level Name'); ?>  <span class="text-danger">*</span></label>
                  <input type="text" name="quiz_level_name[1]" class="form-control" value="<?php echo xss_clean($first_row_name); ?>">
                  <span class="small form-error"> <?php echo xss_clean($first_row_name) ? '' : strip_tags(form_error("quiz_level_name[]")); ?> </span>
                </div>

              </div>

              <div class="col-5">
                
                <div class="form-group <?php echo form_error('quiz_level_marks[]') && empty($first_row_marks) ? ' has-error' : ''; ?> choice_block">
                  <label class="control-label"> <?php echo lang('Quiz Level Minimum Percentage'); ?>  <span class="text-danger">*</span></label>
                  <input readonly type="number" name="quiz_level_marks[1]" class="form-control" value="0<?php //echo xss_clean($first_row_marks); ?>">
                  <span class="small form-error"> <?php echo xss_clean($first_row_marks) ? '' : strip_tags(form_error("quiz_level_marks[]")); ?> </span>
                </div>

              </div>

              <div class="col-md-2">
                <div class="form-group mt-4 pt-2">
                  <button class="btn btn-info  btn-block add_more_quiz_level" data-index="<?php echo $no_of_level_row; ?>" type="button">
                    <?php echo lang('admin_add_more'); ?></button>
                  </div>
              </div>

            </div>
            <?php

            if($level_names_array)
            {
              unset($level_names_array[1]);
              unset($level_marks_array[1]);
              $index = 1;
              foreach ($level_names_array as $level_array_key => $level_names) 
              { 


                $level_marks = isset($level_marks_array[$level_array_key]) ? $level_marks_array[$level_array_key] : NULL;
                $index ++;
                ?>

                <div class="row added_parent_section_of_level_row popup_added_parent_section_of_level_row">

                    <div class="col-5">
                      
                      <div class="form-group <?php echo form_error('quiz_level_name[]') && empty($level_names) ? ' has-error' : ''; ?> choice_block">
                        <input type="text" name="quiz_level_name[<?php echo $index; ?>]" class="form-control" value="<?php echo xss_clean($level_names); ?>">
                        <span class="small form-error"> <?php echo xss_clean($level_names) ? '' : strip_tags(form_error("quiz_level_name[]")); ?> </span>
                      </div>

                    </div>

                    <div class="col-5">
                      
                      <div class="form-group <?php echo form_error('quiz_level_marks[]') && empty($level_marks) ? ' has-error' : ''; ?> choice_block">
                        <input type="number" name="quiz_level_marks[<?php echo $index; ?>]" class="form-control" value="<?php echo xss_clean($level_marks); ?>">
                        <span class="small form-error"> <?php echo xss_clean($level_marks) ? '' : strip_tags(form_error("quiz_level_marks[]")); ?> </span>
                      </div>

                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <button class="btn btn-danger  btn-block remove_quiz_leve_row_btn" type="button" data-index="<?php echo $index; ?>"><i class="fas fa-trash-alt"></i></button>
                      </div>
                    </div>

                </div>

                <?php
              }
            } ?>

          </div>




          <div class="clearfix"></div>
          <div class="col-12"><hr> </div>

          <div class="col-12 text-right">
            <?php $saveUpdate = isset($grading_id) ? lang('core_button_update') : lang('core_button_save'); ?>
            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
            <a href="<?php echo base_url('admin/quiz-grading');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
          </div>
          <div class="clearfix"></div>

        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>


<div class="w-100 section_quiz_levels_for_copy d-none">
  
  <div class="row added_parent_section_of_level_row">

      <div class="col-5">
        
        <div class="form-group">
          <input type="text" name="quiz_level_name[0]" class="form-control quiz_level_name">
        </div>

      </div>

      <div class="col-5">
        
        <div class="form-group">
          <input type="number" name="quiz_level_marks[0]" class="form-control quiz_level_marks">
        </div>

      </div>

      <div class="col-md-2">
        <div class="form-group">
          <button class="btn btn-danger  btn-block remove_quiz_leve_row_btn" type="button" data-index="<?php echo $level_array_key; ?>"><i class="fas fa-trash-alt"></i></button>
        </div>
      </div>

  </div>

</div>