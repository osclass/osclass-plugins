<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $iDisplayLength = 10;
    $iPage = Params::getParam('iPage');
    $iPage = is_numeric($iPage)?($iPage):1;
    $iDisplayLength = (is_numeric($iDisplayLength)?$iDisplayLength:10);
    $start = ($iPage-1)*$iDisplayLength;

    $conditions = array();
    if(Params::getParam('jobId')!='') {
        $conditions['item'] = Params::getParam('jobId');
    }
    if(Params::getParam('iStatus')!='') {
        $conditions['status'] = Params::getParam('iStatus');
    }
    if(Params::getParam('viewUnread')=='1') {
        $conditions['unread'] = 1;
    }
    if(Params::getParam('onlySpontaneous')=='1') {
        $conditions['spontaneous'] = 1;
    }
    if(Params::getParam('sSearch')!='') {
        if(Params::getParam('opt')=='oItem') {
            $conditions['item_text'] = Params::getParam('sSearch');
        } else if(Params::getParam('opt')=='oName') {
            $conditions['name'] = Params::getParam('sSearch');
        } else if(Params::getParam('opt')=='oEmail') {
            $conditions['email'] = Params::getParam('sSearch');
        }
    }

    $order_col = Params::getParam('sOrderCol')!=''?Params::getParam('sOrderCol'):'a.dt_date';
    $order_dir = Params::getParam('sOrderDir')!=''?Params::getParam('sOrderDir'):'DESC';

    $people = ModelJB::newInstance()->search($start, $iDisplayLength, $conditions, $order_col, $order_dir);
    list($iTotalDisplayRecords, $iTotalRecords) = ModelJB::newInstance()->searchCount($conditions, $order_col, $order_dir);
    $status = jobboard_status();

    $opt = Params::getParam('opt');

    $urlOrder = osc_admin_base_url(true).'?'.$_SERVER['QUERY_STRING'];
    $urlOrder = preg_replace('/&iPage=(\d+)?/', '', $urlOrder) ;
    $urlOrder = preg_replace('/&sOrderCol=([^&]*)/', '', $urlOrder) ;
    $urlOrder = preg_replace('/&sOrderDir=([^&]*)/', '', $urlOrder) ;

    $mSearch = new Search();
    $mSearch->limit(0, 100);
    $aItems = $mSearch->doSearch();
    View::newInstance()->_exportVariableToView('items', $aItems) ;
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.auto-star').rating({
            callback: function(value, link, input){
                var data = value.split("_");
                $.getJSON(
                    "<?php echo osc_admin_ajax_hook_url('jobboard_rating'); ?>",
                    {"applicantId" : data[0], "rating" : data[1]},
                    function(data){
                    }
                );
            }
        });

        $("#filter_btn").click(function(){
            showPage();
            return false;
        });

        $("#dialog-people-delete").dialog({
            autoOpen: false,
            modal: true
        });
    });

    function showPage() {
        var checked = 0;
        if($("#viewUnread").attr("checked")=='checked') {
            checked = 1;
        }
        var checkedSpontaneous = 0;
        if($("#onlySpontaneous").attr("checked")=='checked') {
            checkedSpontaneous = 1;
        }
        window.location = '<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>&opt='+$("#filter-select option:selected").attr("value")+'&sSearch='+$("#sSearch").attr("value")+"&viewUnread="+checked+"&onlySpontaneous="+checkedSpontaneous;
        return false;
    }

    function delete_applicant(id) {
        $("#delete_id").attr("value", id);
        $("#dialog-people-delete").dialog('open');
    }
