        <?php $path = osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__); ?>
        <div class="votes_stars">
            <div style="float:left;padding-right: 5px;"><?php $aux = User::newInstance()->findByPrimaryKey($vote['userId']); echo $aux['s_name']; ?></div>
            <?php if( $vote['can_vote'] ) { ?>
            <div class="votes_vote">
                <div class="votes_star">
                    <span id="">
                        <a href="#" rel="nofollow" title="<?php _e('Without interest', 'voting');?>" class="aPvu vote1"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Uninteresting', 'voting');?>" class="aPvu vote2"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Interesting', 'voting');?>" class="aPvu vote3"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Very interesting', 'voting');?>" class="aPvu vote4"></a>
                        <a href="#" rel="nofollow" title="<?php _e('Essential', 'voting');?>" class="aPvu vote5"></a>
                    </span>
                </div>
                <img width="1" height="19" alt="" src="<?php echo $path; ?>/img/ico_separator.gif">
            </div>
            <?php } ?>
            <div class="votes_results">
                <?php 
                    $avg_vote = $vote['vote'];
                ?>
                <img title="<?php _e('Without interest', 'voting');?>" src="<?php voting_star(1, $avg_vote); ?>">
                <img title="<?php _e('Uninteresting', 'voting');?>" src="<?php voting_star(2, $avg_vote); ?>">
                <img title="<?php _e('Interesting', 'voting');?>" src="<?php voting_star(3, $avg_vote); ?>">
                <img title="<?php _e('Very interesting', 'voting');?>" src="<?php voting_star(4, $avg_vote); ?>">
                <img title="<?php _e('Essential', 'voting');?>"  src="<?php voting_star(5, $avg_vote); ?>"> 
                <span style="float:left; padding-right: 4px; padding-left: 4px;"><?php echo $vote['total'];?> <?php _e('votes', 'voting');?></span>
            </div>
        </div>
