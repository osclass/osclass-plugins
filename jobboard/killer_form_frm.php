<?php
if(!osc_is_admin_user_logged_in()) {
    die;
}
// get plugin action
$pAction    = Params::getParam('paction');
$aQuestions = array();
$title      = Params::getParam('title');

if($pAction=='add_form') {  // INSERT NEW
    if($title!='') {
        $killerFormId = ModelKQ::newInstance()->insertKillerForm($title);
        if($killerFormId!==false) {
            $str_message = __('Form title saved correctly', 'jobboard');
            // insert answers...
            $array_new  = getParamsKillerForm_insert();
            $result     = _insertKillerQuestions($killerFormId, $array_new);
            if($result===false) { // error
                //
                // save data to SESSION
                //
                job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php&id=".$killerFormId));
            } else { // no error
                job_js_redirect_to(osc_admin_render_plugin_url("jobboard/manage_killer.php"));
            }
        } else {
            //
            // save data to SESSION
            //
            osc_add_flash_message(__('Error adding Killer question form', 'jobboard'), 'admin');
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php"));
        }
    } else {
        // add flash message - title cannot be null
        osc_add_flash_message(__('Form title cannot be empty', 'jobboard'), 'admin');
        job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php"));
    }
} else if($pAction=='edit_form') {  // EDIT EXISTENT
    // update killer questions form
    $killerFormId = Params::getParam("id");
    if($title!='') {
        $res = ModelKQ::newInstance()->updateKillerForm($killerFormId, $title);
        if($res===false) {
            // add flash message error updating killer form title!
            osc_add_flash_message(__('Error updating Killer question form', 'jobboard'), 'admin');
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php"));
        }
        // add new questions ---------------------------------------------------
        $array_new  = getParamsKillerForm_insert();
        $resAdd     = _insertKillerQuestions($killerFormId, $array_new);
        if($resAdd===false) {
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php&id=".$killerFormId));
        }
        // and update existing questions ---------------------------------------
        $array_update = getParamsKillerForm_update();
        $resEdit      = _updateKillerQuestions($killerFormId, $array_update);
        if($resEdit===false) {
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php&id=".$killerFormId));
        }
        job_js_redirect_to(osc_admin_render_plugin_url("jobboard/manage_killer.php"));
    } else {
        // add flash message - title cannot be null
        osc_add_flash_message(__('Form title cannot be empty', 'jobboard'), 'admin');
        job_js_redirect_to(osc_admin_render_plugin_url("jobboard/killer_form_frm.php&id=".$killerFormId));
    }
}

$aKillerForm        = array();
$new_killer_form    = true;
$button             = __('Add from', 'jobboard');
$h2                 = __('Add Questions' ,'jobboard');
$killer_form_id     = Params::getParam("id");

if($killer_form_id!='') {
    $aKillerForm = ModelKQ::newInstance()->getKillerForm($killer_form_id);
    if(is_array($aKillerForm) && !empty($aKillerForm)) {
        // get killer form information ...
        $killerQuestions = ModelKQ::newInstance()->getKillerQuestions($killer_form_id);
        $new_killer_form = false;
        $button = __('Update from', 'jobboard');
        $h2     = __('Update Questions' ,'jobboard');
    }
}

?>
<form id="killerquestionsForm" action="<?php echo osc_admin_base_url(true); ?>" method="post">
    <input type="hidden" name="page"    value="plugins">
    <input type="hidden" name="action"  value="renderplugin">
    <input type="hidden" name="file"    value="jobboard/killer_form_frm.php">
    <?php if($new_killer_form){ ?>
    <input type="hidden" name="paction"  value="add_form">
    <?php } else { ?>
    <input type="hidden" name="paction"  value="edit_form">
    <input type="hidden" name="id" value="<?php echo $killer_form_id; ?>" />
    <?php } ?>

    <label><?php _e('Title', 'jobboard'); ?></label>
    <div>
        <input type="text" class="input-large" name="title" value="<?php echo @$aKillerForm['s_title'];?>" />
    </div>

    <br/>

    <h2 class="render-title"><?php echo $h2; ?> <a class="btn btn-mini" onclick="addQuestion();return false;"><?php _e('Add new question', 'jobboard'); ?></a></h2>

    <div id="killerquestions">
        <?php if(!$new_killer_form) {
            $aux_questions = @$killerQuestions['questions'];
            if(isset($aux_questions) && is_array($aux_questions)) {
                foreach(@$killerQuestions['questions'] as $key => $q) {
        ?>
        <div id="question_<?php echo $q['pk_i_id'];?>" data-id="<?php echo $q['pk_i_id'];?>" class="question">
            <label><?php _e('Question', 'jobboard'); ?> <?php echo $key;?></label>
            <a class="delete_question" data-question-id="<?php echo $q['pk_i_id'];?>"></a>
            <input class="input-large question_input" type="text" name="question[<?php echo $q['pk_i_id']; ?>][question]" value="<?php echo osc_esc_html($q['s_text']);?>"/>
            <?php if($q['a_answers']===false){ ?>
            <a class="addAnswers btn btn-mini" onclick="addAnswers($(this));return false;"><?php _e('Add answers', 'jobboard'); ?></a>
            <?php } else {
                $num_questions = count($q['a_answers']);
                ?>
            <a class="removeAnswers btn btn-mini" onclick="removeAnswers($(this));return false;"><?php _e('Remove answers', 'jobboard'); ?></a>
            <p><?php _e('Answer', 'jobboard'); ?>
                <ol>
                    <?php foreach($q['a_answers'] as $key_ => $a){ ?>
                    <li>
                        <a class="delete_answer" onclick="clearAnswer($(this)); return false;"></a>
                        <input class="input-large" type="text" name="question[<?php echo $q['pk_i_id'];?>][answer][<?php echo $a['pk_i_id'];?>]" value="<?php echo osc_esc_html($a['s_text']);?>"/>
                        <?php _punctuationSelect_update($q['pk_i_id'], $a['pk_i_id'], $a['s_punctuation']); ?>
                    </li>
                    <?php }
                    $max_questions = osc_get_preference('max_answers', 'jobboard_plugin');
                    $aux = $num_questions+1; // next answer
                    for($aux; $aux <= $max_questions; $aux++){ ?>
                    <li>
                        <a class="delete_answer" onclick="clearAnswer($(this)); return false;"></a>
                        <input class="input-large" type="text" name="question[<?php echo $q['pk_i_id'];?>][new_answer][<?php echo $aux;?>]" />
                        <?php _punctuationSelect_insert($q['pk_i_id'], $aux); ?>
                    </li>
                    <?php } ?>
                </ol>
            </p>
            <?php }  ?>
        </div>
        <?php } } } ?>
    </div>
    <input type="submit" class="btn submit-right submit" value="<?php echo osc_esc_html( $button ); ?>">
</form>
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