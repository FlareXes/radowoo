
<div class="container home_page">

    <div class="row my-5">
      <?php
      if($filtered_db_record)
      { 
        if($find_like)
        { 
          ?>
          <div class="col-12 text-center alert alert-info">
            <h5 class="text-center"><?php echo lang('we_are_showing_results_for') ?> <span class="text-warning"><?php echo $find_like; ?></span></h5>
          </div>
          <?php
        }
      }
      else
      { ?>
          <div class="col-12 text-center alert alert-danger">
            <h5 class="text-center"><?php echo lang('no_result_found_for') ?> 
              <span class="text-warning"><?php echo $find_like; ?></span>
            </h5>
          </div>
        <?php
      } ?>
    </div>

  <div class="row">
    <div class="col-md-12">
      <?php 

        $data['user_id'] = $user_id;
        $data['paid_quizes_array'] = $paid_quizes_array;
        $data['paid_quizes_array'] = $paid_quizes_array;
        $data['all_category_data'] = $all_category_data;
        $data['find_like'] = $find_like;
        $data['serch_url'] = $serch_url;
        $data['box_to_show_on_row'] = 3;
        $data['filter_category'] = $filter_category;
        $data['filter_purchage_type'] = $filter_purchage_type;
        $data['filter_rating'] = $filter_rating;
        $data['filter_duration'] = $filter_duration;


      if($search_itesm == "category")
      { 
        $data['category_data'] = $filtered_db_record;
        ?>
        <div class="row">
          <?php $this->load->view('category_box',$data); ?>
        </div>
        <?php 
      }

      if($search_itesm == "quiz")
      {
          $data['quiz_list_data'] = $filtered_db_record;
          $data['session_quiz_data'] = $session_quiz_data;
       ?>

        <div class="row">
          <div class="col-3">
              <?php $this->load->view('search_sidebar',$data); ?>
          </div>
          <div class="col-9">
            <div class="row">
              <?php $this->load->view('quiz_data_list',$data); ?>
            </div>
          </div>
        </div>
        <?php 
      }

      if($search_itesm == "study")
      {
        $data['study_material_list_data'] = $filtered_db_record;
        ?>
        <div class="row">
          <div class="col-3">
              <?php $this->load->view('search_sidebar',$data); ?>
          </div>
          <div class="col-9">
            <div class="row">
              <?php $this->load->view('study_material_data_list',$data); ?>
            </div>
          </div>
        </div>
        <?php
      }


      if($search_itesm =="tutor")
      {
        $user_data['users_like'] = $users_like;
        $this->load->view('user_box',$user_data);
      }
      
      ?>
    </div>
   </div>
 </div>


