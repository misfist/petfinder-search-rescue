<?php
/**
 * Markup for Settings Page
 */
?>

<div class="wrap" id="psr__options-settings-form">
    <h2 id="psr__options-main-title"><?php _e( 'WordPress Petfinder Listing', 'wp-petfinder-listing' ) ?></h2>

    <div class="psr__options-settings-instructions">
        <div class="psr__row-fluid">
            <div class="psr__span12">
                <h3><?php _e( 'Use this shortcode to display the listing.', 'wp-petfinder-listing' ) ?></h3>
                <p><pre>[wp-petfinder-listing api_key="YOUR API KEY" shelter_id="YOUR SHELTER ID" count="100"]</pre></p>
            </div>
        </div>
    </div>

    <form name="psr__the_form" action="options-general.php?page=pet_sr" method="post">

        <?php
        if ( function_exists( 'wp_nonce_field' ) )
        wp_nonce_field( 'petfinder-search-and-rescue-update_settings' );  ?>

        <div class="psr_style-options-section">
            <div class="psr__row-fluid">
                <div class="psr__span12"><h2><?php _e( 'Header', 'wp-petfinder-listing' ); ?></h2></div>
            </div><!--end row-->

            <div class="psr__row-fluid">

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Header Background Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_header_bg" value="<?php echo $pet_sr_options['psr_header_bg']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Show/Hide Text Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_show_hide" value="<?php echo $pet_sr_options['psr_show_hide']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Adoption Form Link Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_header_links" value="<?php echo $pet_sr_options['psr_header_links']; ?>" />
                </div>

            </div><!--end row-->
        </div><!--end options sections-->

        <div class="psr_style-options-section">

            <div class="psr__row-fluid">
                <div class="psr__span12"><h2><?php _e( 'Pet Options Section', 'wp-petfinder-listing' ); ?></h2></div>
            </div><!--end row-->

            <div class="psr__row-fluid">

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Options Background Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_options_bg" value="<?php echo $pet_sr_options['psr_options_bg']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Option Section', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_optionssection_bg" value="<?php echo $pet_sr_options['psr_optionssection_bg']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Options Titles Text Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_options_title_color" value="<?php echo $pet_sr_options['psr_options_title_color']; ?>" />
                </div>

            </div><!--end row-->

            <div class="psr__row-fluid">
                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'All Options', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_options_text_color" value="<?php echo $pet_sr_options['psr_options_text_color']; ?>" />
                </div>


                <div class="psr__span4">
                    <div class="psr_options_title">Option Selected Button <br/><em>Text Color</em></div>
                    <input type="text" class="psr-color-field" name="psr_options_selected_text" value="<?php echo $pet_sr_options['psr_options_selected_text']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title">Option Selected Button <br/><em>Background Color</em></div>
                    <input type="text" class="psr-color-field" name="psr_options_selected" value="<?php echo $pet_sr_options['psr_options_selected']; ?>" />
                </div>

            </div><!--end row-->

            <!--Hide options section by default?-->
            <div class="psr__row-fluid">
                <div class="psr__span4">
                    <input type="checkbox" name="psr_hideoptionssection_default" <?php
                    echo ( 'on' ===  $pet_sr_options['psr_hideoptionssection_default'] ) ? 'checked' : ''; ?> /> <?php _e( 'Hide options section by default', 'wp-petfinder-listing' ); ?>
                </div>

                <!--Remove options section?-->
                <div class="psr__span4">
                    <input type="checkbox" name="psr_optionssection_remove" <?php
                    echo ( 'on' === $pet_sr_options['psr_optionssection_remove'] ) ? 'checked' : ''; ?> /><?php _e( 'Remove options section completely', 'wp-petfinder-listing' ); ?>
                </div>
            </div>

        </div>  <!--end section-->

        <div class="psr_style-options-section">

            <div class="psr__row-fluid">
                <div class="psr__span12"><h2><?php _e( 'Pet Grid Section', 'wp-petfinder-listing' ); ?></h2></div>
            </div><!--end row-->

            <div class="psr__row-fluid">

                <div class="psr__span4">
                    <div class="psr_options_title"><?php __( 'Pet Grid Background Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_petgrid_bg" value="<?php echo $pet_sr_options['psr_petgrid_bg']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Pet Title Text Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_pettitle_grid" value="<?php echo $pet_sr_options['psr_pettitle_grid']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Pet Description Background Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_petdesc_grid" value="<?php echo $pet_sr_options['psr_petdesc_grid']; ?>" />
                </div>

            </div><!--end row-->
            <div class="psr__row-fluid">

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Pet Tags Text Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_pettags_grid_color" value="<?php echo $pet_sr_options['psr_pettags_grid_color']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( 'Pet Breed Tag Text Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_petbreedtag_grid_color" value="<?php echo $pet_sr_options['psr_petbreedtag_grid_color']; ?>" />
                </div>

                <div class="psr__span4">
                    <div class="psr_options_title"><?php _e( '"Back to Top" Arrow Background Color', 'wp-petfinder-listing' ); ?></div>
                    <input type="text" class="psr-color-field" name="psr_pageuparrow_grid_color" value="<?php echo $pet_sr_options['psr_pageuparrow_grid_color']; ?>" />
                </div>

            </div><!--end row-->
            <br/>

            <!--show up arrow?-->
            <div class="psr__row-fluid">
                <div class="psr__span6">
                    <input type="checkbox" id='psr_pageuparrow_show' name="psr_pageuparrow_show" value='1' <?php checked( '1', $pet_sr_options['psr_pageuparrow_show'] ); ?> />
                    <?php _e( 'Show arrow to scroll back to top', 'wp-petfinder-listing' ); ?></div>
                </div>
            </div><!--end section-->


            <div class="psr_style-options-section">

                <div class="psr__row-fluid">
                    <div class="psr__span12"><h2><?php _e( 'Pet Pop-Up Section', 'wp-petfinder-listing' ); ?></h2></div>
                </div><!--end row-->

                <div class="psr__row-fluid">
                    <div class="psr__span4">

                        <div class="psr_options_title"><?php _e( 'Pet Title Text Color', 'wp-petfinder-listing' ); ?></div>
                        <input type="text" class="psr-color-field" name="psr_pettitle_popup" value="<?php echo $pet_sr_options['psr_pettitle_popup']; ?>" />
                    </div>

                    <div class="psr__span4">
                        <div class="psr_options_title"><?php _e( 'Tags Text Color', 'wp-petfinder-listing' ); ?></div>
                        <input type="text" class="psr-color-field" name="psr_textontags_popup" value="<?php echo $pet_sr_options['psr_textontags_popup']; ?>" />
                    </div>

                    <div class="psr__span4">
                        <div class="psr_options_title"><?php _e( 'Tags Background Color', 'wp-petfinder-isting' ); ?></div>
                        <input type="text" class="psr-color-field" name="psr_tags_popup" value="<?php echo $pet_sr_options['psr_tags_popup']; ?>" />
                    </div>

                </div><!--end row-->
                <div class="psr__row-fluid">

                    <div class="psr__span4">
                        <div class="psr_options_title"><?php _e( 'Adopt Me Button Text Color', 'wp-petfinder-listing' ); ?></div>
                        <input type="text" class="psr-color-field" name="psr_adoptbtntext_popup" value="<?php echo $pet_sr_options['psr_adoptbtntext_popup']; ?>" />
                    </div>

                    <div class="psr__span4">
                        <div class="psr_options_title"><?php _e( 'Adopt Me Button Background Color', 'wp-petfinder-listing' ); ?></div>
                        <input type="text" class="psr-color-field" name="psr_adoptbtnbg_popup" value="<?php echo $pet_sr_options['psr_adoptbtnbg_popup']; ?>" />
                    </div>

                </div><!--end row-->
            </div><!--end section-->

            <div class="psr_style-options-section">
                <div class="psr__row-fluid">
                    <div class="psr__span12"><h2><?php _e( 'Link to Adoption Form', 'wp-petfinder-listing' ); ?></h2></div>
                </div>
                <div class="psr__row-fluid">
                    <div class="psr__span6">
                        <div class="psr_options_title"><?php _e( 'Please type in the full link', 'wp-petfinder-listing' ); ?></div>
                        <input class="input-large" type="text" name="psr_adoptionformpage_link" value="<?php echo $pet_sr_options['psr_adoptionformpage_link']; ?>" placeholder="http://www.mysite.com/adoption-form/" />
                    </div>
                </div>
            </div>

            <div class="psr_style-options-section">

                <!--THE TEXT FIELD FOR ENTERING ADOPTION INFORMATION-->
                <div class="psr__row-fluid">
                    <div class="psr__span12"><h2><?php _e( 'Adoption Information', 'wp-petfinder-listing' ); ?></h2></div>
                </div>
                <div class="psr__row-fluid">
                    <div class="psr__span12">
                        <div class="psr_options_title"><?php _e( 'Here you can list adoption fees, shelter hours, and how the adoption process works.', 'wp-petfinder-listing' ); ?></div>
                        <?php

                        $content = '';
                        $editor_id = 'psr_shelter_info';
                        $args = array("textarea_name" => "psr_shelter_info");
                        $args = array("textarea_value" => "$pet_sr_options[psr_shelter_info]");
                        //wp_editor( $content, $editor_id );
                        wp_editor( $pet_sr_options['psr_shelter_info'], "psr_shelter_info", $args);

                        ?>
                    </div>
                </div>
            </div>

            <p class="submit">
                <input type="hidden" name="save_changes" value="1" />
                <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-petfinder-listing'  ) ?>" />
            </p>

        </form>

        <h2><?php _e( 'Reset Defaults', 'wp-petfinder-listing' ); ?></h2>
        <form method="post" action="">
            <p class="submit">
                <?php _e( 'Reset colors to original settings:', 'wp-petfinder-listing' ); ?>
                <input name="reset" class="button button-secondary" type="submit" value="<?php _e( 'Reset Colors', 'wp-petfinder-listing' ); ?>">
                <input type="hidden" name="action" value="reset"  />
            </p>
        </form>

</div>
