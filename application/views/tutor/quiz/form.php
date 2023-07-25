<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($quiz_id) && $quiz_id ? '_update' : '' ?>  
<div class="row"> 
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body">
          
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
        <div class="row mt-3">

          <div class="col-6">
            <div class="form-group <?php echo form_error('category_id') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('quiz_category_name'), 'category_id'); ?>

              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('category_id') ? $this->input->post('category_id') : (isset($quiz_data['category_id']) ? $quiz_data['category_id'] :  '' );                     
                echo form_dropdown('category_id', $category_data, $populateData, 'id="category_id" class="form-control select_dropdown"'); 
              ?> 
              <span class="small form-error"> <?php echo strip_tags(form_error('category_id')); ?> </span>  
            </div>
          </div>



          <div class="col-6">
            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_title'), 'title'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($quiz_data['title']) ? $quiz_data['title'] :  '' );
              ?>
              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>

          <?php 
              $populateData = $this->input->post('is_quiz_active') ? $this->input->post('is_quiz_active') : 0;
              $is_quiz_active = $populateData == 1 ? 1 : 0;
              $is_quiz_active_checked = $is_quiz_active == 1 ? "checked" : "";
          ?>

          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('status'), 'is_quiz_active'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="is_quiz_active" value="1" <?php echo xss_clean($is_quiz_active_checked); ?> class="custom-switch-input is_quiz_active"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>


          <div class="col-2">
            <div class="form-group <?php echo form_error('number_questions') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('number_of_questions_require_for_quiz'), 'number_questions'); ?>
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('number_questions') ? $this->input->post('number_questions') : (isset($quiz_data['number_questions']) ? $quiz_data['number_questions'] :  '' );
              ?>
              <input type="number" name="number_questions" id="number_questions" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('number_questions')); ?> </span>
            </div>
          </div>



          <div class="col-2">
            <div class="form-group <?php echo form_error('duration_min') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('quiz_duration_in_minute'), 'duration_min'); ?>
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('duration_min') ? $this->input->post('duration_min') : (isset($quiz_data['duration_min']) ? $quiz_data['duration_min'] :  '' );
              ?>
              <input type="text" name="duration_min" id="duration_min" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('duration_min')); ?> </span>
              <span class="text-warning"><?php echo lang('enter_zero_for_unlitimited_time'); ?></span>
            </div>
          </div>


          <div class="col-3">
            <div class="form-group <?php echo form_error('quiz_grading_id') ? ' has-error' : ''; ?>">
              <?php echo  form_label('Quiz has Grading', 'quiz_grading_id'); ?>

              <span class="required">*</span>

              <?php 
                $populateData = $this->input->post('quiz_grading_id') ? $this->input->post('quiz_grading_id') : (isset($quiz_data['quiz_grading_id']) ? $quiz_data['quiz_grading_id'] :  '' ); 

              echo form_dropdown('quiz_grading_id', $all_quiz_gradings_data, $populateData, 'id="quiz_grading_id" class="form-control select_dropdown"'); 
              ?> 
              <span class="small form-error"> <?php echo strip_tags(form_error('quiz_grading_id')); ?> </span>  

            </div>
          </div>

          <?php 
              $populateData = $this->input->post('is_previous_disable') ? $this->input->post('is_previous_disable') : (isset($quiz_data['is_previous_disable']) ? $quiz_data['is_previous_disable'] : 0 );
              $is_previous_active = $populateData == 1 ? 1 : 0;
              $is_previous_active_checked = $is_previous_active == 1 ? "checked" : "";
          ?>
          
          <div class="col-3">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('is_previous'), 'is_previous_disable'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="is_previous_disable" value="1" <?php echo xss_clean($is_previous_active_checked); ?> class="custom-switch-input is_quiz_active"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
              <span class="text-warning w-100"><?php echo lang('you_can_disable_previous_button_on_test_page'); ?></span>
            </div>
          </div>


          <div class="clearfix"></div>

          <?php 
            $populateData = $this->input->post('leader_board') == 1 ? 'checked' : (isset($quiz_data['leader_board']) && $quiz_data['leader_board'] == 1 ? 'checked' :  '' );
          ?>

          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('display_on_leaderboard'), 'leader_board'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="leader_board" value="1" <?php echo xss_clean($populateData); ?> class="custom-switch-input leader_board"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>

          <?php 
            $populateData = $this->input->post('is_random') == 1 ? 'checked' : (isset($quiz_data['is_random']) && $quiz_data['is_random'] == 1 ? 'checked' :  '' );
          ?>

          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('question_is_random'), 'is_random'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="is_random" value="1" <?php echo xss_clean($populateData); ?> class="custom-switch-input is_random"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>

          <?php 
            $populateData = $this->input->post('is_random_option') == 1 ? 'checked' : (isset($quiz_data['is_random_option']) && $quiz_data['is_random_option'] == 1 ? 'checked' :  '' );
          ?>

          <div class="col-3">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('is_random_option'), 'is_random_option'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="is_random_option" value="1" <?php echo xss_clean($populateData); ?> class="custom-switch-input is_random_option"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>

          <?php 
            $populateData = $this->input->post('is_registered') == 1 ? 'checked' : (isset($quiz_data['is_registered']) && $quiz_data['is_registered'] == 1 ? 'checked' :  '' );
          ?>


          <input type="hidden" name="points_on_correct" value="0">
          <input type="hidden" name="bonus_points" value="0">
          <input type="hidden" name="difficulty_level" value="0">


              <div class="col-3">
                  <div class="form-group">
                    <?php echo  form_label(lang('require_quiz_passing')." %", 'passing_mark'); ?><span class="text-danger"> *</span>
                    <?php 
                    $populateData = $this->input->post('passing_mark')? $this->input->post('passing_mark') : (isset($quiz_data['passing']) ? $quiz_data['passing'] :  '' );
                  ?>

                    <select name="passing_mark" class="form-control select2 passing_mark" id="passing_mark">
                      <option value=""><?php echo lang('select_passing_mark'); ?> %</option>
                      <?php 
                      for($i=1; $i <= 100; $i++)
                      {
                        $selected = $populateData == $i ? "selected" : "";
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $i; ?>%</option>
                        <?php
                      }
                      ?>
                    </select>
                    <span class="small form-error w-100 text-danger"> <?php echo strip_tags(form_error('passing_mark')); ?> </span>

                  </div>
              </div>

              <?php 
                  $populateData = $this->input->post('is_result_disable') ? $this->input->post('is_result_disable') : 0;
                  $is_result_disable = $populateData == 1 ? 1 : 0;
                  $is_result_disable_checked = $is_result_disable == 1 ? "checked" : "";
              ?>   

              <div class="col-2">
              <div class="form-group togle_button">
                <?php echo  form_label(lang('is_disable_result'), 'is_result_disable'); ?>
                <label class="custom-switch form-control">
                  <input type="checkbox" name="is_result_disable" value="1" <?php echo xss_clean($is_result_disable_checked); ?> class="custom-switch-input is_quiz_active"  data-size="sm">
                  <span class="custom-switch-indicator"></span>
                </label>
              </div>
            </div>


              <div class="col-2">
                  <div class="form-group <?php echo form_error('reward_percentage_err') ? ' has-error' : ''; ?>">
                    <?php echo  form_label(lang('reward')." %", 'reward_percentage'); ?>
                    <?php 
                    $populateData = $this->input->post('reward_percentage')? $this->input->post('reward_percentage') : (isset($quiz_data['reward_percentage']) ? $quiz_data['reward_percentage'] :  '' );
                  ?>
                    
                    <select name="reward_percentage" class="form-control select2 reward_percentage" id="reward_percentage">
                      <option value="0"><?php echo lang('select_reward'); ?> %</option>
                      <?php 
                      for($i=1; $i <= 100; $i++)
                      {
                        $selected = $populateData == $i ? "selected" : "";
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $i; ?>%</option>
                        <?php
                      }
                      ?>
                    </select>

                    <span class="small form-error w-100"> <?php echo (form_error('reward_percentage_err')) ? lang('invalid_reward_percentage') : ''; ?> </span>

                  </div>
              </div>






              <div class="col-2">
                  <div class="form-group <?php echo form_error('negative_marking_percentage_err') ? ' has-error' : ''; ?>">
                    <?php echo  form_label(lang('negative_marking')." %", 'negative_marking_percentage'); ?>
                    <?php 
                    $populateData = $this->input->post('negative_marking_percentage')? $this->input->post('negative_marking_percentage') : (isset($quiz_data['negative_marking_percentage']) ? $quiz_data['negative_marking_percentage'] :  '' );
                  ?>

                    <select name="negative_marking_percentage" class="form-control select2 negative_marking_percentage" id="negative_marking_percentage">
                      <option value="0"><?php echo lang('select_negative_marking'); ?> %</option>
                      <?php 
                      for($i=1; $i <= 100; $i++)
                      {
                        $selected = $populateData == $i ? "selected" : "";
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $i; ?>%</option>
                        <?php
                      }
                      ?>
                    </select>
                    <div class="clearfix"></div>
                    <span class="small form-error w-100"> <?php echo (form_error('negative_marking_percentage_err')) ? lang('invalid_negative_marking_percentage') : ''; ?> </span>
                  </div>
              </div>


          <div class="col-2">
              <div class="form-group <?php echo form_error('marks_for_correct_answer_err') ? ' has-error' : ''; ?>">
                <?php echo  form_label(lang('marks_for_correct_answer'), 'marks_for_correct_answer'); ?>
                <?php 
                $populateData = $this->input->post('marks_for_correct_answer')? $this->input->post('marks_for_correct_answer') : (isset($quiz_data['marks_for_correct_answer']) ? $quiz_data['marks_for_correct_answer'] :  '' );
              ?>
                <input type="text" name="marks_for_correct_answer" min="0" max="100" class="form-control marks_for_correct_answer" value="<?php echo $populateData;?>">
                <span class="text-warning w-100"><?php echo lang('enter_no_limit');?></span>
                <div class="clearfix"></div>
                <span class="small form-error w-100"> <?php echo (form_error('marks_for_correct_answer_err')) ? lang('invalid_marks_for_correct_answer') : ''; ?> </span>
              </div>
          </div>


          <?php $populateData = $this->input->post('quiz_attempt') ? $this->input->post('quiz_attempt') :  0 ; ?>

          <div class="col-2">
            <div class="form-group togle_button attempt "> 
              <?php echo  form_label(lang('allow_number_of_attempt_on_quiz'), 'quiz_attempt'); ?>
                <input type="number" name="quiz_attempt" id="quiz_attempt" class="form-control attempt" value="<?php echo $populateData; ?>">
            </div>
          </div> 

          <div class="col-4">
            <div class="form-group">
              <?php echo  form_label(lang('quiz_helpsheet'), 'helpsheet'); ?>
              <input type="File" name="helpsheet" id="image" class="form-control">
                <span class="small form-error"> <?php echo strip_tags(form_error('image_upload_error')); ?> </span>
            </div>
          </div>


          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('is_premium'), 'is_premium'); ?>
              <label class="custom-switch form-control">
                <?php 
                    $populateData = $this->input->post('is_premium') == 1 ? 'checked'  :  '' ;
                    $price_d_none = $populateData == 'checked' ? 'd-none' : '';
                ?>
                <input type="checkbox" name="is_premium" value="1" <?php echo xss_clean($populateData); ?> class="custom-switch-input is_premium"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>



          <div class="col-2">
            <div class="form-group">
              <?php echo  form_label(lang('Evaluation Test')); ?>
              <?php $evelution_test_array = array('YES'=>lang('YES'),"NO" =>lang('NO'));
              $populateData = $this->input->post('evaluation_test') ? $this->input->post('evaluation_test') : 'NO';
              ?>
              <select name="evaluation_test" class="form-control">
                  <?php foreach($evelution_test_array as $key => $value) 
                  { 
                    $select_val = ($populateData == $value) ? 'selected' : ''; ?>
                      <option <?php echo $select_val;?> value="<?php echo $key;?>"><?php echo $value;?></option>
                    <?php 
                } ?>  
              </select>
            </div>
          </div>


           <div class="col-2">
              <div class="form-group <?php echo form_error('price') ? ' has-error' : ''; ?>">
                 <?php echo  form_label(lang('quiz_price'), 'price'); ?>
                 <span class="required">*</span>
                 <?php 
                 $populateData = $this->input->post('price') ? $this->input->post('price') : (isset($quiz_data['price']) ? $quiz_data['price'] :  '' );
                 ?>
                 <input type="number" name="price" id="price" class="form-control" value="<?php echo $populateData;?>">
                 <span class="small form-error"> <?php echo strip_tags(form_error('price')); ?> </span>
              </div>
           </div>


          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('allow_registered_user_only'), 'is_registered'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="is_registered" value="1" <?php echo xss_clean($populateData); ?> class="custom-switch-input is_registered"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>




          <?php 
              $populateData = $this->input->post('hide_correct_answer') ? $this->input->post('hide_correct_answer') : 0;
              $hide_correct_answer = $populateData == 1 ? 1 : 0;
              $hide_correct_answer_checked = $hide_correct_answer == 1 ? "checked" : "";
          ?>

          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('hide_answesr_on_result'), 'hide_correct_answer'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="hide_correct_answer" value="1" <?php echo xss_clean($hide_correct_answer_checked); ?> class="custom-switch-input hide_correct_answer"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>




          <?php 
              $populateData = $this->input->post('is_sheduled_test') ? $this->input->post('is_sheduled_test') : 0;
              $is_sheduled_test = $populateData == 1 ? 1 : 0;
              $is_sheduled_test_checked = $is_sheduled_test == 1 ? "checked" : "";
          ?>

          <div class="col-2">
            <div class="form-group togle_button">
              <?php echo  form_label(lang('is_sheduled_quiz'), 'is_sheduled_test'); ?>
              <label class="custom-switch form-control">
                <input type="checkbox" name="is_sheduled_test" value="1" <?php echo xss_clean($is_sheduled_test_checked); ?> class="custom-switch-input is_sheduled_test"  data-size="sm">
                <span class="custom-switch-indicator"></span>
              </label>
            </div>
          </div>

          <div class="clearfix"></div>
            <?php $is_sheduled_test_class = $is_sheduled_test == 1 ? "" : "d-none"; ?>
            <div class="col-12 is_sheduled_test_section <?php echo $is_sheduled_test_class; ?>">
              
              <div class="row">

                <?php  $populateData = date("Y-m-d H:i:s",$start_date_time);   ?>

                <div class="col-6">
                  <div class="form-group <?php echo form_error('start_date_time') ? ' has-error' : ''; ?>"> 
                    <?php echo  form_label(lang('Quiz Start Date Or Time'), 'start_date_time'); ?>
                      <input type="text" name="start_date_time" id="start_date_time" class="form-control start_date_time datetimepicker_custom" value="<?php echo $populateData; ?>">
                      <span class="small form-error"> <?php echo strip_tags(form_error('start_date_time')); ?> </span>
                  </div>
                </div> 


                <?php  $populateData = date("Y-m-d H:i:s",$end_date_time);   ?>

                <div class="col-6">
                  <div class="form-group <?php echo form_error('end_date_time') ? ' has-error' : ''; ?>"> 
                    <?php echo  form_label(lang('Quiz End Date Or Time'), 'end_date_time'); ?>
                      <input type="text" name="end_date_time" id="end_date_time" class="form-control end_date_time datetimepicker_custom" value="<?php echo $populateData; ?>">
                      <span class="small form-error"> <?php echo strip_tags(form_error('end_date_time')); ?> </span>
                  </div>
                </div> 
              </div>

            </div>



          <div class="clearfix"></div>

          <div class="col-12">
            <div class="form-group <?php echo form_error('description') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('description'), 'description'); ?>
              <span class="required">*</span>
              <?php
                $populateData = $this->input->post('description') ? $this->input->post('description') : (isset($quiz_data['description']) ? $quiz_data['description'] :  '' );
              ?>
              <textarea name="description" id="p_desc" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
              <span class="small form-error"> <?php echo strip_tags(form_error('description')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>

          <div class="col-12 mt-4">
            <div class="form-group <?php echo form_error('featured_image[]') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('quiz_upload_featured_images'), 'featured_image');  ?>
              <div class="dropzone clsbox form-control" id="imageupload">
              </div>
              <span class="small form-error"> <?php echo strip_tags(form_error('featured_image[]')); ?> </span>

              <?php 
                if(isset($quiz_data['featured_image']) && $quiz_data['featured_image'])
                {
                  $featured_image_array = json_decode($quiz_data['featured_image']); 
              ?>
                <div class="row product_uploded_image mt-3">
                  <?php foreach ($featured_image_array as  $featured_image_name) { ?>
                    <div class="col-1">
                      <img class="img-thumbnail popup" src="<?php echo  base_url('assets/images/quiz')?>/<?php echo xss_clean($featured_image_name)?>">
                      <a href="/javascript:void(0)" class="btn btn-link p-0 delete_featured_image" data-image_name = "<?php echo xss_clean($featured_image_name)?>" data-quiz_id="<?php echo xss_clean($quiz_data['id'])?>"><?php echo lang('delete'); ?></a>
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
            <div class="featured_image_block">
            </div>
          </div>

          <div class="clearfix"></div>
          <div class="col-12">
            <div class="form-group <?php echo form_error('quiz_instruction') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('instruction'), 'quiz_instruction'); ?>
              <?php
                $populateData = $this->input->post('quiz_instruction') ? $this->input->post('quiz_instruction') : (isset($quiz_data['quiz_instruction']) ? $quiz_data['quiz_instruction'] :  '' );
              ?>
              <textarea name="quiz_instruction" id="quiz_instruction" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
              <span class="small form-error"> <?php echo strip_tags(form_error('quiz_instruction')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>
          <hr />
          <div class="col-12"> 
            <h2 class="text-center"><?php echo lang('seo_heading');?></h2>
          </div>

          <div class="clearfix"></div>
          <hr />

          <div class="col-12">
            <div class="form-group <?php echo form_error('meta_title') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('meta_title'), 'metatitle'); ?> 
              <?php 
                $populateData = $this->input->post('metatitle') ? $this->input->post('metatitle') : (isset($quiz_data['meta_title']) ? $quiz_data['meta_title'] :  '' );
              ?>
              <input type="text" name="metatitle" id="metatitle" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('metatitle')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>
          <hr />
          
          <div class="col-12">
            <div class="form-group <?php echo form_error('meta_kewords') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('meta_kewords'), 'metakeywords'); ?>
              <?php
                $populateData = $this->input->post('metakeywords') ? $this->input->post('metakeywords') : (isset($quiz_data['meta_keywords']) ? $quiz_data['meta_keywords'] :  '' );
              ?>
              <input type="text" name="metakeywords" id="metakeywords" class="form-control" value="" data-role="tagsinput">
            </div>
          </div>    

          <div class="col-12">
            <div class="form-group <?php echo form_error('meta_description') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('meta_description'), 'metadescription'); ?>
              <?php
                $populateData = $this->input->post('metadescription') ? $this->input->post('metadescription') : (isset($quiz_data['meta_description']) ? $quiz_data['meta_description'] :  '' );
              ?>
              <textarea name="metadescription" id="metadescription" class="form-control " rows="5" ><?php echo xss_clean($populateData);?></textarea>
              <span class="small form-error"> <?php echo strip_tags(form_error('metadescription')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>
          <hr />

          <div class="col-12">
            <?php $saveUpdate = isset($quiz_id) ? lang('core_button_update') : lang('core_button_save'); ?>
            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
            <a href="<?php echo base_url('tutor/quiz');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
          </div>
          <div class="clearfix"></div>

        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>
