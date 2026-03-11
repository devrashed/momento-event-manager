<?php
/**
 *
 *  Wpcraft widget
 *
 **/
namespace Wpcraft\Widget;


class wtmem_gmap_Info_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'google_map_widget', // Base ID
            __('Google Map Widget', 'momento-event-manager'),
            array(
                'description' => __('A simple widget to display Google Map only use Event sidebar', 'momento-event-manager'),
            )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {

            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        ?>

    <div class="contact-info">
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

    <?php echo $args['after_widget']; 
    }

    public function form($instance){
     
        $title=isset($instance['title'])?$instance['title']:'';
            ?>

            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">Caption:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>

        <?php 
    
    } 

    public function update($new_instance, $old_instance){
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}