<?php
    $relations = array('HIRE' =>  __('Hire someone', 'jobs_attributes') , 'LOOK' => __('Looking for a job', 'jobs_attributes'));
    $index = trim(@$detail['e_relation']);
    $locales = osc_get_locales();
?>

<h3><?php _e('Job attributes', 'jobs_attributes'); ?></h3>
<table>
    <tr>
        <td><label for="relation"><?php _e('Relation', 'jobs_attributes'); ?></label></td>
        <td><?php echo @$relations[$index]; ?></td>
    </tr>
    <?php if(isset($detail['s_company_name']) && $detail['s_company_name']!='') { ?>
    <tr>
        <td><label for="companyName"><?php _e('Company name', 'jobs_attributes'); ?></label></td>
        <td><?php echo @$detail['s_company_name']; ?></td>
    </tr>
    <?php }; ?>
    <?php if(isset($detail['e_position_type']) && $detail['e_position_type']!='') { ?>
    <tr>
        <td><label for="positionType"><?php _e('Position type', 'jobs_attributes'); ?></label></td>
        <td><?php echo @$detail['e_position_type']; ?></td>
    </tr>
    <?php }; ?>
    <?php if((isset($detail['i_salary_min']) && $detail['i_salary_min']!='') || (isset($detail['i_salary_max']) && $detail['i_salary_max']!='')) { ?>
    <tr>
        <td><label for="salaryRange"><?php _e('Salary range', 'jobs_attributes'); ?></label></td>
        <td><?php echo @$detail['i_salary_min']; ?> - <?php echo @$detail['i_salary_max']; ?> <?php echo @$detail['e_salary_period']; ?></td>
    </tr>
    <?php }; ?>
    <?php
    if(count($locales)==1) {
        $locale = $locales[0];?>
        <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_desired_exp']) && $detail['locale'][$locale['pk_c_code']]['s_desired_exp']!='') { ?>
        <p>
            <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label><br />
            <?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_studies']) && $detail['locale'][$locale['pk_c_code']]['s_studies']
            <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label><br />
            <?php echo @$detail['locale'][$locale['pk_c_code']]['s_studies']; ?>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']) && $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']!='') { ?>
        <p>
            <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label><br />
            <?php echo @$detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_desired_requirements']) && $detail['locale'][$locale['pk_c_code']]['s_desired_requirements']!='') { ?>
        <p>
            <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label><br />
            <?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_contract']) && $detail['locale'][$locale['pk_c_code']]['s_contract']!='') { ?>
        <p>
            <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label><br />
            <?php echo @$detail['locale'][$locale['pk_c_code']]['s_contract']; ?>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_company_description']) && $detail['locale'][$locale['pk_c_code']]['s_company_description']!='') { ?>
        <p>
            <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label><br />
            <?php echo @$detail['locale'][$locale['pk_c_code']]['s_company_description']; ?>
        </p>
        <?php }; ?>
    <?php }else { ?>
        <div class="tabber">
        <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
                <h2><?php echo $locale['s_name']; ?></h2>
                <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_desired_exp']) && $detail['locale'][$locale['pk_c_code']]['s_desired_exp']!='') { ?>
                <p>
                    <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label><br />
                    <?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>
                </p>
                <?php }; ?>
                <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_studies']) && $detail['locale'][$locale['pk_c_code']]['s_studies']!='') { ?>
                <p>
                    <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label><br />
                    <?php echo @$detail['locale'][$locale['pk_c_code']]['s_studies']; ?>
                </p>
                <?php }; ?>
                <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']) && $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']!='') { ?>
                <p>
                    <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label><br />
                    <?php echo @$detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?>
                </p>
                <?php }; ?>
                <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_desired_requirements']) && $detail['locale'][$locale['pk_c_code']]['s_desired_requirements']!='') { ?>
                <p>
                    <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label><br />
                    <?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?>
                </p>
                <?php }; ?>
                <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_contract']) && $detail['locale'][$locale['pk_c_code']]['s_contract']!='') { ?>
                <p>
                    <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label><br />
                    <?php echo @$detail['locale'][$locale['pk_c_code']]['s_contract']; ?>
                </p>
                <?php }; ?>
                <?php if(isset($detail['locale'][$locale['pk_c_code']]['s_company_description']) && $detail['locale'][$locale['pk_c_code']]['s_company_description']!='') { ?>
                <p>
                    <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label><br />
                    <?php echo @$detail['locale'][$locale['pk_c_code']]['s_company_description']; ?>
                </p>
                <?php }; ?>
            </div>
        <?php }; ?>
        </div>
    <?php }; ?>
</table>
