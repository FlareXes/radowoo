<div class="container home_page">
  <div class="row">

    <?php if($get_page_content)  { ?>
      <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12 mb-4">
        <p><?php echo $get_page_content->content; ?></p>
      </div>
    <?php } ?>  
    <?php 
    $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
    $category_img_dir = base_url("assets/images/category_image/");
    $data['is_premium_member'] = $is_premium_member;
    $data['paid_quizes_array'] = $paid_quizes_array;
    
    if($category_data)
    {  
      $i = 0;
      foreach ($category_data as $category_array) 
      {
        $i++;
        $difficulty_star =  get_category_stars($category_array->id,$user_id); 
        $category_image = $category_array->category_image ? $category_array->category_image : "default.jpg";
        $category_image_name = $category_image ; 
        $category_image = $category_img_dir.$category_image;
        if(!is_file(FCPATH."assets/images/category_image/".$category_image_name))
        {
          $category_image = base_url('assets/default/default.jpg');
        }
        ?>

        <div class="col-md-4 col-xl-4 col-xs-12 col-sm-6 category_data_section" >  
          <div class="grid">
            <figure class="effect-ming">
              <img src="<?php echo xss_clean($category_image); ?>" alt="img09"/>
              <figcaption>
                

                <?php 
                $category_url = base_url('category/').$category_array->category_slug;
                $category_class = "";
                if($user_id > 0 && $difficulty_star) 
                { 
                  $category_url = "javascript:void(0)";
                  $category_class = "difficulty_level_model";
                } ?>

                
                <a href="<?php echo $category_url; ?>" class="<?php echo $category_class; ?>" data-category_slug='<?php echo $category_array->category_slug; ?>'>&nbsp;</a>
                  <div class="row">
                    <div class="col-12 mb-3">
                      <h2 class="category_name"><?php echo xss_clean($category_array->category_title);?></h2>
                    </div>
                    <?php
                    if($user_id > 0 && $difficulty_star) 
                    { 
                      $easy_star = ($difficulty_star['d_l_1'] == 1) ? "gold-star" : "";
                      $medium_star = ($difficulty_star['d_l_2'] == 2) ? "gold-star" : "";
                      $hard_star = ($difficulty_star['d_l_3'] == 3) ? "gold-star" : "";
                      ?>
                      <div class="col-12">
                        <span><i class="fa fa-star level-star <?php echo $easy_star;?>"></i></span>
                        <span><i class="fa fa-star level-star <?php echo $medium_star;?>"></i></span>
                        <span><i class="fa fa-star level-star <?php echo $hard_star;?>"></i></span>
                      </div>
                      <?php 
                    } ?>                  
                  </div>
                
                  
              </figcaption>     
            </figure>
          </div>
        </div>

        <?php 
        if($i == 6 OR $i > 6) { break;}
      }

      if($i == 6 OR $i > 6)
      { ?>
        
        <div class="col-md-12 col-xl-12 col-sm-12 text-center my-5"> 
          <a href="<?php echo base_url('all-quiz-categories'); ?>" class="btn btn-success btn-lg mt-5"><?php echo lang("view_all_categories") ?></a>
        </div>

        <?php
      } 
    }
    else
    { ?>

      <div class="col-12 text-center mt-5">
        <h3 class="text-danger mt-5"><?php echo lang('no_category_found_in')." "; ?>   </h3>

      </div>
      <?php
    } ?>  



        <?php 
        $advertisments_on_position = get_advertisment_by_position('home_page_below_category');
        if($advertisments_on_position)
        {

          foreach ($advertisments_on_position as $advertisment_on_position) 
          {
              $ads_img_url = $path = base_url("assets/images/advertisment/$advertisment_on_position->image");
              ?>
              <div class="col-12 text-center p-0 my-5">
                <?php
                if($advertisment_on_position->is_goole_adsense == 0)
                { ?>
                    <a class="w-100 h-100 d-flex" target="_blank" href="<?php echo $advertisment_on_position->url; ?>">
                      <img class="w-100 advertisment_image_on_front" height="200px" src="<?php echo $ads_img_url; ?>">
                    </a>
                  <?php
                }
                else
                {
                    echo "<div class='w-100 px-2 bg-white'>". htmlspecialchars_decode($advertisment_on_position->google_ad_code)."</div>";
                }
                ?>
              </div>
               <?php 
          }
        } ?>

      <?php $latest_quiz_show_on_home = (isset($this->settings->latest_quiz_show_on_home) && $this->settings->latest_quiz_show_on_home == "YES") ? TRUE : FALSE; ?>


       <?php 
      if($latest_quiz_show_on_home == TRUE)
      {  ?>
        <!-- Latest Quiz Work Start --> 
          <div class="col-12">
            <div class="row">
              <div class="col-12 text-center"> <h2 class="heading"><?php echo lang('latest_quizes') ?></h2> <hr></div>
              <?php 
                  $data['quiz_list_data'] = $latest_quiz_data;
                  $this->load->view('quiz_data_list',$data); 
              ?>
            </div>
          </div>   
          <hr> 
          <!-- Latest Quiz Work End -->
           <?php 
      }  ?>

      <?php $popular_quiz_show_on_home = (isset($this->settings->popular_quiz_show_on_home) && $this->settings->popular_quiz_show_on_home == "YES") ? TRUE : FALSE; ?>


      <?php 
      if($popular_quiz_show_on_home == TRUE)
      {  ?>
        <!-- Popular Quiz Work Start -->
        <div class="col-12">
          <div class="row">
            <div class="col-12 text-center"> 
              <h2 class="heading"><?php echo lang('popular_quizes'); ?></h2><hr>
            </div>
            <?php
              $data['quiz_list_data'] = $popular_quiz_data;
              $this->load->view('quiz_data_list',$data); 
            ?>
              
          </div>
        </div>
        <hr>
        <!-- Popular Quiz Work End --> 
        <?php 
      }  ?>

      <?php $upcoming_quiz_show_on_home = (isset($this->settings->upcoming_quiz_show_on_home) && $this->settings->upcoming_quiz_show_on_home == "YES") ? TRUE : FALSE; ?>
      <?php 
      if($upcoming_quiz_show_on_home == TRUE)
      {  ?>
        <!-- Upcoming Quiz Work Start -->
        <div class="col-12">
          <div class="row">
            <div class="col-12 text-center"> 
              <h2 class="heading"><?php echo lang('upcoming_quizes'); ?></h2><hr>
            </div> 
            <?php
              $data['quiz_list_data'] = $upcoming_quiz_data;
              $this->load->view('quiz_data_list',$data); 
            ?> 
          </div>
        </div>
        <!-- Upcoming Quiz Work End -->
      <?php } ?>  
  </div>
