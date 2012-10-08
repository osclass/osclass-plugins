$(document).ready(function(){
    $('#dismiss-tip').bind('click', function(){
        $.getJSON(jobboard.ajax_dismiss_tip,
            {
                'noticeID': $("#dismiss-tip").attr('data-notice-id')
            },
            function(data) {
                if(data=='1') {
                     $("#dismiss-tip").parent().remove();
                }
            }
        );
    });
});

