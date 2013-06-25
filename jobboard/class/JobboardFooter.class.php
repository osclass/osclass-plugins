<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * Print jobboard object with essential information and internatinalization
 * Load custom css and js
 */
class JobboardFooter
{
    public function __construct()
    {
	osc_add_filter('osclasscom_footer_links', array(&$this, 'footer_links'));
    }

    function footer_links($footer)
    {
	if( Params::getParam('page') !== 'plugins' ) {
	    return $footer;
	}

	switch( urldecode(Params::getParam('file')) ) {
	    case('jobboard/dashboard.php'):
		$footer['dashboard_tour'] = array(
		    'class' => '',
		    'link'  => '#',
		    'text'  => __('Take a tour', 'jobboard'),
		    'attr'  => array(
			'id' => 'dashboard_tour_link'
		    )
		);
	    break;
	}

	return $footer;
    }
}

$jf = new JobboardFooter();

// End of file: ./jobboard/class/JobboardFooter.class.php