<style>
    #wrapper_voting_plugin{
        margin-top:10px;
        padding: 5px;
    }

    .votes_txt_vote {
        display: inline;
        float: left;
    }

    .votes_results {
        display: inline;
        float: left;
    }

    .votes_results img {
        height: auto;
        margin-top: -1px;
        margin-left: -2px;
        vertical-align: middle;
        width: auto;
        border: 0 none;
        margin: 0;
        padding: 0;
        float: left;
    }

    .votes_star .vote1 {
        width: 15px;
        z-index: 14;
    }
    .votes_star .vote2 {
        width: 30px;
        z-index: 13;
    }
    .votes_star .vote3 {
        width: 45px;
        z-index: 12;
    }
    .votes_star .vote4 {
        width: 60px;
        z-index: 11;
    }
    .votes_star .vote5 {
        width: 75px;
        z-index: 10;
    }

    .votes_star a {
        display: block;
        height: 19px;
        position: absolute;
    }

    .votes_star {
        background: url("<?php echo osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__);?>img/ico_vot_vo.gif") repeat scroll 0 0 transparent;
        display: inline;
        float: left;
        height: 20px;
        margin: 0 4px 0 3px;
        position: relative;
        width: 76px;
    }

    .votes_vote {
        display: inline;
        float: left;
        margin-right: 5px;
    }

    .votes_star a:hover {
        background: url("<?php echo osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__);?>img/ico_vot_ov.gif") repeat-x scroll 0 0 transparent;
    }

    #voting_plugin {
        position: relative;
    }
</style>

<div class="box location">
    <h3><strong><?php _e("Best voted", 'voting') ; ?></strong></h3>
    <ul>
    <?php 
        $count = 0;
        foreach($results as $item_vote):
            $avg_vote = $item_vote['avg_vote'];
            $total    = $item_vote['num_votes'];
            $item    = Item::newInstance()->findByPrimaryKey($item_vote['item_id']);
    ?>
        <li>
            <?php if($count+1 < count($results)){?>
            <div style="border-bottom:1px black dashed;">
            <?php } else { ?>
            <div>
            <?php } ?>
                <p style="text-align: center;"><a href="<?php echo osc_item_url_ns($item['pk_i_id'])?>"><?php echo $item['locale'][$locale]['s_title'];?></a></p>
                <p style="text-align: center;">
                    <img title="<?php _e('Without interest', 'voting');?>" src="<?php voting_star(1, $avg_vote); ?>">
                    <img title="<?php _e('Uninteresting', 'voting');?>" src="<?php voting_star(2, $avg_vote); ?>">
                    <img title="<?php _e('Interesting', 'voting');?>" src="<?php voting_star(3, $avg_vote); ?>">
                    <img title="<?php _e('Very interesting', 'voting');?>" src="<?php voting_star(4, $avg_vote); ?>">
                    <img title="<?php _e('Essential', 'voting');?>"  src="<?php voting_star(5, $avg_vote); ?>"> 
                    <span style="position:relative; top:-5px;padding-right: 4px; padding-left: 4px; margin-bottom: 3px;"><?php echo $total;?> <?php _e('votes', 'voting');?></span>
                </p>
            </div>
        </li>
    <?php $count++;endforeach; ?>
    </ul>
</div>