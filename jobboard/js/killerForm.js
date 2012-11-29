/*
 * addQuestion, add base html structure for add a question.
 */
function addQuestion() {
    var newquestionNumber = $('#killerquestions div.new_question').length;
    var oldquestionNumber = $('#killerquestions div.question').length;
    var questionNumber    = newquestionNumber+oldquestionNumber+1;

    if(questionNumber <= jobboard.max_killer_questions) {
        var question        = $('<div id="new_question_'+questionNumber+'" data-id="'+questionNumber+'" class="new_question">')
        var removeQuestion  = $('<a class="add-remove-btn btn btn-mini btn-red" onclick="removeQuestion($(this));return false;">'+jobboard.langs.removeQuestionLink+'</a> ');
        var label           = $('<label>'+jobboard.langs.question+' '+questionNumber+' </label> ');
        var input           = $('<input class="input-large question_input valid_question rangelength" valid_question="'+jobboard.langs.question+' '+questionNumber+'"  type="text" name="new_question['+questionNumber+'][question]"/>');
        /////////////
        var containerAnswers  = $('<div class="containerAnswers"></div>');
        ////////////
        var insertAnswersLink = $('<a class="addAnswers add-remove-btn btn btn-mini"> '+jobboard.langs.insertAnswersLink+'</a>');
        var removeAnswersLink = $('<a class="removeAnswers add-remove-btn btn btn-mini btn-red">'+jobboard.langs.removeAnswersLink+'</a>');
        var containerAnswersReplace = '<div class="containerAnswersReplace"><span class="offset">'+jobboard.langs.openquestion+'</span></div>';

        insertAnswersLink.click(function(){
            insertAnswersLink.hide();
            removeAnswersLink.show();
            addAnswers($(this));
            return false;
        });

        removeAnswersLink.click(function(){
            insertAnswersLink.show();
            removeAnswersLink.hide();
            removeAnswers($(this));
            return false;
        }).hide();

        question.append(label);
        //question.append(insertAnswersLink).append(removeAnswersLink);
        question.append(removeQuestion);
        question.append(input);
            containerAnswers.append(insertAnswersLink).append(removeAnswersLink);
            containerAnswers.append(containerAnswersReplace);
        question.append(containerAnswers);
        $('#killerquestions').append( question );

        // add validation rule question
        $('#new_question_'+questionNumber+' input.question_input').rules("add", {valid_closed_question: [jobboard.langs.question+' '+questionNumber, 2]}) ;
    }
}

/**
 * removeQuestion, this function only remove questions that aren't in the system,
 * only remove questions recently added and not saved yet
 */
function removeQuestion(element) {
    var questionId = $(element).parents('.new_question').attr('data-id');
    $('div#new_question_'+questionId).fadeOut('slow', function(){
        // remove validation rule
        $('div.new_question').each( function(index) {
            var i = index+1;
            // add validate rules questions
            $(this).find('input.question_input').rules("remove", 'valid_closed_question') ;
        });

        // remove question element
        $('div#new_question_'+questionId).remove();
        // reorganize questions, set order
        $('div.new_question,div.question').each( function(index) {
            var i = index+1;
            $(this).attr('id', 'new_question_'+i);
            $(this).attr('data-id', i);
            $(this).find('label').html(jobboard.langs.question+' '+i);
            $(this).find('input.question_input').attr('valid_question', jobboard.langs.question+' '+i);
        });

        $('div.new_question').each( function(index) {
            var i = index+1;
            // add validate rules questions
            $(this).find('input.question_input').rules("remove", 'valid_closed_question') ;
            $(this).find('input.question_input').rules("add", {valid_closed_question: [jobboard.langs.question+' '+i, 2]}) ;
        });

        // remove errors
        $('#error_list').html('');
        if( $(".question_input").length == 0 ) {
            $('#error_list').hide();
        }
        $(".question_input").each( function(index) {
            $(this).valid();
        });
    });
}

/**
 * addAnswers, add base html structure for add answers to a given question,
 * this functions make a closed question type.
 */
