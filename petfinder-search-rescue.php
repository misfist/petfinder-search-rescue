<?php
/**
 * Plugin Name:     WordPress Petfinder Listing
 * Plugin URI:      https://github.com/misfist/petfinder-search-rescue
 * Description:     Filterable and searchable Petfinder listing.
 *
 * Author:          Pea <pea@misfist.com>
 * Author URI:      https://github.com/misfist
 *
 * Text Domain:     wp-petfinder-listing
 * Domain Path:     /languages
 *
 * Version:         2.0.0
 *
 * @package         WP_Petfinder_Listing
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Constants
 */
$pluginName = plugin_basename( __FILE__ );

/**
 * Dependencies
 */
// Widget
include( dirname(__FILE__) . '/widget/petfinder-search-rescue-widget.php' );

/**
 * Enqueue Scripts
 */
// Admin Scripts
function psr_enqueue_color_picker() {
    wp_register_style( 'petfinder-listing-rows', plugins_url( 'css/psr_rows_styles.css', __FILE__ ) );

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'psr-script-handle', plugins_url( '/js/my-script.js', __FILE__ ), array( 'wp-color-picker', 'jquery' ), false, true );
	wp_enqueue_style( 'petfinder-listing-rows' );
}
add_action( 'admin_enqueue_scripts', 'psr_enqueue_color_picker' );


// Public Scripts

/**
 * Options Page
 */
add_option( 'petfinder-search-and-rescue', $pet_sr_options );
$pet_sr_options = get_option( 'petfinder-search-and-rescue' );
add_action( 'admin_menu', 'pet_sr_admin_page' );

/**
 * Add Options Page
 *
 * @uses add_options_page
 * @return void
 */
function pet_sr_admin_page() {
	add_options_page(
		'Petfinder: Search & Rescue Settings',
		'Petfinder: Seach & Rescue',
		'manage_options',
		'pet_sr',
		'pet_sr_options_page'
	);
}

/**
 * Add Links to Plugins Page
 *
 * @param  array $links
 * @return array $links
 *
 * @uses plugin_action_links_
 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
 */
