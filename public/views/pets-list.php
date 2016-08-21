<?php
/**
 * Markup for List of Pets
 */
?>
<div class="psr__span2 picture-item shuffle-item filtered <?php echo $classes; ?>" data-groups="[&quot;<?php echo $data_atts; ?>&quot;]" data-title="<?php echo $pet_name; ?>">

    <div class="picture-item__inner">
        <div class="picture-item__glyph"><?php echo $pet_photo_thumbnail; ?></div>
        <div class="picture-item__details clearfix">
            <div class="picture-item__title"><?php echo $pet_name; ?></div>
            <div class="picture-item__tags"><?php echo $tags;?></p></div>
            <!--hidden pet information-->
            <div class="picture-item__more-details" style="display:none">

                <div class="my-pet-options"><?php echo $pet_options; ?></div>
                <div class="my-pet-attributes"><span><?php echo $atts; ?></span></div>
                <div class="my-pet-petfinder_url"><a href="<?php echo esc_url( $pet_pf_url ); ?>"><?php _e( 'Petfinder Link', 'wp-petfinder-listing' ); ?></a></div>
                <div class="my-pet-description"><?php echo esc_html( $pet_description ); ?></div>
                <div class="my-pet-photos">
                    <ul id="image_slider"><?php echo $pet_photo_all; ?></ul>
                    <span class="nvgt ico-psr_previous" id="prev"></span>
                    <span class="nvgt ico-psr_next" id="next"></span>
                </div>
            </div><!--end details-->
        </div><!--end inner-->
    </div><!--end picture item-->
</div>
