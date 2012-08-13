<fieldset>
    <h3><?php _e("Job attributes", 'jobboard'); ?></h3>
    <div class="row one_input">
        <h6><?php _e('Position type', 'jobboard'); ?></h6>
        <div class="auto">
            <select name="positionType" id="positionType">
                <option value="UNDEF" <?php echo (Params::getParam('positionType')=='UNDEF')?'selected':''; ?>><?php _e('Undefined', 'jobboard'); ?></option>
                <option value="FULL" <?php echo (Params::getParam('positionType')=='FULL')?'selected':''; ?>><?php _e('Full-time', 'jobboard'); ?></option>
                <option value="PART" <?php echo (Params::getParam('positionType')=='PART')?'selected':''; ?>><?php _e('Part time', 'jobboard'); ?></option>
            </select>
        </div>
    </div>
</fieldset>