<?php
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

if($study_material_list_data)  
{
    foreach ($study_material_list_data as  $study_array) 
    {	
        $price = $study_array->price > 0 ? '₹ '.$study_array->price : ' '.lang('free');	
    	$s_m_id = $study_array->id;

        $is_s_m_amout_payed = (isset($paid_s_m_array[$s_m_id]) && $paid_s_m_array[$s_m_id]) ? TRUE : FALSE;

        $price_ribbon = ($study_array->price > 0) ? get_admin_setting('paid_currency') ." " .$study_array->price : ($study_array->is_premium == 1 ? "Premium" : 'Free');
        $ribbon_label = ($study_array->price > 0  OR $study_array->is_premium == 1 ) ? 'paid' : 'free';

    	$study_btn_name = $study_array->price > 0 ? lang('pay_now') : lang('study_material_show');
        $study_m_title = $study_array->title;
        $real_url = base_url('study-material/').slugify_string($study_m_title)."-$s_m_id";
        $view_real_url = base_url('study-content/').slugify_string($study_m_title)."-$s_m_id";


        $is_s_m_amout_payed = (isset($paid_s_m_array[$s_m_id]) && $paid_s_m_array[$s_m_id]) ? TRUE : FALSE;


        if($is_premium_member == FALSE)
        {
            $user_category_membership = get_user_category_membership($user_id,$study_array->category_id);
            if($user_category_membership) 
            {
                $is_premium_member = ($user_category_membership->validity && $user_category_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
            }
        }

        if($user_id == $study_array->user_id)
        {
            $is_s_m_amout_payed = TRUE;
            $is_premium_member = TRUE;
        }

        
        if(empty($user_id))
        {
            if($study_array->price > 0 OR $study_array->is_premium == 1 OR $study_array->is_registered == 1)
            {
                $action_url = base_url("login");
                $action_btn_name = lang('login_please');
                $action_btn_icon = '<i class="fas fa-user"></i>';
                $action_lock_icons = '<i class="fas fa-lock "></i>';
                $action_btn_class = 'btn-danger';

            }
            else
            {
                $action_url = $real_url;
                $action_btn_name = lang("view_content");
                $action_btn_icon = '<i class="far fa-eye"></i>';
                $action_lock_icons = '<i class="fas fa-unlock-alt"></i>';
                $action_btn_class = 'btn-primary';
            }
        }
        else
        {
            if($study_array->price > 0 && $is_s_m_amout_payed == FALSE)
            {
                $action_url = base_url("quiz-pay/payment-mode/material/$s_m_id");
                $action_btn_name = lang('pay_now');
                $action_btn_icon = '<i class="fas fa-money-bill"></i>';
                $action_lock_icons = '<i class="fas fa-lock "></i>';
                $action_btn_class = 'btn-info';
            }
            else if($study_array->is_premium == 1 && $is_premium_member == FALSE)
            {
                $action_url = base_url("membership");
                $action_btn_name = lang('get_membership');
                $action_btn_icon = '<i class="fas fa-user-shield"></i>';
                $action_lock_icons = '<i class="fas fa-lock "></i>';
                $action_btn_class = 'btn-warning';
            }
            else
            {
                $action_url = $real_url;
                $action_btn_name = lang("view_content");
                $action_btn_icon = '<i class="far fa-eye"></i>';
                $action_lock_icons = '<i class="fas fa-unlock-alt"></i>';
                $action_btn_class = 'btn-primary';
                
            }
        }


        $study_title = strlen($study_array->title) > 40 ? substr($study_array->title,0,40)."..." : $study_array->title;
        $study_user_name = $study_array->full_name;
        $study_user_name = strlen($study_user_name) > 20 ? substr($study_user_name,0,20)."..." : $study_user_name;


        $average = 0;
        if($study_array->total_rating > 0 && $study_array->rating > 0)
        {
            $average = $study_array->total_rating / $study_array->rating;
        }  
        $complete_count = get_study_material_user_progress($s_m_id);
        $complete_percentage = 0;
        
        if($complete_count)
        {
            $complete_percentage = round((100 * $complete_count) /$study_array->total_file);   
        }

        $average_rating = (isset($average) && !empty($average) ? round($average) : 0);

        $one_star   = $average_rating >= 1 ? "fill-star" : "empty-star";
        $two_star   = $average_rating >= 2 ? "fill-star" : "empty-star";
        $three_star = $average_rating >= 3 ? "fill-star" : "empty-star";
        $four_star  = $average_rating >= 4 ? "fill-star" : "empty-star";
        $five_star  = $average_rating >= 5 ? "fill-star" : "empty-star";  

        $like_or_not = (isset($study_array->like_id) && $study_array->like_id) ? 'text-success' : 'text-dark';    
        $like_or_not_bottom = (isset($study_array->like_id) && $study_array->like_id) ? 'text-success' : 'text-white';    
        $total_like = (isset($study_array->total_like) && $study_array->total_like > 0 ? $study_array->total_like : "");
        $box_to_show_on_md_row = isset($box_to_show_on_row) ? $box_to_show_on_row : 4;
        $box_to_show_on_lg_row = isset($box_to_show_on_row) ? $box_to_show_on_row+1 : 3;
        
        ?>
		<div class="col-xs-6 col-sm-6 col-md-<?php echo $box_to_show_on_md_row; ?> col-lg-<?php echo $box_to_show_on_lg_row; ?>"> 
            <div class="card card-bundle mb-2" data-turbolinks="false"> 
                <div class="thumbnail">
                    
                	<span class="maskk gradient-defaultt <?php echo xss_clean($gradients[mt_rand(0,7)]); ?>"> 
                    </span> 
                	<a href="<?php echo xss_clean($action_url); ?>" data-url="<?php echo xss_clean($action_url); ?>" id="study_<?php echo xss_clean($study_array->id);?>" class="thumb-cover" data-quiz_id="<?php echo xss_clean($study_array->id);?>" data-toggle="tooltip"  title="<?php  echo $action_btn_name;?>"> </a>

                	<div class="details card-dark-shadow"> 
                        <div class="row">
                            <div class="col-10">
                                <section>
                                    <div class="ribbons-wrapper">
                                        <div class="ribbon">
                                            <span class="ribbon3 <?php echo $ribbon_label;?>">
                                                <?php echo $price_ribbon ;?>
                                            </span>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="col-2 text-right">
                                <?php echo $action_lock_icons; ?>                
                            </div>
                        </div>

                        <div class="framework-logo">
                        	<div class="row quiz_middle_icon">

                                <?php 
                                if($this->settings->study_material_box_contant_first_icons)
                                {  
                                    $icon = get_icon_by_name($this->settings->study_material_box_contant_first_icons);
                                    $contant_count = get_study_material_box_contant_count_by_icon($this->settings->study_material_box_contant_first_icons,$study_array);
                                    ?>

                            		<div class="col-4 text-center">
                            			<?php echo $icon; ?> <br>
                            			<span class="value"><?php echo xss_clean($contant_count); ?></span><br/>
                            			<?php echo lang($this->settings->study_material_box_contant_first_icons); ?>
                            		</div>
                                <?php
                                }
                                else
                                {
                                  ?>
                                    <div class="col-4 text-center">
                                        <i class="fa fa-file"> </i> <br>
                                        <span class="value"><?php echo xss_clean($study_array->total_pdf); ?></span><br/>
                                        <?php echo lang('document') ?>
                                    </div>
                                  <?php  
                                } ?>
                           


                                <?php 
                                if($this->settings->study_material_box_contant_second_icons)
                                {  
                                    $icon = get_icon_by_name($this->settings->study_material_box_contant_second_icons);
                                    $contant_count = get_study_material_box_contant_count_by_icon($this->settings->study_material_box_contant_second_icons,$study_array);
                                    ?>

                                    <div class="col-4 text-center">
                                        <?php echo $icon; ?> <br>
                                        <span class="value"><?php echo xss_clean($contant_count); ?></span><br/>
                                        <?php echo lang($this->settings->study_material_box_contant_second_icons); ?>
                                    </div>   <?php
                                }
                                else
                                { ?>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-video"></i> <br>
                                        <span class="value"><?php echo xss_clean($study_array->total_video); ?></span><br/>
                                        <?php echo lang('video') ?>
                                    </div>  <?php
                                } ?>


                                <?php 
                                if($this->settings->study_material_box_contant_thrird_icons)
                                {  
                                    $icon = get_icon_by_name($this->settings->study_material_box_contant_thrird_icons);
                                    $contant_count = get_study_material_box_contant_count_by_icon($this->settings->study_material_box_contant_thrird_icons,$study_array);
                                    ?>

                                    <div class="col-4 text-center">
                                        <?php echo $icon; ?> <br>
                                        <span class="value"><?php echo xss_clean($contant_count); ?></span><br/>
                                        <?php echo lang($this->settings->study_material_box_contant_thrird_icons); ?>
                                    </div>
                                <?php
                                }
                                else
                                {  ?>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-volume-up"></i> <br>
                                        <span class="value"><?php echo xss_clean($study_array->total_audio); ?></span><br/>
                                        <?php echo lang('audio') ?>
                                    </div> <?php
                                } ?>

                        	</div>
                        </div>


                        <div class="quiz_icons">
                            
                            <a href="javascript:void(0)" class="icon float-left text-white">
                                <i class="fe fe-eye mr-1 text-white"></i>
                                <span class="value text-white">
                                    <?php echo xss_clean($study_array->total_view);?>
                                </span>
                            </a>
                            
                            <a href="javascript:void(0)" class="icon inline-block ml-3 float-right like_study_material like_study_material_box_b_<?php echo $s_m_id;?>  <?php echo $like_or_not;?>">
                                <i class="fav_icon fas fa-heart like_study_material_i_b_<?php echo $s_m_id;?> <?php echo ($like_or_not_bottom);?> "></i> 
                                <span class="value text-white like_study_material_span_b_<?php echo ($s_m_id);?>"><?php echo ($total_like);?>
                                </span>
                            </a>

                        </div>
                        
                    </div>

                    <div class="actions">

                    	<a href="<?php echo xss_clean($action_url); ?>" data-url="<?php echo xss_clean($action_url); ?>" id="quiz_<?php echo xss_clean($s_m_id);?>" class="btn btn-neutral btn-fill statrt_quiz_btn text-dark" data-quiz_id="<?php echo ($s_m_id);?>" data-toggle="tooltip"  title="<?php  echo $action_btn_name;?>">
	                            <?php echo $action_btn_icon; ?>                    
	                   </a>


                        <a class="btn btn-neutral btn-fill like_study_material like_study_material_box_<?php echo $s_m_id;?> <?php echo $like_or_not;?>" data-toggle="tooltip"  title="<?php echo lang('like'); ?>" data-study_id="<?php echo $s_m_id;?>">
                            
                            <i class="fav_icon fas fa-heart like_study_material_i_<?php echo $s_m_id;?> <?php echo xss_clean($like_or_not);?>" ></i> 
                            <span class="like_study_material_span_<?php echo xss_clean($s_m_id);?>">
                                <?php echo xss_clean($total_like); ?></span>
                        </a>
                    </div>

                </div>

            </div>
            <div class="pt-3 infobox">
                <div class="quiz-single-title">
                    <a href="<?php echo xss_clean($view_real_url); ?>" class="text-dark"><?php echo xss_clean($study_title); ?></a>
                </div>
                <div class="text-black-im user-list-name">
                    <?php echo xss_clean($study_user_name); ?> 
                </div>
                <?php $average_rating = (isset($average) && !empty($average) ? round($average) : 0);    ?>
                
                <div class="average-star p-0 ">
                    <span><i class="fa fa-star <?php echo $one_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $two_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $three_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $four_star;?>" aria-hidden="true"></i></span>
                    <span><i class="fa fa-star <?php echo $five_star;?>" aria-hidden="true"></i></span>
                </div>

                <?php
                if(isset($user_study_history) && $user_study_history)
                { ?>
                    <div class="w-100 my-2">
                        <div class="progress progress-barr small bg-secondary" >
                          <div class="progress-bar" role="progressbar" style="width: <?php echo $complete_percentage; ?>%" aria-valuenow="<?php echo $complete_percentage; ?>" aria-valuemin="0" aria-valuemax="100"><span class="text-white span"><?php echo $complete_percentage; ?>%</span></div>
                            </div>
                    </div>
                    <?php
                } ?>

                

                 <a href="<?php echo xss_clean($action_url); ?>" data-url="<?php echo xss_clean($action_url); ?>" id="material_<?php echo xss_clean($s_m_id);?>" class=" statrt_quiz_btn btn btn-sm w-100 <?php echo $action_btn_class; ?>" data-quiz_id="<?php echo xss_clean($s_m_id);?>" data-toggle="tooltip"  title="<?php echo $action_btn_name;?>" > <?php echo $action_btn_icon." ".$action_btn_name;?>
                </a>



                <?php 
                if($this->settings->study_material_view_index_button_on_box == "YES")
                {    ?>                
                        <a href="<?php echo $view_real_url; ?>" class="btn btn-danger btn-sm btn-block mt-2 "><i class="far fa-eye"></i> <?php echo lang('view_index'); ?> </a>
                        <?php
                }
                ?>


                <?php 
                if($this->settings->study_material_manage_button_on_box == "YES")
                {                    
                    if($user_id && isset($this->user['role']) && ($this->user['role'] == 'admin' OR $this->user['role'] == 'subadmin'))
                    { ?>
                        <a href="<?php echo base_url('admin/study/update/').$s_m_id; ?>" class="btn btn-dark btn-sm btn-block mt-2 "><?php echo lang('manage_study_contant'); ?> </a>
                        <?php
                    }

                    if($user_id && $user_id == $study_array->user_id && isset($this->user['role']) && $this->user['role'] == 'tutor')
                    {
                        ?>
                        <a href="<?php echo base_url('tutor/study/update/').$s_m_id; ?>" class="btn btn-dark btn-sm btn-block mt-2 "><?php echo lang('manage_study_contant'); ?> </a>
                        <?php
                    }
                }
                ?>

            </div>
        </div>
<?php } } 
else 
{
    ?>
    <div class="col-12 text-center text-danger"> <?php echo lang('no_study_material_found'); ?></div>
    <?php 
} ?>
