<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */
    if(Params::getParam('plugin_action')=='done') {
        osc_set_preference('hours', Params::getParam('hours'), 'simplecache', 'INTEGER');
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'simplecache') . '.</p></div>' ;
        osc_reset_preferences();
    } else if(Params::getParam('plugin_action')=='clear') {
        if(Params::getParam('items')==1) {
            simplecache_clear_items();
        }
        if(Params::getParam('feeds')==1) {
            simplecache_clear_feeds();
        }
        if(Params::getParam('search')==1) {
            simplecache_clear_search();
        }
        if(Params::getParam('allcache')==1) {
            simplecache_clear_all();
        }
        $cats = Params::getParam('categories');
        $countries = Params::getParam('countries');
        $regions = Params::getParam('regions');
        if(is_array($cats)) {
            foreach($cats as $c) {
                simplecache_clear_category($c);
            }
        }
        if(is_array($countries)) {
            foreach($countries as $c) {
                simplecache_clear_country($c);
            }
        }
        if(is_array($regions)) {
            foreach($regions as $r) {
                simplecache_clear_region($r);
            }
        }
    }
?>
<link href="<?php echo osc_current_admin_theme_styles_url('jquery.treeview.css') ; ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('jquery.treeview.js') ; ?>"></script>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Simle Cache Settings', 'simplecache'); ?></legend>
                <form name="simplecache_form" id="simplecache_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                    <div style="float: left; width: 100%;">
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
                    <input type="hidden" name="plugin_action" value="done" />
                        <label for="hours"><?php _e('Hours between re-generation of cache files for homepage and search results (0 for no cache)', 'simplecache'); ?></label>
                        <br/>
                        <input type="text" name="hours" id="hours" value="<?php echo osc_get_preference('hours', 'simplecache'); ?>"/>
                        <br/>
                        <span style="float:right;"><button type="submit" style="float: right;"><?php _e('Update', 'simplecache');?></button></span>
                    </div>
                    <br/>
                    <div style="clear:both;"></div>
                </form>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Clear cache', 'simplecache'); ?></legend>
                <form name="simplecache_form" id="simplecache_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                    <div style="float: left; width: 100%;">
                        <input type="hidden" name="page" value="plugins" />
                        <input type="hidden" name="action" value="renderplugin" />
                        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
                        <input type="hidden" name="plugin_action" value="clear" />
                        <p>
                            <?php _e('Select the elements you want to clear from cache', 'simplecache'); ?>
                        </p>
                        <p>
                            <table>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <span><?php _e("ALL Cache"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <input type="checkbox" name="allcache" value="1" >
                                        <span><?php _e("Clear all the cache"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <span><?php _e("Categories"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <ul id="cat_tree">
                                            <?php CategoryForm::categories_tree(Category::newInstance()->toTreeAll(), array()); ?>
                                        </ul>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <span><?php _e("Locations"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <ul id="loc_tree">
                                            <?php $countries = Country::newInstance()->listAll();
                                            foreach($countries as $co) { ?>
                                            <li>
                                                <input type="checkbox" name="countries[]" value="<?php echo $co['pk_c_code'];?>" onclick="javascript:checkLoc('<?php echo $co['pk_c_code'];?>', this.checked);"/>
                                                <span><?php echo $co['s_name'];?></span>
                                                <ul id="re_<?php echo $co['pk_c_code']; ?>">
                                                    <?php $regions = Region::newInstance()->getByCountry($co['pk_c_code']);
                                                    foreach($regions as $re) { ?>
                                                    <li>
                                                        <input type="checkbox" name="regions[]" value="<?php echo $re['pk_i_id'];?>" />
                                                        <span><?php echo $re['s_name'];?></span>
                                                    </li>
                                                    <?php }; ?>
                                                </ul>
                                            </li>
                                            <?php }; ?>
                                        </ul>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <span><?php _e("Items"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <input type="checkbox" name="items" value="1" >
                                        <span><?php _e("Clear cache of items"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <span><?php _e("Feeds"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <input type="checkbox" name="feeds" value="1" >
                                        <span><?php _e("Clear cache of feeds"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <span><?php _e("Default search"); ?></span>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td>
                                        <input type="checkbox" name="search" value="1" >
                                        <span><?php _e("Clear cache of default search"); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </p>
                        <br/>
                        <span style="float:right;"><button type="submit" style="float: right;"><?php _e('Clear', 'simplecache');?></button></span>
                    </div>
                    <br/>
                    <div style="clear:both;"></div>
                </form>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#cat_tree").treeview({
            animated: "fast",
            collapsed: true
        });
        $("#loc_tree").treeview({
            animated: "fast",
            collapsed: true
        });
    });
    
    function checkCat(id, check) {
        var lay = document.getElementById("cat" + id);
        if(lay) {
        inp = lay.getElementsByTagName("input");
        for (var i = 0, maxI = inp.length ; i < maxI; ++i) {
            if(inp[i].type == "checkbox") {
                inp[i].checked = check;
            }
        }}
    }    

    function checkLoc(id, check) {
        var lay = document.getElementById("re_" + id);
        if(lay) {
        inp = lay.getElementsByTagName("input");
        for (var i = 0, maxI = inp.length ; i < maxI; ++i) {
            if(inp[i].type == "checkbox") {
                inp[i].checked = check;
            }
        }}
    }    
</script>