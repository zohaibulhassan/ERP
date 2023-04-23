<style>
    .text-center {
        text-align:center !important;
    }
</style>

<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text"><?php echo $group->description . ' ( ' . $group->name . ' ) ' . $this->lang->line("group_permissions"); ?></h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">

            <div>

            <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
        echo admin_form_open_multipart("#", $attrib);
    ?>


                    <table class="uk-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:18%"><?= lang("module_name"); ?></th>
                                <th class="text-center" style="width:3%"><?= lang("view"); ?></th>
                                <th class="text-center" style="width:3%"><?= lang("add"); ?></th>
                                <th class="text-center" style="width:3%"><?= lang("edit"); ?></th>
                                <th class="text-center" style="width:3%"><?= lang("delete"); ?></th>
                                <th class="text-center"><?= lang("misc"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= lang("products"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="products-index" <?php echo $p->{'products-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="products-add" <?php echo $p->{'products-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="products-edit" <?php echo $p->{'products-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="products-delete" <?php echo $p->{'products-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="products-cost" class="checkbox" name="products-cost" <?php echo $p->{'products-cost'} ? "checked" : ''; ?>>
                                    <label for="products-cost" class="padding05"><?= lang('product_cost') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="products-price" class="checkbox" name="products-price" <?php echo $p->{'products-price'} ? "checked" : ''; ?>>
                                    <label for="products-price" class="padding05"><?= lang('product_price') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="products-adjustments" class="checkbox" name="products-adjustments" <?php echo $p->{'products-adjustments'} ? "checked" : ''; ?>>
                                    <label for="products-adjustments" class="padding05"><?= lang('adjustments') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="products-barcode" class="checkbox" name="products-barcode" <?php echo $p->{'products-barcode'} ? "checked" : ''; ?>>
                                    <label for="products-barcode" class="padding05"><?= lang('print_barcodes') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="products-stock_count" class="checkbox" name="products-stock_count" <?php echo $p->{'products-stock_count'} ? "checked" : ''; ?>>
                                    <label for="products-stock_count" class="padding05"><?= lang('stock_counts') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("sales"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-index" <?php echo $p->{'sales-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-add" <?php echo $p->{'sales-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-edit" <?php echo $p->{'sales-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-delete" <?php echo $p->{'sales-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="sales-email" class="checkbox" name="sales-email" <?php echo $p->{'sales-email'} ? "checked" : ''; ?>>
                                    <label for="sales-email" class="padding05"><?= lang('email') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="sales-pdf" class="checkbox" name="sales-pdf" <?php echo $p->{'sales-pdf'} ? "checked" : ''; ?>>
                                    <label for="sales-pdf" class="padding05"><?= lang('pdf') ?></label>
                                </span>
                                <span>
                                    <?php if (POS) { ?>
                                    <input type="checkbox" value="1" id="pos-index" class="checkbox" name="pos-index" <?php echo $p->{'pos-index'} ? "checked" : ''; ?>>
                                    <label for="pos-index" class="padding05"><?= lang('pos') ?></label>
                                    <?php } ?>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="sales-payments" class="checkbox" name="sales-payments" <?php echo $p->{'sales-payments'} ? "checked" : ''; ?>>
                                    <label for="sales-payments" class="padding05"><?= lang('payments') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="sales-return_sales" class="checkbox" name="sales-return_sales" <?php echo $p->{'sales-return_sales'} ? "checked" : ''; ?>>
                                    <label for="sales-return_sales" class="padding05"><?= lang('return_sales') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("deliveries"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-deliveries" <?php echo $p->{'sales-deliveries'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-add_delivery" <?php echo $p->{'sales-add_delivery'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-edit_delivery" <?php echo $p->{'sales-edit_delivery'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-delete_delivery" <?php echo $p->{'sales-delete_delivery'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="sales-email" class="checkbox" name="sales-email_delivery" <?php echo $p->{'sales-email_delivery'} ? "checked" : ''; ?>>
                                    <label for="sales-email_delivery" class="padding05"><?= lang('email') ?></label>
                                </span>
                            
                                <span>
                                    <input type="checkbox" value="1" id="sales-pdf" class="checkbox" name="sales-pdf_delivery" <?php echo $p->{'sales-pdf_delivery'} ? "checked" : ''; ?>>
                                    <label for="sales-pdf_delivery" class="padding05"><?= lang('pdf') ?></label>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("gift_cards"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-gift_cards" <?php echo $p->{'sales-gift_cards'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-add_gift_card" <?php echo $p->{'sales-add_gift_card'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-edit_gift_card" <?php echo $p->{'sales-edit_gift_card'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="sales-delete_gift_card" <?php echo $p->{'sales-delete_gift_card'} ? "checked" : ''; ?>>
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("quotes"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="quotes-index" <?php echo $p->{'quotes-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="quotes-add" <?php echo $p->{'quotes-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="quotes-edit" <?php echo $p->{'quotes-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="quotes-delete" <?php echo $p->{'quotes-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="quotes-email" class="checkbox" name="quotes-email" <?php echo $p->{'quotes-email'} ? "checked" : ''; ?>>
                                    <label for="quotes-email" class="padding05"><?= lang('email') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="quotes-pdf" class="checkbox" name="quotes-pdf" <?php echo $p->{'quotes-pdf'} ? "checked" : ''; ?>>
                                    <label for="quotes-pdf" class="padding05"><?= lang('pdf') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("purchases"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases-index" <?php echo $p->{'purchases-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases-add" <?php echo $p->{'purchases-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases-edit" <?php echo $p->{'purchases-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases-delete" <?php echo $p->{'purchases-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="purchases-email" class="checkbox" name="purchases-email" <?php echo $p->{'purchases-email'} ? "checked" : ''; ?>>
                                    <label for="purchases-email" class="padding05"><?= lang('email') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="purchases-pdf" class="checkbox" name="purchases-pdf" <?php echo $p->{'purchases-pdf'} ? "checked" : ''; ?>>
                                    <label for="purchases-pdf" class="padding05"><?= lang('pdf') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="purchases-payments" class="checkbox" name="purchases-payments" <?php echo $p->{'purchases-payments'} ? "checked" : ''; ?>>
                                    <label for="purchases-payments" class="padding05"><?= lang('payments') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="purchases-return_purchases" class="checkbox" name="purchases-return_purchases" <?php echo $p->{'purchases-return_purchases'} ? "checked" : ''; ?>>
                                    <label for="purchases-return_purchases" class="padding05"><?= lang('return_purchases') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("transfers"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="transfers-index" <?php echo $p->{'transfers-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="transfers-add" <?php echo $p->{'transfers-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="transfers-edit" <?php echo $p->{'transfers-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="transfers-delete" <?php echo $p->{'transfers-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="transfers-email" class="checkbox" name="transfers-email" <?php echo $p->{'transfers-email'} ? "checked" : ''; ?>>
                                    <label for="transfers-email" class="padding05"><?= lang('email') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="transfers-pdf" class="checkbox" name="transfers-pdf" <?php echo $p->{'transfers-pdf'} ? "checked" : ''; ?>>
                                    <label for="transfers-pdf" class="padding05"><?= lang('pdf') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("returns"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="returns-index" <?php echo $p->{'returns-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="returns-add" <?php echo $p->{'returns-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="returns-edit" <?php echo $p->{'returns-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="returns-delete" <?php echo $p->{'returns-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="returns-email" class="checkbox" name="returns-email" <?php echo $p->{'returns-email'} ? "checked" : ''; ?>>
                                    <label for="returns-email" class="padding05"><?= lang('email') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="returns-pdf" class="checkbox" name="returns-pdf" <?php echo $p->{'returns-pdf'} ? "checked" : ''; ?>>
                                    <label for="returns-pdf" class="padding05"><?= lang('pdf') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("customers"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="customers-index" <?php echo $p->{'customers-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="customers-add" <?php echo $p->{'customers-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="customers-edit" <?php echo $p->{'customers-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="customers-delete" <?php echo $p->{'customers-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="customers-deposits" class="checkbox" name="customers-deposits" <?php echo $p->{'customers-deposits'} ? "checked" : ''; ?>>
                                    <label for="customers-deposits" class="padding05"><?= lang('deposits') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="customers-delete_deposit" class="checkbox" name="customers-delete_deposit" <?php echo $p->{'customers-delete_deposit'} ? "checked" : ''; ?>>
                                    <label for="customers-delete_deposit" class="padding05"><?= lang('delete_deposit') ?></label>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><?= lang("suppliers"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="suppliers-index" <?php echo $p->{'suppliers-index'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="suppliers-add" <?php echo $p->{'suppliers-add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="suppliers-edit" <?php echo $p->{'suppliers-edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="suppliers-delete" <?php echo $p->{'suppliers-delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("expenses"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases-expenses" <?php echo $p->{'purchases-expenses'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases_add_expense" <?php echo $p->{'purchases_add_expense'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases_edit_expense" <?php echo $p->{'purchases_edit_expense'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="purchases_delete_expense" <?php echo $p->{'purchases_delete_expense'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("stores"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_view" <?php echo $p->{'store_view'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_add" <?php echo $p->{'store_add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_edit" <?php echo $p->{'store_edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_delete" <?php echo $p->{'store_delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>   
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("product_integration"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_product_integration" <?php echo $p->{'store_product_integration'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_product_integration_add" <?php echo $p->{'store_product_integration_add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_product_integration_edit" <?php echo $p->{'store_product_integration_edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="store_product_integration_delete" <?php echo $p->{'store_product_integration_delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="store_product_integration_delete_both" class="checkbox" name="store_product_integration_delete_both" <?php echo $p->{'store_product_integration_delete_both'} ? "checked" : ''; ?>>
                                    <label for="store_product_integration_delete_both" class="padding05"><?= lang('delete_both') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="store_product_integration_recycle" class="checkbox" name="store_product_integration_recycle" <?php echo $p->{'store_product_integration_recycle'} ? "checked" : ''; ?>>
                                    <label for="store_product_integration_recycle" class="padding05"><?= lang('recycle') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="store_product_integration_report" class="checkbox" name="store_product_integration_report" <?php echo $p->{'store_product_integration_report'} ? "checked" : ''; ?>>
                                    <label for="store_product_integration_report" class="padding05"><?= lang('report') ?></label>
                                </span>
                                
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("purchase_orders"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="po_view" <?php echo $p->{'po_view'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="po_add" <?php echo $p->{'po_add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="po_edit" <?php echo $p->{'po_edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="po_delete" <?php echo $p->{'po_delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="po_receiving" class="checkbox" name="po_receiving" <?php echo $p->{'po_receiving'} ? "checked" : ''; ?>>
                                    <label for="po_receiving" class="padding05"><?= lang('receiving') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_edit_info" class="checkbox" name="po_edit_info" <?php echo $p->{'po_edit_info'} ? "checked" : ''; ?>>
                                    <label for="po_edit_info" class="padding05"><?= lang('edit_info') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_add_new_item" class="checkbox" name="po_add_new_item" <?php echo $p->{'po_add_new_item'} ? "checked" : ''; ?>>
                                    <label for="po_add_new_item" class="padding05"><?= lang('new_item') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_edit_item" class="checkbox" name="po_edit_item" <?php echo $p->{'po_edit_item'} ? "checked" : ''; ?>>
                                    <label for="po_edit_item" class="padding05"><?= lang('edit_item') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_delete_item" class="checkbox" name="po_delete_item" <?php echo $p->{'po_delete_item'} ? "checked" : ''; ?>>
                                    <label for="po_delete_item" class="padding05"><?= lang('delete_item') ?></label>
                                </span>
                                <br>
                                <span>
                                    <input type="checkbox" value="1" id="po_close" class="checkbox" name="po_close" <?php echo $p->{'po_close'} ? "checked" : ''; ?>>
                                    <label for="po_close" class="padding05"><?= lang('close') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_add_receiving" class="checkbox" name="po_add_receiving" <?php echo $p->{'po_add_receiving'} ? "checked" : ''; ?>>
                                    <label for="po_add_receiving" class="padding05"><?= lang('add_receiving') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_edit_receiving" class="checkbox" name="po_edit_receiving" <?php echo $p->{'po_edit_receiving'} ? "checked" : ''; ?>>
                                    <label for="po_edit_receiving" class="padding05"><?= lang('edit_receiving') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_delete_receiving" class="checkbox" name="po_delete_receiving" <?php echo $p->{'po_delete_receiving'} ? "checked" : ''; ?>>
                                    <label for="po_delete_receiving" class="padding05"><?= lang('delete_receiving') ?></label>
                                </span>
                                <br>
                                <span>
                                    <input type="checkbox" value="1" id="po_deactivate_product" class="checkbox" name="po_deactivate_product" <?php echo $p->{'po_deactivate_product'} ? "checked" : ''; ?>>
                                    <label for="po_deactivate_product" class="padding05"><?= lang('deactivate_product') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="po_create_invoice" class="checkbox" name="po_create_invoice" <?php echo $p->{'po_create_invoice'} ? "checked" : ''; ?>>
                                    <label for="po_create_invoice" class="padding05"><?= lang('create_invoice') ?></label>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("sale_orders"); ?></td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="so_view" <?php echo $p->{'so_view'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="so_add" <?php echo $p->{'so_add'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="so_edit" <?php echo $p->{'so_edit'} ? "checked" : ''; ?>>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1" class="checkbox" name="so_delete" <?php echo $p->{'so_delete'} ? "checked" : ''; ?>>
                            </td>
                            <td>
                                <span>
                                    <input type="checkbox" value="1" id="so_edit_info" class="checkbox" name="so_edit_info" <?php echo $p->{'so_edit_info'} ? "checked" : ''; ?>>
                                    <label for="so_edit_info" class="padding05"><?= lang('edit_info') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="so_cancel" class="checkbox" name="so_cancel" <?php echo $p->{'so_cancel'} ? "checked" : ''; ?>>
                                    <label for="so_cancel" class="padding05"><?= lang('cancel') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="so_add_new_item" class="checkbox" name="so_add_new_item" <?php echo $p->{'so_add_new_item'} ? "checked" : ''; ?>>
                                    <label for="so_add_new_item" class="padding05"><?= lang('new_item') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="so_edit_item" class="checkbox" name="so_edit_item" <?php echo $p->{'so_edit_item'} ? "checked" : ''; ?>>
                                    <label for="so_edit_item" class="padding05"><?= lang('edit_item') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="so_delete_item" class="checkbox" name="so_delete_item" <?php echo $p->{'so_delete_item'} ? "checked" : ''; ?>>
                                    <label for="so_delete_item" class="padding05"><?= lang('delete_item') ?></label>
                                </span>
                                <br>
                                <span>
                                    <input type="checkbox" value="1" id="so_complete_item" class="checkbox" name="so_complete_item" <?php echo $p->{'so_complete_item'} ? "checked" : ''; ?>>
                                    <label for="so_complete_item" class="padding05"><?= lang('complete_item') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="so_delete_complete_item" class="checkbox" name="so_delete_complete_item" <?php echo $p->{'so_delete_complete_item'} ? "checked" : ''; ?>>
                                    <label for="so_delete_complete_item" class="padding05"><?= lang('delete_complete_item') ?></label>
                                </span>                                      
                                <span>
                                    <input type="checkbox" value="1" id="so_create_invoice" class="checkbox" name="so_create_invoice" <?php echo $p->{'so_create_invoice'} ? "checked" : ''; ?>>
                                    <label for="so_create_invoice" class="padding05"><?= lang('create_invoice') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" id="so_dispatch" class="checkbox" name="so_dispatch" <?php echo $p->{'so_dispatch'} ? "checked" : ''; ?>>
                                    <label for="so_dispatch" class="padding05"><?= lang('dispatch') ?></label>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("new_reports"); ?></td>
                            <td colspan="5">
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="batchwise_report" name="batchwise_report" <?php echo $p->{'batchwise_report'} ? "checked" : ''; ?>>
                                    <label for="batchwise_report" class="padding05"><?= lang('batchwise_report') ?></label>
                                </span>
                                
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="batchwise_price_report" name="batchwise_price_report" <?php echo $p->{'batchwise_price_report'} ? "checked" : ''; ?>>
                                    <label for="batchwise_price_report" class="padding05"><?= lang('batchwise_price_report') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="report_sales_summary" name="report_sales_summary" <?php echo $p->{'report_sales_summary'} ? "checked" : ''; ?>>
                                    <label for="report_sales_summary" class="padding05"><?= lang('sales_summary') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="sales" name="reports-sales" <?php echo $p->{'reports-sales'} ? "checked" : ''; ?>>
                                    <label for="sales" class="padding05">Sale Items Wise Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="dc_report" name="dc_report" <?php echo $p->{'dc_report'} ? "checked" : ''; ?>>
                                    <label for="dc_report" class="padding05">DC Report</label>
                                </span>
                                <br>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="short_expiry_stock" name="short_expiry_stock" <?php echo $p->{'short_expiry_stock'} ? "checked" : ''; ?>>
                                    <label for="short_expiry_stock" class="padding05">Short Expiry Stock Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="ces_stock" name="ces_stock" <?php echo $p->{'ces_stock'} ? "checked" : ''; ?>>
                                    <label for="ces_stock" class="padding05">Cross Expacted Stock Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="expiry_stock" name="expiry_stock" <?php echo $p->{'expiry_stock'} ? "checked" : ''; ?>>
                                    <label for="expiry_stock" class="padding05">Expired Stock Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="old_stock" name="old_stock" <?php echo $p->{'old_stock'} ? "checked" : ''; ?>>
                                    <label for="old_stock" class="padding05">Old Stock Report</label>
                                </span>
                                <br>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="so_items_wise" name="so_items_wise" <?php echo $p->{'so_items_wise'} ? "checked" : ''; ?>>
                                    <label for="so_items_wise" class="padding05">SO Items Wise Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="monthly_items_demand" name="monthly_items_demand" <?php echo $p->{'monthly_items_demand'} ? "checked" : ''; ?>>
                                    <label for="monthly_items_demand" class="padding05">Monthly Items Demand Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="credit_report" name="credit_report" <?php echo $p->{'credit_report'} ? "checked" : ''; ?>>
                                    <label for="credit_report" class="padding05">Credit Report</label>
                                </span>
                                <br>
                                 <span>
                                    <input type="checkbox" value="1" class="checkbox" id="due_invoice" name="due_invoice" <?php echo $p->{'due_invoice'} ? "checked" : ''; ?>>
                                    <label for="due_invoice" class="padding05">Due Invoice Report</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="product_ledger" name="product_ledger" <?php echo $p->{'product_ledger'} ? "checked" : ''; ?>>
                                    <label for="product_ledger" class="padding05">Product Ledger</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="customers_ledger" name="customers_ledger" <?php echo $p->{'customers_ledger'} ? "checked" : ''; ?>>
                                    <label for="customers_ledger" class="padding05">Customers Ledger</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="customers_wht_ledger" name="customers_wht_ledger" <?php echo $p->{'customers_wht_ledger'} ? "checked" : ''; ?>>
                                    <label for="customers_wht_ledger" class="padding05">Customers WHT Ledger</label>
                                </span>
                                <br>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="legder_summary" name="legder_summary" <?php echo $p->{'legder_summary'} ? "checked" : ''; ?>>
                                    <label for="legder_summary" class="padding05">Legder Summary</label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="supplier_ledger" name="supplier_ledger" <?php echo $p->{'supplier_ledger'} ? "checked" : ''; ?>>
                                    <label for="supplier_ledger" class="padding05">Supplier Ledger</label>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang("misc"); ?></td>
                            <td colspan="5">
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="bulk_actions"
                                    name="bulk_actions" <?php echo $p->bulk_actions ? "checked" : ''; ?>>
                                    <label for="bulk_actions" class="padding05"><?= lang('bulk_actions') ?></label>
                                </span>
                                <span>
                                    <input type="checkbox" value="1" class="checkbox" id="edit_price"
                                    name="edit_price" <?php echo $p->edit_price ? "checked" : ''; ?>>
                                    <label for="edit_price" class="padding05"><?= lang('edit_price_on_sale') ?></label>
                                </span>
                            </td>
                        </tr>

                        </tbody>
                    </table>



                    <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light submitbtn" >Submit</button>
                        </div>

                    <?php echo form_close(); ?>

                </div>


            </div>
        </div>
    </div>
</div>
<div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <button class="md-fab md-fab-success md-fab-wave waves-effect waves-button addbtn" type="button"><i class="fa-solid fa-plus"></i></button>
</div>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('.submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/system_settings/permissions_submit/'.$id); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.status){
                        toastr.success(obj.message);
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('.submitbtn').prop('disabled', false);
                }
            });
        });
    });
</script>