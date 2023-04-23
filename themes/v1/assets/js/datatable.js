$.DataTableInit = function(res){
    var paging = true;
    var searching = true;
    var processing = true;
    var serverSide = true;
    var info = true;
    if ( typeof res.paging !== 'undefined') { paging = res.paging; }
    if ( typeof res.searching !== 'undefined') { searching = res.searching; }
    if ( typeof res.processing !== 'undefined') { processing = res.processing; }
    if ( typeof res.serverSide !== 'undefined') { serverSide = res.serverSide; }
    if ( typeof res.info !== 'undefined') { info = res.info; }
    var t = $(res.selector);
    var a = t.prev(".dt_colVis_buttons");
    var reqsno = 0;
    var columns = $('thead th:not(.dt-no-export)', t);
    t.DataTable({
    processing: processing,
    serverSide: serverSide,
    ajax: {
        url: res.url,
        type: "POST",
        data: res.data,
        beforeSend: function() {
            reqsno++;
            if(reqsno > 1){
                t.dataTableSettings[0].jqXHR.abort();
            }
        }
    },
    aaSorting: res.aaSorting,
    columnDefs: res.columnDefs,
    fixedColumns:   res.fixedColumns,
    scrollX: res.scrollX,
    paging: paging,
    info: info,
    searching: searching,
    aLengthMenu: [[ 10, 20, 50, 100 ,-1],[10,20,50,100,"All"]],
    dom: 'Blfrtip',
    buttons: [
        { extend: "excelHtml5", text: '<i class="uk-icon-file-excel-o"></i> XLSX', titleAttr: "Excel", exportOptions: { columns: columns } },
        { extend: "csvHtml5", text: '<i class="uk-icon-file-text-o"></i> CSV', titleAttr: "CSV", exportOptions: { columns: columns } },
    ]
    })
    .buttons()
    .container()
    .appendTo(a);
};

$.DataTableInit2 = function(res){
    var paging = true;
    var searching = true;
    var info = true;
    if ( typeof res.paging !== 'undefined') { paging = res.paging; }
    if ( typeof res.searching !== 'undefined') { searching = res.searching; }
    if ( typeof res.processing !== 'undefined') { processing = res.processing; }
    if ( typeof res.serverSide !== 'undefined') { serverSide = res.serverSide; }
    if ( typeof res.info !== 'undefined') { info = res.info; }
    var t = $(res.selector);
    var a = t.prev(".dt_colVis_buttons");
    var reqsno = 0;
    t.DataTable({
    aaSorting: res.aaSorting,
    columnDefs: res.columnDefs,
    fixedColumns:   res.fixedColumns,
    scrollX: res.scrollX,
    paging: paging,
    info: info,
    searching: searching,
    aLengthMenu: [[ 10, 20, 50, 100 ,-1],[10,20,50,100,"All"]],
    dom: 'Blfrtip',
    buttons: [
        { extend: "excelHtml5", text: '<i class="uk-icon-file-excel-o"></i> XLSX', titleAttr: "" },
        { extend: "csvHtml5", text: '<i class="uk-icon-file-text-o"></i> CSV', titleAttr: "CSV" },
    ]
    })
    .buttons()
    .container()
    .appendTo(a);
}

