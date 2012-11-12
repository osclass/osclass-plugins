<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $applicantId = Params::getParam("people");

    $mjb    = ModelJB::newInstance();
    $people = $mjb->getApplicant($applicantId);

    $file   = $mjb->getCVFromApplicant($applicantId);
    ModelJB::newInstance()->changeSecret($file['pk_i_id']);
    $file   = $mjb->getCVFromApplicant($applicantId);
    $notes  = $mjb->getNotesFromApplicant($applicantId);

    $job = Item::newInstance()->findByPrimaryKey($people['fk_i_item_id']);

    if($people['b_read']==0) {
        ModelJB::newInstance()->changeRead($applicantId);
    }

    // get killer questions ...
    $jobInfo        = $mjb->getJobsAttrByItemId($people['fk_i_item_id']);
    $killer_form_id = $jobInfo['fk_i_killer_form_id'];
    $aQuestions     = array();
    $acomulateScore = 0;
    $maxPunctuation = 0;
    $aKillerForm    = ModelKQ::newInstance()->getKillerForm($killer_form_id);
    if(is_array($aKillerForm) && !empty($aKillerForm)) {
        // get killer form information ...
        $aQuestions = ModelKQ::newInstance()->getKillerQuestions($killer_form_id);
        $aAnswers   = ModelKQ::newInstance()->getResultsByApplicant($applicantId);

        foreach($aAnswers as $key => $_aux) {
            if(is_numeric( @$_aux['s_punctuation'] )){
                $acomulateScore += @$_aux['s_punctuation'];
            }
        }
        $maxPunctuation = count($aAnswers)*10;
    }
    $score          = $people['d_score'];