function pet_sr_pluginActions( $links ) {
	$settings_link =
		'<a href="' . get_admin_url( null, 'options-general.php' ) . "?page=pet_sr".'">' .
		__('Settings') . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links_' . $pluginName, 'pet_sr_pluginActions' );

/**
 * Default Options
 * @var array $pet_sr_options
 */
$pet_sr_options = array(
	'psr_header_bg' => '#F67D42',
	'psr_show_hide' => '#F67D42',
	'psr_header_links' => '#fff',
	'psr_options_bg' => '#fff',
	'psr_optionssection_bg' => '#fff',
	'psr_options_title_color' => '#555',
	'psr_options_text_color' => '#999',
	'psr_options_selected_text' => '#fff',
	'psr_options_selected' => '#F67D42',
	'psr_petgrid_bg' => '#fff',
	'psr_pettitle_grid' => '#F67D42',
	'psr_petdesc_grid' => '#ECF0F1',
	'psr_pettags_grid_color' => '#333',
	'psr_petbreedtag_grid_color' => '#777',
	'psr_pageuparrow_grid_color' => '#999',
	'psr_pettitle_popup' => '#F67D42',
	'psr_textontags_popup' => '#555',
	'psr_tags_popup' => '#eee',
	'psr_adoptbtnbg_popup' => '#F67D42',
	'psr_adoptbtntext_popup' => '#fff',
	'psr_pageuparrow_show' => '1',
	'psr_hideoptionssection_default' => '',
	'psr_adoptionformpage_link' => '',
	'psr_shelter_info' => ''
);

/**
 * Render Settings Page
 * @todo Move into its own view!
 * @return string
 */
function pet_sr_options_page() {
   global $pet_sr_options;

	if( isset( $_POST['reset'] ) ) {
		//If reset, set to default fvalues
		$pet_sr_options['psr_header_bg']  = '#F67D42';
		$pet_sr_options['psr_show_hide']  = '#F67D42';
		$pet_sr_options['psr_header_links']  = '#fff';
		$pet_sr_options['psr_options_bg']  = '#fff';
		$pet_sr_options['psr_optionssection_bg']  = '#fff';
		$pet_sr_options['psr_options_title_color']  = '#555';
		$pet_sr_options['psr_options_text_color']  = '#999';
		$pet_sr_options['psr_options_selected_text']  = '#fff';
		$pet_sr_options['psr_options_selected']  = '#F67D42';
		$pet_sr_options['psr_petgrid_bg']  = '#fff';
		$pet_sr_options['psr_pettitle_grid']  = '#F67D42';
		$pet_sr_options['psr_petdesc_grid']  = '#ECF0F1';
		$pet_sr_options['psr_pettags_grid_color']  = '#333';
		$pet_sr_options['psr_petbreedtag_grid_color'] = '#777';
		$pet_sr_options['psr_pageuparrow_grid_color'] = '#999';
		$pet_sr_options['psr_pettitle_popup'] = '#F67D42';
		$pet_sr_options['psr_textontags_popup'] = '#555';
		$pet_sr_options['psr_tags_popup'] = '#eee';
		$pet_sr_options['psr_adoptbtnbg_popup'] = '#F67D42';
		$pet_sr_options['psr_adoptbtntext_popup'] = '#fff';
		update_option('petfinder-search-and-rescue', $pet_sr_options);

	echo '<div class=\"notice notice-warning is-dismissible\"><p>' . __( 'Colors have been reset to original default values', 'wp-petfinder-listing' ) . '</p></div>';
	}

	//If saved, store values
    if( isset( $_POST['save_changes'] ) ) {
        check_admin_referer( 'petfinder-search-and-rescue-update_settings' );

        $pet_sr_options['psr_header_bg']  = trim($_POST['psr_header_bg']);
		$pet_sr_options['psr_show_hide']  = trim($_POST['psr_show_hide']);
		$pet_sr_options['psr_header_links']  = trim($_POST['psr_header_links']);
		$pet_sr_options['psr_options_bg']  = trim($_POST['psr_options_bg']);
		$pet_sr_options['psr_optionssection_bg']  = trim($_POST['psr_optionssection_bg']);
		$pet_sr_options['psr_options_title_color']  = trim($_POST['psr_options_title_color']);
		$pet_sr_options['psr_options_text_color']  = trim($_POST['psr_options_text_color']);
		$pet_sr_options['psr_options_selected_text']  = trim($_POST['psr_options_selected_text']);
		$pet_sr_options['psr_options_selected']  = trim($_POST['psr_options_selected']);
		$pet_sr_options['psr_hideoptionssection_default']  = $_POST['psr_hideoptionssection_default'];
		$pet_sr_options['psr_optionssection_remove']  = $_POST['psr_optionssection_remove'];
		$pet_sr_options['psr_petgrid_bg']  = trim($_POST['psr_petgrid_bg']);
		$pet_sr_options['psr_pettitle_grid']  = trim($_POST['psr_pettitle_grid']);
		$pet_sr_options['psr_petdesc_grid']  = trim($_POST['psr_petdesc_grid']);
		$pet_sr_options['psr_pettags_grid_color']  = trim($_POST['psr_pettags_grid_color']);
        $pet_sr_options['psr_petbreedtag_grid_color'] = trim($_POST['psr_petbreedtag_grid_color']);
		$pet_sr_options['psr_pageuparrow_grid_color'] = trim($_POST['psr_pageuparrow_grid_color']);
		$pet_sr_options['psr_pettitle_popup'] = trim($_POST['psr_pettitle_popup']);
		$pet_sr_options['psr_textontags_popup'] = trim($_POST['psr_textontags_popup']);
		$pet_sr_options['psr_tags_popup'] = trim($_POST['psr_tags_popup']);
		$pet_sr_options['psr_adoptbtnbg_popup'] = trim($_POST['psr_adoptbtnbg_popup']);
		$pet_sr_options['psr_adoptbtntext_popup'] = trim($_POST['psr_adoptbtntext_popup']);
		$pet_sr_options['psr_pageuparrow_show'] = $_POST['psr_pageuparrow_show'];
		$pet_sr_options['psr_adoptionformpage_link'] = trim($_POST['psr_adoptionformpage_link']);

		$pet_sr_options['psr_shelter_info'] = stripslashes($_POST['psr_shelter_info']);
		$pet_sr_options['psr_shelter_info'] = wpautop($pet_sr_options['psr_shelter_info']);

        update_option('petfinder-search-and-rescue', $pet_sr_options);

        echo '<div class=\"notice notice-success is-dismissible\"><p>' . __( 'Your changes have been saved successfully!', 'wp-petfinder-listing' ) . '</p></div>';
    }
    ?>
	<!-- =============================================================
	FORM FOR SETTINGS PAGE
	============================================================= -->
	<div class="wrap" id="psr__options-settings-form">
		<h2 id="psr__options-main-title">Petfinder: Search & Rescue</h2>

		<div class="psr__options-settings-instructions">
			<div class="psr__row-fluid">
				<div class="psr__span12">
					<p>PLACE THIS SHORTCODE ON YOUR PAGE: </p>
					<p>[petfinder_search_rescue api_key="YOUR API KEY" shelter_id="YOUR SHELTER ID" count="100"]</p>
				</div>
			</div>
		</div>


		<form name="psr__the_form" action="options-general.php?page=pet_sr" method="post">

			<?php

			if ( function_exists( 'wp_nonce_field' ) )
			wp_nonce_field( 'petfinder-search-and-rescue-update_settings' );  ?>


			<div class="psr_style-options-section">
				<div class="psr__row-fluid">
					<div class="psr__span12"><h2>Header</h2></div>
				</div><!--end row-->

				<div class="psr__row-fluid">

					<div class="psr__span4">
						<div class="psr_options_title">Header <br/><em>Background Color</em></div>
						<input type="text" class="psr-color-field" name="psr_header_bg" value="<?php echo $pet_sr_options['psr_header_bg']; ?>" />
					</div>

					<div class="psr__span4">
						<div class="psr_options_title">Show/Hide <br/><em>Text Color</em></div>
						<input type="text" class="psr-color-field" name="psr_show_hide" value="<?php echo $pet_sr_options['psr_show_hide']; ?>" />
					</div>

					<div class="psr__span4">
						<div class="psr_options_title">Adopton Form/Info <br/><em>Text Color</em></div>
						<input type="text" class="psr-color-field" name="psr_header_links" value="<?php echo $pet_sr_options['psr_header_links']; ?>" />
					</div>

				</div><!--end row-->
			</div><!--end options sections-->

			<div class="psr_style-options-section">

				<div class="psr__row-fluid">
					<div class="psr__span12"><h2>Pet Options Section</h2></div>
				</div><!--end row-->

				<div class="psr__row-fluid">

					<div class="psr__span4">
						<div class="psr_options_title">Options <br/><em>Background Color</em></div>
						<input type="text" class="psr-color-field" name="psr_options_bg" value="<?php echo $pet_sr_options['psr_options_bg']; ?>" />
					</div>

					<div class="psr__span4">
						<div class="psr_options_title">Option Section <br/><em>Background Color</em></div>
						<input type="text" class="psr-color-field" name="psr_optionssection_bg" value="<?php echo $pet_sr_options['psr_optionssection_bg']; ?>" />
					</div>

					<div class="psr__span4">
						<div class="psr_options_title">Options Titles <br/><em>Text Color</em></div>
						<input type="text" class="psr-color-field" name="psr_options_title_color" value="<?php echo $pet_sr_options['psr_options_title_color']; ?>" />
					</div>


				</div><!--end row-->

				<div class="psr__row-fluid">


					<div class="psr__span4">
						<div class="psr_options_title">All Options <br/><em>Text Color<br/></em></div>
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
						if ($pet_sr_options['psr_hideoptionssection_default']=='on') {
							echo "checked";$pet_sr_options['psr_hideoptionssection_default']='on';
						}?> />Hide options section by default
					</div>
					<!--Remove options section?-->
					<div class="psr__span4">
						<input type="checkbox" name="psr_optionssection_remove" <?php
						if ($pet_sr_options['psr_optionssection_remove']=='on'){
							echo "checked";$pet_sr_options['psr_optionssection_remove']='on';
						}?> />Remove options section completely          		</div>
					</div>

				</div>  <!--end section-->

				<div class="psr_style-options-section">

					<div class="psr__row-fluid">
						<div class="psr__span12"><h2>Pet Grid Section</h2></div>
					</div><!--end row-->

					<div class="psr__row-fluid">

						<div class="psr__span4">
							<div class="psr_options_title">Pet Grid <br/><em>Background Color</em></div>
							<input type="text" class="psr-color-field" name="psr_petgrid_bg" value="<?php echo $pet_sr_options['psr_petgrid_bg']; ?>" />
						</div>

						<div class="psr__span4">
							<div class="psr_options_title">Pet Title <br/><em>Text Color</em></div>
							<input type="text" class="psr-color-field" name="psr_pettitle_grid" value="<?php echo $pet_sr_options['psr_pettitle_grid']; ?>" />
						</div>

						<div class="psr__span4">
							<div class="psr_options_title">Pet Description <br/><em>Background Color</em></div>
							<input type="text" class="psr-color-field" name="psr_petdesc_grid" value="<?php echo $pet_sr_options['psr_petdesc_grid']; ?>" />
						</div>

					</div><!--end row-->
					<div class="psr__row-fluid">

						<div class="psr__span4">
							<div class="psr_options_title">Pet Tags <br/><em>Text Color</em></div>
							<input type="text" class="psr-color-field" name="psr_pettags_grid_color" value="<?php echo $pet_sr_options['psr_pettags_grid_color']; ?>" />
						</div>

						<div class="psr__span4">
							<div class="psr_options_title">Pet Breed Tag <br/><em>Text Color</em></div>
							<input type="text" class="psr-color-field" name="psr_petbreedtag_grid_color" value="<?php echo $pet_sr_options['psr_petbreedtag_grid_color']; ?>" />
						</div>

						<div class="psr__span4">
							<div class="psr_options_title">"Back to Top" Arrow <br/><em>Background Color</em></div>
							<input type="text" class="psr-color-field" name="psr_pageuparrow_grid_color" value="<?php echo $pet_sr_options['psr_pageuparrow_grid_color']; ?>" />
						</div>

					</div><!--end row-->
					<br/>

					<!--show up arrow?-->
					<div class="psr__row-fluid">
						<div class="psr__span6">
							<input type="checkbox" id='psr_pageuparrow_show' name="psr_pageuparrow_show" value='1' <?php checked('1',$pet_sr_options['psr_pageuparrow_show']); ?> />
							Show arrow to scroll back to top </div>
						</div>
					</div><!--end section-->


					<div class="psr_style-options-section">

						<div class="psr__row-fluid">
							<div class="psr__span12"><h2>Pet Pop-Up Section</h2></div>
						</div><!--end row-->

						<div class="psr__row-fluid">
							<div class="psr__span4">

								<div class="psr_options_title">Pet Title<br/><em>Text Color</em></div>
								<input type="text" class="psr-color-field" name="psr_pettitle_popup" value="<?php echo $pet_sr_options['psr_pettitle_popup']; ?>" />
							</div>

							<div class="psr__span4">
								<div class="psr_options_title">Tags <br/><em>Text Color</em></div>
								<input type="text" class="psr-color-field" name="psr_textontags_popup" value="<?php echo $pet_sr_options['psr_textontags_popup']; ?>" />
							</div>

							<div class="psr__span4">
								<div class="psr_options_title">Tags <br/><em>Background Color</em></div>
								<input type="text" class="psr-color-field" name="psr_tags_popup" value="<?php echo $pet_sr_options['psr_tags_popup']; ?>" />
							</div>

						</div><!--end row-->
						<div class="psr__row-fluid">

							<div class="psr__span4">
								<div class="psr_options_title">Adopt Me Button<br/><em>Text Color</em></div>
								<input type="text" class="psr-color-field" name="psr_adoptbtntext_popup" value="<?php echo $pet_sr_options['psr_adoptbtntext_popup']; ?>" />
							</div>

							<div class="psr__span4">
								<div class="psr_options_title">Adopt Me Button <br/><em>Background Color</em></div>
								<input type="text" class="psr-color-field" name="psr_adoptbtnbg_popup" value="<?php echo $pet_sr_options['psr_adoptbtnbg_popup']; ?>" />
							</div>


						</div><!--end row-->
					</div><!--end section-->


					<div class="psr_style-options-section">
						<div class="psr__row-fluid">
							<div class="psr__span12"><h2>Link to Adoption Form</h2></div>
						</div>
						<div class="psr__row-fluid">
							<div class="psr__span6">
								<div class="psr_options_title"> Please type in the full link <br/><em> Example: https://www.mysite.com/adoption-form/</em></div>
								<input class="input-large" type="text" name="psr_adoptionformpage_link" value="<?php echo $pet_sr_options['psr_adoptionformpage_link']; ?>" />
							</div>
						</div>
					</div>

					<div class="psr_style-options-section">

						<!--THE TEXT FIELD FOR ENTERING ADOPTION INFORMATION-->
						<div class="psr__row-fluid">
							<div class="psr__span12"><h2>Adoption Information</h2></div>
						</div>
						<div class="psr__row-fluid">
							<div class="psr__span12">
								<div class="psr_options_title">Here you can list adoption fees, shelter hours, and how the adoption process works</div>
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
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>

				</form>

				<h2>Reset Defaults</h2>
				<form method="post" action="">
					<p class="submit">
						Reset colors to original settings: <input name="reset" class="button button-secondary" type="submit" value="Reset colors">
						<input type="hidden" name="action" value="reset"  />
					</p>
				</form>

			</div>

<?php
}

/**
 * Get Data Functions
 *
 */

/**
 * Get Data from Petfinder API
 * @todo Convert to JSON and add error handling
 *
 * @uses http_build_query
 * @uses @simplexml_load_file
 *
 * @param  string $api_key
 * @param  string $shelter_id
 * @param  int $count
 * @param  string $pet
 * @return array $petfinder_data
 */
function get_petfinder_data( $api_key, $shelter_id, $count, $pet = '' ) {

	$request = get_transient( 'wp_petfinder_data' );

	if ( empty( $pet ) ) {
		$base_url = 'https://api.petfinder.com/shelter.getPets?';
		$params = http_build_query(
			array(
				//'format' => 'json',
				'key' => $api_key,
				'id' => $shelter_id,
				'count' => (int) $count,
				'status' => 'A',
				'output' => 'full',
			)
		);
	}
	else {
		$base_url = 'https://api.petfinder.com/pet.get?';
		$params = http_build_query(
			array(
				//'format' => 'json',
				'key' => $api_key,
				'id' => (int) $pet,
			)
		);
	}

	// Request data from Petfinder
	$petfinder_data = @simplexml_load_file( $base_url . $params );

	// If data not available, don't display errors on page
	if ( false === $petfinder_data ) {
		throw new Exception( 'Petfinder API returned an error' );
	}

	return $petfinder_data;

}

/**
 * Parse Data Functions
 */

 /* =============================================================
 	CONVERT PETFINDER RETURN VALUES
 	Convert abbreviated values into recognizable words for user
  * ============================================================= */

/**
 * Parse Petfinder Return Values - Pet Type
 * @param  string $pet_type
 * @return string $pet_type
 */
function get_pet_type( $pet_type ) {
	if ($pet_type == 'Dog') return 'Dog';
	if ($pet_type == 'Cat') return 'Cat';
	if ($pet_type == 'Small&amp;Furry') return 'Small & Furry';
	if ($pet_type == 'BarnYard') return 'Barnyard';
	if ($pet_type == 'Horse') return 'Horse';
	if ($pet_type == 'Pig') return 'Pig';
	if ($pet_type == 'Rabbit') return 'Rabbit';
	if ($pet_type == 'Reptile') return 'Scales, Fins & Other';
	return 'Not Known';
}

/**
 * Parse Petfinder Return Values - Pet Size
 * @param  string $pet_size
 * @return string $pet_size
 */
function get_pet_size( $pet_size ) {
	if ($pet_size == 'S') return 'Small';
	if ($pet_size == 'M') return 'Medium';
	if ($pet_size == 'L') return 'Large';
	if ($pet_size == 'XL') return 'Extra Large';
	return 'Not Known';
}

/**
 * Parse Petfinder Return Values - Pet Age
 * @param  string $pet_age
 * @return string $pet_age
 */
function get_pet_age( $pet_age ) {
	if ($pet_age == 'Baby') return 'Baby';
	if ($pet_age == 'Young') return 'Young';
	if ($pet_age == 'Adult') return 'Adult';
	if ($pet_age == 'Senior') return 'Senior';
	return 'Not Known';
}

/**
 * Parse Petfinder Return Values - Pet Gender
 * @param  string $pet_gender
 * @return string $pet_gender
 */
function get_pet_gender( $pet_gender ) {
	if ( $pet_gender == 'M' ) return 'Male';
	if ( $pet_gender == 'F' ) return 'Female';
	return 'Not Known';
}

/**
 * Parse Petfinder Return Values - Special Needs & Options
 * @param  string $pet_option
 * @return string $pet_option
 */
function get_pet_option( $pet_option ) {
	if ($pet_option == 'specialNeeds') return 'Special Needs';
	if ($pet_option == 'noDogs') return 'No Dogs';
	if ($pet_option == 'noCats') return 'No Cats';
	if ($pet_option == 'noKids') return 'No Kids';
	if ($pet_option == 'noClaws') return 'No Claws';
	if ($pet_option == 'hasShots') return 'Has Shots';
	if ($pet_option == 'housebroken') return 'Housebroken';
	if ($pet_option == 'altered') return 'Spayed/Neutered';
	//return 'Not Known';
}

 // Convert plain text links to working links
function get_text_links( $text ) {

	// Regex pattern
	$url_filter = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';

	// If any URLs exist, convert them to links
	if ( preg_match( $url_filter, $text, $url ) ) {
		return preg_replace( $url_filter, '<a href="' . $url[0] . '" rel="nofollow">' . $url[0] . '</a>', $text );
	} else {
		return $text;
	}
}

// Convert plain text email addresses to working links
function get_text_emails( $text ) {

	// Regex pattern
	$email_filter = '/([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/';

	// If any emails exist, convert them to links
	if ( preg_match( $email_filter, $text, $email ) ) {
		return preg_replace( $email_filter, '<a href="mailto:' . $email[0] . '">' . $email[0] . '</a>', $text );
	} else {
		return $text;
	}
}

 /* =============================================================
 	PET NAME CLEANUP
 	Remove any special characters from pet names
  * ============================================================= */
/**
 * Sanitize Pet Names
 * @param  string $pet_name
 * @return string $pet_name
 */
function get_pet_name( $pet_name ) {

	// Clean-up pet name
	// $pet_name = array_shift(explode('-', $pet_name)); // Remove '-' from animal names
	// $pet_name = array_shift(explode('(', $pet_name)); // Remove '(...)' from animal names
	// $pet_name = array_shift(explode('[', $pet_name)); // Remove '[...]' from animal names
	//$pet_name = ; // Transform names to lowercase
	$pet_name = wp_strip_all_tags( $pet_name );
	$pet_name = ucwords( strtolower( $pet_name ) );

	// Return pet name
	return $pet_name;

}


 /* =============================================================
 	PET DESCRIPTION CLEANUP
 	Remove inline styling and empty tags from pet descriptions.
  * ============================================================= */

 function get_pet_description($pet_description) {

 	// Remove unwanted styling from pet description
 	$pet_description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $pet_description);// Remove inline styling
 	$pet_description = preg_replace('/<font[^>]+>/', '', $pet_description); // Remove font tag
 	$pet_description_scrub = array('<p></p>' => '', '<p> </p>' => '', '<p>&nbsp;</p>' => '', '<span></span>' => '', '<span> </span>' => '', '<span>&nbsp;</span>' => '', '<span>' => '', '</span>' => '', '<font>' => '', '</font>' => ''); // Define empty tags to remove
 	$pet_description = strtr($pet_description, $pet_description_scrub); // Remove empty tags
 	$pet_description = get_text_links($pet_description); // Convert plain text URLs to links
 	$pet_description = get_text_emails($pet_description); // Convert plain text emails to links
 	$pet_description = '<pre class="pf-description">' . $pet_description . '</pre>'; // Wrap in <pre> tags to preserve formatting

 	// Return pet description
 	return $pet_description;

 }

