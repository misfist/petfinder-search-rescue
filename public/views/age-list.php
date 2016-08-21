<?php
/**
 * Markup for Age List
 */
?>
<div class="psr__span3 petOption-section">
    <p class="filter__label"><?php _e( 'Age', 'wp-petfinder-listing' ); ?></p>
    <ul class="filter-options psr__btn-group">
        <?php echo $age_list; ?>
        <li class="btn  allbtn" data-group="all"><span class="psr__hoverme"><?php _e( 'Any Age', 'wp-petfinder-listing' ); ?></span></li>
    </ul><!-- //end btn-group -->
</div><!-- //end psr__span3 -->
