<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $status = jobboard_status();
    $mjb = ModelJB::newInstance();
?>
</div>
</div>
<div style="width:900px">
    <div class="grid-row grid-first-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3><?php _e('Activitiy', 'jobboard'); ?></h3></div>
                <div class="widget-box-content">
                    <table cellpadding="0" cellspacing="0" id="activity-stat">
                        <tbody>
                            <tr>
                                <td>
                                    <a href="<?php echo osc_admin_base_url(true); ?>?page=items" style="text-decoration:none;">
                                        <div class="card card-vacancies">
                                            <div class="container">
                                                <div class="icon-car"></div>
                                                <span><?php _e('Vacancies','jobboard'); ?></span>
                                                <?php
                                                $mSearch = new Search(true);
                                                $mSearch->addItemConditions(DB_TABLE_PREFIX.'t_item.b_enabled = 1');
                                                ?>
                                            </div>
                                            <b><?php echo $mSearch->count(); ?></b>
                                        </div>
                                    </a>
                                </td>
                                <td class="separate-cl">&nbsp;</td>
                                <td>
                                    <a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>" style="text-decoration:none;">
                                        <div class="card card-applicants">
                                            <div class="container">
                                                <div class="icon-car"></div>
                                                <span><?php _e('Applicants','jobboard'); ?></span>
                                                <?php
                                                list($iTotalDisplayRecords, $iTotalRecords) = ModelJB::newInstance()->searchCount();
                                                ?>
                                            </div>
                                            <b><?php echo $iTotalRecords; ?></b>
                                        </div>
                                    </a>
                                </td>
                                <td class="separate-cl">&nbsp;</td>
                                <td>
                                    <a href="<?php echo osc_admin_base_url(true); ?>?page=items" style="text-decoration:none;">
                                        <div class="card card-views">
                                            <div class="container">
                                                <div class="icon-car"></div>
                                                <span><?php _e('Total views','jobboard'); ?></span>
                                                <?php $allViews = ItemStats::newInstance()->getAllViews(); ?>
                                            </div>
                                            <b><?php echo $allViews; ?></b>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <?php   $mSearch4 = new Search(true);
                                        $mSearch4->addTable(DB_TABLE_PREFIX."t_item_stats");
                                        $mSearch4->addField("SUM(".DB_TABLE_PREFIX."t_item_stats.i_num_views) as i_num_views");
                                        $mSearch4->addConditions(DB_TABLE_PREFIX."t_item_stats.fk_i_item_id = ".DB_TABLE_PREFIX."t_item.pk_i_id");
                                        $mSearch4->order('i_num_views');
                                        $mSearch4->set_rpp(1);
                                        $mSearch4->addGroupBy("fk_i_item_id");
                                        $mostViewedJob = $mSearch4->doSearch();?>
                                <td colspan="5"><div class="most-viwed"><span><?php _e('Most viewed', 'jobboard'); ?> - <b><?php echo $mostViewedJob[0]['i_num_views']." ".__('views', 'jobboard'); ?></b></span><a href="<?php echo osc_item_admin_edit_url($mostViewedJob[0]['fk_i_item_id']); ?>"><?php echo osc_highlight($mostViewedJob[0]['s_title'],30); ?></a></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-first-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3><?php _e('Most viewed jobs', 'jobboard'); ?></h3></div>
                <div class="widget-box-content">
                    <table class="table" cellpadding="0" cellspacing="0" id="applicants_list">
                        <tbody>
                            <thead>
                                <th><?php _e('Job','jobboard'); ?></th>
                                <th><?php _e('Views','jobboard'); ?></th>
                            </thead>
                            <?php $mSearch3 = new Search(true);
                            $mSearch3->addTable(DB_TABLE_PREFIX."t_item_stats");
                            $mSearch3->addField("SUM(".DB_TABLE_PREFIX."t_item_stats.i_num_views) as i_num_views");
                            $mSearch3->addConditions(DB_TABLE_PREFIX."t_item_stats.fk_i_item_id = ".DB_TABLE_PREFIX."t_item.pk_i_id");
                            $mSearch3->order('i_num_views');
                            $mSearch3->set_rpp(7);
                            $mSearch3->addGroupBy("fk_i_item_id");
                            $jobs = $mSearch3->doSearch(); ?>
                            <?php foreach($jobs as $job) { ?>
                            <tr>
                                <td><a href="<?php echo osc_item_admin_edit_url($job['pk_i_id']); ?>" ><?php echo osc_highlight($job['s_title'],30); ?></a></td>
                                <td><?php echo $job['i_num_views']; ?></td>
                            </tr>
                            <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-first-row grid-100">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3 class="has-tabs"><?php _e('Recent applicants', 'jobboard'); ?></h3>
                    <ul class="tabs">
                        <?php foreach($status as $k => $v) {
                            echo '<li><a href="#status-'.$k.'">'.$v.'</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="widget-box-content">
                    <?php foreach($status as $k => $v) {
                        echo '<div id="status-'.$k.'">';
                        echo '<table class="table" cellpadding="0" cellspacing="0"><tbody>';
                        echo '<thead><th>'.__('Applicant','jobboard').'</th><th>'.__('Job title','jobboard').'</th><th>'.__('Received','jobboard').'</th></thead>';
                        $people = ModelJB::newInstance()->search(0, 6, array('status'=>$k), 'a.dt_date', 'DESC');
                            if(count($people)){
                                foreach($people as $applicant){
                                    $item = Item::newInstance()->findByPrimaryKey($applicant['fk_i_item_id']);
                                    //Notes
                                    $notes = ModelJB::newInstance()->getNotesFromApplicant($applicant['pk_i_id']);
                                    $note_tooltip = '';
                                    for($i = 0; $i < count($notes); $i++) {
                                        $note_tooltip .= sprintf('<strong>%1$s</strong> - %2$s', date('d/m/Y H:i', strtotime($notes[$i]['dt_date'])), $notes[$i]['s_text']);
                                        if( $i < (count($notes) - 1) ) {
                                            $note_tooltip .= '<br/>';
                                        }
                                    }
                                    echo '<tr>';
                                    echo '<td><a href="'.osc_admin_render_plugin_url("jobboard/people_detail.php").'&people='.$applicant['pk_i_id'].'">'.$applicant['s_name']; if($applicant['b_has_notes'] == 1 ) { echo '<span class="note" data-tooltip="'.$note_tooltip.'"></span>'; } echo '</a></td>';
                                    echo '<td>'.osc_highlight(@$item['s_title'],30).'</td>';
                                    echo '<td>'._jobboard_time_elapsed_string( strtotime(@$applicant['dt_date']) ) .'</td>';
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
        $('.widget-box-title .tabs').each(function(){
            $(this).find('li:first').click();
        })
    });
</script>