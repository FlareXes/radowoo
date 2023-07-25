<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($question_id) && $question_id ? '_update' : '' ?>
<div class="row">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <?php
        $data['tab_quiz_id'] = $quiz_id;
        $data['quiz_all_data'] = $quiz_data;
        $this->load->view('admin/quiz/tab_list',$data);
      ?>
    </div>
    <hr>
    <?php if($update)
    { ?>

        <div class="col-md-12">
          <a href="<?php echo base_url('admin/questions/translate-questions/').$question_id ?>" class="btn btn-warning float-right">Translate Questions</a>
        </div>
      <div class="clearfix"></div>
    <hr>

      <?php
    }?>
    <div class="typeit_toolbar_edit"></div>
    <div class="textbox-and-buttons-container"></div>

    <div class="col-12">      
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
        
        <div class="row"> 

          <div class="col-12">
            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_title'), 'title'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($question_data['title']) ? $question_data['title'] :  '' );
              ?>
              <textarea name="title" id="title" class="form-control custom-area" rows="5" ><?php echo xss_clean($populateData);?></textarea>
              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
            </div>

          </div>




          <div class="col-6">
            <div class="form-group">
              <?php echo  form_label(lang('Upload File Type')); ?>
              <?php $upload_type_array = array('image'=>lang('Image'),"audio" =>lang('Audio'));
              $populateData = $this->input->post('upload_type') ? $this->input->post('upload_type') : (isset($question_data['upload_type']) ? $question_data['upload_type'] :  'image' );
              
              ?>
              <select name="upload_type" class="form-control select2">
                  <?php foreach($upload_type_array as $key => $value) 
                  { 
                    $select_val = ($populateData == $key) ? 'selected' : ''; ?>
                      <option <?php echo $select_val;?> value="<?php echo $key;?>"><?php echo $value;?></option>
                    <?php 
                } ?>  
              </select>
              <span class="small form-error"> <?php echo strip_tags(form_error('upload_type')); ?> </span>
            </div>
          </div>




          <div class="col-6">
            <div class="question_img_block form-group <?php echo form_error('image_upload_error') ? ' has-error' : ''; ?>">
                <?php echo  form_label(lang('admin_upload_image'), 'image'); ?>
                <?php 
                  $populateData = isset($question_data['image']) ? $question_data['image'] :  '';
                ?>
                <input type="File" name="image" id="image" class="form-control">
                <span class="small form-error"> <?php echo strip_tags(form_error('image_upload_error')); ?> </span>
                
                <div class="attachment_block">
                  
                  <?php 
                  $file_upload_type = (isset($question_data['upload_type']) && $question_data['upload_type']) ? $question_data['upload_type'] :  'image';
                  if($populateData && $file_upload_type == "image" && isset($question_id) && $question_id)
                  { ?>
                    <img src='<?php echo base_url("assets/images/questions/$populateData"); ?>' class="question_image popup"> 

                    <a href="javascript:void(0)" class="delete_attachment" data-question_id="<?php echo $question_id; ?>">Delete Attachment</a>

                    <?php
                  }

                  if($populateData && $file_upload_type == "audio" && isset($question_id) && $question_id)
                  { 
                    $ext = pathinfo($populateData, PATHINFO_EXTENSION);
                    $audio_source_type = "audio/mpeg";
                    if(strtolower($ext) == "mp3")
                    {
                      $audio_source_type = "audio/mpeg";
                    }
                    else if(strtolower($ext) == "ogg")
                    {
                      $audio_source_type = "audio/ogg";
                    }
                    else if(strtolower($ext) == "wav")
                    {
                      $audio_source_type = "audio/wav";
                    }
                    else if(strtolower($ext) == "m4a")
                    {
                      $audio_source_type = "audio/mp4";
                    }
                    ?>

                    <!-- <a target="blank" href="<?php //echo base_url("assets/images/questions/").$populateData; ?>">View Attachment</a> -->
                    <audio controls class="w-100 no_underline">
                      <source src="<?php echo base_url("assets/images/questions/").$populateData; ?>" type="<?php echo $audio_source_type; ?>">
                      Your browser does not support the audio element.
                    </audio>
                    <a href="javascript:void(0)" class="delete_attachment" data-question_id="<?php echo $question_id; ?>">Delete Attachment</a>

                    <?php
                  } ?>
              </div>

            </div>
          </div>


          <div class="clearfix"></div>






          <div class="col-4">
            <div class="form-group">
              <?php echo form_label(lang('admin_question_addon_type'), 'select'); ?>
              <?php 
                $populateData = isset($question_data['addon_type']) ? $question_data['addon_type'] :  0;
              ?>
              <select name="addon_type" id="addon_type" class="form-control">
                  <option value="0">None</option>
                  <option value="1" <?php if($populateData==1) echo "selected" ?> ><?php echo lang('youtube_embed_code'); ?></option>
                  <option value="2" <?php if($populateData==2) echo "selected" ?> ><?php echo lang('vimeo_embed_code'); ?></option>
                  <option value="3" <?php if($populateData==3) echo "selected" ?> > <?php echo lang('external_video_link'); ?></option>
                  <option value="4" <?php if($populateData==3) echo "selected" ?> ><?php echo lang('external_audio_link'); ?></option>
              </select>
            </div>
          </div>

          <div class="col-8">
            <div class="form-group">
              <?php echo form_label(lang('admin_question_addon_value'), 'text'); ?>
              <?php 
                $populateData = isset($question_data['addon_value']) ? $question_data['addon_value'] :  0;
              ?>
              <input type="text" name="addon_value" id="addon_value" value="<?php echo xss_clean($populateData);?>" class="form-control" />
            </div>
          </div>    
          
          <div class="clearfix"></div>




          <div class="col-4">
            <div class="form-group">
              <?php echo  form_label(lang('Questions Help Paragraph')); ?>
              <?php 
              $populateData = $this->input->post('question_paragraph_id') ? $this->input->post('question_paragraph_id') : (isset($question_data['question_paragraph_id']) ? $question_data['question_paragraph_id'] :  'image' );
              ?>
              <select name="question_paragraph_id" class="form-control select2">
                  <option value="">Select Pragraph </option>
                  <?php foreach($pragraph_data as $value) 
                  { 
                    $selected = ($populateData == $value->id) ? 'selected' : ''; ?>
                      <option <?php echo $selected; ?>  value="<?php echo $value->id;?>"><?php echo $value->title;?></option>
                    <?php 
                  } ?>  
              </select>
              <span class="small form-error"> <?php echo strip_tags(form_error('question_paragraph_id')); ?> </span>
            </div>
          </div>

          <div class="col-4">
            <div class="form-group">
              <?php echo  form_label(lang('Questions Section')); ?>
              <?php 
              $populateData = $this->input->post('question_section_id') ? $this->input->post('question_section_id') : (isset($question_data['question_section_id']) ? $question_data['question_section_id'] :  'image' );
              ?>
              <select name="question_section_id" class="form-control select2">
                  <option <?php echo ($populateData == 0) ? 'selected' : ''; ?> value="0">Select Section </option>
                  <?php foreach($section_data as $value) 
                  { 
                    $selected = ($populateData == $value->id) ? 'selected' : ''; ?>
                      <option <?php echo $selected; ?>  value="<?php echo $value->id;?>"><?php echo $value->title;?></option>
                    <?php 
                  } ?>  
              </select>
              <span class="small form-error"> <?php echo strip_tags(form_error('question_section_id')); ?> </span>
            </div>
          </div>



          <div class="col-4">
            <div class="question_img_block form-group <?php echo form_error('solutionimage_error') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_upload_solution_image'), 'solutionimage'); ?>
              <?php 
                $populateData = isset($question_data['solution_image']) ? $question_data['solution_image'] :  '';
              ?>
              <input type="File" name="solutionimage" id="solutionimage" class="form-control">
              <span class="small form-error"> <?php echo strip_tags(form_error('solutionimage_error')); ?> </span>
              <?php 
                if($populateData)
                {
              ?>
                <img src='<?php echo base_url("assets/images/questions/solution/$populateData"); ?>' class="question_image popup">

              <?php
                }
              ?>
            </div>
          </div>   

          <div class="clearfix"></div>


          <div class="col-12">
            <div class="form-group <?php echo form_error('solution') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('solution'), 'solution'); ?>
              
              <?php
              
                $populateData = $this->input->post('solution') ? $this->input->post('solution') : (isset($question_data['solution']) ? $question_data['solution'] :  '' );

              ?>
              <textarea name="solution" id="p_desc" class="form-control custom-area" rows="5" ><?php echo xss_clean($populateData);?></textarea>
              <span class="small form-error"> <?php echo strip_tags(form_error('solution')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>



          <div class="col-12">
            <div class="form-group">
              <?php echo  form_label(lang('Is Match Type')); ?>
              <?php $choies_input_array = array('NO'=>lang('NO'),"YES" =>lang('YES'));
              
              $populateData = $this->input->post('question_type_is_match') ? $this->input->post('question_type_is_match') : ((isset($question_data['question_type_is_match']) && $question_data['question_type_is_match']) ? $question_data['question_type_is_match'] :  NULL );

              if($populateData)
              { ?>

                <input type="text" class="form-control question_type_is_match" readonly="true" name="question_type_is_match" value="<?php echo $populateData; ?>">
                <?php
              }
              else
              { ?>
                <select name="question_type_is_match" class="form-control select2 question_type_is_match">
                    <?php foreach($choies_input_array as $key => $value) 
                    { 
                      $select_val = ($populateData == $key) ? 'selected' : ''; ?>
                        <option <?php echo $select_val;?> value="<?php echo $key;?>"><?php echo $value;?></option>
                      <?php 
                  } ?>  
                </select>
                <p class="m-0 w-100 text-warning">One Choice Consider As Text Input Answer</p>
                <span class="small form-error"> <?php echo strip_tags(form_error('question_type_is_match')); ?> </span>
              <?php
              }
              ?>
            </div>
          </div>






          <div class="clearfix"></div>

          <?php 
            $first_choies = NULL;
            if($question_type_is_match != "YES")
            {
              $first_choies = isset($choices_array[0]) ? $choices_array[0] : ''; 
            }
            $display_none = ($question_type_is_match == "YES") ? "style='display:none';" : "";
          ?>
          <div class="clearfix"></div> 

















          <div class="clearfix"></div>


          <div class="col-12">
            <div class="after_ticket_section row">

              <div class="col-md-8">
                <div class="form-group <?php echo form_error('choices[]') && empty($first_choies) ? ' has-error' : ''; ?> choice_block">
                  <label class="control-label"> <?php echo lang('question_choices'); ?>  <span class="text-danger">*</span></label>
                  <textarea name="choices[0]" id="choices" class="form-control choices custom-area" rows="5" ><?php echo xss_clean($first_choies); ?></textarea>
                  <span class="small form-error"> <?php echo xss_clean($first_choies) ? '' : strip_tags(form_error("choices[]")); ?> </span>
                </div>
              </div>

              <?php 
                $first_is_checked = '';
                foreach ($arr_is_correct as $array_key => $correct_value) 
                {
                  if(empty($first_is_checked))
                  {
                    $first_is_checked = $correct_value == $first_choies ? 'checked' : '';
                  }
                }
                $no_of_option = $no_of_option ? $no_of_option : 0;
              ?>

              <div class="col-2">
                <div class="form-group togle_button">
                  <label for="status" ><?php echo lang('admin_is_correct'); ?> </label>
                  <label class="custom-switch form-control">
                    <input type="checkbox" name="is_correct[0]" value="1" class="custom-switch-input is_correct"  <?php echo xss_clean($first_is_checked);  ?>>
                    <span class="custom-switch-indicator"></span>
                  </label>
                </div>
              </div>

              <div class="col-md-2">
                <div class="form-group mt-4 pt-2">
                  <button class="btn btn-primary  btn-block add-more" data-index="<?php echo xss_clean($no_of_option); ?>" type="button">
                    <?php echo lang('admin_add_more'); ?></button>
                  </div>
                </div>

                <div class="clearfix"></div>

                <?php 
                  if($choices_array)
                  { 
                    unset($choices_array[0]);

                    $is_checked = '';
                    $i = 0;

                    foreach ( $choices_array as  $choices_value) 
                    { 
                      $i++;
                      foreach ($arr_is_correct as $array_key => $correct_value) 
                      {
                        if(empty($is_checked))
                        {
                          $is_checked = $correct_value == $choices_value ? 'checked' : '';
                        }
                      } 
                ?>

                    <div class="copied_ticket_section col-12">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group <?php echo form_error('choices[]') && empty($choices_value) ? ' has-error' : ''; ?>">
                            <label class="control-label">Options <span class="text-danger">*</span></label>
                            <textarea name="choices[<?php echo xss_clean($i); ?>]" id="choices" class="form-control choices custom-area" rows="5" ><?php echo xss_clean($choices_value); ?></textarea>
                            <span class="small form-error"> <?php echo xss_clean($choices_value) ? '' : strip_tags(form_error("choices[]")); ?> </span>
                          </div>
                        </div>

                        <div class="col-2">
                          <div class="form-group togle_button">
                            <label for="status" >Is Correct </label>
                            <label class="custom-switch form-control">
                              <input type="checkbox" name="is_correct[<?php echo xss_clean($i); ?>]" value="1" class="custom-switch-input is_correct"  <?php echo xss_clean($is_checked);  ?>>
                              <span class="custom-switch-indicator"></span>
                            </label>
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group mt-4 pt-2 ">
                            <button class="btn btn-danger  btn-block remove_block_btn<?php echo xss_clean($update); ?>" type="button">Remove</button>
                          </div>
                        </div>  
                        <div class="clearfix"></div>

                      </div>
                    </div>
                <?php
                  $is_checked = '';
                  } }
                ?>

              </div>
            </div>
            <div class="clearfix"></div>
            <hr>

            <div class="col-12">
              <?php $saveUpdate = isset($question_id) ? lang('core_button_update') : lang('core_button_save'); ?>
              <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
              <a href="<?php echo base_url('admin/quiz/questions/').$quiz_id;?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
            </div>
            <div class="clearfix"></div>
          </div>
          <?php echo form_close();?>
        </div>        

      <section class="copy_ticket_section d-none">
        <div class="copied_ticket_section col-12">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group choice_block">
                <label class="control-label"><?php echo lang('question_choices'); ?> <span class="text-danger">*</span></label>
                <textarea name="choices[]" id="choices" class="form-control choices custom-area checkkk" rows="5" ></textarea>

              </div>
            </div>

            <div class="col-2">
              <div class="form-group togle_button">
                <label for="status" ><?php echo lang('admin_is_correct'); ?> </label>
                <label class="custom-switch form-control">
                  <input type="checkbox" name="is_correct[]" value="1" class="custom-switch-input is_correct"  data-size="sm">
                  <span class="custom-switch-indicator"></span>
                </label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group mt-4 pt-2 ">
                <button class="btn btn-danger  btn-block remove_block_btn<?php echo xss_clean($update); ?>" type="button"><?php echo lang('admin_record_remove'); ?></button>
              </div>
            </div>  
            <div class="clearfix"></div>

          </div>
        </div>
      </section> 
    </div>
  </div>
