<?php defined('BASEPATH') OR exit('No direct script access allowed');
  $setting_group = array();
  foreach ($settings as $setting) 
  {
    $setting_group[$setting['setting_group']][] = $setting;  
  }

?>

<div class="col-12 col-md-12 col-lg-12">
   <div class="card">
      <div class="card-body setting_form">
        <div class="row">

            <div class="col-md-12 mb-5">
              <hr>
            </div>
          </div>
         <?php echo form_open_multipart('', array('role'=>'form')); ?>
         <div class="row">

          <div class="col-3 setting_form_nav">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              
              <?php
              $first = TRUE;
              foreach ($setting_group as $key => $groups)
              { 
                if($first == TRUE)
                {
                  $active = 'active';
                  $first = FALSE;
                }
                else
                {
                  $active = '';
                }
                ?>
                  <a class="nav-link <?php echo $active; ?>" id="v-pills-tab-<?php echo slugify_string($key); ?>" data-toggle="pill" href="#v-pills-<?php echo slugify_string($key); ?>" role="tab" aria-controls="v-pills-<?php echo slugify_string($key); ?>" aria-selected="true">
                    <?php echo lang($key);?>
                  </a>

              <?php
              }
              ?>
            </div>
          </div>

          <div class="col-9 setting_form_tab">

            <div class="tab-content" id="v-pills-tabContent">


              <?php
              $line = FALSE;
              $first = TRUE;
              foreach ($setting_group as $key => $groups)
              { 
    
                if($first == TRUE)
                {
                  $active = 'show active ';
                  $first = FALSE;
                }
                else
                {
                  $active = '';
                }

                ?>

                 <div class="tab-pane fade <?php echo $active; ?>" id="v-pills-<?php echo slugify_string($key); ?>" role="tabpanel" aria-labelledby="v-pills-<?php echo slugify_string($key); ?>-tab">


                  <?php
                  if($key == "mail setting" && $line == FALSE)
                  {
                    $line = TRUE;
                    ?>
                      <div class="row">
                        <div class="col-md-12 ">
                          <div class="float-right">
                            <a href="<?php echo base_url("admin/settings/test_mail"); ?>" class="btn btn-warning"> TEST MAIL</a>
                          </div>
                        </div>
                      </div>
                    <?php
                  }

                  echo $this->load->view('admin/settings/common_setting_tab',array('setting_section'=>$groups),TRUE); 
                  ?>
                </div>

                <?php
              }
              ?>

            </div>
            <div class="row">
              <div class="col-12">
                
                
                  <input type="submit" name="submit" class="btn btn-primary mr-3" value="<?php echo lang('core_button_save'); ?>">

                    <a class="btn btn-dark" href="<?php echo base_url('admin/settings'); ?>"><?php echo lang('core_button_cancel'); ?>
                      
                    </a>
                

              </div>
            </div>


          </div>

        </div>

         <?php echo form_close(); ?>
      </div>
   </div>
</div>
