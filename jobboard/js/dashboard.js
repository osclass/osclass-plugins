$(document).ready(function(){
    $('.widget-box-title .tabs li').each(function(){
        var dest = function(el){
            var dests = new Array();
            el.parent().find('a').each(function(){
                dests.push($(this).attr('href'));
            });
            return dests.join(',');
        }

        $(this).click(function(){
            $(this).parent().children().removeClass('active').filter($(this).addClass('active'));
            $(dest($(this))).hide().filter($(this).children().attr('href')).show();
            return false;
        });
    }).filter(':first').click();
    $('.widget-box-title .tabs').each(function(){
        $(this).find('li:first').click();
    })
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