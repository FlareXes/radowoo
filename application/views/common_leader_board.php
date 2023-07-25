<div class="container">
  <div class="row">
    <div class="col-12 text-center"> <h2 class="heading"><?php echo ucwords(lang('common_leader_board')); ?></h2> <hr></div>
    <?php 
      if($site_leader_board_quiz_history)
      {
    ?>
	    <div class="col-12 my-5">
	        <div class="table100 ver1  m-b-110">
	        	<div class="table100-head ">
	            	<table>
	              		<thead>
	              			<tr class="row100 head">
			                  <th class="cell100 column1"><?php echo lang('name') ?></th>
			                  <th class="cell100 column5"><?php echo lang('total_points') ?></th>
			                  <th class="cell100 column5"><?php echo lang('score') ?></th>
			                  <th class="cell100 column5"><?php echo lang('accuray') ?></th>
			                  <th class="cell100 column5"><?php echo lang('Time Taken') ?><span class="small d-none"> H:M:S</span></th>
			                  <th class="cell100 column5"><?php echo lang('rank') ?></th>
			                </tr>
	              		</thead>
	          		</table>
	          	</div>
	          	<div class="table100-body js-pscroll ps ps--active-y">
	          		<table>
              			<tbody>
              				<?php
			                  $i = 0;
			                  $rank_array_data = array();
			                foreach ($site_leader_board_quiz_history as $ind => $quiz_array) 
			                {
			                  	$first_name  = $quiz_array->first_name ? $quiz_array->first_name : $quiz_array->guest_name;
			                    $full_name_of_user = $first_name. ' '.$quiz_array->last_name;
			                    $name_of_user = (strlen($full_name_of_user) > 30) ? substr($full_name_of_user, 0, 30).'...' : $full_name_of_user ;
			                    $started = date( "d M Y , h:i:s", strtotime($quiz_array->started));
			                    $completed_time = $quiz_array->completed;
			                    $score = $quiz_array->score;
			                    
			                    $score = number_format((float)$score, 2, '.', '');
			                    $rank_array['name_of_user'] = $name_of_user;
			                    $rank_array['total_earned'] = $quiz_array->total_earned;
			                    $rank_array['total_attemp'] = $quiz_array->total_attemp ? $quiz_array->total_attemp : 0;
			                    $rank_array['correct'] = $quiz_array->correct ;
			                    $rank_array['date_of_exam'] = $started;
			                    $rank_array['score'] = $score;
			                    $rank_array['accuray'] = number_format((float)$quiz_array->accuray, 2, '.', '');
			                    $rank_array['take_time_for_exam'] = $quiz_array->take_time_for_exam;
			                    $rank_array['age'] = $quiz_array->age;

			                    $rank_array_data[$ind] = $rank_array;
			                }    
		                    foreach($rank_array_data as $k=>$v) 
		                    {
			                    $sort['score'][$k] = $v['score'];
			                }

				                        
                  				$last_score = 0;

                  				foreach ($rank_array_data as $key => $value_array) 
				                {
				                    
				                    $i++;
				                    $t = $value_array['take_time_for_exam'];
				                    
			                		?>  
			                	<tr class="row100 body">
			                		<td class="cell100 column1"><?php echo xss_clean($value_array['name_of_user']); ?></td>
			                		<td class="cell100 column5"><?php echo xss_clean($value_array['total_earned']); ?></td>
				                    <td class="cell100 column5"><?php echo xss_clean($value_array['score']); ?> %</td>
				                    <td class="cell100 column5"><?php echo xss_clean($value_array['accuray']); ?> %</td>
				                    <td class="cell100 column5"><?php echo sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60); ?> </td>
				                    <td class="cell100 column5"><strong><?php echo xss_clean($i); ?></strong></td>
			                	</tr>
			                <?php 
			                    $last_score = $value_array['score'];     
			                  }
			                ?>		
              			</tbody>
              		</table>
	          	</div>
	        </div>
	    </div>
	<?php }
		else
        {
    ?>
    		<div class="col-12 text-center text-danger"> <?php echo lang('no_quiz_given'); ?> </div>
    <?php
        }
    ?>
  </div>
</div>