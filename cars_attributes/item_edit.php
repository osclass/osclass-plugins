<script type="text/javascript">
    $(document).ready(function(){

        $("#make").change(function(){
            var make_id = $(this).val();
            var url = '<?php echo osc_ajax_plugin_url("cars_attributes/ajax.php")."&makeId="; ?>' + make_id;
            var result = '';
            var model_id = '';
            if(make_id != '') {
                $("#model").attr('disabled',false);
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    success: function(data){
                        var length = data.length;
                        if(length > 0) {
                            result += '<option value=""><?php _e('Select a model', 'cars_attributes'); ?>...</option>';
                            for(key in data) {
                                if(data[key].pk_i_id==model_id) {
                                    result += '<option value="' + data[key].pk_i_id + '" selected>' + data[key].s_name + '</option>';
                                } else {
                                    result += '<option value="' + data[key].pk_i_id + '">' + data[key].s_name + '</option>';
                                }
                            }
                        } else {
                            result += '<option value=""><?php _e('No results', 'cars_attributes'); ?></option>';
                        }
                        $("#model").html(result);
                    }
                 });
             } else {
                $("#model").attr('disabled',true);
             }
        });
        
        // uniform()
        $('#plugin-hook input:text, select#make, select#model, select#car_type, select#doors, select#seats, select#num_airbags, select#transmission, select#fuel, select#seller, select#power_unit, select#gears').uniform();
        
        
    });



