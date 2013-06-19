<?php
/*
Plugin Name: Location required
Plugin URI: http://www.osclass.org/
Description: This Plugin makes the location required when posting or editing ads.
Version: 1.0.4
Author: JChapman & Osclass
Author URI: http://www.osclass.org/
Short Name: location_required
Plugin update URI: location-required
*/


osc_register_script('jquery-metadata', osc_base_url().'oc-content/plugins/location_required/jquery.metadata.js', array('jquery'));
osc_enqueue_script('jquery-metadata');

function location_js() {
	?>
    <!-- requrire location -->
    <script type="text/javascript">
        $(document).ready(function() {
	        if( $("form[name=item]").length > 0 ) {
                $('input[id="region"]').addClass("{required: true, messages: { required: '<?php _e("Region is required", "location_required") ; ?>'}}") ;
                $('input[id="city"]').addClass("{required: true, messages: { required: '<?php _e("City is required", "location_required") ; ?>'}}") ;
	        }
        }) ;
    </script>
    <!-- require location end -->
	<?php	
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '') ;

    osc_add_hook('footer', 'location_js') ;

?>
