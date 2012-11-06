$(document).ready(function() {
    // delete killer form
    $("#dialog-killerform-delete").dialog({
        autoOpen: false,
        modal: true
    });


    // delete killer form used by job entries.
    $("#dialog-killerforminuse-delete").dialog({
        autoOpen: false,
        modal: true
    });

    $('.delete_killerforminuse').live('click', function(){
        $("#dialog-killerforminuse-delete").dialog('open');
    });
});

function delete_killerform(id) {
    $("#delete_id").attr("value", id);
    $("#dialog-killerform-delete").dialog('open');
}