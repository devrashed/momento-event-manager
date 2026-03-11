<?php get_header(); ?>


<?php 
    $spontempla = get_option('wtmem_sponser_template'); 
    if( "right" === $spontempla ){
?>
    <div class="wtmem-container">

            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();

                    $profile = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        
                    $image_id  = get_post_meta( $post->ID, '_event_sponser_image_id', true );
                    $herobanner = $image_id ? wp_get_attachment_url( $image_id ) : '';
            ?>

                <?php if ( $herobanner ) : ?>
                <div class="top-header" style="background-image:url('<?php echo esc_attr($herobanner); ?>');object-fit: cover; background-repeat: round;">

                    <h1 class="top_heading"> <?php the_title(); ?> </h1>
                    
                    <div class="top_container"> 
                        <div class="flexbox_one"> 
                        <div class="org_logo" style="background-image:url('<?php echo esc_attr($profile); ?>'); background-size: cover;"> <img src=""></div>                                    
                    </div>
                        <div class="flexbox_two">

                            <div class="org_name"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_name', true )); ?> </div>
                            <div class="org_desig"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_desig', true )); ?> </div>
                    
                        </div>

                        <div class="flexbox_three"> 
                                
                                <span class="org_addr"> 
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_street', true )); ?> 
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_city', true )); ?> 
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_state', true )); ?>
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_postcode', true )); ?> 
                                </span>  
                                <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_phone', true )); ?>  </div>
                                <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_email', true )); ?>  </div>
                                <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_website', true )); ?>  </div>  
                                <span class="org_info">  

                                <ul>
                                    <?php $social_link = get_post_meta(get_the_ID(), 'wtmem_spon_extras', true); ?> 
                                        <?php foreach ($social_link as $item): 
                                        
                                            if('facebook'=== $item['spon_social_media'] ){
                                            ?>    
                                                <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-facebook"></i> </a><li>
                                            <?php    
                                            }elseif('linkedin'=== $item['spon_social_media'] ) {
                                            ?>
                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i> </a><li>   
                                                
                                            <?php    
                                            }elseif('X'=== $item['spon_social_media'] ) {
                                            ?> 

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-x-twitter"></i> </a><li>   
                                            
                                            <?php    
                                            }elseif('instagram'=== $item['spon_social_media'] ) {
                                            ?> 

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-instagram"></i> </a><li>   
                                            
                                            <?php    
                                            }elseif('pinterest'=== $item['spon_social_media'] ) {
                                            ?>

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-pinterest"></i>  </a><li>   
                                            
                                            <?php    
                                            }elseif('tiktok'=== $item['spon_social_media'] ) {
                                            ?>

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-tiktok"></i> </a><li>   
                                            
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
                $gallery_ids = get_post_meta($post->ID, '_sponser_gallery_ids', true);
                    if (!empty($gallery_ids)) {
                        $ids = explode(',', $gallery_ids);
                        foreach ($ids as $id) {
                            $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<img src="' . esc_url($image[0]) . '" />';
                    
                        }
                    }
                }        
            ?>
            <br>
            <br>    
            <h3> <?php echo esc_html__('Video Gallery:', 'momento-event-manager') ?> </h3>     
            
            <?php

            $video_type   = get_post_meta( $post->ID, '_wtmem_spon_video_type', true );
            $youtube_url  = get_post_meta( $post->ID, '_wtmem_spon_youtube_url', true );
            $vimeo_url    = get_post_meta( $post->ID, '_wtmem_spon_vimeo_url', true );
            $ownvideo     = get_post_meta( $post->ID, '_wtmem_spon_own_video_id', true );

            $youvideo_embed = str_replace("watch?v=", "embed/", $youtube_url);
            $ext = !empty($ownvideo) ? strtolower(pathinfo($ownvideo, PATHINFO_EXTENSION)) : '';

            if ( 'youtube' === $video_type ) {
            ?>
            
            <iframe width="560" height="315" 
                src="<?php echo esc_url($youvideo_embed); ?>" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
        </iframe>  
        
            <?php 
            } elseif ( 'vimeo' === $video_type ) {

            $vimeovieo = get_post_meta($post->ID, '_wtmem_spon_vimeo_url', true);
            $video_id = preg_replace('/[^0-9]/', '', $vimeovieo);
            $vimeo_embed = "https://player.vimeo.com/video/" . $video_id;
            ?>

            <iframe 
                width="460" 
                height="315"
                src="<?php echo esc_url($vimeo_embed); ?>" 
                frameborder="0" 
                allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
            ></iframe>

            <script src="https://player.vimeo.com/api/player.js"></script>
            <?php
            } elseif ('ownvideo' === $video_type ) {
                
                $ownvideo = get_post_meta($post->ID, '_wtmem_spon_own_video_id', true);
                $image_url = wp_get_attachment_url( $ownvideo );  
                ?>
                <video width="560" height="315" controls> 
                <source src="<?php echo esc_url($image_url); ?>">
                </video> 

        <?php } ?> 

        </main>

            <!-- Sidebar -->
            <aside class="wtmem-sidebar">
           
                <?php

                if ( is_singular('mem_sponsor') ) {
                    dynamic_sidebar( 'wtmem_event_sponser_sidebar' );
                }                        
                ?>
                        
            </aside>

        </div>

    </div>

    <?php } elseif ( "left" === $spontempla ) {?>


    <div class="wtmem-container">

            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();

                    $profile = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        
                    $image_id  = get_post_meta( $post->ID, '_event_sponser_image_id', true );
                    $herobanner = $image_id ? wp_get_attachment_url( $image_id ) : '';
            ?>

                <?php if ( $herobanner ) : ?>
                <div class="top-header" style="background-image:url('<?php echo esc_attr($herobanner); ?>');object-fit: cover; background-repeat: round;">

                    <h1 class="top_heading"> <?php the_title(); ?> </h1>
                    
                    <div class="top_container"> 
                        <div class="flexbox_one"> 
                        <div class="org_logo" style="background-image:url('<?php echo esc_attr($profile); ?>'); background-size: cover;"> <img src=""></div>                                    
                    </div>
                        <div class="flexbox_two">

                            <div class="org_name"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_name', true )); ?> </div>
                            <div class="org_desig"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_desig', true )); ?> </div>
                    
                        </div>

                        <div class="flexbox_three"> 
                                
                                <span class="org_addr"> 
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_street', true )); ?> 
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_city', true )); ?> 
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_state', true )); ?>
                                        <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_postcode', true )); ?> 
                                </span>  
                                <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_phone', true )); ?>  </div>
                                <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_email', true )); ?>  </div>
                                <div class="org_phone"> <?php echo esc_attr (get_post_meta( $post->ID, 'wtmem_spon_website', true )); ?>  </div>  
                                <span class="org_info">  

                                <ul>
                                    <?php $social_link = get_post_meta(get_the_ID(), 'wtmem_spon_extras', true); ?> 
                                        <?php foreach ($social_link as $item): 
                                        
                                            if('facebook'=== $item['spon_social_media'] ){
                                            ?>    
                                                <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-facebook"></i> </a><li>
                                            <?php    
                                            }elseif('linkedin'=== $item['spon_social_media'] ) {
                                            ?>
                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i> </a><li>   
                                                
                                            <?php    
                                            }elseif('X'=== $item['spon_social_media'] ) {
                                            ?> 

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-x-twitter"></i> </a><li>   
                                            
                                            <?php    
                                            }elseif('instagram'=== $item['spon_social_media'] ) {
                                            ?> 

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-instagram"></i> </a><li>   
                                            
                                            <?php    
                                            }elseif('pinterest'=== $item['spon_social_media'] ) {
                                            ?>

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-square-pinterest"></i>  </a><li>   
                                            
                                            <?php    
                                            }elseif('tiktok'=== $item['spon_social_media'] ) {
                                            ?>

                                            <li> <a href="<?php echo esc_url($item['url']); ?>" target="_blank"> <i class="fa-brands fa-tiktok"></i> </a><li>   
                                            
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
                if ( is_singular('mem_sponsor') ) {
                    dynamic_sidebar( 'wtmem_event_sponser_sidebar' );
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
                    $gallery_ids = get_post_meta($post->ID, '_sponser_gallery_ids', true);
                        if (!empty($gallery_ids)) {
                            $ids = explode(',', $gallery_ids);
                            foreach ($ids as $id) {
                                $image = wp_get_attachment_image_src($id, 'thumbnail');
                            if ($image) {
                                echo '<img src="' . esc_url($image[0]) . '" />';
                        
                            }
                        }
                    }        
                ?>
                <br>
                <br>    
                <h3> <?php echo esc_html__('Video Gallery:', 'momento-event-manager') ?> </h3>     
                
                <?php

                $video_type   = get_post_meta( $post->ID, '_wtmem_spon_video_type', true );
                $youtube_url  = get_post_meta( $post->ID, '_wtmem_spon_youtube_url', true );
                $vimeo_url    = get_post_meta( $post->ID, '_wtmem_spon_vimeo_url', true );
                $ownvideo     = get_post_meta( $post->ID, '_wtmem_spon_own_video_id', true );

                $youvideo_embed = str_replace("watch?v=", "embed/", $youtube_url);
                $ext = !empty($ownvideo) ? strtolower(pathinfo($ownvideo, PATHINFO_EXTENSION)) : '';

                if ( 'youtube' === $video_type ) {
                ?>
                
                <iframe width="560" height="315" 
                    src="<?php echo esc_url($youvideo_embed); ?>" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen>
                </iframe>  
            
                <?php 
                } elseif ( 'vimeo' === $video_type ) {

                $vimeovieo = get_post_meta($post->ID, '_wtmem_spon_vimeo_url', true);
                $video_id = preg_replace('/[^0-9]/', '', $vimeovieo);
                $vimeo_embed = "https://player.vimeo.com/video/" . $video_id;
                ?>

                <iframe 
                    width="460" 
                    height="315"
                    src="<?php echo esc_url($vimeo_embed); ?>" 
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                ></iframe>

                <script src="https://player.vimeo.com/api/player.js"></script>
                <?php
                } elseif ('ownvideo' === $video_type ) {
                    
                    $ownvideo = get_post_meta($post->ID, '_wtmem_spon_own_video_id', true);
                    $image_url = wp_get_attachment_url( $ownvideo );  
                    ?>
                    <video width="560" height="315" controls> 
                    <source src="<?php echo esc_url($image_url); ?>">
                    </video> 

                <?php } ?> 

        </main>

        </div>

    </div>

    <?php } ?>   


<?php get_footer(); ?>
