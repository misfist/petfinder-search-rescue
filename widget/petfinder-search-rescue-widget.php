<?php

class Petfinder_Search_Rescue_Widget extends WP_Widget {

	public function __construct() {
		
		parent::__construct(
	 		'psr', // Base ID
			'Petfinder Search & Rescue Widget', // Name
			array( 'description' => __( 'Advertise your adoptable pets' ), ) // Args
		);
	}

    ///CONNECT TO PETFINDER DATA
    function widget($args, $instance) {
		
        extract( $args );
	
		$response = wp_remote_get("http://api.petfinder.com/pet.getRandom?key=" . $instance['psr_widget_apikey'] . "&shelterid=" . $instance['psr_widget_shelterid'] . "&output=full");
		$xml = simplexml_load_string(wp_remote_retrieve_body($response));
	
		?>
<!-- =============================================================
	DISPLAY THE WIDGET
* ============================================================= -->
        
        <!--load stylesheet-->
          <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('psr-widget-style.css', __FILE__ );?>">
       
          
          <style>.psr__adoptbtn_widget span{background-color:<?php  echo $instance["psr_widget_buttoncolor"];?>	}</style>
          
          <!--start container-->
        <div id="search_rescue_widget">
        <?php
       
               ?>
				<!--START LINK-->    
                <?php if( $instance['psr_link_adoptpage'] != "" ){
                  echo "<a class='psr__link_widget' href=\"" . $instance['psr_link_adoptpage'] . "\"> ";
				}?>
                
                <!--SHELTER NAME-->
               <?php  if( $instance['psr_widget_sheltername'] != "" ){?>
                <div class="psr__sheltername_widget"><div class="psr__sheltername_inner_widget"><span class="ico-psr_adopt"></span><?php  echo $instance["psr_widget_sheltername"];?></div></div>
                <?php } ?>
                
                <!--WIDGET BTN-->
              	<div class='psr__adoptbtn_widget'>
                <?php if($instance["psr_widget_buttontext"]!=''){?>
                	<span><?php  echo $instance["psr_widget_buttontext"];?></span>
                    <?php } 
					else{?>
                   <span>Seach Our Pets</span>
					<?php }?>
                </div>
                
                <!--RANDOM IMG-->
                <div class="psr__random-img_widget">
                <?php
				

				// If the Petfinder API returns without errors
				 if( $xml->header->status->code == "100" ){
                	$pet = $xml->pet;
					
				if($pet->media->photos){//if pet has photo
                		foreach ( $pet->media->photos->photo as $photo ) {
							foreach( $photo->attributes() as $key => $value ) {
								if ( $key == 'size' ) {
									if ( $value == 'x' ) {//grab large size						
                				 	echo "<img class=\"petfinder-featured\" id=\"" . $pet->id . "\"  src=\"" . $photo . "\">";		
								 	break 2; //only display one image		 				
									}
										else if ( $value == 'pn' ) {//grab medium size						
                				 	echo "<img class=\"petfinder-featured\" id=\"" . $pet->id . "\"  src=\"" . $photo . "\">";		
								 	break 1; //only display one image		 				
									}
									
									//(if image is smaller than medium, exit loop
								}
							}
						}
				}
				else {//if no photos or not large/medium img, show placeholder
				echo "<div class='psr_widget_img_placeholder'><span class='ico-psr_adopt'></span></div>";
				}
					
				}
				else{//if petfinder is down, show placeholder
               	echo "<div class='psr_widget_img_placeholder'><span class='ico-psr_adopt'></span></div>";
       			 }
				 ?>
				
        		</div> <!-- widget img -->
                
        <?php 
			//end link
			if( $instance['psr_link_adoptpage'] != "" ){
				echo "</a>";
			}
		
		
		?>  <!--end link -->
       </div>
   
   <?php
        echo $after_widget;
    }
	
/*=============================================================
	END THE WIDGET
* ============================================================= */

