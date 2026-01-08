
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

        $address = "Road 13, Sector 10, House 77, Uttara, Dhaka 1230";
        $apiKey  = "AIzaSyDNT_-1pQwPcxZ1wyXj3uIREJJVq-Z-sx8";

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
    <?php

    }
}

/*  public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {

            echo $args['before_title'] . $instance['title'] . $args['after_title'];

        }

        ?>

        <div class="contact-info">

            <p><strong>Phone:</strong> <?php echo esc_html($instance['phone']); ?></p>

            <p><strong>Email:</strong> <?php echo esc_html($instance['email']); ?></p>

            <p><strong>Address:</strong> <?php echo esc_html($instance['address']); ?></p>

        </div>

        <?php echo $args['after_widget'];}public function form($instance){$title=isset($instance['title'])?$instance['title']:'';$phone=isset($instance['phone'])?$instance['phone']:'';$email=isset($instance['email'])?$instance['email']:'';$address=isset($instance['address'])?$instance['address']:''; ?>

        <p>

            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>

            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />

        </p>

        <p>

            <label for="<?php echo $this->get_field_id('phone'); ?>">Phone:</label>

            <input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="text" value="<?php echo esc_attr($phone); ?>" />

        </p>

        <p>

            <label for="<?php echo $this->get_field_id('email'); ?>">Email:</label>

            <input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo esc_attr($email); ?>" />

        </p>

        <p>

            <label for="<?php echo $this->get_field_id('address'); ?>">Address:</label>

            <input class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" type="text" value="<?php echo esc_attr($address); ?>" />

        </p>

        <?php 
    } 
    public function update($new_instance,$old_instance){
        $instance=$old_instance;
        $instance['title']=strip_tags($new_instance['title']);
        $instance['phone']=strip_tags($new_instance['phone']);
        $instance['email']=strip_tags($new_instance['email']);
        $instance['address']=strip_tags($new_instance['address']);
        return $instance;
    } */


