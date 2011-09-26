$(document).ready(function(){
    // Flash messages effect
    $("#main").live('pagecreate',function(event){
        $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
    });
});