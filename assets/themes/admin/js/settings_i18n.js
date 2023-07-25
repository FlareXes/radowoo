(function ($) {
  "use strict";

var csrfName = $('#csrf_hash').val();
var csrf_token = $('#csrf_token').val();


  $(document).ready(function () {
    /**
     * Apply form-control class and id to timezones dropdown
     */
    $("select[name=timezones]")
      .addClass("form-control")
      .attr("id", "timezones");
  });

  $(".popup").on("click", function () {
    $("#imagepreview").attr("src", $(this).attr("src"));
    $("#imagemodal").modal("show");
  });



$(document).ready(function(){
  $('#sortable').sortable({

      update: function (event, ui) 
        {
                  // console.log($(this));
                   $(this).children().each(function(index){
                    //console.log(index);
                    if($(this).attr('data-grid_order') !=(index+1))
                        {
                            $(this).attr('data-grid_order',(index+1)).addClass('updated_section_position');
                        }
                   });
                    
                   update_section_position();
                    //make ajax call
        } 
  });  
});



function update_section_position()
{
 var position = [];
    $('.updated_section_position').each(function()
    {
        position.push([$(this).attr('data-menu_slug'),$(this).attr('data-menu_id'),$(this).attr('data-grid_order')]);
        $(this).removeClass('updated_section_position');
    });

        $.ajax({
          url: BASE_URL+"admin/settings/change_menu_position",
          type: 'POST',
          data: { position:position,},
          success: function(response)
          {

            response = JSON.parse(response);
            console.log(response);
            if(response.status =='success')
            {
            
                console.log('move');
                console.log(response.message);
                // $.notify(response.message,"success");
                $.notify({ message: response.message},{type: 'success'});
            }
            else
            {
               console.log('fail');
               console.log(response.message);
               $.notify({ message: response.message },{ type: 'danger'});
            }

          },
          error: function (jqXHR, status, err) {
            console.log(jqXHR);
          }
        });
}


    $(document).on('change','.togle_switch',function(e){

        
        

        if($(this).prop('checked')==true)
        {
            var status = 1;
        }
        else
        {
            var status = 0; 
        }


        var id = $(this).data('id');
        
        $.ajax({
            url: BASE_URL+"admin/settings/update_menu_status/"+id,
            type: "POST",
            data:{menu_id:id,status:status},
            success:function(response)
            {
              
              response = JSON.parse(response);
              if(response.status =='success')
              {
                  console.log('move');
                  console.log(response.message);
                  // $.notify(response.message,"success");
                  $.notify({ message: response.message},{type: 'success'});
              }
              else
              {
                 console.log('fail');
                 console.log(response.message);
                 $.notify({ message: response.message },{ type: 'danger'});
              }

            },
            error:function(e)
            {
              $.notify({ message: 'Something Went Wrong' },{ type: 'danger'});
            },        
        })


    });


})(jQuery);
