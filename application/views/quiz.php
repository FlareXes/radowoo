<style>
 .sub-category{
  line-height: 9px;
  padding-left: 5px;
    padding-bottom: 13px;
    float: left;
 }
 .rating-star
 {
    line-height: 3px;
    padding: 0px 4px;
    float: left;
 }
 .rating-up-down{
  font-size: 14px;
  line-height: 14px;
  float: left;
 }
 .star-size
 {
  font-size: 11px;
 }
 .rating-star-main
 {
  padding: 5px 0px;
 }
 .quiz-filter
 {
  position: absolute;
  right: 12px;
  top: 9px;
 }

</style>
<div class="container">


  <div class="row">
    <div class="col-12 text-center"> 
      <h2 class="heading"><?php echo ucwords($category_data->category_title); ?></h2><hr>
    </div>
    <?php 
      $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
      $data['is_premium_member'] = $is_premium_member;
      $data['paid_quizes_array'] = $paid_quizes_array;
      $data['paid_s_m_array'] = $paid_s_m_array;

      $category_img_dir = base_url("assets/images/category_image/");
      if($sub_category_data && $this->settings->sub_category_show_or_not == 'Enable')
      {
        foreach ($sub_category_data as $category_array) 
        {
          $difficulty_star =  get_category_stars($category_array->id, $user_id);

          $category_image = $category_array->category_image ? $category_array->category_image : "default.jpg";
          $category_image_name = $category_image ; 
          $category_image = $category_img_dir.$category_image;
          if(!is_file(FCPATH."assets/images/category_image/".$category_image_name))
          {
            $category_image = base_url('assets/default/default.jpg');
          } ?>

            <div class="col-md-4 col-xl-4 col-xs-12 col-sm-6 sub_category_data_section category_data_section mb-5" > 
              
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
            </div> <?php 
        }
        
      } ?>
    <div class="col-12 bg-white">
      <div class="row">
        <div class="col-2">
          <div class="accordionnn md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
            <div class="card mb-1">
              <div class="card-header px-1" role="tab" id="headingTwo2">
                <a class="collapsed" data-toggle="collapse" href="#collapseTwo2"
                   aria-expanded="false" aria-controls="collapseTwo2">
                  <h5 class="mb-0">
                    <?php echo lang('sub_category'); ?> <i class="fas fa-angle-down rotate-icon"></i>
                  </h5>
                </a>
              </div>
              
              <div id="collapseTwo2" class="collapse show" >
                <div class="card-body py-2 px-1">
                  <input type="hidden" class="category-slug" value="<?php echo $category_slug; ?>">
                  <?php 
                    if($sub_category_data)
                    {
                      $sub_categories_array = explode('~~||~~', $sub_category);
                      
                      foreach ($sub_category_data as $category_array) 
                      {
                        $checked = in_array($category_array->category_title, $sub_categories_array) ? "checked='checked'" : ''; 
                  ?>  
                      <input type="checkbox" <?php echo $checked; ?> class="float-left filter-click" data-field_slug="sub-category" name="subcategory" value="<?php echo $category_array->category_title; ?>"><div class="sub-category"><?php echo $category_array->category_title; ?></div>
                      <div class="clearfix"></div>
                  <?php    
                    } }
                    else { echo 'no sub category';}
                  ?>
                </div>
              </div>
            </div>

            <div class="card mb-1">
              <div class="card-header px-1" role="tab" id="headingThree3">
                <a class="collapsed" data-toggle="collapse" href="#collapseThree3">
                  <h5 class="mb-0">
                    <?php echo lang('rating'); ?> <i class="fas fa-angle-down rotate-icon"></i>
                  </h5>
                </a>
              </div>
              
              <div id="collapseThree3" class="collapse show" role="tabpanel" >
                <div class="card-body p-1 ">
                  <div class="rating-star-main">
                    <?php $rating_checked = (isset($rating) && $rating == '4.5' ? "checked='checked'" : ''); ?>
                    <input type="radio" <?php echo $rating_checked; ?> name="rattings" value="4.5" class="float-left filter-click" data-field_slug="rating">
                    <div class="average-star rating-star">
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star-half fill-star" aria-hidden="true"></i></span>
                       
                    </div>
                    <div class="rating-up-down">4.5 & up</div>
                    <div class="clearfix"></div>
                  </div>
                  
                  <div class="rating-star-main">
                    <?php $rating_checked = (isset($rating) && $rating == '4.0' ? "checked='checked'" : ''); ?>
                    <input type="radio" <?php echo $rating_checked; ?> name="rattings" value="4.0" class="float-left filter-click" data-field_slug="rating">
                    <div class="average-star rating-star">
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="far fa-star star-size" aria-hidden="true"></i></span>
                       
                    </div>
                    <div class="rating-up-down">4.0 & up</div>
                    <div class="clearfix"></div>
                  </div>

                  <div class="rating-star-main">
                    <?php $rating_checked = (isset($rating) && $rating == '3.5' ? "checked='checked'" : ''); ?>
                    <input type="radio" <?php echo $rating_checked; ?> name="rattings" value="3.5" class="float-left filter-click" data-field_slug="rating">
                    <div class="average-star rating-star">
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star-half fill-star" aria-hidden="true"></i></span>
                      <span><i class="far fa-star star-size" aria-hidden="true"></i></span>
                       
                    </div>
                    <div class="rating-up-down">3.5 & up</div>
                    <div class="clearfix"></div>
                  </div>

                  <div class="rating-star-main">
                    <?php $rating_checked = (isset($rating) && $rating == '3.0' ? "checked='checked'" : ''); ?>
                    <input type="radio" <?php echo $rating_checked; ?> name="rattings" value="3.0" class="float-left filter-click" data-field_slug="rating">
                    <div class="average-star rating-star">
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                      <span><i class="far fa-star star-size" aria-hidden="true"></i></span>
                      <span><i class="far fa-star star-size" aria-hidden="true"></i></span>
                       
                    </div>
                    <div class="rating-up-down">3.0 & up</div>
                    <div class="clearfix"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card mb-1">
              <div class="card-header px-1" role="tab" id="headingfour">
                <a class="collapsed" data-toggle="collapse" href="#collapsefour">
                  <h5 class="mb-0">
                    <?php echo lang('price'); ?> <i class="fas fa-angle-down rotate-icon"></i>
                  </h5>
                </a>
              </div>

              <div id="collapsefour" class="collapse show" role="tabpanel">
                <div class="card-body p-1">
                  <?php $price_paid = (isset($price) && $price == 'paid' ? "checked='checked'" : ''); 
                      $price_free = (isset($price) && $price == 'free' ? "checked='checked'" : ''); 
                  ?>
                  <input type="radio" <?php echo $price_paid; ?> class="float-left filter-click" data-field_slug="price" name="price" value="paid">
                  <div class="sub-category"><?php echo lang('paid'); ?></div>
                  <div class="clearfix"></div>
                  <input type="radio" <?php echo $price_free; ?> class="float-left filter-click" data-field_slug="price" name="price" value="free">
                  <div class="sub-category"><?php echo lang('free'); ?></div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>
        
          
        </div>
        <div class="col-10"> 
        <ul class="nav nav-tabs m-0 p-0 bg-white" id="my-profile-Tab" role="tablist">
           <li class="nav-item w-30">
              <a class="nav-link active" id="quiz-tab" data-toggle="tab" href="#quiz" role="tab" aria-controls="quiz" aria-selected="false"><?php echo lang('quiz'); ?></a>
           </li>
           <li class="nav-item w-30">
              <a class="nav-link " id="study-material-tab" data-toggle="tab" href="#study-material" role="tab" aria-controls="material" aria-selected="true"><?php echo lang('study_material'); ?></a> 
           </li>
           
           <div class="quiz-filter mb-4">
              <form action="" method="GET" id="Quiz_filter_form">
                <div class="form-controll">
                  <select class="form-control" id="Quiz_filter" name="most">
                    <option  value="">Select One</option>
                    <option <?php echo $this->input->get('most') == 'recent' ? 'Selected': ''; ?> value="recent"><?php echo lang('most_recent'); ?></option>
                    <option <?php echo $this->input->get('most') == 'liked' ? 'Selected': ''; ?> value="liked"><?php echo lang('most_liked'); ?></option>
                    <option <?php echo $this->input->get('most') == 'attended' ? 'Selected': ''; ?> value="attended"><?php echo lang('most_attended'); ?></option>
                  </select>
                </div>
              </form>
            </div>
            
         </ul> 
         <div class="tab-content" id="my-profile-TabContent">
            <div class="tab-pane fade active show" id="quiz" role="tabpanel" aria-labelledby="quiz-tab">
              <div class="bg-white card-primary px-5">
                <div class="row pt-3">
                  <?php
                    $data['quiz_list_data'] = $quiz_data;
                    $this->load->view('quiz_data_list',$data);  
                  ?>
                  <?php echo xss_clean($pagination) ?>
                </div>  
              </div>
            </div>
            <div class="tab-pane fade " id="study-material" role="tabpanel" aria-labelledby="study-material-tab">
              <div class="bg-white card-primary px-5">
                <?php
                  if($category_study_material_data)
                  { ?>
                    <div class="row my-4">
                      
                      <?php 
                          $data['study_material_list_data'] = $category_study_material_data;
                          $this->load->view('study_material_data_list',$data); 
                      ?>
                      <div class="col-12">
                         <?php echo xss_clean($pagination) ?>
                      </div>
                    </div>
                   
                    <?php 
                  } 
                  else 
                  {
                ?>
                    <div class="col-12 text-center text-danger pt-3"> <?php echo lang('no_study_material_found'); ?></div>
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

