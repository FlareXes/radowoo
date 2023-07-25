<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
      echo '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> '.$this->session->flashdata("success").'
      </div>';
   }
?>
<div class="panel panel-default">
   <input type="hidden" class="user-id" value="<?php echo $user_id;?>">
   <table id="user-quiz-history-table" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
         <tr>
            <th><?php echo lang('admin_title'); ?></th>
            <th><?php echo lang('questions'); ?></th>
            <th><?php echo lang('attended'); ?></th>
            <th><?php echo lang('correct'); ?></th>
            <th><?php echo lang('wrong'); ?></th>
            <th><?php echo lang('quiz_date'); ?></th>
            <th><?php echo lang('admin_table_action'); ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>
</div>