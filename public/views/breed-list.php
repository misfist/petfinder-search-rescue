<?php
/**
 * Markup for Breed List
 */
?>
<div class="psr__span7 breedOption-section petOption-section">
    <p class="filter__label"><?php _e( 'Breed', 'wp-petfinder-listing' ); ?></p>
    <ul class="filter-options psr__btn-group">
        <li class="btn  allbtn" data-group="all"><span class="psr__hoverme"><?php _e( 'All Breeds', 'wp-petfinder-listing' ); ?></span></li>
        <?php
        echo '<pre>';
        var_dump( 'breeds', $breed_list );
        echo '</pre>'; ?>
        <?php echo $breed_list; ?>
    </ul><!-- //end btn-group -->
</div><!-- //end SPAN -->

<div class="psr__span1">
    <ul class="filter-options psr__btn-group">
        <li class="btn  viewallbtn" data-group="all"><span class="psr__hoverme"><?php _e( 'Reset', 'wp-petfinder-listing' ); ?></span></li>
    </ul>
</div><!-- //end div -->
</div><!-- //end row -->
</div><!-- //end pet options -->
