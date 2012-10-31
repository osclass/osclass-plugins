/*
 * addQuestion, add base html structure for add a question.
 */
function addQuestion() {
    var newquestionNumber = $('#killerquestions div.new_question').length;
    var oldquestionNumber = $('#killerquestions div.question').length;
    var questionNumber    = newquestionNumber+oldquestionNumber+1;

    if(questionNumber <= jobboard.max_killer_questions) {
        var question        = $('<div id="new_question_'+questionNumber+'" data-id="'+questionNumber+'" class="new_question">')
        var removeQuestion  = $('<a class="delete_question_ico" onclick="removeQuestion($(this));return false;"></a> ');
        var label           = $('<label>'+jobboard.langs.question+' '+questionNumber+' </label> ');
        var input           = $('<input class="input-large question_input" type="text" name="new_question['+questionNumber+'][question]"/>');
        question.append(label);
        question.append(removeQuestion);
        question.append(input);

        var insertAnswersLink = $('<a class="addAnswers btn btn-mini" onclick="addAnswers($(this));return false;"> '+jobboard.langs.insertAnswersLink+'</a>');
        $(question).append(insertAnswersLink);
        $('#killerquestions').append( question );
    }
}

/**
 * removeQuestion, this function only remove questions that aren't in the system,
 * only remove questions recently added and not saved yet
 */
function removeQuestion(element) {
    var questionId = $(element).parent().attr('data-id');
    $('div#new_question_'+questionId).fadeOut('slow', function(){
        $('div#new_question_'+questionId).remove();
        // reorganize questions, set order
        $('div.new_question,div.question').each( function(index) {
            var i = index+1;
            $(this).attr('id', 'new_question_'+i);
            $(this).attr('data-id', i);
            $(this).find('label').html(jobboard.langs.question+' '+i);
        });
    });
}

/**
 * addAnswers, add base html structure for add answers to a given question,
 * this functions make a closed question type.
 */
function addAnswers(element) {
    var questionId = $(element).parent().attr('data-id');
    var name     = 'new_question';
    var answer   = 'answer';
    var punct    = 'answer_punct';
    var question = $('#new_question_'+questionId);
    if(question.length==0) {
        question = $('#question_'+questionId);
        name    = 'question';
        answer  = 'new_answer';
        punct   = 'new_answer_punct';
    }
    // question exist
    if($(question).length==1) {
        // if there are no questions
        var list_answers = $(this).find('ol').length;
        if(list_answers==0) {
            var select = $('<select name="_to_change_">');
            select.append( $('<option>').attr('value', '').html(jobboard.langs.punctuation) );
            for(var i=10;i>=1;i--) {
                select.append( $('<option>').attr('value', i).html(i) );
            }
            select.append( $('<option>').attr('value', 'reject').html(jobboard.langs.reject) );

            var containerAnswers  = $('<p>');
            containerAnswers.html(jobboard.langs.answer);
            var answers      = $('<ol>');
            for(i=0;i<5;i++) {
                var _select = $(select).clone(); // ¿¿bien alguna manera mejor??  FERNANDO??
                var _i = i+1;
                var listAnswer = $('<li>');
                var _deleteAnswer = $('<a class="delete_answer" onclick="clearAnswer($(this)); return false;"></a>');
                var _input = $('<input class="input-large" type="text" name="'+name+'['+questionId+']['+answer+']['+_i+']"/>');
                $(_select).attr('name', name+'['+questionId+']['+punct+']['+_i+']');

                listAnswer.append(_deleteAnswer);
                listAnswer.append(_input);
                listAnswer.append(_select);

                $(answers).append(listAnswer);
            }
            $(containerAnswers).append(answers);
            $(question).append(containerAnswers);
            // remove 'add answers' link and add 'remove answers'
            $('#'+name+'_'+questionId+' a.addAnswers').remove();
            var removeAnswersLink = $('<a class="removeAnswers btn btn-mini" onclick="removeAnswers($(this));return false;">'+jobboard.langs.removeAnswersLink+'</a>');
            $('#'+name+'_'+questionId+' input.question_input').after(removeAnswersLink);

        } else {
            // show flash message
            // you have questions now, 'you have already answers for this question'
//            alert('you have already answers for this question');  // REMOVE
        }
    } else {
        // show error message
        // 'You cannot add more questions, the maximum number of questions is '+questionNumber
//        alert('cannot add more answers');  // REMOVE
    }
}


/**
 * removeAnswers, remove answers belonging to a question
 */
function removeAnswers(element) {
    var questionId = $(element).parent().attr('data-id');

    var name     = 'new_question';
    var answer   = 'answer';
    var punct    = 'answer_punct';
    var question = $('#new_question_'+questionId);
    if(question.length==0) {
        question = $('#question_'+questionId);
        name    = 'question';
        answer  = 'new_answer';
        punct   = 'new_answer_punct';
    }

    $('#'+name+'_'+questionId+' p').remove();
    $('#'+name+'_'+questionId+' ol').remove();
    // remove 'remove answers' link and add 'add answers'
    $('#'+name+'_'+questionId+' a.removeAnswers').remove();
    var addAnswersLink = $('<a class="addAnswers btn btn-mini" onclick="addAnswers($(this));return false;">'+jobboard.langs.insertAnswersLink+'</a>');
    $('#'+name+'_'+questionId+' input.question_input').after(addAnswersLink);
}

/**
 * clearAnswer, clear content and punctuation of an answer
 */
function clearAnswer(element) {
    var answer_container = $(element).parent();
    $(answer_container).find('input').attr('value', '');
    var select = $(answer_container).find('select');
    $(select).find('option').removeAttr('selected');
    $(select).find('option[value=""]').attr('selected','selected');
    $(select).triggerHandler('change');
}

$(document).ready(function() {

    // validate form
    $("form#datatablesForm").validate({
        rules: {
            title: { required: true }
        },
        messages: {
            title: { required: jobboard.langs.title_msg_required }
        },
        errorLabelContainer: "#error_list",
        wrapper: "li",
        invalidHandler: function(form, validator) {
            $('html,body').animate({scrollTop: $('h1').offset().top}, {duration: 250, easing: 'swing'});
        }
    });


    // delete question
    // ( used when a existent question is removed from the system )
    $("#dialog-question-delete").dialog({
        autoOpen: false,
        modal: true
    });

    $('.delete_question').live('click', function(){
        $("#dialog-question-delete").attr('data-question-id', $(this).attr('data-question-id'));
        $("#dialog-question-delete").dialog('open');
    });

    $('#question-delete-submit').bind('click', function(){
        if($("#dialog-question-delete").attr('data-killerform-id')!='') {
            $.getJSON(jobboard.ajax_question_delete,
                {
                    'questionId'    : $("#dialog-question-delete").attr('data-question-id'),
                    'killerFormId'  : $("#dialog-question-delete").attr('data-killerform-id')
                },
                function(data) {
                    if(data=='1') {
                        var questionId = $("#dialog-question-delete").attr('data-question-id');
                        // question_[ID] -> question_ means existent questions
                        $('div#question_'+questionId).fadeOut('slow', function(){
                            $('div#question_'+questionId).remove();
                            // reorganize questions, set order
                            $('div.new_question,div.question').each( function(index) {
                                var i = index+1;
                                $(this).attr('id', 'new_question_'+i);
                                $(this).attr('data-id', i);
                                $(this).find('label').html(jobboard.langs.question+' '+i);
                            });
                        });
                    } else {
                        // show javascript flash message
                        // error deleting question
                    }
                    $("#dialog-question-delete").dialog('close');
                }
            );
        }
    });
});