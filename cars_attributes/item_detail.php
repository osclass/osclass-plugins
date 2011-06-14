
<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Cars attributes', 'cars_attributes') ; ?></h3>
<table style="margin-left: 20px;">
    <tr>
        <td width="150px"><label><?php _e('Make', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['s_make']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Model', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['s_model']; ?></td>
    </tr>
    <tr>
        <?php $locale = osc_current_user_locale();?>
        <td width="150px"><label><?php _e('Car type', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['locale'][$locale]['s_car_type']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Year', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_year']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Doors', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_doors']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Seats', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_seats']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Mileage', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_mileage']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Engine size (cc)', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_engine_size']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Num. Airbags', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_num_airbags']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Transmission', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['e_transmission']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Fuel', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['e_fuel']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Seller', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['e_seller']; ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Warranty', 'cars_attributes'); ?>: </label></td>
        <td width="150px"><?php echo @$detail['b_warranty'] ? '<strong>' . __('Yes', 'cars_attributes') . '</strong>' : __('No', 'cars_attributes'); ?></td>
    </tr>
        <td><label><?php echo __('New'); ?>: </label></td>
        <td width="150px"><?php echo @$detail['b_new'] ? '<strong>' . __('Yes', 'cars_attributes') . '</strong>' : __('No', 'cars_attributes'); ?></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Power', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_power']; ?></label> <label><?php echo @$detail['e_power_unit']; ?></label></td>
    </tr>
    <tr>
        <td width="150px"><label><?php _e('Gears', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_gears']; ?></label></td>
    </tr>
</table>

