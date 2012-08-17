<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }
    
    $status = jobboard_status();
    $mjb = ModelJB::newInstance();
    
?>
<div>
    <h1><?php _e('Last 10 applicants', 'jobboard'); ?></h1>
    <div id="applicants_list">
        <ul>
            <?php foreach($status as $k => $v) { 
                list($filteredTotal, $total) = $mjb->searchCount(array('status' => $k));
                ?>
            <li><a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php&iStatus=") . $k; ?>"><?php echo $v; ?></a> <span>(<?php echo $filteredTotal; ?>)</span></li>
            <?php }; ?>
        </ul>
    </div>
</div>
<div style="clear:both;"></div>
<div>
    <h1><?php _e('Last 10 jobs', 'jobboard'); ?></h1>
    <div id="jobs_list">
        <ul>
            <?php $mSearch = new Search(true);
            $mSearch->addItemConditions(DB_TABLE_PREFIX.'t_item.b_enabled = 1');?>
            <li><a href="<?php echo osc_admin_base_url(true); ?>?page=items&b_enabled=1"><?php _e('Open', 'jobboard'); ?></a> <span>(<?php echo $mSearch->count(); ?>)</span></li>
            <?php $mSearch2 = new Search(true);
            $mSearch2->addItemConditions(DB_TABLE_PREFIX.'t_item.b_enabled = 0');?>
            <li><a href="<?php echo osc_admin_base_url(true); ?>?page=items&b_enabled=0"><?php _e('On hold', 'jobboard'); ?></a> <span>(<?php echo $mSearch2->count(); ?>)</span></li>
        </ul>
    </div>
</div>
<div style="clear:both;"></div>