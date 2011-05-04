<fieldset>
    <h3><?php _e('Cars attributes') ; ?></h3>

    <div class="row one_input">
        <?php $type = Params::getParam('type') ; ?>
        <h6><?php echo __('Type'); ?></h6>
        <input type="text" name="type" id="type" value="<?php if($type != ""){echo $type;}?>" />
    </div>

    <div class="row one_input">
        <?php $model = Params::getParam('model') ; ?>
        <h6><?php echo __('Model'); ?></h6>
        <input type="text" name="model" id="model" value="<?php if($model != ""){echo $model;}?>" />
    </div>

    <div class="row one_input">
        <?php $numAirbags = Params::getParam('numAirbags') ; ?>
        <h6><?php echo __('Num. of airbags'); ?></h6>
        <div class="auto">
            <select name="numAirbags" id="numAirbags">
            <?php foreach(range(0, 8) as $n): ?>
                <option value="<?php echo $n; ?>" <?php if($n == $numAirbags) {echo 'selected';}?>><?php echo $n; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row one_input">
        <?php $transmission = Params::getParam('transmission') ; ?>
        <h6 for="transmission"><?php echo __('Transmission'); ?></h6>

        <input style="width:20px;" type="radio" name="transmission" value="MANUAL" id="manual" <?php if($transmission == 'MANUAL') {echo 'checked="yes"';}?>/> <label for="manual"><?php echo __('Manual'); ?></label><br />
        <input style="width:20px;" type="radio" name="transmission" value="AUTO" id="auto" <?php if($transmission == 'AUTO') {echo 'checked="yes"';}?>/> <label for="auto"><?php echo __('Automatic'); ?></label>
    </div>
</fieldset>