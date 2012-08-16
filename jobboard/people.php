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
    if(Params::getParam('jobId')!='') {
        $conditions['item'] = Params::getParam('jobId');
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
    
    $people = ModelJB::newInstance()->search($start, $iDisplayLength, $conditions);
    list($iTotalDisplayRecords, $iTotalRecords) = ModelJB::newInstance()->searchCount($conditions);
    $status = jobboard_status();
    
    $opt = Params::getParam('opt');
    

    
?>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.MetaData.js" type="text/javascript" language="javascript"></script>
<link href="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
    $(document).ready(function() {
        $('.auto-star').rating({
            callback: function(value, link, input){
                var data = value.split("_");
                $.getJSON(
                    "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__);?>ajax.php&paction=rating",
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
        
    });
    
    function showPage() {
        window.location = '<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>&iDisplayLength='+$("#iDisplayLength option:selected").attr("value")+'&opt='+$("#filter-select option:selected").attr("value")+'&sSearch='+$("#sSearch").attr("value");
        return false;
    }
</script>
<div>
    <h1><?php _e('Resumes', 'jobboard'); ?></h1>
    
</div>
<div style="clear:both;"></div>
    <div id="listing-toolbar">
        <div class="float-right">
            <form method="get" action="<?php echo osc_admin_base_url(true); ?>" >
                <select id="iDisplayLength" name="iDisplayLength" class="select-box-extra select-box-medium float-left" onchange="javascript:showPage();" >
                    <option value="10"><?php printf(__('%d Listings', 'jobboard'), 10); ?></option>
                    <option value="25" <?php if( Params::getParam('iDisplayLength') == 25 ) echo 'selected'; ?> ><?php printf(__('%d Listings', 'jobboard'), 25); ?></option>
                    <option value="50" <?php if( Params::getParam('iDisplayLength') == 50 ) echo 'selected'; ?> ><?php printf(__('%d Listings', 'jobboard'), 50); ?></option>
                    <option value="100" <?php if( Params::getParam('iDisplayLength') == 100 ) echo 'selected'; ?> ><?php printf(__('%d Listings', 'jobboard'), 100); ?></option>
                </select>
            </form>
            <form method="get" action="<?php echo osc_admin_base_url(true); ?>" id="shortcut-filters" class="inline">
                <select id="filter-select" name="shortcut-filter" class="select-box-extra select-box-input">
                    <option value="oEmail" <?php if($opt == 'oEmail'){ echo 'selected="selected"'; } ?>><?php _e('E-mail', 'jobboard') ; ?></option>
                    <option value="oName" <?php if($opt == 'oName'){ echo 'selected="selected"'; } ?>><?php _e('Name', 'jobboard') ; ?></option>
                    <option value="oItem" <?php if($opt == 'oItem'){ echo 'selected="selected"'; } ?>><?php _e('Job', 'jobboard') ; ?></option>
                </select>
                <input type="text" id="sSearch" name="sSearch" value="<?php echo osc_esc_html(Params::getParam('sSearch')); ?>" />

                <input type="submit" id="filter_btn" class="btn submit-right" value="<?php echo osc_esc_html( __('Find', 'jobboard') ) ; ?>">
            </form>
        </div>
    </div>

<div id="upload-plugins">
    <table class="table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?php _e('Applicant', 'jobboard') ; ?></th>
                <th><?php _e('Job', 'jobboard') ; ?></th>
                <th><?php _e('Status', 'jobboard') ; ?></th>
                <th><?php _e('Rating', 'jobboard') ; ?></th>
                <th><?php _e('Received', 'jobboard') ; ?></th>
                <th><?php _e('Actions', 'jobboard') ; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if(count($people)>0) { ?>
        <?php foreach($people as $p) { ?>
            <tr>
                <td><a href="<?php echo osc_admin_render_plugin_url("jobboard/people_detail.php");?>&people=<?php echo $p['pk_i_id']; ?>" title="<?php echo @$p['s_name']; ?>" ><?php echo @$p['s_name']; ?></a></td>
                <td><?php echo @$p['s_title']; ?></td>
                <td><?php echo $status[isset($p['i_status'])?$p['i_status']:0]; ?></td>
                <td>
                    <?php for($k=1;$k<=5;$k++) {
                        echo '<input name="star'.$p['pk_i_id'].'" type="radio" class="auto-star required" value="'.$p['pk_i_id'].'_'.$k.'" title="'.$k.'" '.($k==$p['i_rating']?'checked="checked"':'').'/>';
                    } ?>
                </td>
                <td><?php echo @$p['dt_date']; ?></td>
                <td><?php _e("Delete", "jobboard"); ?></td>
            </tr>
        <?php }; ?>
        <?php  } else { ?>
        <tr>
            <td colspan="6" class="text-center">
            <p><?php _e('No data available in table', 'jobboard') ; ?></p>
            </td>
        </tr>
        <?php }; ?>
        </tbody>
    </table>
    <?php
        $aData = array(
            'iTotalDisplayRecords' => $iTotalDisplayRecords
            ,'iTotalRecords' => $iTotalRecords
            ,'iDisplayLength' => $iDisplayLength
            ,'iPage' => $iPage
        );
        echo osc_pagination_showing((($iPage-1)*$iDisplayLength)+1, (($iPage-1)*$iDisplayLength)+count($people), $iTotalDisplayRecords, $iTotalRecords);
        osc_show_pagination_admin($aData);
    ?>
    </div>
<div style="clear:both;"></div>