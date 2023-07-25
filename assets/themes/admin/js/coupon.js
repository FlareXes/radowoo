(function ($) {
    "use strict";
  
    var table;

    table = $("#coupontable").DataTable({
      language: {
        info: table_showing +
          " _START_ " +
          table_to +
          " _END_ " +
          table_of +
          " _TOTAL_ " +
          table_entries,
        sLengthMenu: table_show + " _MENU_ " + table_entries,
        sSearch: table_search,
        paginate: {
          previous: table_previous,
          next: table_next,
        },
      },
    
      processing: true, //Feature control the processing indicator.
      serverSide: true, //Feature control DataTables' server-side processing mode.
      order: [], //Initial no order.
      ajax: {
        url: BASE_URL + "admin/coupon/coupon_list",
        type: "POST",
      },
    
      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });

//$('.item-for').hide();
$(document).ready(function(){ 



  $('.coupon-for').on('change',function(e){
    e.preventDefault();
    
    var coupon_for = $(this).find(":selected").val();

    if(coupon_for != 'all')
    {
      $('.item-for').show();
      $(".select2-ajax").select2({
        ajax: {
          url: BASE_URL + "admin/coupon/get_coupon_for",
          type: 'post',
          dataType: 'json',
          delay: 250,
          
          data: function (params) 
          {  
            var query = {
              search_text: params.term,
              coupon_for:coupon_for,
            }
            return query;
          },
          processResults: function (data)
          {
            if (data.status == "success") 
            {
              return {
                results: data.data
              };
            }
          },
          cache: true
        },

        escapeMarkup: function(markup) {
          return markup;
        }, 
        placeholder: 'Search for a related data',
        minimumInputLength: 2,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
        allowClear: true
      });

      function formatRepo (repo) 
      {
        
        if (repo.loading) {
          return repo.text;
        }

        var $container = $(
          "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
              "<div class='select2-result-data_name'></div>" +
              "</div>" +
            "</div>" +
          "</div>"
        );

        $container.find(".select2-result-data_name").text(repo.title);

        return $container;
      }

      function formatRepoSelection (repo) {
        return repo.title || repo.id;
      }
    }
    else
    {
      $('.item-for').hide();
    }
    
    if($('#coupon_for').hasClass('coupon-for'))
    {
      $(".select2-ajax").empty().trigger('change');
    }
  });
});
  
  var table;
  var coupon_id = $('.coupon-id').val();
    table = $("#coupontracktable").DataTable({
      language: {
        info: table_showing +
          " _START_ " +
          table_to +
          " _END_ " +
          table_of +
          " _TOTAL_ " +
          table_entries,
        sLengthMenu: table_show + " _MENU_ " + table_entries,
        sSearch: table_search,
        paginate: {
          previous: table_previous,
          next: table_next,
        },
      },
    
      processing: true, //Feature control the processing indicator.
      serverSide: true, //Feature control DataTables' server-side processing mode.
      order: [], //Initial no order.
      ajax: {
        url: BASE_URL + "admin/coupon/coupon_track_list/"+coupon_id,
        type: "POST",
      },
    
      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });

})(jQuery);  