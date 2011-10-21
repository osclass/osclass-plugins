<?php $path = osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__); ?>
<div id="wrapper_voting_plugin">
    <script type="text/javascript">
    $(document).ready(function(){
        
        $('.aPs').live('click', function(){
            var params = '';
            var vote   = 0;
            if( $(this).hasClass('vote1') ) vote = 1;
            if( $(this).hasClass('vote2') ) vote = 2;
            if( $(this).hasClass('vote3') ) vote = 3;
            if( $(this).hasClass('vote4') ) vote = 4;
            if( $(this).hasClass('vote5') ) vote = 5;

            var itemId = <?php echo osc_item_id(); ?>;
            params = 'itemId='+itemId+'&vote='+vote;

            $.ajax({
                type: "POST",
                url: '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__).'ajax.php'?>&'+params,
                dataType: 'text',
                beforeSend: function(){
                    $('#voting_plugin').hide();
                    $('#voting_loading').fadeIn('slow');
                },
                success: function(data){
                    $('#voting_loading').fadeOut('slow', function(){
                        $('#voting_plugin').html(data).fadeIn('slow');
                    });
                }
            });
        });
    });
    </script>

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
    <span id="voting_loading" style="display:none;"><img src="<?php echo $path; ?>img/spinner.gif" style="margin-left:20px;"/> <?php _e('Loading', 'voting');?></span>
    <div id="voting_plugin">
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
            <div class="votes_results" >
                <span style="float:left; padding-right: 4px;"><?php _e('Result', 'voting');?>  </span>
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
    </div>
    <div style="clear:both;"></div>
</div>