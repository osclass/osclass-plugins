<script type="text/javascript">
jQuery(document).ready(function($) {
	$(".watchlist").click(function() {
		var id = $(this).attr("id");
		var dataString = 'id='+ id ;
		var parent = $(this);

		$(this).fadeOut(300);
		$.ajax({
		type: "POST",
		url: "<?php echo osc_ajax_plugin_url('watchlist/ajax_watchlist.php');?>",
		data: dataString,
		cache: false,

		success: function(html)
		{
		parent.html(html);
		parent.fadeIn(300);
		}
		});
	});
});
</script>