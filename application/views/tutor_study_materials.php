<?php 
  $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
  $data['user_id'] = $user_id;
  $data['is_premium_member'] = $is_premium_member;
  $data['paid_s_m_array'] = $paid_s_m_array;
?>

<div class="container">
  <div class="row">
    <div class="col-12 text-center"> 
      <h2 class="heading"><?php echo ucwords($user_data->first_name." ".$user_data->last_name); ?></h2><hr>
    </div>
  </div>


  <div class="row">
    <!-- <div class="col-12 py-5"><?php //echo lang('tutor_study_materials'); ?></div>    -->
    
    <?php
      $data['study_material_list_data'] = $study_material_data;
      $this->load->view('study_material_data_list',$data); 
    ?>

    <div class="col-12 my-5">
      <?php echo xss_clean($pagination) ?>
    </div>

  </div>

</div>

