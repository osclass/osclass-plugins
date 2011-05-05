
<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Cars attributes') ; ?></h3>
<table style="margin-left: 20px;">
<tr>
    <td width="150px"><label><?php echo __('Make'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['s_make']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Model'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['s_model']; ?></td>
</tr>
<tr>
    <?php $locale = osc_current_user_locale();?>
    <td width="150px"><label><?php echo __('Car type'); ?></label></td>
    <td width="150px"><?php echo @$detail['locale'][$locale]['s_car_type']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Year'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_year']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Doors'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_doors']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Seats'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_seats']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Mileage'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_mileage']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Engine size (cc)'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_engine_size']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Num. Airbags'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_num_airbags']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Transmission'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['e_transmission']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Fuel'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['e_fuel']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Seller'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['e_seller']; ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Warranty'); ?>: </label></td>
    <td width="150px"><?php echo @$detail['b_warranty'] ? '<strong>'.__('Yes').'</strong>' : __('No'); ?></td>
</tr>
    <td><label><?php echo __('New'); ?>: </label></td>
    <td width="150px"><?php echo @$detail['b_new'] ? '<strong>'.__('Yes').'</strong>' : __('No'); ?></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Power'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_power']; ?></label> <label><?php echo  @$detail['e_power_unit']; ?></label></td>
</tr>
<tr>
    <td width="150px"><label><?php echo __('Gears'); ?></label></td>
    <td width="150px"><label><?php echo  @$detail['i_gears']; ?></label></td>
</tr>
</table>

