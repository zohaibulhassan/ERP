<style>
    .md-list-addon>li {
        margin-left:0px;
    }
    b.in {
        color:green;
    }
    b.out {
        color:red;
    }

    .gmap_list i {
        /* color:#a30745; */
        /* color:#035f71; */
        color:#00aca1;
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Users Tracker</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="uk-grid uk-grid-medium" data-uk-grid-margin style="height: 400px;">
                    <div class="uk-width-medium-3-10 uk-row-first">
                        <ul class="md-list md-list-addon gmap_list" id="map_users_list" style="height: 400px;overflow-y: auto;">
                            <?php
                                foreach($trackers as $tracker){
                                    ?>
                                    <li data-gmap-lat="<?php echo $tracker->gps_latitude ?>"  data-gmap-lon="<?php echo $tracker->gps_longitude ?>" data-gmap-user="
                                        <?php
                                            if($tracker->type == 1){
                                                echo 'Attendance ';
                                            }
                                            else if($tracker->type == 2){
                                                echo 'Route ';
                                            }
                                            else if($tracker->type == 3){
                                                echo 'Shop ';
                                            }
                                            if($tracker->status == 1){
                                                echo 'Punch In';
                                            }
                                            else if($tracker->status == 0){
                                                echo 'Punch Out';
                                            }
                                        ?>
                                    " data-gmap-user-company="Battery Level: <?php echo $tracker->battery?>%">
                                        <div class="md-list-content">
                                            <span class="md-list-heading">
                                                <?php
                                                    if($tracker->type == 1){
                                                        echo 'Attendance ';
                                                    }
                                                    else if($tracker->type == 2){
                                                        echo 'Route ';
                                                    }
                                                    else if($tracker->type == 3){
                                                        echo 'Shop ';
                                                    }
                                                    if($tracker->status == 1){
                                                        echo '<b class="in" >Punch In</b>';
                                                    }
                                                    else if($tracker->status == 0){
                                                        echo '<b class="out">Punch Out</b>';
                                                    }
                                                ?>
                                            </span>
                                            <span class="uk-text-small uk-text-muted"><i class="fa-solid fa-clock"></i> <?php echo $tracker->created_at?></span>
                                            <span class="uk-text-small uk-text-muted"><i class="fa-solid fa-battery-three-quarters"></i> <?php echo $tracker->battery?>%</span>
                                        </div>
                                    </li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>
                    <div class="uk-width-medium-7-10 uk-row-first">
                        <div id="map_users" style="height:100%" ></div>
                    </div>
                </div>    




            </div>
        </div>


    </div>
</div>
<!-- maplace (google maps) -->
<script src="https://maps.google.com/maps/api/js?key=AIzaSyAxoTbY8_b8Pbmy9Q3woI2gs624zrN22g0"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/maplace-js/dist/maplace.min.js"></script>
<script>
    if ($("#map_users").length) {
        var e = $("#map_users_list").children("li"),
            a = "assets/img/md-images/ic_place_black.png",
            i = new google.maps.Size(24, 24),
            n = new google.maps.Size(24, 24),
            r = [];
        e.each(function () {
            var e = $(this),
                t = {
                    lat: e.attr("data-gmap-lat"),
                    lon: e.attr("data-gmap-lon"),
                    title: e.attr("data-gmap-user"),
                    html: '<div class="gmap-info-window"><h3 class="uk-text-nowrap">' + e.attr("data-gmap-user") + "</h3><p>" + e.attr("data-gmap-user-company") + "</p></div>",
                    zoom: 16,
                    icon: { url: a, size: i, scaledSize: n },
                };
            r.push(t);
        });
        var s = new Maplace({
            map_div: "#map_users",
            locations: r,
            controls_on_map: !1,
            map_options: { set_center: [24.867282, 67.081411], zoom: 16 },
        }).Load();
        e.on("click", function (e) {
            e.preventDefault();
            var t = $(this),
                a = t.index();
            t.addClass("md-list-item-active").siblings().removeClass("md-list-item-active"), google.maps.event.trigger(s.markers[a], "click");
        }),
            $(window).on("debouncedresize", function () {
                var e = s.oMap;
                google.maps.event.trigger(e, "resize"), e.fitBounds(s.oBounds);
            });
    }

</script>