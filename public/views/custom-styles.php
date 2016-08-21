<?php
/**
 * Custom styles based on settings
 */
?>
<style>
    /** Options Section **/
    .psr__header,
    #psr__preloader {
        background-color: <?php echo $pet_sr_options['psr_header_bg']; ?>;
        border-bottom: 1px solid <?php echo $pet_sr_options['psr_options_bg']; ?>;
    }
    #toggle-petOptions {
        color: <?php echo $pet_sr_options['psr_show_hide']; ?>;
        background-color: <?php echo $pet_sr_options['psr_options_bg']; ?>;
    }
    .psr__header .psr__header-icon,
    #petfinder_search_rescue_container .psr__header .psr__header-icon a,
    #psr__preloader {
        color: <?php echo $pet_sr_options['psr_header_links']; ?>;
    }
    p.filter__label {
        color: <?php echo $pet_sr_options['psr_options_title_color']; ?>;
    }
    #all-pet-options {
        background-color: <?php echo $pet_sr_options['psr_options_bg']; ?>;
    }
    .petOption-section {
        background-color: <?php echo $pet_sr_options['psr_optionssection_bg']; ?>;
        border:1px solid <?php echo $pet_sr_options['psr_options_text_color']; ?>;
    }
    .psr__btn-group .btn span{
        color: <?php echo $pet_sr_options['psr_options_text_color']; ?>;
    }
    .petOption-section .btn-group .btn span {
        background-color: <?php echo $pet_sr_options['psr_optionssection_bg']; ?>;
        color: <?php echo $pet_sr_options['psr_options_text_color']; ?>;
    }
    .psr__btn-group .btn.active span,
    .psr__btn-group .btn:hover span,
    .psr__btn-group .onlyOption span {
        background-color: <?php echo $pet_sr_options['psr_options_selected']; ?>;
        color: <?php echo $pet_sr_options['psr_options_selected_text']; ?>;
    }

    /** Grid Section **/
    .psr_container-pets{
        background-color: <?php echo $pet_sr_options['psr_petgrid_bg']; ?>;
    }
    .picture-item .picture-item__title,
    .window-popup-title{
        color: <?php echo $pet_sr_options['psr_pettitle_grid']; ?>;
    }
    .picture-item .picture-item__inner {
        background-color: <?php echo $pet_sr_options['psr_petdesc_grid']; ?>;
    }
    .picture-item .picture-item__tags {
        color: <?php echo $pet_sr_options['psr_pettags_grid_color']; ?>;
    }
    .picture-item .picture-item__tags .item__breed-tag {
        color: <?php echo $pet_sr_options['psr_petbreedtag_grid_color']; ?>;
    }
    .p_sr-pagetop-arrow {
        background-color: <?php echo $pet_sr_options['psr_pageuparrow_grid_color']; ?>;
    }

    /** Modal Style **/';
    .window-popup #pet-attributes span,
    .window-popup #pet-options span {
        background-color: <?php echo $pet_sr_options['psr_tags_popup']; ?>;
        color: <?php echo $pet_sr_options['psr_textontags_popup']; ?>;
    }
    .window-popup .window-popup-title {
        color: <?php echo $pet_sr_options['psr_pettitle_popup']; ?>;
    }
    .window-popup #pet-adopt-btn .psr__hoverme a.psr__adoptformlink,
    #pet-adopt-btn .psr__hoverme {
        color: <?php echo $pet_sr_options['psr_adoptbtntext_popup']; ?>;
        background-color: <?php echo $pet_sr_options['psr_adoptbtnbg_popup']; ?>;
    }
</style>
