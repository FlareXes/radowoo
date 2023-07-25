<div class="container-fluid">
	<div class="row">
    	<div class="col-md-12">
      		<div class="card mt-5">
      			<div class="card-body">
                    <input type="hidden" id="active_study_matrial_id" value="<?php echo $study_material_id ?>">
                    <input type="hidden" id="url_study_material_content_id" value="">

                        <?php 
                            $userimage_dir = base_url("assets/images/user_image/");
                            $s_m_id = $study_data->id;
                            $user_id = isset($this->user['id']) ? $this->user['id'] : NULL;

                            if($study_data->media_file)
                            {
                                $current_detail_image = base_url("media/").$study_data->media_file;
                                if(!is_file(FCPATH."media/".$study_data->media_file))
                                {
                                    $current_detail_image = base_url("assets/default/quiz.png"); 
                                } 
                            }
                            else
                            {
                                $current_sm_image = $study_data->image ? $study_data->image : "default.jpg";
                                $current_detail_image = base_url("assets/images/studymaterial/").$current_sm_image;
                                if(!is_file(FCPATH."assets/images/studymaterial/".$current_sm_image))
                                {
                                    $current_detail_image = base_url("assets/default/quiz.png"); 
                                } 
                            }
                           

                            $user_image = (isset($study_m_user_data->image) && $study_m_user_data->image) ? $study_m_user_data->image : "default.jpg";
                            $user_profile_image = $user_image; 
                            $user_image = $userimage_dir.$user_profile_image;
                            
                            if(!is_file(FCPATH."assets/images/user_image/".$user_profile_image))
                            {
                              $user_image = base_url('assets/default/user.png');
                            }
                            
                            $like_or_not = (isset($study_data->like_id) && $study_data->like_id ) ? 'text-success' : 'text-white';

                            $price = ($study_data->price > 1) ? $this->settings->paid_currency." " .$study_data->price : (($study_data->is_premium == 1) ? " ".lang('premium') : ' '.lang('free'));
                            $current_url = current_url();                    

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
                           $leader_board_url = "#";
                           $study_m_title = $study_data->title;
                           $real_url = base_url('study-material/').slugify_string($study_m_title)."-$s_m_id";
                           $enroll_now_url = base_url('study-content/').slugify_string($study_m_title)."-$s_m_id?enroll=1";
                           $is_s_m_amout_payed = (isset($paid_s_m_array[$s_m_id]) && $paid_s_m_array[$s_m_id]) ? TRUE : FALSE;




                            if($is_premium_member == FALSE)
                            {
                                $user_category_membership = get_user_category_membership($user_id,$study_data->category_id);
                                if($user_category_membership) 
                                {
                                    $is_premium_member = ($user_category_membership->validity && $user_category_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
                                }
                            }


                           if($user_id == $study_data->user_id)
                           {
                                $is_s_m_amout_payed = TRUE;
                                $is_premium_member = TRUE;
                           }

                            if(empty($user_id))
                            {
                                if($study_data->price > 0 OR $study_data->is_premium == 1 OR $study_data->is_registered == 1)
                                {
                                    $action_url = base_url("login");
                                    $action_btn_name = lang('login_please');
                                    $action_btn_icon = '<i class="fas fa-user"></i>';

                                }
                                else
                                {
                                    if($is_user_enrolled)
                                    {
                                        $action_url = $real_url;
                                        $action_btn_name = lang("view_content");
                                        $action_btn_icon = '<i class="far fa-eye"></i>';
                                    }
                                    else
                                    {
                                        $action_url = $enroll_now_url;
                                        $action_btn_name = lang("enroll_now");
                                        $action_btn_icon = '<i class="fas fa-user-lock"></i>';
                                    }
                                    
                                }
                            }
                            else
                            {
                                if($study_data->price > 0 && $is_s_m_amout_payed == FALSE)
                                {
                                    $action_url = base_url("quiz-pay/payment-mode/material/$s_m_id");
                                    $action_btn_name = lang('pay_now');
                                    $action_btn_icon = '<i class="fas fa-money-bill"></i>';
                                }
                                else if($study_data->is_premium == 1 && $is_premium_member == FALSE)
                                {
                                    $action_url = base_url("membership");
                                    $action_btn_name = lang('get_membership');
                                    $action_btn_icon = '<i class="fas fa-user-shield"></i>';
                                }
                                else
                                {
                                    if($is_user_enrolled)
                                    {
                                        $action_url = $real_url;
                                        $action_btn_name = lang("view_content");
                                        $action_btn_icon = '<i class="far fa-eye"></i>';
                                    }
                                    else
                                    {
                                        $action_url = $enroll_now_url;
                                        $action_btn_name = lang("enroll_now");
                                        $action_btn_icon = '<i class="fas fa-user-lock"></i>';
                                    }                         
                                }
                            }

                        ?>





                    <div class="detail_page_header w-100">
                        <div class="row">
                            <div class="col-12">
                                <img  src="<?php echo $current_detail_image; ?>" class="detail_page_header_img">
                            </div>
                        </div>
                        
                        <div class="w-100 h-100 detail_page_header_abslute" style="background: <?php echo $background_background; ?>">

                            <?php 
                            if($this->settings->study_material_display_stats_icons == "YES")
                            {  ?>

                            <div class="detail_page_header_sidebar w-100">
                                <div class="detail_page_header_abslute_sidebar">
                                    <div class="row">
                                        
                                        <div class="col-12 text-right pt-1">
                                                <p class="btn btn-neutral my-2 mt-3 mx-4 btn-fill like_study_material a sidebar_box" data-study_id="<?php echo $s_m_id;?>">
                                                    <i class="fav_icon fas fa-heart a like_quiz_view_i_b_<?php echo $s_m_id;?>  <?php echo ($like_or_not);?>" ></i><br>
                                                    <span class="w-100 mb-0 small a like_quiz_view_span_b_l_<?php echo $s_m_id;?> <?php echo ($like_or_not);?>"><?php echo $study_data->total_like; ?> Like</span>
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
                                                    <label class="w-100 mb-0 small"><?php echo $study_data->total_view; ?> View</label><br>
                                                </p>
                                        </div>

                                    </div>
                                </div>
                            </div> <?php
                            } ?>
                            
                            <div class="row m-0 p-5">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-3 col-md-1 col-sm-2 col-xl-1 col-lg-1">
                                            <img class='quiz_user_image' src="<?php echo $user_image; ?>">
                                        </div>
                                        <div class="col-9 col-md-11 col-sm-10 col-xs-8 col-xl-11 col-lg-11 my-auto">
                                            <h6 class="text-white w-100 mb-2"><?php echo isset($study_m_user_data->first_name) ? $study_m_user_data->first_name." ".$study_m_user_data->last_name : "";  ?></h6>
                                            <h6 class="text-white w-100 mb-0"><?php echo isset( $study_m_user_data->email) ?  $study_m_user_data->email : ""; ?></h6>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-12 my-3">                                            
                                            <h1 class="text-uppercase text-white text-center display-6"><?php echo $study_data->title; ?></h1>
                                        </div>
                                    </div>
                                

                                    <div class="row">
                                        <div class="col-12 mb-5 text-center">                                           
                                            
                                            <label class="badge badge-light large text-dark rounded mr-4"><?php echo lang('VIDEO'); ?> <span class="badge badge-danger"><?php echo $study_data->total_video; ?></span></label>
                                            
                                            <label class="badge badge-light large text-dark rounded mr-4"><?php echo lang('AUDIO'); ?> <span class="badge badge-danger"><?php echo $study_data->total_audio; ?></span></label>


                                            <label class="badge badge-light large text-dark rounded mr-4"><?php echo lang('IMAGES'); ?> <span class="badge badge-danger"><?php echo $study_data->total_images; ?></span></label>

                                            <label class="badge badge-light large text-dark rounded mr-4"><?php echo lang('PDF'); ?> <span class="badge badge-danger"><?php echo $study_data->total_pdf; ?></span></label>

                                            <label class="badge badge-light large text-dark rounded mr-4"><?php echo lang('DOC'); ?> <span class="badge badge-danger"><?php echo $study_data->total_doc; ?></span></label>

                                            <label class="badge badge-light large text-dark rounded "><?php echo lang('OTHER'); ?> <span class="badge badge-danger"><?php echo $study_data->total_other; ?></span></label>

                                        </div>
                                    </div>
                                                                

                                    <div class="row">
                                        <div class="col-12 text-center mx-auto">                                           
                                            <a href="<?php echo $action_url; ?>" id="study_data_contant_btn" class="btn btn-light text-info btn-md text-uppercase"><span class="mr-2"><?php echo $action_btn_icon; ?></span> <?php echo lang($action_btn_name); ?> </a>


                                            <?php 
                                            if($user_id && isset($this->user['role']) && ($this->user['role'] == 'admin' OR $this->user['role'] == 'subadmin'))
                                            {
                                                ?>
                                                <a href="<?php echo base_url('admin/study/update/').$s_m_id; ?>" class="btn btn-info text-white btn-md text-uppercase "><?php echo lang('manage_study_contant'); ?> </a>
                                                <?php
                                            }

                                            if($user_id && $user_id == $study_data->user_id && isset($this->user['role']) && $this->user['role'] == 'tutor')
                                            {
                                                ?>
                                                <a href="<?php echo base_url('tutor/study/update/').$s_m_id; ?>" class="btn btn-info text-white btn-md text-uppercase "><?php echo lang('manage_study_contant'); ?> </a>
                                                <?php
                                            }
                                            ?>



                                        </div>
                                    </div>

                                </div>


                            </div>
                            
                        </div>

                        <?php 
                        if($this->settings->study_material_social_share_icons == "YES")
                        {  ?>
                        <div class="w-100 share_icon_detail_page">
                            

                                <div class="row mx-0 share_box">
                                    <div class="col-12  px-5">
                                        <ul class="social_detail_page d-flex ">
                                          <li class="px-1 h4">
                                            <a href="line://msg/text/<?php echo $current_url; ?>" class="fab fa-line" target="_blank"></a>
                                          </li>
                                          
                                          <li class="px-1 h4">
                                            <a href="tg://msg?text=<?php echo str_replace(' ', '+', $study_data->title); ?> on <?php echo $current_url; ?>" class="fab fa-telegram" target="_blank"></a>
                                          </li>

                                          <li class="px-1 h4">
                                            <a href="//web.whatsapp.com/send?text= <?php  echo str_replace(' ', '+', $study_data->title); ?> on <?php  echo $current_url; ?>" class="fab fa-whatsapp" target="_blank"></a>

                                          </li>

                                          <li class="px-1 h4">
                                            <a href="//www.facebook.com/sharer.php?u=<?php echo $current_url; ?>" class="fab fa-facebook" target="_blank"></a>
                                          </li>

                                          <li class="px-1 h4">
                                            <a href="//twitter.com/share?text=<?php echo str_replace(' ', '+', $study_data->title); ?>&url=<?php echo $current_url; ?>" class="fab fa-twitter" target="_blank"></a>
                                          </li>

                                       </ul>
                                    </div>
                                </div>
                            
                        </div>
                        <?php
                        }
                        ?>
                    </div>


                    <div class="row">
                        <div class="col-12"> <hr> </div>
                    </div>


                    <?php


                        $attachment_dir = "./assets/uploads/study_material";
                        $attachment_dir_link = base_url("assets/uploads/study_material/");
                        
                        ?>
                        <div class="row studay_section_data">
                            
                            <div class="col-12 border">
                               
                            <?php 
                            if($study_material_section_data)
                            {
                                $i = 0;
                                foreach ($study_material_section_data as $s_m_section_data) 
                                { 
                                    $s_m_section_id = $s_m_section_data->id;
                                    $section_contant_data = get_study_section_contant($s_m_id,$s_m_section_id);
                                    $this_section_has_completed_contant_ids = get_user_completed_s_m_section_contant($study_material_id,$s_m_section_id,$user_id);
                                    $complete_count = $this_section_has_completed_contant_ids ? count($this_section_has_completed_contant_ids) : 0;
                                    $section_contant_data_arra = $section_contant_data ? json_decode(json_encode($section_contant_data), true) : array();
                                    $total_contant_count = count($section_contant_data_arra);

                                    $i++;
                                    $collapsed = "";
                                    $show = "";
                                    if($i == 1)
                                    {
                                        $collapsed = "collapsed";
                                        $show = "show";
                                    }
                                     ?>
                                    
                                    <div class="accordion" id="accordionExample">
                                        
                                        <div class="card mb-1">
                                            <div class="card-header p-0" id="heading_section_<?php echo $s_m_section_data->id; ?>">
                                                <h2 class="mb-0 w-100">
                                                    <button type="button" class="btn btn-link w-100 py-1 <?php echo $collapsed; ?>" data-toggle="collapse" data-target="#collapse_section_<?php echo $s_m_section_data->id; ?>">
                                                        <span class="float-left"><?php echo lang('section')." ".$i." : "; ?><?php echo $s_m_section_data->title; ?>
                                                        </span> 
                                                        <span class="float-right"><i class="fa fa-angle-up ml-5"></i></span>
                                                        <span class="clearfix"></span>
                                                        <div class="small w-100 float-left text-left">
                                                            <i class="fas fa-stopwatch mr-1"></i> 
                                                            <?php echo $s_m_section_data->total_duration." ".lang('min'); ?> 
                                                        </div>  
                                                    </button> 
                                                </h2>
                                            </div>

                                            <div id="collapse_section_<?php echo $s_m_section_data->id; ?>" class="collapse <?php echo $show; ?>" aria-labelledby="heading_section_<?php echo $s_m_section_data->id; ?>" data-parent="#accordionExample">
                                                <div class="border-bottom">
                                                    <?php
                                                        if($section_contant_data)
                                                        {
                                                            $j = 0;
                                                            $active_contant_section = "";
                                                            $chech_box_checked = "";
                                                            foreach ($section_contant_data as $data_section_contant) 
                                                            { 
                                                                $this_contant_url = base_url("study-material/").slugify_string($study_m_title)."-$study_material_id/$data_section_contant->id";

                                                                $j++;
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-check my-auto">
                                                                         
                                                                          <label class="form-check-label" forr="data_section_contant_<?php echo $data_section_contant->id ?>">
                                                                            <p  class="py-2 text-info no_underline w-100 p-0"><?php echo $j.".  ".$data_section_contant->title; ?></p>
                                                                            <p class="small"><i class="fas fa-stopwatch mr-1"></i> <?php echo $data_section_contant->duration." ".lang('min'); ?></p>
                                                                          </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                     ?>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <?php
                                }
                            }
                            ?>
                            </div>

                        </div>
                        <?php
                     ?>

			    </div>
			</div>
		</div>
        <div class="col-12 col-md-12">
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
                          <a class="nav-link " id="associate-quiz-tab" data-toggle="tab" href="#associate-quiz" role="tab" aria-controls="associate" aria-selected="true"><?php echo lang('associate quiz'); ?></a> 
                       </li>

                    </ul>


                    <div class="tab-content" id="qui-detail-TabContent">

                          <div class="tab-pane fade active show" id="description" role="tabpanel" aria-labelledby="description-tab">
                              <div class="bg-white card-primary">

                                <div class="card-body">
                                  <div class="row">
                                    <div class="col-12 px-5">
                                        <?php echo $study_data->description; ?>
                                    </div>
                                  </div>
                                </div>    
                              </div>  
                          </div>

                        <div class="tab-pane fade " id="quiz-reviews" role="tabpanel" aria-labelledby="quiz-reviews-tab">
                            <div class="bg-white card-primary">

                                <div class="card-body">
                                 
                                    <div class="row">
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
                                    foreach($material_comments as $comment_key => $comment_value)
                                    {
                                        $count ++;
                                        
                                            ?>
                                        <div class="comment-list pb-3">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <?php $userimage = (isset($comment_value->image) && !empty($comment_value->image) ? base_url('/assets/images/user_image/'.$comment_value->image) : base_url('assets/images/user_image/avatar-1.png'));?>
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

                        <div class="tab-pane fade py-5" id="associate-quiz" role="tabpanel" aria-labelledby="associate-quiz-tab">
                            <div class="row">
                                <?php
                                    $data['quiz_list_data'] = $quiz_data;
                                    $this->load->view('quiz_data_list',$data);
                                ?>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
	</div>
</div>
