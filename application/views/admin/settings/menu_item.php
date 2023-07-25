<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="panel panel-default">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-body">
               <hr>
               <div class="row">
                  <div class="col-md-12">
                     <?php 
                     if($menu_items)
                     {?>

                     <ul id="sortable" class="list-group">
                        <?php
                        foreach ($menu_items as $items) 
                        { ?>
                          <li class="list-group-item " data-menu_slug="<?php echo $items->slug; ?>" data-menu_id="<?php echo $items->id; ?>"><?php echo $items->order; ?> : <?php echo $items->title; ?></li>
                           <?php 
                        } ?>
                     </ul>
                        <?php
                     }?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>