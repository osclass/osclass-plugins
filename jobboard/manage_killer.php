<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $iDisplayLength = Params::getParam('iDisplayLength');
    $iPage = Params::getParam('iPage');
    $iPage = is_numeric($iPage)?($iPage):1;
    $iDisplayLength = (is_numeric($iDisplayLength)?$iDisplayLength:10);
    $start = ($iPage-1)*$iDisplayLength;

    $conditions = array();

    if(Params::getParam('sTitle')!='') {
        $conditions['title'] = Params::getParam('sTitle');
    }

    $order_col = Params::getParam('sOrderCol')!=''?Params::getParam('sOrderCol'):'kf.dt_pub_date';
    $order_dir = Params::getParam('sOrderDir')!=''?Params::getParam('sOrderDir'):'DESC';

    $killer = ModelKQ::newInstance()->search($start, $iDisplayLength, $conditions, $order_col, $order_dir);
    list($iTotalDisplayRecords, $iTotalRecords) = ModelKQ::newInstance()->searchCount($conditions, $order_col, $order_dir);

    $urlOrder = osc_admin_base_url(true).'?'.$_SERVER['QUERY_STRING'];
    $urlOrder = preg_replace('/&iPage=(\d+)?/', '', $urlOrder) ;
    $urlOrder = preg_replace('/&sOrderCol=([^&]*)/', '', $urlOrder) ;
    $urlOrder = preg_replace('/&sOrderDir=([^&]*)/', '', $urlOrder) ;
