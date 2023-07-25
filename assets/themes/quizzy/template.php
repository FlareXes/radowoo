<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* Default Public Template 
*/
?>
<!DOCTYPE html>
<?php 

$is_rtl = '';
$rtl_dir = '';
$margin_auto = 'mr-auto'; 
$order_two = NULL; 
if ($this->session->is_rtl) 
{
  
  ?>
  <html lang="en" dir="rtl">
  <?php  
  $is_rtl = 'rtl_language';
  $rtl_dir = 'rtl';
  $margin_auto = 'ml-auto';
   $order_two = 'order-2';
}
else
{
  ?>
  <html lang="en">
  <?php 
}
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta http-equiv="Content-Language" content="en" />
  <meta name="msapplication-TileColor" content="#2d89ef">
  <meta name="theme-color" content="#4188c9">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="HandheldFriendly" content="True">
  <meta name="MobileOptimized" content="320">

  <link rel="icon" type="image/x-icon" sizes="32x32" href="<?php echo base_url('/assets/images/logo/'); ?><?php echo get_admin_setting('site_favicon'); ?>" />
  <!-- Generated: 2018-04-16 09:29:05 +0200 -->
    <?php 
    
    if(isset($meta_data) && $meta_data)
    {

    $meta_image = json_decode($meta_data['image']);

    ?>
      <meta name="keywords" content="<?php echo $meta_data['meta_keyword']; ?>">
      <meta name="description" content="<?php echo $meta_data['meta_description']; ?>">
      
       <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="<?php echo $meta_data['title']; ?>">
        <meta itemprop="description" content="<?php echo strip_tags(xss_clean($meta_data['description'])); ?>">
        <?php
          if(is_array($meta_image))
          {
            foreach($meta_image as $meta_key => $meta_value)
            { 
        ?>    
          <meta itemprop="image" content="<?php echo $meta_value; ?>">
        <?php 
            }
           } 
           else 
           { 
        ?>  
          <meta itemprop="image" content="<?php echo $meta_data['image']; ?>">
        <?php } ?>  

        <!-- Twitter Card data -->
        <meta name="twitter:site" content="<?php echo $this->settings->site_name; ?>">
        <meta name="twitter:title" content="<?php echo $meta_data['title']; ?>">
        <meta name="twitter:description" content="<?php echo strip_tags(xss_clean($meta_data['description'])); ?>">
        <meta name="twitter:creator" content="<?php echo $this->settings->site_name; ?>">
        <?php
          if(is_array($meta_image))
          {
            foreach($meta_image as $meta_key => $meta_value)
            {
            
        ?>
          <meta name="twitter:image" content="<?php echo $meta_value; ?>">
        <?php 
            }
           } 
           else 
           { 
        ?>  
          <meta itemprop="image" content="<?php echo $meta_data['image']; ?>">
        <?php } ?>

        <!-- Open Graph data -->
        <meta property="og:title" content="<?php echo $meta_data['title']; ?>" />

        <meta property="og:url" content="<?php echo current_url();?>" />
        <?php
          if(is_array($meta_image))
          {
            foreach($meta_image as $meta_key => $meta_value)
            {
            
        ?>
          <meta property="og:image" content="<?php echo $meta_value;?>" />
        <?php 
            }
           } 
           else 
           { 
        ?>  
          <meta itemprop="image" content="<?php echo $meta_data['image']; ?>">
        <?php } ?>  
        <meta property="og:description" content="<?php echo strip_tags(xss_clean($meta_data['description'])); ?>" />
        <meta property="og:site_name" content="<?php echo $this->settings->site_name; ?>" />

      <?php
        }
        else
        { 
      ?> 
          <meta name="keywords" content="<?php echo $this->settings->meta_keywords; ?>">
          <meta name="description" content="<?php echo $this->settings->meta_description; ?>">
      <?php     
        } 
      ?>
      <link href="https://fonts.googleapis.com/css?family=Quicksand:300,500,700|Work+Sans:400,700" rel="stylesheet">


  <?php 
    $meta_title = (isset($meta_data['meta_title']) && !empty($meta_data['meta_title']) ? $meta_data['meta_title'] : $this->settings->site_name);
  ?>
  <title><?php echo xss_clean($page_title); ?> - <?php echo xss_clean($meta_title); ?></title>
  <?php 
    if ($this->session->is_rtl) 
    {
  ?>
      <link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/themes/admin/css/");?>rtl.bootstrap.min.css">
  <?php  
    }
  ?>

         
  
  <?php if (isset($css_files) && is_array($css_files)) : ?>
  <?php foreach ($css_files as $css) : ?>
    <?php if ( ! is_null($css)) : ?>
      <?php $separator = (strstr($css, '?')) ? '&' : '?'; ?>
      <link rel="stylesheet" href="<?php echo xss_clean($css); ?><?php echo xss_clean($separator); ?>v=<?php echo xss_clean($this->settings->site_version); ?>"><?php echo "\n"; ?>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php endif; ?>


  <?php
  $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
  $ad_left_time = 0;
   $session_time = isset($this->session->quiz_session['quiz_data']['duration_min']) && ($this->session->quiz_session['quiz_data']['duration_min'] > 0 ) ? 'yes' : 'no';
  $ad_active_quiz = '';
  $ad_active_quiz_result_page_url = '';
  $test_page = 'quiz';
  if($this->session->quiz_session)
  {
    $ad_added_time = $this->session->quiz_session['participants_content']['started']; 
    
    $ad_dt = new DateTime($ad_added_time);
    $ad_minutes_to_add = $this->session->quiz_session['quiz_data']['duration_min'];
    $ad_time = new DateTime($ad_added_time);
    $ad_time->add(new DateInterval('PT' . $ad_minutes_to_add . 'M'));
    $ad_expire_time = $this->session->quiz_session['participants_content']['end_time'];

    $ad_expire_time = strtotime($ad_expire_time);
    $ad_current_time = strtotime(date('Y-m-d H:i:s'));
    $ad_session_quiz_id = $this->session->quiz_session['quiz_data']['id'];
    if($ad_current_time >= $ad_expire_time)
    {
      return redirect('result/'.$ad_session_quiz_id);      
    }

    $ad_active_quiz = base_url("test/$ad_session_quiz_id/1");

    $ad_session_participant_id = $this->session->quiz_session['participants_content']['participant_id'];
    $running_encrypted_participant_id = encrypt_decrypt('encrypt',$ad_session_participant_id);
    $ad_active_quiz_result_page_url = base_url("my/test/summary/$running_encrypted_participant_id");

    $ad_page_is_quiz = strstr(uri_string(), "test/$ad_session_quiz_id/") ? 'YES' : '';
    if(empty($ad_page_is_quiz))
    {
      $ad_left_time = $ad_expire_time - $ad_current_time;
      $test_page = 'other';
    }
  }

   $disable_right_click = get_admin_setting('disable_right_click');
   $disable_print_screen = get_admin_setting('disable_print_screen');
   $disable_cut_copy_paste = get_admin_setting('disable_copy_paste_click');
   $hader_logo_height = get_admin_setting('header_logo_height');
   $hader_logo_height = $hader_logo_height > 1 ? $hader_logo_height : 65;

  $flash_error_msg =  str_replace("'","`",$this->session->flashdata('error'));
  $flash_success_msg =  str_replace("'","`",$this->session->flashdata('message'));

  $user_id = isset($this->user['id']) ? $this->user['id'] : NULL; 
  $full_name_of_user = isset($this->user['first_name']) ? $this->user['first_name']. ' '.$this->user['last_name'] : '';
  $is_admin = (isset($this->user['is_admin']) && $this->user['is_admin']==1) ? "Administrator" : "User";
  $is_tutor = (isset($this->user['role']) && $this->user['role']=="tutor") ? TRUE : FALSE;
  $name_of_user = (strlen($full_name_of_user) > 15) ? substr($full_name_of_user, 0, 10).'...' : $full_name_of_user ;

  $profile_url = "profile";

  ?>

  <script> 
    var BASE_URL = '<?php echo base_url(); ?>'; 
    var csrf_Name = '<?php echo $this->security->get_csrf_token_name() ?>'; 
    var csrf_Hash = '<?php echo $this->security->get_csrf_hash(); ?>'; 
    var rtl_dir = "<?php echo xss_clean($rtl_dir); ?>";
    var are_you_sure = "<?php echo lang('are_you_sure'); ?>";
    var permanently_deleted = "<?php echo lang('it_will_permanently_deleted'); ?>";
    var yes_delere_it = "<?php echo lang('yes_delere_it'); ?>";
    var resume_quiz_lang = "<?php echo lang('resume_quiz'); ?>";
    var quiz_result_lang = "<?php echo lang('quiz_result'); ?>";
    var check_quiz_result = "<?php echo lang('check_quiz_result'); ?>";
    var table_search = "<?php echo lang('table_search'); ?>";
    var table_show = "<?php echo lang('table_show'); ?>";
    var table_entries = "<?php echo lang('table_entries'); ?>";
    var table_showing = "<?php echo lang('table_showing'); ?>";
    var table_to = "<?php echo lang('table_to'); ?>";
    var table_of = "<?php echo lang('table_of'); ?>";
    var java_error_msg = "<?php echo lang('java_error_msg'); ?>";
    var table_previous = "<?php echo lang('table_previous'); ?>";
    var table_next = "<?php echo lang('table_next'); ?>";
    var total_attemp = "<?php echo lang('your_total_attemp_is'); ?>";
    var yes_submit_now = "<?php echo lang('submit_test'); ?>";
    var quiz_already_running = "<?php echo lang('quiz_already_running'); ?>";
    var stop_running_quiz_msg = "<?php echo lang('plz_complete_or_stop_running_quiz'); ?>";
    var resume_quiz = "<?php echo lang('resume_quiz'); ?>";
    var already_attemped = "<?php echo lang('question_already_attempted'); ?>";
    var you_cant_resubmit_this_quiz_answer = "<?php echo lang('you_cant_resubmit_attempted_question'); ?>";
    var stop_quiz = "<?php echo lang('stop_quiz'); ?>";
    var no_answer_given_yet = "<?php echo lang('no_answer_given_yet'); ?>";
    <?php if(isset($stripe_key['publishable_key'])) { ?>
      var stripe_publishable_key = "<?php echo xss_clean($stripe_key['publishable_key']); ?>";
    <?php } ?>

    var is_answered_or_disable = false;
    var is_last_question_of_test = false;

    var flash_message = '<?php echo $flash_success_msg; ?>';
    var flash_error = '<?php echo $flash_error_msg; ?>';

    var error_report = '<?php echo xss_clean($this->error); ?>';
    var ad_left_time = '<?php echo xss_clean($ad_left_time); ?>';
    var ad_active_quiz = '<?php echo xss_clean($ad_active_quiz); ?>';
    var ad_active_quiz_result_page_url = '<?php echo xss_clean($ad_active_quiz_result_page_url); ?>';
    var test_page = '<?php echo xss_clean($test_page); ?>';
    var login_user_id = <?php echo xss_clean($login_user_id); ?>;
    login_user_id = parseInt(login_user_id); 

    var disable_right_click = '<?php echo xss_clean($disable_right_click); ?>';
    var disable_print_screen = '<?php echo xss_clean($disable_print_screen); ?>';
    var disable_cut_copy_paste = '<?php echo xss_clean($disable_cut_copy_paste); ?>';
    var session_time = '<?php echo xss_clean($session_time);?>';
    var set_default_theme_in_dark_mode = '<?php echo xss_clean($this->settings->set_default_theme_in_dark_mode);?>';
    <?php if(get_admin_setting('header_javascript')) { echo html_entity_decode(get_admin_setting('header_javascript')); } ?>
  </script>


 <style type="text/css">
     <?php if((get_admin_setting('custom_css'))) { echo html_entity_decode(get_admin_setting('custom_css'));} ?>
 </style>

 <?php
      $list_google_advertisments = get_google_advertisments();
      if($list_google_advertisments)
      {
        ?>
        <script async="async" src="//www.google.com/adsense/search/ads.js"></script>
        <script type="text/javascript" charset="utf-8">
        (function(g,o){g[o]=g[o]||function(){(g[o]['q']=g[o]['q']||[]).push(
          arguments)},g[o]['t']=1*new Date})(window,'_googCsa');
        </script>
        <?php
      }
  ?>