</script>
<h3><?php _e('Cars attributes', 'cars_attributes') ; ?></h3>
<div class="box">
    <div class="row _200">
        <label><?php _e('Make', 'cars_attributes'); ?></label>
        <select name="make" id="make" >
            <option value=""><?php _e('Select a make', 'cars_attributes'); ?></option>
            <?php foreach($makes as $a){ ?>
            <option value="<?php echo $a['pk_i_id']; ?>" <?php if(@$detail['fk_i_make_id'] == $a['pk_i_id']) { echo 'selected';}; ?>><?php echo $a['s_name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="row _200">
        <label><?php _e('Model', 'cars_attributes'); ?></label>
        <select name="model" id="model">
            <?php foreach($models as $a): ?>
            <option value="<?php echo $a['pk_i_id']; ?>"><?php echo $a['s_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="row _200">
        <?php $locales = osc_get_locales();
        if(count($locales)==1) {
            $locale = $locales[0];?>
            <p>
            <label><?php _e('Car type', 'cars_attributes'); ?></label><br />
            <select name="car_type" id="car_type">
            <?php foreach($car_types[$locale['pk_c_code']] as $k => $v) { ?>
            <option value="<?php echo  $k; ?>" <?php if(@$detail['fk_vehicle_type_id'] == $k) { echo 'selected'; } ?>><?php echo @$v; ?></option>
            <?php } ?>
            </select>
            </p>
        <?php } else { ?>
            <div class="tabber">
            <?php foreach($locales as $locale) {?>
                <div class="tabbertab">
                    <h2><?php echo $locale['s_name']; ?></h2>
                    <p>
                        <label><?php _e('Car type', 'cars_attributes'); ?></label><br />
                        <select name="car_type" id="car_type">
                        <?php foreach($car_type[$locale['pk_c_code']] as $k => $v) { ?>
                        <option value="<?php echo  $k; ?>" <?php if(@$detail['fk_vehicle_type_id'] == $k) { echo 'selected'; } ?>><?php echo @$v; ?></option>
                        <?php } ?>
                        </select>
                    </p>
                </div>
            <?php } ?>
            </div>
        <?php }; ?>
    </div>
    <div class="row _200">
        <label><?php _e('Year', 'cars_attributes'); ?></label>
        <input type="text" name="year" id="year" value="<?php echo @$detail['i_year']; ?>" size=4/>
    </div>
    <div class="row auto">
        <label><?php _e('Doors', 'cars_attributes'); ?></label>
        <select name="doors" id="doors">
        <?php foreach(range(3, 5) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if(@$detail['i_doors'] == $n) { echo 'selected'; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="row auto">
        <label><?php _e('Seats', 'cars_attributes'); ?></label>
        <select name="seats" id="seats">
            <?php foreach(range(1, 17) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if(@$detail['i_seats'] == $n) { echo 'selected'; } ?>><?php echo $n; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="row _200">
        <label><?php _e('Mileage', 'cars_attributes'); ?></label>
        <input type="text" name="mileage" id="mileage" value="<?php echo @$detail['i_mileage']; ?>" />
    </div>
    <div class="row _200">
        <label><?php _e('Engine size (cc)', 'cars_attributes'); ?></label>
        <input type="text" name="engine_size" id="engine_size" value="<?php echo  @$detail['i_engine_size']; ?>" />
    </div>
    <div class="row auto">
        <label><?php _e('Num. Airbags', 'cars_attributes'); ?></label>
        <select name="num_airbags" id="num_airbags">
            <?php foreach(range(0, 8) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if(@$detail['i_num_airbags'] == $n) { echo 'selected'; } ?>><?php echo $n; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="row _200">
        <label><?php _e('Transmission', 'cars_attributes'); ?></label>
        <select name="transmission" id="transmission">
            <option value="MANUAL" <?php if(@$detail['e_transmission'] == 'MANUAL') { echo 'selected'; } ?>><?php _e('Manual', 'cars_attributes'); ?></option>
            <option value="AUTO" <?php if(@$detail['e_transmission'] == 'AUTO') { echo 'selected'; } ?>><?php _e('Auto', 'cars_attributes'); ?></option>
        </select>
    </div>
    <div class="row _200">
        <label><?php _e('Fuel', 'cars_attributes'); ?></label>
        <select name="fuel" id="fuel">
            <option value="DIESEL" <?php if(@$detail['e_fuel'] == 'DIESEL') { echo 'selected'; } ?>><?php _e('Diesel', 'cars_attributes'); ?></option>
            <option value="GASOLINE" <?php if(@$detail['e_fuel'] == 'GASOLINE') { echo 'selected'; } ?>><?php _e('Gasoline', 'cars_attributes'); ?></option>
            <option value="ELECTRIC-HIBRID" <?php if(@$detail['e_fuel'] == 'ELECTRIC-HIBRID') { echo 'selected'; } ?>><?php _e('Electric-hibrid', 'cars_attributes'); ?></option>
            <option value="OTHER" <?php if(@$detail['e_fuel'] == 'OTHER') { echo 'selected'; } ?>><?php _e('Other', 'cars_attributes'); ?></option>
        </select>
    </div>
    <div class="row _200">
        <label><?php _e('Seller', 'cars_attributes'); ?></label>
        <select name="seller" id="seller">
            <option value="DEALER" <?php if(@$detail['e_seller'] == 'DEALER') { echo 'selected'; } ?>><?php _e('Dealer', 'cars_attributes'); ?></option>
            <option value="OWNER" <?php if(@$detail['e_seller'] == 'OWNER') { echo 'selected'; } ?>><?php _e('Owner', 'cars_attributes'); ?></option>
        </select>
    </div>
    <div class="row _20">
        <input type="checkbox" name="warranty" id="warranty" value="1" <?php if(@$detail['b_warranty'] == 1) { echo 'checked="yes"'; } ?> /> <label><?php _e('Warranty', 'cars_attributes'); ?></label> <br />
    </div>
    <div class="row _20">
        <input type="checkbox" name="new" id="new" value="1" <?php if(@$detail['b_new'] == 1) { echo 'checked="yes"'; } ?> /> <label><?php _e('New', 'cars_attributes'); ?></label> <br />
    </div>
    <div class="row auto _200">
        <label><?php _e('Power', 'cars_attributes'); ?></label>
        <input type="text" name="power" id="power" value="<?php echo @$detail['i_power']; ?>" />
        <select name="power_unit" id="power_unit">
            <option value="KW" <?php if(@$detail['e_power_unit'] == 'KW') { echo 'selected'; } ?>><?php _e('Kw', 'cars_attributes'); ?></option>
            <option value="CV" <?php if(@$detail['e_power_unit'] == 'CV') { echo 'selected'; } ?>><?php _e('Cv', 'cars_attributes'); ?></option>
            <option value="CH" <?php if(@$detail['e_power_unit'] == 'CH') { echo 'selected'; } ?>><?php _e('Ch', 'cars_attributes'); ?></option>
            <option value="KM" <?php if(@$detail['e_power_unit'] == 'KM') { echo 'selected'; } ?>><?php _e('Km', 'cars_attributes'); ?></option>
            <option value="HP" <?php if(@$detail['e_power_unit'] == 'HP') { echo 'selected'; } ?>><?php _e('Hp', 'cars_attributes'); ?></option>
            <option value="PS" <?php if(@$detail['e_power_unit'] == 'PS') { echo 'selected'; } ?>><?php _e('Ps', 'cars_attributes'); ?></option>
            <option value="PK" <?php if(@$detail['e_power_unit'] == 'PK') { echo 'selected'; } ?>><?php _e('Pk', 'cars_attributes'); ?></option>
            <option value="CP" <?php if(@$detail['e_power_unit'] == 'CP') { echo 'selected'; } ?>><?php _e('Cp', 'cars_attributes'); ?></option>
        </select>
    </div>
    <div class="row auto">
        <label><?php _e('Gears', 'cars_attributes'); ?></label>
        <select name="gears" id="gears">
        <?php foreach(range(1, 8) as $n) { ?>
            <option value="<?php echo $n; ?>" <?php if(@$detail['i_gears'] == $n) { echo 'selected'; } ?>><?php echo $n; ?></option>
        <?php } ?>
        </select>
    </div>
</div>
<script type="text/javascript">
    tabberAutomatic();
</script>