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
                            result += '<option value=""><?php echo __("Select a model..."); ?></option>';
                            for(key in data) {
                                if(data[key].pk_i_id==model_id) {
                                    result += '<option value="' + data[key].pk_i_id + '" selected>' + data[key].s_name + '</option>';
                                } else {
                                    result += '<option value="' + data[key].pk_i_id + '">' + data[key].s_name + '</option>';
                                }
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
        
        // uniform()
        //$('#plugin-hook input:text, select#make, select#model, select#car_type, select#doors, select#seats, select#num_airbags, select#transmission, select#fuel, select#seller, select#power_unit, select#gears').uniform();
        
        
    });



</script>
<?php 
$make = Params::getParam('make') ;
$model = Params::getParam('model') ;
$type = Params::getParam('type') ;

$conn = getConnection();
$makes = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_make_attr ORDER BY s_name ASC', DB_TABLE_PREFIX);
if($make!='') {
    $models = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_model_attr WHERE `fk_i_make_id` = %d ORDER BY s_name ASC', DB_TABLE_PREFIX, $make);
} else {
    $models = array();
}

$types = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_vehicle_type_attr WHERE fk_c_locale_code = \'%s\'', DB_TABLE_PREFIX, osc_locale());
?>
<fieldset>
    <h3><?php _e('Cars attributes') ; ?></h3>

    <div class="row one_input">
        <h6><?php echo __('Make'); ?></h6>
        <select name="make" id="make" >
            <option value=""><?php  _e('Select a make'); ?></option>
            <?php foreach($makes as $m): ?>
    			<option value="<?php echo $m['pk_i_id']; ?>" <?php if($make==$m['pk_i_id']) { echo 'selected';};?>><?php echo $m['s_name']; ?></option>
        	<?php endforeach; ?>
        </select>
    </div>

    <div class="row one_input">
        <h6><?php echo __('Model'); ?></h6>
        <select name="model" id="model">
            <option value=""><?php  _e('Select a model'); ?></option>
            <?php foreach($models as $m): ?>
                <option value="<?php echo $m['pk_i_id']; ?>" <?php if($model==$m['pk_i_id']) { echo 'selected';};?>><?php echo $m['s_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row one_input">
        <h6><?php echo __('Car type'); ?></h6>
        <select name="type" id="type">
            <option value=""><?php  _e('Select a car type'); ?></option>
            <?php foreach($types as $p): ?>
                <option value="<?php echo $p['pk_i_id']; ?>" <?php if($type==$p['pk_i_id']) { echo 'selected';};?>><?php echo $p['s_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row one_input">
        <?php $transmission = Params::getParam('transmission') ; ?>
        <h6 for="transmission"><?php echo __('Transmission'); ?></h6>

        <input style="width:20px;" type="radio" name="transmission" value="MANUAL" id="manual" <?php if($transmission == 'MANUAL') {echo 'checked="yes"';}?>/> <label for="manual"><?php echo __('Manual'); ?></label><br />
        <input style="width:20px;" type="radio" name="transmission" value="AUTO" id="auto" <?php if($transmission == 'AUTO') {echo 'checked="yes"';}?>/> <label for="auto"><?php echo __('Automatic'); ?></label>
    </div>
</fieldset>