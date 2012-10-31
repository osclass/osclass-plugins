$(document).ready(function() {
    // delete killer form
    $("#dialog-killerform-delete").dialog({
        autoOpen: false,
        modal: true
    });

    $('.delete_killerform').live('click', function(){
        $("#dialog-killerform-delete").dialog('open');
    });
});