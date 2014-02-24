$(document).ready(function(){
    var queues = $('#queues');
    var opener = $('#menu .opener');
    var queuesOpener = $('#menu .queues-opener');
    var leftMenu = $('#left-menu');
    var circularMenu = $('#options');
    var toggleButton = $('#toggle-button');

    opener.hover(smallOpen, closeMenu);
    opener.click(function(e){
        e.stopPropagation();
        e.preventDefault();
        if (leftMenu.hasClass('full'))
            leftMenu.removeClass('opened full');
        else
            leftMenu.addClass('opened full');
    });
    queuesOpener.click(function(e){
        e.stopPropagation();
        e.preventDefault();
        queues.toggleClass('opened');
    });

    function smallOpen(e)  {
        e.stopPropagation();
        e.preventDefault();
        leftMenu.addClass('opened');
    }
    function closeMenu(e) {
        e.stopPropagation();
        e.preventDefault();
        leftMenu.removeClass('opened full');
    }

    toggleButton.click(function(e){
        e.stopPropagation();
        e.preventDefault();
        toggleButton.text(toggleButton.text() == '+' ? '-' : '+');
        circularMenu.toggleClass('opened');
    });

    $('#options i[title], #options img[title]').tooltip({
        'container':'body'
    });
    $('#menu .resources img, #menu .queues-opener, #menu .queueSelector a').tooltip({'placement': 'bottom', 'container': 'body'});

    // QUEUES
    $('.queueSelector a').click(function(e){
        e.preventDefault();
        e.stopPropagation();
        var elem = $(this);
        if (elem.hasClass('opened'))
            return;
        var target = $('#queues .queue' + elem.data('target'));
        $('#queues .opened').removeClass('opened');
        target.addClass('opened');
        elem.addClass('opened');
    });
});
