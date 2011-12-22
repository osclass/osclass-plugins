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
?>
<!DOCTYPE html>
<html>
    <head>
        <?php osc_current_web_theme_path('head.php') ; ?>
        <meta name="robots" content="noindex, nofollow" />
        <meta name="googlebot" content="noindex, nofollow" />
        <?php MblItemForm::location_javascript(); ?>
        <?php if(osc_images_enabled_at_items()) MblItemForm::photos_javascript(); ?>
        <style>
            h3{
                margin-bottom: 9px;
                margin-top: 9px;
            }
        </style>
    </head>
    <body>
        <div data-role="page" id="item-post">
            <div data-role="header">
                <a data-rel="back" data-icon="back"  data-iconpos="notext"></a>
                <h1><strong><?php _e('Publish an item', 'osc_mobile'); ?></strong></h1>
                <?php osc_show_flash_message() ; ?>
            </div>

            <div data-role="content" data-theme="c">
                <form name="item" action="<?php echo osc_base_url(true);?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="item_add_post" />
                    <input type="hidden" name="page" value="item" />
                    
                    <h3><?php _e('General Information', 'osc_mobile'); ?></h3>
                    <fieldset data-role="fieldcontain">
                        <label for="catId"><?php _e('Category', 'osc_mobile'); ?> *</label>
                        <?php MblItemForm::category_select(null, null, __('Select a category', 'osc_mobile')); ?>
                    </fieldset>
                    
                    <?php MblItemForm::multilanguage_title_description(); ?>
                    
                    <?php if( osc_price_enabled_at_items() ) { ?>
                    <fieldset data-role="fieldcontain">
                        <label for="price"><?php _e('Price', 'osc_mobile'); ?></label>
                        <?php MblItemForm::price_input_text(); ?>
                        <label for="currency"> </label>
                        <?php MblItemForm::currency_select(); ?>
                    </fieldset>
                    <hr/>
                    <?php } ?>
                    
                    <?php if( osc_images_enabled_at_items() ) { ?>
                    <fieldset data-role="fieldcontain">
                        <div class="box photos">
                            <h3><?php _e('Photos', 'osc_mobile'); ?></h3>
                            <div id="photos">
                                <div class="row">
                                    <input type="file" name="photos[]" />
                                </div>
                            </div>
                            <a href="#" onclick="addNewPhoto(); return false;"><?php _e('Add new photo', 'osc_mobile'); ?></a>
                        </div>
                    </fieldset>
                    <hr/>
                    <?php } ?>
                    
                    <fieldset data-role="fieldcontain">
                        <h3><?php _e('Item Location', 'osc_mobile'); ?></h3>
                        <fieldset data-role="fieldcontain">
                            <label for="countryId"><?php _e('Country', 'osc_mobile'); ?></label>
                            <?php MblItemForm::country_select(osc_get_countries(), osc_user()) ; ?>
                        </fieldset>
                        <fieldset id="field_select_region" data-role="fieldcontain" style="display:none;">
                            <label for="regionId"><?php _e('Region', 'osc_mobile'); ?></label>
                            <a id="a_select_region" data-role="button" href="#page_list_regions"><?php _e("Select a region...", 'osc_mobile'); ?></a>
                            <?php MblItemForm::region_text_hidden(); ?>
                        </fieldset>
                        <fieldset id="field_select_city" data-role="fieldcontain" style="display:none;">
                            <label class="ui-select" for="city"><?php _e('City', 'osc_mobile'); ?></label>
                            <a id="a_select_city" data-role="button" href="#page_list_cities"><?php _e("Select a city...", 'osc_mobile'); ?></a>
                            <?php MblItemForm::city_text_hidden(); ?>
                        </fieldset>
                        <fieldset id="field_select_city_area" data-role="fieldcontain" style="display:none;">
                            <label for="city"><?php _e('City Area', 'osc_mobile'); ?></label>
                            <?php MblItemForm::city_area_text(osc_user()) ; ?>
                        </fieldset>
                        <fieldset id="field_select_address" data-role="fieldcontain" style="display:none;">
                            <label for="address"><?php _e('Address', 'osc_mobile'); ?></label>
                            <?php MblItemForm::address_text(osc_user()) ; ?>
                        </fieldset>
                    </fieldset>
                    <hr/>
                    
                    <!-- seller info -->
                    <?php if(!osc_is_web_user_logged_in() ) { ?>
                    <fieldset data-role="fieldcontain">
                        <h3><?php _e('Seller\'s information', 'osc_mobile'); ?></h3>
                        <fieldset data-role="fieldcontain">
                            <label for="contactName"><?php _e('Name', 'osc_mobile'); ?></label>
                            <?php MblItemForm::contact_name_text() ; ?>
                        </fieldset>
                        <fieldset data-role="fieldcontain">
                            <label for="contactEmail"><?php _e('E-mail', 'osc_mobile'); ?> *</label>
                            <?php MblItemForm::contact_email_text() ; ?>
                        </fieldset>
                        <fieldset data-role="fieldcontain">
                            <label for="showEmail" style="width: 250px;"><?php _e('Show e-mail on the item page', 'osc_mobile'); ?></label>
                            <?php MblItemForm::show_email_checkbox() ; ?>
                        </fieldset>
                    </fieldset>
                    <hr/>
                    <?php }; ?>    
                        
                    <?php if( osc_recaptcha_items_enabled() ) {?>
                    <fieldset data-role="fieldcontain">
                        <?php osc_show_recaptcha(); ?>
                    </fieldset>
                    <hr/>
                    <?php }?>    
                    <fieldset data-role="fieldcontain">
                    <button  type="submit"><?php _e('Publish', 'osc_mobile'); ?></button>
                    </fieldset>
                 </form>
            </div>
        </div>   
        
        <!-- Start of SELECT REGION page -->
        <div data-role="page" data-theme="b" id="page_list_regions">
            <div data-theme="b" data-role="header">
                <h1><?php _e("Select a region...", 'osc_mobile'); ?></h1>
            </div><!-- /header -->

            <div data-theme="b" data-role="content">
                <ul id="list_regions" data-role="listview" data-filter="true">

                </ul>
            </div><!-- /content -->
        </div><!-- /page -->
        
        <!-- Start of SELECT CITY page -->
        <div data-role="page" data-theme="b" id="page_list_cities">
            <div data-theme="b" data-role="header">
                <h1><?php _e("Select a city...", 'osc_mobile'); ?></h1>
            </div><!-- /header -->

            <div data-theme="b" data-role="content">
                <ul id="list_cities" data-role="listview" data-filter="true">

                </ul>
            </div><!-- /content -->
        </div><!-- /page -->
    </body>
</html>