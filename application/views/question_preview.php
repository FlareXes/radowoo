<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
	$lang_id = get_language_data_by_language($this->session->userdata('language'));
    $question_type_is_match = isset($question_data['question_type_is_match']) ? $question_data['question_type_is_match'] : "NO";
    $footer_btn = ($this->settings->is_compatible == 'MOBILE') ? 'footer-button' : '';
    $is_previous_disable = (isset($question_data['is_previous_disable']) && $question_data['is_previous_disable'] == 1) ? true : false; 
    
?>
<div class="row">
	<div class="col-lg-12">
	 	<div class="card">
        	<div class="card-header">
            	<div class="container-fluid w-100">
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
		                          <textarea readonly class="running_quiz_question w-100"><?php echo $translate_question_title2; ?></textarea>  
		                      </div>
		                    <?php } ?>               
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
				        <?php } else {
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

			                    if($question_data['queston_choies_type'] == "text")
			                    {
			                      ?>
			                      <label id="queston_choies_type_text_id" name="answer[]" class="form-control answer_text_field"></label>
			                      <?php
			                    }
			                    else
			                    {
			                      ?>

			                      <label class="selectgroup-item btn-block">
			                        <input type="hidden" class="render-content" value="<?php echo $question_data['render_content']; ?>">
			                        <div class="selectgroup-button <?php echo $is_multiple_border;?>">
			                          <?php echo $choice_render; ?>
			                        </div>
			                      </label>
			                      <?php 
			                      $checked = '';   
			                    }
			                   } 
			                   if($question_data['is_multiple'] == 1) 
				                  { 
				               ?>
				                <div class="w-100 text-right text-warning"><?php echo lang('multiple_choice_question');?></div>
				               <?php } ?>
				        <?php } ?>    
            	</div>
      		</div>
      		<?php 
                $card_footer_compatible = ($this->settings->is_compatible == 'MOBILE') ? 'card_footer_compatible' : '';
            ?>
            <div class="card-footer <?php echo $card_footer_compatible; ?> <?php echo $footer_btn; ?>">
            <div class="card-options" style="display: flex;">
              
              <div class="next-previous-main <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'pre-main' : '';?>">
                <?php $previous_button_disable = (isset($question_data['is_previous_disable']) && $question_data['is_previous_disable'] == 1) ? 'd-none' : '';?>
                <div disable="disable" class="btn btn-sm btn-azure mr-2 preview_quiz <?php echo $previous_button_disable; ?>">
                  <i class="fe fe-chevron-left"></i><?php echo lang('previous_btn'); ?>
                </div>

                <div class="btn btn-sm btn-azure mr-2 next_quiz <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'pre-next' : '';?>" disable="disable"><?php echo lang('next_btn'); ?> 
                  <i class="fe fe-chevron-right"></i>
                </div>
              </div> 
              <?php if(isset($this->settings->mark_for_review_and_next) && $this->settings->mark_for_review_and_next == 'Enable' && $is_previous_disable == false) { ?>
                <div class="btn btn-sm btn-orange mr-2 ml-auto answer_given mark_or_next_quiz <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'mrk-nxt' : '';?>">
                  <i class="fe fe-check-circle"></i><?php echo lang('mark_for_review_and_next'); ?> 
                </div>
              <?php }  
              if($is_previous_disable == false)
              { ?>
              <div class="btn btn-sm btn-yellow mr-2 ml-auto mark_for_answer_and_next answer_given <?php echo ($this->settings->is_compatible == 'MOBILE') ? 'mrk-rev' : '';?>">
                <i class="fe fe-check-circle"></i><?php echo lang('mark_for_answer_and_next'); ?> 
              </div>
              <?php } ?>

              <div class="save-next-main">

                <div class="btn btn-sm btn-teal mr-2 answer_given save_or_next_quiz">
                  <i class="fe fe-save"></i> <?php echo lang('save_and_next'); ?>
                </div>

              </div> 

            </div>
            <?php if($question_data['is_previous_disable'] == 1 && isset($this->settings->previous_disable_note) && $this->settings->previous_disable_note) { ?>
              <div class="text-danger py-4"><span>***</span><?php echo lang('note').': '.$this->settings->previous_disable_note; ?></div>
            <?php } ?>  
          </div>
	 	</div>
	</div>
</div>