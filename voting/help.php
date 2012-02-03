<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <h1>Help</h1>
    <h2><?php _e('Plugin information', 'voting') ; ?></h2>
    <p>
        <?php _e('This plugin adds a rating system and allows users to vote among them the quality of the item and quality sellers', 'voting') ; ?>.
        </p>
    <p>
        <ul>
            <li><?php _e('Easy plugin configuration.', 'voting') ; ?></li>
            <li><?php _e('Vote items, can be enabled and disabled. Can be configured what kind of users can vote items, registered users only or guest too', 'voting') ; ?></li>
            <li><?php _e('Vote users, can be enabled and disabled. Only registered users can vote sellers', 'voting') ; ?></li>
            <li><?php _e('Allows to show the best rated items or users at frontend, adding some extra code at your template', 'voting') ; ?>.</li>
        </ul>
    </p>
    
    <hr>
    
    <h2><?php _e('Show best rated', 'voting') ;?></h2>
    <p>
        <?php _e('You can display the best rated list, where you want', 'voting') ; ?>.
    </p>
    <p>
        <?php _e('Adding this line of code, you can show the items or users at main web page, into sidebar', 'voting') ; ?>
        <br>
    </p>
    <p>
        <p><b><?php _e('Items', 'voting');?></b></p>
        <code>
            <?php echo htmlentities('<?php'); ?> echo_best_rated(NUMBER_OF_ITEMS); ?><br>
        </code>
        <br>
        <em><?php _e('Edit main.php (located under root theme folder)', 'voting') ; ?></em>
        <br>
        <br>
        <code>
            <span style="padding-left: 10px;"><?php echo htmlentities('<div id="sidebar">'); ?></span>
            <br/>
            <span style="padding-left: 30px;"><?php echo htmlentities('<div class="navigation">'); ?> </span>
            <br/>
            <span style="padding-left: 50px;"><?php echo htmlentities('<?php'); ?> echo_best_rated(3); ?></span> 
            <br/>
            <span style="padding-left: 50px;">...</span> 
            <br/>
            <span style="padding-left: 30px;"><?php echo htmlentities('</div>'); ?> </span>
            <br/>
            <span style="padding-left: 10px;"><?php echo htmlentities('</div>'); ?></span>
            <br/>
        </code>
    </p>
    <p>
        <p><b><?php _e('Users', 'voting');?></b></p>
        <code>
            <?php echo htmlentities('<?php'); ?> echo_users_best_rated(NUMBER_OF_USERS); ?><br>
        </code>
        <br>
        <em><?php _e('Edit main.php (located under root theme folder)', 'voting') ; ?></em>
        <br>
        <br>
        <code>
            <span style="padding-left: 10px;"><?php echo htmlentities('<div id="sidebar">'); ?></span>
            <br/>
            <span style="padding-left: 30px;"><?php echo htmlentities('<div class="navigation">'); ?> </span>
            <br/>
            <span style="padding-left: 50px;"><?php echo htmlentities('<?php'); ?> echo_users_best_rated(3); ?></span> 
            <br/>
            <span style="padding-left: 50px;">...</span> 
            <br/>
            <span style="padding-left: 30px;"><?php echo htmlentities('</div>'); ?> </span>
            <br/>
            <span style="padding-left: 10px;"><?php echo htmlentities('</div>'); ?></span>
            <br/>
        </code>
    </p>
   
   
    <hr>
    
    <h2><?php _e('Change place of "voting plugin"', 'voting') ; ?></h2>
    <p>
        <b><?php _e('Vote section', 'voting');?></b> <?php _e('by default is added below item description, but if you need place into another place follow this instructions:' ,'voting' ) ; ?>
    </p>
    <p><b><?php _e('Items', 'voting') ; ?></b></p>
    
    <p><?php _e('You need add this line at end of functions.php (located under root theme folder) :', 'voting' ) ; ?><br></p>
    <code>
        <?php echo htmlentities('<?php'); ?><br/> 
        osc_remove_hook('item_detail', 'voting_item_detail'); 
        <br/>
        ?>
    </code>
    
    <p><?php _e('Now you can call function', 'voting'); ?> <code>voting_item_detail()</code> <?php _e('directly where you want into item page', 'voting');?>.</p>
    
    <code>
        <?php echo htmlentities('<?php'); ?><br/>
        voting_item_detail(); 
        <br/>
        ?>
    </code>
    <p><b><?php _e('Users', 'voting') ; ?></b></p>
    
    <p><?php _e('You need add this line at end of functions.php (located under root theme folder) :', 'voting' ) ; ?><br></p>
    <code>
        <?php echo htmlentities('<?php'); ?><br/> 
        osc_remove_hook('item_detail', 'voting_item_detail_user'); 
        <br/>
        ?>
    </code>
    
    <p><?php _e('Now you can call function', 'voting'); ?> <code>voting_item_detail_user()</code> <?php _e('directly where you want into item page', 'voting');?>.</p>
    
    <code>
        <?php echo htmlentities('<?php'); ?><br/>
        voting_item_detail_user(); 
        <br/>
        ?>
    </code>
    
    <p><h3><?php _e('If you want modify the templates, you can find them in the plugin folder', 'voting');?>.</h3></p>
    <p><b>view_votes.php</b> <?php _e('Template rating items', 'voting') ; ?></p>
    <p><b>view_votes_user.php</b> <?php _e('Template rating users', 'voting') ; ?></p>
    <br>
    <p><b>set_results.php</b>  <?php _e('Template best rated items', 'voting') ; ?></p>
    <p><b>set_results_user.php</b>  <?php _e('Template best rated users', 'voting') ; ?></p>
    
</div>