/**
 * Render Functions
 */

/**
 * Helpers
 */


// ini_set("allow_url_fopen", true);
// ini_set("allow_url_include", true);





/* =============================================================
	PET PHOTO SETTINGS
	Set size and number of pet photos.
	$photo_size options: large, medium, thumb_small, thumb_medium, thumb_large
	$limit: true (default) = only show one. false = show all.
 * ============================================================= */

function get_pet_photos($pet, $photo_size = 'medium', $limit = true) {

	// Set size
	if ( $photo_size == 'large' ) {
		$pet_photo_size = 'x';
	}
	if ( $photo_size == 'medium' ) {
		$pet_photo_size = 'pn';
	}
	if ( $photo_size == 'thumb_small' ) {
		$pet_photo_size = 't';
	}
	if ( $photo_size == 'thumb_medium' ) {
		$pet_photo_size = 'pnt';
	}
	if ( $photo_size == 'thumb_large' ) {
		$pet_photo_size = 'fpm';
	}

	// Define Variables
	$pet_photos = '';

	// If pet has photos
	if( count($pet->media->photos) > 0 ) {

		// Get Pet Name
		$pet_name = get_pet_name($pet->name);

		// For each photo, get photos that match the set size
		foreach ( $pet->media->photos->photo as $photo ) {
			foreach( $photo->attributes() as $key => $value ) {
				if ( $key == 'size' ) {
					if ( $value == $pet_photo_size ) {

						// If limit set on number of photos, get the first photo
						if ( $limit == true ) {
							$pet_photos = '<p><img alt="Photo of ' . $pet_name . '" src="' . $photo . '"></p>';
							break 2;
						}

						// Otherwise, get all of them
						else {
							$pet_photos .= '<li><img alt="Photo of ' . $pet_name . '" src="' . $photo . '"></li>';
						}

					}
				}
			}
		}
	}

	// If no photos have been uploaded for the pet
	else {
		$pet_photos = '<p>No Photo Available</p>'; // Add a fallback/placeholder photo
	}

	return $pet_photos;

}

