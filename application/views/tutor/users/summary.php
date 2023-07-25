<input type="hidden" class="not-attemp" value="<?php echo xss_clean($total_question) - xss_clean($total_attemp); ?>">
<input type="hidden" class="correct" value="<?php echo xss_clean($correct); ?>">
<input type="hidden" class="wrong-answer" value="<?php echo xss_clean($total_attemp) - xss_clean($correct); ?>">
<div class="container">
  <div class="row"> 
    <div class="col-12 text-center">
      <h2 class="heading">
        <?php 
          $lang_id = get_language_data_by_language($this->session->userdata('language'));
          $translate_quiz_title = get_translated_column_value($lang_id,'quizes',$quiz_data->id,'title');
          $quiz_title = $translate_quiz_title ? $translate_quiz_title : $quiz_data->title;
          $difficulty_star = get_category_stars($quiz_data->category_id, $participant_data['user_id']);
          
          echo ucfirst($quiz_title); ?> <?php echo lang('quiz_summary'); 
        ?>
          
        </h2>
        <hr>
    </div> 
  </div>
  <div class="row">
    <div class="col-md-12">
      <input type="button" class="print-btn btn float-right" value="print" />
    </div>
  </div>



  <div class="row" id="print-area">  
    <div class="clearfix "></div>  
    
    <div class="col-md-4">
     <?php echo lang('total_atemp_ques'); ?>  : <?php echo  $total_attemp; ?> / <?php echo xss_clean($total_question); ?>
    </div>  

    <div class="col-md-4">
      <?php 

      $marks_for_correct_answer = $participant_data['marks_for_correct_answer'];
      $marks_for_correct_answer = $marks_for_correct_answer > 0 ? $marks_for_correct_answer : 1;
      
      $wrong_answer = $total_attemp - $correct;
      $total_marks = $total_question * $marks_for_correct_answer;
      
      $negative_marking_percentage = isset($participant_data['negative_marking_percentage']) ? $participant_data['negative_marking_percentage'] : 0;

      $incorrect_marks = round($negative_marking_percentage * $marks_for_correct_answer * $wrong_answer / 100,2);

      $correct_marks = $correct * $marks_for_correct_answer;

      $marks_achived = $correct_marks - $incorrect_marks;


      $percent_marks = round(($marks_achived / $total_marks)*100,2);


      $result = "FAIL";
      if($quiz_data->passing <= $percent_marks)
      { 
        $result_status =  lang('pass');
        $result = "PASS";
      }
      else
      { 
        $result_status =  lang('fail');    
        $result = "FAIL"; 
      } 
      ?>
      <strong><?php echo lang('your_score'); ?> </strong> <?php echo $percent_marks;?>%
      <span class="text-success"><?php echo $result_status; ?></span>
    </div>

    <div class="col-md-4">
      <?php 
        $started = $participant_data['started'];
        $completed = $participant_data['completed'];
        
        $started = new DateTime($started);
        $completed = new DateTime($completed);
        $interval = $completed->diff($started);        
      ?>
      <strong> <?php echo lang('time_spend'); ?>  : - </strong> <?php echo sprintf("%02d", $interval->h).':'.sprintf("%02d", $interval->i).':'.sprintf("%02d", $interval->s) ; ?>
    </div>
    <hr />
    <div class="clearfix"></div>
    <div class="col-md-6">
      <canvas id="myChart" width="100%" height="50px"></canvas>
    </div>

    <div class="col-md-6">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tbody>
            <tr class="question">
              <td><span class="py-1 px-3 mr-2 bg-red"></span><?php echo lang('total_question'); ?></td>
              <td><?php echo xss_clean($total_question); ?></td>
            </tr>
            
            <tr class="answered">
              <td><span class="py-1 px-3 mr-2 bg-yellow"></span><?php echo lang('total_attem_ques'); ?> </td>
              <td><?php echo  $total_attemp ?></td>
            </tr>

            <tr class="correct">
              <td><span class="py-1 px-3 mr-2 bg-green"></span><?php echo lang('correct_answer'); ?></td>
              <td><?php echo xss_clean($correct); ?></td>
            </tr>

            <tr class="incorrect">
              <td><span class="py-1 px-3 mr-2 bg-orange"></span><?php echo lang('incorrect_answered'); ?></td>
              <td>
                <?php 
                    $wrong_answer = $total_attemp - $correct;
                    echo xss_clean($wrong_answer); 
                ?>
              </td>
            </tr>
            <tr class="notanswer">
              <td><span class="py-1 px-3 mr-2 bg-yellow"></span><?php echo lang('not_attempted'); ?></td>
              <td>
                <?php 
                    $notanswer = $total_question - $total_attemp;
                    echo xss_clean($notanswer); 
                ?>
               </td>
            </tr>
            <tr class="test_language">
              <td><span class="py-1 px-3 mr-2 bg-info"></span><?php echo lang('quiz_language'); ?></td>
              <td>
                <?php 

                    $test_language = isset($participant_data['test_language']) ? $participant_data['test_language'] : 'English';
                    echo xss_clean($test_language); 
                ?>
               </td>
            </tr>

            <tr class="marks_for_correct_answer">
              <td><span class="py-1 px-3 mr-2 bg-primary"></span><?php echo lang('marks_for_correct_answer'); ?></td>
              <td>  <?php  echo xss_clean($marks_for_correct_answer);  ?> </td>
            </tr>
            <?php 
            if($negative_marking_percentage > 0)
            {
              ?>
                <tr class="test_language bg-warning text-white">
                  <td><span class="py-1 px-3 mr-2 bg-white"></span><?php echo lang('negative_marking_percantage'); ?></td>
                  <td>
                    <?php  echo xss_clean($negative_marking_percentage)."%";   ?>
                   </td>
                </tr>
              <?php
            }
            ?>


            <tr class="test_language <?php echo $result == "PASS" ? 'bg-success' : 'bg-danger'; ?> text-white">
              
              <td colspan="2">
                <h6 class="text-center"><?php  echo lang("got")." ".$marks_achived." ".lang('out_of')." ".$total_marks; ?><h6>
               </td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>


    <?php 
      if($participant_data['reward_percentage'] > 0 AND $percent_marks >= $participant_data['reward_percentage']) 
      {
        ?>
        <div class="col-12 text-center w-100 my-5">
          <img width="300" class="thumbmail img-thumbmail text-center" src="<?php echo base_url('/assets/images/logo/').$this->settings->quizzy_reward_animation_image ?>">
        </div>
        <?php
      }
    ?>

    <?php
      $db_quiz_levels_data = json_decode($participant_data['quiz_levels_data']);
      $db_quiz_levels_data = json_decode(json_encode($db_quiz_levels_data), true);

      $level_names_array = isset($db_quiz_levels_data['level_names_array']) ? $db_quiz_levels_data['level_names_array'] : array();
      $level_names_array = is_array($level_names_array) ? $level_names_array : array();
      
      $level_marks_array = isset($db_quiz_levels_data['level_marks_array']) ? $db_quiz_levels_data['level_marks_array'] : array();
      $level_marks_array = is_array($level_marks_array) ? $level_marks_array : array();
      
      $level_common_array = isset($db_quiz_levels_data['level_common_array']) ? $db_quiz_levels_data['level_common_array'] : array();
      $level_common_array = is_array($level_common_array) ? $level_common_array : array();
      
      $quiz_grading_id = $participant_data['quiz_grading_id'] ? $participant_data['quiz_grading_id'] : 0;
      if($quiz_grading_id > 0)
      { 
        $search = $percent_marks;
        $your_level_name = "NO-LEVEL";
        $new_level_common_array = $level_common_array;
        krsort($new_level_common_array);
        foreach ($new_level_common_array as $level_marks_total => $level_name_by_mark) 
        {
          if ($level_marks_total == $search OR $search > $level_marks_total) 
          {
            $your_level_name = $level_name_by_mark;
            break;
          }
        }
        ksort($new_level_common_array);
        ?>
       
        <div class="col-md-12 my-5"><div class="clearfix"></div></div> 
        <div class="col-md-12 my-5">
          <div class="table-responsive level_table_section">
            <table class="table table-bordered m-0">
              <tbody>
                <tr>
                  <th class="h4 text-info"><?php echo lang('level_name'); ?></th>
                  <?php
                  foreach ($new_level_common_array as $key => $value) 
                  { 

                   $class_name_td = $your_level_name == $value ? "text-white bg-success " : "";
                   $title_name_td = $your_level_name == $value ? "Your Level Is : $value" : "";
                   $class_name_td_icon = $your_level_name == $value ? "<i class='far fa-check-circle ml-2 text-white'></i>" : "";
                    ?>
                      <td title="<?php echo $title_name_td; ?>" class="<?php echo $class_name_td; ?>"><?php echo $value; ?> <?php echo $class_name_td_icon; ?></td>
                    <?php
                  } ?>
                </tr>
                <tr>
                  <th class="h4 text-info"><?php echo lang('levael_marks'); ?></th>
                  <?php
                  $keys = array_keys($new_level_common_array);
                  // foreach ($new_level_common_array as $key => $value) 
                  foreach(array_keys($keys) AS $k )
                  { 
                    
                    $value = $new_level_common_array[$keys[$k]];
                    $key = $keys[$k];

                    $nextkey = isset($keys[$k+1]) ? $keys[$k+1] : NULL;

                    $next_level_marks = $nextkey ? " - ".($nextkey - 1) : "+";

                    $class_name_td = $your_level_name == $value ? "text-white bg-success" : "";
                    $class_name_td_icon = $your_level_name == $value ? "<i class='far fa-check-circle ml-2 text-white'></i>" : "";
                    $title_name_td = $your_level_name == $value ? "Your Level Is : $value" : "";

                    ?>
                      <td  title="<?php echo $title_name_td; ?>"  class="<?php echo $class_name_td; ?>" ><?php echo $key; ?> <?php echo $next_level_marks; ?> <?php echo $class_name_td_icon; ?></td>
                    <?php
                  } ?>
                </tr>
              </tbody>
            </table>
          </div>

        </div> 

        <?php
      } ?>

    <div class="clearfix"></div>




    <div class="clearfix"></div>
    <div class="col-md-12 my-5">
      <div class="table100 py-0 ver1  m-b-110 mobile-table">
      
        <table>
          <thead>
            <tr class="row100 head "> 
              <th class="cell100 result-column1 py-5 "><?php echo lang('questions'); ?></th>
              <th class="cell100 result-column2"><?php echo lang('goven_answer'); ?></th>
              <th class="cell100 result-column3"><?php echo lang('correct_answer'); ?></th>
              <th class="cell100 result-column4"><?php echo lang('status'); ?></th>
              <th class="cell100 result-column5"><?php echo lang('detail'); ?></th>
            </tr>
          </thead>

          <tbody>
            <?php 
            $l = 0;
            foreach ($user_question_data as  $question_data) 
            {

              $question_type_is_match = $question_data['question_type_is_match'];
              $given_correct_choice = json_decode($question_data['correct_choice']);
              if($question_type_is_match == "YES")
              {

                $actual_correct_choices = json_decode(json_encode($given_correct_choice), true);
                // p($actual_correct_choices);
                $display_order_array = isset($given_correct_choice->display_order_array) ? $given_correct_choice->display_order_array : array();
                $araay_to_display_match = isset($given_correct_choice->araay_to_display_match) ? $given_correct_choice->araay_to_display_match : array();
                $arr_is_correct = isset($given_correct_choice->arr_is_correct) ? $given_correct_choice->arr_is_correct : array();
                $submit_answer_array = isset($given_correct_choice->submit_answer_array) ? $given_correct_choice->submit_answer_array : array();
                $arr_is_correct = json_decode(json_encode($arr_is_correct), true);
                $araay_to_display_match = json_decode(json_encode($araay_to_display_match), true);
                $display_order_array = json_decode(json_encode($display_order_array), true);
                $submit_answer_array = json_decode(json_encode($submit_answer_array), true);

                $given_correct_choice = $arr_is_correct;
                $given_correct_choice_string = implode(',', $arr_is_correct);
                $user_answer_string = implode(',', $submit_answer_array);

              }

              $l++;
              ?>
              <tr class="row100 body botder-bottom">
                
                <td class="cell100 result-column1 py-5"><?php echo sprintf("%02d", $l); ?>. <?php echo htmlspecialchars(($question_data['question'])); ?>
                </td>
                
                <td class="cell100 result-column2"> 
                  <?php 

                  $given_answer_array = json_decode($question_data['given_answer']);
                  $given_answer_array = json_decode(json_encode($given_answer_array), true);

                  if($given_answer_array)
                  {
                    if(is_array($given_answer_array))
                    {
                      $given_answer_array_string = implode(',', $given_answer_array);
                    }
                    else
                    {
                      $given_answer_array_string = $given_answer_array;                    
                    }
                      echo $given_answer_array_string;
                  }
                  else
                  {
                    echo " - ";
                  }
                  ?> 


                </td>
                
                <td class="cell100 result-column3"> 


                  <?php 
                  if($question_type_is_match == "YES")
                  {
                    echo $given_correct_choice_string;
                  }
                  else
                  {
                    $given_correct_choice = json_decode($question_data['correct_choice']);
                  

                    if($given_correct_choice)
                    {
                      $given_correct_choice = json_decode(json_encode($given_correct_choice), true);
                      $correct_choice_string = implode(',', $given_correct_choice);
                      ?>
                        <li><?php echo htmlspecialchars($correct_choice_string); ?></li>
                      <?php 
                    }
                    else
                    {
                      echo " - ";
                    }
                  }

                  ?> 
                </td>

                <td class="cell100 result-column4">
                  <?php
                  if($question_data['is_correct'] == 1 && $given_answer_array )
                  {
                    ?>
                    <a class="badge btn-success text-white badge-xs"><?php echo lang('correct'); ?></a> 
                  <?php
                  }
                  elseif($question_data['is_correct'] == 0 && $given_answer_array)
                  {
                  ?>
                    <a class="badge btn-danger text-white badge-xs"><?php echo lang('wrong'); ?></a>
                  <?php
                  }
                  else
                  {
                  ?>
                    <a class="badge btn-warning  text-white badge-xs"><?php echo lang('not_attempted'); ?></a>
                  <?php
                  }
                  ?>
                </td>
                <td class="cell100 result-column5">
                  <?php
                  if($question_data['is_correct'] == 1 && $given_answer_array)
                  {
                    ?>
                    <a class="btn btn-info text-white btn-xs" data-toggle="modal" data-target="#Modal_<?php echo xss_clean($question_data['id']); ?>"><?php echo lang('view_answer'); ?></a>
                  <?php } 
                    elseif($question_data['is_correct'] == 0 && $given_answer_array)
                    {
                  ?>
                      <a class="btn btn-info text-white btn-xs" data-toggle="modal" data-target="#Modal_<?php echo xss_clean($question_data['id']); ?>"><?php echo lang('view_answer'); ?></a>  
                  <?php
                  }
                  else
                  {
                  ?>   
                      <a class="btn btn-info text-white btn-xs" data-toggle="modal" data-target="#Modal_<?php echo xss_clean($question_data['id']); ?>"><?php echo lang('view_answer'); ?></a>
                    <?php
                  }
                  ?>   
                </td>
              </tr>
              <?php
            } 
            ?>
          </tbody>
        </table>
      
    </div>
  </div>
