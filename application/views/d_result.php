<input type="hidden" class="not-attemp" data-color_code="<?php echo $color_code = (isset($this->settings->not_answered_question_color_code) && $this->settings->not_answered_question_color_code) ? $this->settings->not_answered_question_color_code : '#495057';?>" value="<?php echo xss_clean($total_question) - xss_clean($total_attemp); ?>">
<input type="hidden" class="correct" value="<?php echo xss_clean($correct); ?>">
<input type="hidden" class="wrong-answer" value="<?php echo xss_clean($total_attemp) - xss_clean($correct); ?>">

<div style="background-color: #fafdfb;">
	<h2 style="text-align: center; color: #6c757d; font-size: 35px; padding: 15px 0px;margin:0px 0px;">
		<?php
          
          $lang_id = get_language_data_by_language($this->session->userdata('language'));
          $translate_quiz_title = get_translated_column_value($lang_id,'quizes',$quiz_data->id,'title');
          $quiz_title = $translate_quiz_title ? $translate_quiz_title : $quiz_data->title;
          $difficulty_star = get_category_stars($quiz_data->category_id, $participant_data['user_id']);
          
          echo ucfirst($quiz_title); ?> <?php echo lang('quiz_summary'); 
        ?>
	</h2>
	<hr/>
	
	<!----  section first start --->
	<section style="width: 100%;">
		<!----  total attemp question start --->
		<div style="width: 33%;float: left;padding-right: 15px; padding-left: 15px;color: #6c757d;font-size: 14px;"><strong style="color: #6c757d;"><?php echo lang('total_atemp_ques'); ?>  :</strong> <?php echo  $total_attemp; ?> / <?php echo xss_clean($total_question); ?>
		</div>
		<!----  total attemp question end --->

		<!----  your score start --->
		<div style="width: 33%;float: left;padding-right: 15px; padding-left: 15px;">
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
		        $result_color = '#5eba00';
		      }
		      else
		      { 
		        $result_status =  lang('fail');    
		        $result = "FAIL";
		        $result_color = '#cd201f'; 
		      } 
	      ?>
	      <strong style="color: #6c757d;"><?php echo lang('your_score'); ?> : </strong> <span style="color: #6c757d;"><?php echo $percent_marks;?>%</span>
	      <span style="color:<?php echo $result_color; ?>"><?php echo $result_status; ?></span>	
		</div>
		<!----  your score end --->

		<!----  time spend start --->
		<div style="width: 25%;float: left;padding-right: 15px; padding-left: 15px;">
			<?php 
	        $started = $participant_data['started'];
	        $completed = $participant_data['completed'];
	        
	        $started = new DateTime($started);
	        $completed = new DateTime($completed);
	        $interval = $completed->diff($started);        
	      ?>
	      <strong style="color: #6c757d;"> <?php echo lang('time_spend'); ?>  :</strong> <span style="color: #6c757d;"><?php echo sprintf("%02d", $interval->h).':'.sprintf("%02d", $interval->i).':'.sprintf("%02d", $interval->s) ; ?></span>
		</div>
		<!----  time spend end --->
	</section>
	<!----  section first end --->
	<div style="clear: both;"></div>

	<!----  section second start --->
	<section style="width: 100%;margin: 20px 0px;">
		<?php $username = (isset($participant_data['first_name']) && $participant_data['first_name'] ? $participant_data['first_name']." ".$participant_data['last_name'] : ($participant_data['guest_name'] ? $participant_data['guest_name'] : '')); ?>
		<div style="width: 33%;float: left;padding-right: 15px; padding-left: 15px; color: #6c757d; font-size: 14px;margin-bottom:20px;">	<strong style="color: #6c757d;"><?php echo lang('name'); ?></strong> : <?php echo $username; ?>
		</div>
		<div style="width: 33%;float: left;padding-right: 15px; padding-left: 15px; color: #6c757d; font-size: 14px;margin-bottom:20px;">	<strong style="color: #6c757d;"><?php echo lang('date'); ?>  :</strong> <?php echo date('d M, Y h:i A',strtotime($participant_data['started'])); ?>
		</div>
		<div style="width: 25%;float: left;padding-right: 15px; padding-left: 15px;">
			<div style="width: 70%;float: left;color: #6c757d; font-size: 14px;margin-bottom:20px;">	<strong style="color: #6c757d;"><?php echo lang('passing_mark'); ?></strong> : <?php echo $participant_data['quiz_passing_marks'].'%'; ?></div>
		</div>
	</section>
	<div style="clear: both;">
	<!----  section second end --->


	<!----  section third start --->
	<section>
		<!----  question answer table start --->
		<table style="width:60%;border: 1px solid #dee2e6;margin: 0px auto;margin-top: 20px;border-collapse: collapse;">
			<tbody>
				<tr>
					<td style="width:20px;height:20px;background-color: #cd201f;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;padding-left: 10px;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo lang('total_question'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo xss_clean($total_question); ?></td>
				</tr>
				
				<tr>
					<td style="width:20px;height:20px;background-color: #888;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;padding-left: 10px;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo lang('total_attem_ques'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo  $total_attemp ?></td>
				</tr>
				
				<tr>
					<td style="width:20px;height:20px;background-color: #5eba00;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;padding-left: 10px;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo lang('correct_answer'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo xss_clean($correct); ?></td>
				</tr>
				
				<tr>
					<td style="width:20px;height:20px;background-color: #fd9644;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;padding-left: 10px;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo lang('incorrect_answered'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php 
	                    $wrong_answer = $total_attemp - $correct;
	                    echo xss_clean($wrong_answer); 
	                ?></td>
				</tr>
				
				<tr>
					<td style="width:20px;height:20px;background-color: #495057;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;padding-left: 10px;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo lang('not_attempted'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php 
	                    $notanswer = $total_question - $total_attemp;
	                    echo xss_clean($notanswer); 
	                ?></td>
				</tr>
				
				<tr>
					<td style="width:20px;height:20px;background-color: #3abaf4;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;padding-left: 10px;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php echo lang('quiz_language'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php 

	                    $test_language = isset($participant_data['test_language']) ? $participant_data['test_language'] : 'English';
	                    echo xss_clean($test_language); 
	                ?></td>
				</tr>
				
				<tr>
					<td style="width:20px;height:20px;background-color: #c0e798db;border-bottom: solid 1px #dee2e6;"></td>
					<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;padding-left: 10px;border-left: solid 1px #dee2e6;"><?php echo lang('marks_for_correct_answer'); ?></td>
	              	<td style="color: #6c757d;border-bottom: solid 1px #dee2e6;border-left: solid 1px #dee2e6;"><?php  echo xss_clean($marks_for_correct_answer);  ?></td>
				</tr>
				
				<?php 
	            if($negative_marking_percentage > 0)
	            {
	              ?>
	              <tr>
	              	<td style="width:20px;height:20px;background-color: #6c7ae0;border-bottom: solid 1px #dee2e6;color:#6c7ae0">asdf</td>
	              	<td style="color: #6c757d;padding-left: 10px;border-left: solid 1px #dee2e6;"><?php echo lang('negative_marking_percantage'); ?></td>
	              	<td style="color: #6c757d;border-left: solid 1px #dee2e6;"><?php  echo xss_clean($negative_marking_percentage)."%";   ?></td>
	              </tr>
	            <?php } ?> 
	           
			</tbody>
		</table>
		<!----  question answer table end --->
	</section>
	<!----  section third end --->
	<div style="clear: both;">

	<!----  section four start --->
	<section>
		<div style="width: 60%; margin: 0px auto;display: block; background-color: #fc544b; height: 30px; color: #ffffff;"><h6 style="text-align: center; padding: 6px 0px; font-size: 15px;"><?php  echo lang("got")." ".$marks_achived." ".lang('out_of')." ".$total_marks; ?><h6></div>
	</section>		
	<!----  section four end --->

	<!----  section five start --->
	<section>
		<table style="width: 100%;">
			<thead>
				<tr>
					<th style="font-size: 18px; color: #fff; line-height: 1.4; background-color: #6c7ae0;padding: 10px 0px;border-right: solid 1px #dee2e6;"><?php echo lang('questions'); ?></th>
					
					<th style="font-size: 18px; color: #fff; line-height: 1.4; background-color: #6c7ae0;padding: 10px 0px;"><?php echo lang('status'); ?></th>
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
					<tr>
						<?php     
		                    $question_title = $question_data['question'];
							$dom = new DOMDocument();
							@$dom->loadHTML(mb_convert_encoding($question_title, 'HTML-ENTITIES', 'UTF-8'));
							$p = $dom->getElementsByTagName('p');
							$images = $dom->getElementsByTagName('img');
							
							foreach ($images as $key => $image) {
									
							        $src = $image->getAttribute('src');
							        if(!empty($src))
							        {
							        	$type = pathinfo($src, PATHINFO_EXTENSION);

							        	$data = @file_get_contents($src);

							        	$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

							        	$image->setAttribute("src", $base64);
							        }

							}

							$html = $dom->saveHTML();
							
		                ?>
						<td style="border-bottom: solid 1px #dee2e6;padding-bottom: 20px;border-right: solid 1px #dee2e6;padding:0px 40px;"><?php echo $html; ?></td>
						
						
						<td style="border-bottom: solid 1px #dee2e6;padding-bottom: 20px;">
							<?php
							$given_answer_array = json_decode($question_data['given_answer']);
                    		$given_answer_array = json_decode(json_encode($given_answer_array), true);
		                    if($question_data['is_correct'] == 1 && $given_answer_array )
		                    {
		                      ?>
		                      <a style="box-shadow: 0 2px 6px #a8f5b4; background-color: #63ed7a; border-color: #63ed7a;color:#ffffff;padding: 5px 10px;"><?php echo lang('correct'); ?></a> 
		                    <?php
		                    }
		                    elseif($question_data['is_correct'] == 0 && $given_answer_array)
		                    {
		                    ?>
		                      <a style="color:#ffffff;box-shadow: 0 2px 6px #fd9b96; background-color: #fc544b; border-color: #fc544b;padding: 5px 10px;"><?php echo lang('wrong'); ?></a>
		                    <?php
		                    }
		                    else
		                    {
		                    ?>
		                      <a style="color:#ffffff;box-shadow: 0 2px 6px #ffc473; background-color: #ffa426; border-color: #ffa426;padding: 5px 10px;"><?php echo lang('not_attempted'); ?></a>
		                    <?php
		                    }
		                    ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</section>
	<!----  section five end --->

</div>	