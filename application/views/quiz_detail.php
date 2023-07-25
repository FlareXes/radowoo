<style>

.upcoming{
	width:200px;
    font-size: 30px;
    color: #000000;
    display: inline-flex;
    background-color: #ffffff;
}
a.disabled {
  pointer-events: none;
  cursor: default;
}

</style>
<div class="container">
	<div class="row">
    	<div class="col-md-12">
      		<div class="card mt-5">
      			<div class="card-body">
      				
      			<?php 
				    $userimage_dir = base_url("assets/images/user_image/");

      				$quiz_id = $quiz_data->id;
      				$user_id = isset($this->user['id']) ? $this->user['id'] : NULL; 
					$left_time = $start_quiz_btn_disable = '';
      				$quiz_featur_image_arr = $quiz_data->featured_image ? json_decode($quiz_data->featured_image) : array();
      				$quiz_featur_image_arr = is_array($quiz_featur_image_arr) ? $quiz_featur_image_arr : array();
      				$quiz_featur_image = NULL;
      				if(count($quiz_featur_image_arr))
      				{
	      				$quiz_featur_image_index = array_rand($quiz_featur_image_arr);
      					$quiz_featur_image = $quiz_featur_image_arr[$quiz_featur_image_index];
      				}
      				
  				  	$current_quiz_image = $quiz_featur_image ? $quiz_featur_image : "default.jpg";
			        $current_quiz_image_name = $current_quiz_image ; 
			        $quiz_image = base_url("assets/images/quiz/").$current_quiz_image_name;
			        
			        if(!is_file(FCPATH."assets/images/quiz/".$current_quiz_image_name))
			        {
        				$quiz_image = base_url("assets/default/quiz.png"); 
			        } 

			        $user_image = $user_quiz_data->image ? $user_quiz_data->image : "default.jpg";
			        $user_profile_image = $user_image ; 
			        $user_image = $userimage_dir.$user_profile_image;
			        
			        if(!is_file(FCPATH."assets/images/user_image/".$user_profile_image))
			        {
			          $user_image = base_url('assets/default/user.png');
			        } 
			        

			        $like_or_not = (isset($quiz_data->like_id) && !empty($quiz_data->like_id) ? 'text-success' : 'text-white');
			        $price = ($quiz_data->price > 1) ? $this->settings->paid_currency." " .$quiz_data->price : (($quiz_data->is_premium == 1) ? " ".lang('premium') : ' '.lang('free'));
			        $start_quiz_link = $quiz_data->price > 1 ? '₹ '.$quiz_data->price : ' '.lang('free');
			        $is_quiz_amout_payed = (isset($paid_quizes_array[$quiz_id]) && $paid_quizes_array[$quiz_id]) ? TRUE : FALSE;
			        $current_url = current_url();
			        $leader_board_url = ($quiz_data->leader_board == 1) ? base_url("quiz/leader-board/$quiz_id") : "javascript:void(0)";
	        		

					if($quiz_data->is_sheduled_test == 1) 
			        {
			            $start_date_time_code = $quiz_data->start_date_time;
			            $end_date_time_code = $quiz_data->end_date_time;

			            if($end_date_time_code < strtotime(date("Y-m-d H:i:s")))
			            {
			                //continue;
			            }
					}
					
					if($quiz_data->is_sheduled_test == 1 && $quiz_data->start_date_time != 0 && $quiz_data->end_date_time != 33168837600)
					{
						
						$current_date_time = date('Y-m-d h:i:s a');
						$current_date_time = strtotime($current_date_time);
						$new_current_time = $current_date_time + ($this->settings->display_countdown_before_starting_quiz * 60 * 60);
						// $start_date_time = date('Y-m-d h:i:s a',$quiz_array->start_date_time);
						// $end_date_time = date('Y-m-d h:i:s a',$quiz_array->end_date_time);
						
						if($quiz_data->start_date_time > $current_date_time)
						{
							$left_time = $quiz_data->start_date_time - $current_date_time;
							$quiz_running_btn = "no_quiz_start";
							$quiz_url = "";
							$quiz_btn_name = "";
							//$quiz_action_icon = '<i class="far fa-play-circle"></i>';
							$quiz_lock_icons = '';
							$quiz_timer = 'btn-primary timerrr';
						}
					}

			        $quiz_running = lang('quiz_running');
			        $session_quiz_id = NULL;
					$quiz_running_btn = NULL;

					if($session_quiz_data && $session_quiz_question_data) 
					{ 
					    $quiz_running = 'quiz_running';
					    $session_quiz_id = $session_quiz_data['id'];
					    echo "<input type='hidden' value='".$session_quiz_id."' class='session_quiz_id'>";        
					}

			        if($session_quiz_id && $session_quiz_id == $quiz_id)
			        {
			            $quiz_running_btn = $quiz_running;
			            $quiz_url = base_url("test/$session_quiz_id/1");
			            $quiz_btn_name = lang('resume_test');
			            $quiz_action_icon = '<i class="fab fa-rev"></i>';
			        }
			        else
			        {
			            if(empty($user_id))
			            {
			                if($quiz_data->price > 0 OR $quiz_data->is_premium == 1 OR $quiz_data->is_registered == 1)
			                {
			                    $quiz_running_btn = "";
			                    $quiz_url = base_url("login");
			                    $quiz_btn_name = lang('login_please');
			                    $quiz_action_icon = '<i class="fas fa-user"></i>';
			                }
			                else
			                {
								$start_quiz_btn_disable = ($left_time > 0) ? 'disabled' : '';
								$quiz_running_btn = "no_quiz_start";
								$quiz_url = base_url("instruction/quiz/$quiz_id");
								$quiz_btn_name = lang("start_now");
								$quiz_action_icon = '<i class="far fa-play-circle"></i>';
			                }
			            }
			            else
			            {
			                $quiz_running_btn = "";
			                if($quiz_data->price > 0 && $is_quiz_amout_payed == FALSE)
			                {
			                    $quiz_url = base_url("quiz-pay/payment-mode/quiz/$quiz_id");
			                    $quiz_btn_name = lang('pay_now');
			                    $quiz_action_icon = '<i class="fas fa-money-bill"></i>';
			                    $quiz_lock_icons = '<i class="fas fa-lock "></i>';
			                    $quiz_action_btn_class = 'btn-info';
			                }
			                else if($quiz_data->is_premium == 1 && $is_premium_member == FALSE)
			                {
			                    $quiz_url = base_url("membership");
			                    $quiz_btn_name = lang('get_membership');
			                    $quiz_action_icon = '<i class="fas fa-user-shield"></i>';
			                }
			                else
			                {
								$start_quiz_btn_disable = ($left_time > 0) ? 'disabled' : '';
								$quiz_url = base_url("instruction/quiz/$quiz_id");
								$quiz_btn_name = lang("start_quiz");
								$quiz_action_icon = '<i class="far fa-play-circle"></i>';			                    
			                }

			            }
			        }
					
			       $background_arr[] =  "rgb(193 142 68 / 68%)";
			       $background_arr[] =  "rgb(191 68 193 / 68%)";
			       $background_arr[] =  "rgb(193 68 68 / 68%)";
			       $background_arr[] =  "rgb(68 193 112 / 68%)";
			       $background_arr[] =  "rgb(70 68 193 / 68%)";
			       $background_arr[] =  "rgb(55 137 185 / 68%)";
			       $background_arr[] =  "rgb(14 20 23 / 68%)";
			       $background_arr[] =  "rgb(28 57 66 / 68%)";
			       $background_arr_key = array_rand($background_arr);
			       $background_background = $background_arr[$background_arr_key];
			                                          
      			?>


      				<div class="detail_page_header w-100">
	      				<div class="row">
	      					<div class="col-12">
	      						<img  src="<?php echo $quiz_image; ?>" class="w-100 detail_page_header_img">
	      					</div>
	      				</div>
	      				
	      				<div class="w-100 h-100 detail_page_header_abslute" style="background: <?php echo $background_background; ?>">

		      				<div class="detail_page_header_sidebar w-100">
		      					<div class="detail_page_header_abslute_sidebar">
		      						<div class="row">
		      							
		      							<div class="col-12 text-right pt-1">
	      										<p class="btn btn-neutral my-2 mt-3 mx-4 btn-fill like_unlike_quiz a sidebar_box" data-quiz_id="<?php echo $quiz_id;?>">
	      											<i class="fav_icon fas fa-heart a like_quiz_view_i_b_<?php echo $quiz_id;?>  <?php echo ($like_or_not);?>" ></i><br>
	      											<span class="w-100 mb-0 small a like_quiz_view_span_b_l_<?php echo $quiz_id;?> <?php echo ($like_or_not);?>"><?php echo $quiz_data->total_like; ?> Like</span>
	      										</p>
	      								</div>

		      							<div class="col-12 text-right">

	      										<p class="btn btn-neutral btn-fill my-2 mx-4 sidebar_box">
	      											<label class="w-100 mb-0 small"><i class="fas fa-hand-holding-usd"></i></label><br>
	      											<label class="w-100 mb-0 small"> <?php echo $price; ?></label><br>
	      										</p>
	      								</div>

		      							<div class="col-12 text-right">

	      										<p class="btn btn-neutral btn-fill my-2 mx-4 sidebar_box quiz_sharing_buttons">
	      											<label class="w-100 mb-0 small"><i class="fas fa-star"></i></label><br>
	      											<label class="w-100 mb-0 small"><?php echo $average_rating; ?></label><br>
	      										</p>
	      								</div>

		      							<div class="col-12 text-right">

	      										<p class="btn btn-neutral my-2 mx-4 btn-fill sidebar_box">
	      											<label class="w-100 mb-0 small"><i class="fas fa-eye"></i></label><br>
	      											<label class="w-100 mb-0 small"><?php echo $quiz_data->total_view; ?> View</label><br>
	      										</p>
	      								</div>


		      						</div>
	      						</div>
		      				</div>
		      				
		      				<div class="row m-0 p-5">
		      					<div class="col-12">
		      						<div class="row">
		      							<div class="col-1">
		      								<img class='quiz_user_image' height="50px" width="50px" src="<?php echo $user_image; ?>">
		      							</div>
		      							<div class="col-6 my-auto">
		      								<h6 class="text-white w-100 mb-2"><?php echo $user_quiz_data->first_name." ".$user_quiz_data->last_name ?></h6>
		      								<h6 class="text-white w-100 mb-0"><?php echo $user_quiz_data->email; ?></h6>
		      							</div>
		      						</div>


		      						<div class="row">
		      							<div class="col-12">		      								
		      								<h1 class="text-uppercase text-white text-center"><?php echo $quiz_data->title; ?></h1>
		      							</div>
		      						</div>
		      					

		      						<div class="row mb-5">
		      							<div class="col-12 mb-5 text-center">		      								
		      								<label class="badge badge-light large text-dark rounded mr-4">Questions <span class="badge badge-danger"><?php echo xss_clean($quiz_data->number_questions); ?></span></label>
		      								<label class="badge badge-light large text-dark rounded">Duration <span class="badge badge-danger"><?php echo xss_clean($quiz_data->duration_min); ?> Min</span></label>
		      							</div>
		      						</div>
		      							      					

		      						<div class="row mt-5">
		      							<div class="col-12 mt-5 text-center mx-auto">		      								
		      								<a href="<?php echo $quiz_url; ?>" class="btn btn-light text-info btn-lg text-uppercase <?php echo $quiz_running_btn; ?> "><?php echo $quiz_btn_name; ?> </a>
		      								<?php 
	      								 	if($user_id && isset($this->user['role']) && ($this->user['role'] == 'admin' OR $this->user['role'] == 'subadmin'))
							                {
							                	?>
							                	<a href="<?php echo base_url('admin/quiz/update/').$quiz_id; ?>" class="btn btn-info text-white btn-lg text-uppercase "><?php echo lang('manage_quiz'); ?> </a>
							                	<?php
							                }

	      								 	if($user_id && $user_id == $quiz_data->user_id && isset($this->user['role']) && $this->user['role'] == 'tutor')
							                {
							                	?>
							                	<a href="<?php echo base_url('tutor/quiz/update/').$quiz_id; ?>" class="btn btn-info text-white btn-lg text-uppercase "><?php echo lang('manage_quiz'); ?> </a>
							                	<?php
							                }
		      								?>
		      							</div>
										<?php if($left_time > 0) { ?>
											<div class="col-12  text-center mx-auto">
												<span class="upcoming my-2 <?php echo $quiz_timer; ?>" data-seconds-left="<?php echo $left_time; ?>"></span>
											</div>
										<?php } ?>	  
		      						</div>

		      					</div>


		      				</div>
		      				
	      				</div>

	      				<div class="w-100 share_icon_detail_page">
	      					

	      						<div class="row mx-0 share_box">
	      							<div class="col-12  px-5">
										<ul class="social_detail_page d-flex ">
						                  <li class="px-1 h4">
						                  	<a href="line://msg/text/<?php echo $current_url; ?>" class="fab fa-line" target="_blank"></a>
						                  </li>
						                  
						                  <li class="px-1 h4">
						                  	<a href="tg://msg?text=<?php echo str_replace(' ', '+', $quiz_data->title); ?> on <?php echo $current_url; ?>" class="fab fa-telegram" target="_blank"></a>
						                  </li>

						                  <li class="px-1 h4">
						                  	<a href="//web.whatsapp.com/send?text= <?php  echo str_replace(' ', '+', $quiz_data->title); ?> on <?php  echo $current_url; ?>" class="fab fa-whatsapp" target="_blank"></a>

						                  </li>

						                  <li class="px-1 h4">
						                  	<a href="//www.facebook.com/sharer.php?u=<?php echo $current_url; ?>" class="fab fa-facebook" target="_blank"></a>
						                  </li>

						                  <li class="px-1 h4">
						                  	<a href="//twitter.com/share?text=<?php echo str_replace(' ', '+', $quiz_data->title); ?>&url=<?php echo $current_url; ?>" class="fab fa-twitter" target="_blank"></a>
						                  </li>

						               </ul>
	      							</div>
	      						</div>
	      					
	      				</div>
	      			</div>


			        <div class="row">
				    	<div class="col-12"> <hr> </div>
				    </div>



				    <div class="row">
				    	<div class="col-12">
				    		

				            <ul class="nav nav-tabs m-0 p-0 bg-white" id="qui-detail-Tab" role="tablist">
				               <li class="nav-item">
				                  <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="false"><?php echo lang('description'); ?></a>
				               </li>
				               <li class="nav-item ">
				                  <a class="nav-link " id="quiz-reviews-tab" data-toggle="tab" href="#quiz-reviews" role="tab" aria-controls="reviews" aria-selected="true"><?php echo lang('reviews'); ?></a> 
				               </li>

				               <li class="nav-item ">
				                  <a class="nav-link " id="quiz-leaderboard-tab" data-toggle="tab" href="#quiz-leaderboard" role="tab" aria-controls="leaderboard" aria-selected="true"><?php echo lang('leader_board'); ?></a> 
				               </li>

				            </ul>

				             <div class="tab-content" id="qui-detail-TabContent">
				               
				              <div class="tab-pane fade active show" id="description" role="tabpanel" aria-labelledby="description-tab">
				                  <div class="bg-white card-primary">
				                    
				                    <div class="card-body">
				                      <div class="row">
				                        <div class="col-12 px-5">
				                        	<?php echo $quiz_data->description; ?>
				                        </div>
				                      </div>
				                    </div>    
				                  </div>  
				              </div>
				              
				              <div class="tab-pane fade " id="quiz-reviews" role="tabpanel" aria-labelledby="quiz-reviews-tab">
				                  <div class="bg-white card-primary">
				                    <!-- <div class="card-header"> -->
				                      <!-- <h4><?php  //echo lang('quiz_reviews'); ?></h4> -->
				                    <!-- </div> -->
				                    <div class="card-body">
				                     
									    <div class="row">
								    		<?php 
								    		if(empty($comments_exist_quizid_userid)) 
					                 		{ ?>
										     	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										        	<?php echo form_open('rating/'.$rel_type, array('role'=>'form',)); ?>
										        		<input type="hidden" name="quizid" class="quizid" value="<?php echo $quiz_data->id?>">
										        		<textarea class="form-control save-heading" name="ratingcontent" placeholder="Wrtie your comment here"></textarea>
										        		<section class='rating-widget'>
														  <!-- Rating Stars Box -->
														  <div class='rating-stars'>
														    <ul id='stars'>
														      <li class='star' data-toggle="tooltip" data-placement="bottom" title="Poor" data-value='1'>
														        <i class='fa fa-star fa-fw'></i>
														      </li>
														      <li class='star' data-toggle="tooltip" data-placement="bottom" title='Fair' data-value='2'>
														        <i class='fa fa-star fa-fw'></i>
														      </li>
														      <li class='star' data-toggle="tooltip" data-placement="bottom" title='Good' data-value='3'>
														        <i class='fa fa-star fa-fw'></i>
														      </li>
														      <li class='star' data-toggle="tooltip" data-placement="bottom" title='Excellent' data-value='4'>
														        <i class='fa fa-star fa-fw'></i>
														      </li>
														      <li class='star' data-toggle="tooltip" data-placement="bottom" title='WOW!!!' data-value='5'>
														        <i class='fa fa-star fa-fw'></i>
														      </li>
														    </ul>
														  </div>
														  <span class="small text-danger"> <?php echo strip_tags(form_error('title')); ?> </span>
														  <div class='success-box'>
														    <div class='text-message'></div>
														    <div class='clearfix'></div>
														  </div>
														</section>
						                                <input type="hidden" class="rate" name="reviewstar" value="">
						                                <input type="submit" name="save" value="Save" class="btn btn-primary btn-lg">
										        	<?php form_close();?>
										        </div> 
										        <div class="col-12"> <hr> </div>

										        <?php 
								    		} ?> 



									        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							                	<?php
								                    $one_star   = $average_rating >= 1 ? "fill-star" : "empty-star";
								                    $two_star   = $average_rating >= 2 ? "fill-star" : "empty-star";
								                    $three_star = $average_rating >= 3 ? "fill-star" : "empty-star";
								                    $four_star  = $average_rating >= 4 ? "fill-star" : "empty-star";
								                    $five_star  = $average_rating >= 5 ? "fill-star" : "empty-star";
							                	?>
							                	<h4 class="average-text w-100 px-4">Average Rating </h4>
							                  	<div class="average-star p-0 float-left px-4">
								                    <span><i class="fa fa-star <?php echo $one_star;?>" aria-hidden="true"></i></span>
								                    <span><i class="fa fa-star <?php echo $two_star;?>" aria-hidden="true"></i></span>
								                    <span><i class="fa fa-star <?php echo $three_star;?>" aria-hidden="true"></i></span>
								                    <span><i class="fa fa-star <?php echo $four_star;?>" aria-hidden="true"></i></span>
								                    <span><i class="fa fa-star <?php echo $five_star;?>" aria-hidden="true"></i></span>
								                    <span class="ml-4">(<?php echo $average_rating;?>)</span>
							                  	</div>
									        </div> 


									        

									    </div>

									    <div class="row">
									    	<div class="col-12"> <hr> </div>
									    </div>

									    <?php   
					                    $count = 0;
					                    foreach($quiz_comments as $comment_key => $comment_value)
					                    {
					                      	$count ++;
					                      	
					               			?>
										    <div class="comment-list pb-3">
											    <div class="row">
											    	<div class="col-md-1">
											    		<?php 						    								    		
											    		$userimage = (isset($comment_value->image) && $comment_value->image) ? $comment_value->image : "default.png";

														$real_image_name = $userimage ; 
												        $userimage = $userimage_dir.$real_image_name;
												        if(!is_file(FCPATH."assets/images/user_image/".$real_image_name))
												        {
												          $userimage = base_url('assets/images/user_image/avatar-1.png');
												        }

											    		?>

					                           			<div class="user-image"><img src="<?php echo $userimage;?>"></div>
											    	</div>
											    	<div class="col-md-2">
											    		<div class="user-name"><?php echo $comment_value->first_name. ' ' .$comment_value->last_name;?></div>
											    		<div class="comment-date"><?php echo get_date_or_time_formate($comment_value->added);?></div>
											    	</div>
											    	<div class="col-lg-3">
											    		<div class="averate-number-main">
											    			<div class="detail-average-star">
											    				<?php if($comment_value->rating >= 1) { ?>
					                                    			<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
					                                 			<?php } else { ?> 
					                                 				<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
					                                 			<?php } if($comment_value->rating >= 2) {?>	
					                                 				<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
					                                 			<?php } else { ?>   
					                                    			<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
					                                    		<?php } if($comment_value->rating >= 3) {  ?>   
					                                    			<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
					                                 			<?php } else { ?>   
					                                    			<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>	
					                                    		<?php } if($comment_value->rating >= 4) { ?>
					                                    			<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
					                                 			<?php } else { ?>   
					                                    			<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
					                                    		<?php } if($comment_value->rating >= 5) { ?>
								                                    <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
								                                <?php } else { ?>   
								                                    <span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
								                                <?php } ?>	
											    			</div>
											    		</div>
											    	</div>
											    	<div class="col-lg-6">
											    		<div class="comment-text"><p><?php echo $comment_value->review_content;?></p></div>
											    		<?php
								                            if(get_user_review_like($comment_value->id))
								                            {
								                            	$liked_or_not = 'review-like';
								                            } 
								                            else
								                            {
								                                $liked_or_not = 'review-not-visit';
								                            }
								                        ?>
											    		<div class="evet thumbs-up ">
											    			<i class="fa fa-thumbs-up <?php echo $liked_or_not;?>" id="change-color_<?php echo $comment_value->id; ?>" aria-hidden="true" data-review_id="<?php echo $comment_value->id;?>" data-rel_type="<?php echo $comment_value->rel_type;?>"></i>
											    			<span class="total-likes"></span>
											    		</div>
											    	</div>
											    </div>
											</div>

											<?php 
										} ?>

				                    </div>    
				                  </div>  
				              </div>

				              <div class="tab-pane fade " id="quiz-leaderboard" role="tabpanel" aria-labelledby="quiz-leaderboard-tab">
				                  <div class="bg-white card-primary">
				                   
				                    <div class="card-body">
				                     
									    <div class="row">
									    	
										    <?php 
										      if($leader_board_quiz_history)
										      {
										    ?>
										      <div class="col-12 my-5">
										        <div class="table100 ver1  m-b-110">
										          <div class="table100-head ">
										            <table>
										              <thead>
										                <tr class="row100 head">
										                  <th class="cell100 column1"><?php echo lang('name') ?></th>
										                  <th class="cell100 column2"><?php echo lang('attended') ?></th>
										                  <th class="cell100 column3"><?php echo lang('correct') ?></th>
										                  <th class="cell100 column4"><?php echo lang('date') ?></th>
										                  <th class="cell100 column5"><?php echo lang('score') ?></th>
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
										                  foreach ($leader_board_quiz_history as $ind => $quiz_array) 
										                  {
										                    $first_name  = $quiz_array->first_name ? $quiz_array->first_name : $quiz_array->guest_name;
										                    $full_name_of_user = $first_name. ' '.$quiz_array->last_name;
										                    $name_of_user = (strlen($full_name_of_user) > 30) ? substr($full_name_of_user, 0, 30).'...' : $full_name_of_user ;
										                    $started = get_date_or_time_formate($quiz_array->started);
										                    $date_of_exam = get_date_formate($quiz_array->started);

										                    $duration_min = $quiz_data->duration_min;
										                    $completed_time = $quiz_array->completed;
										                    $score = 0;
										                    if($quiz_array->correct > 0)
										                    {
										                      $score = ($quiz_array->correct/$quiz_array->questions)*100;
										                      $score = round($score, 2);
										                    }

										                    if($completed_time)
										                    {          
										                      $completed = get_date_or_time_formate($completed_time);
										                    }
										                    else
										                    {
										                      $complete_count = strtotime("+$duration_min minutes", strtotime($started));
										                      $completed = date("d M Y , h:i:s", $complete_count);
										                      $completed = get_date_or_time_formate($completed);
										                    }


										                    $rank_array['name_of_user'] = $name_of_user;
										                    $rank_array['total_attemp'] = $quiz_array->total_attemp ? $quiz_array->total_attemp : 0;
										                    $rank_array['correct'] = $quiz_array->correct ;
										                    $rank_array['date_of_exam'] = $date_of_exam;
										                    $rank_array['score'] = $score;

										                    $rank_array_data[$ind] = $rank_array;
										                  }

										                  foreach($rank_array_data as $k=>$v) {
										                    $sort['score'][$k] = $v['score'];
										                  }

										                  array_multisort($sort['score'], SORT_DESC, $rank_array_data);          
										                  $last_score = 0;

										                  foreach ($rank_array_data as $key => $value_array) 
										                  {
										                    if($value_array['score'] != $last_score)
										                    {
										                      $i++;
										                    }
										                ?>
										                  <tr class="row100 body">
										                    <td class="cell100 column1"><?php echo xss_clean($value_array['name_of_user']); ?></td>
										                    <td class="cell100 column2"><?php echo xss_clean($value_array['total_attemp']); ?></td>
										                    <td class="cell100 column3"><?php echo xss_clean($value_array['correct']); ?></td>
										                    <td class="cell100 column4"><?php echo xss_clean($value_array['date_of_exam']); ?></td>
										                    <td class="cell100 column5"><?php echo xss_clean($value_array['score']); ?> %</td>
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
										      <?php
										        }
										        else
										        {
										      ?>
										        <div class="col-12 text-center text-danger"> <?php echo lang('no_quiz_given'); ?> </div>
										      <?php
										        }
										      ?>
									    </div>

									    <div class="row">
									    	<div class="col-12"> <hr> </div>
									    </div>


				                    </div>    
				                  </div>  
				              </div>


            				</div>

				    	</div>
				    </div>




      			</div>
    		</div>
    	</div>
    </div>
</div>



