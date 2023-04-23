<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Customer/Sales Ledger</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/customer_legder') ?>" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" >Supplier *</label>
                                    <select name="supplier" class="form-control searching_select" id="customerID" data-placeholder="Select Supplier" required="required">
                                        <option value="0" selected="selected">All Supplier</option>
                                        <?php 
                                            foreach($suppliers as $row){
                                        ?>
                                            <option value="<?php echo $row->id?>" <?php if($row->id==$supplier_id){ echo 'selected'; } ?> ><?php echo $row->name ?></option>
                                        <?php 
                                            }
                                        ?>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" >Customer *</label>
                                    <select name="customer" class="form-control searching_select" id="customerID" data-placeholder="Select Customer" required="required">
                                        <option value="" selected="selected">Select Customer</option>
                                        <?php 
                                            foreach($customers as $row){
                                        ?>
                                            <option value="<?php echo $row->id?>" <?php if($row->id==$customer_id){ echo 'selected'; } ?> ><?php echo $row->name?></option>
                                        <?php 
                                            }
                                        ?>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="own_companies">Companies *</label>
                                    <select name="company" id="companyID" class="form-control input-tip searching_select " data-placeholder="Select Companies" style="width:100%;" >
                                        <option value="" selected="selected">Select Company</option>
                                        <?php 
                                            foreach($companies as $row){
                                        ?>
                                            <option value="<?php echo $row->id?>" <?php if($row->id==$company_id){ echo 'selected'; } ?> ><?php echo $row->name?></option>
                                        <?php 
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" name="start_date" value="<?php echo $start?>" class="form-control date2" id="start_date" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="text" name="end_date" value="<?php echo $end?>" class="form-control date2" id="end_date" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang("Sorting Type", "sortingtype"); ?>
                                    <select name="sortingtype" class="form-control input-tip searching_select" >
                                        <option value="date">Date Wise</option>
                                        <option value="invoice">Invoice Wise</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="controls">
                                    <input type="submit" name="submit_report" value="Submit" class="btn btn-primary" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Transation Ref</th>
                            <th>Particulars</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>Due</th>
                            <th>Ageing</th>
                            <th>Note</th>
                            <th>Remarts</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                        ?>
                                <tr>
                                    <td>
                                        <?php
                                        if($row->particular != "Opening"){
                                            echo date_format(date_create($row->date),"Y-m-d");
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $row->particular; ?></td>
                                    <td><?php echo $row->tref; ?></td>
                                    <td><?php echo $row->paid_by; ?></td>
                                    <td><?php echo amountformate($row->debit); ?></td>
                                    <td><?php echo amountformate($row->credit); ?></td>
                                    <td><?php echo amountformate($row->balance); ?></td>
                                    <td><?php echo amountformate($row->due); ?></td>
                                    <td><?php echo $row->aging ?></td>
                                    <td><?php echo $row->note; ?></td>
                                    <td data-payid="<?php echo $row->pay_id; ?>" data-sale_id="<?php echo $row->sale_id; ?>" class="remarkschange"><?php echo $row->remarks; ?></td>
                                    <td>
                                        <?php
                                            if(isset($row->pay_id)){
                                                if($row->pay_id > 0){
                                                ?>
                                                    <a href="<?= admin_url('sales/edit_payment/'.$row->pay_id) ?>" data-toggle="modal" data-target="#myModal2"><i class="fa fa-edit"></i></a>
                                                <?php
                                                }
                                                else{
                                                    if($row->pay_status != "paid" && $row->pay_status != "excise"){
                                                    ?>
                                                        <a href="<?= admin_url('sales/add_payment/'.$row->sale_id) ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i></a>
                                                    <?php
                                                    }
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script>
    $(document).ready(function(){

        $('.table').DataTable({
            dom: 'Bfrtip',
            paging: true,
            info: true,
            ordering:false,
            pageLength: -1,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
            ]
        });

        $('.remarkschange').dblclick(function(){
            var payid = $(this).data('payid');
            var sale_id = $(this).data('sale_id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            Swal.fire({
                title: "Enter Your Remarks?",
                text: "",
                icon: "info",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: (remarks) => {
                    return fetch(`<?= base_url('admin/reports/remarks?payid=') ?>${payid}&sale_id=${sale_id}&remarks=${remarks}&[${csrfName}]=${csrfHash}`)
                    .then(response => {
                        console.log(response);
                        if (!response.ok) {
                            console.log('Error');
                            throw new Error(response.statusText)
                        }
                        else if(remarks == ""){
                            throw new Error('Enter Remarks')
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
                        title: 'Remarks Add Successfully',
                        showConfirmButton: false,
                        timer: 10000
                    });
                    setTimeout(function(){ 
                        location.reload();
                    }, 1000);
                }
            });






        });

    });
</script>
