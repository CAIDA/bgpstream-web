require(['jquery', 'moment'], function ($, moment) {

    var MOMENT_DATE_FORMAT = 'YYYY/MM/DD HH:mm';

    function buildTable(tbodyId, data) {
        var tbody = $('#' + tbodyId);

        for (var collector in data.collectors) {
            if (data.collectors.hasOwnProperty(collector)) {
                var info = data.collectors[collector];

                var row = $('<tr></tr>').appendTo(tbody);

                // how many dump types are there?
                var dumpTypeCnt = 0;
                for (k in info.dataTypes) if (info.dataTypes.hasOwnProperty(k)) dumpTypeCnt++;

                $('<td class="vertical-center" rowspan="' + dumpTypeCnt + '"><code>' + collector + '</code></td>').appendTo(row);

                var newRow = 0;
                for (dumpType in info.dataTypes) {
                    if (info.dataTypes.hasOwnProperty(dumpType)) {
                        if (newRow) {
                            row = $('<tr></tr>').appendTo(tbody);
                        }

                        var humanName = dumpType == 'ribs' ? 'RIBs' : dumpType == 'updates' ? 'Updates' : dumpType;

                        $('<td>' + humanName + ' (<code>' + dumpType + '</code>)</td>').appendTo(row);

                        var dumpPeriod = info.dataTypes[dumpType]['dumpPeriod'];
                        var freqMoment = moment.duration(dumpPeriod * 1000);
                        $('<td>' + freqMoment.humanize() + '</td>').appendTo(row);

                        var fromMoment = moment.unix(info.dataTypes[dumpType]['oldestDumpTime']).utc();
                        var fromTime = fromMoment.format(MOMENT_DATE_FORMAT);
                        var untilMoment = moment.unix(info.dataTypes[dumpType]['latestDumpTime']).utc();
                        var untilTime = untilMoment.format(MOMENT_DATE_FORMAT);

                        var diffNow = untilMoment.diff(moment());
                        var diffNowMoment = moment.duration(diffNow);
                        var diffClass = diffNow*-1 < dumpPeriod*1000*2 ? 'text-success' : diffNow*-1 < dumpPeriod*1000*3 ? 'text-warning' : 'text-danger';

                        $('<td>' + fromTime + ' <small>(' + fromMoment.fromNow() + '</small>)</td>').appendTo(row);
                        $('<td>' + untilTime + ' <small class="'+diffClass+'">(' + diffNowMoment.humanize() + ' ago)</small></td>').appendTo(row);

                        newRow = 1;
                    }
                }
            }
        }
    }

    function onData(json) {

        // first build the route views table
        if (!json || !json.data || !json.data.projects) {
            onError('Malformed metadata response');
            return;
        }
        var rvData = json.data.projects.routeviews;
        if (!rvData) {
            onError('Missing Route Views data');
            return;
        }
        buildTable('rv-tbody', rvData);

        var risData = json.data.projects.ris;
        if (!risData) {
            onError('Missing RIS data');
            return;
        }
        buildTable('ris-tbody', risData);

        var caidaBmpData = json.data.projects['caida-bmp'];
        if (!caidaBmpData) {
            onError('Missing CAIDA BMP data');
            return;
        }
        buildTable('caida-bmp-tbody', caidaBmpData);

        $('.progress').hide();
        $('table').show();
    }

    function onError(error) {
        // TODO: handle this
        console.error(error);
    }

    $(function () {
        var hash = document.location.hash;
        var prefix = "!";
        if (hash) {
            $('.nav-tabs a[href=' + hash.replace(prefix, "") + ']').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash.replace("#", "#" + prefix);
        });

        $.ajax({
            url: 'https://broker.bgpstream.caida.org/v2/meta/projects',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function (json) {
                if (json.hasOwnProperty('error') && json.error) {
                    onError(json.error);
                    return;
                }

                onData(json);
            },
            error: function (xOptions, textStatus, errorThrown) {
                if (textStatus == "abort") return;  // Call intentionally aborted
                onError(textStatus + (errorThrown ? ' (' + errorThrown + ')' : ''));
            }
        });
    });
});
