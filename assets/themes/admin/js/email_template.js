(function ($) {
  "use strict";

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
        url: BASE_URL + "admin/template/email_list",
        type: "POST",
      },

      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });	

})(jQuery);