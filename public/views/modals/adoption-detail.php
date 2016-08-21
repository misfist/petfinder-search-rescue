<?php
/**
* Modal Adoption Detail (nee pop-up)
*/
?>
<div id="adoptinfo-popup" class="window-popup">
    <div class="window-popup-title"><?php _e( 'Adoption Information', 'wp-petfinder-listing' ); ?></div>
    <div id="indiepet-popup-topbtns" class="window-popup-topbtns">
        <span class="ico-psr_close psr__hoverme window-popup-close"></span>
    </div>
    <pre id="adoptinfo-popup-inner" class="window-popup-inner"><?php echo esc_attr( $pet_sr_options['psr_shelter_info'] ); ?></pre>
</div>

<div id="psr__main">
    <div class="psr__header">
        <div class="psr__row-fluid">

            <?php if ( $pet_sr_options['psr_optionssection_remove']!='on' ) : ?>

                <div class="psr__span4" id="toggle-petOptions-holder">
                    <div id="toggle-petOptions" class="psr__hoverme toggle-petOptions">
                        <?php if ( $pet_sr_options['psr_hideoptionssection_default']=='on' ) : ?>

                            <span class="toggle-petOptions-text"><?php _e( 'Show Options', 'wp-petfinder-listing' ); ?></span>
                            <span class="ico-psr_show"></span>

                        <?php else : ?>

                            <span class="toggle-petOptions-text"><?php _e( 'Hide Options', 'wp-petfinder-listing' ); ?></span>
                            <span class="ico-psr_hide"></span>

                        <?php endif; ?>
                    </div>
                </div>
            <?php else : ?>

                <div class="psr__span4"></div>

            <?php endif; ?>

            <div class="psr__span4" id="search-icon-container">
                <span id="search-icon"><span class="ico-psr_search"></span></span>
                <input class="filter__search js-shuffle-search" type="text" placeholder="<?php _e( 'Search pet name...', 'wp-petfinder-listing' ) ; ?>">
            </div>
            <div class="psr__span4">
                <div class="psr__header-icon psr__hoverme" id="psr__adoptform-btn" title="<?php _e( 'Adoption Form', 'wp-petfinder-listing' ); ?>">
                    <a href="<?php echo esc_url( $pet_sr_options['psr_adoptionformpage_link'] ); ?>">
                        <span class="ico-psr_application"></span>
                        <span class="psr__header-title"><?php _e( 'Adoption Form', 'wp-petfinder-listing' ); ?></span>
                    </a>
                </div>
                <div class="psr__header-icon psr__hoverme" id="psr__adoptinfo-btn" title="<?php _e( 'Adoption Information', 'wp-petfinder-listing' ); ?>">
                    <span class="ico-psr_info"></span>
                </div>
            </div><!-- //end div -->
        </div><!-- //end top row -->
    </div><!-- //end header -->

    <?php echo $arrowdiv; ?>
