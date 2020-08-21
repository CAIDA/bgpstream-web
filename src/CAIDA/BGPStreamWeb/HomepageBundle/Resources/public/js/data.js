/*
 * Copyright (C) 2014 The Regents of the University of California.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require(['jquery', 'moment'], function ($, moment) {

    var MOMENT_DATE_FORMAT = 'YYYY/MM/DD HH:mm';

    function buildTable(tbodyId, data) {
        var tbody = $('#' + tbodyId);

        var cNames = Object.keys(data.collectors).sort();
        cNames.forEach(function (collector) {
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
        });
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
            const hashTab = $('.nav-tabs a[href=' + hash.replace(prefix, "") + ']');
            if (hashTab && hashTab.tab) {
                hashTab.tab('show');
            }
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
