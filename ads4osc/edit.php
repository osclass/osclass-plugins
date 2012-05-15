<script type="text/javascript"> 
    $(document).ready(function(){
        $("#ads-adtype").change(function(){
            var type = $("#ads-adtype").val();
            if(type=='CUSTOM') {
                $("#ads-text").hide();
                $("#ads-width").removeAttr("readonly");
                $("#ads-hide").removeAttr("readonly");
            } else {
                $("#ads-text").show();
                $("#ads-width").attr("readonly", true);
                $("#ads-hide").attr("readonly", true);
                var clean = $("#ads-adformat-text").val().split("#");
                var sizes = clean[0].split("x");
                $("#ads-width").val(sizes[0]);
                $("#ads-height").val(sizes[1]);
            }
        });
    });

    function update_size(size) {
        var clean = size.value.split("#");
        var sizes = clean[0].split("x");
        $("#ads-width").val(sizes[0]);
        $("#ads-height").val(sizes[1]);
    }
</script>
<form action="<?php echo osc_admin_render_plugin_url("ads4osc/launcher.php"); ?>" method="post" enctype="multipart/form-data">
    <div>
        <input type="hidden" name="ads-action" id="ads-action" value="save-settings">
        <input type="hidden" name="ads-id" id="ads-id" value="<?php echo $ad['pk_i_id']; ?>">
        <input type="hidden" name="ads-network" id="ads-network" value="<?php echo $ad['s_network']; ?>">

        <h2><?php _e('Edit Settings for Ad', 'ads4osc'); ?>: <input type="text" name="ads-title" value="<?php echo $ad['s_title']; ?>"></h2>
        <div>
            <span style="font-size:x-small;color:gray;"><?php _e('Enter the name for this ad. Ads with the same name will rotate according to their relative weights', 'ads4osc'); ?>.</span>
        </div>
        <?php if($ad['s_network'] == 'adsense') { ?>
        <div>
            <h3><span><?php _e('Account Details', 'ads4osc'); ?></span></h3>
            <div>
                <table>
                    <tr>
                        <td><label><?php _e('Account ID', 'ads4osc'); ?>:</label></td>
                        <td>
                            <input type="text" name="ads-account-id" style="width:200px" id="ads-account-id" value="<?php echo $ad['s_account_id']; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ads-partner"><?php _e('Partner ID', 'ads4osc'); ?>:</label></td>
                        <td>
                            <input type="text" name="ads-partner" style="width:200px" id="ads-partner" value="<?php echo $ad['s_partner_id']; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ads-slot"><?php _e('Slot ID', 'ads4osc'); ?>:</label></td>
                        <td>
                            <input type="text" name="ads-slot" style="width:200px" id="ads-slot" value="<?php echo $ad['s_slot_id']; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ads-counter"><?php _e('Max Ads Per Page', 'ads4osc'); ?>:</label></td>
                        <td>
                            <input type="text" name="ads-counter" style="width:200px" id="ads-counter" value="<?php echo $ad['i_max_ads_per_page']; ?>" />
                        </td>
                    </tr>
                </table>
                <br/>
                <span style="font-size:x-small; color:gray;"><?php _e('Enter the information specific to the Google Adsense ad type. The Partner ID is the ID for a partner revenue sharing account, usually your blog hosting provider.  Note that a Partner ID does not necessarily mean that your partner is sharing revenues.  Google Adsense will notify you if this is the case. Leave the Max Ads Per Page field blank if you do not want to restrict the number of ads per page', 'ads4osc'); ?>. </span>
            </div>
        </div>
        <?php } else { ?>
        <input type="hidden" name="ads-account-id" id="ads-account-id" value="<?php echo $ad['s_account_id']; ?>">
        <input type="hidden" name="ads-partner" id="ads-partner" value="<?php echo $ad['s_partner_id']; ?>">
        <input type="hidden" name="ads-slot" id="ads-slot" value="<?php echo $ad['s_slot_id']; ?>">
        <input type="hidden" name="ads-counter" id="ads-counter" value="<?php echo $ad['i_max_ads_per_page']; ?>">
        <?php } /* IF NETWORK ADSENSE */ ?>
        <div>
            <h3 class='hndle'><span><?php _e('Ad Format', 'ads4osc'); ?></span></h3>
            <div>
                <table>
                    <tr>
                        <td><label><?php _e('Ad Type', 'ads4osc'); ?>:</label></td>
                        <td>
                            <select name="ads-adtype" id="ads-adtype">
                                <option <?php if($ad['e_ad_type']=='CUSTOM') { echo 'selected="selected"'; } ?> value="CUSTOM"><?php _e('Use Custom', 'ads4osc'); ?></option>
                                <option <?php if($ad['e_ad_type']=='ALL') { echo 'selected="selected"'; } ?>value="ALL"><?php _e('All ad types', 'ads4osc'); ?></option>
                                <option <?php if($ad['e_ad_type']=='TEXT') { echo 'selected="selected"'; } ?>value="TEXT"><?php _e('Text ads', 'ads4osc'); ?></option>
                                <option <?php if($ad['e_ad_type']=='IMAGES') { echo 'selected="selected"'; } ?>value="IMAGES"><?php _e('Image & Rich Media ads', 'ads4osc'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="ads-text" <?php if($ad['e_ad_type']=='CUSTOM') { echo 'style="display:none"'; } ?>>
                        <td><label><?php _e('Format', 'ads4osc');?>:</label></td>
                        <td>
                            <select name="ads-format-text" id="ads-adformat-text" onchange="update_size(this);">
                                <optgroup label="Recommended">
                                    <option <?php if($ad['s_ad_format']=='300x250') { echo 'selected="selected"'; } ?> value="300x250"><?php _e('300 x 250 Medium Rectangle', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='336x280') { echo 'selected="selected"'; } ?> value="336x280"><?php _e('336 x 280 Large Rectangle', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='728x90') { echo 'selected="selected"'; } ?> value="728x90"><?php _e('728 x 90 Leaderboard', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='160x600') { echo 'selected="selected"'; } ?> value="160x600"><?php _e('160 x 600 Wide Skyscraper', 'ads4osc'); ?></option>
                                </optgroup>
                                <optgroup label="Horizontal">
                                    <option <?php if($ad['s_ad_format']=='468x60') { echo 'selected="selected"'; } ?> value="468x60"><?php _e('468 x 60 Banner', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='234x60') { echo 'selected="selected"'; } ?> value="234x60"><?php _e('234 x 60 Half Banner', 'ads4osc'); ?></option>
                                </optgroup>
                                <optgroup label="Vertical">
                                    <option <?php if($ad['s_ad_format']=='120x600') { echo 'selected="selected"'; } ?> value="120x600"><?php _e('120 x 600 Skyscraper', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='120x240') { echo 'selected="selected"'; } ?> value="120x240"><?php _e('120 x 240 Vertical Banner', 'ads4osc'); ?></option>
                                </optgroup>
                                <optgroup label="Square">
                                    <option <?php if($ad['s_ad_format']=='250x250') { echo 'selected="selected"'; } ?> value="250x250"><?php _e('250 x 250 Square', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='200x200') { echo 'selected="selected"'; } ?> value="200x200"><?php _e('200 x 200 Small Square', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='180x150') { echo 'selected="selected"'; } ?> value="180x150"><?php _e('180 x 150 Small Rectangle', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='125x125') { echo 'selected="selected"'; } ?> value="125x125"><?php _e('125 x 125 Button', 'ads4osc'); ?></option>
                                </optgroup>
                                <optgroup label="Text Links">
                                    <option <?php if($ad['s_ad_format']=='728x15') { echo 'selected="selected"'; } ?> value="336x280"><?php _e('336 x 280 Large Rectangle', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='468x15') { echo 'selected="selected"'; } ?> value="300x250"><?php _e('300 x 250 Medium Rectangle', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='200x90') { echo 'selected="selected"'; } ?> value="250x250"><?php _e('250 x 250 Square', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='180x90') { echo 'selected="selected"'; } ?> value="200x200"><?php _e('200 x 200 Small Square', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='160x90') { echo 'selected="selected"'; } ?> value="180x150"><?php _e('180 x 150 Small Rectangle', 'ads4osc'); ?></option>
                                    <option <?php if($ad['s_ad_format']=='120x90') { echo 'selected="selected"'; } ?> value="125x125"><?php _e('125 x 125 Button', 'ads4osc'); ?></option>
                                </optgroup>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ads-width"><?php _e('Dimensions', 'ads4osc'); ?>:</label></td>
                        <td>
                            <input name="ads-width" id="ads-width" size="5" title="Custom width for this unit." value="<?php echo $ad['i_ad_width']; ?>" /> x
                            <input name="ads-height" id="ads-height" size="5" title="Custom height for this unit." value="<?php echo $ad['i_ad_height']; ?>" /> px
                        </td>
                    </tr>
                </table>
                <br />
                <span style="display:none;font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select Custom and fill in your size', 'ads4osc'); ?>.</span>
            </div>
        </div>
<?php /*
    <div>
        <h3><span><?php _e('Display Options', 'ads4osc');?></span></h3>
        <div>
            <table>
            <tr>
                <td><label><?php _e('By Page Type:', 'ads4osc');?></label></td>
                <td>
                    <select id="ads-pagetype" name="ads-show-pagetype[]" multiple="multiple" size="3">

                        <option value=""></option>
                        <option value="home" selected='selected'><?php _e('Homepage', 'ads4osc');?></option>
                        <option value="page" selected='selected'><?php _e('Static Pages', 'ads4osc');?></option>
                        <option value="search" selected='selected'><?php _e('Search', 'ads4osc');?></option>

                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?php _e('By Category:', 'ads4osc');?></label></td>
                <td>
                    <input type="hidden" name="ads-show-category[]" value="">
                    <select id="ads-category" name="ads-show-category[]" multiple="multiple" size="5">
                    <option selected='selected' value="1"><?php _e('Uncategorized', 'ads4osc');?></option>
                    <?php $categories = osc_get_categories(); 
                    foreach($categories as $category) {
                        echo "<option value=\"".$category['pk_i_id']."\">".$category['s_name']."</option>";
                    }; ?>
                    </select>
                </td>
            </tr>
            </table>
        <br />
        <span style="font-size:x-small;color:gray;"><?php _e('Website display options determine where on your website your ads will appear. You could select multiples values for each option, just press "Ctrl + mouse click"', 'ads4osc');?></span>
        </div>
        */ ?>
        <div>
            <h2><?php _e('Advanced Options', 'ads4osc'); ?></h2>
            <div>
                <p>
                    <label><?php _e('Weight', 'ads4osc'); ?>:</label>
                    <input type="text" name="ads-weight" style="width:50px" id="ads-weight" value="<?php echo $ad['f_weight']; ?>" />
                </p>
                <br />
                <span style="font-size:x-small; color:gray;"><?php _e('Weight determines how often this ad is displayed relative to the other ads with the same name.  A weight of \'0\' will stop this ad from displaying', 'ads4osc'); ?>.</span>
            </div>
        </div>
    </div>
    <div>
        <h3><span><?php _e('Code', 'ads4osc'); ?></span></h3>
        <div>
            <table>
                <tr>
                    <td>
                    <label for="html_before"><?php _e('HTML Code Before', 'ads4osc'); ?></label><br />
                    <textarea rows="1" cols="57" name="ads-html-before" id="ads-html-before"><?php echo $ad['s_html_before']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                    <label for="ad_code"><?php _e('Ad Code', 'ads4osc'); ?></label><br />
                    <textarea rows="6" cols="60" name="ads-code" id="ads-code" style='background:#cccccc' <?php if($ad['s_network']=='adsense') { echo 'readonly'; } ?>><?php echo $ad['s_code']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                    <label for="html_after"><?php _e('HTML Code After', 'ads4osc');?></label><br />
                    <textarea rows="1" cols="57" name="ads-html-after" id="ads-html-after"><?php echo $ad['s_html_after']; ?></textarea>
                    </td>
                </tr>
            </table>
        </div>
        <br />
        <span style="font-size:x-small;color:gray;"><?php _e('Place any HTML code you want to display before or after your tag in the appropriate section. If you want to change your ad network tag, you need to import the new tag again', 'ads4osc'); ?>.</span>
    </div>
    <div>
        <input type="submit" value="<?php _e('Save', 'ads4osc'); ?>" />
    </div>
    <div class="clear"></div>
</form>