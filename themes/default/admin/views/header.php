<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= site_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= $Settings->site_name ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>
    <noscript><style type="text/css">#loading { display: none; }</style></noscript>
    <?php if ($Settings->user_rtl) { ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
    <?php } ?>
    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
    </script>
</head>
<body>
    <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
            </div>
        </div>
    </noscript>
    <div id="loading"></div>
    <div id="app_wrapper">
        <header id="header" class="navbar">
            <div class="container">
                <a class="navbar-brand" href="<?= admin_url() ?>"><span class="logo"><img src="themes/default/admin/views/logo2.png " width="200" /></span></a>
                <div class="btn-group visible-xs pull-right btn-visible-sm">
                    <button class="navbar-toggle btn" type="button" data-toggle="collapse" data-target="#sidebar_menu">
                        <span class="fa fa-bars"></span>
                    </button>
                    <?php if (SHOP) { ?>
                    <a href="<?= site_url('/') ?>" class="btn">
                        <span class="fa fa-shopping-cart"></span>
                    </a>
                    <?php } ?>
                    <a href="<?= admin_url('calendar') ?>" class="btn">
                        <span class="fa fa-calendar"></span>
                    </a>
                    <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn">
                        <span class="fa fa-user"></span>
                    </a>
                    <a href="<?= admin_url('logout'); ?>" class="btn">
                        <span class="fa fa-sign-out"></span>
                    </a>
                </div>
                <div class="header-nav">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown">
                        <a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
                            <img alt="" src="<?= $this->session->userdata('avatar') ? base_url() . 'assets/uploads/avatars/thumbs/' . $this->session->userdata('avatar') : base_url('assets/images/' . $this->session->userdata('gender') . '.png'); ?>" class="mini_avatar img-rounded">
                            <div class="user">
                                <span><?= lang('welcome') ?> <?= $this->session->userdata('username'); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>">
                                    <i class="fa fa-user"></i> <?= lang('profile'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>">
                                    <i class="fa fa-key"></i> <?= lang('change_password'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= admin_url('logout'); ?>">
                                    <i class="fa fa-sign-out"></i> <?= lang('logout'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown hidden-xs" style="padding: 8px 20px 0 0;" id="CurentDateTimeShow" ><?php echo date('Y-m-d H:i:s') ?></li>
                    <li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('dashboard') ?>" data-placement="bottom" href="<?= admin_url('welcome') ?>"><i class="fa fa-dashboard"></i></a></li>
                    <?php if ($Owner) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn tip" title="<?= lang('settings') ?>" data-placement="bottom" href="<?= admin_url('system_settings') ?>">
                                <i class="fa fa-cogs"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($info) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn tip" title="<?= lang('notifications') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-info-circle"></i>
                                <span class="number blightOrange black"><?= sizeof($info) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>
                                <li class="dropdown-content">
                                    <div class="scroll-div">
                                        <div class="top-menu-scroll">
                                            <ol class="oe">
                                                <?php foreach ($info as $n) {
                                                    echo '<li>' . $n->comment . '</li>';
                                                } ?>
                                            </ol>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if ($events) { ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                                <span class="number blightOrange black"><?= sizeof($events) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header">
                                    <i class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?>
                                </li>
                                <li class="dropdown-content">
                                    <div class="top-menu-scroll">
                                        <ol class="oe">
                                            <?php foreach ($events as $event) {
                                                echo '<li>' . date($dateFormats['php_ldate'], strtotime($event->start)) . ' <strong>' . $event->title . '</strong><br>'.$event->description.'</li>';
                                            } ?>
                                        </ol>
                                    </div>
                                </li>
                                <li class="dropdown-footer">
                                    <a href="<?= admin_url('calendar') ?>" class="btn-block link">
                                        <i class="fa fa-calendar"></i> <?= lang('calendar') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (($Owner || $Admin || $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts']) && ($qty_alert_num > 0 || $exp_alert_num > 0 || $shop_sale_alerts)) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn blightOrange tip" title="<?= lang('alerts') ?>" data-placement="left" data-toggle="dropdown" href="#">
                                <i class="fa fa-exclamation-triangle"></i>
                                <span class="number bred black"><?= $qty_alert_num+(($Settings->product_expiry) ? $exp_alert_num : 0)+$shop_sale_alerts+$shop_payment_alerts; ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <?php if ($qty_alert_num > 0) { ?>
                                    <li>
                                        <a href="<?= admin_url('reports/quantity_alerts') ?>" class="">
                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                            <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ($Settings->product_expiry) { ?>
                                    <li>
                                        <a href="<?= admin_url('reports/expiry_alerts') ?>" class="">
                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                                            <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ($shop_sale_alerts) { ?>
                                    <li>
                                        <a href="<?= admin_url('sales?shop=yes&delivery=no') ?>" class="">
                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_sale_alerts; ?></span>
                                            <span style="padding-right: 35px;"><?= lang('sales_x_delivered') ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ($shop_payment_alerts) { ?>
                                    <li>
                                        <a href="<?= admin_url('sales?shop=yes&attachment=yes') ?>" class="">
                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_payment_alerts; ?></span>
                                            <span style="padding-right: 35px;"><?= lang('manual_payments') ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (POS) { ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn bdarkGreen tip" title="<?= lang('pos') ?>" data-placement="bottom" href="<?= admin_url('pos') ?>">
                                <i class="fa fa-th-large"></i> <span class="padding05"><?= lang('pos') ?></span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($Owner) { ?>
                        <li class="dropdown">
                            <a class="btn bdarkGreen tip" id="today_profit" title="<span><?= lang('today_profit') ?></span>" data-placement="bottom" data-html="true" href="<?= admin_url('reports/profit') ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-hourglass-2"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($Owner || $Admin) { ?>
                        <?php if (POS) { ?>
                            <li class="dropdown hidden-xs">
                                <a class="btn bblue tip" title="<?= lang('list_open_registers') ?>" data-placement="bottom" href="<?= admin_url('pos/registers') ?>">
                                    <i class="fa fa-list"></i>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn bred tip" title="<?= lang('clear_ls') ?>" data-placement="bottom" id="clearLS" href="#">
                                <i class="fa fa-eraser"></i>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </header>
        <div class="container" id="container">
            <div class="row" id="main-con">
                <table class="lt">
                    <tr>
                        <td class="sidebar-con">
                            <div id="sidebar-left">
                                <div class="sidebar-nav nav-collapse collapse navbar-collapse" id="sidebar_menu">
                                    <ul class="nav main-menu">
                                        <!-- Dashboard Start -->
                                        <li class="mm_welcome">
                                            <a href="<?= admin_url() ?>">
                                                <i class="fa fa-dashboard"></i>
                                                <span class="text"> <?= lang('dashboard'); ?></span>
                                            </a>
                                        </li>
                                        <!-- Dashboard End -->
                                        <?php if ($Owner || $Admin || $GP['products-index'] || $GP['products-add'] || $GP['products-barcode'] || $GP['products-adjustments'] || $GP['products-stock_count']) { ?>
                                            <!-- Porducts Start -->
                                            <li class="mm_products">
                                                <a class="dropmenu" href="#">
                                                    <i class="fa fa-barcode"></i>
                                                    <span class="text"> <?= lang('products'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="products_index">
                                                        <a class="submenu" href="<?= admin_url('products'); ?>">
                                                            <i class="fa fa-barcode"></i>
                                                            <span class="text"> <?= lang('list_products'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="products_index">
                                                        <a class="submenu" href="<?= admin_url('products/deactive_product'); ?>">
                                                            <i class="fa fa-barcode"></i>
                                                            <span class="text"> <?= lang('Deactive Product'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                        if($Owner || $Admin || $GP['products-add']){
                                                        ?>
                                                        <li id="products_add">
                                                            <a class="submenu" href="<?= admin_url('products/add'); ?>">
                                                                <i class="fa fa-plus-circle"></i>
                                                                <span class="text"> <?= lang('add_product'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="system_settings_categories">
                                                            <a href="<?= admin_url('system_settings/categories') ?>">
                                                                <i class="fa fa-folder-open"></i><span class="text"> <?= lang('categories'); ?></span>
                                                            </a>
                                                        </li>
                                                        <?php
                                                        }
                                                    ?>
                                                    <li id="products_group">
                                                        <a class="submenu" href="<?= admin_url('products/groups'); ?>">
                                                            <i class="fa fa-plus-circle"></i>
                                                            <span class="text"> <?= lang('Product Groups'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                        if($Owner){
                                                        ?>
                                                        <!-- <li id="products_import_csv">
                                                            <a class="submenu" href="<?= admin_url('products/import_csv'); ?>">
                                                                <i class="fa fa-file-text"></i>
                                                                <span class="text"> <?= lang('import_products'); ?></span>
                                                            </a>
                                                        </li> -->
                                                        <?php
                                                        }
                                                    ?>
                                                    <?php
                                                        if($Owner || $Admin || $GP['products-barcode']){
                                                        ?>
                                                        <li id="products_print_barcodes">
                                                            <a class="submenu" href="<?= admin_url('products/print_barcodes'); ?>">
                                                                <i class="fa fa-tags"></i>
                                                                <span class="text"> <?= lang('print_barcode_label'); ?></span>
                                                            </a>
                                                        </li>
                                                        <?php
                                                        }
                                                    ?>
                                                    <?php
                                                        if($Owner || $Admin || $GP['products-adjustments']){
                                                        ?>
                                                        <!-- <li id="products_quantity_adjustments">
                                                            <a class="submenu" href="<?= admin_url('products/quantity_adjustments'); ?>">
                                                                <i class="fa fa-filter"></i>
                                                                <span class="text"> <?= lang('quantity_adjustments'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="products_add_adjustment">
                                                            <a class="submenu" href="<?= admin_url('products/add_adjustment'); ?>">
                                                                <i class="fa fa-filter"></i>
                                                                <span class="text"> <?= lang('add_adjustment'); ?></span>
                                                            </a>
                                                        </li> -->
                                                        <?php
                                                        }
                                                    ?>
                                                    <?php
                                                        if($Owner || $Admin || $GP['products-stock_count']){
                                                        ?>
                                                        <!-- <li id="products_stock_counts">
                                                            <a class="submenu" href="<?= admin_url('products/stock_counts'); ?>">
                                                                <i class="fa fa-list-ol"></i>
                                                                <span class="text"> <?= lang('stock_counts'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="products_count_stock">
                                                            <a class="submenu" href="<?= admin_url('products/count_stock'); ?>">
                                                                <i class="fa fa-plus-circle"></i>
                                                                <span class="text"> <?= lang('count_stock'); ?></span>
                                                            </a>
                                                        </li> -->
                                                        <?php
                                                        }
                                                    ?>
                                                </ul>
                                            </li>
                                            <!-- Porducts End -->
                                        <?php } ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['sales-index'] || $GP['sales-add'] || $GP['sales-deliveries'] || $GP['sales-gift_cards']) {
                                            ?>
                                                <!-- Sales Start -->
                                                <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-heart"></i>
                                                        <span class="text"> <?= lang('sales'); ?>
                                                        </span> <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php
                                                            if($Owner || $Admin || $GP['sales-index'] || $GP['sales-add']){
                                                            ?>
                                                                <li id="sales_index">
                                                                    <a class="submenu" href="<?= admin_url('sales'); ?>">
                                                                        <i class="fa fa-heart"></i>
                                                                        <span class="text"> <?= lang('list_sales'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if (POS && ($Owner || $Admin || $GP['pos-index'])) { 
                                                            ?>
                                                                <li id="pos_sales">
                                                                    <a class="submenu" href="<?= admin_url('pos/sales'); ?>">
                                                                        <i class="fa fa-heart"></i>
                                                                        <span class="text"> <?= lang('pos_sales'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['sales-add']){
                                                            ?>
                                                                <li id="sales_add">
                                                                    <a class="submenu" href="<?= admin_url('sales/add'); ?>">
                                                                        <i class="fa fa-plus-circle"></i>
                                                                        <span class="text"> <?= lang('add_sale'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if($Owner){
                                                            ?>
                                                                <!-- <li id="sales_sale_by_csv">
                                                                    <a class="submenu" href="<?= admin_url('sales/sale_by_csv'); ?>">
                                                                        <i class="fa fa-plus-circle"></i>
                                                                        <span class="text"> <?= lang('add_sale_by_csv'); ?></span>
                                                                    </a>
                                                                </li> -->
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['sales-deliveries']){
                                                            ?>
                                                                <!-- <li id="sales_deliveries">
                                                                    <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                                                        <i class="fa fa-truck"></i>
                                                                        <span class="text"> <?= lang('deliveries'); ?></span>
                                                                    </a>
                                                                </li> -->
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['sales-gift_cards']){
                                                            ?>
                                                                <!-- <li id="sales_gift_cards">
                                                                    <a class="submenu" href="<?= admin_url('sales/gift_cards'); ?>">
                                                                        <i class="fa fa-gift"></i>
                                                                        <span class="text"> <?= lang('list_gift_cards'); ?></span>
                                                                    </a>
                                                                </li> -->
                                                            <?php
                                                            }
                                                        ?>
                                                    </ul>
                                                </li>
                                                <!-- Sales End -->
                                            <?php
                                            }
                                        ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['so_view'] || $GP['so_add']) {
                                            ?>
                                                <!-- Sale Order Start -->
                                                <li class="mm_salesorders">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-heart"></i>
                                                        <span class="text"> Sale Orders
                                                        </span> <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php
                                                            if($Owner || $Admin || $GP['so_view']){
                                                                ?>
                                                                <li id="salesorders_index">
                                                                    <a class="submenu" href="<?= admin_url('salesorders'); ?>">
                                                                        <i class="fa fa-heart"></i>
                                                                        <span class="text"> <?= lang('list_sales'); ?></span>
                                                                    </a>
                                                                </li>
                                                                <?php
                                                            }    
                                                            ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['so_add']){
                                                            ?>
                                                            <li id="salesorders_add">
                                                                <a class="submenu" href="<?= admin_url('salesorders/add'); ?>">
                                                                    <i class="fa fa-heart"></i>
                                                                        <span class="text"> Add Sale Order</span>
                                                                    </a>
                                                                </li>
                                                                <?php
                                                            }    
                                                            ?>
                                                    </ul>
                                                </li>
                                                <!-- Sale Order End -->
                                            <?php
                                            }
                                        ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['purchases-index'] || $GP['purchases-add']) {
                                            ?>
                                                <!-- Purchase Start -->
                                                <li class="mm_purchases">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-star"></i>
                                                        <span class="text"> <?= lang('purchases'); ?>
                                                        </span> <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php
                                                            if($Owner || $Admin || $GP['purchases-index']){
                                                            ?>
                                                                <li id="purchases_index">
                                                                    <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                                                        <i class="fa fa-star"></i>
                                                                        <span class="text"> <?= lang('list_purchases'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }    
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['purchases-add']){
                                                            ?>
                                                                <li id="purchases_add">
                                                                    <a class="submenu" href="<?= admin_url('purchases/add'); ?>">
                                                                        <i class="fa fa-plus-circle"></i>
                                                                        <span class="text"> <?= lang('add_purchase'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }    
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['purchase_adj_view']){
                                                            ?>
                                                                <li id="purchases_adjustment">
                                                                    <a class="submenu" href="<?= admin_url('purchases/adjustment'); ?>">
                                                                        <i class="fa fa-plus-circle"></i>
                                                                        <span class="text"> Batch adjustments</span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }    
                                                        ?>
                                                    </ul>
                                                </li>
                                                <!-- Purchase End -->
                                            <?php
                                            }
                                        ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['po_view'] || $GP['po_add']) {
                                            ?>
                                                <!-- Purchase Orders Start -->
                                                <li class="mm_purchaseorder">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-star"></i>
                                                        <span class="text"> Purchase Orders
                                                        </span> <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php
                                                            if($Owner || $Admin || $GP['po_view']){
                                                            ?>
                                                                <li id="purchaseorder_index">
                                                                    <a class="submenu" href="<?= admin_url('purchaseorder'); ?>">
                                                                        <i class="fa fa-star"></i><span class="text"> List Purchase Orders</span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['po_add']){
                                                            ?>
                                                                <li id="purchaseorder_add">
                                                                    <a class="submenu" href="<?= admin_url('purchaseorder/add'); ?>">
                                                                        <i class="fa fa-star"></i><span class="text"> Add Purchase Orders</span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                    </ul>
                                                </li>
                                                <!-- Purchase Orders End -->
                                            <?php
                                            }
                                        ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['purchases-expenses'] || $GP['purchases_add_expense']) {
                                            ?>
                                                <!-- Expenses Start -->
                                                <li class="mm_expenses">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                        <span class="text"> <?= lang('expenses'); ?>
                                                        </span> <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php
                                                            if($Owner || $Admin || $GP['purchases-expenses']){
                                                            ?>
                                                                <li id="purchases_expenses">
                                                                    <a class="submenu" href="<?= admin_url('purchases/expenses'); ?>">
                                                                        <i class="fa fa-dollar"></i>
                                                                        <span class="text"> <?= lang('list_expenses'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                        <?php
                                                            if($Owner || $Admin || $GP['purchases_add_expense']){
                                                            ?>
                                                                <li id="purchases_add_expense">
                                                                    <a class="submenu" href="<?= admin_url('purchases/add_expense'); ?>">
                                                                        <i class="fa fa-plus-circle"></i>
                                                                        <span class="text"> <?= lang('add_expense'); ?></span>
                                                                    </a>
                                                                </li>
                                                                <li id="system_settings_expense_categories">
                                                                    <a href="<?= admin_url('system_settings/expense_categories') ?>">
                                                                        <i class="fa fa-folder-open"></i><span class="text"> <?= lang('expense_categories'); ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php
                                                            }
                                                        ?>
                                                    </ul>
                                                </li>
                                                <!-- Expenses End -->
                                            <?php
                                            }
                                        ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['transfers-index'] || $GP['transfers-add']) {
                                            ?>
                                                <!-- Transfers Start -->
                                                <li class="mm_transfers">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-star-o"></i>
                                                        <span class="text"> <?= lang('transfers'); ?> </span>
                                                        <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php if ($Owner || $Admin || $GP['transfers-index']) { ?>
                                                            <li id="transfers_index">
                                                                <a class="submenu" href="<?= admin_url('transfers'); ?>">
                                                                    <i class="fa fa-star-o"></i><span class="text"> <?= lang('list_transfers'); ?></span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($Owner || $Admin || $GP['transfers-add']) { ?>
                                                            <li id="transfers_add">
                                                                <a class="submenu" href="<?= admin_url('transfers/add'); ?>">
                                                                    <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_transfer'); ?></span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($Owner) { ?>
                                                            <!-- <li id="transfers_purchase_by_csv">
                                                                <a class="submenu" href="<?= admin_url('transfers/transfer_by_csv'); ?>">
                                                                    <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_transfer_by_csv'); ?></span>
                                                                </a>
                                                            </li> -->
                                                        <?php } ?>
                                                    </ul>
                                                </li>
                                                <!-- Transfers End -->
                                            <?php
                                            }
                                        ?>
                                        <?php
                                            if ($Owner || $Admin || $GP['store_view'] || $GP['store_add']) {
                                            ?>
                                                <!-- Stores Start -->
                                                <li class="mm_stores">
                                                    <a class="dropmenu" href="#">
                                                        <i class="fa fa-shopping-cart"></i>
                                                        <span class="text"> Stores</span> <span class="chevron closed"></span>
                                                    </a>
                                                    <ul>
                                                        <?php if ($Owner || $Admin || $GP['store_view']) { ?>
                                                            <li id="stores_index">
                                                                <a class="submenu" href="<?= admin_url('stores'); ?>">
                                                                    <i class="fa fa-list-ol"></i><span class="text"> Store List</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($Owner || $Admin || $GP['store_add']) { ?>
                                                            <li id="stores_add">
                                                                <a class="submenu" href="<?= admin_url('stores/add'); ?>">
                                                                    <i class="fa fa-cart-plus"></i><span class="text"> Add Store</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </li>
                                                <!-- Stores End -->
                                            <?php
                                            }
                                        ?>
                                        <?php if ($Owner || $Admin || $GP['customers-index'] || $GP['customers-add'] || $GP['suppliers-index'] || $GP['suppliers-add']) { ?>
                                            <!-- Peoples Start -->
                                            <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                                <a class="dropmenu" href="#">
                                                    <i class="fa fa-users"></i>
                                                    <span class="text"> <?= lang('people'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <?php if ($Owner) { ?>
                                                        <li id="auth_users">
                                                            <a class="submenu" href="<?= admin_url('users'); ?>">
                                                                <i class="fa fa-users"></i><span class="text"> <?= lang('list_users'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="auth_create_user">
                                                            <a class="submenu" href="<?= admin_url('users/create_user'); ?>">
                                                                <i class="fa fa-user-plus"></i><span class="text"> <?= lang('new_user'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="billers_index">
                                                            <a class="submenu" href="<?= admin_url('billers'); ?>">
                                                                <i class="fa fa-users"></i><span class="text"> <?= lang('list_billers'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="billers_index">
                                                            <a class="submenu" href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_biller'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($Owner || $Admin || $GP['customers-index']) { ?>
                                                        <li id="customers_index">
                                                            <a class="submenu" href="<?= admin_url('customers'); ?>">
                                                                <i class="fa fa-users"></i><span class="text"> <?= lang('list_customers'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                                        <li id="customers_index">
                                                            <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_customer'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($Owner || $Admin || $GP['suppliers-index']) { ?>
                                                        <li id="suppliers_index">
                                                            <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                                                <i class="fa fa-users"></i><span class="text"> <?= lang('list_suppliers'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($Owner || $Admin || $GP['suppliers-add']) { ?>
                                                        <li id="suppliers_index">
                                                            <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_supplier'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>


                                                </ul>
                                            </li>
                                            <!-- Peoples End -->
                                        <?php } ?>
                                        <!-- New Report Start -->
                                        <li class="mm_reports">
                                            <a class="dropmenu" href="#">
                                                <i class="fa fa-bar-chart-o"></i>
                                                <span class="text"> <?= lang('reports'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <?php if ($Owner || $Admin || $GP['dc_report']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/dc_report') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> DC Report</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['report_sales_summary']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/salessummary') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Sales Summary</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['reports-sales']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/salesreport') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Sales Report</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= admin_url('reports/salesreturnremarksreport') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> SRIR Report</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['purchase_report']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/purchasereport') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Purchase Report</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= admin_url('reports/purchasereturn') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Purchase Return Report</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['batchwise_report']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/batchwise') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Batch wise</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['old_stock']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/old_stock_report') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Old Stuck</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['monthly_items_demand']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/monthly_items_demand') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Monthly Items Demand</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['ces_stock']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/expected_soldout') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> CES Stock</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['short_expiry_stock']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/shortexpiry_stock') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Short Expiry Stock</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['expiry_stock']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/expired_stock') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Expired Stock</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['customers_ledger']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/customer_legder') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Customers Ledger</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['customers_wht_ledger']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/customer_wht_legder') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Customers WHT Ledger</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['legder_summary']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/ledger_summery') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Ledger Summary</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['supplier_ledger']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/supplier_legder') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Supplier Ledger</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['credit_report']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/creadits') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Creadit Report</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['due_invoice']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/due_invoices') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Due Invoices</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['so_items_wise']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/so_items_wise') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> SO Items Wise</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= admin_url('reports/po_items_wise') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> PO Items Wise</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['product_ledger']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/products_ledger') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> Products Ledger</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['customers_ledger']) { ?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/batch_wise_true_false') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> True & False Batch Waise</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= admin_url('reports/product_wise_true_false') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> True & False Product Waise</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= admin_url('reports/so_hold_quantity') ?>">
                                                            <i class="fa fa-bars"></i><span class="text"> SO Hold Quantity</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                        <!-- New Report End -->
                                        <?php 
                                            if($Owner || $Admin || $GP['bulk_discount']){
                                        ?>
                                            <li id="system_settings_bulk_discounts">
                                                <a href="<?= admin_url('system_settings/bulk_discounts') ?>">
                                                    <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('Bulk Discounts'); ?></span>
                                                </a>
                                            </li>
                                        <?php
                                            }
                                        
                                        ?>
                                        <?php 
                                            if($Owner || $Admin || $GP['tax_rates']){
                                        ?>
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/tax_rates') ?>">
                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('tax_rates'); ?></span>
                                            </a>
                                        </li>
                                        <?php
                                            }
                                        
                                        ?>
                                        <?php 
                                            if($Owner || $Admin || $GP['wallet_view']){
                                        ?>
                                        <li id="system_settings_wallets">
                                            <a href="<?= admin_url('system_settings/wallets') ?>">
                                                <i class="fa fa-money"></i><span class="text"> <?= lang('Wallets'); ?></span>
                                            </a>
                                        </li>
                                        <?php
                                            }
                                        
                                        ?>
                                        <?php if ($Owner || $Admin) {?>
                                            <!-- System Settings Start -->
                                            <li class="mm_system_settings <?= strtolower($this->router->fetch_method()) == 'sales' ? '' : 'mm_pos' ?>">
                                                <a class="dropmenu" href="#">
                                                    <i class="fa fa-cog"></i><span class="text"> <?= lang('settings'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="system_settings_index">
                                                        <a href="<?= admin_url('system_settings') ?>">
                                                            <i class="fa fa-cogs"></i><span class="text"> <?= lang('system_settings'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_company">
                                                        <a href="<?= admin_url('system_settings/own_companies') ?>">
                                                            <i class="fa fa-th-list"></i><span class="text"> <?= lang('company'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php if (POS) { ?>
                                                        <li id="pos_settings">
                                                            <a href="<?= admin_url('pos/settings') ?>">
                                                                <i class="fa fa-th-large"></i><span class="text"> <?= lang('pos_settings'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="pos_printers">
                                                            <a href="<?= admin_url('pos/printers') ?>">
                                                                <i class="fa fa-print"></i><span class="text"> <?= lang('list_printers'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="pos_add_printer">
                                                            <a href="<?= admin_url('pos/add_printer') ?>">
                                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_printer'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <!-- <li id="system_settings_change_logo">
                                                        <a href="<?= admin_url('system_settings/change_logo') ?>" data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-upload"></i><span class="text"> <?= lang('change_logo'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_currencies">
                                                        <a href="<?= admin_url('system_settings/currencies') ?>">
                                                            <i class="fa fa-money"></i><span class="text"> <?= lang('currencies'); ?></span>
                                                        </a>
                                                    </li> -->
                                                    <!-- <li id="system_settings_customer_groups">
                                                        <a href="<?= admin_url('system_settings/customer_groups') ?>">
                                                            <i class="fa fa-chain"></i><span class="text"> <?= lang('customer_groups'); ?></span>
                                                        </a>
                                                    </li> -->
                                                    <!-- <li id="system_settings_price_groups">
                                                        <a href="<?= admin_url('system_settings/price_groups') ?>">
                                                            <i class="fa fa-dollar"></i><span class="text"> <?= lang('price_groups'); ?></span>
                                                        </a>
                                                    </li> -->
                                                    <li id="system_settings_units">
                                                        <a href="<?= admin_url('system_settings/units') ?>">
                                                            <i class="fa fa-wrench"></i><span class="text"> <?= lang('units'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_brands">
                                                        <a href="<?= admin_url('system_settings/brands') ?>">
                                                            <i class="fa fa-th-list"></i><span class="text"> <?= lang('brands'); ?></span>
                                                        </a>
                                                    </li>
                                                    <!-- <li id="system_settings_variants">
                                                        <a href="<?= admin_url('system_settings/variants') ?>">
                                                            <i class="fa fa-tags"></i><span class="text"> <?= lang('variants'); ?></span>
                                                        </a>
                                                    </li> -->
                                                    <li id="system_settings_warehouses">
                                                        <a href="<?= admin_url('system_settings/warehouses') ?>">
                                                            <i class="fa fa-building-o"></i><span class="text"> <?= lang('warehouses'); ?></span>
                                                        </a>
                                                    </li>
                                                    <!-- <li id="system_settings_email_templates">
                                                        <a href="<?= admin_url('system_settings/email_templates') ?>">
                                                            <i class="fa fa-envelope"></i><span class="text"> <?= lang('email_templates'); ?></span>
                                                        </a>
                                                    </li> -->
                                                    <li id="system_settings_user_groups">
                                                        <a href="<?= admin_url('system_settings/user_groups') ?>">
                                                            <i class="fa fa-key"></i><span class="text"> <?= lang('group_permissions'); ?></span>
                                                        </a>
                                                    </li>
                                                    <!-- <li id="system_settings_backups">
                                                        <a href="<?= admin_url('system_settings/backups') ?>">
                                                            <i class="fa fa-database"></i><span class="text"> <?= lang('backups'); ?></span>
                                                        </a>
                                                    </li> -->
                                                </ul>
                                            </li>
                                            <!-- System Settings End -->
                                        <?php } ?>
                                        <?php if ($Owner || $Admin) { ?>
                                            <li class="mm_logs">
                                                <a class="submenu" href="<?= admin_url('logs'); ?>">
                                                    <i class="fa fa-eye"></i><span class="text"> <?= lang('Activity Logs'); ?></span>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <a href="#" id="main-menu-act" class="full visible-md visible-lg">
                                    <i class="fa fa-angle-double-left"></i>
                                </a>
                            </div>
                        </td>
                        <td class="content-con">
                            <div id="content">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <ul class="breadcrumb">
                                            <?php
                                                foreach ($bc as $b) {
                                                    if ($b['link'] === '#') {
                                                        echo '<li class="active">' . $b['page'] . '</li>';
                                                    } else {
                                                        echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                                                    }
                                                }
                                            ?>
                                            <li class="right_log hidden-xs">
                                                <?= lang('your_ip') . ' ' . $ip_address . " <span class='hidden-sm'>( " . lang('last_login_at') . ": " . date($dateFormats['php_ldate'], $this->session->userdata('old_last_login')) . " " . ($this->session->userdata('last_ip') != $ip_address ? lang('ip:') . ' ' . $this->session->userdata('last_ip') : '') . " )</span>" ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if ($message) { ?>
                                            <div class="alert alert-success">
                                                <button data-dismiss="alert" class="close" type="button">×</button>
                                                <?= $message; ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($error) { ?>
                                            <div class="alert alert-danger">
                                                <button data-dismiss="alert" class="close" type="button">×</button>
                                                <?= $error; ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($warning) { ?>
                                            <div class="alert alert-warning">
                                                <button data-dismiss="alert" class="close" type="button">×</button>
                                                <?= $warning; ?>
                                            </div>
                                        <?php } ?>
                                        <?php
                                            if ($info) {
                                                foreach ($info as $n) {
                                                    if (!$this->session->userdata('hidden' . $n->id)) {
                                                        ?>
                                                        <div class="alert alert-info">
                                                            <a href="#" id="<?= $n->id ?>" class="close hideComment external"
                                                            data-dismiss="alert">&times;</a>
                                                            <?= $n->comment; ?>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="alerts-con"></div>