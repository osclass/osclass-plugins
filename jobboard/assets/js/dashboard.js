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
    $.getJSON(jobboard.ajax.dismiss_tip,
        {
        'noticeID': $("#dismiss-tip").attr('data-notice-id')
        },
        function(data) {
        if( !data.error ) {
             $(".dismiss-tip").parent().remove();
        }
        }
    );
    });
    $('.jobboard-dashboard .ico-close').bind('click', function(){
    var fm = $(this).parents('.flashmessage-dashboard-jobboard');
    if($(fm).hasClass('flashmessage-dismiss')) {
        $(fm).remove();
    }
    });

    var dashboard = {
    tour: {
        id: "hello",
        steps: [
        {
          target: 'btn-dashboard-create',
          title: jobboard.langs.hopscotch.dashboard.step1.title,
          content: jobboard.langs.hopscotch.dashboard.step1.content,
          placement: "left",
          yOffset: "-20px",
          arrowOffset: "13px"
        },
        {
          target: 'menu_items',
          title: jobboard.langs.hopscotch.dashboard.step2.title,
          content: jobboard.langs.hopscotch.dashboard.step2.content,
          placement: "right"
        },
        {
          target: 'menu_corporateboard',
          title: jobboard.langs.hopscotch.dashboard.step3.title,
          content: jobboard.langs.hopscotch.dashboard.step3.content,
          placement: "right"
        },
        {
          target: 'menu_settings',
          title: jobboard.langs.hopscotch.dashboard.step4.title,
          content: jobboard.langs.hopscotch.dashboard.step4.content,
          placement: "right"
        },
        {
          target: 'menu_appearance',
          title: jobboard.langs.hopscotch.dashboard.step5.title,
          content: jobboard.langs.hopscotch.dashboard.step5.content,
          placement: "right"
        },
        {
          target: 'dashboard_tour_link',
          title: jobboard.langs.hopscotch.dashboard.step6.title,
          content: jobboard.langs.hopscotch.dashboard.step6.content,
          placement: "top",
          arrowOffset: "260px",
          xOffset: "-250px"
        }
        ],
        showPrevButton: true,
        scrollTopMargin: 100,
        showSkip: true,
        i18n: jobboard.langs.hopscotch.i18n
    },
    init: function() {
        if( jobboard.dashboard.tour.visible && jobboard.dashboard.tour.times_seen < 3 ) {
        dashboard.tour_start();
        }

        // save that the tour is done
        hopscotch.listen('end', function(){
        dashboard.tour_done();
        });

        $('#dashboard_tour_link').on('click', dashboard.tour_start);
    },
    tour_start: function() {
        hopscotch.startTour(dashboard.tour);
    },
    tour_done: function() {
        $.post(jobboard.ajax.dashboard_tour, {}, function(result) {}, 'json');
    }
    }
    dashboard.init();
});