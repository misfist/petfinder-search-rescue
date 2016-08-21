<?php
/**
 * Markup for Pet Filter List
 */
?>
<div class="psr__row-fluid">
    <div class="psr__span3 petOption-section">
        <p class="filter__label"><?php _e( 'Type', 'wp-petfinder-listing' ); ?></p>
        <ul class="filter-options psr__btn-group OR-psr__btn-group">
            <?php echo $type_list; ?>
            <li class="btn  allbtn" data-group="all"><span class="psr__hoverme"><?php _e( 'Any Type', 'wp-petfinder-listing' ); ?></span></li>
        </ul>
    </div>
</div>
