    
    <div class="uk-modal" id="modal_ajax">
        <div class="uk-modal-dialog">
        </div>
    </div>
    
    
    
    <!-- uikit functions -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>js/uikit_custom.min.js"></script>
    <!-- altair common functions/helpers -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>js/altair_admin_common.min.js"></script>
    <!-- select2 -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/select2/dist/js/select2.min.js"></script>
    <!-- Sweet Alert -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/sweetalert2/dist/js/sweetalert2.min.js"></script>
    <!--  notifications functions -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/toastr/toastr.min.js"></script>

    
    <script>
        $(function() {
            var $switcher = $('#style_switcher'),
                $switcher_toggle = $('#style_switcher_toggle'),
                $theme_switcher = $('#theme_switcher'),
                $mini_sidebar_toggle = $('#style_sidebar_mini'),
                $slim_sidebar_toggle = $('#style_sidebar_slim'),
                $boxed_layout_toggle = $('#style_layout_boxed'),
                $accordion_mode_toggle = $('#accordion_mode_main_menu'),
                $html = $('html'),
                $body = $('body');
            $switcher_toggle.click(function(e) {
                e.preventDefault();
                $switcher.toggleClass('switcher_active');
            });
            $theme_switcher.children('li').click(function(e) {
                e.preventDefault();
                var $this = $(this),
                    this_theme = $this.attr('data-app-theme');

                $theme_switcher.children('li').removeClass('active_theme');
                $(this).addClass('active_theme');
                $html
                    .removeClass('app_theme_a app_theme_b app_theme_c app_theme_d app_theme_e app_theme_f app_theme_g app_theme_h app_theme_i app_theme_dark')
                    .addClass(this_theme);

                if(this_theme == '') {
                    localStorage.removeItem('altair_theme');
                    $('#kendoCSS').attr('href','<?php echo base_url('themes/v1/assets/'); ?>bower_components/kendo-ui/styles/kendo.material.min.css');
                } else {
                    localStorage.setItem("altair_theme", this_theme);
                    if(this_theme == 'app_theme_dark') {
                        $('#kendoCSS').attr('href','<?php echo base_url('themes/v1/assets/'); ?>bower_components/kendo-ui/styles/kendo.materialblack.min.css')
                    } else {
                        $('#kendoCSS').attr('href','<?php echo base_url('themes/v1/assets/'); ?>bower_components/kendo-ui/styles/kendo.material.min.css');
                    }
                }

            });
            // hide style switcher
            $document.on('click keyup', function(e) {
                if( $switcher.hasClass('switcher_active') ) {
                    if (
                        ( !$(e.target).closest($switcher).length )
                        || ( e.keyCode == 27 )
                    ) {
                        $switcher.removeClass('switcher_active');
                    }
                }
            });
            // get theme from local storage
            if(localStorage.getItem("altair_theme") !== null) {
                $theme_switcher.children('li[data-app-theme='+localStorage.getItem("altair_theme")+']').click();
            }
            // toggle mini sidebar
            // change input's state to checked if mini sidebar is active
            if((localStorage.getItem("altair_sidebar_mini") !== null && localStorage.getItem("altair_sidebar_mini") == '1') || $body.hasClass('sidebar_mini')) {
                $mini_sidebar_toggle.iCheck('check');
            }
            $mini_sidebar_toggle.on('ifChecked', function(event){
                $switcher.removeClass('switcher_active');
                localStorage.setItem("altair_sidebar_mini", '1');
                localStorage.removeItem('altair_sidebar_slim');
                location.reload(true);
            })
            .on('ifUnchecked', function(event){
                $switcher.removeClass('switcher_active');
                localStorage.removeItem('altair_sidebar_mini');
                location.reload(true);
            });
            // toggle slim sidebar
            // change input's state to checked if mini sidebar is active
            if((localStorage.getItem("altair_sidebar_slim") !== null && localStorage.getItem("altair_sidebar_slim") == '1') || $body.hasClass('sidebar_slim')) {
                $slim_sidebar_toggle.iCheck('check');
            }
            $slim_sidebar_toggle
                .on('ifChecked', function(event){
                    $switcher.removeClass('switcher_active');
                    localStorage.setItem("altair_sidebar_slim", '1');
                    localStorage.removeItem('altair_sidebar_mini');
                    location.reload(true);
                })
                .on('ifUnchecked', function(event){
                    $switcher.removeClass('switcher_active');
                    localStorage.removeItem('altair_sidebar_slim');
                    location.reload(true);
                });
            // toggle boxed layout
            if((localStorage.getItem("altair_layout") !== null && localStorage.getItem("altair_layout") == 'boxed') || $body.hasClass('boxed_layout')) {
                $boxed_layout_toggle.iCheck('check');
                $body.addClass('boxed_layout');
                $(window).resize();
            }

            $boxed_layout_toggle.on('ifChecked', function(event){
                $switcher.removeClass('switcher_active');
                localStorage.setItem("altair_layout", 'boxed');
                location.reload(true);
            })
            .on('ifUnchecked', function(event){
                $switcher.removeClass('switcher_active');
                localStorage.removeItem('altair_layout');
                location.reload(true);
            });
            // main menu accordion mode
            if($sidebar_main.hasClass('accordion_mode')) {
                $accordion_mode_toggle.iCheck('check');
            }
            $accordion_mode_toggle.on('ifChecked', function(){
                $sidebar_main.addClass('accordion_mode');
            })
            .on('ifUnchecked', function(){
                $sidebar_main.removeClass('accordion_mode');
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            var showDateTimey = <?= date('Y') ?>;
            var showDateTimem = <?= date('m') ?>;
            var showDateTimed = <?= date('d') ?>;
            var showDateTimeH = <?= date('H') ?>;
            var showDateTimei = <?= date('i') ?>;
            var showDateTimes = <?= date('s') ?>;
            var showDateTime = showDateTimey+"-"+showDateTimem+"-"+showDateTimed+" "+showDateTimeH+":"+showDateTimei+":"+showDateTimes;
            setInterval(function () {
                showDateTimes++;
                if(showDateTimes > 60){
                    showDateTimes = 0;
                    showDateTimei++;
                    if(showDateTimei > 60){
                        showDateTimei = 0;
                        showDateTimeH++;
                    }
                }
                showDateTime = decimalTime(showDateTimey)+"-"+decimalTime(showDateTimem)+"-"+decimalTime(showDateTimed)+" "+decimalTime(showDateTimeH)+":"+decimalTime(showDateTimei)+":"+decimalTime(showDateTimes);
                $('#CurentDateTimeShow').html(showDateTime);
            }, 1000);
            function decimalTime(no){
                if(no<10){
                    no = "0"+no;                    
                }
                return no;

            }
            loadHodBill();
            $('#holdbillnumber').keyup(function(){
                loadHodBill();
            });

        });
        var countbill = 1;
        function loadHodBill(){
            var holdNo = $('#holdbillnumber').val();
            $.ajax({
                url: '<?php echo base_url('admin/pos/hold_bill_list'); ?>',
                type: 'GET',
                data: {holdno:holdNo},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    $('.holddiv').html(obj.html);
                    if(countbill == 1){
                        $('#holdCount').html(obj.count);
                        countbill = 0;
                    }
                }
            });
        }
        $('input:not([search])').keydown(function(e){
            if(e.keyCode === 13) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
