<?php

    if (Params::getParam('id') != '') {
        $id    = Params::getParam('id');
        $count = 0;

        if ( osc_is_web_user_logged_in() ) {
            //check if the item is not already in the watchlist
            $conn   = getConnection();
            $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_watchlist WHERE fk_i_item_id = %d and fk_i_user_id = %d", DB_TABLE_PREFIX, $id, osc_logged_user_id());

            if(osc_version()<320) {
                $url = osc_base_url(true).'?page=custom&file=watchlist/watchlist.php';
            } else {
                $url = osc_route_url('watchlist-user', array('iPage' => null));
            }

            //If nothing returned then we can process
            if (!isset($detail['fk_i_item_id'])) {
                $conn = getConnection();
                $conn->osc_dbExec("INSERT INTO %st_item_watchlist (fk_i_item_id, fk_i_user_id) VALUES (%d, '%d')", DB_TABLE_PREFIX, $id, osc_logged_user_id());
                ?>
                <span align="left">
                    <a style="color: inherit;" href="<?php echo $url; ?>">
                        <?php _e('View your watchlist', 'watchlist') ?>
                    </a>
                </span>
                <?php
            } else {
                //Already in watchlist !
                echo '<span align="left"><a style="color: inherit;" href="' . $url . '">' . __('View your watchlist', 'watchlist') . '</a></span>';
            }
        } else {
            //error user is not login in
            echo '<a style="color: inherit;" href="' . osc_user_login_url() . '">' . __('Please login', 'watchlist') . '</a>';
        }
    }

?>