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
        url: BASE_URL + "admin/Package/package_list",
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

	  $('.select-category').on('change',function(e){
	  	e.preventDefault(false);
	  	var category_id = $(this).val();
	  	var package_id = $('.package-id').val();
	  	if(category_id != "")
	  	{
	  		$.ajax({
	          type: "POST",
	          url: BASE_URL + "admin/package/get_quiz",
	          data: {category_id: category_id,package_id:package_id},

	          success: function (data) {
	          	data = JSON.parse(data);
	          	
	            if (data.success == "successfull") 
	            {
	            	$('.select-quiz').removeClass('d-none');
	            	$('.quiz-data').html(data.quiz_data);  
	            } 
	            else if(data.error == "unsuccessfull")
	            {
	              $('.select-quiz').addClass('d-none');
	            }
	            else
	            {
	            	alert("error");
	            }
	          },
	          error: function (e) {
	            console.log(e);
	          },
	        });	
	  	}
	  	else
	  	{
	  		$('.select-quiz').addClass('d-none');
	  	}

	  });
  	  
	  $('.add-now').on('click',function(e){
	  	e.preventDefault();
	  	var quiz_id = $('select[name="quiz_id"] :selected').val();
	  	var package_id = $('.package-id').val();
	  	if(quiz_id && package_id)
	  	{
	  		$.ajax({
	          type: "POST",
	          url: BASE_URL + "admin/package/add_package_quiz_order",
	          data: {quiz_id: quiz_id,package_id:package_id},

	          success: function (data) {
	          	data = JSON.parse(data);
	          	console.log(data);
	            if (data.success) 
	            {
	            	window.location.href = BASE_URL+"admin/package/package_quiz/"+package_id;
	            } 
	            else 
	            {
	              alert("error");
	            }
	          },
	          error: function (e) {
	            console.log(e);
	          },
	        });	
	  	}
	  	else
	  	{
	  		alert('please select quiz');
	  	}
	  });

	  //package and related package quiz delete with sweetalert
	  $("body").on("click", ".package-delete-quiz", function (e) {
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

	  
	  $('.package-quiz-position').each(function(){
	  	var quiz_order = $(this).data('quiz_order');
	  	console.log(quiz_order);
	  });
	var sortEventHandler = function(event, ui){
	    console.log(ui.position);
	};

  	$('.sortable-div').sortable({
  		stop: sortEventHandler, 
	  	update: function(event, ui) {
	        var $lis = $(this).children('div.row-drag');
	        $lis.each(function() {
	            var $li = $(this);
	            var newVal = $(this).index() + 1;
	            $(this).find('.sortable-number').html(newVal);
	            $(this).find('.sortable_number').val(newVal);
	        });
	    }  
  	});

  	$("form#packagequiz").on('submit',function(e){
		e.preventDefault();
		var form_data = new FormData(this);
		$.ajax({
			url: BASE_URL + "admin/package/package_quiz_order_save",
			type: "POST",
			data: form_data,
			success: function (data) 
			{
				data = JSON.parse(data);
	          	console.log(data);
	          	if(data.success == "successfull")
	          	{
					new Noty({
					      type: "success",
					      layout: "topRight",
					      text: data.msg,
					      timeout: 5000,
					      progressBar: true,
					      theme: "metroui ",
					      closeWith: ["click", "button"],
					    }).show();
	          	}
			},
			error: function(e)
			{
				console.log(e);
			},
			cache: false,
	        contentType: false,
	        processData: false
		});
	});

	$('.select-category-package').on('change',function(e){
	  	e.preventDefault(false);
	  	var category_id = $(this).val();
	  	var package_id = $('.package-id').val();
	  	if(category_id != "")
	  	{
	  		$.ajax({
	          type: "POST",
	          url: BASE_URL + "admin/package/get_study_material",
	          data: {category_id: category_id,package_id:package_id},

	          success: function (data) {
	          	data = JSON.parse(data);
	          	
	            if (data.success == "successfull") 
	            {
	            	$('.select-study-material').removeClass('d-none');
	            	$('.study-material-data').html(data.study_material_data);  
	            } 
	            else if(data.error == "unsuccessfull") 
	            {
	     			$('.select-study-material').addClass('d-none');         
	            }
	            else
	            {
	            	alert("error");
	            }
	          },
	          error: function (e) {
	            console.log(e);
	          },
	        });	
	  	}
	  	else
	  	{
	  		$('.select-study-material').addClass('d-none');
	  	}

	});	

	$('.add-now-study').on('click',function(e){
	  	e.preventDefault();
	  	var study_material_id = $('select[name="study_material_id"] :selected').val();
	  	var package_id = $('.package-id').val();
	  	if(study_material_id && package_id)
	  	{
	  		$.ajax({
	          type: "POST",
	          url: BASE_URL + "admin/package/add_package_study_material_order",
	          data: {study_material_id: study_material_id,package_id:package_id},

	          success: function (data) {
	          	data = JSON.parse(data);
	          	
	            if (data.success) 
	            {
	            	window.location.href = BASE_URL+"admin/package/package_study_material/"+package_id;
	            } 
	            else 
	            {
	              alert("error");
	            }
	          },
	          error: function (e) {
	            console.log(e);
	          },
	        });	
	  	}
	  	else
	  	{
	  		alert('please select study material');
	  	}
	});

	$("body").on("click", ".package-delete-study", function (e) {
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

	$('.study-sortable-div').sortable({
  		stop: sortEventHandler, 
	  	update: function(event, ui) {
	        var $lis = $(this).children('div.row-drag');
	        $lis.each(function() {
	            var $li = $(this);
	            var newVal = $(this).index() + 1;
	            $(this).find('.sortable-number').html(newVal);
	            $(this).find('.sortable_number').val(newVal);
	        });
	    }  
  	});

  	$("form#packagestudy").on('submit',function(e){
		e.preventDefault();
		var form_data = new FormData(this);
		$.ajax({
			url: BASE_URL + "admin/package/package_study_order_save",
			type: "POST",
			data: form_data,
			success: function (data) 
			{
				data = JSON.parse(data);
	          	if(data.success == "successfull")
	          	{
					new Noty({
					      type: "success",
					      layout: "topRight",
					      text: data.msg,
					      timeout: 5000,
					      progressBar: true,
					      theme: "metroui ",
					      closeWith: ["click", "button"],
					    }).show();
	          	}
			},
			error: function(e)
			{
				console.log(e);
			},
			cache: false,
	        contentType: false,
	        processData: false
		});
	});

	
  });

})(jQuery);