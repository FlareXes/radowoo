<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
      echo '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> '.$this->session->flashdata("success").'
      </div>';
   }
?>
<div class="card">
   <?php
      $data['tab_study_material_id'] = $study_material_id;
      $this->load->view('admin/study/common_tab_list',$data);
   ?>
</div>  
<input type="hidden" class="study_material_id" name="study_material_id" value="<?php echo xss_clean($study_material_id); ?>">    

<div class="panel panel-default">
   
      <a href="javascript:void(0)" hreff="<?php echo base_url('admin/study/add-material-file/'.$study_material_id);?>" class="btn btn-primary cat float-right" data-toggle="modal" data-target="#s_m_contant_type" ><?php echo lang('admin_add_material_file'); ?></a>


   <a href="javascript:void(0)" class="btn btn-primary cat float-right mr-2" data-toggle="modal" data-target="#section_modal" ><?php echo lang('add_section'); ?></a>

   <div class="clearfix"></div>
   <hr>
   
      <div class="card">
         <div class="card-body">
            <div class="row mt-3">
               <div class="col-12">

                  <?php 
                     if($study_section_data)
                     {
                        ?>
                        <div class="section_main_box row">
                           <?php 
                           foreach ($study_section_data as $key => $section_array) 
                           { 
                              $study_material_section_id = $section_array->id;
                              ?>
                             
                              <div class=" col-12 section_box border rounded section_box mb-5" data-study_material_section_id="<?php echo $study_material_section_id; ?>">
                                 
                                    <div class="row bg-secondary rounded">
                                      
                                       <div class="col-10">
                                          <h4 class=" p-3 mb-0 text-white"><?php echo $section_array->title; ?></h4>
                                       </div>
                                       <div class="col-2 m-auto">
                                          
                                          <a href="javascript:void(0)" class="m-auto btn btn-info update_studay_matrial_section" data-study_material_section_id="<?php echo $study_material_section_id; ?>" data-title="<?php echo $section_array->title; ?>" data-action="<?php echo base_url('admin/study/section-update/'.$study_material_section_id);?>">
                                             <i class="fas fa-pencil-alt"></i>
                                          </a>

                                          <a href="<?php echo base_url('admin/study/section-delete/'.$study_material_section_id); ?>" class="m-auto btn btn-danger study_material_section_delete" data-action="<?php echo base_url('admin/study/section-delete/'.$study_material_section_id); ?>">
                                             <i class="fas fa-trash-alt"></i>
                                          </a>

                                       </div>

                                    </div>


                                    <div class="row contant_main_section">
                                       <?php 
                                          $section_contant_obj = get_study_section_contant($study_material_id,$study_material_section_id);
                                          if($section_contant_obj)
                                          {
                                             foreach ($section_contant_obj as $section_contant_data) 
                                             { 
                                                $s_m_section_contant_id = $section_contant_data->id; 
                                                ?>

                                                <div class="col-12 contant_section border-bottom-1 py-2" data-s_m_section_contant_id="<?php echo $s_m_section_contant_id; ?>">
                                                   <div class="row">
                                                      <div class="col-7 my-auto">
                                                         <h6 class="small"><?php echo $section_contant_data->title; ?></h6>
                                                      </div>
                                                      <div class="col-3  my-auto">
                                                         <h6 class="small">  <?php echo $section_contant_data->type; ?></h6>
                                                      </div>
                                                      <div class="col-2  my-auto"> 
                                                         <a href="<?php echo base_url('admin/study/update-material-content/'.$s_m_section_contant_id) ?>" class="m-auto btn btn-info" data-s_m_section_contant_id="<?php echo $s_m_section_contant_id; ?>"><i class="fas fa-pencil-alt"></i></a>

                                                         <a href="<?php echo base_url('admin/study/delete-material-content/'.$s_m_section_contant_id); ?>" class="m-auto btn btn-danger study_material_section_contant_delete"><i class="fas fa-trash-alt"></i> </a>
                                                      </div>
                                                   </div>
                                                   
                                                </div>

                                                <?php
                                             }
                                          }
                                          else
                                          {
                                             ?>
                                             <div class="col-12 text-center text-danger py-2">
                                                <label>No Contant Added Yet</label>
                                             </div>
                                             <?php
                                          }
                                       ?>                                       
                                    </div>

                              </div>

                              <?php 
                           } ?>
                        </div>
                        <?php
                     }
                     else
                     {
                        echo "<h4 class='text-center text-danger w-100'>".lang('please_add_section')."</h4>" ;
                     }
                     ?>

               </div>
            </div>
         </div>
      </div>
</div>

      <div class="modal fade" id="section_modal" tabindex="-1" role="dialog" aria-labelledby="section_modal_view" aria-hidden="true">
         <div class="modal-dialog ">
            <div class="modal-content">
               <?php echo form_open_multipart(base_url('admin/study/section/'.$study_material_id), array('role'=>'form','class'=> 'w-100')); ?>
               <div class="row">
                  <div class="col-12">
                     
                     <div class="modal-header">
                        <h4 class="modal-title" id="section_modal_view"><?php echo lang('add_secton'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                     </div>

                     <div class="modal-body">
                       
                       <div class="form-group">
                          <label>Section Title<span class="text-danger"> *</span></label>
                          <input type="text" required="true" name="title" class="form-control" value="">
                       </div>

                     </div>
                     
                     <div class="modal-footer">
                        <button type="submit" class="btn btn-info">Submit</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>

               <?php echo form_close();?>

            </div>
         </div>
      </div>

      <div class="modal fade" id="section_modal_update" tabindex="-1" role="dialog" aria-labelledby="section_modal_update_title" aria-hidden="true">
         <div class="modal-dialog ">
            <div class="modal-content"> 
               <?php echo form_open_multipart(base_url('admin/study/section-update/1'), array('role'=>'form','class'=> 'form w-100')); ?>
               <div class="row">
                  <div class="col-12">
                     
                     <div class="modal-header">
                        <h4 class="modal-title" id="section_modal_update_title"><?php echo lang('update_secton'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                     </div>

                     <div class="modal-body">
                       
                       <div class="form-group">
                          <label>Section Title<span class="text-danger"> *</span></label>
                          <input type="text" required="true" name="title" class="form-control form_title" value="">
                       </div>

                     </div>
                     
                     <div class="modal-footer">
                        <button type="submit" class="btn btn-info">Submit</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>

               <?php echo form_close();?>

            </div>
         </div>
      </div>


      <div class="modal fade" id="s_m_contant_type" tabindex="-1" role="dialog" aria-labelledby="s_m_contant_type_add" aria-hidden="true">
         <div class="modal-dialog ">
            <div class="modal-content"> 
               <div class="row">
                  <div class="col-12">
                     <div class="modal-header">
                        <h4 class="modal-title" id="s_m_contant_type_add"><?php echo lang('contant_type'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                     </div>

                     <div class="modal-body">
                       
                       <div class="form-group">
                          <label>Contant Type<span class="text-danger"> *</span></label>
                          <input type="hidden" value="<?php echo $study_material_id; ?>" id="input_study_material_id" class="input_study_material_id">
                          <select name="contant_type" id="contant_type" class="form-control contant_type">
                              <?php foreach ($this->contant_types as $key => $value) 
                              {
                                 echo "<option value='".$key."'>$value</option>";
                              } ?>
                             
                          </select>
                       </div>

                     </div>
                     
                     <div class="modal-footer">
                        <button type="button" class="btn btn-info submit_contant_type">Submit</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

