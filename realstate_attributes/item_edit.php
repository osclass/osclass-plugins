<script type="text/javascript">
    $(document).ready(function(){
        $('#plugin-hook input:text, #plugin-hook select').uniform();
    });
</script>
<h2><?php _e('Realestate attributes', 'realstate_attributes'); ?></h2>
<div class="box">
    <div class="row">
        <label for="property_type"><?php _e('Type', 'realstate_attributes'); ?></label>
        <select name="property_type" id="property_type">
            <option value="FOR RENT" <?php if(@$detail['e_status'] == 'FOR RENT') { echo "selected"; } ?>><?php _e('For rent', 'realstate_attributes'); ?></option>
            <option value="FOR SALE" <?php if(@$detail['e_status'] == 'FOR SALE') { echo "selected"; } ?>><?php _e('For sale', 'realstate_attributes'); ?></option>
        </select>
    </div>
    <div class="row">
        <?php
        $locales = osc_get_locales();
        if(count($locales)==1) {
        ?>
            <p>
                <label><?php _e('Property type', 'realstate_attributes'); ?></label><br />
                <select name="p_type" id="p_type">
                <?php foreach($p_type[$locales[0]['pk_c_code']] as $k => $v) { ?>
                    <option value="<?php echo  $k; ?>" <?php if($k == @$detail['fk_i_property_type_id']) { echo 'selected'; } ?>><?php echo @$v; ?></option>
                <?php } ?>
                </select>
            </p>
        <?php } else { ?>
            <div class="tabber">
            <?php foreach($locales as $locale) {?>
                <div class="tabbertab">
                    <h2><?php echo $locale['s_name']; ?></h2>
                    <p>
                        <label><?php _e('Property type', 'realstate_attributes'); ?></label><br />
                        <select name="p_type" id="p_type">
                        <?php foreach($p_type[$locale['pk_c_code']] as $k => $v) { ?>
                            <option value="<?php echo $k; ?>" <?php if($k==@$detail['fk_i_property_type_id']) { echo 'selected';};?>><?php echo  @$v;?></option>
                        <?php } ?>
                        </select>
                    </p>
                </div>
            <?php } ?>
            </div>
        <?php } ?>
    </div>
    <div class="row">
        <label for="numRooms"><?php _e('Num. of rooms', 'realstate_attributes'); ?></label>
        <select name="numRooms" id="numRooms">
        <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n == @$detail['i_num_rooms']) { echo "selected"; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row">
        <label for="numBathrooms"><?php _e('Num. of bathrooms', 'realstate_attributes'); ?></label>
        <select name="numBathrooms" id="numBathrooms">
        <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n==@$detail['i_num_bathrooms']) { echo "selected"; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row">
        <label for="status"><?php _e('Status', 'realstate_attributes'); ?></label>
        <select name="status" id="status">
            <option value="NEW CONSTRUCTION" <?php if(@$detail['e_status'] == 'NEW CONSTRUCTION') { echo "selected"; } ?>><?php _e('New construction', 'realstate_attributes'); ?></option>
            <option value="TO RENOVATE" <?php if(@$detail['e_status'] == 'TO RENOVATE') { echo "selected"; } ?>><?php _e('To renovate', 'realstate_attributes'); ?></option>
            <option value="GOOD CONDITION" <?php if(@$detail['e_status'] == 'GOOD CONDITION') { echo "selected"; } ?>><?php _e('Good condition', 'realstate_attributes'); ?></option>
        </select>
    </div>
    <div class="row">
        <label for="squareMeters"><?php _e('Square meters', 'realstate_attributes'); ?></label>
        <input type="text" name="squareMeters" id="squareMeters" value="<?php echo @$detail['s_square_meters']; ?>" size="4" maxlength="4" />
    </div>
    <div class="row">
        <label for="year"><?php _e('Construction Year', 'realstate_attributes'); ?></label>
        <input type="text" name="year" id="year" value="<?php echo @$detail['i_year'];?>" size="4" maxlength="4" />
    </div>
    <div class="row">
        <label for="squareMetersTotal"><?php _e('Square meters (total)', 'realstate_attributes'); ?></label>
        <input type="text" name="squareMetersTotal" id="squareMetersTotal" value="<?php echo  @$detail['i_plot_area'];?>" size="4" maxlength="6" />
    </div>
    <div class="row">
        <label for="numFloors"><?php _e('Num. of floors', 'realstate_attributes'); ?></label>
        <select name="numFloors" id="numFloors">
        <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n == @$detail['i_num_floors']) { echo "selected"; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row">
        <label for="numGarages"><?php _e('Num. of garages (place for a car = one garage)', 'realstate_attributes'); ?></label>
        <select name="numGarages" id="numGarages">
            <?php foreach(range(0, 15) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if($n==@$detail['i_num_garages']) { echo "selected"; } ?>><?php echo $n; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="row">
        <label for="condition"><?php _e('Condition', 'realstate_attributes'); ?></label>
        <input type="text" name="condition" id="condition" value="<?php echo @$detail['s_condition']; ?>" />
    </div>
    <div class="row">
        <label for="agency"><?php _e('Agency', 'realstate_attributes'); ?></label>
        <input type="text" name="agency" id="agency" value="<?php echo @$detail['s_agency']; ?>" />
    </div>
    <div class="row">
        <label for="floorNumber"><?php _e('Floor Number', 'realstate_attributes'); ?></label>
        <input type="text" name="floorNumber" id="floorNumber" value="<?php echo @$detail['i_floor_number']; ?>" />
    </div>
    <div class="row">
        <label><?php _e('Other characteristics', 'realstate_attributes'); ?></label>
        <div style="clear:both;"></div>
        <ul style="list-style: none outside none;">
            <li> 
                <input style="width: 20px;" type="checkbox" name="heating" id="heating" value="1" <?php if(@$detail['b_heating'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="heating"><?php _e('Heating', 'realstate_attributes'); ?></label>
            </li> 
            <li>
                <input style="width: 20px;" type="checkbox" name="airCondition" id="airCondition" value="1" <?php if(@$detail['b_air_condition'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="airCondition"><?php _e('Air condition', 'realstate_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="checkbox" name="elevator" id="elevator" value="1" <?php if(@$detail['b_elevator'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="elevator"><?php _e('Elevator', 'realstate_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="checkbox" name="terrace" id="terrace" value="1" <?php if(@$detail['b_terrace'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="terrace"><?php _e('Terrace', 'realstate_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="checkbox" name="parking" id="parking" value="1" <?php if(@$detail['b_parking'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="parking"><?php _e('Parking', 'realstate_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="checkbox" name="furnished" id="furnished" value="1" <?php if(@$detail['b_furnished'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="furnished"><?php _e('Furnished', 'realstate_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="checkbox" name="new" id="new" value="1" <?php if(@$detail['b_new'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="new"><?php _e('New', 'realstate_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="checkbox" name="by_owner" id="by_owner" value="1" <?php if(@$detail['b_by_owner'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="by_owner"><?php _e('By owner', 'realstate_attributes'); ?></label>
            </li>
        </ul>
    </div>
    <?php $locales = osc_get_locales();
    if(count($locales)==1) { ?>

    <div class="row">
        <label for="transport"><?php _e('Transport', 'realstate_attributes'); ?></label><br />
        <input type="text" name="<?php echo $locales[0]['pk_c_code']; ?>#transport" id="transport" style="width: 100%;" value="<?php echo @$detail['locale'][$locales[0]['pk_c_code']]['s_transport']; ?>" />
    </div>
    <div class="row">    
        <label for="zone"><?php _e('Zone', 'realstate_attributes'); ?></label><br />
        <input type="text" name="<?php echo $locales[0]['pk_c_code']; ?>#zone" id="zone" style="width: 100%;" value="<?php echo @$detail['locale'][$locales[0]['pk_c_code']]['s_zone']; ?>"/>
    </div>

    <?php } else { ?>

    <div class="tabber">
    <?php foreach($locales as $locale) {?>
        <div class="tabbertab">
            <h2><?php echo $locale['s_name']; ?></h2>
            <p>
                <label for="transport"><?php _e('Transport', 'realstate_attributes'); ?></label><br />
                <input type="text" name="<?php echo $locale['pk_c_code']; ?>#transport" id="transport" style="width: 100%;" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_transport']; ?>" />
            </p>
            <p>
                <label for="zone"><?php _e('Zone', 'realstate_attributes'); ?></label><br />
                <input type="text" name="<?php echo $locale['pk_c_code']; ?>#zone" id="zone" style="width: 100%;" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_zone']; ?>"/>
            </p>
        </div>
    <?php } ?>
    </div>

    <?php } ?>
</div>
<script type="text/javascript">
        tabberAutomatic();
</script>