        <div class="votes_stars">
            <?php if( $vote_['can_vote'] ) { ?>
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
                    $avg_vote = $vote_['vote'];
                ?>
                <img title="<?php _e('Without interest', 'voting');?>" src="<?php voting_star(1, $avg_vote); ?>">
                <img title="<?php _e('Uninteresting', 'voting');?>" src="<?php voting_star(2, $avg_vote); ?>">
                <img title="<?php _e('Interesting', 'voting');?>" src="<?php voting_star(3, $avg_vote); ?>">
                <img title="<?php _e('Very interesting', 'voting');?>" src="<?php voting_star(4, $avg_vote); ?>">
                <img title="<?php _e('Essential', 'voting');?>"  src="<?php voting_star(5, $avg_vote); ?>"> 
                <span style="float:left; padding-right: 4px; padding-left: 4px;"><?php echo $vote_['total'];?> <?php _e('votes', 'voting');?></span>
            </div>
        </div>