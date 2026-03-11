
<?php 
    function google_map_location() {
?>
        <div class="container">	
                <h3><?php echo esc_html('Event Location on google Map', 'mega_events_manager')?> </h3>
                
            <?php       
                $post_id= get_the_ID(); 

                $address=get_post_meta($post_id,'wtmem_ve_street',true);
                $city=get_post_meta($post_id,'wtmem_ve_city',true);
                $state=get_post_meta($post_id,'wtmem_ve_state',true);
                $zip=get_post_meta($post_id,'wtmem_ve_postcocde',true);
                $country=get_post_meta($post_id,'wtmem_ve_country',true);

                $apiKey=get_option('google_map_api');

                $full_address = implode( ', ', array_filter( array(
                    $address,
                    $city,
                    $state,
                    $zip,
                    $country
                ) ) );  
                
                $encodedAddress = urlencode($full_address);
                $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&key={$apiKey}";      
                $response = file_get_contents($geocodeUrl);
                $data = json_decode($response, true);
                $locations = [];
                if ($data['status'] === 'OK') {
                    $lat = $data['results'][0]['geometry']['location']['lat'];
                    $lng = $data['results'][0]['geometry']['location']['lng'];
                    $locations[] = [
                        'title' => 'My Event Location',
                        'lat'   => $lat,
                        'lng'   => $lng,
                    ];
                } else {
                    echo "Geocoding failed: " . $data['status'];
                }

            if ( ! empty( $locations ) ) {  

                ?>

                 

                <div id="map" style="height: 400px; width: 100%;"></div>
                 
                

                <script>

                    function initMap() {
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 12,
                            center: {lat: <?php echo $locations[0]['lat']; ?>, lng: <?php echo $locations[0]['lng']; ?>}
                        });

                        <?php foreach ( $locations as $location ) : ?>
                        var marker = new google.maps.Marker({
                            position: {lat: <?php echo $location['lat']; ?>, lng: <?php echo $location['lng']; ?>},
                            map: map,
                            title: '<?php echo esc_js( $location['title'] ); ?>'
                        });
                        <?php endforeach; ?>
                    }
                    
                </script>
                <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap">
                </script>
                <?php
            }
        ?>
        </div>   
    <?php   
    
    }    

?>                   