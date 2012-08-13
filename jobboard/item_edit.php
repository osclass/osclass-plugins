<div class="jobboard-plugin">
    <h3 class="render-title"><?php _e('Job details', 'jobboard'); ?></h3>
    <div class="form-horizontal">
        <div class="form-row">
            <div class="form-label"><?php _e('Position type', 'jobboard'); ?></div>
            <div class="form-controls">
                <select name="positionType" class="select-box">
                    <?php foreach(get_jobboard_position_types() as $k => $v) { ?>
                    <option value="<?php echo $k; ?>" <?php if($detail['e_position_type'] == $k) { echo 'selected'; } ?>><?php echo $v; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Salary', 'jobboard'); ?></div>
            <div class="form-controls">
                <input type="text" class="input-large" name="salaryText" value="<?php echo $detail['s_salary_text']; ?>" />
            </div>
        </div>
        <?php foreach(osc_get_locales() as $locale) { ?>
        <div class="form-row">
            <div class="form-label"><?php _e('Contract', 'jobboard'); ?></div>
            <div class="form-controls">
                <input type="text" class="input-large" name="<?php echo $locale['pk_c_code']; ?>#contract" value="<?php echo $detail['locale'][$locale['pk_c_code']]['s_contract']; ?>" />
            </div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Studies', 'jobboard'); ?></div>
            <div class="form-controls">
                <input type="text" class="input-large" name="<?php echo $locale['pk_c_code']; ?>#studies" value="<?php echo $detail['locale'][$locale['pk_c_code']]['s_studies']; ?>" />
            </div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Desired experience', 'jobboard'); ?></div>
            <div class="form-controls">
                <input type="text" class="input-large" name="<?php echo $locale['pk_c_code']; ?>#desired_exp" value="<?php echo $detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>" />
            </div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Minimum requirements', 'jobboard'); ?></div>
            <div class="form-controls">
                <textarea name="<?php echo $locale['pk_c_code']; ?>#min_reqs" style="width: 350px; height: 100px;"><?php echo $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Desired requirements', 'jobboard'); ?></div>
            <div class="form-controls">
                <textarea name="<?php echo $locale['pk_c_code']; ?>#desired_reqs" style="width: 350px; height: 100px;"><?php echo $detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?></textarea>
            </div>
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