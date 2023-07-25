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
              <textarea name="title" id="title" class="form-control custom-area normaleditor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
            </div>

          </div>




          <div class="col-4">
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

          <div class="col-4">
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

                    <a href="<?php echo base_url('admin/QuestionController/delete_upload_file_type/'.$quiz_id.'/'.$question_data['id']); ?>" class="delete_attachment" data-question_id="<?php echo $question_id; ?>">Delete Attachment</a>

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

                    <a href="<?php echo base_url('admin/QuestionController/delete_upload_file_type/'.$quiz_id.'/'.$question_data['id']); ?>" class="delete_attachment" data-question_id="<?php echo $question_id; ?>">Delete Attachment</a>

                    <?php
                  } ?>
              </div>

            </div>
          </div>

          <div class="col-4">
            <div class="form-group">
              <?php echo  form_label(lang('helpsheet'), 'helpsheet'); ?>
              <input type="File" name="helpsheet" id="image" class="form-control">
                <span class="small form-error"> <?php echo strip_tags(form_error('image_upload_error')); ?> </span>   
                <?php 
                  $helpsheet_img = (isset($question_data['helpsheet']) && $question_data['helpsheet']) ? base_url('assets/images/helpsheet/'.$question_data['helpsheet']) :  base_url('assets/default/default.jpg');

                ?>
                <img src='<?php echo $helpsheet_img; ?>' class="img_thumb mt-2 popup">  
                <?php if(isset($question_data['helpsheet']) && $question_data['helpsheet']) { ?>
                  <a href="<?php echo base_url('admin/QuestionController/delete_helpsheet/'.$quiz_id.'/'.$question_data['id']); ?>" class="delete_attachment" data-question_id="<?php echo xss_clean($question_data['id'])?>">Delete Attachment</a>
                <?php } ?>  
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

          <div class="col-6">
            <div class="form-group">
              <?php echo form_label(lang('admin_question_addon_value'), 'text'); ?>
              <?php 
                $populateData = isset($question_data['addon_value']) ? $question_data['addon_value'] :  0;
              ?>
              <input type="text" name="addon_value" id="addon_value" value="<?php echo xss_clean($populateData);?>" class="form-control" />
            </div>
          </div> 

          <div class="col-2">
            <div class="form-group">
              <?php echo form_label(lang('render_content'), 'text'); ?>
              <select name="render_content" class="form-control">
                  
                  <option value="0" <?php if(isset($question_data['render_content']) && $question_data['render_content']==0) echo "selected" ?> ><?php echo lang('No'); ?></option>
                  <option value="1" <?php if(isset($question_data['render_content']) && $question_data['render_content']==1) echo "selected" ?> ><?php echo lang('Yes'); ?></option> 
              </select>   
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
              <textarea name="solution" id="p_desc" class="form-control custom-area normaleditor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
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













 
          <div class="col-12">
            
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

              


            <div class="after_ticket_section row" <?php echo $display_none ; ?> >
              


                  <div class="col-md-6">
                    <div>
                    <button class="btn btn-primary add-btn  btn-block add-more" data-index="<?php echo $no_of_option; ?>" type="button">
                      <i class="fa fa-plus"></i></button>
                    </div>
                    <div class="form-group <?php echo form_error('choices[]') && empty($first_choies) ? ' has-error' : ''; ?> choice_block">
                      <label class="control-label"> <?php echo lang('question_choices'); ?>  <span class="text-danger">*</span></label>
                      <textarea name="choices[0]" id="choices" class="form-control choices custom-area editor" rows="5" ><?php echo xss_clean($first_choies); ?></textarea>
                      <span class="small form-error"> <?php echo xss_clean($first_choies) ? '' : strip_tags(form_error("choices[]")); ?> </span>
                    </div>

                    <div class="form-group togle_button">
                      <label for="status" ><?php echo lang('admin_is_correct'); ?> </label>
                      <label class="custom-switch form-control">
                        <input type="checkbox" name="is_correct[0]" value="1" class="custom-switch-input is_correct"  <?php echo xss_clean($first_is_checked);  ?>>
                        <span class="custom-switch-indicator"></span>
                      </label>
                    </div>
                  <hr class="mb-5" />
                  </div>





                  <?php 
                  if($choices_array && $question_type_is_match == "NO")
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
                      }  ?>

                          
                              <div class="copied_ticket_section col-md-6">

                                <div>
                                  <button class="btn btn-danger remove-btn btn-block remove_block_btn<?php echo xss_clean($update); ?>" type="button"><i class="fas fa-times"></i></button>
                                </div>

                                <div class="form-group <?php echo form_error('choices[]') && empty($choices_value) ? ' has-error' : ''; ?>">
                                  <label class="control-label">Options <span class="text-danger">*</span></label>
                                  <textarea name="choices[<?php echo xss_clean($i); ?>]" id="choices" class="form-control choices custom-area editor" rows="5" ><?php echo xss_clean($choices_value); ?></textarea>
                                  <span class="small form-error"> <?php echo xss_clean($choices_value) ? '' : strip_tags(form_error("choices[]")); ?> </span>
                                </div>

                                <div class="form-group togle_button">
                                  <label for="status" >Is Correct </label>
                                  <label class="custom-switch form-control">
                                    <input type="checkbox" name="is_correct[<?php echo xss_clean($i); ?>]" value="1" class="custom-switch-input is_correct"  <?php echo xss_clean($is_checked);  ?>>
                                    <span class="custom-switch-indicator"></span>
                                  </label>
                                </div>

                                <hr class="mb-5" />

                              </div>
                          
                        
                      <?php
                      $is_checked = '';
                    } 
                  }
                ?>


            </div>
          
          </div>


          <!-------------------------------- cross Choices Work Start ---------------------------------------->
          <?php 

          $display_none = ($question_type_is_match == "NO" OR empty($question_type_is_match)) ? "style='display:none';" : "";

          ?>

          <div class="muliple_selection_work col-12 " <?php echo $display_none; ?> >
            <div class="row">
              
            

            <?php 
              $first_choice = NULL;
              $second_choice_is_correct = NULL;
              $choice_display_order = NULL;
              $no_of_option = $no_of_option ? $no_of_option : 1;

              if($question_type_is_match == "YES")
              {
                $first_choice = isset($choices_array[1]) ? $choices_array[1] : '';
                $second_choice_is_correct = isset($arr_is_correct[1]) ? $arr_is_correct[1] : "";
                $choice_display_order = isset($display_order_array[1]) ? $display_order_array[1] : 0;
              }
              
            ?>


          <div class="col-12">
            <div class="row">
              <div class="col-4">
                <label>Question Choice 1</label>
              </div>
              <div class="col-4">
                <label>Question Choice 2</label>
              </div>
              <div class="col-3">
                <label >Choice 2 Display Order</label>
              </div>
              <div class="col-1">
                <label>&nbsp;</label>
              </div>
            </div>
          </div>

          <div class="col-12 mb-5">
            <div class="after_cross_input_section row">
              <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-4">
                      <div class="form-group mb-0 <?php echo form_error('mark_choices[]') && empty($first_choice) ? ' has-error' : ''; ?>">                        
                        <input type="text" name="mark_choices[1]" value="<?php echo $first_choice; ?>" class="form-control" >
                        <span class="small form-error"> <?php echo xss_clean($first_choice) ? '' : strip_tags(form_error("mark_choices[]")); ?> </span>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="row">
                        <div class="col-10">
                          <div class="form-group mb-0 <?php echo form_error('mark_is_correct[]') && empty($second_choice_is_correct) ? ' has-error' : ''; ?>">   
                            <input type="text" name="mark_is_correct[1]" value="<?php echo $second_choice_is_correct; ?>"  class="form-control" >
                            <span class="small form-error"> <?php echo xss_clean($second_choice_is_correct) ? '' : strip_tags(form_error("mark_is_correct[]")); ?> </span>
                          </div>
                        </div>
                        <div class="col-2 mt-2 ">
                          <span class="badge badge-primary"><?php echo 1; ?></span>
                        </div>
                      </div>
                      
                    </div>
                    <div class="col-3">
                      <div class="form-group mb-0 <?php echo form_error('is_correct_display_order[]') && empty($choice_display_order) ? ' has-error' : ''; ?>">   
                        <input type="number" name="is_correct_display_order[1]" value="<?php echo $choice_display_order; ?>"  class="form-control" >
                        <span class="small form-error"> <?php echo xss_clean($choice_display_order) ? '' : strip_tags(form_error("is_correct_display_order[]")); ?> </span>
                      </div>
                    </div>
                    <div class="col-1">
                      <button class="btn btn-primary  btn-block add-more-cross" data-index="<?php echo xss_clean($no_of_option ); ?>" type="button">
                          <i class="far fa-plus-square"></i></button>
                    </div>
                </div>
              </div>




                  <?php 
                  if($choices_array && $question_type_is_match == "YES")
                  { 
                    unset($choices_array[1]);
                    unset($arr_is_correct[1]);
                    unset($display_order_array[1]);

                    $is_checked = '';
                    $i = 0;

                    foreach ( $choices_array as $choice_array_key => $choices_value) 
                    {    

                      $first_choice = isset($choices_array[$choice_array_key]) ? $choices_array[$choice_array_key] : '';
                      $second_choice_is_correct = isset($arr_is_correct[$choice_array_key]) ? $arr_is_correct[$choice_array_key] : "";
                      $choice_display_order = isset($display_order_array[$choice_array_key]) ? $display_order_array[$choice_array_key] : 0;                      
                      ?>

                      <div class="copied_cross_section col-12 my-2">
                        <div class="row">
                              
                              <div class="col-4">

                                <div class="form-group mb-0 <?php echo form_error('mark_choices[]') && empty($first_choice) ? ' has-error' : ''; ?>">  

                                  <input type="text" name="mark_choices[<?php echo $choice_array_key; ?>]" value="<?php echo $first_choice; ?>" class="form-control match_question_choices_one" >

                                  <span class="small form-error"> <?php echo xss_clean($first_choice) ? '' : strip_tags(form_error("mark_choices[]")); ?> </span>

                                </div>

                              </div>

                              <div class="col-4">
                                <div class="row">
                                  <div class="col-10">
                                    
                                    <div class="form-group mb-0 <?php echo form_error('mark_is_correct[]') && empty($second_choice_is_correct) ? ' has-error' : ''; ?>">   
                                      
                                      <input type="text" name="mark_is_correct[<?php echo $choice_array_key; ?>]" value="<?php echo $second_choice_is_correct; ?>"  class="form-control match_question_choices_two">

                                      <span class="small form-error"> <?php echo xss_clean($second_choice_is_correct) ? '' : strip_tags(form_error("mark_is_correct[]")); ?> </span>

                                    </div>

                                  </div>
                                  <div class="col-2 mt-2">
                                    <span class="badge badge-primary match_question_choices_two_index"><?php echo $choice_array_key; ?></span>
                                  </div>
                                </div>
                                
                              </div>
                              <div class="col-3">

                                <div class="form-group mb-0 <?php echo form_error('is_correct_display_order[]') && empty($choice_display_order) ? ' has-error' : ''; ?>">   

                                  <input type="number" name="is_correct_display_order[<?php echo $choice_array_key; ?>]" class="form-control match_question_display_index" value="<?php echo $choice_display_order; ?>" >

                                  <span class="small form-error"> <?php echo xss_clean($choice_display_order) ? '' : strip_tags(form_error("is_correct_display_order[]")); ?> </span>

                                </div>

                              </div>

                              <div class="col-1">
                                 
                              <button class="btn btn-danger  btn-block remove_cross_block_btn" type="button" data-index="<?php echo $choice_array_key; ?>">
                                <i class="fas fa-trash-alt"></i>
                              </button>
                                
                              </div>
                          </div>
                      </div><?php
                    } 
                  }
                ?>
            </div>
          </div>

            </div>
          </div>

          <!-------------------------------- cross Choices Work End ---------------------------------------->


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
        
            
            <div class="copied_ticket_section col-md-6"> 
              <div>
                <button class="btn btn-danger remove-btn btn-block remove_block_btn<?php echo xss_clean($update); ?>" type="button"><i class="fas fa-times"></i></button>
              </div>
              <div class="form-group choice_block">
                <label class="control-label"><?php echo lang('question_choices'); ?> <span class="text-danger">*</span></label>
                <textarea name="choices[]" id="choices" class="form-control choices custom-area checkkk" rows="5" ></textarea>
              </div>
              <div class="form-group togle_button">
                <label for="status" ><?php echo lang('admin_is_correct'); ?> </label>
                <label class="custom-switch form-control">
                  <input type="checkbox" name="is_correct[]" value="1" class="custom-switch-input is_correct"  data-size="sm">
                  <span class="custom-switch-indicator"></span>
                </label>
              </div>
            </div>  
            <hr class="mb-5">
         
      </section> 

      <section class="copy_cross_section d-none">
        <div class="copied_cross_section col-12 my-2">
          <div class="row">
                <div class="col-4">
                  <input type="text" name="mark_choices[]" value="" class="form-control match_question_choices_one" >
                </div>
                <div class="col-4">
                  <div class="row">
                    <div class="col-10">
                      <input type="text" name="mark_is_correct[]" value=""  class="form-control match_question_choices_two" >
                    </div>
                    <div class="col-2 my-auto">
                      <span class="badge badge-primary match_question_choices_two_index">1</span>
                    </div>
                  </div>
                  
                </div>
                <div class="col-3">
                  <input type="number" name="is_correct_display_order[]" class="form-control match_question_display_index" data-index="" >
                </div>

                <div class="col-1">
                   
                <button class="btn btn-danger  btn-block remove_cross_block_btn" type="button">
                  <i class="fas fa-trash-alt"></i>
                </button>
                  
                </div>
            </div>

        </div>
      </section>


    </div>
  </div>
