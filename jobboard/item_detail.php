<?php
    $positions = array('UNDEF' => __('Undefined', 'jobboard'), 'PART' => __('Part time', 'jobboard'), 'FULL' => __('Full-time', 'jobboard'));
    $locale    = osc_current_user_locale();
?>
<h2><?php _e('Job details', 'jobboard'); ?></h2>
<div class="job-detail">
    <table>
        <?php if(@$detail['e_position_type'] != "") { ?>
        <tr>
            <td><label for="positionType"><?php _e('Position type', 'jobboard'); ?></label></td>
            <td><?php echo $positions[$detail['e_position_type']]; ?></td>
        </tr>
        <?php } ?>
        <?php if(@$detail['s_salary_text'] != "" ) { ?>
        <tr>
            <td><label for="salaryText"><?php _e('Salary', 'jobboard'); ?></label></td>
            <td><?php echo @$detail['s_salary_text']; ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php if(isset($detail['locale'][$locale]['s_desired_exp']) && $detail['locale'][$locale]['s_desired_exp']!='') { ?>
    <div>
        <label for="desired_exp"><?php _e('Desired experience', 'jobboard'); ?></label>
        <p><?php echo @$detail['locale'][$locale]['s_desired_exp']; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_studies']) && $detail['locale'][$locale]['s_studies']!='') { ?>
    <div>
        <label for="studies"><?php _e('Studies', 'jobboard'); ?></label>
        <p><?php echo @$detail['locale'][$locale]['s_studies']; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_minimum_requirements']) && $detail['locale'][$locale]['s_minimum_requirements']!='') { ?>
    <div>
        <label for="min_reqs"><?php _e('Minimum requirements', 'jobboard'); ?></label>
        <p><?php echo nl2br( @$detail['locale'][$locale]['s_minimum_requirements'] ) ; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_desired_requirements']) && $detail['locale'][$locale]['s_desired_requirements']!='') { ?>
    <div>
        <label for="desired_reqs"><?php _e('Desired requirements', 'jobboard'); ?></label>
        <p><?php echo nl2br( @$detail['locale'][$locale]['s_desired_requirements'] ); ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_contract']) && $detail['locale'][$locale]['s_contract']!='') { ?>
    <div>
        <label for="contract"><?php _e('Contract', 'jobboard'); ?></label>
        <p><?php echo @$detail['locale'][$locale]['s_contract']; ?></p>
    </div>
    <?php } ?>
</div>