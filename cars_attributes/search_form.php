<fieldset>
    <h3><?php _e('Cars attributes') ; ?></h3>

    <div class="row one_input">
        <h6><?php echo __('Type'); ?></h6>
        <input type="text" name="type" id="type" value="" />
    </div>

    <div class="row one_input">
        <h6><?php echo __('Model'); ?></h6>
        <input type="text" name="model" id="model" value="" />
    </div>

    <div class="row one_input">
        <h6><?php echo __('Num. of airbags'); ?></h6>
        <div class="auto">
            <select name="numAirbags" id="numAirbags">
            <?php foreach(range(0, 8) as $n): ?>
                <option value="<?php echo $n; ?>"><?php echo $n; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row one_input">
        <h6 for="transmission"><?php echo __('Transmission'); ?></h6>

        <input style="width:20px;" type="radio" name="transmission" value="MANUAL" id="manual" /> <label for="manual"><?php echo __('Manual'); ?></label><br />
        <input style="width:20px;" type="radio" name="transmission" value="AUTO" id="auto" /> <label for="auto"><?php echo __('Automatic'); ?></label>
    </div>
</fieldset>