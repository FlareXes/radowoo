(function ($) {
  "use strict";
 
	$(document).ready(function () {
		$('.select_dropdown').select2();

		var table;
		//datatables
	    table = $("#table").DataTable({
	      language: {
	        info: table_showing +
	          " _START_ " +
	          table_to +
	          " _END_ " +
	          table_of +
	          " _TOTAL_ " +
	          table_entries,
	        paginate: {
	          previous: table_previous,
	          next: table_next,
	        },
	        sLengthMenu: table_show + " _MENU_ " + table_entries,
	        sSearch: table_search,
	      },

	      processing: true, //Feature control the processing indicator.
	      serverSide: true, //Feature control DataTables' server-side processing mode.
	      order: [],
	      ajax: {
	        url: BASE_URL + "tutor/study/study-material-list",
	        type: "POST",
	      },

	      //Set column definition initialisation properties.
	      columnDefs: [{
	        targets: [0], //first column / numbering column
	        orderable: false, //set not orderable
	      }, ],
	    });

	  //package and related package quiz delete with sweetalert
	  $("body").on("click", ".common_delete", function (e) {
	    var link = $(this).attr("href");

	    e.preventDefault(false);
	    swal({
	        title: are_you_sure,
	        text: permanently_deleted,
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#3085d6",
	        cancelButtonColor: "#d33",
	        confirmButtonText: yes_delere_it,
	      },
	      function (isConfirm) {
	        if (isConfirm == true) {
	          window.location.href = link;
	        }
	      }
	    );
	  });

	  var table;
	  var study_material_id = $('.study_material_id').val();
		//datatables
	    table = $("#materialfiletable").DataTable({
	      language: {
	        info: table_showing +
	          " _START_ " +
	          table_to +
	          " _END_ " +
	          table_of +
	          " _TOTAL_ " +
	          table_entries,
	        paginate: {
	          previous: table_previous,
	          next: table_next,
	        },
	        sLengthMenu: table_show + " _MENU_ " + table_entries,
	        sSearch: table_search,
	      },

	      processing: true, //Feature control the processing indicator.
	      serverSide: true, //Feature control DataTables' server-side processing mode.
	      order: [],
	      ajax: {
	        url: BASE_URL + "tutor/study/material-file-list/"+study_material_id,
	        type: "POST",
	      },

	      //Set column definition initialisation properties.
	      columnDefs: [{
	        targets: [0], //first column / numbering column
	        orderable: false, //set not orderable
	      }, ],
	    });

	    $(document).on("click", ".is_premium", function (e) 
	    {
	       if($(this).prop("checked")==true)
	       {
	       		$('.input_price').attr('readonly', true);
	       }
	       else
	       {
	        	$('.input_price').attr('readonly', false);
	       }
	    });


	    $(document).on("click", ".update_studay_matrial_section", function (e) 
	    {
	       var study_material_section_id = $(this).data('study_material_section_id');
	       var title = $(this).data('title');
	       var action = $(this).data('action');

	       $("#section_modal_update .form").attr('action',action);
	       $("#section_modal_update .form_title").val(title);
	       $("#section_modal_update").modal("show");
	    });


	    $(document).on("click", ".submit_contant_type", function (e) 
	    {
	       var contant_type = $("#contant_type").val();
	       var study_material_id = $("#input_study_material_id").val();
	       if(contant_type)
	       {
	       		window.location.href = BASE_URL+"tutor/study/add-material-file/"+study_material_id+"/"+contant_type;
	       }
	       else
	       {
	       		$.notify({ message: 'something went wrong' },{ type: 'danger'});
	       }

	    });





	$(document).on("click", ".study_material_section_delete", function (e) 
	{
	    var link = $(this).attr("href");
	    e.preventDefault(false);
	    swal({
	        title: are_you_sure,
	        text: "Section Contant Will Also Delete !",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#3085d6",
	        cancelButtonColor: "#d33",
	        confirmButtonText: yes_delere_it,
	      },
	      function (isConfirm) {
	        if (isConfirm == true) {
	          window.location.href = link;
	        }
	      }
	    );
	});

	$(document).on("click", ".study_material_section_contant_delete", function (e) 
	{
	    var link = $(this).attr("href");
	    e.preventDefault(false);
	    swal({
	        title: are_you_sure,
	        text: "Contant Will Delete !",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#3085d6",
	        cancelButtonColor: "#d33",
	        confirmButtonText: yes_delere_it,
	      },
	      function (isConfirm) {
	        if (isConfirm == true) {
	          window.location.href = link;
	        }
	      }
	    );
	});


	$('.section_main_box').sortable(
	{
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


	$('.contant_main_section').sortable(
	{
	    update: function (event, ui) 
	    {
              // console.log($(this));
               $(this).children().each(function(index){
                //console.log(index);
                if($(this).attr('data-grid_order') !=(index+1))
                    {
                        $(this).attr('data-grid_order',(index+1)).addClass('updated_section_contant_position');
                    }
               });
                
               update_section_contant_position();
                //make ajax call
	    } 
	}); 


	function update_section_position()
	{
	 	var position = [];
	    $('.updated_section_position').each(function()
	    {
	        position.push([$(this).attr('data-study_material_section_id'),$(this).attr('data-grid_order')]);
	        $(this).removeClass('updated_section_position');
	    });

        $.ajax({
          url: BASE_URL+"tutor/Study_Material/change_section_position",
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


	function update_section_contant_position()
	{
	 	var position = [];
	    $('.updated_section_contant_position').each(function()
	    {
	        position.push([$(this).attr('data-s_m_section_contant_id'),$(this).attr('data-grid_order')]);
	        $(this).removeClass('updated_section_contant_position');
	    });

        $.ajax({
          url: BASE_URL+"tutor/Study_Material/change_section_contant_position",
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



	});


	$(document).ready(function() {
		$('.js-example-basic-multiple').select2();
		$(".js-example-basic-multiple option[value='X']").remove();
	});


})(jQuery);