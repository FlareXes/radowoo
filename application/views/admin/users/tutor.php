<?php defined('BASEPATH') OR exit('No direct script access allowed');
   
   if($this->session->flashdata('success')) 
   {
      echo '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> '.$this->session->flashdata("success").'
      </div>';
   }
?>
<div class="panel panel-default">
   <div class="clearfix"></div>
   <hr>
   <table id="table" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
         <tr>
            <th><?php echo lang('no'); ?></th>
            <th><?php echo lang('name'); ?></th>
            <th><?php echo lang('username'); ?></th>
            <th><?php echo lang('email'); ?></th>
            <th><?php echo lang('action'); ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>
</div>


<div class="modal fade" id="user_qualification_experience_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel"><?php echo lang('user_qualification_and_experience') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            </div>
            <div class="modal-body user_qualification_and_experience">
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>