<?php
/**
 * Markup for Header
 */
?>
<div id="petfinder_search_rescue_container">

    <!-- Preloader -->
    <div id="psr__preloader">
        <?php _e( 'Loading our adoptable pets...', 'wp-petfinder-listing' ); ?>
        <div id="preloader-icon"></div>
    </div>

    <div data-twttr-rendered="true" cz-shortcut-listen="true">

        <?php include_once( PETFINDER_PLUGIN_DIR . 'public/views/custom-styles.php' ); ?>

        <div class="popup-bg"></div>

        <?php include_once( PETFINDER_PLUGIN_DIR . 'public/views/modals/pet-detail.php' ); ?>

        <?php include_once( PETFINDER_PLUGIN_DIR . 'public/views/modals/adoption-detail.php' ); ?>