/* =============================================================
	PET VALUE CONDENSER
	Removes spacing and special characters from strings.
 * ============================================================= */

function pet_value_condensed($pet_value) {

	// Define characters to remove and remove them
	$condense_list = array('(' => '', ')' => '', '&' => '-', '/' => '-', '  ' => '-', ' ' => '-');
	$pet_value = strtr($pet_value, $condense_list);

	// Return condensed list
	return $pet_value;

}


/* =============================================================
	PET TYPE LIST
	Example: Dog, Dat, Horse, Rabbit, etc.)
 * ============================================================= */

function get_type_list($pets) {

	// Define Variables
	$types = '';
	$type_list = '';

	// Create a list of types of pets
	foreach( $pets as $pet ) {
		$types .= get_pet_type($pet->animal) . "|";
	}

	// Remove duplicates, convert into an array, and alphabetize
	$types = array_filter(array_unique(explode('|', $types)));
	asort($types);

	// For each type of pet
	foreach( $types as $type ) {

		// Create a condensed version without spaces or special characters
		$type_condensed = pet_value_condensed($type);

		// Create a list
		$type_list .= '<li class="btn " data-group='.$type_condensed.'><span>'.$type_condensed.'</span></li>';
	}


	//////////////RETURN THE TYPE LIST (including start of psr_container-pets container)///////
	///////////////////////////////////////////////////////////////////////////////////////////
	//grab admin option vars
	add_option("petfinder-search-and-rescue", $pet_sr_options);
	$pet_sr_options = get_option('petfinder-search-and-rescue');

	$startContainerOutput = '';

	$startContainerOutput .= '<div class="psr_container-pets">';
	//all pet options
	if ($pet_sr_options['psr_optionssection_remove']=='on' || $pet_sr_options['psr_hideoptionssection_default']=='on'){
	    $startContainerOutput .= '<div id="all-pet-options" style="display:none;">';
	}
	else {
		$startContainerOutput .= '<div id="all-pet-options">';
	}

	$startContainerOutput .= '<div class="psr__row-fluid">';

	$startContainerOutput .= '<div class="psr__span3 petOption-section">';
	$startContainerOutput .= '<p class="filter__label">Type</p>';
	$startContainerOutput .= '<ul class="filter-options psr__btn-group OR-psr__btn-group">';
	$startContainerOutput .= $type_list;
	$startContainerOutput .= '<li class="btn  allbtn" data-group="all"><span class="psr__hoverme">Any Type</span></li>';
	$startContainerOutput .= '</ul>';//end btn-group
	$startContainerOutput .= '</div>';//end span

	return $startContainerOutput;
}

