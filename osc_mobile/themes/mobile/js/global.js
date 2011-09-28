$(document).ready(function(){
    $('[data-role=page]').live('pagecreate',function(event){
        $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
    });
});

$('[data-role=page]').live('pagecreate',function(event){
    $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
});