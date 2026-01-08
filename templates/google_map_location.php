<?php 
/**
 *
 * Google Map Location
 * 
 **/
/* function webcu_google_map_function(){

    $webcu_street = get_post_meta($post->ID, 'webcu_ve_street', true);
    $webcu_city = get_post_meta($post->ID, 'webcu_ve_city', true);
    $webcu_state = get_post_meta($post->ID, 'webcu_ve_state', true);
    $webcu_postcode = get_post_meta($post->ID, 'webcu_ve_postcocde', true);
    $webcu_gmp_api = get_post_meta($post->ID, 'webcu_googleMap_Api', true);


    $address = $webcu_street.','.$webcu_city.','.$webcu_state.','.$webcu_postcode;  //"Road 13, Sector 10, House 77, Uttara, Dhaka 1230";
    $apiKey  = $webcu_gmp_api;

    $encodedAddress = urlencode($address);
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
    }
    ?>

    <div id="map" style="height:400px;width:100%;"></div>

    <script>
    function initMap() {
        const locations = <?php echo json_encode($locations); ?>;

        if (!locations.length) return;

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: {
                lat: parseFloat(locations[0].lat),
                lng: parseFloat(locations[0].lng)
            }
        });

        locations.forEach(loc => {
            new google.maps.Marker({
                position: {
                    lat: parseFloat(loc.lat),
                    lng: parseFloat(loc.lng)
                },
                map: map,
                title: loc.title
            });
        });
    }
    </script>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $webcu_gmp_api ?>&callback=initMap">
    </script>
<?php 
}
add_shortcode( 'gmap_link', 'webcu_google_map_function' );
 */


function webcu_google_map_function() {

    global $post;

    if ( ! $post ) {
        return '';
    }

    $street   = get_post_meta( $post->ID, 'webcu_ve_street', true );
    $city     = get_post_meta( $post->ID, 'webcu_ve_city', true );
    $state    = get_post_meta( $post->ID, 'webcu_ve_state', true );
    $postcode = get_post_meta( $post->ID, 'webcu_ve_postcocde', true );
    $api_key  = get_post_meta( $post->ID, 'webcu_googleMap_Api', true );

    if ( empty( $api_key ) ) {
        return '';
    }

    $address = implode( ', ', array_filter( array(
        $street,
        $city,
        $state,
        $postcode
    ) ) );

    $geocode_url = add_query_arg(
        array(
            'address' => urlencode( $address ),
            'key'     => $api_key,
        ),
        'https://maps.googleapis.com/maps/api/geocode/json'
    );

    $response = wp_remote_get( $geocode_url );

    if ( is_wp_error( $response ) ) {
        return '';
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( empty( $data['results'][0]['geometry']['location'] ) ) {
        return '';
    }

    $lat = $data['results'][0]['geometry']['location']['lat'];
    $lng = $data['results'][0]['geometry']['location']['lng'];

    $map_id = 'webcu-map-' . esc_attr( $post->ID );

    ob_start();
    ?>

    <div id="<?php echo esc_attr( $map_id ); ?>" style="height:400px;width:100%;"></div>

    <script>
    function webcuInitMap<?php echo esc_js( $post->ID ); ?>() {
        const map = new google.maps.Map(
            document.getElementById('<?php echo esc_js( $map_id ); ?>'),
            {
                zoom: 15,
                center: { lat: <?php echo esc_js( $lat ); ?>, lng: <?php echo esc_js( $lng ); ?> }
            }
        );

        new google.maps.Marker({
            position: { lat: <?php echo esc_js( $lat ); ?>, lng: <?php echo esc_js( $lng ); ?> },
            map: map,
            title: 'My Event Location'
        });
    }
    </script>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( $api_key ); ?>&callback=webcuInitMap<?php echo esc_attr( $post->ID ); ?>">
    </script>

    <?php
    return ob_get_clean();

    /* <div id="map" style="height:400px;width:100%;"></div> */
}

add_shortcode( 'gmap_link', 'webcu_google_map_function' );