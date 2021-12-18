$(function () {

    document.title = "LiveLog | " + window.location.protocol + '//' + window.location.hostname;

    let lastLogger      = [],
        requestGroups   = [],
        $document       = $(document),
        $body           = $('body'),
        timer           = null,
        $btn            = {
            search: $('#search'),
            scroll: $('#scroll'),
            collapse: $('#collapse'),
            top: $('#top'),
            bottom: $('#bottom'),
            clear: $('#clear')
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
            $requestGroup.attr('level', data.severity);
            $requestGroup.find('.level').remove();
            if (logLevels[data.severity] <= 3 ) {
                $requestGroup.find('.requestUri').prepend('<i class="level icon icon-cancel-circled"></i>');
            }
            else if (logLevels[data.severity] === 4) {
                $requestGroup.find('.requestUri').prepend('<i class="level icon icon-attention"></i>');
            }
            else if (logLevels[data.severity] === 5) {
                $requestGroup.find('.requestUri').prepend('<i class="level icon icon-attention-circled"></i>');
            }
            else if (logLevels[data.severity] >= 6) {
                $requestGroup.find('.requestUri').prepend('<i class="level icon icon-info-circled"></i>');
            }

        }

        if (lastLogger[data.requestId] !== data.logger) {
            lastLogger[data.requestId] = data.logger;
            $requestGroup.find('.request-body').append(
                $('<div class="logger-group">\n' +
                    '<div class="logger-group-head">\n' +
                        '<div class="logger-name search">' + data.logger + '</div>\n' +
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
                        '<span class="message search">' + data.message + '</span>\n';

        if (data.context.data && data.context.data.length !== 0) {
            entry = entry + '</div>\n'
                + '<div class="log-entry-context">\n' +
                    '<pre class="search">' + JSON.stringify(data.context.data) +  '</pre>\n' +
                '</div>\n';
        }

        entry = entry + '</div>';

        return entry;
    }

    function getRequestGroup(data)
    {
        let open        = '',
            opener      = '<span class="opener"><i class="icon icon-expand"></i></span></div>\n',
            requestId   = data.requestId,
            requestUri  = data.requestUri;

        if (typeof requestGroups[requestId] !== 'undefined') {
            return requestGroups[requestId]
        }

        if (false === $btn.collapse.hasClass('collapse')) {
            open = ' open'
            opener = '<span class=\"opener\"><i class="icon icon-collapse"></i></span></div>\n'
        }

        let dom = '<div class="request debug' + open + '" id="' + requestId + '">\n' +
                '<div class="request-head">\n' +
                    '<div class="requestUri">' +
                        '<span class="request-uri short search" title="' + requestUri + '">' + getShortString(requestUri) +'</span>' +
                        '<span class="request-uri long search">' + requestUri +'</span>' +
                        opener +
                    '<div class="requestId search">' + requestId + '</div>\n' +
                '</div>\n' +
                '<div class="request-body"></div>' +
            '</div>';

        requestGroups[requestId] = $(dom);

        $('#log').append(requestGroups[requestId]);

        return requestGroups[requestId];
    }

    function getShortString( string ) {
        if (string.length > 50) {
            return string.substring(0,50) + "...."
        }

        return string;
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
                $target = $self.parents('.request');
            if ($target.hasClass('open')) {
                $target.removeClass('open')
                $self.find('.opener').html('<i class="icon icon-expand">')
            } else {
                $target.addClass('open');
                $self.find('.opener').html('<i class="icon icon-collapse">')
            }
        });
    }

    function showLoading()
    {
        let indicator = $('#loading-indicator');

        if (false === indicator.hasClass('loading')) {
            indicator.addClass('loading');
        }

        window.clearTimeout(timer);
        timer = setTimeout(function (indicator) {
            indicator.removeClass('loading');
        }, 2000, indicator);
    }


    function findTerm(term)
    {
        let $found = $('.found'),
            elements = document.getElementsByClassName('search');

        $('.request').removeClass('results-available')
        $found.each(function (){
            this.outerHTML = this.innerHTML;
        });


        if (term.length === 0) {
            return;
        }

        for (let index = 0; index < elements.length; index ++) {
            let content = elements[index].innerHTML,
                pattern = new RegExp(term, 'gi');

            elements[index].innerHTML = content.replace(pattern, function(found) {
                return '<span class="found">' + found + '</span>';
            });
        }

        $('.found').each(function (){
            $(this).parents('.request').addClass('results-available');
        });
    }

    $btn.toggleScroll = function() {
        $btn.scroll.click(function () {
            if ($btn.scroll.hasClass('active')) {
                $btn.scroll.removeClass('active');
                $btn.scroll.html('<i class="icon icon-play"></i>');
            } else {
                $btn.scroll.addClass('active');
                $btn.scroll.html('<i class="icon icon-pause"></i>');
            }
        });
    }

    $btn.collapseAll = function ()
    {
        $btn.collapse.click(function (){
            if ($btn.collapse.hasClass('collapse')) {
                $btn.collapse.removeClass('collapse');
                $btn.collapse.html('<i class="icon icon-collapse"></i>');
                $document.find('.request').addClass('open');
                $document.find('.opener').html('<i class="icon icon-collapse">');

            } else {
                $btn.collapse.addClass('collapse');
                $btn.collapse.html('<i class="icon icon-expand"></i>');
                $document.find('.request').removeClass('open');
                $document.find('.opener').html('<i class="icon icon-expand">');
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

    $btn.clearLog = function(){
        $btn.clear.click(function (){
            $document.find('.request').remove();
            requestGroups.splice(0, requestGroups.length);
        });
    }

    $btn.handleSearch = function ()
    {
        $btn.search.click(function () {
            let term = $('#searchinput').val();
            findTerm(term);
        });
    }

    $document.ready(function () {
        checkForNewEntries();
        handleRequestOpen();
        $btn.toggleScroll();
        $btn.collapseAll();
        $btn.handleScrolling();
        $btn.clearLog();
        $btn.handleSearch();
    });
});
