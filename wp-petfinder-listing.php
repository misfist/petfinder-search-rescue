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
 * Version:         1.02.1
 *
 * @package         WP_Petfinder_Listing
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

WordPress Petfinder Listing uses both Petfinder.com API as well as the Shuffle.js plugin v2.1.1 http://vestride.github.io/Shuffle/ By @Vestride.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Widget
 */
include( 'widget/petfinder-search-rescue-widget.php' );

/**
 * Load Shortcode
 */
include( 'public/wp-petfinder-listing-shortcode.php' );


/**
 * Variables
 */
$pluginName = plugin_basename( __FILE__ );

define( 'PETFINDER_BASE_URL', esc_url( 'https://api.petfinder.com/shelter.getPets?' ) );
define( 'PETFINDER_SINGLE_PET_URL', 'http://api.petfinder.com/pet.get?' );

/**
 * Enqueue Admin Scripts
 */
function wp_petfinder_listing_enqueue_admin_scripts(  ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'psr-script-handle', plugins_url( '/js/my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    wp_enqueue_script( 'jquery' );

    if( is_admin() ) {
        wp_enqueue_style( 'wp-petfinder-listing-admin', plugins_url('css/psr_rows_styles.css', __FILE__ ) );
    }

}
add_action( 'admin_enqueue_scripts', 'wp_petfinder_listing_enqueue_admin_scripts' );

/**
 * Enqueue Public Scripts
 * Load scripts and styles the right way
 *
 * @uses wp_enqueue_style
 * @uses wp_enqueue_script
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 *
 * @return void
 */
function wp_petfinder_listing_enqueue_public_scripts() {
    wp_enqueue_style( 'wp-petfinder-listing-filter', plugins_url( __FILE__ ) . 'public/assets/css/psr_filter_styles.css' );
    wp_enqueue_style( 'wp-petfinder-listing-rows', plugins_url( __FILE__ ) . 'public/assets/css/psr_rows_styles.css' );
    wp_enqueue_script( 'wp-petfinder-listing-public', plugins_url( __FILE__ ) . 'public/assets/js/modernizr.custom.min.js', '', null, true );
    wp_enqueue_script( 'jquery-shuffle', plugins_url( __FILE__ ) . 'public/assets/js/jquery.shuffle.js', array( 'jquery' ), null, true );
    wp_enqueue_script( 'wp-petfinder-listing-homepage', plugins_url( __FILE__ ) . 'public/assets/js/homepage.js', array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'wp_petfinder_listing_enqueue_public_scripts' );

// ini_set("allow_url_fopen", true);
// ini_set("allow_url_include", true);

/**
 * Default Variables
 * Used in Options Page
 * @var array
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
 * Settings Page
 */
add_option( 'petfinder-search-and-rescue', $pet_sr_options );
$pet_sr_options = get_option( 'petfinder-search-and-rescue' );

add_action( 'admin_menu', 'pet_sr_admin_page' );

/**
 * Add Settings Page to Admin Menu
 */
function pet_sr_admin_page() {
	add_options_page(
        __( 'Petfinder Listing', 'wp-petfinder-listing' ),
        __( 'Petfinder Listing', 'wp-petfinder-listing' ),
        'manage_options',
        'pet_sr',
        'pet_sr_options_page'
    );
}
add_filter( 'plugin_action_links_' . $pluginName, 'pet_sr_pluginActions' );

/**
 * Settings Link
 * @param  array $links
 * @return array $links
 */
function pet_sr_pluginActions( $links ) {
	$settings_link =
		'<a href="' . get_admin_url( null, 'options-general.php' ) . "?page=pet_sr".'">' .
		__('Settings') . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

/**
 * Render Settings Page
 */
function pet_sr_options_page() {
    global $pet_sr_options;

    if( isset( $_POST['reset'] ) ) {
        //IF CHOSE RESET COLORS - USE DEFAULT VALUES
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

        update_option( 'petfinder-search-and-rescue', $pet_sr_options );

        ?>
        <div class="trashed notice notice-warning">
            <p><?php _e( 'Colors have been reset to original default values.', 'wp-petfinder-listing' ); ?></p>
        </div>
        <?php
    }

    /**
     * Store Changes on Save
     */
    if( isset( $_POST['save_changes'] ) ) {
        check_admin_referer('petfinder-search-and-rescue-update_settings');

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

		$pet_sr_options['psr_shelter_info'] = stripslashes( $_POST['psr_shelter_info'] );
		$pet_sr_options['psr_shelter_info'] = wpautop( $pet_sr_options['psr_shelter_info'] );

        update_option( 'petfinder-search-and-rescue', $pet_sr_options );
        ?>

        <div class="updated notice notice-success">
            <p><?php _e( 'Your changes have been saved successfully!', 'wp-petfinder-listing' ); ?></p>
        </div>

        <?php
    }

    /**
     * Settings Page Form
     */
    include_once( 'admin/views/settings.php' );

}

/**
 * Get Petfinder Data
 * Using API and shelter id used in shortcode
 * @param  string $api_key
 * @param  string $shelter_id
 * @param  int $count
 * @param  int $pet
 * @return array
 */
function get_petfinder_data( $api_key, $shelter_id, $count, $pet = '' ) {

    // Get settings
    if ( empty( $api_key ) || empty( $shelter_id ) ) {
        throw new Exception( 'Petfinder requires a valid API key and shelter ID in order to fetch the pet listings.' );
        return;
    }

    if( empty( $pet ) ) {
        // Variables
        $base_url = PETFINDER_BASE_URL;
        $params = http_build_query(
            array(
                'format' => 'json',
                'key' => $api_key,
                'id' => $shelter_id,
                'count' => (int) $count,
                'status' => 'A',
                'output' => 'full',
            )
        );
    } else {
        // Variables
        $base_url = PETFINDER_SINGLE_PET_URL;
        $params = http_build_query(
            array(
                'format' => 'json',
                'key' => $api_key,
                'id' => (int) $pet
            )
        );
    }


    // Get API data
    $request = wp_remote_get( $base_url . $params );
    $response = wp_remote_retrieve_body( $request );
    $data = json_decode( $response, true );

    // If there was an error, return null
    if ( intval( $data['petfinder']['header']['status']['code']['$t'] ) !== 100 ) {
        throw new Exception( 'Petfinder returned an error: ', intval( $data['petfinder']['header']['status']['code']['$t'] ) );
        return null;
    }

    // If no records were found return null
    if ( empty( $data ) ) {
        throw new Exception( 'No pet records were found' );
        return null;
    }

    // Return the pet data
    return $data['petfinder']['pets']['pet'];

	// // If no specific pet is specified
	// if ( $pet == '' ) {
	// 	// Create request URL for all pets from the shelter
	// 	$request_url = 'http://api.petfinder.com/shelter.getPets?key=' . $api_key . '&count=' . $count . '&id=' . $shelter_id . '&status=A&output=full';
	// }
    //
	// // If a specific pet IS specified
	// else {
	// 	// Create a request URL for that specific pet's data
	// 	$request_url = 'http://api.petfinder.com/pet.get?key=' . $api_key . '&id=' . $pet;
	// }
    //
	// // Request data from Petfinder
	// $petfinder_data = @simplexml_load_file( $request_url );
    //
	// // If data not available, don't display errors on page
	// if ($petfinder_data === false) {}
    //
	// return $petfinder_data;

}

/**
 * Parse Return Values
 * Convert abbreviated values into recognizable words for user
 */
// Convert Pet Animal Type
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

// Convert Pet Size
function get_pet_size( $pet_size ) {
	if ($pet_size == 'S') return 'Small';
	if ($pet_size == 'M') return 'Medium';
	if ($pet_size == 'L') return 'Large';
	if ($pet_size == 'XL') return 'Extra Large';
	return 'Not Known';
}

// Convert Pet Age
function get_pet_age( $pet_age ) {
	if ($pet_age == 'Baby') return 'Baby';
	if ($pet_age == 'Young') return 'Young';
	if ($pet_age == 'Adult') return 'Adult';
	if ($pet_age == 'Senior') return 'Senior';
	return 'Not Known';
}

// Convert Pet Gender
function get_pet_gender( $pet_gender ) {
	if ($pet_gender == 'M') return 'Male';
	if ($pet_gender == 'F') return 'Female';
	return 'Not Known';
}

// Convert Special Needs & Options
function get_pet_option( $pet_option ) {
	if ( $pet_option == 'specialNeeds' ) return 'Special Needs';
	if ( $pet_option == 'noDogs' ) return 'No Dogs';
	if ( $pet_option == 'noCats' ) return 'No Cats';
	if ( $pet_option == 'noKids' ) return 'No Kids';
	if ( $pet_option == 'noClaws' ) return 'Declawed';
	if ( $pet_option == 'hasShots' ) return 'Has Shots';
	if ( $pet_option == 'housebroken' ) return 'Housebroken';
	if ( $pet_option == 'altered' ) return 'Spayed/Neutered';
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

/**
 * Cleanup Pet Names
 * @param  string $pet_name
 * @return string $pet_name
 */
function get_pet_name( $pet_name ) {

	// Clean-up pet name
	$pet_name = array_shift( explode( '-', $pet_name ) ); // Remove '-' from animal names
	$pet_name = array_shift( explode( '(', $pet_name ) ); // Remove '(...)' from animal names
	$pet_name = array_shift( explode( '[', $pet_name ) ); // Remove '[...]' from animal names
	$pet_name = strtolower( $pet_name ); // Transform names to lowercase
	$pet_name = ucwords( $pet_name ); // Capitalize the first letter of each name

	// Return pet name
	return $pet_name;

}

/**
 * Clean up Pet Description
 * Remove inline styling and empty tags from pet descriptions.
 *
 * @since 1.02.1 changed to use native `wp_strip_all_tags`
 *
 * @uses wp_strip_all_tags
 *
 * @param  string $pet_description
 * @return string $pet_description
 */
function get_pet_description( $pet_description ) {

    return wp_strip_all_tags( $pet_description );

}

/**
 * Photo Settings
 * Set size and number of pet photos.
 * $photo_size options: large, medium, thumb_small, thumb_medium, thumb_large
 * $limit: true (default) = only show one. false = show all.
 * @param  int $pet
 * @param  string $photo_size
 * @param  boolean $limit
 * @return array $pet_photos
 */
function get_pet_photos( $pet, $photo_size = 'medium', $limit = true ) {

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
	if( count( $pet->media->photos ) > 0 ) {

		// Get Pet Name
		$pet_name = get_pet_name( $pet->name );

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
		$pet_photos = '<p>' . __( 'No Photo Available', 'wp-petfinder-listing' ) . '</p>';
	}

	return $pet_photos;

}

/**
 * Condense Pet Values
 * Removes spacing and special characters from strings.
 *
 * @param  string $pet_value
 * @return string $pet_value
 */
function pet_value_condensed( $pet_value ) {

	// Define characters to remove and remove them
	$condense_list = array( '(' => '', ')' => '', '&' => '-', '/' => '-', '  ' => '-', ' ' => '-' );
	$pet_value = strtr( $pet_value, $condense_list );

	// Return condensed list
	return $pet_value;

}

/**
 * Render Pet List
 * @param  array $pets
 * @return string $output
 */
function get_type_list( $pets ) {

	// Define Variables
	$types = '';
	$type_list = '';

	// Create a list of types of pets
	foreach( $pets as $pet ) {
		$types .= get_pet_type( $pet['animal']['$t'] ) . "|";
	}

	// Remove duplicates, convert into an array, and alphabetize
	$types = array_filter( array_unique( explode( '|', $types ) ) );
	asort( $types );

	// For each type of pet
	foreach( $types as $type ) {

		// Create a condensed version without spaces or special characters
		$type_condensed = pet_value_condensed( $type );

		// Create a list
		$type_list .= '<li class="btn " data-group=' . $type_condensed . '><span>' . $type_condensed . '</span></li>';
	}

	//grab admin option vars
	add_option( 'petfinder-search-and-rescue', $pet_sr_options );
    $pet_sr_options = get_option('petfinder-search-and-rescue');

	$output = '<div class="psr_container-pets">';
	//all pet options
	if ( $pet_sr_options['psr_optionssection_remove']=='on' || $pet_sr_options['psr_hideoptionssection_default']=='on' ) {
        $output .= '<div id="all-pet-options" style="display: none;">';
	}
	else {
    	$output .= '<div id="all-pet-options">';
	}

    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/pet-filter-list.php' );

    $output .= ob_get_contents();

    ob_end_clean();

    return $output;

}

/**
 * Render Listing Header
 * @return string $output
 */
function getHeader() {
	//Get admin settings
	add_option( 'petfinder-search-and-rescue', $pet_sr_options );
    $pet_sr_options = get_option( 'petfinder-search-and-rescue' );

    // Render page top arrow if option is selected
    $arrowdiv = ( 1 === $pet_sr_options['psr_pageuparrow_show'] ) ? '<div class="psr__hoverme p_sr-pagetop-arrow"><span class="ico-psr_up"></span></div>' : '';

	$output = '';
	// $output .= '<div id="petfinder_search_rescue_container">';
    //
	// //PRELOADER
	// $output .= '<div id="psr__preloader">';
 //  	$output .= 'Loading our adoptable pets';
    // $output .= '<div id="preloader-icon"></div>';
	// $output .= '</div>';
    //
    // $output .= '<div data-twttr-rendered="true" cz-shortcut-listen="true">';

    /**
     * Load custom styles
     */
    // ob_start();
    //
    // include_once( plugins_url(__FILE__ ) . 'public/views/custom-styles.php' );
    //
    // $output .= ob_get_contents();
    //
    // ob_end_clean();

    /**
     * Load modal background
     */
    // ob_start();
    //
    // include_once( plugins_url(__FILE__ ) . 'public/views/modals/background.php' );
    //
    // $output .= ob_get_contents();
    //
    // ob_end_clean();

    /**
     * Load pet detail modal
     */
    // ob_start();
    //
    // include_once( plugins_url(__FILE__ ) . 'public/views/modals/pet-detail.php' );
    //
    // $output .= ob_get_contents();
    //
    // ob_end_clean();

    /**
     * Load adoption modal
     */
    // ob_start();
    //
    // include_once( plugins_url(__FILE__ ) . 'public/views/modals/adoption-detail.php' );
    //
    // $output .= ob_get_contents();
    //
    // ob_end_clean();


    /**
     * Load adoption modal
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/header.php' );

    $output .= ob_get_contents();

    ob_end_clean();

    return $output;

}

/**
 * Render Breed List
 * List of available breeds.
 * @param  array $pets
 * @return string $output
 */
function get_breed_list( $pets ) {

	// Define Variables
	$breeds = '';
	$breed_list = '';

	// Get a list of breeds for each pet
	foreach( $pets as $pet ) {
		foreach( $pet['breeds'] as $breed ) {
			$breeds .= $breed['$t'] . "|";
		}
	}

	// Remove duplicates, convert into an array and alphabetize
	$breeds = array_filter( array_unique( explode( '|', $breeds) ) );
	asort( $breeds );

	// For each breed
	foreach( $breeds as $breed ) {

		// Create a condensed version without spaces or special characters
		$breed_condensed = pet_value_condensed( $breed) ;

		// Create a list
		$breed_list .= '<li class="btn " data-group=' . $breed_condensed . '><span>' . $breed_condensed . '</span></li>';
	}

    /**
     * Load breed list
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/breed-list.php' );

    $output .= ob_get_contents();

    ob_end_clean();

    /**
     * Load breed list
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/pet-grid.php' );

    $output .= ob_get_contents();

    ob_end_clean();

	return $output;
}

/**
 * Render Size List
 * List of available sizes of pets.
 * @param  array $pets
 * @return string $output
 */
function get_size_list( $pets ) {

	// Define Variables
	$sizes = '';
	$size_list = '';

	// Create a list of pet sizes
	foreach( $pets as $pet ) {
		$sizes .= get_pet_size( $pet['size']['$t'] ) . "|";
	}

	// Remove duplicates, convert into an array, alphabetize and reverse list order
	$sizes = array_filter( array_unique( explode( '|', $sizes) ) );
	asort( $sizes );
	$sizes = array_reverse( $sizes );

	// For each size of pet
	foreach( $sizes as $size ) {

		// Create a condensed version without spaces or special characters
		$size_condensed = pet_value_condensed( $size );

		// Create a list
		$size_list .= '<li class="btn " data-group=' . $size_condensed . '><span class="psr__hoverme">' . $size_condensed . '</li>';
	}

    /**
     * Size List
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/size-list.php' );

    $output = ob_get_contents();

    ob_end_clean();

    return $output;

}

/**
 * Render Age List
 * Available pet ages.
 * @param  array $pets
 * @return string $output
 */
function get_age_list( $pets ) {

	// Define Variables
	$ages = '';
	$age_list = '';

	// Create a list of pet ages
	foreach( $pets as $pet ) {
		$ages .= get_pet_age( $pet['age']['$t'] ) . "|";
	}

	// Remove duplicates, convert into an array and reverse list order
	$ages = array_reverse( array_filter( array_unique( explode('|', $ages ) ) ) );

	// For each pet age
	foreach( $ages as $age ) {

		// Create a condensed version without spaces or special characters
		$age_condensed = pet_value_condensed( $age );

		// Create a list
		$age_list .= '<li class="btn " data-group=' . $age_condensed . '><span class="psr__hoverme">' . $age_condensed . '</span></li>';
	}

    /**
     * Age List
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/age-list.php' );

    $output = ob_get_contents();

    ob_end_clean();

    return $output;

}

/**
 * Render gender list
 * List of available pet genders.
 * @param  array $pets
 * @return string $output
 */
function get_gender_list( $pets ) {

	// Define Variables
	$genders = '';
	$gender_list = '';

	// Create a list available pet genders
	foreach( $pets as $pet ) {
		$genders .= get_pet_gender( $pet['sex']['$t'] ) . "|";
	}

	// Remove duplicates and convert into an array
	$genders = array_filter( array_unique( explode( '|', $genders ) ) );

	// For each pet gender
	foreach( $genders as $gender ) {

		// Create a condensed version without spaces or special characters
		$gender_condensed = pet_value_condensed( $gender );

		// Create a list
		$gender_list .= '<li class="btn " data-group=' . $gender_condensed . '><span class="psr__hoverme">' . $gender_condensed . '</span></li>';
	}

    /**
     * Gender List
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/gender-list.php' );

    $output = ob_get_contents();

    ob_end_clean();

    return $output;

}

/**
 * Render Options & Special Needs List
 * Used for Looing for... section:
 * Cat friendly, dog friendly, kid friendly, special needs
 * Rest of options/special needs displayed in tags in popup
 * such as spayed, has shots
 * @param  array $pets
 * @return string $output
 */
function get_options_list( $pets ) {

	// Define Variables
	$options = '';
	$options_list = '';

	// Create a list of pet options and special needs
	foreach( $pets as $pet ) {
        echo '<pre>';
        var_dump( $pet['options']['$t'] );
        echo '</pre>';

		foreach( $pet->options->option as $pet_option ) {
			$options .= get_pet_option( $pet_option ) . "|";
		}
	}

	// Remove duplicates, convert into an array and reverse list order
	$options = array_reverse( array_filter( array_unique( explode( '|', $options) ) ) );

	// For each pet option
	foreach( $options as $option ) {

		// Create a condensed version without spaces or special characters
		$option_condensed = pet_value_condensed( $option );

		// Create a list (only use if want to list all special needs/options as filter btns)
		//$options_list .= '<li class="btn " data-group=' . $option_condensed . '><span>' . $option_condensed . '</span></li>';
	}

    /**
     * Options List
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/options-list.php' );

    $output = ob_get_contents();

    ob_end_clean();

    return $output;

}

/**
 * Individual Pet Options
 * List of options for a specific pet.
 * @param  int $pet
 * @return string $pet_options
 */
function get_pet_options_list( $pet ) {

	// Define Variables
	$pet_options = '';

	// For each option
	foreach( $pet->options->option as $option ) {

		// Get option value
		$get_option = get_pet_option( $option );

		// If option value has been set
		if ( $get_option != '' ) {
			$pet_options .= '<span>' . $get_option .'</span>';
		}

	}

	return $pet_options;

}

 /**
  * Pet Options Classes
  * Used for filtering
  * Example no cats = !cat-friendly
  * Used for LooKing for... section:
  * Cat friendly, dog friendly, kid friendly, special needs
  *
  * @param  int $pet
  * @return string $classes
  */
function get_pet_options_list_classes( $pet ) {

	// Define Variables
	$pet_optionClasses = '';

	// Default Values
	$is_dog_friendly = true;
	$is_cat_friendly = true;
	$is_kid_friendly = true;
	$is_special_needs = false;

	// For each option
	foreach( $pet->options->option as $option ) {
		//only display certain options/special needs for classes
		if ( 'noDogs' === $option ){
			$is_dog_friendly = false;
		}
		if ( 'noCats' === $option ){
			$is_cat_friendly = false;
		}
		if ( 'noKids' === $option ){
			$is_kid_friendly = false;
		}
		if ( 'specialNeeds' === $option ){
			$is_special_needs = true;
		}
	}

    //Put classes altogether based on options
	if ( true === $is_dog_friendly ){
    	$classes .= 'dog-friendly ';
	}
	if ( true === $is_cat_friendly ){
    	$classes .= 'cat-friendly ';
	}
	if ( true === $is_kid_friendly ){
    	$classes .= 'kid-friendly ';
	}
	if ( true === $is_special_needs ){
    	$classes .= 'special-needs ';
	}

	return $classes;
}

/**
 * Render Data Attributes
 * Used for filtering
 * Example no cats = !cat-friendly
 * Used for LooKing for... section:
 * Cat friendly, dog friendly, kid friendly, special needs
 * @param  int $pet
 * @return string $data_atts
 */
function get_pet_options_list_data_groups( $pet ) {

	// Default Values
	$pet_optionDataGroups = '';
	$is_dog_friendly = true;
	$is_cat_friendly = true;
	$is_kid_friendly = true;
	$is_special_needs = false;

	// For each option
	foreach( $pet->options->option as $option ) {
	//only display certain options/special needs for classes
        if ( 'noDogs' === $option ){
            $is_dog_friendly = false;
        }
        if ( 'noCats' === $option ){
            $is_cat_friendly = false;
        }
        if ( 'noKids' === $option ){
            $is_kid_friendly = false;
        }
        if ( 'specialNeeds' === $option ){
            $is_special_needs = true;
        }
	}
	//Put data attributes altogether based on options
	if ( true === $is_dog_friendly ){
    	$data_atts .= '&quot;dog-friendly&quot;,';
	}
	if ( true === $is_cat_friendly ){
    	$data_atts .= '&quot;cat-friendly&quot;,';
	}
	if ( true === $is_kid_friendly ){
    	$data_atts .= '&quot;kid-friendly&quot;,';
	}
	if ( true === $is_special_needs ){
    	$data_atts .= '&quot;special-needs&quot;,';
	}

	return $data_atts;
}

/**
 * Render List of Pets
 * List of all available pets.
 * @param  array int $pets
 * @return string $output
 */
function get_all_pets( $pets ) {

	foreach( $pets as $pet ) {

		// Define Variables
		$pet_name = get_pet_name( $pet->name );
		$pet_type = get_pet_type( $pet->animal );
		$pet_size = get_pet_size( $pet->size );
		$pet_age = get_pet_age( $pet->age );
		$pet_gender = get_pet_gender( $pet->sex );
		$pet_options = get_pet_options_list( $pet );
		$pet_optionClasses = get_pet_options_list_classes( $pet );
		$pet_optionDataGroups = get_pet_options_list_data_groups( $pet );
		$pet_description = get_pet_description( $pet->description );
		$pet_photo_thumbnail = get_pet_photos( $pet, 'medium' );
		$pet_photo_all = get_pet_photos ( $pet, 'large', false );
		$pet_more_url = get_permalink() . '?view=pet-details&id=' . $pet->id;
		$pet_pf_url = esc_url( 'https://www.petfinder.com/petdetail/' ) . $pet->id;

		// Create breed classes
		$pet_breeds_condensed = '';
		foreach( $pet->breeds->breed as $breed ) {
			$pet_breeds_condensed .= pet_value_condensed( $breed ) . ' ';
		}

		// Create options classes
		$pet_options_condensed = '';
		foreach( $pet->options->option as $option ) {
			$option = get_pet_option( $option );
			if ( $option != '' ) {
				$pet_options_condensed .= pet_value_condensed( $option ) . ' ';
			}
		}

        $classes = implode( ' ', array(
            pet_value_condensed( $breed ),
            pet_value_condensed( $pet_type ),
            pet_value_condensed( $pet_size ),
            pet_value_condensed( $pet_age ),
            pet_value_condensed( $pet_gender ),
            $pet_optionClasses
        ) );

        $data_atts = implode( '&quot;,&quot;', array(
            pet_value_condensed( $breed ),
            pet_value_condensed( $pet_type ),
            pet_value_condensed( $pet_size ),
            pet_value_condensed( $pet_age ),
            pet_value_condensed( $pet_gender ),
            $pet_optionDataGroups
        ) );

        $tags = implode( ', ', array(
            pet_value_condensed( $breed ),
            pet_value_condensed( $pet_type ),
            pet_value_condensed( $pet_size ),
            pet_value_condensed( $pet_age ),
            pet_value_condensed( $pet_gender )
        ) );

        $atts = implode( '</span><span>', array(
            pet_value_condensed( $breed ),
            pet_value_condensed( $pet_type ),
            pet_value_condensed( $pet_size ),
            pet_value_condensed( $pet_age ),
            pet_value_condensed( $pet_gender )
        ) );

        /**
         * Pets List
         */
        ob_start();

        include_once( plugins_url(__FILE__ ) . 'public/views/pets-list.php' );

        $output = ob_get_contents();

        ob_end_clean();

	}

    /**
     * Close List
     */
    ob_start();

    include_once( plugins_url(__FILE__ ) . 'public/views/footer.php' );

    $output = ob_get_contents();

    ob_end_clean();

    return $output;

}

?>
