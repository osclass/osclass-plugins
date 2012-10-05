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
    $('#sex').rules('add', {
        required: true,
        messages: {
            required: jobboard.langs.sex_required
        }
    });

    $('#birthday').rules("add", {
        required: true,
        birthdate: true,
        messages: {
            required: jobboard.langs.birthday_required
        }
    });
});