<?php
$quiz_running = 'no_quiz_start';
$session_quiz_id = NULL;
$quiz_running_btn = NULL;
$user_id = isset($this->user['id']) ? $this->user['id'] : NULL; 

$gradients = array();
$gradients[] = 'mask gradient-vue';
$gradients[] = 'mask gradient-angular';
$gradients[] = 'mask gradient-react';
$gradients[] = 'mask gradient-material';
$gradients[] = 'mask gradient-html';
$gradients[] = 'mask gradient-laravel';
$gradients[] = 'mask gradient-react-native';
$gradients[] = 'mask gradient-nuxtjs';

if($session_quiz_data && $session_quiz_question_data) 
{ 
    $quiz_running = 'quiz_running';
    $session_quiz_id = $session_quiz_data['id'];
    echo "<input type='hidden' value='".$session_quiz_id."' class='session_quiz_id'>";        
}
if($quiz_list_data)  
{
    foreach ($quiz_list_data as  $quiz_array) 
    {

        if($quiz_array->is_sheduled_test == 1) 
        {
            $start_date_time_code = $quiz_array->start_date_time;
            $end_date_time_code = $quiz_array->end_date_time;

            if($end_date_time_code < strtotime(date("Y-m-d H:i:s")))
            {
                //continue;
            }
        }

        $price = $quiz_array->price > 1 ? '₹ '.$quiz_array->price : ' '.lang('free');
        $start_quiz_link = $quiz_array->price > 1 ? get_admin_setting('paid_currency') ." ".$quiz_array->price : ' '.lang('free');
        $quiz_id = $quiz_array->id;
        $is_quiz_amout_payed = (isset($paid_quizes_array[$quiz_id]) && $paid_quizes_array[$quiz_id]) ? TRUE : FALSE;
        $ribbon_label = ($quiz_array->price > 0  OR $quiz_array->is_premium == 1 ) ? 'paid' : 'free';
        $quiz_price_ribbon = ($quiz_array->price > 0) ? get_admin_setting('paid_currency') ." " .$quiz_array->price : ($quiz_array->is_premium == 1 ? "Premium" : 'Free');


        $login_user_id = isset($this->user['id']) ? $this->user['id'] : NULL;

        if($is_premium_member == FALSE)
        {

            $user_category_membership = get_user_category_membership($login_user_id,$quiz_array->category_id);
            if($user_category_membership) 
            {
                $is_premium_member = ($user_category_membership->validity && $user_category_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
            }
        }

        
        if($login_user_id == $quiz_array->user_id)
        {
            $is_quiz_amout_payed =  true;
            $is_premium_member =true;
        }


        if($session_quiz_id && $session_quiz_id == $quiz_id)
        {
            $quiz_running_btn = $quiz_running;
            $quiz_url = base_url("test/$session_quiz_id/1");
            $quiz_btn_name = lang('resume_test');
            $quiz_action_icon = '<i class="fab fa-rev"></i>';
            $quiz_lock_icons = '<i class="fas fa-unlock-alt"></i>';
            $quiz_action_btn_class = 'btn-danger';
        }
        else
        {
            if(empty($user_id))
            {
                if($quiz_array->price > 0 OR $quiz_array->is_premium == 1 OR $quiz_array->is_registered == 1)
                {
                    $quiz_running_btn = "";
                    $quiz_url = base_url("login");
                    $quiz_btn_name = lang('login_please');
                    $quiz_action_icon = '<i class="fas fa-user"></i>';
                    $quiz_lock_icons = '<i class="fas fa-lock "></i>';
                    $quiz_action_btn_class = 'btn-danger';

                }
                else
                {
                    $quiz_running_btn = "no_quiz_start";
                    $quiz_url = base_url("instruction/quiz/$quiz_id");
                    $quiz_btn_name = lang("start_quiz");
                    $quiz_action_icon = '<i class="far fa-play-circle"></i>';
                    $quiz_lock_icons = '<i class="fas fa-unlock-alt"></i>';
                    $quiz_action_btn_class = 'btn-primary';
                }
            }
            else
            {
                $quiz_running_btn = "";
                if($quiz_array->price > 0 && $is_quiz_amout_payed == FALSE)
                {
                    $quiz_url = base_url("quiz-pay/payment-mode/quiz/$quiz_id");
                    $quiz_btn_name = lang('pay_now');
                    $quiz_action_icon = '<i class="fas fa-money-bill"></i>';
                    $quiz_lock_icons = '<i class="fas fa-lock "></i>';
                    $quiz_action_btn_class = 'btn-info';
                }
                else if($quiz_array->is_premium == 1 && $is_premium_member == FALSE)
                {
                    $quiz_url = base_url("membership");
                    $quiz_btn_name = lang('get_membership');
                    $quiz_action_icon = '<i class="fas fa-user-shield"></i>';
                    $quiz_lock_icons = '<i class="fas fa-lock "></i>';
                    $quiz_action_btn_class = 'btn-warning';
                }
                else
                {
                    $quiz_url = base_url("instruction/quiz/$quiz_id");
                    $quiz_btn_name = lang("start_quiz");
                    $quiz_action_icon = '<i class="far fa-play-circle"></i>';
                    $quiz_lock_icons = '<i class="fas fa-unlock-alt"></i>';
                    $quiz_action_btn_class = 'btn-primary';
                    
                }
            }
        }
                                          


        $lang_id = get_language_data_by_language($this->session->userdata('language'));
        $translate_quiz_title = get_translated_column_value($lang_id,'quizes',$quiz_id,'title');
        $quiz_title = $translate_quiz_title ? $translate_quiz_title : $quiz_array->title;
        $quiz_title = strlen($quiz_title) > 30 ? substr($quiz_title,0,30)."..." : $quiz_title;
        $quiz_user_name = $quiz_array->first_name.' '.$quiz_array->last_name;
        $quiz_user_name = strlen($quiz_user_name) > 20 ? substr($quiz_user_name,0,20)."..." : $quiz_user_name;
        $quiz_user_name = trim($quiz_user_name) ? trim($quiz_user_name) : "Admin";

        $like_or_not = (isset($quiz_array->like_id) && $quiz_array->like_id ) ? 'text-success' : 'text-dark';
        $like_or_not_bottom = (isset($quiz_array->like_id) && $quiz_array->like_id) ? 'text-success' : 'text-white';   
        $total_like = (isset($quiz_array->total_like) && $quiz_array->total_like) ? $quiz_array->total_like : 0;
        $average = 0;
        if($quiz_array->total_rating > 0 && $quiz_array->rating > 0)
        {
            $average = $quiz_array->total_rating / $quiz_array->rating;
        }  ?>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4"> 
            
            <div class="card card-bundle mb-2" data-turbolinks="false"> 
                <div class="thumbnail"> 
                    <span class="maskk gradient-defaultt <?php echo xss_clean($gradients[mt_rand(0,7)]); ?>"> </span> 
                    <a href="<?php echo xss_clean($quiz_url); ?>" data-url="<?php echo xss_clean($quiz_url); ?>" id="quiz_<?php echo xss_clean($quiz_array->id);?>" class="thumb-cover  <?php echo xss_clean($quiz_running_btn); ?> statrt_quiz_btn" data-quiz_id="<?php echo xss_clean($quiz_array->id);?>" data-toggle="tooltip"  title="<?php  echo $quiz_btn_name;?>"> &nbsp; </a>
                    <div class="details card-dark-shadow">
                            
                            <div class="row">
                                <div class="col-10">
                                    <section>
                                        <div class="ribbons-wrapper">
                                            <div class="ribbon">
                                                <span class="ribbon3 <?php echo $ribbon_label;?>">
                                                    <?php echo $quiz_price_ribbon ;?>
                                                </span>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                                <div class="col-2 text-right">
                                    <?php echo $quiz_lock_icons; ?>                       
                                </div>
                            </div>

                            <div class="framework-logo"> 
                                <div class="row quiz_middle_icon">
                                    <div class="col-6 text-center">
                                        <i class="far fa-question-circle"> </i> <br>
                                        <span class="value"><?php echo xss_clean($quiz_array->number_questions); ?> </span> <br>
                                        <?php echo lang('questions') ?>
                                    </div>

                                    <div class="col-6 text-center">
                                        <i class="fas fa-stopwatch"></i>   <br>
                                        <span class="value"><?php echo xss_clean($quiz_array->duration_min); ?></span><br>
                                        <?php echo lang('minutes') ?>
                                    </div>
                                </div>
                            </div> 


                            <div class="quiz_icons">
                                <a href="javascript:void(0)" class="icon float-left text-white-im"><i class="fe fe-eye mr-1"></i> 
                                    <span class="value"><?php echo xss_clean($quiz_array->total_view);?></span>
                                </a>

                                <a href="javascript:void(0)" class="icon inline-block ml-3 float-right like-quiz like_unlike_quiz like_quiz_view_box_b_<?php echo $quiz_array->id;?> <?php echo $like_or_not_bottom ;?>" data-quiz_id="<?php echo xss_clean($quiz_array->id);?>" >
                                    <i class="fav_icon fas fa-heart <?php echo $like_or_not_bottom ;?> like_quiz_view_i_b_<?php echo $quiz_array->id;?>"></i> 
                                    <span class="value like_quiz_view_span_b_<?php echo $quiz_array->id;?> <?php echo $like_or_not_bottom ;?>"> <?php echo xss_clean($total_like);?></span>
                                </a>
                            </div>

                    </div> 

                        <div class="actions"> 
                            <a href="<?php echo xss_clean($quiz_url); ?>" data-url="<?php echo xss_clean($quiz_url); ?>" id="quiz_<?php echo xss_clean($quiz_array->id);?>" class="btn btn-neutral btn-fill <?php echo xss_clean($quiz_running_btn); ?> statrt_quiz_btn" data-quiz_id="<?php echo xss_clean($quiz_array->id);?>" data-toggle="tooltip"  title="<?php  echo $quiz_btn_name;?>">
                                <?php echo $quiz_action_icon; ?>                    
                            </a> 
                            <?php 
                            if($quiz_array->leader_board == 1)
                            { ?>
                                <a href="<?php echo base_url("quiz/leader-board/$quiz_id") ?>" class="btn btn-neutral btn-fill" data-toggle="tooltip"  title="<?php echo lang('leader_board'); ?>"> 
                                    <i class="fas fa-poll"></i> 
                                </a> 
                                <?php 
                            } ?>



                            <a class="btn btn-neutral btn-fill like_unlike_quiz like_quiz_view_box_<?php echo $quiz_array->id;?> <?php echo $like_or_not ;?>" data-quiz_id="<?php echo xss_clean($quiz_array->id);?>" data-toggle="tooltip"  title="<?php echo lang('like'); ?>">
                                
                                <i class="fav_icon fas fa-heart <?php echo $like_or_not;?> like_quiz_view_i_<?php echo $quiz_array->id;?>"></i> 
                                 <span class="value like_quiz_view_span_<?php echo $quiz_array->id;?> <?php echo $like_or_not ;?>"> <?php echo xss_clean($total_like);?></span>
                            </a>

                            

                        </div>
                </div> 
            </div>
            <?php
                $quiz_slug_url = base_url('quiz/').slugify_string($quiz_title)."-$quiz_id";
             ?>
            <div class="pt-3 infobox">
                <div class="quiz-single-title translate_quiz_title">
                    <a href="<?php echo xss_clean($quiz_slug_url); ?>" class="text-dark"><?php echo xss_clean($quiz_title); ?>
                        
                    </a>
                </div>

                <div class="text-black-im user-list-name">
                    <?php echo xss_clean($quiz_user_name); ?> 
                </div>



                <?php $average_rating = (isset($average) && !empty($average) ? round($average) : 0); ?>

                <?php
                    $one_star   = $average_rating >= 1 ? "fill-star" : "empty-star";
                    $two_star   = $average_rating >= 2 ? "fill-star" : "empty-star";
                    $three_star = $average_rating >= 3 ? "fill-star" : "empty-star";
                    $four_star  = $average_rating >= 4 ? "fill-star" : "empty-star";
                    $five_star  = $average_rating >= 5 ? "fill-star" : "empty-star";
                ?>
                  <div class="average-star p-0 float-left">
                    <span><i class="fa fa-star <?php echo $one_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $two_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $three_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $four_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $five_star;?>" aria-hidden="true"></i></span>
                     
                  </div>





                <a href="<?php echo xss_clean($quiz_url); ?>" data-url="<?php echo xss_clean($quiz_url); ?>" id="quiz_<?php echo xss_clean($quiz_array->id);?>" class=" <?php echo xss_clean($quiz_running_btn); ?> statrt_quiz_btn btn btn-sm w-100 <?php echo $quiz_action_btn_class; ?>" data-quiz_id="<?php echo xss_clean($quiz_array->id);?>" data-toggle="tooltip"  title="<?php echo $quiz_btn_name;?>" > <?php echo $quiz_action_icon." ".$quiz_btn_name;?>
                    
                </a>
                <div class="clearfix"></div>
            </div> 

        </div>
         
        <?php 
    } 
}
else 
{
    ?>
    <div class="col-12 text-center text-danger"> <?php echo lang('no_quiz_found'); ?></div>
    <?php 
} ?>      