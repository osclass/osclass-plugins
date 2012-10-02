$(document).ready(function(){
    $('.list-star').rating({showCancel: false, readOnly: true});

    $('.filter-star').rating({showCancel: true, cancelValue: null});

    $("#dialog-people-delete").dialog({
        autoOpen: false,
        modal: true
    });

    // tooltips notes
    $.each($('.note'), function(index, value) {
        $(value).osc_tooltip($(value).attr('data-tooltip'), {layout: 'gray-tooltip', position: { x: 'right', y: 'middle' }});
    });
})

function delete_applicant(id) {
    $("#delete_id").attr("value", id);
    $("#dialog-people-delete").dialog('open');
}