document.domain = 'osclass.com';

function utf8_decode (str_data) {
    var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0;

    str_data += '';

    while (i < str_data.length) {
        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if (c1 > 191 && c1 < 224) {
            c2 = str_data.charCodeAt(i + 1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i + 1);
            c3 = str_data.charCodeAt(i + 2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    }

    return tmp_arr.join('');
}


function get_linkedin_profile(profile) {
    var p = profile.person;
    var yourName = p.firstName+' '+p.lastName;
    yourName = utf8_decode(yourName);

    // remove input:file + add input:hidden from=linkedin
    $('form').append('<input type="hidden" name="from" value="linkedin"/>');
    $('form').append('<input type="hidden" name="pdfUrl" value="'+profile.pdfUrl+'"/>');
    $('input[name="attachment"]').remove();
    $('label[for="subject"]').remove();
    // show dialog
    $("#contact-dialog h2").text(jobboard.langs.complete_form_please);
    $('#contact-dialog').dialog("open");
    // remove input:file + add input:hidden from=linkedin DIALOG
    $('#contact-dialog form').append('<input type="hidden" name="from" value="linkedin"/>');
    $('#contact-dialog form').append('<input type="hidden" name="pdfUrl" value="'+profile.pdfUrl+'"/>');
    $('#contact-dialog input[name="attachment"]').remove();
    $('#contact-dialog label[for="subject"]').remove();

    // fill form with profile info
    var cover = profile.coverLetter;
    $('#contact-dialog textarea#message').val(cover);
    $('#contact-dialog input#yourName').val(yourName);
    $('#contact-dialog input#yourEmail').val(p.emailAddress);
    if(p.phoneNumbers && p.phoneNumbers.values && p.phoneNumbers.values[0]) {
        $('#contact-dialog input#phoneNumber').val(p.phoneNumbers.values[0].phoneNumber);
    }
}