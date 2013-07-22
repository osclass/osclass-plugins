<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
$path = osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__); ?>
<div id="wrapper_voting_plugin">
    <script type="text/javascript">
    $(function(){
        $('.aPs').click(function(){
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
        <?php include('view_votes.php');?>
    </div>
    <div style="clear:both;"></div>
</div>