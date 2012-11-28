<?php
if(Params::getParam('subaction')=='update-settings') {
    osc_set_preference('country', Params::getParam('countryName'), 'jobboard');
    osc_set_preference('countryId', Params::getParam('countryId'), 'jobboard');
    osc_set_preference('region', Params::getParam('region'), 'jobboard');
    osc_set_preference('regionId', Params::getParam('regionId'), 'jobboard');
    osc_set_preference('city', Params::getParam('city'), 'jobboard');
    osc_set_preference('cityId', Params::getParam('cityId'), 'jobboard');
    osc_reset_preferences();
}

$data = array();
$data['s_country'] = osc_get_preference('country', 'jobboard');
$data['fk_c_country_code'] = osc_get_preference('countryId', 'jobboard');
$data['s_region'] = osc_get_preference('region', 'jobboard');
$data['fk_i_region_id'] = osc_get_preference('regionId', 'jobboard');
$data['s_city'] = osc_get_preference('city', 'jobboard');
$data['fk_i_city_id'] = osc_get_preference('cityId', 'jobboard');

?>
<div id="general-settings">
    <form name="jobboardform" method="post">
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>settings.php" />
        <input type="hidden" name="subaction" value="update-settings">
        <fieldset>
            <div class="form-horizontal">
                <h2 class="render-title"><?php _e('Default locations', 'jobboard') ; ?></h2>
                <div class="form-row">
                    <div class="form-label"><?php _e('Default country', 'jobboard') ; ?></div>
                    <div class="form-controls">
                        <?php ItemForm::country_text($data) ; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Default region', 'jobboard') ; ?></div>
                    <div class="form-controls">
                        <?php ItemForm::region_text($data) ; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-label"><?php _e('Default city', 'jobboard') ; ?></div>
                    <div class="form-controls">
                        <?php ItemForm::city_text($data) ; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="<?php echo osc_esc_html( __('Save changes', 'jobboard') ) ; ?>" class="btn btn-submit" />
                </div>
            </div>
        </fieldset>
    </form>
</div>