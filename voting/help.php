<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <h1>Help</h1>
    <h2>Change place of "voting plugin"</h2>
    <p>
        'Vote section' by default is added below item description, but if you need place into another place follow this instructions:
    </p>
    <p>
        You need add this line at end of functions.php (located under root theme folder) :
    </p>
    
    <code>
        <?php echo htmlentities('<?php'); ?><br/> 
        osc_remove_hook('item_detail', 'voting_item_detail'); 
        <br/>
        ?>
    </code>
    
    <p>Now you can call function <code>voting_item_detail()</code> directly where you want into item page.</p>
    
    <code>
        <?php echo htmlentities('<?php'); ?><br/>
        voting_item_detail(); 
        <br/>
        ?>
    </code>
    
    <h2>Show best rated items</h2>
    <p>
        You can display the best rated items, where you want.
    </p>
    <p>
        Adding this line of code, you can show the items at main web page, into sidebar.
    </p>
    <code>
        <?php echo htmlentities('<?php'); ?> echo_best_rated(NUMBER_OF_ITEMS); ?>
    </code>
    <p>
        Edit main.php (located under root theme folder):
    </p>
    <code>
        <span style="padding-left: 10px;"><?php echo htmlentities('<div id="sidebar">'); ?></span>
        <br/>
        <span style="padding-left: 30px;"><?php echo htmlentities('<div class="navigation">'); ?> </span>
        <br/>
        <span style="padding-left: 50px;"><?php echo htmlentities('<?php'); ?> echo_best_rated(3); ?></span> 
        <br/>
    </code>
</div>