?>
<h2 class="render-title"><?php _e('Killer questions forms', 'jobboard'); ?> <a href="<?php echo osc_admin_render_plugin_url("jobboard/killer_form_frm.php"); ?>" class="btn btn-mini"><?php _e('Add new'); ?></a></h2>
<div class="relative killer">
    <div id="listing-toolbar">
        <div class="float-right">
            <form method="get" action="<?php echo osc_admin_base_url(true); ?>" class="inline select-items-per-page float-left">
                <?php foreach( Params::getParamsAsArray('get') as $key => $value ) { ?>
                <?php if( $key != 'iDisplayLength' ) { ?>
                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo osc_esc_html($value); ?>" />
                <?php } } ?>
                <select name="iDisplayLength" class="select-box-extra float-right" onchange="this.form.submit();" >
                    <option value="10"><?php printf(__('Show %d Killer forms'), 10); ?></option>
                    <option value="25" <?php if( Params::getParam('iDisplayLength') == 25 ) echo 'selected'; ?> ><?php printf(__('Show %d Killer forms'), 25); ?></option>
                    <option value="50" <?php if( Params::getParam('iDisplayLength') == 50 ) echo 'selected'; ?> ><?php printf(__('Show %d Killer forms'), 50); ?></option>
                    <option value="100" <?php if( Params::getParam('iDisplayLength') == 100 ) echo 'selected'; ?> ><?php printf(__('Show %d Killer forms'), 100); ?></option>
                </select>
            </form>
            <form method="get" action="<?php echo osc_admin_base_url(true); ?>" id="shortcut-filters" class="inline">
                <input type="hidden" name="page" value="plugins">
                <input type="hidden" name="action" value="renderplugin">
                <input type="hidden" name="file" value="jobboard/manage_killer.php">

                <input type="hidden" name="iDisplayLength" value="<?php echo $iDisplayLength;?>" />
                <label style="padding-top:9px;"><?php _e('Title', 'jobboard'); ?></label>
                <input type="text" name="sTitle" value="<?php echo osc_esc_html(Params::getParam('sTitle')); ?>" class="input-text input-actions"/>
                <input type="submit" class="btn submit-right" value="<?php echo osc_esc_html( __('Find') ) ; ?>">
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <form id="datatablesForm" action="<?php echo osc_admin_base_url(true); ?>" method="get">
        <input type="hidden" name="page" value="plugins">
        <input type="hidden" name="action" value="renderplugin">
        <input type="hidden" name="file" value="jobboard/manage_killer.php">
        <div class="table-contains-actions">
            <table class="table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th <?php if($order_col=='kf.s_title') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=kf.s_title&sOrderDir=".($order_col=='kf.s_title'?($order_dir=='ASC'?'DESC':'ASC'):'ASC');?>" >
                                <?php _e('Title', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='kf.dt_pub_date') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=kf.dt_pub_date&sOrderDir=".($order_col=='kf.dt_pub_date'?($order_dir=='ASC'?'DESC':'ASC'):'ASC');?>" >
                                <?php _e('Publication date', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='kf.n_questions') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=kf.n_questions&sOrderDir=".($order_col=='kf.n_questions'?($order_dir=='ASC'?'DESC':'ASC'):'ASC');?>" >
                                <?php _e('Nº questions', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='kf.n_used') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=kf.n_used&sOrderDir=".($order_col=='kf.n_used'?($order_dir=='DESC'?'ASC':'DESC'):'DESC');?>" >
                                <?php _e('Used by', 'jobboard') ; ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($killer) > 0) { ?>
                <?php foreach($killer as $k) { ?>
                    <tr>
                        <td class="killer"><?php echo @$k['s_title']; ?>
                            <div class="actions">
                                <ul>
                                    <li><a href="<?php echo osc_admin_render_plugin_url("jobboard/killer_form_frm.php").'&id='.$k['pk_i_id']; ?>"><?php _e("Edit", "jobboard"); ?></a></li>
                                    <?php if(@$k['n_used']>0 && is_numeric($k['n_used'])) { ?>
                                    <li><a class="delete_killerforminuse" href="javascript:void(0);"><?php _e("Delete", "jobboard"); ?></a></li>
                                    <?php } else {?>
                                    <li><a href="javascript:delete_killerform(<?php echo @$k['pk_i_id']; ?>);"><?php _e("Delete", "jobboard"); ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </td>
                        <td><?php echo @$k['dt_pub_date']; ?></td>
                        <td><?php echo @$k['n_questions']; ?></td>
                        <td><?php echo @$k['n_used']; ?></td>
                    </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="4" class="text-center">
                        <p><?php _e('No data available in table', 'jobboard') ; ?></p>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <div id="table-row-actions"></div>
        </div>
    </form>
</div>
<div class="has-pagination">
    <?php
        $aData = array(
            'iTotalDisplayRecords' => $iTotalDisplayRecords,
            'iTotalRecords'        => $iTotalRecords,
            'iDisplayLength'       => $iDisplayLength,
            'iPage'                => $iPage
        );
    ?>
    <ul class="showing-results">
        <li><span><?php echo osc_pagination_showing((($iPage-1)*$iDisplayLength)+1, (($iPage-1)*$iDisplayLength)+count($killer), $iTotalDisplayRecords, $iTotalRecords); ?></span></li>
    </ul>
    <?php osc_show_pagination_admin($aData); ?>
</div>

<form id="dialog-killerforminuse-delete" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Delete killer question form', 'jobboard')); ?>">
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Only can delete killer questions form if isn\'t used by any job' , 'jobboard'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <button type="button" class="btn" onclick="$('#dialog-killerforminuse-delete').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></button>
            </div>
        </div>
    </div>
</form>

<form id="dialog-killerform-delete" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Delete killer question form', 'jobboard')); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>actions.php" />
    <input type="hidden" name="paction" value="delete_killer_form"/>
    <input type="hidden" id="delete_id" name="id" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('You are going to delete killer questions form. Are you sure?' , 'jobboard'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <input type="submit" class="btn btn-red" value="<?php _e('Remove', 'jobboard'); ?>"/>
                <button type="button" class="btn" onclick="$('#dialog-killerform-delete').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></button>
            </div>
        </div>
    </div>
</form>