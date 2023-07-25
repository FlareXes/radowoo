<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
      echo '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> '.$this->session->flashdata("success").'
      </div>';
   }
?>
<div class="panel panel-default">
   <a href="<?php echo base_url("admin/db-backup");?>" class="btn btn-primary cat float-right" ><?php echo lang('backup_now'); ?></a>
   <div class="clearfix"></div>
   <hr>
   <table  class="display table table-striped table-bordered datatable" cellspacing="0" width="100%">
      <thead>
         <tr>
            <th><?php echo lang('admin_table_no'); ?></th>
            <th><?php echo lang('File Name'); ?></th>
            <th><?php echo lang('admin_table_action'); ?></th>
         </tr>
      </thead>
      <tbody>
         <?php 
         if($backups_array)
         {
            $backups_arraSy = rsort($backups_array);
            $i = 0;
            foreach ($backups_array as  $backup_file_name) 
            {
               $i++;
               ?>

               <tr>
                  <td><?php echo $i; ?></td>
                  <td><?php echo $backup_file_name; ?></td>
                  <td>
                     
                     <a href="<?php echo base_url('admin/backup/remove_backup/').$backup_file_name; ?>" data-toggle="tooltip"  title="<?php echo lang("admin_delete_record"); ?>" class="btn btn-danger btn-action mr-1 common_delete">
                        <i class="fas fa-trash"></i>
                     </a>

                     <a href="<?php echo base_url('admin/backup/download_backup/').$backup_file_name; ?>" data-toggle="tooltip"  title="<?php echo lang("download"); ?>" class="btn btn-info btn-action mr-1">
                        <i class="fas fa-download"></i>
                     </a>

                  </td>
               </tr>

               <?php
            }
         }
         ?>

      </tbody>
   </table>
</div>