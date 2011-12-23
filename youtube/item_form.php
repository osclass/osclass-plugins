<h2><?php _e('Youtube video', 'youtube'); ?></h2>
<div class="box">
    <div class="row">
        <?php printf( __( 'Enter the youtube url, i.e.: <em>%s</em> or <em>%s</em>', 'youtube' ), 'http://www.youtube.com/watch?v=ojqWclLQOxk', 'http://www.youtube.com/v/ojqWclLQOxk') ; ?>
    </div>
    <div class="row" style="width: 500px;">
        <input type="text" name="s_youtube" value="<?php echo $detail['s_youtube'] ; ?>" />
    </div>
</div>