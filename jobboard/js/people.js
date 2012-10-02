$(document).ready(function(){
    $('.auto-star').rating({
        callback: function(value, link, input){
            var data = value.split("_");
            $.getJSON(
                osc.jobboard.ajax_url_rating,
                {
                    "applicantId" : data[0],
                    "rating" : data[1]
                },
                function(data){}
            );
        }
    });

    $("#filter_btn").click(function(){
        showPage();
        return false;
    });

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