</head>

<body class="h-100 <?php echo xss_clean($is_rtl); ?>">
  <!-- Back to top button -->
  <input type="hidden" id="BASE_URL_OF_SITE" value="<?php echo base_url(); ?>">
  <a id="back-to-top-button"><i class="fas fa-angle-double-up"></i></a>
  <div class="page">
    <div class="page-main">
      <?php  if(!empty(strip_tags(get_admin_setting('top_text_left'))) OR !empty(strip_tags(get_admin_setting('top_text_right')))) 
      { 
        ?>
        <div class="top-bar">
             <div class="container">
               <div class="navbar navbar-expand-lg">
                 <div class="col-6">
                   <span class="text-white"><?php echo strip_tags(get_admin_setting('top_text_left'));?></span>
                 </div>
                 <div class="col-6">
                   <span class="text-white float-right"><?php echo strip_tags(get_admin_setting('top_text_right'));?></span>
                 </div>
               </div>
             </div> 

        </div>
        <?php 
      } ?>
      <div class="sticky-header-top" data-sticy="<?php echo get_admin_setting('is_sticky_header'); ?>"></div>
      <div class="container-fluid sticky_top bg-white">
        <div class="container">
          <div class="row header">
            <div class="col-md-4 col-sm-12 col-xs-12">
              <div class="row">
                <div class="col-md-6 col-xl-6  col-6">
                    <a class="header-brand" href="<?php echo base_url()?>">
                      <img class="header-brand-img" src="<?php echo base_url('/assets/images/logo/'); ?><?php echo get_admin_setting('site_logo'); ?>" style="height: <?php echo $hader_logo_height; ?>px;">
                    </a>
                </div>
                <div class="col-md-6 col-xl-6  col-6">
                    <div id="dl-menu" class="dl-menuwrapper mt-2">
                      <button class="dl-trigger"><?php echo lang('categories') ?></button>
                        <?php get_categories_tree() ?>
                    </div><!-- /dl-menuwrapper -->                
                </div>
              </div>
                
            </div>
            <div class="col-md-5 col-sm-12 col-xs-12 m-auto before_togle_menu_width">
              <div class="row">

                <div class="  col-xl-12 col-2 text-center display_on_mobile_only">
                  <button class="navbar-toggler mt-2" id="menu_togle_btn" type="button" data-toggle="collapse" data-target="#headerMenuCollapse" aria-controls="headerMenuCollapse" aria-expanded="false" aria-label="Toggle navigation">
                      <i class="fas fa-bars"></i>
                  </button>
                </div>

                <div class=" col-xl-12 col-10">
                  <?php echo form_open(base_url("search"), array('role'=>'form','class' => "w-100 serch_headr_form"));?>
                  
                  <div class="row">
                    <div class="col-3 pr-0">
                      <select name="header_search_type" class="serch_selection">
                        <option <?php echo ($this->uri->segment(1) == 'search' && $this->uri->segment(2) == 'quiz') ? 'selected' : ''; ?> value="quiz"><?php echo lang('quiz'); ?></option>
                        <option <?php echo ($this->uri->segment(1) == 'search' && $this->uri->segment(2) == 'study') ? 'selected' : ''; ?> value="study"><?php echo lang('study_material'); ?></option>
                        <option <?php echo ($this->uri->segment(1) == 'search' && $this->uri->segment(2) == 'category') ? 'selected' : ''; ?> value="category"><?php echo lang('Category'); ?></option>
                        <option <?php echo ($this->uri->segment(1) == 'search' && $this->uri->segment(2) == 'tutor') ? 'selected' : ''; ?> value="tutor"><?php echo lang('tutor'); ?></option>
                      </select>                
                    </div>
                    <div class="col-9 pl-0">
                      <div class="input-group ">
                        <input name="header_search_value" class="form-controll roundedd" type="text" name="search" value="<?php echo $this->input->get('query',true); ?>" id="serchbar" placeholder="<?php echo lang('search_items') ?>" aria-label="<?php echo lang('search_items') ?>" aria-describedby="header_serch_submit">
                        <div class="input-group-append search_box">
                          <button class="btn btn-outline-secondary" id="header_serch_submit" type="submit"><?php echo lang('Search'); ?></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php echo form_close();?>
                </div>
            
  
              </div>

            </div>
            
            <div class="col-md-3 col-sm-12 col-xs-12 theme_or_profile_section ">

              <div class="row  ">
                
                <div class="col-md-8 col-sm-8 col-xs-8 my-auto  text-center header_profile_menu_option">
                  <div class="navbar navbar-expand-lg py-1 my-auto">
                    <ul class="navbar-nav nav nav-tabs navbar-right my-auto  border-0">
                          <?php

                          if ($user_id) 
                          { ?>

                                <li class="nav-item">
                                  
                                  <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown" title="<?php echo xss_clean($full_name_of_user); ?>"><span class="avatar" >
                                    <?php $loginImage = ($this->session->logged_in['image'] ? base_url('assets/images/user_image/'.$this->session->logged_in['image']) : base_url('assets/images/user_image/avatar-1.png'))?>
                                      <img  alt="" src="<?php echo xss_clean($loginImage);?>" class="rounded-circle mr-1 w-100 h-100">
                                  </span>

                                    <span class="ml-2 d-none d-lg-block">
                                      <span class="text-defaultt"> <?php echo xss_clean($name_of_user); ?></span>
                                    </span>

                                  </a>

                                  <div class="dropdown-menu dropdown-menu-arrow">
                                    <?php $rtl_icon = $this->session->is_rtl ? 'rtl-icon' : ""; ?>
                                    <a class="dropdown-item" href="<?php echo base_url($profile_url);?>">
                                      <i class="dropdown-icon fe fe-user <?php echo $rtl_icon;?>"></i><?php echo lang('user_profile') ?> 
                                    </a>
                                    

                                                              
                                    <?php if ($is_admin == 'Administrator') 
                                    { 
                                      if(is_loged_in_user_is_subadmin() == FALSE)
                                      { ?>
                                        <a class="dropdown-item" href="<?php echo base_url("admin/settings");?>">
                                          <i class="dropdown-icon fe fe-settings <?php echo $rtl_icon;?>"></i><?php echo lang('admin_admin_settings') ?>
                                        </a>
                                        <?php
                                      }
                                      else
                                      {
                                        ?>
                                        <a class="dropdown-item" href="<?php echo base_url("admin/dashboard");?>">
                                          <i class="dropdown-icon fas fa-fire <?php echo $rtl_icon;?>"></i><?php echo lang('dashboard') ?>
                                        </a>
                                        <?php
                                      }
                                    } ?>

                                                              
                                    <?php 
                                    if ($is_tutor == TRUE) 
                                    { ?>
                                      <a class="dropdown-item" href="<?php echo base_url("tutor");?>">
                                        <i class="dropdown-icon fas fa-fire <?php echo $rtl_icon;?>"></i><?php echo lang('dashboard') ?>
                                      </a>
                                      <?php
                                    } ?>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="<?php echo base_url("logout");?>">
                                      <i class="dropdown-icon fe fe-log-out <?php echo $rtl_icon;?>"></i><?php echo lang('sign_out') ?>
                                    </a>

                                  </div>

                                </li>

                                <?php
                          }
                          else
                          {
                            ?>
                            <li class="nav-item">
                              <a href="<?php echo base_url('login'); ?>" class="nav-link">
                                <i class="fas fa-sign-in-alt mr-3"></i><?php echo lang('login') ?>
                              </a>
                            </li>
                            <?php
                          } ?>

                    </ul>
                  </div>
                </div>


                <div class="col-md-4 col-sm-4 col-xs-4 text-center  my-auto">
                    <label id="switch" class="switch mt-2">
                        <input type="checkbox" class="toggleTheme" id="slider">
                        <span class="slider round"></span>
                      </label>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
                           


      <div class="header py-0 navigation-wrap start-header start-style" id="navbar">
        <div class=" my-auto">
          <div class="navbar navbar-expand-lg py-0 m-auto">
            <div class="collapse navbar-collapse text-center m-auto" id="headerMenuCollapse">


              <ul class="navbar-nav nav nav-tabs navbar-right  border-0  m-auto">


                <?php 
                $front_menu_order = get_front_menu_order();
                if($front_menu_order)
                {
                  foreach ($front_menu_order as $menu_order_array) 
                  { 


                    if($menu_order_array->slug == 'pages_menu')
                    { 

                      $menu_category_array =  get_header_menu_item_helper(); 
                      if($menu_category_array)
                      {
                        foreach ($menu_category_array as $menu_array) 
                        {
                          ?>

                          <li class="nav-item">
                            <a href="<?php echo base_url('pages/').$menu_array->slug; ?>" class='nav-link <?php echo (uri_string() == "pages/$menu_array->slug") ? "active" : ""; ?>'>
                              <?php echo ucfirst($menu_array->title); ?></a>
                          </li>
                          <?php
                        }
                      }
                     
                    }





                    if($menu_order_array->slug == 'my_history')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'my/history') ? 'active' : ''; ?>" href="<?php echo base_url('my/history')?>"><?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }


                    if($menu_order_array->slug == 'common_leader_board')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'site/common-leader-board') ? 'active' : ''; ?>" href="<?php echo base_url('site/common-leader-board')?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }



                    if($menu_order_array->slug == 'home')
                    { 
                      $active_menu = (base_url() == current_url()) ? 'active' : '';
                      ?>
                      
                      <li class="nav-item <?php echo $active_menu; ?>">

                        <a class="nav-link <?php echo $active_menu; ?>" href="<?php echo base_url();?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }


                    if($menu_order_array->slug == 'membership')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'membership') ? 'active' : ''; ?>" href="<?php echo base_url('membership')?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }




                    if($menu_order_array->slug == 'all_quiz_categories')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'all-quiz-categories') ? 'active' : ''; ?>" href="<?php echo base_url('all-quiz-categories')?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }




                    if($menu_order_array->slug == 'contact')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'contact') ? 'active' : ''; ?>" href="<?php echo base_url('contact')?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }


                    if($menu_order_array->slug == 'profile')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'profile') ? 'active' : ''; ?>" href="<?php echo base_url('profile')?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }

                    if($menu_order_array->slug == 'blogs')
                    { ?>
                      
                      <li class="nav-item ">

                        <a class="nav-link <?php echo (uri_string() == 'blogs') ? 'active' : ''; ?>" href="<?php echo base_url('blogs')?>"> <?php echo lang($menu_order_array->title); ?></a>
                      </li>   
                      <?php
                    }

                    if($menu_order_array->slug == 'language')
                    { ?>
                              
                        <li class="nav-item">
                          <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"> <i class=" mr-2 fa fa-language"></i><?php echo lang($menu_order_array->title); ?></a>
                          <div class="dropdown-menu dropdown-menu-arrow" id="session-language-dropdown">
                            <?php 
                            foreach ($this->languages as $key=>$name) : ?>
                              <a href="<?php echo base_url('change-language'); ?>" rel="<?php echo xss_clean($key); ?>" class="dropdown-item ">
                                <?php if ($key == $this->session->language) : ?>
                                  <i class="fa fa-check selected-session-language"></i>
                                <?php endif; ?>
                                <?php echo xss_clean($name); ?>
                              </a>
                            <?php endforeach; ?>
                          </div>
                        </li>
   
                      <?php
                    }

                  }
                }
                ?>


                <li class="nav-item display_on_mobile_only">
                      <label id="switch" class="switch mt-2">
                        <input type="checkbox" class="toggleTheme" id="slider">
                        <span class="slider round"></span>
                      </label>      
                </li>


                 <?php
                  if ($user_id) 
                  { ?>
                      <li class="nav-item display_on_mobile_only">
                        
                        <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown" title="<?php echo xss_clean($full_name_of_user); ?>"><span class="avatar" >
                           <?php $loginImage = ($this->session->logged_in['image'] ? base_url('assets/images/user_image/'.$this->session->logged_in['image']) : base_url('assets/images/user_image/avatar-1.png'))?>
                            <img  alt="" src="<?php echo xss_clean($loginImage);?>" class="rounded-circle mr-1 w-100 h-100">
                        </span>

                          <span class="ml-2 d-none d-lg-block">
                            <span class="text-defaultt"> <?php echo xss_clean($name_of_user); ?></span>
                          </span>

                        </a>

                        <div class="dropdown-menu dropdown-menu-arrow">
                          <?php $rtl_icon = $this->session->is_rtl ? 'rtl-icon' : ""; ?>
                          <a class="dropdown-item" href="<?php echo base_url($profile_url);?>">
                            <i class="dropdown-icon fe fe-user <?php echo $rtl_icon;?>"></i><?php echo lang('user_profile') ?> 
                          </a>
                          

                                                    
                          <?php if ($is_admin == 'Administrator') 
                          { 
                            if(is_loged_in_user_is_subadmin() == FALSE)
                            { ?>
                              <a class="dropdown-item" href="<?php echo base_url("admin/settings");?>">
                                <i class="dropdown-icon fe fe-settings <?php echo $rtl_icon;?>"></i><?php echo lang('admin_admin_settings') ?>
                              </a>
                              <?php
                            }
                            else
                            {
                              ?>
                              <a class="dropdown-item" href="<?php echo base_url("admin/dashboard");?>">
                                <i class="dropdown-icon fas fa-fire <?php echo $rtl_icon;?>"></i><?php echo lang('dashboard') ?>
                              </a>
                              <?php
                            }
                          } ?>

                                                    
                          <?php 
                          if ($is_tutor == TRUE) 
                          { ?>
                            <a class="dropdown-item" href="<?php echo base_url("tutor");?>">
                              <i class="dropdown-icon fas fa-fire <?php echo $rtl_icon;?>"></i><?php echo lang('dashboard') ?>
                            </a>
                            <?php
                          } ?>

                          <div class="dropdown-divider"></div>

                          <a class="dropdown-item" href="<?php echo base_url("logout");?>">
                            <i class="dropdown-icon fe fe-log-out <?php echo $rtl_icon;?>"></i><?php echo lang('sign_out') ?>
                          </a>

                        </div>

                      </li>

                    <?php
                  }
                  else
                  {
                    ?>
                    <li class="nav-item display_on_mobile_only">
                      <a href="<?php echo base_url('login'); ?>" class="nav-link">
                        <i class="fas fa-sign-in-alt mr-3"></i><?php echo lang('login') ?>
                      </a>
                    </li>
                    <?php
                  } ?>

   

              </ul>
            </div>
          </div>
        </div>
      </div>   

      
                

               <!--  <div class="col-md-4 col-sm-4 col-xs-4 text-right  my-auto mobile_nav_bar_button">
                     <button class="navbar-toggler" id="menu_togle_btn" type="button" data-toggle="collapse" data-target="#headerMenuCollapse" aria-controls="headerMenuCollapse" aria-expanded="false" aria-label="Toggle navigation">
                      <i class="fas fa-bars"></i>
                    </button>
                </div> -->



      <div class="header_margin_padding">
        <?php 
        $advertisments_on_position = get_advertisment_by_position('common_under_menu');
        if($advertisments_on_position)
        {
          foreach ($advertisments_on_position as $key => $advertisment_on_position) 
          {
              $ads_img_url = $path = base_url("assets/images/advertisment/$advertisment_on_position->image");
              ?>
              <div class="w-100 my-3 header_add_section">
                <div class="container">
                  <div class="row">
                    <div class="col-12 text-center">
                      <?php
                      if($advertisment_on_position->is_goole_adsense == 0)
                      { ?>
                        <a class="w-100 h-100 d-flex" target="_blank" href="<?php echo $advertisment_on_position->url; ?>">
                          <img class="w-100 advertisment_image_on_front" height="150px" src="<?php echo $ads_img_url; ?>">
                        </a>
                        <?php
                      }
                      else
                      {
                        echo "<div class='w-100 px-2 bg-white'>". htmlspecialchars_decode($advertisment_on_position->google_ad_code)."</div>";
                      }
                      ?>
                      
                    </div>
                  </div>
                </div>
              </div> <?php 
          } 
        }
        ?>

        <?php echo ($content) ?>
      </div>




        <?php 
        $advertisments_on_position = get_advertisment_by_position('common_before_footer');
        if($advertisments_on_position)
        {
          foreach ($advertisments_on_position as $advertisment_on_position) 
          {
              $ads_img_url = $path = base_url("assets/images/advertisment/$advertisment_on_position->image");
              ?>
              <div class="w-100 my-3 footer_add_section">
                <div class="container">
                  <div class="row">
                    <div class="col-12 text-center">
                      <?php
                      if($advertisment_on_position->is_goole_adsense == 0)
                      { ?>
                        <a class="w-100 h-100 d-flex" target="_blank" href="<?php echo $advertisment_on_position->url; ?>">
                          <img class="w-100 advertisment_image_on_front" height="150px" src="<?php echo $ads_img_url; ?>">
                        </a>
                        <?php
                      }
                      else
                      {
                        echo "<div class='w-100 px-2 bg-white'>". htmlspecialchars_decode($advertisment_on_position->google_ad_code)."</div>";
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div> <?php 
          }
        } ?>

      <div class="footer mt-5">

        <div class="container">
          <div class="row">

            <?php 
            $footer_sec_1 =  get_footer_section_helper(1); 
            $footer_sec_2 =  get_footer_section_helper(2); 
            $footer_sec_3 =  get_footer_section_helper(3); 
            $footer_sec_4 =  get_footer_section_helper(4); 
            ?>

            <div class="col-lg-3 Footer_section_1">

              <div class="row">
                <?php
                if($footer_sec_1)
                {                          
                  foreach ($footer_sec_1 as  $first_section_array) 
                  {
                    if($first_section_array->type =='text')
                    {
                      ?>

                      <div class="col-12">
                        <h4 class="text_heading"><?php echo xss_clean($first_section_array->title); ?> </h4>
                        <p class="footer_text_1"><?php echo xss_clean($first_section_array->value); ?> </p>
                      </div>

                      <?php
                    }
                    elseif($first_section_array->type =='link')
                    {

                      ?>

                      <div class="col-12 colum_link_section_1">
                        <a class="link_section_1" href="<?php echo xss_clean($first_section_array->value); ?>"> <?php echo xss_clean($first_section_array->title); ?></a>                              
                      </div>

                      <?php
                    }

                    elseif($first_section_array->type =='editor')
                    {

                      ?>

                      <div class="col-12">
                        <h6><?php echo xss_clean($first_section_array->title); ?> </h6>
                        <?php echo xss_clean($first_section_array->value); ?>
                      </div>

                      <?php
                    }

                    elseif($first_section_array->type =='image')
                    {
                      ?>
                      <div class="col-12 column_1">
                        <h6><?php echo xss_clean($first_section_array->title); ?> </h6>
                        <div class="img_content"> 
                          <?php $img = $first_section_array->value ? $first_section_array->value : 'default.png'; ?>
                          <img src="<?php echo base_url('/assets/images/footer/section/').$img; ?>" class="field_img">
                        </div>
                      </div>
                      <?php
                    }
                  }
                }
                ?>
              </div>

            </div>





            <div class="col-lg-3 Footer_section_2">

              <div class="row">
                <?php
                if($footer_sec_2)
                {                          
                  foreach ($footer_sec_2 as  $second_section_array) 
                  {
                    if($second_section_array->type =='text')
                    {
                      ?>

                      <div class="col-12">
                        <h4 class="text_heading"><?php echo xss_clean($second_section_array->title); ?> </h4>
                        <?php echo xss_clean($second_section_array->value); ?>
                      </div>

                      <?php
                    }
                    elseif($second_section_array->type =='link')
                    {

                      ?>

                      <div class="col-12 colum_link_section_2">
                        <a class="link_section_2" href="<?php echo xss_clean($second_section_array->value); ?>"> <?php echo xss_clean($second_section_array->title); ?></a>                              
                      </div>

                      <?php
                    }

                    elseif($second_section_array->type =='editor')
                    {

                      ?>

                      <div class="col-12">
                        <h6><?php echo xss_clean($second_section_array->title); ?> </h6>
                        <?php echo xss_clean($second_section_array->value); ?>
                      </div>

                      <?php
                    }

                    elseif($second_section_array->type =='image')
                    {
                      ?>
                      <div class="col-12">
                        <h6><?php echo xss_clean($second_section_array->title); ?> </h6>
                        <div class="img_content"> 
                          <?php $img = $second_section_array->value ? $second_section_array->value : 'default.png'; ?>
                          <img src="<?php echo base_url('/assets/images/footer/section/').$img; ?>" class="field_img">
                        </div>
                      </div>
                      <?php
                    }
                  }
                }
                ?>
              
              </div>


            </div>


            <div class="col-lg-3 Footer_section_3">

              <div class="row">
                <?php
                if($footer_sec_3)
                {                          
                  foreach ($footer_sec_3 as  $third_section_array) 
                  {
                    if($third_section_array->type =='text')
                    {
                      ?>

                      <div class="col-12">
                        <h4 class="text_heading"><?php echo xss_clean($third_section_array->title); ?> </h4>
                        <?php echo xss_clean($third_section_array->value); ?>
                      </div>

                      <?php
                    }
                    elseif($third_section_array->type =='link')
                    {

                      ?>

                      <div class="col-12 colum_link_section_3">
                        <a class="link_section_3" href="<?php echo xss_clean($third_section_array->value); ?>"> <?php echo xss_clean($third_section_array->title); ?></a>                              
                      </div>

                      <?php
                    }

                    elseif($third_section_array->type =='editor')
                    {

                      ?>

                      <div class="col-12">
                        <h6><?php echo xss_clean($third_section_array->title); ?> </h6>
                        <?php echo xss_clean($third_section_array->value); ?>
                      </div>

                      <?php
                    }

                    elseif($third_section_array->type =='image')
                    {
                      ?>
                      <div class="col-12">
                        <h6><?php echo xss_clean($third_section_array->title); ?> </h6>
                        <div class="img_content"> 
                          <?php $img = $third_section_array->value ? $third_section_array->value : 'default.png'; ?>
                          <img src="<?php echo base_url('/assets/images/footer/section/').$img; ?>" class="field_img">
                        </div>
                      </div>
                      <?php
                    }
                  }
                }
                ?>
              </div>

            </div>


            <div class="col-lg-3 Footer_section_4">

              <div class="row">
                <?php
                if($footer_sec_4)
                {                          
                  foreach ($footer_sec_4 as  $fourth_section_array) 
                  {
                    if($fourth_section_array->type =='text')
                    {
                      ?>

                      <div class="col-12">
                        <h4 class="text_heading"><?php echo xss_clean($fourth_section_array->title); ?> </h4>
                        <?php echo xss_clean($fourth_section_array->value); ?>
                      </div>

                      <?php
                    }
                    elseif($fourth_section_array->type =='link')
                    {

                      ?>

                      <div class="col-12">
                        <a href="<?php echo xss_clean($fourth_section_array->value); ?>"> <?php echo xss_clean($fourth_section_array->title); ?></a>                              
                      </div>

                      <?php
                    }

                    elseif($fourth_section_array->type =='editor')
                    {

                      ?>

                      <div class="col-12">
                        <h6><?php echo xss_clean($fourth_section_array->title); ?> </h6>
                        <?php echo xss_clean($fourth_section_array->value); ?>
                      </div>

                      <?php
                    }

                    elseif($fourth_section_array->type =='image')
                    {
                      ?>
                      <div class="col-12">
                        <h6><?php echo xss_clean($fourth_section_array->title); ?> </h6>
                        <div class="img_content"> 
                          <?php $img = $fourth_section_array->value ? $fourth_section_array->value : 'default.png'; ?>
                          <img src="<?php echo base_url('/assets/images/footer/section/').$img; ?>" class="field_img">
                        </div>
                      </div>
                      <?php
                    }
                  }
                }
                ?>
              </div>

            </div>
          </div>

        </div>
      </div>


      <footer class="footer">
        <div class="container">
          <div class="row">
            <div class="col-12 text-center copyright_footer">
              <?php echo $this->settings->footer_text ?>
            </div>
          </div>
        </div>
        <?php 
        if($this->settings->cookies_content_display == "YES")
        {

         ?>

        <!-- START Bootstrap-Cookie-Alert -->
        <div class="alert text-center cookiealert" role="alert">
          <!-- <b><?php //echo 'Do you like cookies' ?></b> &#x1F36A; <?php //echo 'We use cookies'; ?> <a href="https://cookiesandyou.com/" target="_blank"><?php //echo 'Learn more'; ?></a> -->
          <?php echo $this->settings->cookies_content; ?>

          <button type="button" class="btn btn-primary btn-sm acceptcookies" aria-label="Close">
             <?php echo $this->settings->cookies_content_btn_text; ?>
          </button>
        </div>
        <!-- END Bootstrap-Cookie-Alert -->

        <?php
        } ?>

      </footer>

    </div>
  </div>

      <?php // Javascript files ?>
      <?php if (isset($js_files) && is_array($js_files)) : ?>
        <?php foreach ($js_files as $js) : ?>
        <?php if ( ! is_null($js)) : ?>
          <?php $separator = (strstr($js, '?')) ? '&' : '?'; ?>
          <?php echo "\n"; ?><script type="text/javascript" src="<?php echo xss_clean($js); ?><?php echo xss_clean($separator); ?>v = <?php echo xss_clean($this->settings->site_version); ?>"></script><?php echo "\n"; ?>
        <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (isset($js_files_i18n) && is_array($js_files_i18n)) : ?>
        <?php foreach ($js_files_i18n as $js) : ?>
          <?php if ( ! is_null($js)) : ?>
            <?php echo "\n"; ?><script type="text/javascript"><?php echo "\n" . $js . "\n"; ?></script><?php echo "\n"; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <script type="text/javascript">  
          <?php if(!empty(get_admin_setting('footer_javascript'))) { echo html_entity_decode(get_admin_setting('footer_javascript')); } ?>
      </script>





      <script>
        $(function() {
          $( '#dl-menu' ).dlmenu({
            animationClasses : { classin : 'dl-animate-in-5', classout : 'dl-animate-out-5' }
          });
        });
      </script>


    <div class="modal fade" id="quiz_all_in_one_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content quiz_all_in_one_modal_content">
          <div class="modal-header">
            <h2 class="modal-title quiz_all_in_one_modal_title text-info text-uppercase" id="quiz_all_in_one_modal_title">Modal title</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body quiz_all_in_one_modal_body">
            ...
          </div>
          <div class="modal-footer quiz_all_in_one_modal_footer">
            <a href="javascript:void(0)" class="btn btn-warning quiz_all_in_one_modal_footer_action">Action</a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

</body>
</html>