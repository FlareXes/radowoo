<style>
.question-palette-right_side{ margin-right: 50px; } .question-palette-right_side2{ margin-right: 60px; } .question-palette-right_side3{ margin-right: 30px; } @media only screen and (max-width: 767px) { .top-bar,.sticky_top,.header_add_section,.footer_add_section,.footer{ display:none; } .header-card{ position:relative; } .header-timer{ position: fixed; top: -17px; z-index: 94; background-color: #eee;padding-bottom: 5px !important; } .footer-button { width:100%; position: fixed; bottom: 0px; z-index: 9; background: #eee; } .test-heading{ text-align: center !important; margin: 0px 0px !important; } .question-pallete{ padding-bottom:100px; } .pre-main { float:none; } .pre-next { float:right; } .mrk-nxt { float: left; font-size: 9px; } .mrk-rev { float: right; font-size: 9px; } }
</style>
<?php 
    $question_answered_status = (isset($question_data['status'])) ? $question_data['status'] : "";
    $is_previous_disable = (isset($quiz_data['is_previous_disable']) && $quiz_data['is_previous_disable'] == 1) ? true : false; 
      $is_answered_or_disable = ($question_answered_status && $question_answered_status != "visited" && $is_previous_disable) ? true : false;

      $top_bar = ($this->settings->is_compatible == 'MOBILE') ? 'top-bar' : '';
      $card_header = ($this->settings->is_compatible == 'MOBILE') ? 'header-card' : '';
      $header_timer = ($this->settings->is_compatible == 'MOBILE') ? 'header-timer' : '';
      $footer_btn = ($this->settings->is_compatible == 'MOBILE') ? 'footer-button' : '';
      $test_heading = ($this->settings->is_compatible == 'MOBILE') ? 'test-heading' : '';
      $ques_pallete = ($this->settings->is_compatible == 'MOBILE') ? 'question-pallete' : '';
      $queston_choies_type = $question_data['queston_choies_type'];
      
?>
<div class="container">
  
  <div class="row">
    <div class="col-12 mb-5">
      <div class="row mt-5">
        <div class="card mb-0 <?php echo $card_header; ?>">
          <div class="card-header timer py-5 <?php echo $header_timer; ?> row">
              <div class="col-md-5 col-xl-5 col-sm-12 text-left my-5 <?php echo $test_heading; ?>">
                <h2 class="heading m-0">
                    <?php
                    
                    $lang_id = get_language_data_by_language($this->session->userdata('language'));
                    $translate_quiz_title = get_translated_column_value($lang_id,'quizes',$quiz_data['id'],'title');
                    $quiz_title = $translate_quiz_title ? $translate_quiz_title : $quiz_data['title'];
                    echo ucwords($quiz_title);

                    ?>
                </h2>

              </div>
              <?php if($quiz_data['duration_min'] > 0) 
              { 
                $mobile_compatible_timer = ($this->settings->is_compatible == 'MOBILE') ? 'compatible-mobile-view' : 'my-5';
                $mobile_compatible_h2 = ($this->settings->is_compatible == 'MOBILE') ? 'compatible-mobile-h2' : '';
                ?>
                <div class="col-md-4 col-xl-4 col-sm-12 text-left <?php echo $mobile_compatible_timer; ?>">  
                  <h2 class="card-titlee timer heading m-0 <?php echo $mobile_compatible_h2; ?>">
                    <span class="float-left mr-3"> <?php echo lang('count_down'); ?>: </span>
                    <span class="text-danger timerrr" data-seconds-left=<?php echo xss_clean($left_time); ?> > &nbsp; </span>
                    <section class='actions'></section>
                  </h2>

                  <?php 
                  $loged_in_user_data = $this->session->userdata('logged_in');
                  $time_accommodation = (isset($loged_in_user_data['time_accommodation']) && $loged_in_user_data['time_accommodation'] > 0) ? $loged_in_user_data['time_accommodation'] : 0;


                  $quiz_time_assign = $quiz_data['duration_min'];
                  if($time_accommodation > 1)
                  {
                     $quiz_time_with_time_accommodation = round(($quiz_time_assign * $time_accommodation) * 60);
                     
                     $quiz_time_assign_sec = $quiz_time_assign * 60;

                     $time_accommodation_in_sec = $quiz_time_with_time_accommodation - $quiz_time_assign_sec;
                      $_hours = floor($time_accommodation_in_sec / 3600);
                      $_mins = floor($time_accommodation_in_sec / 60 % 60);
                      $_secs = floor($time_accommodation_in_sec % 60);
                     echo "<p class='w-100 p-1 mt-2 text-secondary'> Time Accommodation : ".sprintf('%02d:%02d:%02d', $_hours, $_mins, $_secs)."</p>";
                  }
                 

                   ?>
                </div>
                <?php 
              } ?>
              
                <div class="col-md-3 col-xl-3 col-sm-12 text-right <?php echo $mobile_compatible_timer; ?>">
                  
                  <?php
                    if($question_data['question_helpsheet'])
                    {
                  ?> 
                    <a class="helpitems btn btn-secondary" data-toggle="modal" title="<?php  echo lang('helpsheet'); ?>" data-target="#questionhelpmodal"><i class="fa fa-question"></i></a>
                  <?php } else if($quiz_data['quiz_helpsheet']) { ?>
                    <a class="helpitems btn btn-secondary" data-toggle="modal" title="<?php  echo lang('helpsheet');?>" data-target="#quizhelpmodal"><i class="fa fa-question"></i></a>
                  <?php } ?>
                  <a class="helpitems btn btn-info reportform" data-toggle="modal" title="<?php  echo lang('report_question');?>" data-target="#reportmodal"><i class="fa fa-exclamation"></i></a>
                </div>
              
               
          </div>
        </div>
      </div>
    </div>

    <!------ quiz helpsheet modal start ---->
    <div class="modal fade bd-example-modal-lg" id="quizhelpmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-custom">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo lang('helpsheet'); ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="d-none" aria-hidden="true">×</span>
              </button>
            </div>
        
          <div class="modal-body">
            <img src="<?php echo base_url('assets/images/helpsheet/'.$quiz_data['quiz_helpsheet']); ?>" class="d-block mx-auto">
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>  
      </div>
    </div>
    <!------ quiz helpsheet modal end ---->

     <!------ question helpsheet modal start ---->
    <div class="modal fade bd-example-modal-lg" id="questionhelpmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-custom">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo lang('helpsheet'); ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="d-none" aria-hidden="true">×</span>
              </button>
            </div>
        
          <div class="modal-body">
            <img src="<?php echo base_url('assets/images/helpsheet/'.$question_data['question_helpsheet']); ?>" class="d-block mx-auto">
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>  
      </div>
    </div>
    <!------ question helpsheet modal end ---->

    <!------ report form modal start ---->
     <div class="modal fade bd-example-modal-lg" id="reportmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-custom">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo lang('report_question_problem'); ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="d-none" aria-hidden="true">×</span>
              </button>
            </div>
          
          <?php echo form_open_multipart(base_url('send-report'), array('role'=>'form','novalidate'=>'novalidate')); ?>
            <div class="modal-body">
                <input type="hidden" name="page_url" value="<?php echo base_url('test/'.$quiz_id.'/'.$question_id); ?>">
                <input type="hidden" name="question_id" value="<?php echo $question_data['id']; ?>">
                <input type="radio" name="report_option" class="d-inline" value="<?php echo lang('i_think_the_answer_is_wrong'); ?>"> <?php echo lang('i_think_the_answer_is_wrong'); ?><br/>
                <input type="radio" name="report_option" class="d-inline" value="<?php echo lang('there_are_multiple_correct_answers'); ?>"> <?php echo lang('there_are_multiple_correct_answers'); ?><br/>
                <input type="radio" name="report_option" class="d-inline" value="<?php echo lang('there_is_a_typing_error'); ?>"> <?php echo lang('there_is_a_typing_error'); ?><br/>
                <input type="radio" name="report_option" class="d-inline" value="<?php echo lang('image_or_comprehension_text_is_not_display_properly'); ?>"> <?php echo lang('image_or_comprehension_text_is_not_display_properly'); ?><br/>
                <input type="radio" name="report_option" class="d-inline" value="<?php echo lang('something_else'); ?>"> <?php echo lang('something_else').'(please specify below)'; ?><br/>

                <h5 class="pt-5"><?php echo  form_label(lang('any_more_information')); ?></h5>
                <textarea name="any_info" class="form-control"></textarea>
              
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" name="sendreport" value="<?php echo lang('send_report'); ?>">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          <?php echo form_close(); ?>
        </div>  
      </div>
    </div>
    <!------ report form modal start ---->

    <?php $question_type_is_match = isset($question_data['question_type_is_match']) ? $question_data['question_type_is_match'] : "NO";

    ?>



  <div class="page-header">
    <?php 
      $csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
      );
    ?>
  </div>

  <div class="clearfix"></div>
  <!-- <div class="col-12">  <hr> </div> -->
  <?php
  if($is_evaluation_test == "YES")
  {
    
    ?>
   <input type="hidden" class="current_question_json" value='<?php echo $current_question_json; ?>'>
   <input type="hidden" class="current_question_answers_string" value='<?php echo $current_question_answers_string; ?>'>
   <input type="hidden" id="current_question_answers_string_for_fillintheblank" value='<?php echo trim(strip_tags($current_question_answers_string)); ?>'>
   <input type="hidden" class="current_question_answers_string_on_render" value='<?php echo $current_question_answers_string_on_render; ?>'>
   
   <input type="hidden" class="current_question_answers_string_keys" value='<?php echo $current_question_answers_string_keys; ?>'>
    <?php
  }
  ?>
  <input type="hidden" class="is_evaluation_test" value="<?php echo $is_evaluation_test; ?>">
  <input type="hidden" class="question_type_is_match_value" value="<?php echo $question_type_is_match; ?>">


  <input type="hidden" class="is_last_question" value="<?php echo $last_question; ?>">
  <input type="hidden" class="is_first_question" value="<?php echo $is_first_question; ?>">
  <input type="hidden" class="is_last_question_answerd" value="<?php echo $is_last_question_answerd; ?>">
  <input type="hidden" id="question_type_is_correct_value" value="<?php echo $queston_choies_type; ?>">


  <form action="" method="POST" class="running_quiz_question_form w-100" id="myform" class="container" >
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="running_quiz_id" value="<?php echo $quiz_id; ?>" />

    <div class="row">



    <?php 
    $section_name_array = array();

    foreach ($quiz_question_data as $array_key => $question_section_array) 
    {

      $section_question_id = $array_key + 1;
      $section_name_array[$question_section_array['question_section_id']] = array(
        'question_section_name' => $question_section_array['question_section_name'],
        'question_section_id' => $question_section_array['question_section_id'],
        'question_id' => $section_question_id
      );
    }
    $count_section_name = count($section_name_array);
    ?>

      <div class="col-lg-8">

        <?php
        if ($count_section_name > 1) 
        { ?>

            <div class="row">
              <div class="col-md-12">

                <ul class="nav nav-tabs  mx-0 mb-3 border-0">
                  <?php

                  foreach ($section_name_array as $key => $section_array) 
                  { 
                    $section_question_id = $section_array['question_id'];
                    $active = $section_array['question_id'] == $question_id ? "active" : "";
                    $active_section = $section_array['question_id'] == $question_id ? "btn-success" : "btn-primary";

                    foreach ($quiz_question_data as $temp_key => $temp_value) 
                    {
                      if($temp_value['question_section_id'] == $key)
                      {
                        $section_question_id = $temp_key + 1;
                        break;
                      }
                    }
                    ?>
                      <li class="nav-item lo <?php echo $active; ?> p-0 mr-1">
                          <a class="btn <?php echo $active_section; ?>" id="section<?php echo $section_array['question_section_id']; ?>-tab" href="<?php echo base_url('test/').$quiz_id.'/'.$section_question_id; ?>"><?php echo strtoupper(lang($section_array['question_section_name'])); ?></a>
                      </li>

                    <?php
                  }
                  ?>                 
                </ul> 

                <div class="clearfix"></div>
              </div>
            </div>
                <?php
        } ?>
              
              

        
        <div class="card">
          
          <div class="card-header">
            <div class="container-fluid w-100">
              <div class="row">
                <div class="col-md-12 mb-3">
                  <h3 class="w-100">
                    <p class="font-weight-bold"><?php echo lang('question_no'); ?> <?php echo xss_clean($question_id); ?></p>
                  </h3> 
                  <hr> 
                </div>
              </div>
              <?php 
                $question_paragraph = isset($question_data['question_paragraph_text']) ? $question_data['question_paragraph_text'] : NULL;

                if($question_paragraph)
                { ?>

                  <div class="row w-100">
                    <div class="col-md-12 mb-3">
                      <?php echo $question_paragraph;  ?>
                    </div>
                    <hr>    
                  </div>
                  <?php
                }
              ?>
              <div class="row">
                <div class="col-md-12">
                  <?php
                    $translate_question_title = get_translated_column_value($lang_id,'questions',$question_data['id'],'title');
                    $translate_question_title = $translate_question_title ? $translate_question_title : $question_data['title'];
                    $translate_question_title2 = $question_data['render_content'] == 0 ? strip_tags($translate_question_title) : htmlspecialchars(xss_clean($translate_question_title));
                     
                    ?>
                    <?php if(isset($translate_question_title2) && !empty($translate_question_title2)) 
                    { ?>
                      <div class="question-area w-100">
                        
                          <textarea readonly class="running_quiz_question"><?php echo $translate_question_title2; ?></textarea>  
                      </div>
                      <?php 
                    } ?>               
                </div>
              </div>
            </div>
          </div>

          <div class="card-body">


            <?php 
            $question_attachment = isset($question_data['image']) ? $question_data['image'] : NULL;

            $question_attach_type = isset($question_data['upload_type']) ? $question_data['upload_type'] : 'image';
            if($question_attachment)
            {

              $ext = pathinfo($question_attachment, PATHINFO_EXTENSION);
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

              if($question_attach_type == "audio")
              { ?>

                <audio controls class="w-100 no_underline">
                    <source src="<?php echo base_url("assets/images/questions/").$question_attachment; ?>" type="<?php echo $audio_source_type; ?>">
                    Your browser does not support the audio element.
                </audio>

                <?php
              }
              else
              {  ?>
                <div class="questions_img">
                  <img src="<?php echo base_url('assets/images/questions/').$question_attachment; ?>" class="image">
                </div>
              
                <?php
              }
            }
            ?>

            <div class="w-100 mb-4">
              
                <?php 
                // p($question_data);
                $addon_value = $question_data['addon_value'];
                // Youtube
                if($question_data['addon_type']==1) {
                    echo '<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" class="w-100" height="400" type="text/html" src="https://www.youtube.com/embed/'.$addon_value.'?autoplay=0&fs=1&iv_load_policy=3&showinfo=0&rel=0&cc_load_policy=0&start=0&end=0"><div></iframe>';
                }
                // Vimeo
                if($question_data['addon_type']==2) {
                    echo "<iframe class='w-100' height='400px' src='https://player.vimeo.com/video/$addon_value?&title=0&portrait=0'></iframe></div>";
                    
                }
                // Link
                if($question_data['addon_type']==3) {
                    echo '<a href="'.$addon_value.'" target="_blank">'.$addon_value.'</a>';
                    
                }
                if($question_data['addon_type']==4) { ?>

                  <audio controls="" class="w-100 no_underline">
                    <source src="<?php echo $addon_value; ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio> <?php
                }
                ?>

            </div>

            <div class="selectgroup selectgroup-pills w-100">
              <?php 
                $translate_question_choies = $question_data['choices'];

                if($question_type_is_match != "YES")
                {  
                  $translate_question_choies = get_translated_column_value($lang_id,'questions',$question_data['id'],'choices');
                }
                $translate_question_choies = $translate_question_choies ? $translate_question_choies : $question_data['choices'];

                $translate_question_choies_data = json_decode($translate_question_choies,true); 
                $question_choies = json_decode(json_encode($translate_question_choies_data), true);               
                

                $display_order_array = array();
                $araay_to_display_match = array();
                $arr_is_correct = array();


                if($question_type_is_match == "YES")
                {  

                  $question_choies_count = COUNT($question_choies);
 
                  $correct_choice = json_decode($question_data['correct_choice']); 
                  $display_order_array = isset($correct_choice->display_order_array) ? $correct_choice->display_order_array : array();
                  $araay_to_display_match = isset($correct_choice->araay_to_display_match) ? $correct_choice->araay_to_display_match : array();
                  $arr_is_correct = isset($correct_choice->arr_is_correct) ? $correct_choice->arr_is_correct : array();
                  

                  $araay_to_display_match = json_decode(json_encode($araay_to_display_match), true);
                  $arr_is_correct = json_decode(json_encode($arr_is_correct), true);
                  $display_order_array = json_decode(json_encode($display_order_array), true);
                  $q_answer = isset($question_data['answer'])  ?  $question_data['answer'] : array();

                  ?>


                  <div class="w-100 quiz_running_mark_coorect_block text-center">
                    <div class="row">
                      <div class="col-md-9 col-sm-9 col-xs-9">
                        <h6 class="bg-info text-white py-4 mb-3">A</h6>
                        <?php
                        $index = 0;
                        foreach ($question_choies as $key => $value) 
                        { 
                          $index ++;
                          $choices_second = isset($araay_to_display_match[$key]) ? $araay_to_display_match[$key] : ""; 
                          $answerd_choies_val = isset($q_answer[$key]) ? $q_answer[$key] : "";
                          ?>
                            <div class="form-group d-flex w-100 mb-2">
                              <h6 class="choies1 form-control border border-light"><?php echo $value; ?></h6>
                            </div>
                            <?php
                        } ?>
                      </div>

                      <div class="col-md-3 col-sm-3 col-xs-3">
                        <h6 class="bg-info text-white py-4 mb-3">Answer Box</h6>
                        <?php
                        $index = 0;
                        foreach ($question_choies as $key => $value) 
                        { 
                          $index ++;
                          $choices_second = isset($araay_to_display_match[$key]) ? $araay_to_display_match[$key] : ""; 
                          $answerd_choies_val = isset($q_answer[$key]) ? $q_answer[$key] : "";
                          ?>
                             <div class="form-group mb-2 d-flex w-100 padding_zero_for_desktop">
                                <input type="number" name="answer[<?php echo $key; ?>]" class="answer_input_box form-control border border-info correct_match_index float-left " data-value="<?php echo $value;?>" width="50" value="<?php echo $answerd_choies_val; ?>">
                              </div>
                            <?php
                        } ?>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-md-12">
                        <h6 class="bg-info text-white py-4 mb-3">B</h6>
                        <?php
                        $index = 0;
                        foreach ($question_choies as $key => $value) 
                        { 
                          
                          $index ++;
                          $choices_second = isset($araay_to_display_match[$key]) ? $araay_to_display_match[$key] : ""; 
                          $answerd_choies_val = isset($q_answer[$key]) ? $q_answer[$key] : "";
                          ?>
                            <div class="form-group d-flex w-100 multi_choice_question_number_section mb-2">
                              <h6 class="choies2 question_choies2 form-control border border-light"><span class="badge badge-dark  question_match_index"><?php echo $index; ?></span><?php echo $choices_second; ?></h6>
                            </div>
                            <?php
                        } ?>
                      </div>

                    </div>
                  </div>

                  <?php
                }
                else
                {

                  $question_choies_count = COUNT($question_choies);
                  $checked = '';
                  $chk = 'DUE';
                  $p = 0;
                  
                  foreach ($question_choies as $key =>  $question_choice) 
                  { 
                    
                    $answer_text = "";
                    $p++;              
                    $q_answer = isset($question_data['answer'])  ?  $question_data['answer'] : array();
                    foreach ($q_answer as  $value) 
                    {
                      $answer_text = $value;
                      if($question_choice == $value)
                      {
                        $checked = 'checked';
                        $chk = 'DONE';
                      }
                    }
                    
                    $is_multiple = $question_data['is_multiple'] == 1 ? 'checkbox' : 'radio';

                    $is_multiple_border = $question_data['is_multiple'] == 1 ? 'multiple-choise' : '';
                    
                    $question_type = $question_data['render_content'] == 0 ? xss_clean($question_choice) : htmlspecialchars(xss_clean($question_choice));
                    $choice_render = $question_data['render_content'] == 0 ? xss_clean($translate_question_choies_data[$key]) : htmlspecialchars(xss_clean($translate_question_choies_data[$key]));

                    if($queston_choies_type == "text")
                    {
                      ?>
                      <input type="text" id="queston_choies_type_text_id" name="answer[]" value="" class="form-control answer_text_field">
                      <?php
                    }
                    else
                    {
                      ?>

                      <label class="selectgroup-item btn-block">
                        <input type="hidden" class="render-content" value="<?php echo $question_data['render_content']; ?>">
                        <input <?php echo xss_clean($checked) ;?> type="<?php echo xss_clean($is_multiple); ?>" name="answer[]" value="<?php echo htmlspecialchars($question_type); ?>" class="selectgroup-input answer_input" >

                        <div class="selectgroup-button <?php echo $is_multiple_border;?>">
                          <?php echo $choice_render; ?>
                        </div>
                      </label>
                      <?php 
                      $checked = '';   
                    }            
                  }
                  if($question_data['is_multiple'] == 1) 
                  { ?>
                    <div class="w-100 text-right text-warning"><?php echo lang('multiple_choice_question');?></div>
                    <?php 
                  } 
                } ?>  

            </div>

          </div>
          <?php 
                  $card_footer_compatible = ($this->settings->is_compatible == 'MOBILE') ? 'card_footer_compatible' : '';
                ?>
          <div class="card-footer <?php echo $card_footer_compatible; ?> <?php echo $footer_btn; ?>">
            <div class="card-options">
              
              <div class="next-previous-main <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'pre-main' : '';?>">
                <?php $previous_button_disable = (isset($quiz_data['is_previous_disable']) && $quiz_data['is_previous_disable'] == 1) ? 'd-none' : ''; 
                  
                ?>
                <button type="Submit" name="preview_quiz" value="Previous" class="btn btn-sm btn-azure mr-2 preview_quiz <?php echo $previous_button_disable; ?>">
                  <i class="fe fe-chevron-left"></i><?php echo lang('previous_btn'); ?>
                </button>

                <button type="Submit" name="next_quiz" value="Next" class="btn btn-sm btn-azure mr-2 next_quiz <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'pre-next' : '';?>"><?php echo lang('next_btn'); ?> 
                  <i class="fe fe-chevron-right"></i>
                </button>
              </div> 
              <?php if(isset($this->settings->mark_for_review_and_next) && $this->settings->mark_for_review_and_next == 'Enable' && $is_previous_disable == false) { ?>
                <button type="Submit" name="mark_or_next_quiz" value="<?php echo lang('Mark for Review and Next'); ?>" class="btn btn-sm btn-orange mr-2 ml-auto answer_given mark_or_next_quiz <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'mrk-nxt' : '';?>">
                  <i class="fe fe-check-circle"></i><?php echo lang('mark_for_review_and_next'); ?> 
                </button>
              <?php }  
              if($is_previous_disable == false)
              { ?>
              <button type="Submit" name="mark_for_answer_and_next" value="<?php echo lang('Mark for Answer and Next'); ?>" class="btn btn-sm btn-yellow mr-2 ml-auto mark_for_answer_and_next answer_given <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'mrk-rev' : '';?>">
                <i class="fe fe-check-circle"></i><?php echo lang('mark_for_answer_and_next'); ?> 
              </button>
              <?php } ?>

              <div class="save-next-main">

                <button type="Submit" name="save_or_next_quiz" value="Save & Next" class="btn btn-sm btn-teal mr-2 answer_given save_or_next_quiz">
                  <i class="fe fe-save"></i> <?php echo lang('save_and_next'); ?>
                </button>

              </div> 

            </div>
            <?php if($quiz_data['is_previous_disable'] == 1 && isset($this->settings->previous_disable_note) && $this->settings->previous_disable_note) { ?>
              <div class="text-danger py-4"><span>***</span><?php echo lang('note').': '.$this->settings->previous_disable_note; ?></div>
            <?php } ?>  
          </div>

        </div>
      </div>

      <div class="col-lg-4 order-lg-1 mb-4">
        <div class="card <?php echo $ques_pallete; ?>">
          <div class="card-header timerrrrrr d-none">
            <h3 class="card-title "><span class="float-left mr-3"> <?php echo lang('count_down'); ?>: </span>
               <span class="text-danger timerrr" data-seconds-left=<?php echo xss_clean($left_time); ?> > &nbsp; </span>
              <section class='actions'></section>
            </h3>
          </div>
 
          <div class="card-body">
            <p class="mb-3"><?php echo lang('question_palette'); ?> : </p>
            <?php 
              $i = 0;
              foreach($quiz_question_data as $quiz_question_array) 
              { 
                $i++;
                $attemp = isset($quiz_question_array['status']) ? $quiz_question_array['status'] : 'btn-gray';

                $attemp = $attemp == 'visited' ? '#495057' : ($attemp == 'mark' ? '#495057' : ($attemp=='answer' && $this->settings->answered_question_color_code ? $this->settings->answered_question_color_code : ($attemp == 'mark-answer' && $this->settings->mark_for_answer_color_code ? $this->settings->mark_for_answer_color_code : '#495057')));
                $previous_button_disable_link = (isset($quiz_data['is_previous_disable']) && $quiz_data['is_previous_disable'] == 1) ? 'javascript:void(0)' : base_url('test/').$quiz_data['id'].'/'.$i;
              ?>
                <a href="<?php echo $previous_button_disable_link; ?>" class="btn ml-1 mb-2 question_no text-white" style="background-color: <?php echo $attemp; ?>" > <?php echo xss_clean($i) ?></a>
            <?php 
              } 
            ?>
          </div>
          <div class="card-footer p-0"></div>
          <ul class="p-3" style="columns: 2;">
            <li>
              <?php if(isset($this->settings->answered_question) && $this->settings->answered_question == 'Enable') { 
                  $color_code = (isset($this->settings->answered_question_color_code) && $this->settings->answered_question_color_code) ? $this->settings->answered_question_color_code : '#495057';
              ?>
                  <span class="tag tag-green mr-2 mb-2 " style="background-color: <?php echo $color_code; ?>"><?php echo xss_clean($runn_answered); ?></span>
                  <span class="question-palette-right_side"><?php echo lang('answered_tag'); ?></span>
              <?php } ?> 
            </li>
            <li>  
              <?php if(isset($this->settings->not_answered_question) && $this->settings->not_answered_question == 'Enable') { 
                $color_code = (isset($this->settings->not_answered_question_color_code) && $this->settings->not_answered_question_color_code) ? $this->settings->not_answered_question_color_code : '#495057';
              ?>  
                  <span class="tag tag-red mr-3 mb-2 ml-auto" style="background-color: <?php echo $color_code; ?>"><?php echo xss_clean($runn_visited); ?></span>
                  <span><?php echo lang('not_answered_tag'); ?></span>
              <?php } ?>
            </li>
            <li>
              <?php if(isset($this->settings->total_attempt_show_or_not) && $this->settings->total_attempt_show_or_not == 'Enable') { 
                  $color_code = (isset($this->settings->total_attempt_color_code) && $this->settings->total_attempt_color_code) ? $this->settings->total_attempt_color_code : '#495057';
              ?>
                  <span class="tag tag-purple mr-2 mb-2" style="background-color: <?php echo $color_code; ?>"><?php echo xss_clean($runn_attemp); ?></span>
                  <span class="question-palette-right_side2" ><?php echo lang('total_attempt'); ?></span>
              <?php } ?>
            </li>
            <li>  
              <?php if(isset($this->settings->not_visit_show_or_not) && $this->settings->not_visit_show_or_not == 'Enable') { 
                  $color_code = (isset($this->settings->not_visit_color_code) && $this->settings->not_visit_color_code) ? $this->settings->not_visit_color_code : '#495057';
              ?>
                <span class="tag tag-gray mr-3 mb-2 ml-auto" style="background-color: <?php echo $color_code; ?>"><?php echo xss_clean($runn_not_visited); ?> </span> 
                <span><?php echo lang('not_visited_tag'); ?></span>
              <?php } ?>  
            </li>
            
            <li>
              <?php if(isset($this->settings->mark_for_review_show_or_not) && $this->settings->mark_for_review_show_or_not == 'Enable' && $is_previous_disable == false) {
                  $color_code = (isset($this->settings->mark_for_review_color_code) && $this->settings->mark_for_review_color_code) ? $this->settings->mark_for_review_color_code : '#495057';
              ?>
                <span class="tag tag-orange mr-2 mb-2" style="background-color: <?php echo $color_code; ?>"><?php echo xss_clean($runn_mark); ?></span>
                <span class="question-palette-right_side3"><?php echo lang('mark_for_review'); ?></span>
              <?php } ?> 
            </li>
            <li>  
              <?php if(isset($this->settings->mark_for_answer_show_or_not) && $this->settings->mark_for_answer_show_or_not == 'Enable'  && $is_previous_disable == false) {
                    $color_code = (isset($this->settings->mark_for_answer_color_code) && $this->settings->mark_for_answer_color_code) ? $this->settings->mark_for_answer_color_code : '#495057';
              ?> 
                <span class="tag tag-yellow mr-2 mb-2 ml-auto" style="background-color: <?php echo $color_code; ?>"><?php echo xss_clean($runn_mark_answer); ?></span> 
                <span><?php echo lang('mark_for_answer'); ?></span>
              <?php } ?>  
            </li> 
          </ul>

          <div class="card-footer">
            <div class="card-options">
              <a href="javascript:void(0)" class="btn btn-danger btn-block stop_this_quiz"> <?php echo lang('Stop This Quiz'); ?> </a> 
            </div> 
          </div>

        </div>
      </div>

    </div>
  </form>

</div>

<?php 
  $last_question = false;
  if($last_question_nbr == $question_id)
  {
    $last_question = true;
  }
  ?>

<script type="text/javascript">
  is_answered_or_disable = "<?php echo $is_answered_or_disable; ?>";
  var quiz_last_attempted_question_url = "<?php echo $quiz_last_attempted_question_url; ?>";
  var is_last_question_of_test = "<?php echo $last_question; ?>";


</script>