</div>

<!-- study start matirial -->

<div class="container"> 
        


    <?php $show_home_page_latest_study_material = (isset($this->settings->show_home_page_latest_study_material) && $this->settings->show_home_page_latest_study_material == "YES") ? TRUE : FALSE; ?>


    <?php
    if($latest_study_material_data && $show_home_page_latest_study_material == TRUE)
    { ?>
        <!-- Latest study material Work Start --> 
        <div class="col-12">
          <div class="row">
            <div class="col-12 text-center"> <h2 class="heading"><?php echo lang('latest_study_material') ?></h2> <hr></div>
            <?php 
                $data['paid_s_m_array'] = $paid_s_m_array;
                $data['study_material_list_data'] = $latest_study_material_data;
                $this->load->view('study_material_data_list',$data); 
            ?>
          </div>
        </div>
        <!-- Latest study material Work End -->  
      <?php
    }?>

      
      <?php $show_home_page_popular_study_material = (isset($this->settings->show_home_page_popular_study_material) && $this->settings->show_home_page_popular_study_material == "YES") ? TRUE : FALSE; ?>

      <?php
      if($popular_study_material_data  && $show_home_page_popular_study_material == TRUE)
      { ?>
        <!-- Popular study material Work Start -->
        <div class="col-12">
          <div class="row">
            <div class="col-12 text-center"> <h2 class="heading"><?php echo lang('popular_study_material') ?></h2> <hr></div>
            <?php 
                $data['study_material_list_data'] = $popular_study_material_data;
                $this->load->view('study_material_data_list',$data); 
            ?>
          </div>
        </div>
        <!-- Popular study material Work End --> 
        <?php
      }?>
