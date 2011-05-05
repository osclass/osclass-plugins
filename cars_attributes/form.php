<script type="text/javascript">
    $(document).ready(function(){

        $("#make").change(function(){
            var make_id = $(this).val();
            var url = '<?php echo osc_ajax_plugin_url("cars_attributes/ajax.php")."&makeId="; ?>' + make_id;
            var result = '';
            if(make_id != '') {
                $("#model").attr('disabled',false);
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    success: function(data){
                        var length = data.length;
                        if(length > 0) {
                            result += '<option value=""><?php echo __("Select a model..."); ?></option>';
                            for(key in data) {
                                result += '<option value="' + data[key].pk_i_id + '">' + data[key].s_name + '</option>';
                            }
                        } else {
                            result += '<option value=""><?php echo __('No results') ?></option>';
                        }
                        $("#model").html(result);
                    }
                 });
             } else {
                $("#model").attr('disabled',true);
             }
        });

        $("#model").attr('disabled',true);
        // uniform()
        $('#plugin-hook input:text, select#make, select#model, select#car_type, select#doors, select#seats, select#num_airbags, select#transmission, select#fuel, select#seller, select#power_unit, select#gears').uniform();
        
    });

    alert(checkFuncs);
    
    checkFuncs.push(function() {
        if(document.getElementById('make').value == "") {
            alert("You have to select a make.");
            return false;
        }

        if(document.getElementById('model').value == "") {
            alert("You have to select a model.");
            return false;
        }

        return true;
    })

</script>
<h2><?php _e('Cars attributes') ; ?></h2>

<div class="box">
    <div class="row _200">
	<label><?php _e('Make'); ?></label>
        <select name="make" id="make" >
            <option value=""><?php  _e('Select a make'); ?></option>
            <?php foreach($make as $a): ?>
            <option value="<?php echo $a['pk_i_id']; ?>"><?php echo $a['s_name']; ?></option>
            <?php endforeach; ?>
	</select>
    </div>
    <div class="row _200">
	<label><?php _e('Model'); ?></label>
        <select name="model" id="model"></select>
    </div>

    <div class="row _200">
        <?php $locales = osc_get_locales();
        if(count($locales)==1) {
            $locale = $locales[0];?>
            <p>
            <label><?php _e('Car type'); ?></label><br />
            <select name="car_type" id="car_type">
            <?php foreach($car_type[$locale['pk_c_code']] as $k => $v) { ?>
            <option value="<?php echo  $k; ?>"><?php echo  $v;?></option>
            <?php }; ?>
            </select>
            </p>
        <?php } else { ?>
            <div class="tabber">
            <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
            <h2><?php echo $locale['s_name']; ?></h2>

            <p>
            <label><?php _e('Car type'); ?></label><br />
            <select name="car_type" id="car_type">
            <?php foreach($car_type[$locale['pk_c_code']] as $k => $v) { ?>
            <option value="<?php echo  $k; ?>"><?php echo  $v;?></option>
            <?php }; ?>
            </select>
            </p>

            </div>
            <?php }; ?>
            </div>
        <?php }; ?>
    </div>
    <div class="row _200">
	<label><?php _e('Year'); ?></label>
	<input type="text" name="year" id="year" value=""  size=4/>
    </div>
    <div class="row auto">
	<label><?php _e('Doors'); ?></label>
        <select name="doors" id="doors">
            <?php foreach(range(3, 5) as $n): ?>
            <option value="<?php echo $n; ?>"><?php echo $n; ?></option>
            <?php endforeach; ?>
	</select>
    </div>
    <div class="row auto">
	<label><?php _e('Seats'); ?></label>
        <select name="seats" id="seats">
            <?php foreach(range(1, 17) as $n): ?>
            <option value="<?php echo $n; ?>"><?php echo $n; ?></option>
            <?php endforeach; ?>
	</select>
    </div>
    <div class="row _200">
	<label><?php _e('Mileage'); ?></label>
	<input type="text" name="mileage" id="mileage" value="" />
    </div>
    <div class="row _200">
        <label><?php _e('Engine size (cc)'); ?></label>
        <input type="text" name="engine_size" id="engine_size" value="" />
    </div>
    <div class="row auto">
	<label><?php _e('Num. Airbags'); ?></label>
        <select name="num_airbags" id="num_airbags">
            <?php foreach(range(0, 8) as $n): ?>
            <option value="<?php echo $n; ?>"><?php echo $n; ?></option>
            <?php endforeach; ?>
	</select>
    </div>
    <div class="row _200">
	<label><?php _e('Transmission'); ?></label>
	<select name="transmission" id="transmission">
            <option value="MANUAL"><?php _e('Manual');?></option>
            <option value="AUTO"><?php _e('Auto');?></option>
        </select>
    </div>
    <div class="row _200">
	<label><?php _e('Fuel'); ?></label>
	<select name="fuel" id="fuel">
            <option value="DIESEL"><?php _e('Diesel');?></option>
            <option value="GASOLINE"><?php _e('Gasoline');?></option>
            <option value="ELECTRIC-HIBRID"><?php _e('Electric-hibrid');?></option>
            <option value="OTHER"><?php _e('Other');?></option>
        </select>
    </div>
    <div class="row _200">
	<label><?php _e('Seller'); ?></label>
	<select name="seller" id="seller">
            <option value="DEALER"><?php _e('Dealer');?></option>
            <option value="OWNER"><?php _e('Owner');?></option>
        </select>
    </div>
    <div class="row _20">
	<input type="checkbox" name="warranty" id="warranty" value="1" /> <label><?php _e('Warranty'); ?></label> <br />
    </div>
    <div class="row _20">
	<input type="checkbox" name="new" id="new" value="1" /> <label><?php _e('New'); ?></label> <br />
    </div>
    <div class="row auto _200">
	<label><?php _e('Power'); ?></label>
	<input type="text" name="power" id="power" value="" />
        <select name="power_unit" id="power_unit">
            <option value="KW"><?php _e('Kw');?></option>
            <option value="CV"><?php _e('Cv');?></option>
            <option value="CH"><?php _e('Ch');?></option>
            <option value="KM"><?php _e('Km');?></option>
            <option value="HP"><?php _e('Hp');?></option>
            <option value="PS"><?php _e('Ps');?></option>
            <option value="PK"><?php _e('Pk');?></option>
            <option value="CP"><?php _e('Cp');?></option>
        </select>
    </div>
    <div class="row auto">
	<label><?php _e('Gears'); ?></label>
	<select name="gears" id="gears">
            <?php foreach(range(1, 8) as $n): ?>
            <option value="<?php echo $n; ?>"><?php echo $n; ?></option>
            <?php endforeach; ?>
	</select>
    </div>
</div>

<script type="text/javascript">
    tabberAutomatic();
</script>