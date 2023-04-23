<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
    .md-btn:active, .md-btn:focus, .md-btn:hover, .uk-button-dropdown.uk-open>.md-btn {
        background: #69b54a;
        color: white;

    }
    .md-btn>i.material-icons{
        margin-top:0px;
    }
    .uk-dropdown, .uk-dropdown-blank{
        width: auto;
    }
    #dt_tableExport .dtfc-fixed-right{
        /* position: absolute !important; */
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
    <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-toggle" style="opacity: 1; transform: scale(1);"></i>
                </div>
                <h3 class="md-card-toolbar-heading-text">Filters </h3>
            </div>
            <div class="md-card-content" >
                <form action="<?php echo base_url('admin/sales'); ?>" method="get">
                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse</label>
                                <select name="warehouse" class="uk-width-1-1 select2" >
                                    <option value="all">All Warehosues</option>
                                    <?php
                                        foreach($warehouses as $w){
                                            echo '<option value="'.$w->id.'" ';
                                            if($w->id == $warehouse){
                                                echo 'selected';
                                            }
                                            echo ' >'.$w->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Customers</label>
                                <select name="customer" class="uk-width-1-1 select2">
                                    <option value="all">All Customer</option>
                                    <?php
                                        foreach($customers as $c){
                                            echo '<option value="'.$c->id.'" ';
                                            if($c->id == $customer){
                                                echo 'selected';
                                            }
                                            echo ' >'.$c->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Own Company</label>
                                <select name="own_company" class="uk-width-1-1 select2">
                                    <option value="all">All Own Company</option>
                                    <?php
                                        foreach($owncompanies as $ow){
                                            echo '<option value="'.$ow->id.'" ';
                                            if($ow->id == $own_company){
                                                echo 'selected';
                                            }
                                            echo ' >'.$ow->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Start Date</label>
                                <input class="md-input  label-fixed" type="text" name="start_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $start_date ?>" readonly >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Start Date</label>
                                <input class="md-input  label-fixed" type="text" name="end_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="<?php echo $end_date; ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >Submit</button>
                            <a href="<?php echo base_url('admin/salesorders'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Sales Orders</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th style="width:120px">Date</th>
                            <th>Invoice No</th>
                            <th>SO No</th>
                            <th style="width:120px">PO Number</th>
                            <th style="width:150px">Customer Name</th>
                            <th style="width:150px">Customer Phone</th>
                            <th style="width:150px">Own Compnay</th>
                            <th style="width:150px">Warehouse</th>
                            <th>Grand Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Sale Type</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <a class="md-fab md-fab-success md-fab-wave waves-effect waves-button addbtn" href="<?php echo base_url('admin/salesorders/add') ?>"><i class="fa-solid fa-plus"></i></a>
</div>
<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;
    data['warehouse'] = '<?php echo $warehouse; ?>';
    data['supplier'] = '<?php echo $supplier; ?>';
    data['customer'] = '<?php echo $customer; ?>';
    data['own_company'] = '<?php echo $own_company; ?>';
    data['start_date'] = '<?php echo $start_date; ?>';
    data['end_date'] = '<?php echo $end_date; ?>';
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('sales/get_lists'); ?>",
        data:data,
        aaSorting: [[1, "desc"]],
        columnDefs: [
            { 
                "targets": 15,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 2},
        scrollX: true,
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();

        $( "body" ).on( "click", ".deletebtn", function() {
            var iid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            console.log(iid);
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this sale!",
                icon: "warning",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    return fetch(`<?= base_url('admin/sales/delete/') ?>${iid}?reason=${reason}&[${csrfName}]=${csrfHash}`)
                    .then(response => {
                        console.log(response);
                        if (!response.ok) {
                            console.log('Error');
                            throw new Error(response.statusText)
                        }
                        else if(reason == ""){
                            throw new Error('Enter Reason')
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                console.log('CR: '+result);
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sale Delete Successfully',
                        showConfirmButton: false,
                        timer: 10000
                    });
                    setTimeout(function(){ 
                        location.reload();
                    }, 1000);
                }
            });
        });
        $( "body" ).on( "click", ".printBtn", function() {
            var id = $(this).data('id');
            $.ajax({
                url: '<?php echo base_url('admin/sales/print_slip'); ?>',
                type: 'GET',
                data: {id:id},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    toastr.success(obj.message);
                    if(obj.status){
                        if(obj.print){
                            // window.open(obj.url, '_blank'); 
                            var mapForm = document.createElement("form");
                            mapForm.target = "Map";
                            mapForm.method = "POST"; // or "post" if appropriate
                            mapForm.action = obj.url2;

                            var mapInput = document.createElement("input");
                            mapInput.type = "text";
                            mapInput.name = "print_data";
                            mapInput.value = obj.form_data;
                            mapForm.appendChild(mapInput);

                            document.body.appendChild(mapForm);

                            map = window.open("", "Map", "status=0,title=0,height=600,width=800,scrollbars=1");
                            if (map) {
                                mapForm.submit();
                            } else {
                                alert('You must allow popups for this map to work.');
                            }



                        }
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });




        });
    });
</script>