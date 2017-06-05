/**
 * Created by subham on 1/6/17.
 */
$.get( URL+"financialTransaction")
    .done(function( value ) {
        var dataset = value.value;

        var margin = {top: 10, right: 10, bottom: 20, left: 40},
            height = 300,
            width = 480,
            w = width - margin.left - margin.right,
            h = height - margin.top - margin.bottom;
        var parseDate = d3.time.format("%Y-%m-%d").parse;

        dataset.forEach(function (d) {
            d.date = parseDate(d.date);
        });
        var tip = d3.tip()
            .attr('class', 'd3-tip')
            .offset([-10, 0])
            .html(function(d) {
                return "<strong>Balance:</strong> <span style='color:red'>" + d.balance + "</span>";
            })
        var svg = d3.select('#frequencyChart')
            .append('svg')
            .attr("width", '100%')
            .attr("height", '100%')
            .attr("viewBox", "0 0 " + width + " " + height)
            .attr("preserveAspectRatio", "xMinYMin meet")
            .append("g")
            .attr("transform",
                "translate(" + margin.left + "," + margin.top + ")");
        svg.call(tip);
        var dateArray = d3.time.days(d3.min(dataset, function (d) {
            return d.date
        }), d3.time.day.offset(d3.max(dataset, function (d) {
            return d.date
        }), +1));

        var x = d3.scale.ordinal().rangeRoundBands([0, w], .2, .02);
        var xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom")
            .tickSize(0)
            .tickPadding(5)
            .tickFormat(d3.time.format("%a"));
        x.domain(dateArray);

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + h + ")")
            .call(xAxis)
            .selectAll("text")
            .style("text-anchor", "middle");

        var y = d3.scale.linear().range([h, 0]);
        var formatPercent = d3.format(".2s");
        var yAxis = d3.svg.axis()
            .scale(y)
            .orient("left")
            .tickFormat(formatPercent);
        y.domain([0, d3.max(dataset, function (d) {
            return d.balance;
        })]);

        svg.append("g")
            .attr("class", "axis northing")
            .call(yAxis)
            .selectAll("line")
            .attr("x2", w)

        svg.selectAll('rect')
            .data(dataset)
            .enter()
            .append('rect')
            .attr("class", "bar")
            .attr('width', function (d, i) {
                return x.rangeBand();
            })
            .attr('height', function (d, i) {
                return h - y(d.balance);
            })
            .attr('x', function (d, i) {
                return x(d.date);
            })
            .attr('y', function (d, i) {
                return y(d.balance);
            })
            .on('mouseover', tip.show)
            .on('mouseout', tip.hide)
    })