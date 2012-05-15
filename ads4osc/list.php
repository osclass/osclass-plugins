                <div class="dataTables_wrapper">
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="datatables_list">
                        <thead>
                            <tr>
                                <th style="width: 30%; " class="sorting"><?php _e('Name', 'ads4osc'); ?></th>
                                <th ><?php _e('Type', 'ads4osc'); ?></th>
                                <th ><?php _e('Format', 'ads4osc'); ?></th>
                                <th ><?php _e('Active', 'ads4osc'); ?></th>
                                <th ><?php _e('Default', 'ads4osc'); ?></th>
                                <th ><?php _e('Views Today', 'ads4osc'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $odd = 1;
                                foreach($ads as $ad) {
                                    if($odd==1) {
                                        $odd_even = "odd";
                                        $odd = 0;
                                    } else {
                                        $odd_even = "even";
                                        $odd = 1;
                                    }
                            ?>
                                <tr class="<?php echo $odd_even;?>">
                                    <td><?php echo $ad['s_title']; ?><div><a href="<?php echo osc_admin_render_plugin_url("ads4osc/launcher.php").'?ads-action=edit&ads-id='.$ad['pk_i_id']; ?>"><?php _e('Edit', 'ads4osc'); ?></a> | <a href="<?php echo osc_admin_render_plugin_url("ads4osc/launcher.php").'?ads-action=delete&ads-id='.$ad['pk_i_id']; ?>"><?php _e('Delete' ,'ads4osc'); ?></a></div></td>
                                    <td><?php echo $ad['e_ad_type']; ?></td>
                                    <td><?php echo $ad['i_ad_width']."x".$ad['i_ad_height']; ?></td>
                                    <td><?php echo @$ad['b_active']; ?></td>
                                    <td><?php echo ''; ?></td>
                                    <td><?php echo $ad['i_num_views']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>