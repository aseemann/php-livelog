$(function () {

    document.title = "LiveLog | " + window.location.protocol + '//' + window.location.hostname;

    let lastLogger      = [],
        requestGroups   = [],
        $document        = $(document);


    function addLogEntry(data)
    {
        let $requestGroup = getRequestGroup(data);

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
    }

    function getLogEntry(data)
    {
        return  '<div class="log-entry severity-' + data.severity + '">\n' +
                    '<div class="log-entry-head ">\n' +
                        '<span class="severity">' + data.severity + '</span><br>\n' +
                        '<span class="message">' + data.message + '</span>\n' +
                    '</div>\n' +
                    '<div class="log-entry-context">\n' +
                        '<pre>' + JSON.stringify(data.context) +  '</pre>\n' +
                    '</div>\n' +
                '</div>'
    }

    function getRequestGroup(data)
    {
        if (typeof requestGroups[data.requestId] !== 'undefined') {
            return requestGroups[data.requestId]
        }

        let dom = '<div class="request" id="' + data.requestId + '">\n' +
                '<div class="request-head">\n' +
                    '<div class="requestUri">' + data.requestUri +' <span class="opener">&plus;</span></div>\n' +
                    '<div class="requestId">' + data.requestId + '</div>\n' +
                '</div>\n' +
                '<div class="request-body"></div>' +
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

            data.forEach(function (logEntry, index) {
                addLogEntry(logEntry);
            });
            checkForNewEntries();
        })
    }

    function handleRequestOpen()
    {
        $document.on('click', '.opener', function (){
            let $self = $(this),
                $target = $self.parents('.request').find('.request-body');
            if ($target.hasClass('open')) {
                $target.removeClass('open')
                $self.html('&plus;')
            } else {
                $target.addClass('open');
                $self.html('&minus;')
            }
        });
    }

    $document.ready(function () {
        checkForNewEntries();
        handleRequestOpen();
    });
});


//{"application":"Test","logger":"\/var\/www\/html\/logs.php","requestId":"61923c348645f","requestUri":"\/logs.php","severity":"info","message":"Test info","context":[]}