    //UPDATE VARS FROM FORM
    function update( $new_instance, $old_instance ) {

	 $instance = $old_instance;
		$instance['psr_widget_buttontext']      = strip_tags( $new_instance['psr_widget_buttontext'] );
		$instance['psr_widget_buttoncolor']      = strip_tags( $new_instance['psr_widget_buttoncolor'] );
		$instance['psr_link_adoptpage']      = strip_tags( $new_instance['psr_link_adoptpage'] );
		$instance['psr_widget_apikey']      = strip_tags( $new_instance['psr_widget_apikey'] );
		$instance['psr_widget_shelterid']      = strip_tags( $new_instance['psr_widget_shelterid'] );
		$instance['psr_widget_sheltername']      = strip_tags( $new_instance['psr_widget_sheltername'] );
		
		return $instance;
    }

  
    function form( $instance ) {
        // Default vars in form
		$instance = wp_parse_args( (array) $instance, array(
			'psr_widget_buttontext' => 'Seach Our Pets',
			'psr_widget_buttoncolor' => 'F67D42',
			'psr_link_adoptpage' => '',
			'psr_widget_apikey' => '',
			'psr_widget_shelterid' => '',
			$pet => '',
			$photo => '',
			'psr_widget_sheltername' => ''
		));

     $psr_widget_buttontext = esc_attr($instance['psr_widget_buttontext']);
		$psr_widget_buttoncolor = esc_attr($instance['psr_widget_buttoncolor']);
        $psr_link_adoptpage = esc_attr($instance['psr_link_adoptpage']);
		$psr_widget_apikey = esc_attr($instance['psr_widget_apikey']);
		$psr_widget_shelterid = esc_attr($instance['psr_widget_shelterid']);
		$psr_widget_sheltername = esc_attr($instance['psr_widget_sheltername']);
		?>
       
      <p>
          <label for="<?php echo $this->get_field_id('psr_widget_apikey'); ?>"><?php _e('API Key:'); ?></label>
          <input id="<?php echo $this->get_field_id('psr_widget_apikey'); ?>" name="<?php echo $this->get_field_name('psr_widget_apikey'); ?>" type="text" value="<?php echo $psr_widget_apikey; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('psr_widget_shelterid'); ?>"><?php _e('Shelter ID:'); ?></label>
          <input id="<?php echo $this->get_field_id('psr_widget_shelterid'); ?>" name="<?php echo $this->get_field_name('psr_widget_shelterid'); ?>" type="text" value="<?php echo $psr_widget_shelterid; ?>" />
        </p>
        
                <p>
          <label for="<?php echo $this->get_field_id('psr_widget_sheltername'); ?>"><?php _e('Shelter Name:'); ?></label>
          <input id="<?php echo $this->get_field_id('psr_widget_sheltername'); ?>" name="<?php echo $this->get_field_name('psr_widget_sheltername'); ?>" type="text" value="<?php echo $psr_widget_sheltername; ?>" />
        </p>
        
             <p>
          <label for="<?php echo $this->get_field_id('psr_widget_buttontext'); ?>"><?php _e('Button Text:'); ?></label>
          <input id="<?php echo $this->get_field_id('psr_widget_buttontext'); ?>" name="<?php echo $this->get_field_name('psr_widget_buttontext'); ?>" type="text" value="<?php echo $psr_widget_buttontext; ?>" />
        </p>
        
          <p>
          <label for="<?php echo $this->get_field_id('psr_widget_buttoncolor'); ?>"><?php _e('Button Color:'); ?></label><br/>
          <input class="my-color-field" id="<?php echo $this->get_field_id('psr_widget_buttoncolor'); ?>" name="<?php echo $this->get_field_name('psr_widget_buttoncolor'); ?>" type="text" value="<?php echo $psr_widget_buttoncolor; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('psr_link_adoptpage'); ?>"><?php _e('Link to Adoption Page (Full URL):'); ?></label>
          <input id="<?php echo $this->get_field_id('psr_link_adoptpage'); ?>" name="<?php echo $this->get_field_name('psr_link_adoptpage'); ?>" type="text" value="<?php echo $psr_link_adoptpage; ?>" />
        </p>
<script>jQuery(document).ready(function($){
    $('#widgets-right .my-color-field').wpColorPicker();
});   
</script>
        
        <?php
    }
	   


}


add_action('widgets_init', create_function('', 'return register_widget("Petfinder_Search_Rescue_Widget");'));
