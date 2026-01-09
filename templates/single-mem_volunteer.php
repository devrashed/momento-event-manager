<?php get_header(); ?>


<?php $webcu_voltem = get_option('webcu_volunteer_template'); 
 
if ( "right" === $webcu_voltem ){

?>

<div class="webcu-container">

        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                $webcu_profile = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                $webcu_image_id  = get_post_meta( $post->ID, '_event_volunteer_image_id', true );
                $webcu_herobanner = $webcu_image_id ? wp_get_attachment_url( $webcu_image_id ) : '';
        ?>

            <?php if ( $webcu_herobanner ) : ?>
              <div class="top-header" style="background-image:url('<?php echo esc_attr($webcu_herobanner); ?>');object-fit: cover; background-repeat: round;">

                 <div class="top_heading"> <?php the_title(); ?> </div>
                
                <div class="top_container"> 
                    <div class="flexbox_one"> 
                       <div class="org_logo" style="background-image:url('<?php echo esc_attr($webcu_profile); ?>'); background-size: cover;"> <img src=""></div>                                    
                   </div>
                    <div class="flexbox_two">

                        <div class="org_name"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_name', true )); ?> </div>
                        <div class="org_desig"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_spon_desig', true )); ?> </div>
                 
                    </div>

                    <div class="flexbox_three"> 
                              
                            <span class="org_addr"> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_street', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_city', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_state', true )); ?> 
                            </span>  
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_phone', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_email', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_website', true )); ?>  </div>  
                            <span class="org_info">  

                              <ul>
                                   <?php $webcu_social_link = get_post_meta(get_the_ID(), 'webcu_volun_extras', true); ?> 
                                    <?php foreach ($webcu_social_link as $webcu_item): 
                                    
                                        if('facebook'=== $webcu_item['volun_social_media'] ){
                                        ?>    
                                            <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-facebook"></i> </a><li>
                                        <?php    
                                        }elseif('linkedin'=== $webcu_item['volun_social_media'] ) {
                                        ?>
                                          <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i> </a><li>   
                                            
                                        <?php    
                                        }elseif('X'=== $webcu_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-x-twitter"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('instagram'=== $webcu_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-instagram"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('pinterest'=== $webcu_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-pinterest"></i>  </a><li>   
                                        
                                        <?php    
                                        }elseif('tiktok'=== $webcu_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-tiktok"></i> </a><li>   
                                        
                                        <?php } ?>

                                    <?php endforeach; ?>

                              </ul>

                            </span>  

                    </div>   

                </div>
            </div>
            <?php endif; ?>

        <?php
            endwhile;
        endif;
        ?> 

    <div class="webcu-wrapper">

        <main class="webcu-content">

            <?php
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();
                ?>
                <h1 class="webcu-title"><?php the_title(); ?></h1>
                    <div class="webcu-description">
                        <?php the_content(); ?>
                    </div>
                <?php
                    endwhile;
                endif;
            ?>

        <h3> <?php echo esc_html__('Photo Gallery:', 'ultimate-event-manager') ?> </h3>     

        <?php 
            $webcu_gallery_ids = get_post_meta($post->ID, '_volenteer_gallery_ids', true);
                if (!empty($webcu_gallery_ids)) {
                    $webcu_ids = explode(',', $webcu_gallery_ids);
                    foreach ($webcu_ids as $id) {
                        $webcu_image = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($webcu_image) {
                        echo '<img src="' . esc_url($webcu_image[0]) . '" />';
                
                    }
                }
            }        
        ?>
        <br>
        <br>    
        <h3> <?php echo esc_html__('Video Gallery:', 'ultimate-event-manager') ?> </h3>     
        
        <?php

        
         $webcu_ownvideo = get_post_meta($post->ID, '_webcu_video_type', true);
         $webcu_youvideo = get_post_meta($post->ID, '_webcu_youtube_url', true);
         $webcu_vimeovieo = get_post_meta($post->ID, '_webcu_vimeo_url', true);   

         $webcu_youvideo_embed = str_replace("watch?v=", "embed/", $webcu_youvideo);

         $webcu_ext = !empty($webcu_ownvideo) ? strtolower(pathinfo($webcu_ownvideo, PATHINFO_EXTENSION)) : '';

         if ( !empty($webcu_youvideo) && strpos($webcu_youvideo, 'youtube') !== false ) {
        ?>
        
        <iframe width="560" height="315" 
            src="<?php echo esc_url($webcu_youvideo_embed); ?>" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen>
       </iframe>  

       
        <?php 
        } elseif ( !empty($webcu_vimeovieo) && is_numeric($webcu_vimeovieo) ) {

        $webcu_vimeovieo = get_post_meta($post->ID, '_webcu_vimeo_url', true);
        $webcu_video_id = preg_replace('/[^0-9]/', '', $webcu_vimeovieo);
        $webcu_vimeo_embed = "https://player.vimeo.com/video/" . $webcu_video_id;
        ?>

        <iframe 
            width="460" 
            height="315"
            src="<?php echo esc_url($webcu_vimeo_embed); ?>" 
            frameborder="0" 
            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
        ></iframe>

        
        <!-- <script src="https://player.vimeo.com/api/player.js"></script> -->
        
 
       <?php 
        } elseif ( ! empty( $webcu_ownvideo ) ) {
            $webcu_ownvideo = get_post_meta( $post->ID, '_webcu_video_type', true );
            $webcu_filetype = wp_check_filetype( $webcu_ownvideo );
            $webcu_mime_type      = $webcu_filetype['type'];

            if ( ! empty( $webcu_mime_type ) && 0 === strpos( $webcu_mime_type, 'video/' ) ) :
                ?>
                <video width="560" height="315" controls>
                    <source
                        src="<?php echo esc_url( $webcu_ownvideo ); ?>"
                        type="<?php echo esc_attr( $webcu_mime_type ); ?>">
                </video>
                <?php
            endif;
        }
    
        $webcu_video_type = get_post_meta( $post->ID, '_webcu_volun_video_type', true );
        $webcu_youtube_url  = get_post_meta( $post->ID, '_webcu_volun_youtube_url', true );
        $webcu_vimeo_url    = get_post_meta( $post->ID, '_webcu_volun_vimeo_url', true );
        $webcu_video_id = get_post_meta( $post->ID, '_webcu_volun_own_video_id', true );
        $webcu_video_url    = $webcu_video_id ? wp_get_attachment_url( $webcu_video_id ) : '';

        $webcu_youvideo_embed = str_replace("watch?v=", "embed/", $webcu_youtube_url);

        if ( 'youtube' === $webcu_video_type ) {
        ?>
        
        <iframe width="560" height="315" 
            src="<?php echo esc_url($webcu_youvideo_embed); ?>" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen>
       </iframe>  

       <?php 
        } elseif ( 'vimeo' === $webcu_video_type ) {

        $webcu_vimeovieo = get_post_meta($post->ID, '_webcu_volun_vimeo_url', true);
        $webcu_video_id = preg_replace('/[^0-9]/', '', $webcu_vimeovieo);
        $webcu_vimeo_embed = "https://player.vimeo.com/video/" . $webcu_video_id;
        ?>

        <iframe 
            width="460" 
            height="315"
            src="<?php echo esc_url($webcu_vimeo_embed); ?>" 
            frameborder="0" 
            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
        ></iframe>

        
        <?php
        } elseif ('webcu_ownvideo' === $webcu_video_type ) {
            
            $webcu_ownvideo = get_post_meta($post->ID, '_webcu_volun_own_video_id', true);
            $webcu_image_url = wp_get_attachment_url( $webcu_ownvideo );  
            ?>
            <video width="560" height="315" controls> 
            <source src="<?php echo esc_url($webcu_image_url); ?>">
            </video> 

       <?php } ?> 

        
        </main>

        <!-- Sidebar -->
        <aside class="webcu-sidebar">

        <?php 
            if ( is_singular('mem_volunteer') ) {
                dynamic_sidebar( 'webcu_event_volunteer_sidebar' );
            }                        
         ?>                        
            
       </aside>

    </div>
</div>

<?php } elseif ( "left" === $webcu_voltem ){?>


    <div class="webcu-container">

        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                $webcu_profile = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                $webcu_image_id  = get_post_meta( $post->ID, '_event_volunteer_image_id', true );
                $webcu_herobanner = $webcu_image_id ? wp_get_attachment_url( $webcu_image_id ) : '';
        ?>

            <?php if ( $webcu_herobanner ) : ?>
              <div class="top-header" style="background-image:url('<?php echo esc_attr($webcu_herobanner); ?>');object-fit: cover; background-repeat: round;">

                 <div class="top_heading"> <?php the_title(); ?> </div>
                
                <div class="top_container"> 
                    <div class="flexbox_one"> 
                       <div class="org_logo" style="background-image:url('<?php echo esc_attr($webcu_profile); ?>'); background-size: cover;"> <img src=""></div>                                    
                   </div>
                    <div class="flexbox_two">

                        <div class="org_name"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_name', true )); ?> </div>
                        <div class="org_desig"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_spon_desig', true )); ?> </div>
                 
                    </div>

                    <div class="flexbox_three"> 
                              
                            <span class="org_addr"> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_street', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_city', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_state', true )); ?> 
                            </span>  
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_phone', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_email', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'webcu_volun_website', true )); ?>  </div>  
                            <span class="org_info">  

                              <ul>
                                   <?php $webcu_social_link = get_post_meta(get_the_ID(), 'webcu_volun_extras', true); ?> 
                                    <?php foreach ($webcu_social_link as $webcu_item): 
                                    
                                        if('facebook'=== $webcu_item['volun_social_media'] ){
                                        ?>    
                                            <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-facebook"></i> </a><li>
                                        <?php    
                                        }elseif('linkedin'=== $webcu_item['volun_social_media'] ) {
                                        ?>
                                          <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i> </a><li>   
                                            
                                        <?php    
                                        }elseif('X'=== $webcu_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-x-twitter"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('instagram'=== $webcu_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-instagram"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('pinterest'=== $webcu_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-pinterest"></i>  </a><li>   
                                        
                                        <?php    
                                        }elseif('tiktok'=== $webcu_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($webcu_item['url']); ?>" target="_blank"> <i class="fa-brands fa-tiktok"></i> </a><li>   
                                        
                                        <?php } ?>

                                    <?php endforeach; ?>

                              </ul>

                            </span>  

                    </div>   

                </div>
            </div>
            <?php endif; ?>

        <?php
            endwhile;
        endif;
        ?> 

        <div class="webcu-wrapper">

            <!-- Sidebar -->
            <aside class="webcu-sidebar">
                <?php 
                    if ( is_singular('mem_volunteer') ) {
                        dynamic_sidebar( 'webcu_event_volunteer_sidebar' );
                    }                        
                ?>                                    
            </aside>   

            <main class="webcu-content">

                    <?php
                        if ( have_posts() ) :
                            while ( have_posts() ) : the_post();
                        ?>
                        <h1 class="webcu-title"><?php the_title(); ?></h1>
                            <div class="webcu-description">
                                <?php the_content(); ?>
                            </div>
                        <?php
                            endwhile;
                        endif;
                    ?>

                <h3> <?php echo esc_html__('Photo Gallery:', 'ultimate-event-manager') ?> </h3>     

                <?php 
                    $webcu_gallery_ids = get_post_meta($post->ID, '_volenteer_gallery_ids', true);
                        if (!empty($webcu_gallery_ids)) {
                            $webcu_ids = explode(',', $webcu_gallery_ids);
                            foreach ($webcu_ids as $id) {
                                $webcu_image = wp_get_attachment_image_src($id, 'thumbnail');
                            if ($webcu_image) {
                                echo '<img src="' . esc_url($webcu_image[0]) . '" />';
                        
                            }
                        }
                    }        
                ?>
                <br>
                <br>    
                <h3> <?php echo esc_html__('Video Gallery:', 'ultimate-event-manager') ?> </h3>     
                
                <?php
                $webcu_ownvideo = get_post_meta($post->ID, '_webcu_video_type', true);
                $webcu_youvideo = get_post_meta($post->ID, '_webcu_youtube_url', true);
                $webcu_vimeovieo = get_post_meta($post->ID, '_webcu_vimeo_url', true);   

                $webcu_youvideo_embed = str_replace("watch?v=", "embed/", $webcu_youvideo);

                $webcu_ext = !empty($webcu_ownvideo) ? strtolower(pathinfo($webcu_ownvideo, PATHINFO_EXTENSION)) : '';

                if ( !empty($webcu_youvideo) && strpos($webcu_youvideo, 'youtube') !== false ) {
                ?>
                
                <iframe width="560" height="315" 
                    src="<?php echo esc_url($webcu_youvideo_embed); ?>" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen>
                </iframe>  

            
                <?php 
                } elseif ( !empty($webcu_vimeovieo) && is_numeric($webcu_vimeovieo) ) {

                $webcu_vimeovieo = get_post_meta($post->ID, '_webcu_vimeo_url', true);
                $webcu_video_id = preg_replace('/[^0-9]/', '', $webcu_vimeovieo);
                $webcu_vimeo_embed = "https://player.vimeo.com/video/" . $webcu_video_id;
                ?>

                <iframe 
                    width="460" 
                    height="315"
                    src="<?php echo esc_url($webcu_vimeo_embed); ?>" 
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                ></iframe>

                <!-- <script src="https://player.vimeo.com/api/player.js"></script> -->
        
                <?php 
                    } elseif ( !empty($webcu_ownvideo) && in_array($webcu_ext, ['mp4','webm','ogg']) ) {
                        
                    $webcu_ownvideo = get_post_meta($post->ID, '_webcu_video_type', true);
                    $webcu_ext = strtolower(pathinfo($webcu_ownvideo, PATHINFO_EXTENSION));
                    ?>
                    <video width="560" height="315" controls> 
                    <source src="<?php echo esc_url($webcu_ownvideo); ?>" type="video/<?php echo $webcu_ext; ?>">
                    </video> 

                <?php } ?> 

                <?php     
                $webcu_video_type   = get_post_meta( $post->ID, '_webcu_volun_video_type', true );
                $webcu_youtube_url  = get_post_meta( $post->ID, '_webcu_volun_youtube_url', true );
                $webcu_vimeo_url    = get_post_meta( $post->ID, '_webcu_volun_vimeo_url', true );
                $webcu_video_id     = get_post_meta( $post->ID, '_webcu_volun_own_video_id', true );
                $webcu_video_url    = $webcu_video_id ? wp_get_attachment_url( $webcu_video_id ) : '';

                $webcu_youvideo_embed = str_replace("watch?v=", "embed/", $webcu_youtube_url);

                if ( 'youtube' === $webcu_video_type ) {
                ?>
                
                <iframe width="560" height="315" 
                    src="<?php echo esc_url($webcu_youvideo_embed); ?>" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen>
                </iframe>  

                <?php 
                    } elseif ( 'vimeo' === $webcu_video_type ) {

                    $$webcu_vimeovieo = get_post_meta($post->ID, '_webcu_volun_vimeo_url', true);
                    $webcu_video_id = preg_replace('/[^0-9]/', '', $$webcu_vimeovieo);
                    $webcu_vimeo_embed = "https://player.vimeo.com/video/" . $webcu_video_id;
                    ?>

                    <iframe 
                        width="460" 
                        height="315"
                        src="<?php echo esc_url($webcu_vimeo_embed); ?>" 
                        frameborder="0" 
                        allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>

                    <!-- <script src="https://player.vimeo.com/api/player.js"></script> -->
                    <?php
                    } elseif ('webcu_ownvideo' === $webcu_video_type ) {
                        
                        $webcu_ownvideo = get_post_meta($post->ID, '_webcu_volun_own_video_id', true);
                        $webcu_image_url = wp_get_attachment_url( $webcu_ownvideo );  
                        ?>
                        <video width="560" height="315" controls> 
                        <source src="<?php echo esc_url($webcu_image_url); ?>">
                        </video> 

                <?php } ?> 

            
            </main>      

        </div>
   </div>


<?php } ?>    



<?php get_footer(); ?>
