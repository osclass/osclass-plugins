<?php
$new_killer_form    = true;
$killerQuestions    = array();
?>


<h2 class="render-title"><?php _e('Add Questions' ,'jobboard'); ?> <a class="btn btn-mini" onclick="addQuestion();return false;"><?php _e('Add new question', 'jobboard'); ?></a></h2>

<div id="killerquestions">
    <?php if(!$new_killer_form) { foreach($killerQuestions['questions'] as $key => $q) { ?>
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
    <?php } } ?>
</div>





<script type="text/javascript">
    $('.killer-form-select select').each(function() {
        selectUi($(this));
    });
</script>