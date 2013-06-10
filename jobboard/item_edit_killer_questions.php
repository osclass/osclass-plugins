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
    } else {
        
        $killerQuestions = array();
        foreach($detail['array_killer_questions'] as $key => $value) {
            $array = array();
            $array['e_type']  = 'OPEN';
            if(empty($value['answer'])) {
                $array['e_type']  = 'CLOSED';
            }

            $array['s_text']     = $value['question'];
            $array['a_answers']  = $value['answer'];
            $killerQuestions['questions'][$key] = $array;
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
    <?php //if( $kf_status !== 'new' ) {
        foreach($killerQuestions['questions'] as $key => $q) { ?>
    <?php if($kf_status !== 'new') { ?>
    <div id="question_<?php echo $q['pk_i_id']; ?>" data-id="<?php echo $q['pk_i_id']; ?>" class="new_question">
    <?php } else { ?>
    <div id="new_question_<?php echo $key; ?>" data-id="<?php echo $key; ?>" class="new_question">
    <?php } ?>
        <label><?php printf(__('Question %1$s', 'jobboard'), $key); ?></label>
        <?php
            $hide = array('add' => 'style="display:none"', 'remove' => '');
            $hasAnswers = false;
            if($q['a_answers'] === false || count($q['a_answers']) == 0) {
                $hide['add']    = '';
                $hide['remove'] = 'style="display:none"';
            } else {
                $hasAnswers = true;
            }
        ?>
        <?php if($kf_status !== 'new') { ?>
            <input <?php if( $kf_status === 'disabled' ) { ?>disabled="disabled"<?php } ?> class="input-large question_input" type="text" name="question[<?php echo $q['pk_i_id']; ?>][question]" value="<?php echo osc_esc_html($q['s_text']); ?>"/>
        <?php } else { ?>
            <a class="add-remove-btn btn btn-mini btn-red" onclick="removeQuestion($(this));return false;"><?php _e('Remove question', 'jobboard'); ?></a>
            <input class="input-large question_input" type="text" name="new_question[<?php echo $key;?>][question]" value="<?php echo osc_esc_html($q['s_text']); ?>"/>
        <?php } ?>
        <!--añadir addAnswer removeAnswer-->
        <div class="containerAnswers">
            <?php if( $hasAnswers ) { ?>
            <a class="addAnswers add-remove-btn btn btn-mini" style="display: none;"><?php _e('Add answers', 'jobboard'); ?></a>
            <?php } else { ?>
            <a class="addAnswers add-remove-btn btn btn-mini"><?php _e('Add answers', 'jobboard'); ?></a>
            <?php } ?>

            <div class="containerAnswersReplace">
            <?php if( $hasAnswers ) { ?>
                <ol>
                    <?php
                    $num_questions = count($q['a_answers']);
                    foreach($q['a_answers'] as $key_ => $a) {
                    ?>
                    <li>
                        <?php if($kf_status !== 'new') { ?>
                        <input <?php if( $kf_status === 'disabled' ) { ?>disabled="disabled"<?php } ?> class="input-large" type="text" name="question[<?php echo $q['pk_i_id'];?>][answer][<?php echo $a['pk_i_id'];?>]" value="<?php echo osc_esc_html($a['s_text']);?>"/><?php _punctuationSelect_update($q['pk_i_id'], $a['pk_i_id'], $a['s_punctuation'], (($kf_status === 'disabled') ? true : false)); ?>
                        <?php } else { ?>
                        <a class="delete_answer jb_tooltip" onclick="clearAnswer($(this)); return false;" title="Remove this answer"></a>
                        <input class="input-large" type="text" name="new_question[<?php echo $key; ?>][answer][<?php echo $key_;?>]" value="<?php echo osc_esc_html($a['text']);?>"/><?php _punctuationSelect_insert($key, $key_, $a['punct'], (($kf_status === 'disabled') ? true : false)); ?>
                        <?php } ?>
                    </li>
                    <?php } ?>
                    <?php
                    $max_answers = osc_get_preference('max_answers', 'jobboard_plugin');
                    $i = count($q['a_answers']); // 5 -2 = 3
                    if($i!=0 && $kf_status === 'new') {  // no open question
                        $i++;    // next answer
                        for($i; $i <= $max_answers; $i++ ) {
                    ?>
                    <li>
                        <a class="delete_answer jb_tooltip" onclick="clearAnswer($(this)); return false;" title="Remove this answer"></a>
                        <input class="input-large" type="text" name="new_question[<?php echo $key; ?>][answer][<?php echo $i;?>]" value=""/><?php _punctuationSelect_insert($key, $i, '', (($kf_status === 'disabled') ? true : false)); ?>
                    </li>
                    <?php
                        }

                        } ?>

                    <?php if($num_questions==0) {
                        _e('Open question by default', 'jobboard');
                    } ?>
                </ol>
            <?php } else {  ?>
            <span class="offset">Open question by default</span>
            <?php } ?>
            </div>
        </div>
    </div>
    <?php }
    // } ?>
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