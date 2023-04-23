<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Bulk Integration</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <h1>Total: <span id="total" >0</span></h1>
                <h1>Complete: <span id="complete" >0</span></h1>
                <input type="button" value="Update Store" id="submitbutton" >
            </div>
        </div>
    </div>
</div>

<script>
    var index = 0;
    var total_index = 0;
    var products = [];
    $('#submitbutton').click(function(){
        $.ajax({
            url: '<?php echo base_url('testing/products'); ?>',
            type: 'GET',
            data: {},
            success: function(data) {
                var obj = jQuery.parseJSON(data);
                total_index = obj.length-1;
                $('#total').html(total_index);
                console.log(total_index);
                products = obj
                sendrequest();
            }
        });

    });
    function sendrequest(){
        // console.log(products[index].id);
        $.ajax({
            url: '<?php echo base_url('testing/product_update'); ?>',
            type: 'GET',
            data: {pid:products[index].id},
            success: function(data) {
                index++;
                $('#complete').html(index);
                if(index <= total_index){
                    sendrequest();
                }
            }
        });
    }
</script>