/* =============================================================
	CUSTOM STYLES
	Grab vars entered on settings page
	Display new color values in style tag
 * ============================================================= */
function getHeader(){
	//grab admin option vars
	add_option("petfinder-search-and-rescue", $pet_sr_options);
$pet_sr_options = get_option('petfinder-search-and-rescue');

// see if arrow is hidden
 if ($pet_sr_options['psr_pageuparrow_show']==1){
      $arrowdiv= '<div class="psr__hoverme p_sr-pagetop-arrow"><span class="ico-psr_up"></span></div>';
  }
  else{$arrowdiv= '';}
	$startPluginOutput='';
	$startPluginOutput.= '<div id="petfinder_search_rescue_container">';
	//PRELOADER
	$startPluginOutput.= '<div id="psr__preloader">';
  	$startPluginOutput.= 'Loading our adoptable pets';
    $startPluginOutput.= '<div id="preloader-icon"></div>';
	$startPluginOutput.= '</div>';
$startPluginOutput.= '<link rel="stylesheet" type="text/css" href= "' . plugins_url("css/psr_filter_styles.css", __FILE__ ).'">';
  $startPluginOutput.= '<link rel="stylesheet" type="text/css" href="' . plugins_url( "css/psr_rows_styles.css", __FILE__ ).'">';
$startPluginOutput.= '<script type="text/javascript" src="'.plugins_url( "js/modernizr.custom.min.js", __FILE__ ).'"></script>';
  $startPluginOutput.= '<div data-twttr-rendered="true" cz-shortcut-listen="true">';
//set colors
$startPluginOutput.= '<style>';
$startPluginOutput.= '/**STYLE OPTIONS SECTION**/';
$startPluginOutput.= '.psr__header, #psr__preloader{background-color: ' . $pet_sr_options['psr_header_bg'] . '!important;border-bottom:1px solid ' . $pet_sr_options['psr_options_bg'] . '!important;}';
$startPluginOutput.= '#toggle-petOptions{color: ' . $pet_sr_options['psr_show_hide'] . '!important; background-color: ' . $pet_sr_options['psr_options_bg'] . '!important;}';
$startPluginOutput.= '.psr__header .psr__header-icon, #petfinder_search_rescue_container .psr__header .psr__header-icon a, #psr__preloader{color: ' . $pet_sr_options['psr_header_links'] . '!important;}';

$startPluginOutput.= 'p.filter__label{color:' . $pet_sr_options['psr_options_title_color'] . '!important;}';
$startPluginOutput.= '#all-pet-options{background-color: ' . $pet_sr_options['psr_options_bg'] . '!important;}';
$startPluginOutput.= '.petOption-section{background-color:' . $pet_sr_options['psr_optionssection_bg'] . '!important; border:1px solid ' . $pet_sr_options['psr_options_text_color'] . '!important;}';
$startPluginOutput.= '.psr__btn-group .btn span{color:' . $pet_sr_options['psr_options_text_color'] . '!important;}';
$startPluginOutput.= '.petOption-section .btn-group .btn span{background-color:' . $pet_sr_options['psr_optionssection_bg'] . '!important;color:' . $pet_sr_options['psr_options_text_color'] . $startPluginOutput.= '!important;}';
$startPluginOutput.= '.psr__btn-group .btn.active span, .psr__btn-group .btn:hover span, .psr__btn-group .onlyOption span{background-color:' . $pet_sr_options['psr_options_selected'] . '; color:' . $pet_sr_options['psr_options_selected_text'] . '!important;}';

$startPluginOutput.= '/**STYLE GRID SECTION**/';
$startPluginOutput.= '.psr_container-pets{background-color: ' . $pet_sr_options['psr_petgrid_bg'] . '!important;}';
$startPluginOutput.= '.picture-item .picture-item__title, .window-popup-title{color: ' . $pet_sr_options['psr_pettitle_grid'] . '!important;}';
$startPluginOutput.= '.picture-item .picture-item__inner{ background-color: ' . $pet_sr_options['psr_petdesc_grid'] . '!important;}';
$startPluginOutput.= '.picture-item .picture-item__tags{color: ' . $pet_sr_options['psr_pettags_grid_color'] . '!important;}';
$startPluginOutput.= '.picture-item .picture-item__tags .item__breed-tag{color: ' . $pet_sr_options['psr_petbreedtag_grid_color'] . '!important;}';
$startPluginOutput.= '.p_sr-pagetop-arrow{background-color: ' . $pet_sr_options['psr_pageuparrow_grid_color'] . '!important;}';

$startPluginOutput.= '/**STYLE POP-UP SECTION**/';
$startPluginOutput.= '.window-popup #pet-attributes span, .window-popup #pet-options span{background-color: ' . $pet_sr_options['psr_tags_popup'] . '!important;color:' . $pet_sr_options['psr_textontags_popup'] . '!important;}';
$startPluginOutput.= '.window-popup .window-popup-title{color: ' . $pet_sr_options['psr_pettitle_popup'] . '!important;}';
$startPluginOutput.= '.window-popup #pet-adopt-btn .psr__hoverme a.psr__adoptformlink, #pet-adopt-btn .psr__hoverme{color: ' . $pet_sr_options['psr_adoptbtntext_popup'] .'!important; background-color: ' . $pet_sr_options['psr_adoptbtnbg_popup'] .'!important;}';
$startPluginOutput.= '</style>';

  //BG FOR ALL POPUPS
  $startPluginOutput.= '<div class="popup-bg"></div>';
  //POPUP - CLICK ANY PET
  $startPluginOutput.= '<div id="indiepet-popup" class="window-popup">';
  $startPluginOutput.= '<div id="indiepet-popup-topbtns" class="window-popup-topbtns">';
  $startPluginOutput.= '<span class="ico-psr_left psr__hoverme" id="indiepet-popup-previous"></span>';
  $startPluginOutput.= '<span class="ico-psr_right psr__hoverme" id="indiepet-popup-next"></span>';
  $startPluginOutput.= '<span class="ico-psr_close psr__hoverme window-popup-close"></span>';
  $startPluginOutput.= '</div>';
  //photos
  $startPluginOutput.= '<div id="pet-multiphotos"></div>';
  //adopt btn
  $startPluginOutput.= '<div id="pet-adopt-btn">';
  $startPluginOutput.= '<div class="psr__hoverme pet-adopt-btn-inner">';
  $startPluginOutput.= '<span id="adoptionformlink" data-group="'.$pet_sr_options['psr_adoptionformpage_link'].'"></span>';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '<div class="my-petInfoAll">';
  //pet name
  $startPluginOutput.= '<div id="indiepet-popup-title" class="window-popup-title"></div>';
  //pet attributes
  $startPluginOutput.= '<div id="pet-attributes"></div>';
  //pet options
  $startPluginOutput.= '<div id="pet-options"></div>';
  //description
  $startPluginOutput.= '<div id="pet-description"></div>';
  //petfinder url
  $startPluginOutput.= '<div id="pet-petfinder_url"></div>';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '</div>';//END PET POPUP
  //START ADOPTION INFO POPUP
  $startPluginOutput.= '<div id="adoptinfo-popup" class="window-popup">';
  $startPluginOutput.= '<div class="window-popup-title">Adoption Information</div>';
  $startPluginOutput.= '<div id="indiepet-popup-topbtns" class="window-popup-topbtns">';
  $startPluginOutput.= '<span class="ico-psr_close psr__hoverme window-popup-close"></span>';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '<pre id="adoptinfo-popup-inner" class="window-popup-inner">'.$pet_sr_options['psr_shelter_info'].'</pre>';
  $startPluginOutput.= '</div>';

  $startPluginOutput.= '<div id="psr__main">';
  $startPluginOutput.= '<div class="psr__header">';
  $startPluginOutput.= '<div class="psr__row-fluid">';
  //show options
  if ($pet_sr_options['psr_optionssection_remove']!='on'){
  $startPluginOutput.= '<div class="psr__span4" id="toggle-petOptions-holder">';
  $startPluginOutput.= '<div id="toggle-petOptions" class="psr__hoverme toggle-petOptions">';
  if ($pet_sr_options['psr_hideoptionssection_default']=='on'){
  $startPluginOutput.= '<span class="toggle-petOptions-text">Show Options</span>';
  $startPluginOutput.= '<span class="ico-psr_show"></span>';
  }
  else{
  $startPluginOutput.= '<span class="toggle-petOptions-text">Hide Options</span>';
  $startPluginOutput.= '<span class="ico-psr_hide"></span>';
  }
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '</div>';
  }
  else {
	  $startPluginOutput.= '<div class="psr__span4"></div>';
  }
  $startPluginOutput.= '<div class="psr__span4" id="search-icon-container">';
  $startPluginOutput.= '<span id="search-icon"><span class="ico-psr_search"></span></span>';
  $startPluginOutput.= '<input class="filter__search js-shuffle-search" type="text" placeholder="Search pet name...">';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '<div class="psr__span4">';
  $startPluginOutput.= '<div class="psr__header-icon psr__hoverme" id="psr__adoptform-btn" title="Adoption Form">';
  $startPluginOutput.= '<a href="'.$pet_sr_options['psr_adoptionformpage_link'].'">';
  $startPluginOutput.= '<span class="ico-psr_application"></span>';
  $startPluginOutput.= '<span class="psr__header-title">Adoption Form</span>';
  $startPluginOutput.= '</a>';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '<div class="psr__header-icon psr__hoverme" id="psr__adoptinfo-btn" title="Adoption Information">';
  $startPluginOutput.= '<span class="ico-psr_info"></span>';
  $startPluginOutput.= '</div>';
  $startPluginOutput.= '</div>';//end SPAN
  $startPluginOutput.= '</div>';//end top row
  $startPluginOutput.= '</div>';//end header
  $startPluginOutput.= $arrowdiv;//up arrow to go back to top of page
  return $startPluginOutput;
}