</script>
<h2 class="render-title"><?php _e('Resumes', 'jobboard'); ?></h2>
<div class="relative">
    <div id="listing-toolbar">
        <div class="float-right">
            <form method="get" action="<?php echo osc_admin_base_url(true); ?>" id="shortcut-filters" class="inline">
                <span style="float: left;margin-right: 2em;margin-top: 0.5em;">
                    <input type="checkbox" id="onlySpontaneous" name="onlySpontaneous" value="1" <?php if(Params::getParam('onlySpontaneous')=='1') { echo 'checked="checked"'; }; ?> /><?php _e("Only spontaneous", "jobboard"); ?>
                </span>
                <span style="float: left;margin-right: 2em;margin-top: 0.5em;">
                    <input type="checkbox" id="viewUnread" name="viewUnread" value="1" <?php if(Params::getParam('viewUnread')=='1') { echo 'checked="checked"'; }; ?> /><?php _e("View unread", "jobboard"); ?>
                </span>
                <select id="filter-select" name="shortcut-filter" class="select-box-extra select-box-input">
                    <option value="oEmail" <?php if($opt == 'oEmail'){ echo 'selected="selected"'; } ?>><?php _e('E-mail', 'jobboard') ; ?></option>
                    <option value="oName" <?php if($opt == 'oName'){ echo 'selected="selected"'; } ?>><?php _e('Name', 'jobboard') ; ?></option>
                    <?php /*<option value="oItem" <?php if($opt == 'oItem'){ echo 'selected="selected"'; } ?>><?php _e('Job', 'jobboard') ; ?></option>*/ ?>
                </select>
                <input type="text" id="sSearch" name="sSearch" value="<?php echo osc_esc_html(Params::getParam('sSearch')); ?>" class="input-text input-actions input-has-select float-left" />
                <input type="submit" id="filter_btn" class="btn submit-right float-left" value="<?php echo osc_esc_html( __('Find', 'jobboard') ) ; ?>"><div class="clear"></div>
            </form>
        </div>
    </div>
    <form id="datatablesForm" action="<?php echo osc_admin_base_url(true); ?>" method="get">
        <input type="hidden" name="page" value="plugins">
        <input type="hidden" name="action" value="renderplugin">
        <input type="hidden" name="file" value="jobboard/people.php">
        <div id="bulk-actions">
            <label>
                <select name="jobId" class="select-box-extra">
                    <option value=""><?php _e('All jobs', 'jobboard'); ?></option>
                    <?php while( osc_has_items() ) { ?>
                    <option value="<?php echo osc_item_id(); ?>" <?php if( Params::getParam('jobId') == osc_item_id() ) echo "selected" ?>><?php echo osc_item_title(); ?></option>
                    <?php } ?>
                </select> <input type="submit" class="btn" value="<?php echo osc_esc_html(__('View', 'jobboard')); ?>" />
            </label>
        </div>
        <div class="table-contains-actions">
            <table class="table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th <?php if($order_col=='a.s_name') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=a.s_name&sOrderDir=".($order_col=='a.s_name'?($order_dir=='ASC'?'DESC':'ASC'):'ASC');?>" >
                                <?php _e('Applicant', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='a.s_email') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=a.s_email&sOrderDir=".($order_col=='a.s_email'?($order_dir=='ASC'?'DESC':'ASC'):'ASC');?>" >
                                <?php _e('Email', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='d.s_title') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=d.s_title&sOrderDir=".($order_col=='d.s_title'?($order_dir=='ASC'?'DESC':'ASC'):'ASC');?>" >
                                <?php _e('Job', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='a.b_has_notes') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=a.b_has_notes&sOrderDir=".($order_col=='a.b_has_notes'?($order_dir=='DESC'?'ASC':'DESC'):'DESC');?>" >
                                <?php _e('Notes', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='a.i_status') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=a.i_status&sOrderDir=".($order_col=='a.i_status'?($order_dir=='DESC'?'ASC':'DESC'):'DESC');?>" >
                                <?php _e('Status', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='a.i_rating') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=a.i_rating&sOrderDir=".($order_col=='a.i_rating'?($order_dir=='DESC'?'ASC':'DESC'):'DESC');?>" >
                                <?php _e('Rating', 'jobboard') ; ?>
                            </a>
                        </th>
                        <th <?php if($order_col=='a.dt_date') { echo 'class="sorting_'.strtolower($order_dir).'"';}; ?>>
                            <a href="<?php echo $urlOrder."&sOrderCol=a.dt_date&sOrderDir=".($order_col=='a.dt_date'?($order_dir=='DESC'?'ASC':'DESC'):'DESC');?>" >
                                <?php _e('Received', 'jobboard') ; ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($people) > 0) { ?>
                <?php foreach($people as $p) { ?>
                    <tr style="background-color: <?php echo ($p['b_read']==1)?'#EDFFDF':'#FFF0DF'; ?>;" >
                        <td><a href="<?php echo osc_admin_render_plugin_url("jobboard/people_detail.php");?>&people=<?php echo $p['pk_i_id']; ?>" title="<?php echo @$p['s_name']; ?>" ><?php echo @$p['s_name']; ?></a><div class="actions">
                                <ul>
                                    <li><a href="javascript:delete_applicant(<?php echo $p['pk_i_id']; ?>);" ><?php _e("Delete", "jobboard"); ?></a></li>
                                </ul>
                            </div>
                        </td>
                        <td><?php echo @$p['s_email']; ?></td>
                        <td><?php echo $p['fk_i_item_id']==''?__('Spontaneous job', 'jobboard'):@$p['s_title']; ?></td>
                        <td><?php echo $p['b_has_notes']==1?__("Has notes", "jobboard"):__("No notes yet", "jobboard"); ?></td>
                        <td><?php echo $status[isset($p['i_status'])?$p['i_status']:0]; ?></td>
                        <td>
                            <div class="rater big-star">
                                <?php for($k=1;$k<=5;$k++) {
                                    echo '<input name="star'.$p['pk_i_id'].'" type="radio" class="auto-star required" value="'.$p['pk_i_id'].'_'.$k.'" title="'.$k.'" '.($k==$p['i_rating']?'checked="checked"':'').'/>';
                                } ?>
                            </div>
                        </td>
                        <td><?php echo @$p['dt_date']; ?></td>
                    </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="8" class="text-center">
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
        <li><span><?php echo osc_pagination_showing((($iPage-1)*$iDisplayLength)+1, (($iPage-1)*$iDisplayLength)+count($people), $iTotalDisplayRecords, $iTotalRecords); ?></span></li>
    </ul>
    <?php osc_show_pagination_admin($aData); ?>
</div>

<form id="dialog-people-delete" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Delete applicant', 'jobboard')); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>actions.php" />
    <input type="hidden" name="paction" value="delete_applicant" />
    <input type="hidden" id="delete_id" name="id" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Are you sure you want to delete this applicant?', 'jobboard'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-people-delete').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></a>
            <input id="people-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete', 'jobboard') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>