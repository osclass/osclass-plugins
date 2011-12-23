<?php if( isset($detail['s_youtube']) && !empty($detail['s_youtube']) ) { ?>
<h2 style="margin-top: 10px;"><?php _e('Youtube video', 'youtube') ; ?></h3>
<object width="425" height="344">
    <param name="movie" value="<?php echo trim($detail['s_youtube']) ; ?>"></param>
    <param name="allowFullScreen" value="true"></param>
    <param name="allowScriptAccess" value="always"></param>
    <embed src="<?php echo $detail['s_youtube'] ; ?>"
      type="application/x-shockwave-flash"
      allowfullscreen="true"
      allowscriptaccess="always"
      width="425" height="344">
    </embed>
</object>
<?php } ?>