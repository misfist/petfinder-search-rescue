<?php
/**
 * WordPress Petfinder Listing Helpers
 *
 * @package    WP_Petfinder_Listing
 * @subpackage WP_Petfinder_Listing\Includes
 * @since      1.02.1
 * @license    GPL-2.0+
 */

function array_value_recursive( $key, array $arr ) {
    $val = array();
    array_walk_recursive( $arr, function( $v, $k ) use( $key, &$val ) {
        if( $k == $key ) {
            array_push( $val, $v );
        }
    });
    return count($val) > 1 ? $val : array_pop( $val );
}

function multidimensional_search( $array, $key, $value ) {
    $results = array();

    if ( is_array( $array ) ) {
        if ( isset( $array[$key] ) && $array[$key] == $value ) {
            $results[] = $array;
        }

        foreach ( $array as $subarray ) {
            $results = array_merge( $results, search( $subarray, $key, $value ) );
        }

    }

    return $results;
}
