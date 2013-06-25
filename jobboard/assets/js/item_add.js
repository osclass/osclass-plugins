$(document).ready(function(){
    $($('select#parentCategory').find('option')[2]).prop("selected",true);
    setTimeout(function() {
	$("select#parentCategory").trigger('change');
	$('select#parentCategory').find('option:first').prop("selected",true);
	$("select#parentCategory").trigger('change');
    }, 1) ;

});