/* =============================================================
	BREED LIST
	List of available breeds.
 * ============================================================= */

function get_breed_list($pets) {

	// Define Variables
	$breeds = '';
	$breed_list = '';

	// Get a list of breeds for each pet
	foreach( $pets as $pet ) {
		foreach( $pet->breeds->breed as $pet_breed ) {
			$breeds .= $pet_breed . "|";
		}
	}

	// Remove duplicates, convert into an array and alphabetize
	$breeds = array_filter(array_unique(explode('|', $breeds)));
	asort($breeds);

	// For each breed
	foreach( $breeds as $breed ) {

		// Create a condensed version without spaces or special characters
		$breed_condensed = pet_value_condensed($breed);

		// Create a list
		$breed_list .= '<li class="btn " data-group='.$breed_condensed.'><span>'.$breed_condensed.'</span></li>';
	}

	//////////////RETURN THE BREED LIST (including start of psr__grid)///////
	///////////////////////////////////////////////////////////////////////////////////////////
	$breedListOutput = '';

	$breedListOutput .= '<div class="psr__span7 breedOption-section petOption-section">';
    $breedListOutput .= '<p class="filter__label">Breed</p>';
    $breedListOutput .= '<ul class="filter-options psr__btn-group">';
	$breedListOutput .= '<li class="btn  allbtn" data-group="all"><span class="psr__hoverme">All Breeds</span></li>';
	$breedListOutput .= $breed_list;
	$breedListOutput .= '</ul>';//end btn-group
	$breedListOutput .= '</div>';//end SPAN

	$breedListOutput .= '<div class="psr__span1">';
	$breedListOutput .= '<ul class="filter-options psr__btn-group">';
	$breedListOutput .= '<li class="btn  viewallbtn" data-group="all"><span class="psr__hoverme">Reset</span></li>';
	$breedListOutput .= '</ul>';
	$breedListOutput .= '</div>';//end span
	$breedListOutput .= '</div>';//end row
	$breedListOutput .= '</div>';//end pet options

	$breedListOutput .= '<div id="noPetsCriteria-msg" class="noPetsFound-msg">Sorry, no pets were found with your search criteria.  Keep looking :)</div>';
	$breedListOutput .= '<div id="noPetsName-msg" class="noPetsFound-msg">Sorry, no pets were found with that name.  Keep looking :)</div>';

	//START GRID
	$breedListOutput .= '<div id="psr__grid" class="psr__row-fluid psr__m-row shuffle--container shuffle--fluid shuffle" style="transition: height 250ms ease-out; -webkit-transition: height 250ms ease-out; height: 1220px;">';
		return $breedListOutput;
	}


/* =============================================================
	SIZE LIST
	List of available size of pets.
 * ============================================================= */

function get_size_list($pets) {

	// Define Variables
	$sizes = '';
	$size_list = '';

	// Create a list of pet sizes
	foreach( $pets as $pet ) {
		$sizes .= get_pet_size($pet->size) . "|";
	}

	// Remove duplicates, convert into an array, alphabetize and reverse list order
	$sizes = array_filter(array_unique(explode('|', $sizes)));
	asort($sizes);
	$sizes = array_reverse($sizes);

	// For each size of pet
	foreach( $sizes as $size ) {

		// Create a condensed version without spaces or special characters
		$size_condensed = pet_value_condensed($size);

		// Create a list
		$size_list .= '<li class="btn " data-group='.$size_condensed.'><span class="psr__hoverme">'.$size_condensed.'</li>';
	}

	////////////////////////////////RETURN THE SIZE LIST //////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////
	$sizeListOutput = '';

	$sizeListOutput .= '<div class="psr__span3 petOption-section">';
    $sizeListOutput .= '<p class="filter__label">Size</p>';
    $sizeListOutput .= '<ul class="filter-options psr__btn-group">';
	$sizeListOutput .= $size_list;
	$sizeListOutput .= '<li class="btn  allbtn" data-group="all"><span class="psr__hoverme">Any Size</span></li>';
	$sizeListOutput .= '</ul>';//end btn-group
	$sizeListOutput .= '</div>';//end span

	return $sizeListOutput;

}


/* =============================================================
	AGE LIST
	List of available pet ages.
 * ============================================================= */

