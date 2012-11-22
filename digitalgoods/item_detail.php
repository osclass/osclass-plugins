<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Digital Goods', 'digitalgoods'); ?></h3>
<div class="box">
    <div class="box dg_files">
            <?php
            if($dg_files!=null && is_array($dg_files) && count($dg_files)>0) {
                foreach($dg_files as $_r) { ?>
                    <div id="<?php echo $_r['pk_i_id'];?>" fkid="<?php echo $_r['fk_i_item_id'];?>" name="<?php echo $_r['s_name'];?>">
                        <label><?php echo $_r['s_name']; ?></label><a href="<?php echo osc_base_url()."oc-content/plugins/".osc_plugin_folder(__FILE__)."download.php?file=".$_r['s_code']."_".$_r['fk_i_item_id']."_".$_r['s_name'];?>" ><?php _e('Download', 'digitalgoods'); ?></a>
                    </div>
                <?php }
            }; ?>
    </div>
</div>
