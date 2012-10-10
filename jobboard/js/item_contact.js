function isValidDate(s) {
    var bits = s.split('/');
    // new Date(year, month, day, hours, minutes, seconds, milliseconds);
    var d = new Date(bits[2] + '/' + bits[0] + '/' + bits[1]);
    return !!(d && (d.getMonth() + 1) == bits[0] && d.getDate() == Number(bits[1]));
}

jQuery.validator.addMethod("birthdate", function(value, element) {
    return isValidDate( value );
}, jobboard.langs.invalid_birthday_date);


$(document).ready(function() {
    $('body').append('<div id="contact-dialog" class="item" />');
    $('head').append('<style>.ui-dialog-title{display:none; }.ui-widget-overlay{background-color:#000;opacity:0.5; position:absolute; float:left; left:0; top:0}.ui-dialog-titlebar-close{position:absolute;right:0;top:0;display:block; padding:5px 10px; margin-top:10px; margin-right:10px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; background-color:#666;color:#fff; text-decoration:none}</style>');
    $('#contact').clone().appendTo('#contact-dialog').find('#error_list').addClass('error_list_2');
    var $dialog = $('#contact-dialog').dialog({
        width:'330px', modal: true, closeText:theme.langs.close, autoOpen: false,
        open: function(event, ui) {/* var form = $("#contact-dialog form").get(0); $.removeData(form,'validator'); */}
    });

    $('a#btn-apply-job').click(function(){
        $('html, body').animate({ scrollTop: 0 }, 'fast',
            function(){
                $('#contact-dialog').dialog('open');
            });
        return false
    });

    // Code for form validation
    $("#contact-dialog form").validate({
        rules: {
            yourName: {
                required: true
            },
            yourEmail: {
                required: true,
                email: true
            },
            message: {
                required: true,
                minlength: 1
            }
        },
        messages: {
            yourName: {
                required: "Name: this field is required"
            },
            yourEmail: {
                required: "Email: this field is required",
                email: "Invalid email address"
            },
            message: {
                required: "Message: this field is required",
                minlength: "Message: this field is required"
            }
        },
        errorLabelContainer: ".error_list_2",
        wrapper: "li",
        invalidHandler: function(form, validator) {
            $('html,body').animate({ scrollTop: $('h1').offset().top }, { duration: 250, easing: 'swing'});
        }
    });
});