(function($) {
    'use strict';
    $(function() {
      $('#order-listing').DataTable({
        responsive: true,
      "aLengthMenu": [
        [5, 10, 20, 30, 100, -1],
        [5, 10, 20, 30, 100, "Todo"]
      ],
        "iDisplayLength": 10,
        "language": {
          "info": "Mostrando del _START_ al _END_ de un total de _TOTAL_ registro(s).",
          "search": "",
          "paginate": {
              "next": "Siguiente",
              "previous": "Anterior",
          },
          "lengthMenu": 'Mostrando <select class="form-control">' +
                '<option value="5">5</option>' +
                '<option value="10">10</option>' +
                '<option value="5">20</option>' +
                '<option value="30">30</option>' +
                '<option value="100">100</option>' +
                '<option value="-1">Todo</option>' +
                '</select> registros',
          "loadinRecords": " Cargando...",
          "processing": " Procesando...",
          "emptyTable": "No hay datos Disponibles",
          "zeroRecords": "No hay coincidencias",
          "infoEmpty": "",
          "infoFiltered": "",
        }
      });

      $('#order-listing1').DataTable({

        "aLengthMenu": [
            [5, 10, 20, 30, 100, -1],
            [5, 10, 20, 30, 100, "Todo"]
        ],
        "iDisplayLength": 10,
        "language": {
          "info": "Mostrando del _START_ al _END_ de un total de _TOTAL_ registro(s).",
          "search": "",
          "paginate": {
              "next": "Siguiente",
              "previous": "Anterior",
          },
          "lengthMenu": 'Mostrando <select class="form-control">' +
                '<option value="5">5</option>' +
                '<option value="10">10</option>' +
                '<option value="5">20</option>' +
                '<option value="30">30</option>' +
                '<option value="100">100</option>' +
                '<option value="-1">Todo</option>' +
                '</select> registros',
          "loadinRecords": " Cargando...",
          "processing": " Procesando...",
          "emptyTable": "No hay datos Disponibles",
          "zeroRecords": "No hay coincidencias",
          "infoEmpty": "",
          "infoFiltered": "",
        }
      });


      $('#order-listing').each(function() {
        var datatable = $(this);
        // SEARCH - Add the placeholder for Search and Turn this into in-line form control
        var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        search_input.attr('placeholder', 'Buscar');
        search_input.removeClass('form-control-sm');
        // LENGTH - Inline-Form control
        var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        length_sel.removeClass('form-control-sm');
      });
      
      $('#order-listing1').each(function() {
        var datatable = $(this);
        // SEARCH - Add the placeholder for Search and Turn this into in-line form control
        var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        search_input.attr('placeholder', 'Buscar');
        search_input.removeClass('form-control-sm');
        // LENGTH - Inline-Form control
        var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        length_sel.removeClass('form-control-sm');
      });
    });
  })(jQuery);