?>
<div id="applicant-detail">
    <span><a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>" ><?php _e('Applicants', 'jobboard'); ?></a> &raquo; <?php echo @$people['s_name']; ?></span>
    <div class="applicant-header">
        <h2 class="render-title"><?php echo @$people['s_name']; ?> <a href="<?php echo osc_plugin_url(__FILE__); ?>download.php?data=<?php echo $applicantId; ?>|<?php echo $file['s_secret']; ?>" class="btn btn-mini btn-blue" style="float:right;height:14px;"><?php _e('Download resume', 'jobboard'); ?></a><a href="<?php echo osc_admin_render_plugin_url("jobboard/people.php"); ?>&amp;jb_action=unread&amp;applicantID=<?php echo $applicantId; ?>" class="btn btn-mini" style="float:right;height:14px;"><?php _e('Mark as unread', 'jobboard'); ?></a></h2>
    </div>
    <div>
        <div id="left-side">
            <div class="applicant-information well">
                <div class="half">
                    <p><label><?php _e('Phone', 'jobboard'); ?> </label><br/><?php echo @$people['s_phone']; ?></p>
                    <p><label><?php _e('Sex', 'jobboard'); ?> </label><br/><?php echo jobboard_sex_to_string( @$people['s_sex'] ); ?></p>
                </div>
                <div class="half">
                    <p><label><?php _e('Apply date', 'jobboard'); ?> </label><br/><?php echo @$people['dt_date']; ?></p>
                    <p><label><?php _e('Birthday', 'jobboard'); ?> </label><br/><?php echo @$people['dt_birthday']; ?></p>
                </div>
                <p><label><?php _e('Email', 'jobboard'); ?> </label><br/><?php echo @$people['s_email']; ?></p>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <div class="applicant-cover-letter">
                <h3 class="render-title"><?php _e('Cover letter', 'jobboard'); ?></h3>
                <p><?php echo nl2br($people['s_cover_letter']); ?></p>
            </div>
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
                    <?php if( !is_null(@$job['fk_i_item_id']) ) { ?>
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
    <?php if(count($aQuestions)>0) { ?>
    <h3 class="sidebar-title render-title" style="display:inline-block;">
        <?php _e("Killer questions", "jobboard"); ?> <span id="sum_punctuations"><?php echo $acomulateScore; ?></span>/<?php echo $maxPunctuation;?><?php if(@$people['b_corrected']) { ?><i class="circle circle-green" style="position: relative;top: 6px;padding-right: 4px;padding-left: 4px;"> <?php _e('Corrected', 'jobboard'); echo @$people['d_score']; ?> </i><?php } ?>
    </h3>
    <div style="clear:both;"></div>
    <div id="killer_questions_applicant" style="margin-top:15px;">
        <div id="kq_table_div">
            <div id="killerquestions">
                <?php foreach($aQuestions['questions'] as $key => $q) { ?>
                <div id="question_<?php echo $q['pk_i_id'];?>" data-id="<?php echo $q['pk_i_id'];?>" class="well rounded-well" style="margin-bottom: 15px;">
                    <label class="score">
                        <div class="score-box">
                        <?php
                        $hide_circle = false;
                        $puntuationShow = '?';
                        if(@$aAnswers[$q['pk_i_id']]['s_punctuation']!='') {
                            $puntuationShow = @$aAnswers[$q['pk_i_id']]['s_punctuation'];
                        }
                        ?>
                        <i class="score-unit"><?php echo $puntuationShow;?></i>
                        <span class="help"><?php _e('score','jobboard'); ?></span>
                        <?php if($q['a_answers']===false){ ?>
                            <div class="score-manage">
                                <select class="answer_punctuation" data-question-id="<?php echo $q['pk_i_id'];?>"
                                data-killerform-id="<?php echo @$aAnswers[$q['pk_i_id']]['fk_i_killer_form_id']; ?>"
                                data-applicant-id="<?php echo @$aAnswers[$q['pk_i_id']]['fk_i_applicant_id']; ?>">
                                    <option value="" <?php if(@$default==''){ echo 'selected'; } ?>><?php _e('Change', 'jobboard'); ?></option>
                                    <option value="10" <?php if(@$default=='10'){ echo 'selected'; } ?>>10</option>
                                    <option value="9" <?php if(@$default=='9'){ echo 'selected'; } ?>>9</option>
                                    <option value="8" <?php if(@$default=='8'){ echo 'selected'; } ?>>8</option>
                                    <option value="7" <?php if(@$default=='7'){ echo 'selected'; } ?>>7</option>
                                    <option value="6" <?php if(@$default=='6'){ echo 'selected'; } ?>>6</option>
                                    <option value="5" <?php if(@$default=='5'){ echo 'selected'; } ?>>5</option>
                                    <option value="4" <?php if(@$default=='4'){ echo 'selected'; } ?>>4</option>
                                    <option value="3" <?php if(@$default=='3'){ echo 'selected'; } ?>>3</option>
                                    <option value="2" <?php if(@$default=='2'){ echo 'selected'; } ?>>2</option>
                                    <option value="1" <?php if(@$default=='1'){ echo 'selected'; } ?>>1</option>
                                    <option value="reject" <?php if(@$default=='reject'){ echo 'selected'; } ?>><?php _e('Reject', 'jobboard'); ?></option>
                                </select>
                            </div>
                        <?php } ?>
                        </div>
                    </label>
                    <?php _e('Question', 'jobboard'); ?> <?php echo $key;?>
                    <p><?php echo $q['s_text'];?></p>
                    <?php if($q['a_answers']!==false){ ?>
                    <p>
                        <ol style="padding-left:0px;">
                            <?php foreach($q['a_answers'] as $key_ => $a){ ?>
                            <li>
                                <?php $b_answer = @$aAnswers[$q['pk_i_id']]['fk_i_answer_id'] == $a['pk_i_id'];
                                if($b_answer) { ?>
                                <i class="circle circle-green" style="width: 25px;position: relative;top: 6px;">&#10142;</i>
                                <?php } else { ?>
                                <i style="width: 29px;display: inline-block;"></i>
                                <?php } ?>
                                <span class="input-large"><?php echo osc_esc_html($a['s_text']);?></span>
                                <span style="display:inline-block;">
                                    <i class="circle circle-<?php if($b_answer) { echo "red";} else {echo "gray";}?>" style="position: relative;top: 6px;padding-right: 4px;padding-left: 4px;"> <?php echo $a['s_punctuation'];?> </i>
                                </span>
                            </li>
                            <?php } ?>
                        </ol>
                    </p>
                    <?php } else { // opened answer
                        // get punctuation of open question ! -> $default
                        $default = $aAnswers[$q['pk_i_id']]['s_punctuation'];
                        ?>
                    <p>
                        <i class="circle circle-green" style="width: 25px;position: relative;top: 6px;">&#10142;</i>
                        <textarea class="required" name="question[<?php echo $q['pk_i_id']; ?>][open]"><?php echo osc_esc_html(@$aAnswers[$q['pk_i_id']]['s_answer_opened']); ?></textarea>
                    </p>
                    <?php }  ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="jobboard-loading-container" style="display:none;">
        <div id="jobboard-loading-image" ></div>
    </div>
    <?php } ?>
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
<div id="dialog-applicant-status" title="<?php echo osc_esc_html(__('Applicant status')); ?>" class="has-form-actions hide" style="height: 350px; width: 725px;">
    <div class="form-horizontal">
        <div class="form-row"><?php _e('Do you want to send an email to the applicant notifying the status of the application?', 'jobboard'); ?></div>
        <div class="form-row"><textarea id="applicant-status-notification-message" style="width: 700px; height: 150px;"></textarea></div>
        <div class="form-actions">
            <div class="wrapper">
                <a id="applicant-status-cancel" class="btn" href="javascript:void(0);"><?php _e("Don't send", 'jobboard'); ?></a>
                <a id="applicant-status-submit" href="javascript:void(0);" class="btn btn-red" ><?php echo osc_esc_html( __('Send', 'jobboard') ); ?></a>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>