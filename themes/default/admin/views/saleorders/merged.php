    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
        
                <table id="SLData" class="table table-bordered table-hover table-striped" >
                    <thead>
                    <tr>
                        <th colspan="4" >Summary Details</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="width:25%">No of SKU</th>
                            <td style="width:25%"><?php echo $detail['nosku'] ?></td>
                            <th style="width:25%">No of SO</th>
                            <td style="width:25%"><?php echo $detail['noso'] ?></td>
                        </tr>
                        <tr>
                            <th>Demand Quantity</th>
                            <td><?php echo $detail['demand_qty'] ?></td>
                            <th>Demand Value</th>
                            <td><?php echo $detail['demand_val'] ?></td>
                        </tr>
                        <tr>
                            <th>Completed Quantity</th>
                            <td><?php echo $detail['complete_qty'] ?></td>
                            <th>Completed Value</th>
                            <td><?php echo $detail['complete_val'] ?></td>
                        </tr>
                        <tr>
                            <th>Uncompleted Quantity</th>
                            <td><?php echo $detail['uncomplete_qty'] ?></td>
                            <th>Uncompleted Value</th>
                            <td><?php echo $detail['uncomplete_val'] ?></td>
                        </tr>
                        <tr>
                            <th>Complete Quantity Percentage</th>
                            <td><?php echo $detail['qty_percentage'] ?></td>
                            <th>Complate Value Percentage</th>
                            <td><?php echo $detail['val_percentage'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
