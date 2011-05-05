<style type="text/css">
    #slider { margin-right:10px; margin-left:10px;};
</style>

<script type="text/javascript">
    $(function() {
        $("#floor-range").slider({
            range: true,
            min: 1,
            max: 15,
            values: [1, 15],
            slide: function(event, ui) {
                $("#numFloor").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#numFloor").val($("#floor-range").slider("values", 0) + ' - ' + $("#floor-range").slider("values", 1));
        $("#room-range").slider({
            range: true,
            min: 1,
            max: 10,
            values: [1, 10],
            slide: function(event, ui) {
                $("#rooms").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#rooms").val($("#room-range").slider("values", 0) + ' - ' + $("#room-range").slider("values", 1));
        $("#bathroom-range").slider({
            range: true,
            min: 1,
            max: 5,
            values: [1, 5],
            slide: function(event, ui) {
                $("#bathrooms").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#bathrooms").val($("#bathroom-range").slider("values", 0) + ' - ' + $("#bathroom-range").slider("values", 1));
        $("#garage-range").slider({
            range: true,
            min: 1,
            max: 5,
            values: [1, 5],
            slide: function(event, ui) {
                $("#garages").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#garages").val($("#garage-range").slider("values", 0) + ' - ' + $("#garage-range").slider("values", 1));
        $("#year-range").slider({
            range: true,
            min: 1900,
            max: 2011,
            values: [1900, 2011],
            slide: function(event, ui) {
                $("#year").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#year").val($("#year-range").slider("values", 0) + ' - ' + $("#year-range").slider("values", 1));
        $("#sq-range").slider({
            range: true,
            min: 5,
            max: 500,
            values: [5, 500],
            slide: function(event, ui) {
                $("#sq").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#sq").val($("#sq-range").slider("values", 0) + ' - ' + $("#sq-range").slider("values", 1));
    });
</script>

<fieldset>
    <h3><stong style="font-weight: normal;"><?php _e("Realestate attributes", 'realstate_attributes');?></stong></h3>
    <div class="row one_input">
        <h6><?php _e('Type', 'realstate_attributes'); ?></h6>
        <div class="">
            <select name="property_type" id="property_type">
                <option value="FOR RENT"><?php _e('For rent', 'realstate_attributes'); ?></option>
                <option value="FOR SALE"><?php _e('For sale', 'realstate_attributes'); ?></option>
            </select>
        </div>
    </div>
    <div class="row one_input">
        <?php
        $locales = osc_get_locales();
        if(count($locales)==1) {
            $locale = $locales[0];
        ?>
            <p>
                <h6><?php _e('Property type', 'realstate_attributes'); ?></h6>
                <div class="">
                    <select name="p_type" id="p_type">
                    <?php foreach($p_type[$locale['pk_c_code']] as $k => $v) { ?>
                        <option value="<?php echo  $k; ?>"><?php echo  @$v;?></option>
                    <?php }; ?>
                    </select>
                </div>
            </p>
        <?php } else { ?>
            <div class="tabber">
                <?php foreach($locales as $locale) {?>
                <div class="tabbertab">
                    <h2><?php echo $locale['s_name']; ?></h2>
                    <p>
                        <h6><?php _e('Property type', 'realstate_attributes'); ?></h6>
                        <div class="auto">
                            <select name="p_type" id="p_type">
                            <?php foreach($p_type[$locale['pk_c_code']] as $k => $v) { ?>
                                <option value="<?php echo  $k; ?>"><?php echo @$v;?></option>
                            <?php }; ?>
                            </select>
                        </div>
                    </p>
                </div>
                <?php }; ?>
            </div>
        
        <?php }; ?>
    </div>
    
    <div class="row one_input">
        
            <h6 for="numFloor"><?php _e('Num. Floors Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="numFloor" name="numFloor" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/><br/>
        
        <div id="slider" >
            <div id="floor-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Rooms Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="rooms" name="rooms" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div id="slider" >
            <div id="room-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Bathrooms Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="bathrooms" name="bathrooms" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div id="slider" >
            <div id="bathroom-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Garages Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="garages" name="garages" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div id="slider" >
            <div id="garage-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Construction year Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="year" name="year" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div id="slider" >
            <div id="year-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Square Meters Range', 'realstate_attributes'); ?></h6>
            <input type="text" name="sq" id="sq" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div id="slider" >
            <div id="sq-range"></div>
        </div>
    </div>
    
    <div class="row checkboxes">
        <h6><?php _e('Other characteristics', 'realstate_attributes'); ?></h6>
        <ul>
            <li>
                <input style="width:20px;" type="checkbox" name="heating" id="heating" value="1" /> <label for="heating"><strong><?php _e('Heating', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="airCondition" id="airCondition" value="1" /> <label for="airCondition"><strong><?php _e('Air condition', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="elevator" id="elevator" value="1" /> <label for="elevator"><strong><?php _e('Elevator', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="terrace" id="terrace" value="1" /> <label for="terrace"><strong><?php _e('Terrace', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="parking" id="parking" value="1" /> <label for="parking"><strong><?php _e('Parking', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="furnished" id="furnished" value="1" /> <label for="furnished"><strong><?php _e('Furnished', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="new" id="new" value="1" /> <label for="new"><strong><?php _e('New', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input style="width:20px;" type="checkbox" name="by_owner" id="by_owner" value="1" /> <label for="by_owner"><strong><?php _e('By owner', 'realstate_attributes'); ?></strong></label>
            </li>
        </ul>
    </div>
</fieldset>