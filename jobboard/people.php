<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $length = 10;
    $start = (is_numeric(Params::getParam('iPage'))?Params::getParam("iPage"):0)*$length;
    $people = ModelJB::newInstance()->search($start, $length);

    
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
    });
</script>
<div>
    <h1><?php _e('Resumes', 'jobboard'); ?></h1>
    
</div>
<div style="clear:both;"></div>
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
                <td><?php echo jobboard_status(isset($p['i_status'])?$p['i_status']:0); ?></td>
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
        /*function showingResults(){
            $aData = __get('aPlugins');
            echo '<ul class="showing-results"><li><span>'.osc_pagination_showing((Params::getParam('iPage')-1)*$aData['iDisplayLength']+1, ((Params::getParam('iPage')-1)*$aData['iDisplayLength'])+count($aData['aaData']), $aData['iTotalDisplayRecords']).'</span></li></ul>' ;
        }
        osc_add_hook('before_show_pagination_admin','showingResults');
        osc_show_pagination_admin($aData);*/
    ?>
    </div>
<div style="clear:both;"></div>