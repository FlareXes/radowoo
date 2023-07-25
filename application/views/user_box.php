      <div class="row my-5">
       <div class="col-12 text-center">
         <!-- <h4 class="text-info w-100 text-center"><?php // echo lang('result_for_tutor'); ?></h4> -->
       </div>
          <?php 

          $user_img_dir = base_url('assets/images/user_image/');

          if($users_like)
          { 
            foreach ($users_like as $user_obj) 
            {

              $user_image = $user_obj->image ? $user_obj->image : "default.jpg";
              $user_image_name = $user_image ; 
              $user_image = $user_img_dir.$user_image;

              if(!is_file(FCPATH."assets/images/user_image/".$user_image_name))
              {
                $user_image = base_url('assets/default/default.jpg');
              }
              $tutor_profile_url = base_url('tutor-profile/').$user_obj->username;
              ?>

              <div class="col-md-4 col-xl-4 col-xs-12 col-sm-6 category_data_sectionn" > 
                <div class="grid">
                  <figure class="effect-ming">
                    <img src="<?php echo xss_clean($user_image); ?>" alt="<?php echo $user_obj->first_name; ?>"/>
                    <figcaption>
                      
                      <!-- <a href="<?php //echo $tutor_profile_url; ?>" class="<?php //echo $category_class; ?>" data-category_slug='<?php //echo $user_obj->category_slug; ?>'>&nbsp;</a> -->

                      <div class="row">
                        <div class="col-12 mb-3">
                          <h2 class="category_name"><?php echo xss_clean($user_obj->first_name." ".$user_obj->last_name);?></h2>
                        </div> 
                        <div class="col-12">
                          <a class="btn btn-info" href="<?php echo base_url('tutor-quiz/').$user_obj->username; ?>"><?php echo lang('quizes'); ?></a>
                          <a class="btn btn-primary" href="<?php echo base_url('tutor-study-material/').$user_obj->username; ?>"><?php echo lang('study_data'); ?></a>
                        </div>                
                      </div>

                    </figcaption>     
                  </figure>
                </div>
              </div>
              <?php 
              
            }
          }
          else
          { ?>

            <div class="col-12 text-center mt-5">
              <h3 class="text-danger mt-5"><?php echo lang('no_tutor_fond')." "; ?>                
              </h3>
            </div>
            <?php
          }?> 

      </div>