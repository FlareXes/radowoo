<div class="container">
  <div class="row">
    <!-- <div class="col-12 text-center"> 
      <h2 class="heading"><?php //echo ucwords($category_data->category_title); ?></h2><hr>
    </div> -->
    <?php $category_img_dir = base_url("assets/images/category_image/"); ?>
  </div>
  <div class="row">
    <?php
      $data['quiz_list_data'] = $quiz_data;
      $this->load->view('quiz_data_list_modal',$data);  
    ?>

    <div class="clearfix"> </div>
    <div class="col-12 my-3 text-center">
      <!-- <a href="<?php //echo  base_url('category/').$category_data->category_slug ?>" class="btn btn-info"> View All</a>  -->
    </div>
  </div>
</div>
