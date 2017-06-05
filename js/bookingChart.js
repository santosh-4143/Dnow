/**
 * Created by subham on 1/6/17.
 */

function getDate(d) {
    var dt = new Date(d.date);
    dt.setHours(0);
    dt.setMinutes(0);
    dt.setSeconds(0);
    dt.setMilliseconds(0);
    return dt;
}

$.get( URL+"bookingDetails")
    .done(function( value ) {
        var data = value.value;
        var m = [20, 30, 20, 100]; // margins
        var w = 450 - m[1] - m[3]; // width
        var h = 300 - m[0] - m[2]; // height

        data.sort(function (a, b) {
            var d1 = getDate(a);
            var d2 = getDate(b);
            if (d1 == d2) return 0;
            if (d1 > d2) return 1;
            return -1;
        });

        var minDate = getDate(data[0]),
            maxDate = getDate(data[data.length - 1]);

        var x = d3.time.scale().domain([minDate, maxDate]).range([0, w]);

        var y = d3.scale.linear().domain([0, d3.max(data, function (d) {
            return d.totalReq;
        })]).range([h, 0]);

        var tip = d3.tip()
            .attr('class', 'd3-tip1')
            .offset([-10, 0])
            .html(function(d) {
                return "<strong>Completed Request:</strong> <span style='color:red'>" + d.completedReq + "</span></br><strong>Date : </strong><span style='color:red'>"+d.date+"</span>";
            })
        var tip1 = d3.tip()
            .attr('class', 'd3-tip')
            .offset([-10, 0])
            .html(function(d) {
                return "<strong>Total Request:</strong> <span style='color:steelblue'>" + d.totalReq + "</span></br><strong>Date : </strong><span style='color:steelblue'>"+d.date+"</span>";
            })

        var line = d3.svg.line()
            .x(function (d, i) {
                return x(getDate(d)); //x(i);
            })
            .y(function (d) {
                return y(d.totalReq);
            });

        var line1 = d3.svg.line()
            .x(function (d, i) {
                return x(getDate(d)); //x(i);
            })
            .y(function (d) {
                return y(d.completedReq);
            });

        function xx(e) {
            return x(getDate(e));
        }

        function yy(e) {
            return y(e.totalReq);
        }

        function yy1(e) {
            return y(e.completedReq);
        }


        var graph = d3.select("#chart").append("svg:svg")
            .attr("width", w + m[1] + m[3])
            .attr("height", h + m[0] + m[2])
            .append("svg:g")
            .attr("transform", "translate(" + m[3] + "," + m[0] + ")");
        graph.call(tip);
        graph.call(tip1);

        var xAxis = d3.svg.axis().scale(x).ticks(d3.time.days, 1).tickSize(-h).tickSubdivide(true);
        graph.append("svg:g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + h + ")")
            .call(xAxis);

        var yAxisLeft = d3.svg.axis().scale(y).ticks(10).orient("left"); //.tickFormat(formalLabel);
        graph.append("svg:g")
            .attr("class", "y axis")
            .attr("transform", "translate(-25,0)")
            .call(yAxisLeft);


        graph
            .selectAll("circle")
            .data(data)
            .enter().append("circle")
            .attr("fill", "steelblue")
            .attr("r", 5)
            .attr("cx", xx)
            .attr("cy", yy)
            .on('mouseover', tip1.show)
            .on('mouseout', tip1.hide)
        graph
            .selectAll("ellipse")
            .data(data)
            .enter().append("ellipse")
            .attr("fill", "red")
            .attr("cx", xx)
            .attr("cy", yy1)
            .attr('rx', 5)
            .attr('ry', 5)
            .on('mouseover', tip.show)
            .on('mouseout', tip.hide)

        graph.append("svg:path").attr("d", line(data));
        graph.append("svg:path").attr("d", line1(data)).style("stroke", "red");
        graph.append("svg:text")
            .attr("x", -200)
            .attr("y", -90)
            .attr("dy", ".1em")
            .attr("transform", "rotate(-90)")
            .text("Total Request");

    })
