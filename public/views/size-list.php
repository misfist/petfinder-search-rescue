<?php
/**
 * Markup for Size List
 */
?>
<div class="psr__span3 petOption-section">
    <p class="filter__label"><?php _e( 'Any Size', 'wp-petfinder-list' ); ?></p>
    <ul class="filter-options psr__btn-group">
        <?php echo $size_list; ?>
        <li class="btn  allbtn" data-group="all"><span class="psr__hoverme"><?php _e( 'Any Size', 'wp-petfinder-list' ); ?></span></li>
    </ul><!-- //end btn-group -->
</div><!-- //end span -->
