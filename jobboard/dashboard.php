<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }
    
    $status = jobboard_status();
    $mjb = ModelJB::newInstance();
    
?>
</div>
</div>
    <div class="grid-row grid-first-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3><?php _e('Last 10 applicants', 'jobboard'); ?></h3></div>
                <div class="widget-box-content">
                    <table class="table" cellpadding="0" cellspacing="0" id="applicants_list">
                        <tbody>
                            <?php foreach($status as $k => $v) { 
                            list($filteredTotal, $total) = $mjb->searchCount(array('status' => $k));
                            ?>
                            <tr class="table-first-row">
                                <td><a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php&iStatus=") . $k; ?>"><?php echo $v; ?></a> <span>(<?php echo $filteredTotal; ?>)</span></td>
                            </tr>
                            <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-first-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3><?php _e('Last 10 jobs', 'jobboard'); ?></h3></div>
                <div class="widget-box-content">
                    <table class="table" cellpadding="0" cellspacing="0" id="applicants_list">
                        <tbody>
                                                        <?php $mSearch = new Search(true);
                        $mSearch->addItemConditions(DB_TABLE_PREFIX.'t_item.b_enabled = 1');?>
                        <td><a href="<?php echo osc_admin_base_url(true); ?>?page=items&b_enabled=1"><?php _e('Open', 'jobboard'); ?></a> <span>(<?php echo $mSearch->count(); ?>)</span></td>
                        <?php $mSearch2 = new Search(true);
                        $mSearch2->addItemConditions(DB_TABLE_PREFIX.'t_item.b_enabled = 0');?>
                        <td><a href="<?php echo osc_admin_base_url(true); ?>?page=items&b_enabled=0"><?php _e('On hold', 'jobboard'); ?></a> <span>(<?php echo $mSearch2->count(); ?>)</span></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-first-row grid-100">
        <div class="row-wrapper">