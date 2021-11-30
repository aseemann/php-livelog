$(function () {

    document.title = "LiveLog | " + window.location.protocol + '//' + window.location.hostname;

    let lastLogger      = [],
        requestGroups   = [],
        $document       = $(document),
        $body           = $('body'),
        timer           = null,
        $btn            = {
            scroll: $('#scroll'),
            collapse: $('#collapse'),
            top: $('#top'),
            bottom: $('#bottom')
        },
        logLevels = {
            'emergency': 0,
            'alert': 1,
            'critical': 2,
            'error': 3,
            'warning': 4,
            'notice': 5,
            'info': 6,
            'debug': 7
        };

    function addLogEntry(data)
    {
        let $requestGroup = getRequestGroup(data);

        let highestLevel = $requestGroup.attr('level');

        if (!highestLevel) {
            highestLevel = 'debug';
        }

        if (logLevels[highestLevel] > logLevels[data.severity]) {
            $requestGroup.removeClass(highestLevel);
            $requestGroup.addClass(data.severity);
            $requestGroup.attr('level', data.severity);
        }

        if (lastLogger[data.requestId] !== data.logger) {
            lastLogger[data.requestId] = data.logger;
            $requestGroup.find('.request-body').append(
                $('<div class="logger-group">\n' +
                    '<div class="logger-group-head">\n' +
                        '<div class="logger-name">' + data.logger + '</div>\n' +
                    '</div>\n' +
                    '<div class="log-entries">' +
                    '</div>' +
                   '</div>')
            );
        }

        $requestGroup.find('.logger-group').last().find('.log-entries').append($(getLogEntry(data)));
        handleScrolling();
    }

    function getLogEntry(data)
    {
        let entry = '<div class="log-entry severity-' + data.severity + '">\n' +
                    '<div class="log-entry-head ">\n' +
                        '<span class="severity">' + data.severity + '</span><br>\n' +
                        '<span class="message">' + data.message + '</span>\n';

        if (data.context.data && data.context.data.length !== 0) {
            entry = entry + '</div>\n'
                + '<div class="log-entry-context">\n' +
            '<pre>' + JSON.stringify(data.context.data) +  '</pre>\n' +
            '</div>\n';
        }

        entry = entry + '</div>';

        return entry;
    }

    function getRequestGroup(data)
    {
        if (typeof requestGroups[data.requestId] !== 'undefined') {
            return requestGroups[data.requestId]
        }

        let open = '';

        if (false === $btn.collapse.hasClass('collapse')) {
            open = ' open'
        }

        let dom = '<div class="request debug" id="' + data.requestId + '">\n' +
                '<div class="request-head">\n' +
                    '<div class="requestUri"><span class="request-uri">' + data.requestUri +'</span><span class="opener">&plus;</span></div>\n' +
                    '<div class="requestId">' + data.requestId + '</div>\n' +
                '</div>\n' +
                '<div class="request-body' + open + '"></div>' +
            '</div>';

        requestGroups[data.requestId] = $(dom);

        $('#log').append(requestGroups[data.requestId]);

        return requestGroups[data.requestId];
    }

    function checkForNewEntries()
    {
        $.ajax({
            type: "GET",
            url: "/livelog/socket.php"
        }).done(function (data) {
            if (!data) {
                checkForNewEntries();
                return;
            }
            showLoading();
            data.forEach(function (logEntry, index) {
                addLogEntry(logEntry);
            });
            checkForNewEntries();
        });
    }

    function handleScrolling()
    {
        if (false === $btn.scroll.hasClass('active')) {
            return;
        }
        $body.stop().animate({ scrollTop: $(document).height() }, 20);
    }

    function handleRequestOpen()
    {
        $document.on('click', '.request-head', function (){
            let $self = $(this),
                $target = $self.parents('.request').find('.request-body');
            if ($target.hasClass('open')) {
                $target.removeClass('open')
                $self.find('.opener').html('&plus;')
            } else {
                $target.addClass('open');
                $self.find('.opener').html('&minus;')
            }
        });
    }

    function showLoading()
    {
        let indicator = $('#loading-indicator');

        if (false === indicator.hasClass('loading')) {
            indicator.addClass('loading');
        }

        timer = null;
        timer = setTimeout(function (indicator) {
            indicator.removeClass('loading');
        }, 1000, indicator);
    }

    $btn.toggleScroll = function() {
        $btn.scroll.click(function () {
            if ($btn.scroll.hasClass('active')) {
                $btn.scroll.removeClass('active');
                $btn.scroll.text('scrolling off');
            } else {
                $btn.scroll.addClass('active');
                $btn.scroll.text('scrolling on');
            }
        });
    }

    $btn.collapseAll = function ()
    {
        $btn.collapse.click(function (){
            if ($btn.collapse.hasClass('collapse')) {
                $btn.collapse.removeClass('collapse');
                $document.find('.request-body').addClass('open');
            } else {
                $btn.collapse.addClass('collapse');
                $document.find('.request-body').removeClass('open');
            }
        });
    }

    $btn.handleScrolling = function () {
        $btn.top.click(function () {
            $body.stop().animate({ scrollTop: 0 }, 20);
        });
        $btn.bottom.click(function () {
            $body.stop().animate({ scrollTop: $(document).height() }, 20);
        });
    }

    $document.ready(function () {
        checkForNewEntries();
        handleRequestOpen();
        $btn.toggleScroll();
        $btn.collapseAll();
        $btn.handleScrolling();
    });
});

// Data Json
//{"application":"Test","logger":"\/var\/www\/html\/logs.php","requestId":"61923c348645f","requestUri":"\/logs.php","severity":"info","message":"Test info","context":[]}