function get_age_list($pets) {

	// Define Variables
	$ages = '';
	$age_list = '';

	// Create a list of pet ages
	foreach( $pets as $pet ) {
		$ages .= get_pet_age($pet->age) . "|";
	}

	// Remove duplicates, convert into an array and reverse list order
	$ages = array_reverse(array_filter(array_unique(explode('|', $ages))));

	// For each pet age
	foreach( $ages as $age ) {

		// Create a condensed version without spaces or special characters
		$age_condensed = pet_value_condensed($age);

		// Create a list
		$age_list .= '<li class="btn " data-group='.$age_condensed.'><span class="psr__hoverme">'.$age_condensed.'</span></li>';
	}

	//////////////////////////////////RETURN THE AGE LIST /////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////
	$ageListOutput = '';

	$ageListOutput .= '<div class="psr__span3 petOption-section">';
    $ageListOutput .= '<p class="filter__label">Age</p>';
    $ageListOutput .= '<ul class="filter-options psr__btn-group">';
	$ageListOutput .= $age_list;
	$ageListOutput .= '<li class="btn  allbtn" data-group="all"><span class="psr__hoverme">Any Age</span></li>';
	$ageListOutput .= '</ul>';//end btn-group
	$ageListOutput .= '</div>';//end span

	return $ageListOutput;
}

/* =============================================================
	GENDER LIST
	List of available pet genders.
 * ============================================================= */

function get_gender_list($pets) {

	// Define Variables
	$genders = '';
	$gender_list = '';

	// Create a list available pet genders
	foreach( $pets as $pet ) {
		$genders .= get_pet_gender($pet->sex) . "|";
	}

	// Remove duplicates and convert into an array
	$genders = array_filter(array_unique(explode('|', $genders)));

	// For each pet gender
	foreach( $genders as $gender ) {

		// Create a condensed version without spaces or special characters
		$gender_condensed = pet_value_condensed($gender);

		// Create a list
		$gender_list .= '<li class="btn " data-group='.$gender_condensed.'><span class="psr__hoverme">'.$gender_condensed.'</span></li>';
	}

	/////////////////////////////////////RETURN THE GENDER LIST ////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////
	$genderListOutput = '';

	$genderListOutput .= '<div class="psr__span3 petOption-section">';
	$genderListOutput .= '<p class="filter__label">Gender</p>';
	$genderListOutput .= '<ul class="filter-options psr__btn-group OR-psr__btn-group">';
	$genderListOutput .= $gender_list;
	$genderListOutput .= '<li class="btn  allbtn" data-group="all"><span class="psr__hoverme">Both</span></li>';
	$genderListOutput .= '</ul>';//end btn-group
	$genderListOutput .= '</div>';//end span
	$genderListOutput .= '</div>';//end row

	return $genderListOutput;
}


/* =============================================================
	OPTIONS & SPECIAL NEEDS LIST
	Used for Looing for... section:
	Cat friendly, dog friendly, kid friendly, special needs
	Rest of options/epcial needs displayed in tags in popup
	such as spayed, hasshots
 * ============================================================= */

function get_options_list($pets) {

	// Define Variables
	$options = '';
	$options_list = '';

	// Create a list of pet options and special needs
	foreach( $pets as $pet ) {

		foreach( $pet->options->option as $pet_option ) {
			$options .= get_pet_option($pet_option) . "|";
		}
	}


	// Remove duplicates, convert into an array and reverse list order
	$options = array_reverse(array_filter(array_unique(explode('|', $options))));

	// For each pet option
	foreach( $options as $option ) {

		// Create a condensed version without spaces or special characters
		$option_condensed = pet_value_condensed($option);

		// Create a list (only use if want to list all special needs/options as filter btns)
		//$options_list .= '<li class="btn " data-group='.$option_condensed.'><span>'.$option_condensed.'</span></li>';
	}

//////////////DISPLAY OPTIONS/SPECIAL NEEDS LIST - NOT INCLUDED: SPAYED/UP TO DTE ON SHOTS IN TAGS///////
	///////////////////////////////////////////////////////////////////////////////////////////
	$optionsListOutput = '';

	$optionsListOutput .= '<div class="psr__row-fluid">';
	$optionsListOutput .= '<div class="psr__span3 petOption-section">';
	$optionsListOutput .= '<p class="filter__label">Looking for...</p>';
	$optionsListOutput .= '<ul class="filter-options psr__btn-group lookingFor-optionGroup">';
	$optionsListOutput .= '<li class="btn " data-group="dog-friendly"><span class="psr__hoverme">Kid Friendly</span></li>';
	$optionsListOutput .= '<li class="btn " data-group="cat-friendly"><span class="psr__hoverme">Cat Friendly</span></li>';
	$optionsListOutput .= '<li class="btn " data-group="kid-friendly"><span class="psr__hoverme">Dog Friendly</span></li>';
	$optionsListOutput .= '<li class="btn " data-group="special-needs"><span class="psr__hoverme">Special Needs</span></li>';
	$optionsListOutput .= '<li class="btn  allbtn" data-group="all"><span class="psr__hoverme">All</span></li>';
	$optionsListOutput .= '</ul>';//end btn-group
	$optionsListOutput .= '</div>';//end span

	return $optionsListOutput;
}

/* =============================================================
	END LISTS
 * ============================================================= */




/* =============================================================
	PET OPTIONS LIST
	Get a list of options for a specific pet.
 * ============================================================= */

//gets all options/special needs(used for one pet popup)
function get_pet_options_list($pet) {

	// Define Variables
	$pet_options = '';

	// For each option
	foreach( $pet->options->option as $option ) {

		// Get option value
		$get_option = get_pet_option($option);

		// If option value has been set
		if ( $get_option != '' ) {
			$pet_options .= '<span>' . $get_option .'</span>';
		}

	}

	return $pet_options;

}


/* =============================================================
	CONVERT SPECIAL CLASSES - ONLY USED FOR FILTERING
	Example no cats = !cat-friendly
	Used for LooKing for... section:
	Cat friendly, dog friendly, kid friendly, special needs
 * ============================================================= */
function get_pet_options_list_classes($pet) {

	// Define Variables
	$pet_optionClasses = '';
	//initially set vars to default
	$is_dog_friendly=true;
	$is_cat_friendly=true;
	$is_kid_friendly=true;
	$is_special_needs=false;

	// For each option
	foreach( $pet->options->option as $option ) {
		//only display certain options/special needs for classes
			if ($option == "noDogs"){
					$is_dog_friendly=false;
			}
			if ($option == "noCats"){
					$is_cat_friendly=false;
			}
			if ($option == "noKids"){
					$is_kid_friendly=false;
			}
			if ($option == "specialNeeds"){
					$is_special_needs=true;
			}
	}//end each
		//put classes altogether based on options
			if ($is_dog_friendly == true){
			$pet_optionClasses .='dog-friendly ';
			}
			if ($is_cat_friendly == true){
			$pet_optionClasses .='cat-friendly ';
			}
			if ($is_kid_friendly == true){
			$pet_optionClasses .='kid-friendly ';
			}
			if ($is_special_needs == true){
			$pet_optionClasses .='special-needs ';
			}
	return $pet_optionClasses;
}

/* =============================================================
	CONVERT SPECIAL DATA-GROUPS - ONLY USED FOR FILTERING
	Example no cats = !cat-friendly
	Used for LooKing for... section:
	Cat friendly, dog friendly, kid friendly, special needs
 * ============================================================= */
