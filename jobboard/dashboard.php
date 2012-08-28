<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }
    
    $status = jobboard_status();
    $mjb = ModelJB::newInstance();
    
?>
<style>
.widget-box-content {
height: 230px;
overflow-y: auto;
}
.widget-box-title{
    height: 21px
}
.widget-box-title .tabs{
    float:right;
    margin:0px 0px 0 0;
    padding:0;
}
.widget-box-title .tabs li{
    float:left;

    border: 1px solid #DDD;
    border-bottom:0px;
    background: #E6E6E6;
    font-weight: normal;
    color: #212121;
    margin:0 2px 0 0px;
    border-radius: 4px 4px 0 0;
    -moz-border-radius: 4px 4px 0 0;
    -webkit-border-radius: 4px 4px 0 0;
}
.widget-box-title .tabs li.active{
    border-color:#DDD;
    background: #fff;
    color: #212121;
}
.widget-box-title .tabs li a{
    float: left;
    padding:6px 10px;
    text-decoration: none;
    font-size:13px;
    color:#555;
}
.widget-box-title h3.has-tabs{
    float:left;
}
.widget-box-content p.view-all{
    margin-bottom:0;
}
</style>
</div>
</div>
    <div class="grid-row grid-first-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3 class="has-tabs"><?php _e('Last applicants', 'jobboard'); ?></h3>
                    <ul class="tabs">
                        <?php foreach($status as $k => $v) { 
                            echo '<li><a href="#status-'.$k.'">'.__($v).'</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="widget-box-content">
                    <?php foreach($status as $k => $v) { 
                        echo '<div id="status-'.$k.'">';
                        echo '<table class="table" cellpadding="0" cellspacing="0" id="applicants_list"><tbody>';
                        echo '<thead><th>'.__('Applicant','jobboard').'</th><th>'.__('Job','jobboard').'</th><th>'.__('Received','jobboard').'</th></thead>';
                        $people = ModelJB::newInstance()->search(0, 6, array('status'=>$k), 'a.dt_date', 'DESC');
                            if(count($people)){
                                foreach($people as $applicant){
                                    $item = Item::newInstance()->findByPrimaryKey($applicant['fk_i_item_id']);
                                    echo '<tr>';
                                    echo '<td><a href="'.osc_admin_render_plugin_url("jobboard/people_detail.php").'&people='.$applicant['pk_i_id'].'">'.$applicant['s_name'].'</a></td>';
                                    echo '<td>'.$item['s_title'].'</td>';
                                    echo '<td>'.@$applicant['dt_date'].'</td>';
                                    echo '</tr>';
                                }
                            }
                        echo '</tbody></table>';
                        echo '<p class="view-all"><a href="'.osc_admin_render_plugin_url("jobboard/people.php").'&iStatus='.$k.'">'.__('View all','jobboard').' '.$v.'</a></p>';
                        echo '</div>';
                    }
                    ?>
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
<script type="text/javascript">
    $(function() {
        $('.widget-box-title .tabs li').each(function(){
            var dest = function(el){
                var dests = new Array();
                el.parent().find('a').each(function(){
                    dests.push($(this).attr('href'));
                });
                return dests.join(',');
            }
            
            $(this).click(function(){
                $(this).parent().children().removeClass('active').filter($(this).addClass('active'));
                $(dest($(this))).hide().filter($(this).children().attr('href')).show();
                return false;
            });
        }).filter(':first').click();
    });
</script>