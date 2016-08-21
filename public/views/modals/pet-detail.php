<?php
/**
 * Modal Pet Detail (nee pop-up)
 */
?>
<div id="indiepet-popup" class="window-popup">
    <div id="indiepet-popup-topbtns" class="window-popup-topbtns">
        <span class="ico-psr_left psr__hoverme" id="indiepet-popup-previous"></span>
        <span class="ico-psr_right psr__hoverme" id="indiepet-popup-next"></span>
        <span class="ico-psr_close psr__hoverme window-popup-close"></span>
    </div>
    <div id="pet-multiphotos"></div>
    <div id="pet-adopt-btn">
        <div class="psr__hoverme pet-adopt-btn-inner">
            <span id="adoptionformlink" data-group="<?php echo esc_url( $pet_sr_options['psr_adoptionformpage_link'] ); ?>"></span>
        </div>
    </div>
    <div class="my-petInfoAll">
        <div id="indiepet-popup-title" class="window-popup-title"></div>
        <div id="pet-attributes"></div>
        <div id="pet-options"></div>
        <div id="pet-description"></div>
        <div id="pet-petfinder_url"></div>
    </div>
</div>