function createSelectAnswer(){
    var select = $('<select name="_to_change_">');
    select.append( $('<option>').attr('value', '').html(jobboard.langs.punctuation) );
    for(var i=10;i>=1;i--) {
        select.append( $('<option>').attr('value', i).html(i) );
    }
    select.append( $('<option>').attr('value', 'reject').html(jobboard.langs.reject) );
    return select;
}
function addAnswers(element) {
    var questionId = $(element).parents('.new_question').attr('data-id');
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
            var select = createSelectAnswer();

            var containerAnswers  = $('<div class="containerAnswersReplace">');
            //containerAnswers.html(jobboard.langs.answer);
            var answers      = $('<ol>');
            for(i=0;i<5;i++) {
                var _select = createSelectAnswer();
                var _i = i+1;
                var listAnswer = $('<li>');
                var _deleteAnswer = $('<a class="delete_answer" onclick="clearAnswer($(this)); return false;"></a>');
                var _input = $('<input class="input-large" type="text" name="'+name+'['+questionId+']['+answer+']['+_i+']"/>');
                $(_select).attr('name', name+'['+questionId+']['+punct+']['+_i+']');

                //listAnswer.append(_deleteAnswer);
                listAnswer.append(_input);
                listAnswer.append(_select);

                $(answers).append(listAnswer);
            }
            $(containerAnswers).append(answers);
            $(question).find('.containerAnswersReplace').replaceWith(containerAnswers);

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
    var parent     = $(element).parents('.new_question');
    var questionId = $(parent).attr('data-id');

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

    $('#'+name+'_'+questionId+' .containerAnswersReplace').replaceWith('<div class="containerAnswersReplace">'+jobboard.langs.openquestion+'</div>');
    //$('#'+name+'_'+questionId+' ol').remove();

    // run validate
    $(parent).find('input').valid();
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
function triggerKillerFormCreation(){
    $('.new_question').each(function(){
        var insertAnswersLink = $(this).find('.addAnswers');
        var removeAnswersLink = $(this).find('.removeAnswers');
            insertAnswersLink.click(function(){
                insertAnswersLink.hide();
                removeAnswersLink.show();
                addAnswers($(this));
                return false;
            });
            removeAnswersLink.click(function(){
                insertAnswersLink.show();
                removeAnswersLink.hide();
                removeAnswers($(this));
                return false;
            });
    });
}
$(document).ready(function() {

    // validate form
    window.killerValidator = $("form#datatablesForm, form#killerquestionsForm").validate({
        rules: {
            title: {required: true}
        },
        messages: {
            title: {required: jobboard.langs.title_msg_required}
        },
        errorLabelContainer: "#error_list",
        wrapper: "li",
        invalidHandler: function(form, validator) {
            $('html,body').animate({scrollTop: $('h1').offset().top}, {duration: 250, easing: 'swing'});
        }
    });

    // Validate description without HTML.
    jQuery.validator.setDefaults({
        focusInvalid: false
    });

    function addErrorStyle(element)
    {
        $(element).addClass('border_error');
    }

    $.validator.addMethod(
        "valid_question",
        function(value, element, params) {
            return validate_question(element);
        },
        $.validator.format("{0} cannot be empty")
    );

    function validate_question(input_question)
    {
        var element = $(input_question).parent();
        $(element).find('.border_error').removeClass('border_error');

        // text required, not empty
        if($(input_question).attr('value')=='') {
            addErrorStyle( input_question );
            return false;
        }
        return true;
    }

    $.validator.addMethod(
        "valid_closed_question",

        function(value, element, params) {
            return validate_closed_question(element, eval(params));
        },

        $.validator.format("{0}, at least needs {1} answers")
    );

    function validate_closed_question(input_question, params)
    {
        var element = $(input_question).parent();
        $(element).find('.border_error').removeClass('border_error');
        // open or closed question ?
        if($(element).find('div.containerAnswersReplace input').length > 0) {
            var numAnswers  = 0;
            var closedQuestionInvalid = 0;
            $(element).find('div.containerAnswersReplace input').each( function(i, e) {
                var name = $(e).attr('name');
                if($(e).attr('value')!=''){
                    var punctuation = $(e).parent().find('select option:selected').attr('value');
                    if(punctuation=='') {
                        addErrorStyle( $(e).parent().find('select') );
                        closedQuestionInvalid++;
                    } else {
                        numAnswers++;
                    }
                }
            });

            if(closedQuestionInvalid>0 || numAnswers<params[1]) {
                // highlight errors
                return false;
            }
        }
        return true;
    }

    // delete question
    // ( used when a existent question is removed from the system )
    $('.delete_question').live('click', function(){
        $("#dialog-question-delete").dialog({
            autoOpen: false,
            modal: true
        });
        $("#dialog-question-delete").attr('data-question-id', $(this).attr('data-question-id'));
        $("#dialog-question-delete").dialog('open');
    });

    $('#question-delete-submit').live('click', function(){
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