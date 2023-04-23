
    <div id="page_content">
        <div id="page_content_inner">

            <!-- statistics (small charts) -->
            <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium" data-uk-grid-margin>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <span class="uk-text-muted uk-text-small">Today Sale</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe"><?php echo $current_status['today_sale']; ?></span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <span class="uk-text-muted uk-text-small">Yesterday Sale</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe"><?php echo $current_status['yesterday_sale']; ?></span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <span class="uk-text-muted uk-text-small">Current Month Sale</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe"><?php echo $current_status['current_month_sale']; ?></span></h2>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <span class="uk-text-muted uk-text-small">Previous Month Sale</span>
                            <h2 class="uk-margin-remove"><span class="countUpMe"><?php echo $current_status['last_month_sale']; ?></span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- tasks -->
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-1-1">
                    <div class="md-card">
                        <div class="md-card-toolbar">
                            <div class="md-card-toolbar-actions">
                                <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                            </div>
                            <h3 class="md-card-toolbar-heading-text">
                                Latest Sales
                            </h3>
                        </div>
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Reference No</th>
                                            <th>Customer</th>
                                            <th>Grand Total</th>
                                            <th>Paid</th>
                                            <th>Payment Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if($sales != ""){
                                                foreach($sales as $key => $sale){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $key+1 ?></td>
                                                        <td><?php echo $sale->date ?></td>
                                                        <td><?php echo $sale->reference_no ?></td>
                                                        <td><?php echo $sale->customer ?></td>
                                                        <td><?php echo $sale->grand_total ?></td>
                                                        <td><?php echo $sale->paid ?></td>
                                                        <td><?php echo $sale->payment_status ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- tasks -->
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-toolbar">
                            <div class="md-card-toolbar-actions">
                                <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                            </div>
                            <h3 class="md-card-toolbar-heading-text">
                                Best Selling Products
                            </h3>
                        </div>
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if($bs != ""){
                                                foreach($bs as $key => $b){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $key+1 ?></td>
                                                        <td><?php echo $b->product_code ?></td>
                                                        <td><?php echo $b->product_name ?></td>
                                                        <td><?php echo $b->quantity ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-toolbar">
                            <div class="md-card-toolbar-actions">
                                <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                            </div>
                            <h3 class="md-card-toolbar-heading-text">
                                Last Month Best Selling Products
                            </h3>
                        </div>
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if($lmbs != ""){

                                                foreach($lmbs as $key => $lmb){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $key+1 ?></td>
                                                        <td><?php echo $lmb->product_code ?></td>
                                                        <td><?php echo $lmb->product_name ?></td>
                                                        <td><?php echo $lmb->quantity ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- page specific plugins -->
    <!-- d3 -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/d3/d3.min.js"></script>
    <!-- metrics graphics (charts) -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/metrics-graphics/dist/metricsgraphics.min.js"></script>
    <!-- c3.js (charts) -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/c3js-chart/c3.min.js"></script>
    <!-- chartist (charts) -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/chartist/dist/chartist.min.js"></script>
    <!--  charts functions -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>/js/pages/plugins_charts.min.js"></script>    
    <!-- peity (small charts) -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/peity/jquery.peity.min.js"></script>
    <!-- easy-pie-chart (circular statistics) -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
    <!-- countUp -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/countUp.js/dist/countUp.min.js"></script>
    <!-- handlebars.js -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/handlebars/handlebars.min.js"></script>
    <script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/handlebars_helpers.min.js"></script>
    <!-- CLNDR -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/clndr/clndr.min.js"></script>

