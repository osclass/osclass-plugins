        <?php $path = osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__); ?>
        <div class="votes_stars">
            <?php if( $vote['can_vote'] ) { ?>
            <div class="votes_vote">
                <div class="votes_txt_vote"><?php _e('Vote', 'voting');?></div>
                <div class="votes_star">
                    <span id="">
                        <a href="#" rel="nofollow" title="<?php _e('Without interest', 'voting');?>" class="aPs vote1"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Uninteresting', 'voting');?>" class="aPs vote2"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Interesting', 'voting');?>" class="aPs vote3"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Very interesting', 'voting');?>" class="aPs vote4"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Essential', 'voting');?>" class="aPs vote5"></a>
                    </span>
                </div>
                <img width="1" height="19" alt="" src="<?php echo $path; ?>/img/ico_separator.gif">
            </div>
            <?php } ?>
            <div class="votes_results">
                <span style="float:left; padding-right: 4px;"><?php _e('Result', 'voting');?>  </span>
                <?php
                    $avg_vote = $vote['vote'];
                    if($avg_vote==5) {
                        $tooltip  = __('Essential', 'voting');
                    } else if($avg_vote>=4 && $avg_vote<5) {
                        $tooltip = __('Very interesting', 'voting');
                    } else if($avg_vote>=3 && $avg_vote<4) {
                        $tooltip = __('Interesting', 'voting');
                    } else if($avg_vote>=2 && $avg_vote<3) {
                        $tooltip = __('Uninteresting', 'voting');
                    } else if($avg_vote>=1 && $avg_vote<2) {
                        $tooltip = __('Without interest', 'voting');
                    } else {
                        $tooltip = __('Without information', 'voting');
                    }
                ?>
                <img title="<?php echo $tooltip; ?>" src="<?php voting_star(1, $avg_vote); ?>">
                <img title="<?php echo $tooltip; ?>" src="<?php voting_star(2, $avg_vote); ?>">
                <img title="<?php echo $tooltip; ?>" src="<?php voting_star(3, $avg_vote); ?>">
                <img title="<?php echo $tooltip; ?>" src="<?php voting_star(4, $avg_vote); ?>">
                <img title="<?php echo $tooltip; ?>"  src="<?php voting_star(5, $avg_vote); ?>">
                <span style="float:left; padding-right: 4px; padding-left: 4px;"><?php echo $vote['total'];?> <?php _e('votes', 'voting');?></span>
            </div>
        </div>