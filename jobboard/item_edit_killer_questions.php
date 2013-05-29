<?php
    $killer_form_id  = null;
    $kf_status       = 'new'; // 3 options: new, edit, disabled
    $killerQuestions = array();

    // killer form exist ...
    if( is_numeric(@$detail['fk_i_killer_form_id']) ) {
        $killer_form_id = @$detail['fk_i_killer_form_id'];

        $aKillerForm = ModelKQ::newInstance()->getKillerForm($killer_form_id);
        if( count($aKillerForm) > 1 ) {
            // get killer form information ...
            $killerQuestions = ModelKQ::newInstance()->getKillerQuestions($killer_form_id);

            $kf_status       = 'edit';
            $kf_status       = 'disabled';
            list($applicants, $total) = ModelJB::newInstance()->searchCount(array('item' => $itemID));
            if( $applicants > 0 ) {
                $kf_status = 'disabled';
            }
        }
    }
?>
<h2 class="render-title separate-top"><?php _e('Killer Questions' ,'jobboard'); ?></h2>
<div class="grid-first-row grid-100">
    <div class="flashmessage-dashboard-jobboard">
        <div class="flashmessage flashmessage-inline">
            <?php
                switch($kf_status) {
                    case('new'):
                        _e("Killer question form note: Once you create it you won't be able to update or remove it without deleting vacancy itself", 'jobboard');
                    break;
                    case('edit'):
                        _e("Killer question form note: Once you create it you won't be able to update or remove it without deleting vacancy itself <strong>Edit</strong>", 'jobboard');
                    break;
                    case('disabled'):
                        _e("Killer question form note: Once you create it you won't be able to update or remove it without deleting vacancy itself <strong>Disabled</strong>", 'jobboard');
                    break;
                }
            ?>
            <a class="btn ico btn-mini ico-close">x</a>
        </div>
    </div>
</div>
<div id="killerquestions">
    <?php if( $kf_status !== 'new' ) { foreach($killerQuestions['questions'] as $key => $q) { ?>
    <div id="question_<?php echo $q['pk_i_id']; ?>" data-id="<?php echo $q['pk_i_id']; ?>" class="new_question">
        <label><?php printf(__('Question %1$s', 'jobboard'), $key); ?></label>
        <?php
            $hide = array('add' => 'style="display:none"', 'remove' => '');
            $hasAnswers = false;
            if($q['a_answers'] === false) {
                $hide['add']    = '';
                $hide['remove'] = 'style="display:none"';
            } else {
                $hasAnswers = true;
            }
        ?>
            <?php /*<a class="add-remove-btn btn btn-mini btn-red" onclick="removeQuestion($(this));return false;"><?php _e('Remove question','jobboard'); ?></a> */ ?>
            <input <?php if( $kf_status === 'disabled' ) { ?>disabled="disabled"<?php } ?> class="input-large question_input" type="text" name="question[<?php echo $q['pk_i_id']; ?>][question]" value="<?php echo osc_esc_html($q['s_text']); ?>"/>
        <div class="containerAnswers">

            <?php if( $hasAnswers ) { ?>
            <div class="containerAnswersReplace">
                <ol>
                    <?php
                    $num_questions = count($q['a_answers']);
                    foreach($q['a_answers'] as $key_ => $a){ ?>
                    <li>
                        <input <?php if( $kf_status === 'disabled' ) { ?>disabled="disabled"<?php } ?> class="input-large" type="text" name="question[<?php echo $q['pk_i_id'];?>][answer][<?php echo $a['pk_i_id'];?>]" value="<?php echo osc_esc_html($a['s_text']);?>"/><?php _punctuationSelect_update($q['pk_i_id'], $a['pk_i_id'], $a['s_punctuation'], (($kf_status === 'disabled') ? true : false)); ?>
                    </li>
                    <?php } ?>
                    <?php if($num_questions==0) {
                        _e('Open question by default', 'jobboard');
                    } ?>
                </ol>
            </div>
            <?php }  ?>
        </div>
    </div>
    <?php } } ?>
    <div id="dialog-question-delete" title="<?php echo osc_esc_html(__('Delete question', 'jobboard')); ?>" class="has-form-actions hide" data-killerform-id="<?php echo $killer_form_id; ?>" data-question-id="">
        <div class="form-horizontal">
            <div class="form-row">
                <?php _e('Are you sure you want to delete this question?', 'jobboard'); ?><br/>
                <?php _e('Answers will be deleted too', 'jobboard'); ?>
            </div>
            <div class="form-actions">
                <div class="wrapper">
                    <a class="btn" href="javascript:void(0);" onclick="$('#dialog-question-delete').dialog('close');"><?php _e('Cancel', 'jobboard'); ?></a>
                    <a id="question-delete-submit" class="btn btn-red" href="javascript:void(0);" ><?php echo osc_esc_html( __('Delete', 'jobboard') ); ?></a>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="float:right;">
    <?php if( $kf_status !== 'disabled' ) { ?> <a class="btn btn-mini" onclick="addQuestion(); return false;"><?php _e('Add new question', 'jobboard'); ?></a><?php } ?>
</div>
<script type="text/javascript">
    triggerKillerFormCreation();
</script>