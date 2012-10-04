<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $applicantId = Params::getParam("people");

    $mjb = ModelJB::newInstance();
    $people = $mjb->getApplicant($applicantId);

    $file = $mjb->getCVFromApplicant($applicantId);
    ModelJB::newInstance()->changeSecret($file['pk_i_id']);
    $file = $mjb->getCVFromApplicant($applicantId);
    $notes = $mjb->getNotesFromApplicant($applicantId);

    $job = Item::newInstance()->findByPrimaryKey($people['fk_i_item_id']);

    if($people['b_read']==0) {
        ModelJB::newInstance()->changeRead($applicantId);
    }
?>
<div id="applicant-detail">
    <span><a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>" ><?php _e('Applicants', 'jobboard'); ?></a> &raquo; <?php echo @$people['s_name']; ?></span>
    <div class="applicant-header">
        <h2 class="render-title"><?php echo @$people['s_name']; ?> <a href="<?php echo osc_plugin_url(__FILE__); ?>download.php?data=<?php echo $applicantId; ?>|<?php echo $file['s_secret']; ?>" class="btn btn-mini" style="float:right;height:14px;"><?php _e('Download resume', 'jobboard'); ?></a><a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>&amp;jb_action=unread&amp;applicantID=<?php echo $applicantId; ?>" class="btn btn-mini" style="float:right;height:14px;"><?php _e('Mark as unread', 'jobboard'); ?></a></h2>
    </div>
    <div class="applicant-information">
        <h3 class="render-title" style="margin-bottom: 0px;"><?php _e('Personal information', 'jobboard'); ?></h3>
        <div class="half">
            <p><label><?php _e('Phone', 'jobboard'); ?> </label><?php echo @$people['s_phone']; ?></p>
            <p><label><?php _e('Email', 'jobboard'); ?> </label><?php echo @$people['s_email']; ?></p>
        </div>
        <div class="half">
            <p><label><?php _e('Apply date', 'jobboard'); ?> </label><?php echo @$people['dt_date']; ?></p>
            <p><label><?php _e('Birthday', 'jobboard'); ?> </label><?php echo @$people['dt_birthday']; ?></p>
        </div>
        <div class="half">
            <p><label><?php _e('Sex', 'jobboard'); ?> </label><?php echo jobboard_sex_to_string( @$people['s_sex'] ); ?></p>
        </div>
    </div>
    <div class="applicant-cover-letter">
        <div id="left-side">
            <h3 class="render-title"><?php _e('Cover letter', 'jobboard'); ?></h3>
            <p><?php echo nl2br($people['s_cover_letter']); ?></p>
        </div>
        <div id="right-side">
            <div class="well ui-rounded-corners applicant-box">
                <div class="status-icon">
                </div>
                <div class="rater big-star">
                    <?php for($k=1; $k<=5; $k++) {
                        echo '<input name="star' . $people['pk_i_id'] . '" type="radio" class="auto-star" value="' . $k . '" title="' . $k . '" ' . ($k == $people['i_rating'] ? 'checked="checked"' : '') . '/>';
                    } ?>
                </div>
                <select id="applicant_status" name="applicant_status" class="select-box-medium" data-applicant-id="<?php echo $applicantId; ?>">
                    <?php
                    $st_array = jobboard_status();
                    foreach($st_array as $k => $v) {
                        echo '<option value="'.$k.'" '.($k==$people['i_status']?'selected="selected"':'').'>'.$v.'</option>';
                    }
                    ?>
                </select>
                <div class="applied-for">
                    <?php _e("Applied for", "jobboard"); ?><br />
                    <?php if( !is_null($job['fk_i_item_id']) ) { ?>
                    <a href="<?php echo osc_item_admin_edit_url($job['fk_i_item_id']); ?>"><?php echo $job['s_title']; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo osc_contact_url(); ?>"><?php _e('Spontaneous application', 'jobboard'); ?></a>
                    <?php } ?>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div id="applicant-resume">
        <?php if(empty($file)) {
            _e("This applicant has not sumitted any resume", "jobboard");
        } else { ?>
        <iframe src="http://docs.google.com/viewer?embedded=true&url=<?php echo osc_plugin_url(__FILE__);?>download.php?data=<?php echo $applicantId; ?>|<?php echo $file['s_secret']; ?>"></iframe>
        <?php } ?>
    </div>
    <h3 class="sidebar-title render-title">
        <?php _e("Notes", "jobboard"); ?> <span class="note_plus"><a class="add_note btn btn-mini" href="javascript:void(0);"><?php _e("Add note", "jobboard"); ?></a></span>
    </h3>
    <div style="clear:both;"></div>
    <div id="dashboard_notes">
        <div id="nots_table_div">
            <?php if(count($notes)>0) { ?>
                <?php foreach($notes as $note) { ?>
                    <div class="note well ui-rounded-corners">
                        <div class="note-actions">
                            <a class="delete_note" href="javascript:void(0);" data-note-id="<?php echo $note['pk_i_id']; ?>"><?php _e("Delete", "jobboard"); ?></a>
                            <a class="edit_note" href="javascript:void(0);" data-note-id="<?php echo $note['pk_i_id']; ?>" data-note-text="<?php echo osc_esc_html($note['s_text']); ?>" ><?php _e("Edit", "jobboard"); ?></a>
                        </div>
                        <div class="note-date">
                            <b><?php echo date('d', strtotime($note['dt_date'])); ?></b>
                            <span><?php echo date('M', strtotime($note['dt_date'])); ?><br>
                            <?php echo date('Y', strtotime($note['dt_date'])); ?></span>
                        </div>
                        <div class="clear"></div>
                        <p class="note_text"><?php echo nl2br($note['s_text']); ?></p>
                    </div>
                <?php }; ?>
            <?php } else { ?>
                <div class="note empty-note well ui-rounded-corners">
                    <p><?php _e("No notes have been added to this applicant", "jobboard"); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div id="dialog-note-delete" title="<?php echo osc_esc_html(__('Delete note', 'jobboard')); ?>" class="has-form-actions hide" data-note-id="">
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Are you sure you want to delete this note?', 'jobboard'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-note-delete').dialog('close');"><?php _e('Cancel', 'jobboard'); ?></a>
                <a id="note-delete-submit" class="btn btn-red" href="javascript:void(0);" ><?php echo osc_esc_html( __('Delete', 'jobboard') ); ?></a>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<div id="dialog-note-form" title="<?php echo osc_esc_html(__('Note', 'jobboard')); ?>" class="has-form-actions hide" data-note-id="" data-note-action="" data-applicant-id="<?php echo $applicantId; ?>">
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Note', 'jobboard'); ?>
        </div>
        <div class="form-row">
            <textarea id="note_edit_text" name="note_text" style="margin: 2px 0px; width: 255px; height: 90px; "></textarea>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-note-form').dialog('close');"><?php _e('Cancel', 'jobboard'); ?></a>
                <a id="note-form-submit" href="javascript:void(0);" class="btn btn-red" ><?php echo osc_esc_html( __('Save', 'jobboard') ); ?></a>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<div id="dialog-applicant-status" title="<?php echo osc_esc_html(__('Applicant status')); ?>" class="has-form-actions hide">
    <div class="form-horizontal">
        <div class="form-row"><?php _e('Do you want to send an email to the applicant notifying the status of the application?', 'jobboard'); ?></div>
        <div class="form-actions">
            <div class="wrapper">
                <a id="applicant-status-cancel" class="btn" href="javascript:void(0);"><?php _e('Cancel', 'jobboard'); ?></a>
                <a id="applicant-status-submit" href="javascript:void(0);" class="btn btn-red" ><?php echo osc_esc_html( __('Send', 'jobboard') ); ?></a>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>