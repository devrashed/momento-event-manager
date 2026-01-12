
<?php

class webcu_gmap_Info_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'contact_info_widget', // Base ID
            'Contact Info Widget', // Name
            array( 'description' => 'A simple widget to display contact information.' )

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

          $street = $instance['address'];
          $city = $instance['city'];
          $state = $instance['state'];
          $zip = $instance['zip'];
          $country = $instance['country'];
          $gmap = $instance['gmap'];

             $address = implode( ', ', array_filter( array(
                $street,
                $city,
                $state,
                $zip,
                $country
            ) ) );

            $apiKey = $gmap;

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
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDNT_-1pQwPcxZ1wyXj3uIREJJVq-Z-sx8&callback=initMap">
            </script>

        </div>

        <?php echo $args['after_widget'];}
        
     public function form($instance){$zip=isset($instance['zip'])?$instance['zip']:'';
     $city=isset($instance['city'])?$instance['city']:'';
     $state=isset($instance['state'])?$instance['state']:'';
     $address=isset($instance['address'])?$instance['address']:''; 
     $country=isset($instance['country'])?$instance['country']:'';
     $gmap=isset($instance['gmap'])?$instance['gmap']:''; ?>


        <p>
            <label for="<?php echo $this->get_field_id('address'); ?>">Streat:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" type="text" value="<?php echo esc_attr($address); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('city'); ?>">city:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" type="text" value="<?php echo esc_attr($city); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('state'); ?>">State:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('state'); ?>" name="<?php echo $this->get_field_name('state'); ?>" type="text" value="<?php echo esc_attr($state); ?>" />

        </p>

        <p>
            <label for="<?php echo $this->get_field_id('zip'); ?>">zip:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('zip'); ?>" name="<?php echo $this->get_field_name('zip'); ?>" type="text" value="<?php echo esc_attr($zip); ?>" />

        </p>


        <p>
            <label for="<?php echo $this->get_field_id('country'); ?>">country:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('country'); ?>" name="<?php echo $this->get_field_name('country'); ?>" type="text" value="<?php echo esc_attr($country); ?>" />

        </p>

        <p>
            <label for="<?php echo $this->get_field_id('gmap'); ?>">Google Map APi Key:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('gmap'); ?>" name="<?php echo $this->get_field_name('gmap'); ?>" type="text" value="<?php echo esc_attr($gmap); ?>" />

        </p>

        <?php 
    }
    public function update($new_instance,$old_instance){
        $instance=$old_instance;
        $instance['city']=strip_tags($new_instance['city']);
        $instance['zip']=strip_tags($new_instance['zip']);
        $instance['state']=strip_tags($new_instance['state']);
        $instance['address']=strip_tags($new_instance['address']);
        $instance['country']=strip_tags($new_instance['country']);      
        $instance['gmap']=strip_tags($new_instance['gmap']);                
        return $instance;
    }

}

