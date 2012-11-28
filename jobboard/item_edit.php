<div class="jobboard-plugin">
    <h3 class="render-title"><?php _e('Job details', 'jobboard'); ?></h3>
    <div class="form-horizontal">
        <div class="form-row">
            <label><?php _e('Position type', 'jobboard'); ?></label>
            <select name="positionType" class="select-box">
                <?php foreach(get_jobboard_position_types() as $k => $v) { ?>
                <option value="<?php echo $k; ?>" <?php if(@$detail['e_position_type'] == $k) { echo 'selected'; } ?>><?php echo $v; ?></option>
                <?php } ?>
            </select>
            </div>
        </div>
        <div class="form-row">
            <label><?php _e('Num. of positions', 'jobboard'); ?></label>
            <select name="numPositions" class="select-box">
                <?php for($k=1;$k<=10;$k++) { ?>
                <option value="<?php echo $k; ?>" <?php if(@$detail['i_num_positions'] == $k) { echo 'selected'; } ?>><?php echo $k; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-row">
            <label><?php _e('Salary', 'jobboard'); ?></label>
            <input type="text" class="input-large" name="salaryText" value="<?php echo @$detail['s_salary_text']; ?>" />
        </div>
        <?php foreach(osc_get_locales() as $locale) { ?>
        <div class="form-row input-title-wide">
            <label style="font-size: 14px!important;"><?php _e('Contract', 'jobboard'); ?></label>
            <input type="text" class="input-large" name="contract[<?php echo $locale['pk_c_code']; ?>]" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_contract']; ?>" />
        </div>
        <div class="form-row input-title-wide">
            <label  style="font-size: 14px!important;"><?php _e('Studies', 'jobboard'); ?></label>
            <input type="text" class="input-large" name="studies[<?php echo $locale['pk_c_code']; ?>]" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_studies']; ?>" />
        </div>
        <div class="form-row input-title-wide">
            <label style="font-size: 14px!important;"><?php _e('Desired experience', 'jobboard'); ?></label>
            <input type="text" class="input-large" name="desired_exp[<?php echo $locale['pk_c_code']; ?>]" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>" />
        </div>
        <div class="form-row input-description-wide">
            <label><?php _e('Minimum requirements', 'jobboard'); ?></label>
            <textarea name="min_reqs[<?php echo $locale['pk_c_code']; ?>]" rows="5"><?php echo @$detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?></textarea>
        </div>
        <div class="form-row input-description-wide">
            <label><?php _e('Desired requirements', 'jobboard'); ?></label>
            <textarea name="desired_reqs[<?php echo $locale['pk_c_code']; ?>]" rows="5"><?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?></textarea>
        </div>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    $('.jobboard-plugin select').each(function(){
        selectUi($(this));
    });
    tabberAutomatic();
</script>