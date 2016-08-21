<?php
/**
 * Markup for Gender List
 */
?>
    <div class="psr__span3 petOption-section">
        <p class="filter__label"><?php _e( 'Gender', 'wp-petfinder-listing' ); ?></p>
        <ul class="filter-options psr__btn-group OR-psr__btn-group">
            <?php echo $gender_list; ?>
            <li class="btn  allbtn" data-group="all"><span class="psr__hoverme"><?php _e( 'All', 'wp-petfinder-listing' ); ?></span></li>
        </ul><!-- //end btn-group -->
    </div><!-- //end psr__span3 -->
</div><!-- //end row -->
