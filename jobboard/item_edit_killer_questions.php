<?php
$killer_form_id     = null;
$new_killer_form    = true;
$killerQuestions    = array();

// killer form exist ...
if( is_numeric(@$detail['fk_i_killer_form_id']) ) {
    $killer_form_id = @$detail['fk_i_killer_form_id'];

    $aKillerForm = ModelKQ::newInstance()->getKillerForm($killer_form_id);
    if(is_array($aKillerForm) && !empty($aKillerForm)) {
        // get killer form information ...
        $killerQuestions = ModelKQ::newInstance()->getKillerQuestions($killer_form_id);
        $new_killer_form = false;
    }
}

// load existent killer forms
$aAllKillerForm = ModelKQ::newInstance()->getAllKillerForm();
?>
<h2 class="render-title separate-top"><?php _e('Killer Questions' ,'jobboard'); ?>
<?php if( !isset($itemID) ) { ?> <a class="btn btn-mini" onclick="addQuestion();return false;"><?php _e('Add new question', 'jobboard'); ?></a><?php } ?>
</h2>
<?php if( $new_killer_form ) { ?>
<div class="grid-first-row grid-100">
    <div class="flashmessage-dashboard-jobboard">
        <div class="flashmessage flashmessage-inline">
            <?php _e("Killer question form note: Once you create it you won't be able to update or remove it without deleting vacancy itself", 'jobboard'); ?><a class="btn ico btn-mini ico-close">x</a>
        </div>
    </div>
</div>
<?php } ?>
<div id="killerquestions">
    <?php if(!$new_killer_form) { foreach($killerQuestions['questions'] as $key => $q) { ?>
    <div id="question_<?php echo $q['pk_i_id'];?>" data-id="<?php echo $q['pk_i_id'];?>" class="new_question">
        <label><?php _e('Question', 'jobboard'); ?> <?php echo $key;?></label>
        <?php
            $hide = array('add'=>'style="display:none"','remove'=>'');
            $hasAnswers = false;
            if($q['a_answers']===false){
                $hide['add']    = '';
                $hide['remove'] = 'style="display:none"';
            } else {
                $hasAnswers = true;
            }
/*             <a class="addAnswers btn btn-mini add-remove-btn" <?php echo $hide['add']; ?>><?php _e('Add answers', 'jobboard'); ?></a>
 *             <a class="removeAnswers add-remove-btn btn btn-mini" <?php echo $hide['remove']; ?>><?php _e('Remove answers', 'jobboard'); ?></a>
 *             <a class="delete_question" data-question-id="<?php echo $q['pk_i_id'];?>"></a>
 */
        ?>
        <input disabled="disabled" class="input-large question_input" type="text" name="question[<?php echo $q['pk_i_id']; ?>][question]" value="<?php echo osc_esc_html($q['s_text']);?>"/>
        <?php if($hasAnswers){ ?>
        <div class="containerAnswers">
            <?php _e('Answer', 'jobboard'); ?>
            <ol>
                <?php
                $num_questions = count($q['a_answers']);
                foreach($q['a_answers'] as $key_ => $a){ ?>
                <li>
                    <?php /* <a class="delete_answer" onclick="clearAnswer($(this)); return false;"></a> */ ?>
                    <input disabled="disabled" class="input-large" type="text" name="question[<?php echo $q['pk_i_id'];?>][answer][<?php echo $a['pk_i_id'];?>]" value="<?php echo osc_esc_html($a['s_text']);?>"/>
                    <?php _punctuationSelect_update($q['pk_i_id'], $a['pk_i_id'], $a['s_punctuation'], true); ?>
                </li>
                <?php }
                $max_questions = osc_get_preference('max_answers', 'jobboard_plugin');
                $aux = $num_questions+1; // next answer
                for($aux; $aux <= $max_questions; $aux++){ ?>
                <li>
                    <?php
                   /* <a class="delete_answer" onclick="clearAnswer($(this)); return false;"></a> */
                    ?>
                    <input disabled="disabled" class="input-large" type="text" name="question[<?php echo $q['pk_i_id'];?>][new_answer][<?php echo $aux;?>]" />
                    <?php _punctuationSelect_insert($q['pk_i_id'], $aux, '', true); ?>
                </li>
                <?php } ?>
            </ol>
        </div>
        <?php }  ?>
    </div>
    <?php } } ?>
    <div id="dialog-question-delete" title="<?php echo osc_esc_html(__('Delete question', 'jobboard')); ?>" class="has-form-actions hide" data-killerform-id="<?php echo $killer_form_id;?>" data-question-id="">
        <div class="form-horizontal">
            <div class="form-row">
                <?php _e('Are you sure you want to delete this question?', 'jobboard'); ?><br/>
                <?php _e('Answers will be delete too', 'jobboard'); ?>
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

<script type="text/javascript">
    triggerKillerFormCreation();
    $('.killer-form-select select').each(function() {
        selectUi($(this));
    });
</script>