</div>

<?php foreach ($user_question_data as  $question_data) {  ?>
  <div class="modal fade bd-example-modal-lg" id="Modal_<?php echo xss_clean($question_data['id']); ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-custom">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo lang('question_detail'); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span class="d-none" aria-hidden="true">×</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="left_content">
            <ul>
              <strong> <?php echo lang('question'); ?> </strong>
              <?php if(isset($question_data['question']) && !empty($question_data['question'])) { ?>
                <div class="question-area">
                    <textarea readonly><?php echo $question_data['question']; ?></textarea>  
                </div>
              <?php } ?>
              <?php 
                $question_img = isset($question_data['image']) ? $question_data['image'] : NULL;
                if($question_img)
                {
                  ?>
                  <div class="questions_img">
                    <img src="<?php echo base_url('assets/images/questions/').$question_img; ?>" class="image">
                  </div>
                  <?php
                }
              ?>
              <ul class="py-4">
                <?php 
                  $choices_arr = json_decode($question_data['choices']);
                  $correct_choice = json_decode($question_data['correct_choice']);
                  $given_answer = json_decode($question_data['given_answer']);
                  $question_status = lang('not_attempted_this_questions');
                  foreach ($choices_arr as  $choices_val) 
                  {
                ?>
                  <li>                  
                    <?php
                    if($question_data['is_correct'] == 1)
                    { 
                      $question_status = lang('right_answer');
                      $checked = '';
                      $text_color = '';

                      foreach ($correct_choice as  $valueee) 
                      {
                        if(empty($checked))
                        {                        
                          $checked = $choices_val == $valueee ? 'checked' : '';
                          $text_color = $choices_val == $valueee ? 'text-success' : '';
                        }
                      }
                      ?>
                        <div class="custom-control custom-checkbox right_answer">
                          <input type="checkbox" class="custom-control-input" id="question_<?php echo xss_clean($question_data['id']); ?>" <?php echo xss_clean($checked); ?> >
                          <label class="custom-control-label <?php echo xss_clean($text_color); ?>" for="question_<?php echo (xss_clean($question_data['id'])); ?>"> <?php echo htmlspecialchars($choices_val); ?></label>
                        </div>

                      <?php
                    }
                    elseif($given_answer && $question_data['is_correct'] == 0)
                    { 
                      $question_status = lang('wrong_answer');
                      $checked = '';
                      $checked = '';
                      $text_color = '';
                      $wrong_checked = '';

                      foreach ($given_answer as  $valueee) 
                      {
                        if(empty($wrong_checked))
                        {                        
                          $wrong_checked = $choices_val == $valueee ? 'checked' : '';
                          $text_color = $choices_val == $valueee ? 'text-danger wrong_check' : '';
                        }
                      }

                      foreach ($correct_choice as  $valueee) 
                      {
                        if(empty($checked))
                        {
                          $checked = $choices_val == $valueee ? 'checked' : '';
                        }
                      }
                      ?>
                        <div class="custom-control custom-checkbox wrong_answer">
                          <input type="checkbox" class="custom-control-input" id="question_<?php echo xss_clean($question_data['id']); ?>" <?php echo xss_clean($checked); ?> <?php echo xss_clean($wrong_checked); ?> >
                          <label class="custom-control-label <?php echo xss_clean($text_color); ?>" for="question_<?php echo (xss_clean($question_data['id'])); ?>"> <?php echo htmlspecialchars($choices_val); ?></label>
                        </div>
                      <?php
                    }
                    else
                    { 
                      $question_status = lang('not_attemp_question');
                      $checked = '';
                      $text_color = '';

                      foreach ($correct_choice as  $valueee) 
                      {
                        if(empty($checked))
                        {
                          $checked = $choices_val == $valueee ? 'checked' : '';
                        }
                      }
                      ?>
                        <div class="custom-control custom-checkbox notanswer">
                          <input type="checkbox" class="custom-control-input" id="question_<?php echo xss_clean($question_data['id']); ?>" <?php echo xss_clean($checked); ?> >
                          <label class="custom-control-label <?php echo xss_clean($text_color); ?>" for="question_<?php echo (xss_clean($question_data['id'])); ?>"> <?php echo htmlspecialchars($choices_val); ?></label>
                        </div>
                      <?php
                    }
                    ?>
                  </li>
                    <?php
                } ?>
              </ul>
            </ul>
            <label class="result">
              <?php 
              if($question_data['is_correct'] == 1 && $given_answer_array )
              {
                ?>
                <a class="badge btn-success text-white badge-xs rounded-10"><?php echo lang('correct'); ?></a> 
              <?php
              } 
              elseif($question_data['is_correct'] == 0 && $given_answer_array)
              {
              ?>
                <a class="badge btn-danger text-white badge-xs rounded-10"><?php echo lang('wrong'); ?></a>
              <?php
              }
              else
              {
              ?>
                <a class="badge btn-warning  text-white badge-xs"><?php echo lang('not_attempted'); ?></a>
              <?php
              }
              ?>
            </label>
            <?php if($question_data['solution']) { ?>
              <label class="result w-100">
                <?php echo lang('question_solution'); ?> <div class="solution-area"><textarea readonly><?php echo $question_data['solution']; ?></textarea></div>
              </label>
            <?php } ?>
            <?php 
              $solution_img = isset($question_data['solution_image']) ? $question_data['solution_image'] : NULL;
              
              if($solution_img)
              {
                ?>
                <div class="questions_img">
                  <img src="<?php echo base_url('assets/images/questions/solution/').$solution_img; ?>" alt="><?php echo $question_data['solution']; ?>" class="image">
                </div>
                <?php
              }
            ?>        
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close'); ?></button>
          </div>

        </div>
      </div>
    </div>
  </div>
  <?php
} ?>