function get_pet_options_list_data_groups($pet) {

	// Define Variables
	$pet_optionDataGroups = '';
	$is_dog_friendly=true;
	$is_cat_friendly=true;
	$is_kid_friendly=true;
	$is_special_needs=false;

	// For each option
		foreach( $pet->options->option as $option ) {
		//only display certain options/special needs for classes
			if ($option == "noDogs"){
					$is_dog_friendly=false;
			}
			if ($option == "noCats"){
					$is_cat_friendly=false;
			}
			if ($option == "noKids"){
					$is_kid_friendly=false;
			}
			if ($option == "specialNeeds"){
					$is_special_needs=true;
			}
	}//end each
			//put classes altogether based on options
			if ($is_dog_friendly == true){
			$pet_optionDataGroups .='&quot;dog-friendly&quot;,';
			}
			if ($is_cat_friendly == true){
			$pet_optionDataGroups .='&quot;cat-friendly&quot;,';
			}
			if ($is_kid_friendly == true){
			$pet_optionDataGroups .='&quot;kid-friendly&quot;,';
			}
			if ($is_special_needs == true){
			$pet_optionDataGroups .='&quot;special-needs&quot;,';
			}
	return $pet_optionDataGroups;
}


/* =============================================================
	GET ALL PETS
	Get a list of all available pets.
 * ============================================================= */

function get_all_pets($pets) {

	foreach( $pets as $pet ) {

		// Define Variables
		$pet_name = get_pet_name($pet->name);
		$pet_type = get_pet_type($pet->animal);
		$pet_size = get_pet_size($pet->size);
		$pet_age = get_pet_age($pet->age);
		$pet_gender = get_pet_gender($pet->sex);
		$pet_options = get_pet_options_list($pet);
		$pet_optionClasses = get_pet_options_list_classes($pet);
		$pet_optionDataGroups = get_pet_options_list_data_groups($pet);
		$pet_description = get_pet_description($pet->description);
		$pet_photo_thumbnail = get_pet_photos($pet, 'medium');
		$pet_photo_all = get_pet_photos ($pet, 'large', false);
		$pet_more_url = get_permalink() . '?view=pet-details&id=' . $pet->id;
		$pet_pf_url = 'https://www.petfinder.com/petdetail/' . $pet->id;

		// Create breed classes
		$pet_breeds_condensed = '';
		foreach( $pet->breeds->breed as $breed ) {
			$pet_breeds_condensed .= pet_value_condensed($breed) . ' ';
		}

		// Create options classes
		$pet_options_condensed = '';
		foreach( $pet->options->option as $option ) {
			$option = get_pet_option($option);
			if ( $option != '' ) {
				$pet_options_condensed .= pet_value_condensed($option) . ' ';
			}
		}

/* =============================================================
 * PICTURE-ITEM CONTAINER FOR EACH PET
 * Contains Pet title, photo, description, tags
 * Takes attributes and places them into classes/data-groups
 * ============================================================= */
		$pet_list = '';

		$pet_list .=    '<div class="psr__span2 picture-item shuffle-item filtered ' . pet_value_condensed($breed) . ' ' . $pet_optionClasses . ' ' . pet_value_condensed($pet_type) . ' ' . pet_value_condensed($pet_size) . ' ' . pet_value_condensed($pet_age) . ' ' . pet_value_condensed($pet_gender) .'" data-groups="[&quot;'. pet_value_condensed($breed) . '&quot;,' . $pet_optionDataGroups . '&quot;'. pet_value_condensed($pet_type) . '&quot;,&quot;' . pet_value_condensed($pet_size) . '&quot;,&quot;' . pet_value_condensed($pet_age) . '&quot;,&quot;' . pet_value_condensed($pet_gender) .'&quot;]" data-title="'. $pet_name .'">

  <div class="picture-item__inner">
    <div class="picture-item__glyph">' .$pet_photo_thumbnail . '</div>
	<div class="picture-item__details clearfix">
      <div class="picture-item__title">'.$pet_name.'</div>
	  <div class="picture-item__tags">
	  '. pet_value_condensed($pet_type) .', '. pet_value_condensed($pet_age) .', '. pet_value_condensed($pet_gender) .', '. pet_value_condensed($pet_size) .'<p class="item__breed-tag">'. pet_value_condensed($breed) .'</p></div>
	  <!--hidden pet information-->
	  <div class="picture-item__more-details" style="display:none">

			<div class="my-pet-options">' . $pet_options . '</div>' .
			'<div class="my-pet-attributes"><span>'. pet_value_condensed($pet_type) .'</span><span>'. pet_value_condensed($pet_age) .'</span><span>'. pet_value_condensed($pet_gender) .'</span><span>'. pet_value_condensed($pet_size) .'</span><span>'. pet_value_condensed($breed) .'</span></div>'.
			'<div class="my-pet-petfinder_url"><a href="' . $pet_pf_url . '">Petfinder Link</a></div>' .
			'<div class="my-pet-description">' . $pet_description . '</div>' .
			'<div class="my-pet-photos"><ul id="image_slider">' . $pet_photo_all . '</ul><span class="nvgt ico-psr_previous" id="prev"></span>
				<span class="nvgt ico-psr_next" id="next"></span></div>
			</div><!--end details--></div><!--end inner--></div><!--end picture item--></div>';
	}
	$pet_list .= ' <div class="psr__span1 psr__m-span1 shuffle__sizer"></div>
    </div>
  </div>
</div>

<!-- Filter js files -->
<script type="text/javascript" src="' .plugins_url("js/jquery.shuffle.js", __FILE__ ).'"></script>
<script type="text/javascript" src="' .plugins_url("js/homepage.js", __FILE__ ).'"></script>
</div><div class="poweredByPetfinder">Powered by <a href="https://www.petfinder.com/">Petfinder.com</a></div></div><!--end petfinder search & rescue container--></div>';


	// Return pet list
	return $pet_list;

}



/* =============================================================
	EXTRACT SHORTCODE
	Vars are shelter id, api key, and count.
 * ============================================================= */

function display_petfinder_search_rescue($atts) {

	// Extract shortcode values
	extract(shortcode_atts(array(
		'api_key' => '',
		'shelter_id' => '',
		'count' => '20'
	), $atts));

	// Define variables
	$petfinder_list = '';


// Display a list of all available pets


		// Access Petfinder Data
		$petfinder_data = get_petfinder_data($api_key, $shelter_id, $count);

		// If the API returns without errors
		if( $petfinder_data->header->status->code == '100' ) {

			// If there is at least one animal
			if( count( $petfinder_data->pets->pet ) > 0 ) {

				$pets = $petfinder_data->pets->pet;

				// Compile information that you want to include
				$petfinder_list =   getHeader() .
									get_type_list($pets).
									get_age_list($pets) .
									get_size_list($pets) .
									get_gender_list($pets) .
									get_options_list($pets) .
									get_breed_list($pets) .
									get_all_pets($pets);


			}


/* =============================================================
	IF NOT ANIMALS AVAILABLE FOR ADOPTION
 * ============================================================= */
			else {
				$petfinder_list = '<div id="petfinder_search_rescue_container" style="clear:both;text-align:center;"><p><strong>We don\'t have any pets available for adoption at this time. Sorry! Please check back soon.</strong></p></div>';
			}
		}

/* =============================================================
	IF ERROR CODE OR PETFINDER IS DOWN
 * ============================================================= */
		else {
			$petfinder_list = '<div id="petfinder_search_rescue_container" style="clear:both;text-align:center;"><p id="petfinder_down_message"><strong>Petfinder is down for the moment. Please check back shortly.</strong></p></div>';
		}


	return $petfinder_list;

}
add_shortcode( 'petfinder_search_rescue','display_petfinder_search_rescue' );

?>
