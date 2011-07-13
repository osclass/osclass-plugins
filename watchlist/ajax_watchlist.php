<?php
if($_POST['id'])
	{
	$id=$_POST['id'];
    $count =0;

	// Check if user is login in
	$logged = osc_is_web_user_logged_in();

		if($logged == 1){

		//check if the item is not already in the watchlist
		  $conn   = getConnection();
		  $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_watchlist WHERE fk_i_item_id = %d and fk_i_user_id = %d", DB_TABLE_PREFIX, $id,osc_logged_user_id());
		  
		    //If nothing returned then we can process
			if(!isset($detail['fk_i_item_id']))
			{
				$conn = getConnection();
                $conn->osc_dbExec("INSERT INTO %st_item_watchlist (fk_i_item_id, fk_i_user_id) VALUES (%d, '%d')", DB_TABLE_PREFIX, $id, osc_logged_user_id());
				?>
				<span align="left"><a href="index.php?page=custom&file=watchlist/watchlist.php"><?php _e('View your watchlist', 'watchlist')?></a></span>
				<?
			}
			else
			{
				//Already in watchlist !
				echo '<span align="left"><a href="index.php?page=custom&file=watchlist/watchlist.php">'.__('View your watchlist', 'watchlist').'</a></span>';
			}
		}else{
			//error user is not login in

			echo '<a href="'.osc_user_login_url().'">'.__('Please login', 'watchlist').'</a>';
		}

}
?>