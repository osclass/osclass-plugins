<h2><?php _e('Car details', 'cars_attributes') ; ?></h2>
<table style="margin-left: 20px;">
    <?php if( !empty($detail['s_make']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Make', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['s_make']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['s_model']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Model', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['s_model']; ?></td>
    </tr>
    <?php } ?>
    <?php $locale = osc_current_user_locale(); ?>
    <?php if( !empty($detail['locale'][$locale]['s_car_type']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Car type', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['locale'][$locale]['s_car_type']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_year']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Year', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo $detail['i_year']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_doors']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Doors', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['i_doors']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_seats']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Seats', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['i_seats']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_mileage']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Mileage', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['i_mileage']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_engine_size']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Engine size (cc)', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['i_engine_size']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_num_airbags']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Num. Airbags', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo @$detail['i_num_airbags']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['e_transmission']) ) { ?>
    <tr>
        <?php $transmission = array('MANUAL' => __('Manual', 'cars_attributes'), 'AUTO' => __('Auto', 'cars_attributes')); ?>
        <td width="150px"><label><?php _e('Transmission', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo $transmission[$detail['e_transmission']]; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['e_fuel']) ) { ?>
    <tr>
        <?php $fuel = array('DIESEL'          => __('Diesel', 'cars_attributes')
                           ,'GASOLINE'        => __('Gasoline', 'cars_attributes')
                           ,'ELECTRIC-HIBRID' => __('Electric-hibrid', 'cars_attributes')
                           ,'OTHER'           => __('Other', 'cars_attributes'));
        ?>
        <td width="150px"><label><?php _e('Fuel', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo $fuel[$detail['e_fuel']]; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['e_seller']) ) { ?>
    <tr>
        <?php $seller = array('DEALER' => __('Dealer', 'cars_attributes'), 'OWNER' => __('Owner', 'cars_attributes')); ?>
        <td width="150px"><label><?php _e('Seller', 'cars_attributes'); ?></label></td>
        <td width="150px"><label><?php echo $seller[$detail['e_seller']]; ?></td>
    </tr>
    <?php } ?>
    <tr>
        <td width="150px"><label><?php _e('Warranty', 'cars_attributes'); ?>: </label></td>
        <td width="150px"><?php echo @$detail['b_warranty'] ? '<strong>' . __('Yes', 'cars_attributes') . '</strong>' : __('No', 'cars_attributes'); ?></td>
    </tr>
    <tr>
        <td><label><?php _e('New', 'cars_attributes'); ?>: </label></td>
        <td width="150px"><?php echo @$detail['b_new'] ? '<strong>' . __('Yes', 'cars_attributes') . '</strong>' : __('No', 'cars_attributes'); ?></td>
    </tr>
    <?php if( !empty($detail['i_power']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Power', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['i_power']; ?> <?php echo @$detail['e_power_unit']; ?></td>
    </tr>
    <?php } ?>
    <?php if( !empty($detail['i_gears']) ) { ?>
    <tr>
        <td width="150px"><label><?php _e('Gears', 'cars_attributes'); ?></label></td>
        <td width="150px"><?php echo @$detail['i_gears']; ?></td>
    </tr>
    <?php } ?>
</table>