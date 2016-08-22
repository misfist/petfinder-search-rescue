<?php
/**
 * WordPress Petfinder Listing Shortcode
 *
 * @package    WP_Petfinder_Listing
 * @subpackage WP_Petfinder_Listing\Public
 * @since      1.02.1
 * @license    GPL-2.0+
 */

/**
 * Shortcode
 * @param  array $atts
 * @return $petfinder_list
 */
function display_petfinder_listing( $atts ) {

	// Extract shortcode values
	extract( shortcode_atts( array(
		'api_key' => '',
		'shelter_id' => '',
		'count' => '20'
	), $atts ) );

	// Define variables
	$petfinder_list = '';

	// Access Petfinder Data
	$petfinder_data = get_petfinder_data( $api_key, $shelter_id, $count );

    if( !empty( $petfinder_data ) ) {

        $pets = $petfinder_data;

        // Compile information that you want to include
        $petfinder_list =   getHeader() .
                            get_type_list( $pets ) .
                            get_age_list( $pets ) .
                            get_size_list( $pets ) .
                            get_gender_list( $pets ) .
                            get_options_list( $pets ) .
                            get_breed_list( $pets ) .
                            get_all_pets( $pets );

        // echo '<pre>';
        // var_dump( 'get_type_list', get_type_list( $pets ) );
        // echo '</pre>';

    } else {
        throw new Exception ( 'The API returned without any records.' );
        return;
    }

	return $petfinder_list;

}
add_shortcode( 'wp-petfinder-listing','display_petfinder_listing' );

?>
