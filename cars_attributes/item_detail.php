<?php
    $locale = osc_current_user_locale();
?>
<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Cars attributes') ; ?></h3>
<table style="width: 100%;margin-left: 20px;">
    <?php if(@$detail['s_make'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Make'); ?></label></td>
        <td><label><?php echo  @$detail['s_make']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['s_model'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Model'); ?></label></td>
        <td><label><?php echo  @$detail['s_model']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['locale'][$locale]['s_car_type'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Car type'); ?></label></td>
        <td><?php echo @$detail['locale'][$locale]['s_car_type']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_year'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Year'); ?></label></td>
        <td><label><?php echo  @$detail['i_year']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_doors'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Doors'); ?></label></td>
        <td><label><?php echo  @$detail['i_doors']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_seats'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Seats'); ?></label></td>
        <td><label><?php echo  @$detail['i_seats']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_mileage'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Mileage'); ?></label></td>
        <td><label><?php echo  @$detail['i_mileage']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_engine_size'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Engine size (cc)'); ?></label></td>
        <td><label><?php echo  @$detail['i_engine_size']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_num_airbags'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Num. Airbags'); ?></label></td>
        <td><label><?php echo  @$detail['i_num_airbags']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['e_transmission'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Transmission'); ?></label></td>
        <td><label><?php echo  @$detail['e_transmission']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['e_fuel'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Fuel'); ?></label></td>
        <td><label><?php echo  @$detail['e_fuel']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['e_seller'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Seller'); ?></label></td>
        <td><label><?php echo  @$detail['e_seller']; ?></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_power'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Power'); ?></label></td>
        <td><label><?php echo  @$detail['i_power']; ?></label> <label><?php echo  @$detail['e_power_unit']; ?></label></td>
    </tr>
    <?php } ?>
    <?php if(@$detail['i_gears'] != ""){ ?>
    <tr>
        <td width="150px"><label><?php echo __('Gears'); ?></label></td>
        <td><label><?php echo  @$detail['i_gears']; ?></label></td>
    </tr>
    <?php } ?>
</table>
<br/>
<ul style="margin-left: 20px;">
    <li style="float:left;width: 140px;">
        <?php if(@$detail['b_warranty']) {?>
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <?php } else {?>
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/cross.png"/>
        <?php  } ?>
        <label><?php echo __('Warranty'); ?></label>
    </li>
    <li style="float:left;width: 140px;">
        <?php if(@$detail['b_new']) {?>
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/tick.png"/>
        <?php } else {?>
        <img style="height: 14px; width: 14px;" src="<?php echo osc_plugin_url(__FILE__);?>img/cross.png"/>
        <?php  } ?>
        <label><?php echo __('New'); ?></label>
    </li>
</ul>
<div style="clear: both;"></div>
<br />