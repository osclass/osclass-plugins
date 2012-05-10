<h2><?php _e('Realestate attributes', 'realestate_attributes'); ?></h2>
<div class="box">
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_property_type') != '' ) {
                $detail['e_type'] = Session::newInstance()->_getForm('pre_property_type');
            }
        ?>
        <label for="property_type"><?php _e('Type', 'realestate_attributes'); ?></label>
        <select name="property_type" id="property_type">
            <option value="FOR RENT" <?php if(@$detail['e_type'] == 'FOR RENT') { echo "selected"; } ?>><?php _e('For rent', 'realestate_attributes'); ?></option>
            <option value="FOR SALE" <?php if(@$detail['e_type'] == 'FOR SALE') { echo "selected"; } ?>><?php _e('For sale', 'realestate_attributes'); ?></option>
        </select>
    </div>
    <div class="row">
        <?php
        $locales = osc_get_locales();
        if(count($locales)==1) {
        ?>
            <?php
                if( Session::newInstance()->_getForm('pre_p_type') != '' ) {
                    $detail['fk_i_property_type_id'] = Session::newInstance()->_getForm('pre_p_type');
                }
            ?>
            <label><?php _e('Property type', 'realestate_attributes'); ?></label>
            <select name="p_type" id="p_type">
            <?php foreach($p_type[$locales[0]['pk_c_code']] as $k => $v) { ?>
                <option value="<?php echo  $k; ?>" <?php if($k == @$detail['fk_i_property_type_id']) { echo 'selected'; } ?>><?php echo @$v; ?></option>
            <?php } ?>
            </select>
        <?php } else { ?>
            <div class="tabber">
            <?php
                if( Session::newInstance()->_getForm('pre_p_type') != '' ) {
                    $detail['fk_i_property_type_id'] = Session::newInstance()->_getForm('pre_p_type');
                }
            ?>
            <?php foreach($locales as $locale) {?>
                <div class="tabbertab">
                    <h2><?php echo $locale['s_name']; ?></h2>
                    <p>
                        <label><?php _e('Property type', 'realestate_attributes'); ?></label><br />
                        <select name="p_type" id="p_type">
                        <?php
                        if($p_type[$locale['pk_c_code']]){
                            foreach($p_type[$locale['pk_c_code']] as $k => $v) { ?>
                                <option value="<?php echo $k; ?>" <?php if($k==@$detail['fk_i_property_type_id']) { echo 'selected';};?>><?php echo  @$v;?></option>
                        <?php
                            }
                        }?>
                        </select>
                    </p>
                </div>
            <?php } ?>
            </div>
        <?php }
        ?>
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_numRooms') != '' ) {
                $detail['i_num_rooms'] = Session::newInstance()->_getForm('pre_numRooms');
            }
        ?>
        <label for="numRooms"><?php _e('Num. of rooms', 'realestate_attributes'); ?></label>
        <select name="numRooms" id="numRooms">
        <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n == @$detail['i_num_rooms']) { echo "selected"; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_numBathrooms') != '' ) {
                $detail['i_num_bathrooms'] = Session::newInstance()->_getForm('pre_numBathrooms');
            }
        ?>
        <label for="numBathrooms"><?php _e('Num. of bathrooms', 'realestate_attributes'); ?></label>
        <select name="numBathrooms" id="numBathrooms">
        <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n==@$detail['i_num_bathrooms']) { echo "selected"; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_status') != '' ) {
                $detail['e_status'] = Session::newInstance()->_getForm('pre_status');
            }
        ?>
        <label for="status"><?php _e('Status', 'realestate_attributes'); ?></label>
        <select name="status" id="status">
            <option value="NEW CONSTRUCTION" <?php if(@$detail['e_status'] == 'NEW CONSTRUCTION') { echo "selected"; } ?>><?php _e('New construction', 'realestate_attributes'); ?></option>
            <option value="TO RENOVATE" <?php if(@$detail['e_status'] == 'TO RENOVATE') { echo "selected"; } ?>><?php _e('To renovate', 'realestate_attributes'); ?></option>
            <option value="GOOD CONDITION" <?php if(@$detail['e_status'] == 'GOOD CONDITION') { echo "selected"; } ?>><?php _e('Good condition', 'realestate_attributes'); ?></option>
        </select>
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_squareMeters') != '' ) {
                $detail['s_square_meters'] = Session::newInstance()->_getForm('pre_squareMeters');
            }
        ?>
        <label for="squareMeters"><?php _e('Square meters', 'realestate_attributes'); ?></label>
        <input type="text" name="squareMeters" id="squareMeters" value="<?php echo @$detail['s_square_meters']; ?>" size="4" maxlength="4" />
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_year') != '' ) {
                $detail['i_year'] = Session::newInstance()->_getForm('pre_year');
            }
        ?>
        <label for="year"><?php _e('Construction Year', 'realestate_attributes'); ?></label>
        <input type="text" name="year" id="year" value="<?php echo @$detail['i_year'];?>" size="4" maxlength="4" />
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_squareMetersTotal') != '' ) {
                $detail['i_plot_area'] = Session::newInstance()->_getForm('pre_squareMetersTotal');
            }
        ?>
        <label for="squareMetersTotal"><?php _e('Square meters (total)', 'realestate_attributes'); ?></label>
        <input type="text" name="squareMetersTotal" id="squareMetersTotal" value="<?php echo  @$detail['i_plot_area'];?>" size="4" maxlength="6" />
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_numFloors') != '' ) {
                $detail['i_num_floors'] = Session::newInstance()->_getForm('pre_numFloors');
            }
        ?>
        <label for="numFloors"><?php _e('Num. of floors', 'realestate_attributes'); ?></label>
        <select name="numFloors" id="numFloors">
        <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n == @$detail['i_num_floors']) { echo "selected"; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_numGarages') != '' ) {
                $detail['i_num_garages'] = Session::newInstance()->_getForm('pre_numGarages');
            }
        ?>
        <label for="numGarages"><?php _e('Num. of garages (place for a car = one garage)', 'realestate_attributes'); ?></label>
        <select name="numGarages" id="numGarages">
            <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n==@$detail['i_num_garages']) { echo "selected"; } ?>><?php echo $n; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_condition') != '' ) {
                $detail['s_condition'] = Session::newInstance()->_getForm('pre_condition');
            }
        ?>
        <label for="condition"><?php _e('Condition', 'realestate_attributes'); ?></label>
        <input type="text" name="condition" id="condition" value="<?php echo @$detail['s_condition']; ?>" />
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_agency') != '' ) {
                $detail['s_agency'] = Session::newInstance()->_getForm('pre_agency');
            }
        ?>
        <label for="agency"><?php _e('Agency', 'realestate_attributes'); ?></label>
        <input type="text" name="agency" id="agency" value="<?php echo @$detail['s_agency']; ?>" />
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_floorNumber') != '' ) {
                $detail['i_floor_number'] = Session::newInstance()->_getForm('pre_floorNumber');
            }
        ?>
        <label for="floorNumber"><?php _e('Floor Number', 'realestate_attributes'); ?></label>
        <input type="text" name="floorNumber" id="floorNumber" value="<?php echo @$detail['i_floor_number']; ?>" />
    </div>
    <div class="row">
        <label><?php _e('Other characteristics', 'realestate_attributes'); ?></label>
        <ul class="checkbox-list">
            <?php
                if( Session::newInstance()->_getForm('pre_heating') != '' ) {
                    $detail['b_heating'] = Session::newInstance()->_getForm('pre_heating');
                }
            ?>
            <li> 
                <input style="width: 20px;" type="checkbox" name="heating" id="heating" value="1" <?php if(@$detail['b_heating'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="heating"><?php _e('Heating', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_airCondition') != '' ) {
                    $detail['b_air_condition'] = Session::newInstance()->_getForm('pre_airCondition');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="airCondition" id="airCondition" value="1" <?php if(@$detail['b_air_condition'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="airCondition"><?php _e('Air condition', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_elevator') != '' ) {
                    $detail['b_elevator'] = Session::newInstance()->_getForm('pre_elevator');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="elevator" id="elevator" value="1" <?php if(@$detail['b_elevator'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="elevator"><?php _e('Elevator', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_terrace') != '' ) {
                    $detail['b_terrace'] = Session::newInstance()->_getForm('pre_terrace');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="terrace" id="terrace" value="1" <?php if(@$detail['b_terrace'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="terrace"><?php _e('Terrace', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_parking') != '' ) {
                    $detail['b_parking'] = Session::newInstance()->_getForm('pre_parking');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="parking" id="parking" value="1" <?php if(@$detail['b_parking'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="parking"><?php _e('Parking', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_furnished') != '' ) {
                    $detail['b_furnished'] = Session::newInstance()->_getForm('pre_furnished');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="furnished" id="furnished" value="1" <?php if(@$detail['b_furnished'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="furnished"><?php _e('Furnished', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_new') != '' ) {
                    $detail['b_new'] = Session::newInstance()->_getForm('pre_new');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="new" id="new" value="1" <?php if(@$detail['b_new'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="new"><?php _e('New', 'realestate_attributes'); ?></label>
            </li>
            <?php
                if( Session::newInstance()->_getForm('pre_by_owner') != '' ) {
                    $detail['b_by_owner'] = Session::newInstance()->_getForm('pre_by_owner');
                }
            ?>
            <li>
                <input style="width: 20px;" type="checkbox" name="by_owner" id="by_owner" value="1" <?php if(@$detail['b_by_owner'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="by_owner"><?php _e('By owner', 'realestate_attributes'); ?></label>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    <?php $locales = osc_get_locales();
    if(count($locales)==1) { ?>

    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_'.$locales[0]['pk_c_code'].'transport') != '' ) {
                $detail['locale'][$locales[0]['pk_c_code']]['s_transport'] = Session::newInstance()->_getForm('pre_'.$locales[0]['pk_c_code'].'transport');
            }
        ?>
        <label for="transport"><?php _e('Transport', 'realestate_attributes'); ?></label>
        <input type="text" name="<?php echo $locales[0]['pk_c_code']; ?>#transport" id="transport" value="<?php echo @$detail['locale'][$locales[0]['pk_c_code']]['s_transport']; ?>" />
    </div>
    <div class="row">
        <?php
            if( Session::newInstance()->_getForm('pre_'.$locales[0]['pk_c_code'].'zone') != '' ) {
                $detail['locale'][$locales[0]['pk_c_code']]['s_zone'] = Session::newInstance()->_getForm('pre_'.$locales[0]['pk_c_code'].'zone');
            }
        ?>
        <label for="zone"><?php _e('Zone', 'realestate_attributes'); ?></label>
        <input type="text" name="<?php echo $locales[0]['pk_c_code']; ?>#zone" id="zone" value="<?php echo @$detail['locale'][$locales[0]['pk_c_code']]['s_zone']; ?>"/>
    </div>

    <?php } else { ?>

    <div class="tabber">
    <?php foreach($locales as $locale) {?>
        <div class="tabbertab">
            <h2><?php echo $locale['s_name']; ?></h2>
            <p>
                <?php
                    if( Session::newInstance()->_getForm('pre_'.$locale['pk_c_code'].'transport') != '' ) {
                        $detail['locale'][$locale['pk_c_code']]['s_transport'] = Session::newInstance()->_getForm('pre_'.$locale['pk_c_code'].'transport');
                    }
                ?>
                <label for="transport"><?php _e('Transport', 'realestate_attributes'); ?></label><br />
                <input type="text" name="<?php echo $locale['pk_c_code']; ?>#transport" id="transport" style="width: 100%;" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_transport']; ?>" />
            </p>
            <p>
                <?php
                    if( Session::newInstance()->_getForm('pre_'.$locale['pk_c_code'].'zone') != '' ) {
                        $detail['locale'][$locale['pk_c_code']]['s_zone'] = Session::newInstance()->_getForm('pre_'.$locale['pk_c_code'].'zone');
                    }
                ?>
                <label for="zone"><?php _e('Zone', 'realestate_attributes'); ?></label><br />
                <input type="text" name="<?php echo $locale['pk_c_code']; ?>#zone" id="zone" style="width: 100%;" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_zone']; ?>"/>
            </p>
        </div>
    <?php }
    ?>
    </div>

    <?php } ?>
</div>
<script type="text/javascript">
        if(typeof tabberAutomatic == 'function') { 
            tabberAutomatic(); 
        }
        if(typeof themeUiHook == 'function') { 
            themeUiHook(); 
        }
</script>