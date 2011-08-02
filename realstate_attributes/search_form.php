<?php
    $locale       = osc_current_user_locale();
    
    $numFloor     = explode(" - ", Params::getParam('numFloor'));
    $numFloorMin  = ($numFloor[0]!='')?$numFloor[0]:'0';
    $numFloorMax  = (isset($numFloor[1]) && $numFloor[1]!='')?$numFloor[1]:'15';
    
    $rooms        = explode(" - ", Params::getParam('rooms'));
    $roomsMin     = ($rooms[0]!='')?$rooms[0]:'0';
    $roomsMax     = (isset($rooms[1]) && $rooms[1]!='')?$rooms[1]:'15';
    
    $bathrooms    = explode(" - ", Params::getParam('bathrooms'));
    $bathroomsMin = ($bathrooms[0]!='')?$bathrooms[0]:'0';
    $bathroomsMax = (isset($bathrooms[1]) && $bathrooms[1]!='')?$bathrooms[1]:'15';
    
    $garages      = explode(" - ", Params::getParam('garages'));
    $garagesMin   = ($garages[0]!='')?$garages[0]:'0';
    $garagesMax   = (isset($garages[1]) && $garages[1]!='')?$garages[1]:'15';
    
    $year         = explode(" - ", Params::getParam('year'));
    $yearMin      = ($year[0]!='')?$year[0]:'1900';
    $yearMax      = (isset($year[1]) && $year[1]!='')?$year[1]:date('Y');
    
    $sq           = explode(" - ", Params::getParam('sq'));
    $sqMin        = ($sq[0]!='')?$sq[0]:'0';
    $sqMax        = (isset($sq[1]) && $sq[1]!='')?$sq[1]:'500';
?>
<style type="text/css">
    #slider { margin-right:10px; margin-left:10px;};
</style>

<script type="text/javascript">
    $(function() {
        $("#floor-range").slider({
            range: true,
            min: 0,
            max: 15,
            values: [<?php echo $numFloorMin;?>, <?php echo $numFloorMax;?>],
            slide: function(event, ui) {
                $("#numFloor").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#numFloor").val($("#floor-range").slider("values", 0) + ' - ' + $("#floor-range").slider("values", 1));
        $("#room-range").slider({
            range: true,
            min: 0,
            max: 15,
            values: [<?php echo $roomsMin; ?>, <?php echo $roomsMax;?>],
            slide: function(event, ui) {
                $("#rooms").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#rooms").val($("#room-range").slider("values", 0) + ' - ' + $("#room-range").slider("values", 1));
        $("#bathroom-range").slider({
            range: true,
            min: 0,
            max: 15,
            values: [<?php echo $bathroomsMin; ?>, <?php echo $bathroomsMax; ?>],
            slide: function(event, ui) {
                $("#bathrooms").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#bathrooms").val($("#bathroom-range").slider("values", 0) + ' - ' + $("#bathroom-range").slider("values", 1));
        $("#garage-range").slider({
            range: true,
            min: 0,
            max: 15,
            values: [<?php echo $garagesMin; ?>, <?php echo $garagesMax; ?>],
            slide: function(event, ui) {
                $("#garages").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#garages").val($("#garage-range").slider("values", 0) + ' - ' + $("#garage-range").slider("values", 1));
        $("#year-range").slider({
            range: true,
            min: 1900,
            max: <?php echo date('Y');?>,
            values: [<?php echo $yearMin; ?>, <?php echo $yearMax; ?>],
            slide: function(event, ui) {
                $("#year").val(ui.values[0] + ' - ' + ui.values[1]);
            }
        });
        $("#year").val($("#year-range").slider("values", 0) + ' - ' + $("#year-range").slider("values", 1));
        $("#sq-range").slider({
            range: true,
            min: 0,
            max: 500,
            values: [<?php echo $sqMin; ?>, <?php echo $sqMax; ?>],
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
                <option value="" <?php echo (Params::getParam('property_type')=='')?'selected':''; ?>><?php _e('Undefined', 'realstate_attributes'); ?></option>
                <option value="FOR RENT" <?php echo (Params::getParam('property_type')=='FOR RENT')?'selected':''; ?>><?php _e('For rent', 'realstate_attributes'); ?></option>
                <option value="FOR SALE" <?php echo (Params::getParam('property_type')=='FOR SALE')?'selected':''; ?>><?php _e('For sale', 'realstate_attributes'); ?></option>
            </select>
        </div>
    </div>
    <div class="row one_input">
        <p>
            <h6><?php _e('Property type', 'realstate_attributes'); ?></h6>
            <div class="">
                <select name="p_type" id="p_type">
                <option value=""><?php _e('Select a property type', 'realstate_attributes'); ?></option>
                <?php foreach($p_type[$locale] as $k => $v) { ?>
                    <option value="<?php echo  $k; ?>" <?php echo (Params::getParam('p_type')== $k) ? 'selected' : ''; ?>><?php echo @$v; ?></option>
                <?php } ?>
                </select>
            </div>
        </p>
    </div>
    
    <div class="row one_input">
        <h6 for="numFloor"><?php _e('Num. Floors Range', 'realstate_attributes'); ?></h6>
        <input type="text" id="numFloor" name="numFloor" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/><br/>
        <div class="slider" >
            <div id="floor-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Rooms Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="rooms" name="rooms" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div class="slider" >
            <div id="room-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Bathrooms Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="bathrooms" name="bathrooms" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div class="slider" >
            <div id="bathroom-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Garages Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="garages" name="garages" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div class="slider" >
            <div id="garage-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Construction year Range', 'realstate_attributes'); ?></h6>
            <input type="text" id="year" name="year" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div class="slider" >
            <div id="year-range"></div>
        </div>
    </div>
    
    <div class="row one_input">
        <p>
            <h6><?php _e('Square Meters Range', 'realstate_attributes'); ?></h6>
            <input type="text" name="sq" id="sq" style="background-color: transparent; border:0; color:#f6931f; font-weight:bold;" readonly/>
        </p>
        <div class="slider" >
            <div id="sq-range"></div>
        </div>
    </div>
    
    <div class="row checkboxes">
        <h6><?php _e('Other characteristics', 'realstate_attributes'); ?></h6>
        <ul>
            <li>
                <input <?php if(Params::getParam('heating') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="heating" id="heating" value="1" /> <label for="heating"><strong><?php _e('Heating', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('airCondition') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="airCondition" id="airCondition" value="1" /> <label for="airCondition"><strong><?php _e('Air condition', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('elevator') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="elevator" id="elevator" value="1" /> <label for="elevator"><strong><?php _e('Elevator', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('terrace') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="terrace" id="terrace" value="1" /> <label for="terrace"><strong><?php _e('Terrace', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('parking') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="parking" id="parking" value="1" /> <label for="parking"><strong><?php _e('Parking', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('furnished') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="furnished" id="furnished" value="1" /> <label for="furnished"><strong><?php _e('Furnished', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('new') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="new" id="new" value="1" /> <label for="new"><strong><?php _e('New', 'realstate_attributes'); ?></strong></label>
            </li>
            <li>
                <input <?php if(Params::getParam('by_owner') == 1 ) { echo 'checked="yes"'; } ?> style="width:20px;" type="checkbox" name="by_owner" id="by_owner" value="1" /> <label for="by_owner"><strong><?php _e('By owner', 'realstate_attributes'); ?></strong></label>
            </li>
        </ul>
    </div>
</fieldset>