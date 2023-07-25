<style type="text/css">
    .image_class img{ width: 100%; }
    .top-bar { display: none; }
    .row.header { display: none; }
    div#navbar.start-header { display: none; }
    .footer.mt-5 { display: none; }
    footer.footer { display: none; }
    .header_margin_padding .header_add_section { display: none; }
    .footer_add_section { display: none; }
@media only screen and (max-width: 769px) { .mobile_center { text-align: center !important; margin-top: 5px; } }

</style>
<div class="container-fluid w-100 px-0">
	<div class="row mx-0">
    	<div class="col-md-12 px-0">
      		
                    <input type="hidden" id="is_contant_reasing_page" value="<?php echo lang('yes'); ?>">
                    <input type="hidden" id="active_study_matrial_id" value="<?php echo $study_material_id ?>">
                    <input type="hidden" id="url_study_material_content_id" value="<?php echo $study_material_content_id ?>">

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

                    $user_image = $study_m_user_data->image ? $study_m_user_data->image : "default.jpg";
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

                   $complete_count = $s_m_completed_content_ids ? count($s_m_completed_content_ids) : 0;
                   $study_material_detail_page = base_url('study-content/').$study_material_slug;
                   $user_study_data_page = base_url('my-study-data');

                   $display_none_for_guest = $user_id ? "" : "d-none";

                ?>





                    <div class="detail_page_header w-100 d-none">
                        <div class="row">
                            <div class="col-12">
                                <img  src="<?php echo $current_detail_image; ?>" class="detail_page_header_img">
                            </div>
                        </div>
                        
                        <div class="w-100 h-100 detail_page_header_abslute" style="background: <?php echo $background_background; ?>">

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
                            </div>
                            
                            <div class="row m-0 p-5">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <img class='quiz_user_image' src="<?php echo $user_image; ?>">
                                        </div>
                                        <div class="col-md-6 my-auto">
                                            <h6 class="text-white w-100 mb-2"><?php echo $study_m_user_data->first_name." ".$study_m_user_data->last_name ?></h6>
                                            <h6 class="text-white w-100 mb-0"><?php echo $study_m_user_data->email; ?></h6>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-12">                                            
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
                                            <a href="<?php echo "#study_data_contant"; ?>" id="study_data_contant_btn" class="btn btn-light text-info btn-md text-uppercase"><?php echo lang('view_contant'); ?> </a>




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
                    </div>


                    <div class="row d-none">
                        <div class="col-12"> <hr> </div>
                    </div>


			        
			        <?php 
                    if($study_material_content_data) 
                    {  
                        $complete_percentage = round((100 * $complete_count) /$study_data->total_file);
                        ?>

                        <input type="hidden" value="<?php echo $study_data->total_file; ?>" id="study_data_total_file">
                        <input type="hidden" value="<?php echo $complete_percentage; ?>" id="current_complete_percentage">
                        <div class="col-12  bg-dark py-3 my-auto mobile_center">
                            <div class="row my-auto">
                                <div class="col-md-4 my-auto">
                                    <h4 class="text-white">
                                        <i class="fas fa-book-reader mr-2 border rounded"></i><?php echo ucfirst($study_data->title); ?>
                                    </h4>
                                </div>
                                <div class="col-md-4 my-auto mobile_center" id="study_data_contant">
                                    <?php
                                    if($user_id)
                                    { ?>
                                       <div class="row mobile_center">
                                           <div class="col-6 my-auto mobile_center">
                                                <div class="progress progress-barr small bg-secondary" >
                                                  <div class="progress-bar" role="progressbar" style="width: <?php echo $complete_percentage; ?>%" aria-valuenow="<?php echo $complete_percentage; ?>" aria-valuemin="0" aria-valuemax="100"><span class="text-white span"><?php echo $complete_percentage; ?>%</span></div>
                                                </div>
                                           </div>

                                           <div class="col-6 my-auto">
                                               <h6 class="text-left small text-white text-center"><?php echo lang('you_have_complete'); ?> <span class="total_contant_has_complete" id="total_contant_has_complete"><?php echo $complete_count; ?></span> <?php echo lang('out_of')." ".$study_data->total_file; ?> </h6>
                                           </div>
                                       </div>
                                   <?php
                                    } ?>
                                </div>
                                <div class="col-md-4 text-right mobile_center">
                                    <a href="<?php echo $study_material_detail_page;  ?>" class="btn btn-info btn-sm"><?php echo lang('study_material_detail'); ?></a>
                                    <?php
                                    if($user_id)
                                    { ?>
                                     <a href="<?php echo $user_study_data_page; ?>" class="btn btn-secondary btn-sm"><?php echo lang('my_courses'); ?></a> <?php
                                    } ?>
                                    
                                    <a class="nav-item float-right">
                                        <label id="switch" class="switch mt-2">
                                          <input type="checkbox" class="toggleTheme" id="slider">
                                          <span class="slider round"></span>
                                        </label>
                                    </a>  
                                </div>
                            </div>
                        </div>
			        	
			         <?php 
                    } ?>


            <div class="card my-0">
                <div class="card-body">
                    <?php
                    if($study_material_content_data)
                    { 
                        $study_content_type = $study_material_content_data->type;
                        $study_material_content_value = $study_material_content_data->value;
                        $study_material_content_title = $study_material_content_data->title;
                        $attachment_dir = "./assets/uploads/study_material";
                        $attachment_dir_link = base_url("assets/uploads/study_material/");
                        $attachment_dir_folder = "assets/uploads/study_material/";
                        if($study_material_content_data->is_media_file == 1)
                        {
                            $attachment_dir_link = base_url("media/");
                            $attachment_dir_folder = "media/";
                        }


                        $active_contant_id = isset($study_material_content_data->id) ? $study_material_content_data->id : NULL;
                        $active_section_id = isset($study_material_content_data->section_id) ? $study_material_content_data->section_id : NULL;
                        ?>
                        <div class="row studay_section_data">
                            
                            <div class="col-md-3 col-sm-12 col-xs-12 border">
                               
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

                                    if($active_section_id)
                                    {
                                        if($active_section_id == $s_m_section_data->id)
                                        {
                                            $collapsed = "collapsed";
                                            $show = "show";
                                        }
                                    }
                                    else
                                    {
                                        if($i == 1)
                                        {
                                            $collapsed = "collapsed";
                                            $show = "show";
                                        }
                                    } ?>
                                    
                                    <div class="accordion" id="accordionExample">
                                        
                                        <div class="card mb-1">
                                            <div class="card-header p-0" id="heading_section_<?php echo $s_m_section_data->id; ?>">
                                                <h2 class="mb-0 w-100">
                                                    <button type="button" class="btn btn-link w-100 py-1 <?php echo $collapsed; ?>" data-toggle="collapse" data-target="#collapse_section_<?php echo $s_m_section_data->id; ?>">
                                                        <span class="float-left"><?php echo lang('section')." ".$i." : "; ?><?php echo $s_m_section_data->title; ?>
                                                        </span> 
                                                        <span class="float-right"><i class="fa fa-angle-up ml-5"></i></span>
                                                        <span class="clearfix"></span>
                                                        <div class="small w-100 float-left text-left"><?php  echo $total_contant_count; ?>  /  <span class="section_has_complete"><?php echo $complete_count; ?></span> | <i class="fas fa-stopwatch mr-1"></i> <?php echo $s_m_section_data->total_duration." ".lang('min'); ?> </div>  
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
                                                            foreach ($section_contant_data as $data_section_contant) 
                                                            { 
                                                                $study_m_title = $study_data->title;
                                                                $this_contant_url = base_url("study-material/").slugify_string($study_m_title)."-$study_material_id/$data_section_contant->id";

                                                                $chech_box_checked = "";

                                                                if($s_m_completed_content_ids && in_array($data_section_contant->id, $s_m_completed_content_ids))
                                                                {
                                                                  $chech_box_checked = "checked";
                                                                }

                                                                $j++;
                                                                $active_contant_section = "";
                                                                if($active_contant_id)
                                                                {
                                                                    if($active_contant_id == $data_section_contant->id)
                                                                    {
                                                                        $active_contant_section = "text-success";
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    if($j == 1)
                                                                    {
                                                                        $active_contant_section = "text-info";
                                                                    }
                                                                }

                                                                ?>
                                                                
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-check my-auto">
                                                                          <input <?php echo $chech_box_checked; ?> class="<?php echo $display_none_for_guest; ?> form-check-input data_section_contant_check_box" type="checkbox" value="<?php echo $data_section_contant->id ?>" id="data_section_contant_<?php echo $data_section_contant->id; ?>" data-s_m_section_id = "<?php echo $data_section_contant->section_id; ?>">
                                                                          <label class="form-check-label <?php echo $active_contant_section; ?> " forr="data_section_contant_<?php echo $data_section_contant->id ?>">
                                                                            <a href="<?php echo $this_contant_url; ?>" class="btn btn-link no_underline w-100 p-0 <?php echo $active_contant_section; ?>"><?php echo $j.".  ".$data_section_contant->title; ?></a><br>
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

                            <div class="col-md-9 col-sm-12 col-xs-12  m-auto">
                                <div class="row">
                                    <div class="col-12">
                                         <h1 class="w-100 text-center mb-3 bg-secondary text-white text-uppercase rounded py-3" ><?php echo $study_material_content_title; ?></h1>
                                    </div>
                                </div>
                                <div class="w-100 studay_contant_view border">
                                    
                                    <div class="w-100 my-auto text-center">

                                        <?php

                                        if($study_content_type == "doc")
                                        {   
                                            $real_attachment_name = $study_material_content_value;
                                            $attachment = $real_attachment_name;
                                            
                                            $get_filename = explode('/',$real_attachment_name);
                                            $ffffile_name = end($get_filename);
                                           
                                            // if(!is_file($ffffile_name))
                                            // {
                                            //   $attachment = base_url('assets/default/default.jpg');
                                            // } 
                                            ?>

                                            <p class="my-2 text-danger"><?php echo lang('If You Cant View File Then Refresh Page Again Or'); ?> <a target="_blank" href="http://docs.google.com/viewer?url=<?php echo $attachment; ?>&embedded=true"><?php echo lang('click_here'); ?></a></p>
                                           <iframe class="w-100 hidepoupbtn"  seamless="" height="500px" id="google" src="https://docs.google.com/viewer?url=<?php echo $attachment; ?>&embedded=true" allowfullscreen="allowfullscreen">
                                            </iframe>
                                            
                                            <?php
                                        }
                                        else if($study_content_type == "pdf")
                                        {
                                            $real_attachment_name = $study_material_content_value;
                                            $attachment = $real_attachment_name;
                                            
                                            $get_filename = explode('/',$real_attachment_name);
                                            $ffffile_name = end($get_filename);

                                              ?>

                                           <iframe class="w-100 "  height="500px" src="<?php echo $attachment; ?>" allowfullscreen="allowfullscreen">
                                            </iframe>

                                            <?php

                                        }
                                        else if($study_content_type == "image")
                                        {
                                            $real_attachment_name = $study_material_content_value;
                                            $attachment = $real_attachment_name;
                                            
                                            $get_filename = explode('/',$real_attachment_name);
                                            $ffffile_name = end($get_filename);

                                              ?>

                                            <img class="w-100 " src="<?php echo $attachment; ?>"/>

                                            <?php

                                        }
                                        else if($study_content_type == "audio")
                                        {
                                            $real_attachment_name = $study_material_content_value ; 
                                            $attachment = $attachment_dir_link.$real_attachment_name;

                                            if(!is_file(FCPATH.$attachment_dir_folder.$real_attachment_name))
                                            {
                                              $attachment = base_url('assets/default/default.mp3');
                                            } 
                                            
                                              $ext = pathinfo($real_attachment_name, PATHINFO_EXTENSION);
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

                                            <audio controls class="w-100 no_underline">
                                                <source src="<?php echo $attachment; ?>" type="<?php echo $audio_source_type; ?>">
                                                Your browser does not support the audio element.
                                            </audio>

                                            <?php

                                        }
                                        else if($study_content_type == "video")
                                        {
                                            $real_attachment_name = $study_material_content_value ; 
                                            $attachment = $attachment_dir_link.$real_attachment_name;
                                            if(!is_file(FCPATH.$attachment_dir_folder.$real_attachment_name))
                                            {
                                              $attachment = base_url('assets/default/default.mp4');
                                            } 

                                              $ext = pathinfo($real_attachment_name, PATHINFO_EXTENSION);
                                              $audio_source_type = "video/mp4";
                                              if(strtolower($ext) == "mp4")
                                              {
                                                $audio_source_type = "video/mp4";
                                              }
                                              else if(strtolower($ext) == "ogg")
                                              {
                                                $audio_source_type = "movie.ogg";
                                              }
                                              else if(strtolower($ext) == "webm")
                                              {
                                                $audio_source_type = "video/webm";
                                              }
                                              else if(strtolower($ext) == "ogv")
                                              {
                                                $audio_source_type = "video/ogg";
                                              }
                                              else if(strtolower($ext) == "avi")
                                              {
                                                $audio_source_type = "video/avi";
                                              }
                                              else if(strtolower($ext) == "flv")
                                              {
                                                $audio_source_type = "video/flv";
                                              }
                                              else if(strtolower($ext) == "wma")
                                              {
                                                $audio_source_type = "video/wma";
                                              }
                                              else if(strtolower($ext) == "wav")
                                              {
                                                $audio_source_type = "video/wav";
                                              }
                                              else if(strtolower($ext) == "mov")
                                              {
                                                $audio_source_type = "video/mov";
                                              }
                                              else if(strtolower($ext) == "avchd")
                                              {
                                                $audio_source_type = "video/avchd";
                                              }
                                              else if(strtolower($ext) == "mkv")
                                              {
                                                $audio_source_type = "video/mkv";
                                              }


                                            ?>
                                            
                                            <video controls class="w-100 no_underline" height="400px">
                                                <source src="<?php echo $attachment; ?>" type="<?php echo $audio_source_type; ?>">
                                            Your browser does not support the video tag.
                                            </video>

                                            <?php

                                        }
                                        elseif ($study_content_type == "youtube-embed-code") 
                                        {
                                            ?>
                                            <div class="p-4 w-100">
                                                 <iframe class="quesion_iframe w-100" width="100%" height="400px" src="https://www.youtube.com/embed/<?php echo $study_material_content_value; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                            <?php
                                        } 
                                        elseif ($study_content_type == "vimeo-embed-code") 
                                        {
                                            ?>
                                            <div class="p-4 w-100">
                                                 <iframe class="quesion_iframe w-100" width="100%" height="400px" src="https://player.vimeo.com/video/<?php echo $study_material_content_value; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                            <?php
                                        } 
                                        elseif ($study_content_type == "content") 
                                        {
                                            ?>
                                                <div class="p-4 w-100">
                                                    <?php echo htmlspecialchars_decode($study_material_content_value); ?>
                                                    <?php //echo $study_material_content_value; ?>
                                                </div>
                                            <?php
                                        } 
                                        ?>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <?php
                                        if($next_study_material_content_data)
                                        { 
                                            $study_m_title = $study_data->title;
                                            $next_contant_url = base_url("study-material/").slugify_string($study_m_title)."-$study_material_id/$next_study_material_content_data->id";
                                            ?>
                                            <a data-id_value="<?php echo $data_section_contant->id ?>" href="javscript:void(0)" class="btn btn-info btn-block go_to_next_contant" data-next_url="<?php echo $next_contant_url; ?>" data-s_m_section_id = "<?php echo $data_section_contant->section_id; ?>"><?php echo lang('go_to_next_chapter'); ?></a>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <a href="<?php echo $study_material_detail_page; ?>" class="btn btn-info btn-block"><?php echo lang('complete_chapter'); ?></a>
                                            <?php
                                        } ?>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <?php
                    } ?>



                    <div class="row d-none">
                        <div class="col-12">

                            <ul class="nav nav-tabs m-0 p-0 bg-white" id="qui-detail-Tab" role="tablist">
                               <li class="nav-item">
                                  <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="false"><?php echo lang('description'); ?></a>
                               </li>
                               <li class="nav-item ">
                                  <a class="nav-link " id="quiz-reviews-tab" data-toggle="tab" href="#quiz-reviews" role="tab" aria-controls="reviews" aria-selected="true"><?php echo lang('reviews'); ?></a> 
                               </li>

                            </ul>


                            <div class="tab-content" id="qui-detail-TabContent">

                                  <div class="tab-pane fade active show" id="description" role="tabpanel" aria-labelledby="description-tab">
                                      <div class="bg-white card-primary">
                                        <!-- <div class="card-header"> -->
                                          <!-- <h4><?php // echo lang('description'); ?></h4> -->
                                        <!-- </div> -->
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
                                        <!-- <div class="card-header"> -->
                                          <!-- <h4><?php  //echo lang('quiz_reviews'); ?></h4> -->
                                        <!-- </div> -->
                                        <div class="card-body">
                                         
                                            <div class="row">
                                              <?php 
                                                if(empty($comments_exist_or_not)) 
                                                { ?>
                                                    <div class="col-12">
                                                    <?php echo form_open('material-rating/'.$purchases_type, array('role'=>'form',)); ?>
                                                        <input type="hidden" name="quizid" class="quizid" value="<?php echo $study_data->id?>">
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
                                                    <?php echo form_close();?>
                                                    </div>
                                                    <div class="col-12"><hr></div>
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
                            </div>

                        </div>
                    </div>


			    </div>
			</div>
		</div>
	</div>
</div>
