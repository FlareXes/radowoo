<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav"> 
    <ul class="navbar-nav">
    	<?php 
          $package_active = $package_quiz = $package_study_material = "";
          if(uri_string() == "admin/package/package_update/".$tab_package_id)
          {
            
            $package_active = "active";
          }
          if(uri_string() == "admin/package/package_quiz/".$tab_package_id)
          {
            $package_quiz = "active";
          }
          if(uri_string() == "admin/package/package_study_material/".$tab_package_id)
          {
            $package_study_material = "active";
          }
      ?>          
      <li class="nav-item <?php echo $package_active;?>">
        <a class="nav-link " href="<?php echo base_url('admin/package/package_update/'.$tab_package_id);?>"><?php echo lang('admin_edit_quiz_package'); ?></a>
      </li>
      <li class="nav-item <?php echo $package_quiz;?>">
        <a class="nav-link " href="<?php echo base_url('admin/package/package_quiz/'.$tab_package_id);?>"><?php echo lang('admin_package_quiz'); ?></a>
      </li>
      <li class="nav-item <?php echo $package_study_material;?>">
        <a class="nav-link " href="<?php echo base_url('admin/package/package_study_material/'.$tab_package_id);?>"><?php echo lang('admin_package_study_material'); ?></a>
      </li>
    </ul>
  </div>
</nav>