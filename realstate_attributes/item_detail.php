<?php
    $locale = osc_current_user_locale();
?>
<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Realestate attributes', 'realstate_attributes') ; ?></h3>
<table style="width: 100%;margin-left: 20px;">
    <?php if(@$detail['e_type'] != "") {?>
    <tr>
	<td width="150px"><label><?php _e('Type', 'realstate_attributes'); ?></label></td>
	<td><?php echo @$detail['e_type']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['locale'][$locale]['s_name'] != "") {?>
    <tr>
        <td><label><?php _e('Property type', 'realstate_attributes'); ?></label></td>
        <td><?php echo  @@$detail['locale'][$locale]['s_name']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_num_rooms'] != "") {?>
    <tr>
        <td><label><?php _e('Num. Rooms', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['i_num_rooms']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_num_bathrooms'] != "") {?>
    <tr>
        <td><label><?php _e('Num. Bathrooms', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['i_num_bathrooms']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['e_status'] != "") {?>
    <tr>
        <td><label><?php _e('Status', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['e_status']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['s_square_meters'] != "") {?>
    <tr>
        <td><label><?php _e('Square Meters', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['s_square_meters']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_plot_area'] != "") {?>
    <tr>
        <td><label><?php _e('Square Meters (total)', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['i_plot_area']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_num_floors'] != "") {?>
    <tr>
        <td><label><?php _e('Num. Floors', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['i_num_floors']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_year'] != "") {?>
    <tr>
        <td><label><?php _e('Construction Year', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['i_year']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['s_condition'] != "") {?>
    <tr>
        <td><label><?php _e('Condition', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['s_condition']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['s_agency'] != "") {?>
    <tr>
        <td><label><?php _e('Agency', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['s_agency']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_floor_number'] != "") {?>
    <tr>
        <td><label><?php _e('Floor Number', 'realstate_attributes'); ?></label></td>
        <td><?php echo @$detail['i_floor_number']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@@$detail['locale'][$locale]['s_transport'] != "") {?>
    <tr>
        <td><label><?php _e('Transport', 'realstate_attributes'); ?></label></td>
        <td><?php echo @@$detail['locale'][$locale]['s_transport']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@@$detail['locale'][$locale]['s_zone'] != "") {?>
    <tr>
        <td><label><?php _e('Zone', 'realstate_attributes'); ?></label></td>
        <td><?php echo @@$detail['locale'][$locale]['s_zone']; ?></td>
    </tr>
    <?php } ?>
</table>
<br/>
<strong style="margin-left: 20px;"><?php _e('Other characteristics', 'realstate_attributes'); ?></strong>
<br/>
<ul>
    <?php if(@$detail['b_heating']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('Heating', 'realstate_attributes'); ?></label>
    </li>
    <?php } ?>
    <?php if(@$detail['b_air_condition']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('Air Condition', 'realstate_attributes'); ?></label>
    </li>
    <?php } ?>
    <?php if(@$detail['b_elevator']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('Elevator', 'realstate_attributes'); ?></label>
    </li>
    <?php } ?>
    <?php if(@$detail['b_terrace']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('Terrace', 'realstate_attributes'); ?></label>
    </li>
    <?php } ?>
    <?php if(@$detail['b_parking']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('Parking', 'realstate_attributes'); ?></label>
    </li>
    <?php } ?>
    <?php if(@$detail['b_furnished']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('Furnished', 'realstate_attributes'); ?></label>
    </li>
    <?php } ?>
    <?php if(@$detail['b_new']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('New', 'realstate_attributes'); ?>
    </li>
    <?php } ?>
    <?php if(@$detail['b_by_owner']) {?>
    <li style="float:left;width: 140px;">
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <label><?php _e('By Owner', 'realstate_attributes'); ?>
    </li>
    <?php } ?>
</ul>
<div style="clear: both;"></div>
<br/>
        

