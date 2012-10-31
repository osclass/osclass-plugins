<div class="killer_questions">
    <input type="hidden" name="killerFormId" value="<?php echo $job['fk_i_killer_form_id']; ?>"/>
    <hr/>
    <?php foreach($aQuestions['questions'] as $q) { ?>
    <input type="hidden" name="questionsId[]" value="<?php echo $q['pk_i_id']; ?>"/>
    <label><?php echo $q['s_text']; ?></label>
    <div>
    <?php if($q['e_type']=='CLOSED') { ?>
        <?php foreach($q['a_answers'] as $a) { ?>
        <label>
            <?php
                $selected = false;
                if( Session::newInstance()->_getForm("question[".$q['pk_i_id']."]") == $a['pk_i_id'] ) {
                    $selected = true;
                }
            ?>
            <input class="input_answer required" <?php if($selected) { echo 'checked="checked"';} ?> type="radio" name="question[<?php echo $a['fk_i_question_id']; ?>]" value="<?php echo $a['pk_i_id']; ?>"/><?php echo $a['s_text'];?>
        </label>
        <?php } ?>
    <?php   } else { ?>
        <?php
            $text = '';
            if( Session::newInstance()->_getForm("question[".$q['pk_i_id']."]") != '' ) {
                $text = Session::newInstance()->_getForm("question[".$q['pk_i_id']."]");
            }
        ?>
        <textarea class="required" name="question[<?php echo $q['pk_i_id']; ?>][open]"><?php echo osc_esc_html($text); ?></textarea>
    <?php   } ?>
    </div>
    <hr/>
<?php } ?>
</div>