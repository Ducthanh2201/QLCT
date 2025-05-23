/* Plugin for Flot: jquery.flot.time.js
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2007-2014 IOLA and Ole Laursen.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function($) {

    var options = {
        xaxis: {
            timezone: null,
            timeformat: null,
            twelveHourClock: false,
            monthNames: null,
            timeBase: "seconds"
        },
        yaxis: {
            timeBase: "seconds"
        }
    };

    // Internal helper to easily detect when time mode is enabled.
    // Returns true with timeMode enabled, false otherwise
    function isTimeMode(axis) {
        return axis.options.mode === "time";
    }

    // Returns a string with the date d formatted according to fmt.
    // A subset of the Open Group's strftime format is supported.
    function formatDate(d, fmt, monthNames, dayNames) {
        if (typeof d.strftime === "function") {
            return d.strftime(fmt);
        }

        var leftPad = function(n, pad) {
            n = "" + n;
            pad = "" + (pad == null ? "0" : pad);
            return n.length === 1 ? pad + n : n;
        };

        var r = [];
        var escape = false;
        var hours = d.getHours();
        var isAM = hours < 12;

        if (monthNames == null) {
            monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        }

        if (dayNames == null) {
            dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        }

        var hours12;

        if (hours > 12) {
            hours12 = hours - 12;
        } else if (hours === 0) {
            hours12 = 12;
        } else {
            hours12 = hours;
        }

        for (var i = 0; i < fmt.length; ++i) {
            var c = fmt.charAt(i);

            if (escape) {
                switch (c) {
                    case 'a':
                        c = "" + dayNames[d.getDay()];
                        break;
                    case 'b':
                        c = "" + monthNames[d.getMonth()];
                        break;
                    case 'd':
                        c = leftPad(d.getDate());
                        break;
                    case 'e':
                        c = leftPad(d.getDate(), " ");
                        break;
                    case 'h': // For back-compat with 0.7; remove in 1.0
                    case 'H':
                        c = leftPad(hours);
                        break;
                    case 'I':
                        c = leftPad(hours12);
                        break;
                    case 'l':
                        c = leftPad(hours12, " ");
                        break;
                    case 'm':
                        c = leftPad(d.getMonth() + 1);
                        break;
                    case 'M':
                        c = leftPad(d.getMinutes());
                        break;
                    case 'q':
                        c = "" + (Math.floor(d.getMonth() / 3) + 1);
                        break;
                    case 'S':
                        c = leftPad(d.getSeconds());
                        break;
                    case 'y':
                        c = leftPad(d.getFullYear() % 100);
                        break;
                    case 'Y':
                        c = "" + d.getFullYear();
                        break;
                    case 'p':
                        c = (isAM) ? ("" + "am") : ("" + "pm");
                        break;
                    case 'P':
                        c = (isAM) ? ("" + "AM") : ("" + "PM");
                        break;
                    case 'w':
                        c = "" + d.getDay();
                        break;
                }
                r.push(c);
                escape = false;
            } else {
                if (c === "%") {
                    escape = true;
                } else {
                    r.push(c);
                }
            }
        }

        return r.join("");
    }

    // To have a consistent view of time-based data independent of which time
    // zone the client happens to be in we need a date-like object independent
    // of time zones. This is done through a wrapper that only calls the UTC
    // versions of the accessor methods.

    function makeUtcWrapper(d) {
        function addProxyMethod(sourceObj, sourceMethod, targetObj, targetMethod) {
            sourceObj[sourceMethod] = function() {
                return targetObj[targetMethod].apply(targetObj, arguments);
            };
        };

        var utc = {
            date: d
        };

        // support strftime, if found
        if (d.strftime !== undefined) {
            addProxyMethod(utc, "strftime", d, "strftime");
        }

        addProxyMethod(utc, "getTime", d, "getTime");
        addProxyMethod(utc, "setTime", d, "setTime");

        var props = ["Date", "Day", "FullYear", "Hours", "Milliseconds", "Minutes", "Month", "Seconds"];

        for (var p = 0; p < props.length; p++) {
            addProxyMethod(utc, "get" + props[p], d, "getUTC" + props[p]);
            addProxyMethod(utc, "set" + props[p], d, "setUTC" + props[p]);
        }

        return utc;
    };

    // select time zone strategy. This returns a date-like object tied to the
    // desired timezone

    function dateGenerator(ts, opts) {
        if (opts.timezone === "browser") {
            return new Date(ts);
        } else if (!opts.timezone || opts.timezone === "utc") {
            return makeUtcWrapper(new Date(ts));
        } else if (typeof timezoneJS !== "undefined" && typeof timezoneJS.Date !== "undefined") {
            var d = new timezoneJS.Date();
            // timezone-js is fickle, so be sure to set the time zone before
            // setting the time.
            d.setTimezone(opts.timezone);
            d.setTime(ts);
            return d;
        } else {
            return makeUtcWrapper(new Date(ts));
        }
    }

    // map of app. size of time units in milliseconds
    var timeUnitSize = {
        "second": 1000,
        "minute": 60 * 1000,
        "hour": 60 * 60 * 1000,
        "day": 24 * 60 * 60 * 1000,
        "month": 30 * 24 * 60 * 60 * 1000,
        "quarter": 3 * 30 * 24 * 60 * 60 * 1000,
        "year": 365.2425 * 24 * 60 * 60 * 1000
    };

    // the allowed tick sizes, after 1 year we use
    // an integer algorithm

    var baseSpec = [
        [1, "second"],
        [2, "second"],
        [5, "second"],
        [10, "second"],
        [30, "second"],
        [1, "minute"],
        [2, "minute"],
        [5, "minute"],
        [10, "minute"],
        [30, "minute"],
        [1, "hour"],
        [2, "hour"],
        [4, "hour"],
        [8, "hour"],
        [12, "hour"],
        [1, "day"],
        [2, "day"],
        [3, "day"],
        [0.25, "month"],
        [0.5, "month"],
        [1, "month"],
        [2, "month"]
    ];

    // we don't know which variant(s) we'll need yet, but generating both is cheap

    var specMonths = baseSpec.concat([[3, "month"], [6, "month"], [1, "year"]]);
    var specQuarters = baseSpec.concat([[1, "quarter"], [2, "quarter"], [1, "year"]]);

    function init(plot) {
        plot.hooks.processOptions.push(function(plot, options) {
            $.each(plot.getAxes(), function(axisName, axis) {
                var opts = axis.options;

                if (opts.mode === "time") {
                    axis.tickGenerator = function(axis) {
                        var ticks = [];
                        var d = dateGenerator(axis.min, opts);
                        var minSize = 0;

                        // make quarter use a possibility if quarters are
                        // mentioned in either of these options

                        var spec = (opts.tickSize && opts.tickSize[1] === "quarter") ||
                            (opts.minTickSize && opts.minTickSize[1] === "quarter") ? specQuarters : specMonths;

                        if (opts.minTickSize !== null) {
                            if (typeof opts.tickSize === "number") {
                                minSize = opts.tickSize;
                            } else if (opts.minTickSize[1] === "year") {
                                minSize = opts.minTickSize[0] * timeUnitSize[opts.minTickSize[1]];
                            } else {
                                minSize = opts.minTickSize[0] * timeUnitSize[opts.minTickSize[1]];
                                spec = [opts.minTickSize];
                            }
                        }

                        var delta = axis.delta;

                        // If we have too many ticks, use a more aggressive algorithm
                        if (delta <= 360 * timeUnitSize.day) {
                            // Zoom in to the interesting range of data
                            var tz = opts.tickSize ? [opts.tickSize] : (ticks.length > 1 ? computeTickSize(axis.min, axis.max, ticks.length) : computeAutoTickSize(axis.min, axis.max));

                            for (var i = 0; i < spec.length; ++i) {
                                if (delta < timeUnitSize[spec[i][1]] * spec[i][0]) {
                                    tz = [spec[i]];
                                    break;
                                }
                            }

                            // since we used autotickSize, make sure we got a reasonable number of ticks
                            if (!opts.tickSize && tz[0][1] !== "year" && !opts.minTickSize) {
                                var numTicks = Math.ceil(delta / timeUnitSize[tz[0][1]] / tz[0][0]);
                                if (numTicks > 10) {
                                    // too many ticks, try a larger size
                                    tz[0][0] *= Math.ceil(numTicks / 10);
                                }
                            }

                            axis.tickSize = tz[0];

                            var size = axis.tickSize[0];
                            var unit = axis.tickSize[1];
                            var step = unit === "quarter" ? [1, 4, 7, 10] : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                            var fraction = unit === "quarter" ? 3 : 1;
                            var tickNum = 0;

                            if (unit === "second") {
                                d.setSeconds(floorInBase(d.getSeconds(), size));
                            } else if (unit === "minute") {
                                d.setMinutes(floorInBase(d.getMinutes(), size));
                            } else if (unit === "hour") {
                                d.setHours(floorInBase(d.getHours(), size));
                            } else if (unit === "month") {
                                d.setMonth(floorInBase(d.getMonth(), size));
                            } else if (unit === "quarter") {
                                d.setMonth(3 * floorInBase(d.getMonth() / 3, size));
                            } else if (unit === "year") {
                                d.setFullYear(floorInBase(d.getFullYear(), size));
                            }

                            d.setMilliseconds(0);

                            if (unit === "minute") {
                                d.setSeconds(0);
                            } else if (unit === "hour") {
                                d.setMinutes(0);
                                d.setSeconds(0);
                            } else if (unit === "day") {
                                d.setHours(0);
                                d.setMinutes(0);
                                d.setSeconds(0);
                            } else if (unit === "month") {
                                d.setDate(1);
                                d.setHours(0);
                                d.setMinutes(0);
                                d.setSeconds(0);
                            } else if (unit === "quarter") {
                                d.setDate(1);
                                d.setHours(0);
                                d.setMinutes(0);
                                d.setSeconds(0);
                            } else if (unit === "year") {
                                d.setMonth(0);
                                d.setDate(1);
                                d.setHours(0);
                                d.setMinutes(0);
                                d.setSeconds(0);
                            }

                            var carry = 0;
                            var v = Number.NaN;
                            var prev;

                            do {
                                prev = v;
                                v = d.getTime();
                                ticks.push(v);

                                if (unit === "month" || unit === "quarter") {
                                    if (size < 1) {
                                        // a bit complicated - we'll divide the
                                        // month/quarter up but we need to take
                                        // care of fractions so we don't end up in
                                        // the middle of a day

                                        d.setDate(1);
                                        var start = d.getTime();
                                        d.setMonth(d.getMonth() + (unit === "quarter" ? 3 : 1));
                                        var end = d.getTime();
                                        d.setTime(v + carry * timeUnitSize.hour + (end - start) * size);
                                        carry = d.getHours();
                                        d.setHours(0);
                                    } else {
                                        d.setMonth(d.getMonth() +
                                            size * (unit === "quarter" ? 3 : 1));
                                    }
                                } else if (unit === "year") {
                                    d.setFullYear(d.getFullYear() + size);
                                } else {
                                    d.setTime(v + size * timeUnitSize[unit]);
                                }
                            } while (v < axis.max && v !== prev);

                            return ticks;
                        } else {
                            // For year scales, use a computeAutoTickSize approach
                            var ticksPerYear = delta / timeUnitSize.year;
                            var tickSize = computeAutoTickSize(ticksPerYear, 1);
                            var tickCount = Math.ceil(delta / tickSize);
                            var startYear = d.getFullYear();
                            
                            for (var i = 0; i < tickCount; i++) {
                                ticks.push(new Date(startYear + i * tickSize, 0, 1).getTime());
                            }
                            
                            return ticks;
                        }
                    };

                    axis.tickFormatter = function(v, axis) {
                        var d = dateGenerator(v, axis.options);

                        // first check global format
                        if (opts.timeformat !== null) {
                            return formatDate(d, opts.timeformat, opts.monthNames, opts.dayNames);
                        }

                        // possibly use quarters if quarters are mentioned in
                        // any of these places
                        var useQuarters = (axis.options.tickSize &&
                                axis.options.tickSize[1] === "quarter") ||
                            (axis.options.minTickSize &&
                                axis.options.minTickSize[1] === "quarter");

                        var t = axis.tickSize[0] * timeUnitSize[axis.tickSize[1]];
                        var span = axis.max - axis.min;
                        var suffix = (opts.twelveHourClock) ? " %p" : "";
                        var hourCode = (opts.twelveHourClock) ? "%I" : "%H";
                        var fmt;

                        if (t < timeUnitSize.minute) {
                            fmt = hourCode + ":%M:%S" + suffix;
                        } else if (t < timeUnitSize.day) {
                            if (span < 2 * timeUnitSize.day) {
                                fmt = hourCode + ":%M" + suffix;
                            } else {
                                fmt = "%b %d " + hourCode + ":%M" + suffix;
                            }
                        } else if (t < timeUnitSize.month) {
                            fmt = "%b %d";
                        } else if ((useQuarters && t < timeUnitSize.quarter) ||
                            (!useQuarters && t < timeUnitSize.year)) {
                            if (span < timeUnitSize.year) {
                                fmt = "%b";
                            } else {
                                fmt = "%b %Y";
                            }
                        } else if (useQuarters && t < timeUnitSize.year) {
                            if (span < timeUnitSize.year) {
                                fmt = "Q%q";
                            } else {
                                fmt = "Q%q %Y";
                            }
                        } else {
                            fmt = "%Y";
                        }

                        var rt = formatDate(d, fmt, opts.monthNames, opts.dayNames);

                        return rt;
                    };
                }
            });
        });
    }

    // Time-axis support used to use the flot-axisLabels plugin, but is now internal.
    $.plot.plugins.push({
        init: init,
        options: options,
        name: 'time',
        version: '1.0'
    });

    // floorInBase helps in calculating ticks based on base value
    function floorInBase(n, base) {
        return base * Math.floor(n / base);
    }

    function computeAutoTickSize(delta, target) {
        // Assume we need 10 ticks at most
        var targStep = Math.abs(delta) / 10;
        var step = Math.pow(10, Math.floor(Math.log(targStep) / Math.LN10));
        var err = targStep / step;
        
        if (err <= 1.9) {
            step *= 1;
        } else if (err <= 4.9) {
            step *= 5;
        } else {
            step *= 10;
        }
        
        return step;
    }

    function computeTickSize(min, max, noTicks) {
        var delta = max - min;
        var size, unit;
        
        if (delta <= 0) {
            return [1, "second"];
        }
        
        for (var i = 0; i < baseSpec.length; ++i) {
            size = baseSpec[i][0];
            unit = baseSpec[i][1];
            
            if (size * timeUnitSize[unit] > delta) {
                break;
            }
        }
        
        if (i === baseSpec.length) {
            // We need something larger than all available units
            size = computeAutoTickSize(delta / timeUnitSize.year, 1);
            unit = "year";
        }
        
        return [size, unit];
    }
})(jQuery);