</div>

<!-- End study matirial -->


<?php $show_home_page_testimonial = (isset($this->settings->show_home_page_testimonial) && $this->settings->show_home_page_testimonial == "YES") ? TRUE : FALSE; ?>

<?php
if($testimonial_data && $show_home_page_testimonial == TRUE)
{ ?>
  <div class="container-fluid p-0">
    <!-- Testimonials Work Start -->
    <div class="col-12 text-center">
      <h2 class="text-center heading"><?php echo lang('front_testimonial'); ?></h2>
      <hr />
    </div>
    
    <section class="testimonial-section" style="<?php echo get_admin_setting('testimonial_background');?> ">
      <div class="testimonials testimonial-reel">
          <?php
          $testimonial_path  = base_url('/assets/images/testimonial/');
          foreach ($testimonial_data as  $testimonial_array) 
          { 

            $image_name = $testimonial_array->profile ? $testimonial_array->profile : "user.png";
            $image_real_name = $image_name ; 
            $image_name = $testimonial_path.$image_real_name;
            if(!is_file(FCPATH."assets/images/testimonial/".$image_real_name))
            {
              $image_name = base_url('assets/default/user.png');
            }
            $testimonial_profile = $image_name;

            ?>
            <div class="testimonial">
              <p>“<?php echo strip_tags($testimonial_array->content); ?>”</p>
              <img src="<?php echo xss_clean($testimonial_profile); ?>">
              <div class="details">
                <span><?php echo xss_clean($testimonial_array->name); ?></span>
              </div>
            </div>
            <!-- / Testimonial -->
            <?php
          } ?>
      </div>
    </section>
    <!-- Testimonials Work End -->
  </div><?php
} 
?>

  <?php 
  $advertisments_on_position = get_advertisment_by_position('home_page_below_testimonials');
  if($advertisments_on_position)
  {
    foreach ($advertisments_on_position as $advertisment_on_position) 
    {
        $ads_img_url = $path = base_url("assets/images/advertisment/$advertisment_on_position->image");
        ?>
          <div class="container">
            <div class="row">
              <div class="col-12 text-center p-0 my-5">
                <?php
                if($advertisment_on_position->is_goole_adsense == 0)
                { ?>
                  <a class="w-100 h-100 d-flex" target="_blank" href="<?php echo $advertisment_on_position->url; ?>">
                    <img class="w-100 advertisment_image_on_front" height="200px" src="<?php echo $ads_img_url; ?>">
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
         <?php      
    }
  } ?>




<?php $show_home_page_sponser = (isset($this->settings->show_home_page_sponser) && $this->settings->show_home_page_sponser == "YES") ? TRUE : FALSE; ?>

<?php 
if($sponser_data && $show_home_page_sponser == TRUE)
{ 
  $sponser_logo_path  = base_url('assets/images/sponsors/');
  ?>
  <div class="container home_page">
    <div class="row">
      <!-- Sponsers Work Start -->
      <div class="col-12">
          <div class="row">
            <div class="col-12 text-center"> <h2 class="heading"><?php echo lang('our_partners'); ?>  </h2> <hr></div>
          </div>
          <div class="sponsers text-center" data-slick='{"slidesToShow": 4, "slidesToScroll": 1}'>
            <?php

            foreach ($sponser_data as $sponser_array) 
            { 
              $image_name = $sponser_array->logo ? $sponser_array->logo : "default.png";
              $image_real_name = $image_name ; 
              $image_name = $sponser_logo_path.$image_real_name;
              if(!is_file(FCPATH."assets/images/sponsors/".$image_real_name))
              {
                $image_name = base_url('assets/default/default.png');
              }

              $sponser_logo = $image_name;
              ?>


              <div class="col-6 sponser">
                <a href="<?php echo xss_clean($sponser_array->link); ?>" title="<?php echo xss_clean($sponser_array->name); ?>" target="_blank">
                  <img src="<?php echo $sponser_logo; ?>">
                </a>
              </div>

              <?php
            } ?>

          </div>
      </div>
      <!-- Sponsers Work End -->
    </div>
  </div> <?php
} ?>
     