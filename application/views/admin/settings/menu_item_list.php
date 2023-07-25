<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
    echo '<div class="alert alert-success alert-dismissible">
            <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> '.$this->session->flashdata("success").'
        </div>';
   }
   ?>
<div class="panel panel-default">
   <a href="<?php echo base_url("admin/settings/menu_item");?>" class="btn btn-primary cat float-right" ><?php echo lang('Menu Order'); ?></a>
   <div class="clearfix"></div>
   <hr>
   <table id="table" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
         <tr>
            <th><?php echo lang('admin table no'); ?></th>
            <th><?php echo lang('Menu Title'); ?></th>
            <th><?php echo lang('Slug'); ?></th>
            <th><?php echo lang('Status'); ?></th>
         </tr>
      </thead>
         <?php
         if($menu_items)
         { 
            echo "<tbody>";
            $no = 0;
            foreach ($menu_items as  $menu_item) 
            {
               $no++;
               $checkvalue = ($menu_item->status == 1 ? 'checked="checked"' : "");
               ?>
               <tr>
                  <td><?php echo $no; ?></td>
                  <td><?php echo ucfirst($menu_item->title); ?></td>
                  <td><?php echo $menu_item->slug; ?></td>
                  <td>
                     <label class="custom-switch mt-2">
                        <input type="checkbox" data-id="<?php echo $menu_item->id; ?>" name="custom-switch-checkbox" class="custom-switch-input togle_switch" <?php echo $checkvalue; ?> >
                        <span class="custom-switch-indicator indication"></span>
                     </label>
                  </td>


               </tr>
               <?php 
            }
            echo "</tbody>";
                
         }?>

   </table>
</div>