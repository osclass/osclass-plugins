function setIcon(){
    $('.status-icon').css({
            backgroundPosition: $("#applicant_status").val() * 60
    });
}

$(document).ready(function() {
    // notes
    $("#dialog-note-delete").dialog({
        autoOpen: false,
        modal: true
    });
    $("#dialog-note-form").dialog({
        autoOpen: false,
        modal: true
    });

    $('.edit_note').live('click', function(){
        $("#dialog-note-form").attr('data-note-action', 'edit');
        $("#dialog-note-form").attr('data-note-id', $(this).attr('data-note-id'));
        $("#note_edit_text").attr('value', $(this).attr('data-note-text'));
        $("#dialog-note-form").dialog('open');
    });

    $('.add_note').live('click', function(){
        $("#dialog-note-form").attr('data-note-action', 'add');
        $("#dialog-note-form").attr('data-note-id', '');
        $("#note_edit_text").attr('value', '');
        $("#dialog-note-form").dialog('open');
    });

    $('.delete_note').live('click', function(){
        $("#dialog-note-delete").attr('data-note-id', $(this).attr('data-note-id'));
        $("#dialog-note-delete").dialog('open');
    });

    $('#note-delete-submit').bind('click', function(){
        $.getJSON(osc.jobboard.ajax_note_delete,
            {
                'noteID': $("#dialog-note-delete").attr('data-note-id')
            },
            function(data) {
                $('.delete_note[data-note-id="' + $("#dialog-note-delete").attr('data-note-id') + '"]').parents('.note').remove();
                var note_container = $('<div>').attr('class', 'note empty-note well ui-rounded-corners').append($('<p>').html(osc.jobboard.langs.empty_note_text));
                $('#nots_table_div').append(note_container);
                $(note_container).effect("highlight", {}, 500);
                $("#dialog-note-delete").dialog('close');
            }
        );
    });

    $('#note-form-submit').bind('click', function(){
        var ajax_url = '';
        if( $("#dialog-note-form").attr('data-note-action') == 'add') {
            ajax_url = osc.jobboard.ajax_note_add;
        } else {
            ajax_url = osc.jobboard.ajax_note_edit;
        }
        $.getJSON(ajax_url,
            {
                'applicantID': $("#dialog-note-form").attr('data-applicant-id'),
                'noteID': $("#dialog-note-form").attr('data-note-id'),
                'noteText' : $("#note_edit_text").val()
            },
            function(data) {
                if( $("#dialog-note-form").attr('data-note-action') == 'add') {
                    var note_container = $('<div>').attr('class', 'note well ui-rounded-corners');
                    var note_actions = $('<div>').attr('class', 'note-actions');
                    var delete_note = $('<a>').attr('class', 'delete_note').attr('href', 'javascript:void(0);').attr('data-note-id', data.pk_i_id).html(osc.jobboard.langs.delete_string);
                    var edit_note = $('<a>').attr('class', 'edit_note').attr('href', 'javascript:void(0);').attr('data-note-id', data.pk_i_id).attr('data-note-text', data.s_text).html(osc.jobboard.langs.edit_string);
                    var date_note = $('<div>').attr('class', 'note-date').append($('<b>').html(data.day)).append($('<span>').html(data.month + '<br/>' + data.year));
                    var clear_div = $('<div>').attr('class', 'clear');
                    var note_text = $('<p>').attr('class', 'note_text').html(data.s_text.replace(/\n/g, '<br/>'));

                    $(note_container).append($(note_actions).append(delete_note).append(edit_note)).append(date_note).append(clear_div).append(note_text);
                    $('#nots_table_div').prepend(note_container);
                    $('.empty-note').remove();
                    $(note_container).effect("highlight", {}, 500);
                } else {
                    var note = $('.delete_note[data-note-id="' + $("#dialog-note-form").attr('data-note-id') + '"]').parents('.note');
                    $(note).children('.edit_note').attr('data-note-text', data.s_text);
                    $(note).children('.note_text').html(data.s_text);
                    $(note).effect("highlight", {}, 500);
                }
                $('#dialog-note-form').dialog('close');
            }
        );
    });
    // /notes

    $("#dialog-applicant-status").dialog({
        autoOpen: false,
        modal: true
    });
    $("#applicant-status-submit").click(function() {
        $.getJSON(osc.jobboard.ajax_applicant_status_notification,
            {
                "applicantId" : $('#applicant_status').attr('data-applicant-id'),
                "status" : $("#applicant_status option:selected").attr("value")
            },
            function(data){}
        );
        $('#dialog-applicant-status').dialog('close');
    });
    $("#applicant-status-cancel").click(function() {
        $('#dialog-applicant-status').dialog('close');
    });

    $("#applicant_status").change(function(){
        $.getJSON(osc.jobboard.ajax_applicant_status,
            {
                "applicantId" : $(this).attr('data-applicant-id'),
                "status" : $("#applicant_status option:selected").attr("value")
            },
            function(data){}
        );
        setIcon();
        $("#dialog-applicant-status").dialog('open');
    });
    setIcon();

    $('.auto-star').rating({
        callback: function(value, link, input){
            if( typeof value === 'undefined' ) {
                value = 0;
            }
            console.log('callback: ' + value);
            $.getJSON(osc.jobboard.ajax_rating,
                {
                    "applicantId" : $("#applicant_status").attr('data-applicant-id'),
                    "rating" : value
                },
                function(data){}
            );
        }
    });
});