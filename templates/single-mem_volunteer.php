<?php get_header(); ?>


<?php $wtmem_voltem = get_option('wtmem_volunteer_template'); 
 
if ( "right" === $wtmem_voltem ){

?>

<div class="wtmem-container">

        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                $wtmem_profile = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                $wtmem_image_id  = get_post_meta( $post->ID, '_event_volunteer_image_id', true );
                $wtmem_herobanner = $wtmem_image_id ? wp_get_attachment_url( $wtmem_image_id ) : '';
        ?>

            <?php if ( $wtmem_herobanner ) : ?>
              <div class="top-header" style="background-image:url('<?php echo esc_attr($wtmem_herobanner); ?>');object-fit: cover; background-repeat: round;">

                 <div class="top_heading"> <?php the_title(); ?> </div>
                
                <div class="top_container"> 
                    <div class="flexbox_one"> 
                       <div class="org_logo" style="background-image:url('<?php echo esc_attr($wtmem_profile); ?>'); background-size: cover;"> <img src=""></div>                                    
                   </div>
                    <div class="flexbox_two">

                        <div class="org_name"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_name', true )); ?> </div>
                        <div class="org_desig"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_desig', true )); ?> </div>
                 
                    </div>

                    <div class="flexbox_three"> 
                              
                            <span class="org_addr"> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_street', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_city', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_state', true )); ?> 
                            </span>  
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_phone', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_email', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_website', true )); ?>  </div>  
                            <span class="org_info">  

                              <ul>
                                   <?php $wtmem_social_link = get_post_meta(get_the_ID(), 'wtmem_volun_extras', true); ?> 
                                    <?php foreach ($wtmem_social_link as $wtmem_item): 
                                    
                                        if('facebook'=== $wtmem_item['volun_social_media'] ){
                                        ?>    
                                            <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-facebook"></i> </a><li>
                                        <?php    
                                        }elseif('linkedin'=== $wtmem_item['volun_social_media'] ) {
                                        ?>
                                          <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i> </a><li>   
                                            
                                        <?php    
                                        }elseif('X'=== $wtmem_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-x-twitter"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('instagram'=== $wtmem_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-instagram"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('pinterest'=== $wtmem_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-pinterest"></i>  </a><li>   
                                        
                                        <?php    
                                        }elseif('tiktok'=== $wtmem_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-tiktok"></i> </a><li>   
                                        
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

    <div class="wtmem-wrapper">

        <main class="wtmem-content">

            <?php
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();
                ?>
                <h1 class="wtmem-title"><?php the_title(); ?></h1>
                    <div class="wtmem-description">
                        <?php the_content(); ?>
                    </div>
                <?php
                    endwhile;
                endif;
            ?>

        <h3> <?php echo esc_html__('Photo Gallery:', 'momento-event-manager') ?> </h3>     

        <?php 
            $wtmem_gallery_ids = get_post_meta($post->ID, '_volenteer_gallery_ids', true);
                if (!empty($wtmem_gallery_ids)) {
                    $wtmem_ids = explode(',', $wtmem_gallery_ids);
                    foreach ($wtmem_ids as $id) {
                        $wtmem_image = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($wtmem_image) {
                        echo '<img src="' . esc_url($wtmem_image[0]) . '" />';
                
                    }
                }
            }        
        ?>
        <br>
        <br>    
        <h3> <?php echo esc_html__('Video Gallery:', 'momento-event-manager') ?> </h3>     
        
        <?php

        
         $wtmem_ownvideo = get_post_meta($post->ID, '_wtmem_video_type', true);
         $wtmem_youvideo = get_post_meta($post->ID, '_wtmem_youtube_url', true);
         $wtmem_vimeovieo = get_post_meta($post->ID, '_wtmem_vimeo_url', true);   

         $wtmem_youvideo_embed = str_replace("watch?v=", "embed/", $wtmem_youvideo);

         $wtmem_ext = !empty($wtmem_ownvideo) ? strtolower(pathinfo($wtmem_ownvideo, PATHINFO_EXTENSION)) : '';

         if ( !empty($wtmem_youvideo) && strpos($wtmem_youvideo, 'youtube') !== false ) {
        ?>
        
        <iframe width="560" height="315" 
            src="<?php echo esc_url($wtmem_youvideo_embed); ?>" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen>
       </iframe>  

       
        <?php 
        } elseif ( !empty($wtmem_vimeovieo) && is_numeric($wtmem_vimeovieo) ) {

        $wtmem_vimeovieo = get_post_meta($post->ID, '_wtmem_vimeo_url', true);
        $wtmem_video_id = preg_replace('/[^0-9]/', '', $wtmem_vimeovieo);
        $wtmem_vimeo_embed = "https://player.vimeo.com/video/" . $wtmem_video_id;
        ?>

        <iframe 
            width="460" 
            height="315"
            src="<?php echo esc_url($wtmem_vimeo_embed); ?>" 
            frameborder="0" 
            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
        ></iframe>

        
        <!-- <script src="https://player.vimeo.com/api/player.js"></script> -->
        
 
       <?php 
        } elseif ( ! empty( $wtmem_ownvideo ) ) {
            $wtmem_ownvideo = get_post_meta( $post->ID, '_wtmem_video_type', true );
            $wtmem_filetype = wp_check_filetype( $wtmem_ownvideo );
            $wtmem_mime_type      = $wtmem_filetype['type'];

            if ( ! empty( $wtmem_mime_type ) && 0 === strpos( $wtmem_mime_type, 'video/' ) ) :
                ?>
                <video width="560" height="315" controls>
                    <source
                        src="<?php echo esc_url( $wtmem_ownvideo ); ?>"
                        type="<?php echo esc_attr( $wtmem_mime_type ); ?>">
                </video>
                <?php
            endif;
        }
    
        $wtmem_video_type = get_post_meta( $post->ID, '_wtmem_volun_video_type', true );
        $wtmem_youtube_url  = get_post_meta( $post->ID, '_wtmem_volun_youtube_url', true );
        $wtmem_vimeo_url    = get_post_meta( $post->ID, '_wtmem_volun_vimeo_url', true );
        $wtmem_video_id = get_post_meta( $post->ID, '_wtmem_volun_own_video_id', true );
        $wtmem_video_url    = $wtmem_video_id ? wp_get_attachment_url( $wtmem_video_id ) : '';

        $wtmem_youvideo_embed = str_replace("watch?v=", "embed/", $wtmem_youtube_url);

        if ( 'youtube' === $wtmem_video_type ) {
        ?>
        
        <iframe width="560" height="315" 
            src="<?php echo esc_url($wtmem_youvideo_embed); ?>" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen>
       </iframe>  

       <?php 
        } elseif ( 'vimeo' === $wtmem_video_type ) {

        $wtmem_vimeovieo = get_post_meta($post->ID, '_wtmem_volun_vimeo_url', true);
        $wtmem_video_id = preg_replace('/[^0-9]/', '', $wtmem_vimeovieo);
        $wtmem_vimeo_embed = "https://player.vimeo.com/video/" . $wtmem_video_id;
        ?>

        <iframe 
            width="460" 
            height="315"
            src="<?php echo esc_url($wtmem_vimeo_embed); ?>" 
            frameborder="0" 
            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
        ></iframe>

        
        <?php
        } elseif ('wtmem_ownvideo' === $wtmem_video_type ) {
            
            $wtmem_ownvideo = get_post_meta($post->ID, '_wtmem_volun_own_video_id', true);
            $wtmem_image_url = wp_get_attachment_url( $wtmem_ownvideo );  
            ?>
            <video width="560" height="315" controls> 
            <source src="<?php echo esc_url($wtmem_image_url); ?>">
            </video> 

       <?php } ?> 

        
        </main>

        <!-- Sidebar -->
        <aside class="wtmem-sidebar">

        <?php 
            if ( is_singular('mem_volunteer') ) {
                dynamic_sidebar( 'wtmem_event_volunteer_sidebar' );
            }                        
         ?>                        
            
       </aside>

    </div>
</div>

<?php } elseif ( "left" === $wtmem_voltem ){?>


    <div class="wtmem-container">

        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                $wtmem_profile = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                $wtmem_image_id  = get_post_meta( $post->ID, '_event_volunteer_image_id', true );
                $wtmem_herobanner = $wtmem_image_id ? wp_get_attachment_url( $wtmem_image_id ) : '';
        ?>

            <?php if ( $wtmem_herobanner ) : ?>
              <div class="top-header" style="background-image:url('<?php echo esc_attr($wtmem_herobanner); ?>');object-fit: cover; background-repeat: round;">

                 <div class="top_heading"> <?php the_title(); ?> </div>
                
                <div class="top_container"> 
                    <div class="flexbox_one"> 
                       <div class="org_logo" style="background-image:url('<?php echo esc_attr($wtmem_profile); ?>'); background-size: cover;"> <img src=""></div>                                    
                   </div>
                    <div class="flexbox_two">

                        <div class="org_name"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_name', true )); ?> </div>
                        <div class="org_desig"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_desig', true )); ?> </div>
                 
                    </div>

                    <div class="flexbox_three"> 
                              
                            <span class="org_addr"> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_street', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_city', true )); ?> 
                                    <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_state', true )); ?> 
                            </span>  
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_phone', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_email', true )); ?>  </div>
                            <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_volun_website', true )); ?>  </div>  
                            <span class="org_info">  

                              <ul>
                                   <?php $wtmem_social_link = get_post_meta(get_the_ID(), 'wtmem_volun_extras', true); ?> 
                                    <?php foreach ($wtmem_social_link as $wtmem_item): 
                                    
                                        if('facebook'=== $wtmem_item['volun_social_media'] ){
                                        ?>    
                                            <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-facebook"></i> </a><li>
                                        <?php    
                                        }elseif('linkedin'=== $wtmem_item['volun_social_media'] ) {
                                        ?>
                                          <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i> </a><li>   
                                            
                                        <?php    
                                        }elseif('X'=== $wtmem_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-x-twitter"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('instagram'=== $wtmem_item['volun_social_media'] ) {
                                        ?> 

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-instagram"></i> </a><li>   
                                        
                                        <?php    
                                        }elseif('pinterest'=== $wtmem_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-pinterest"></i>  </a><li>   
                                        
                                        <?php    
                                        }elseif('tiktok'=== $wtmem_item['volun_social_media'] ) {
                                        ?>

                                        <li> <a href="<?php echo esc_url($wtmem_item['url']); ?>" target="_blank"> <i class="fa-brands fa-tiktok"></i> </a><li>   
                                        
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

        <div class="wtmem-wrapper">

            <!-- Sidebar -->
            <aside class="wtmem-sidebar">
                <?php 
                    if ( is_singular('mem_volunteer') ) {
                        dynamic_sidebar( 'wtmem_event_volunteer_sidebar' );
                    }                        
                ?>                                    
            </aside>   

            <main class="wtmem-content">

                    <?php
                        if ( have_posts() ) :
                            while ( have_posts() ) : the_post();
                        ?>
                        <h1 class="wtmem-title"><?php the_title(); ?></h1>
                            <div class="wtmem-description">
                                <?php the_content(); ?>
                            </div>
                        <?php
                            endwhile;
                        endif;
                    ?>

                <h3> <?php echo esc_html__('Photo Gallery:', 'momento-event-manager') ?> </h3>     

                <?php 
                    $wtmem_gallery_ids = get_post_meta($post->ID, '_volenteer_gallery_ids', true);
                        if (!empty($wtmem_gallery_ids)) {
                            $wtmem_ids = explode(',', $wtmem_gallery_ids);
                            foreach ($wtmem_ids as $id) {
                                $wtmem_image = wp_get_attachment_image_src($id, 'thumbnail');
                            if ($wtmem_image) {
                                echo '<img src="' . esc_url($wtmem_image[0]) . '" />';
                        
                            }
                        }
                    }        
                ?>
                <br>
                <br>    
                <h3> <?php echo esc_html__('Video Gallery:', 'momento-event-manager') ?> </h3>     
                
                <?php
                $wtmem_ownvideo = get_post_meta($post->ID, '_wtmem_video_type', true);
                $wtmem_youvideo = get_post_meta($post->ID, '_wtmem_youtube_url', true);
                $wtmem_vimeovieo = get_post_meta($post->ID, '_wtmem_vimeo_url', true);   

                $wtmem_youvideo_embed = str_replace("watch?v=", "embed/", $wtmem_youvideo);

                $wtmem_ext = !empty($wtmem_ownvideo) ? strtolower(pathinfo($wtmem_ownvideo, PATHINFO_EXTENSION)) : '';

                if ( !empty($wtmem_youvideo) && strpos($wtmem_youvideo, 'youtube') !== false ) {
                ?>
                
                <iframe width="560" height="315" 
                    src="<?php echo esc_url($wtmem_youvideo_embed); ?>" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen>
                </iframe>  

            
                <?php 
                } elseif ( !empty($wtmem_vimeovieo) && is_numeric($wtmem_vimeovieo) ) {

                $wtmem_vimeovieo = get_post_meta($post->ID, '_wtmem_vimeo_url', true);
                $wtmem_video_id = preg_replace('/[^0-9]/', '', $wtmem_vimeovieo);
                $wtmem_vimeo_embed = "https://player.vimeo.com/video/" . $wtmem_video_id;
                ?>

                <iframe 
                    width="460" 
                    height="315"
                    src="<?php echo esc_url($wtmem_vimeo_embed); ?>" 
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                ></iframe>
        
                <?php 
                    } elseif ( !empty($wtmem_ownvideo) && in_array($wtmem_ext, ['mp4','webm','ogg']) ) {
                        
                    $wtmem_ownvideo = get_post_meta($post->ID, '_wtmem_video_type', true);
                    $wtmem_ext = strtolower(pathinfo($wtmem_ownvideo, PATHINFO_EXTENSION));
                    ?>
                    <video width="560" height="315" controls> 
                    <source src="<?php echo esc_url($wtmem_ownvideo); ?>" type="video/<?php echo $wtmem_ext; ?>">
                    </video> 

                <?php } ?> 

                <?php     
                $wtmem_video_type   = get_post_meta( $post->ID, '_wtmem_volun_video_type', true );
                $wtmem_youtube_url  = get_post_meta( $post->ID, '_wtmem_volun_youtube_url', true );
                $wtmem_vimeo_url    = get_post_meta( $post->ID, '_wtmem_volun_vimeo_url', true );
                $wtmem_video_id     = get_post_meta( $post->ID, '_wtmem_volun_own_video_id', true );
                $wtmem_video_url    = $wtmem_video_id ? wp_get_attachment_url( $wtmem_video_id ) : '';

                $wtmem_youvideo_embed = str_replace("watch?v=", "embed/", $wtmem_youtube_url);

                if ( 'youtube' === $wtmem_video_type ) {
                ?>
                
                <iframe width="560" height="315" 
                    src="<?php echo esc_url($wtmem_youvideo_embed); ?>" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen>
                </iframe>  

                <?php 
                    } elseif ( 'vimeo' === $wtmem_video_type ) {

                    $$wtmem_vimeovieo = get_post_meta($post->ID, '_wtmem_volun_vimeo_url', true);
                    $wtmem_video_id = preg_replace('/[^0-9]/', '', $$wtmem_vimeovieo);
                    $wtmem_vimeo_embed = "https://player.vimeo.com/video/" . $wtmem_video_id;
                    ?>

                    <iframe 
                        width="460" 
                        height="315"
                        src="<?php echo esc_url($wtmem_vimeo_embed); ?>" 
                        frameborder="0" 
                        allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>

                    <?php
                    } elseif ('wtmem_ownvideo' === $wtmem_video_type ) {
                        
                        $wtmem_ownvideo = get_post_meta($post->ID, '_wtmem_volun_own_video_id', true);
                        $wtmem_image_url = wp_get_attachment_url( $wtmem_ownvideo );  
                        ?>
                        <video width="560" height="315" controls> 
                        <source src="<?php echo esc_url($wtmem_image_url); ?>">
                        </video> 

                <?php } ?> 

            
            </main>      

        </div>
   </div>


<?php } ?>    



<?php get_footer(); ?>
