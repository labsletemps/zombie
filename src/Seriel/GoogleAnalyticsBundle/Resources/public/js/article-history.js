$.widget('seriel.googleAnalyticsArticleHistoNav', $.seriel.navigator, {
	_create : function() {
		this._super();
		
		$('.day_report_container .list_content', this.element).ser_list();
		
		this.initGraph();
	},
	initGraph: function() {
		var svg = d3.select("svg");
		var jq_svg = $('svg', this.element);
		
		var margin = {top: 20, right: 20, bottom: 30, left: 50};

		var width = +jq_svg.width() - margin.left - margin.right;
		var height = +jq_svg.height() - margin.top - margin.bottom;
	    var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	    
	    var x = d3.scaleTime().rangeRound([0, width]);
	    var y = d3.scaleLinear().rangeRound([height, 0]);
	    
	    var line = d3.line().curve(d3.curveMonotoneX).x(function(d) { return x(parseTime(d.day)); }).y(function(d) { return y(d.pages_uniques); });
	    
	    parseTime = d3.timeParse('%Y-%m-%d');
	    
	    var data = json_decode($('.datas', this.element).html());
	    for (var i = 0; i < count(data); i++) {
	    	var dt = data[i];
	    	
	    	x.domain(d3.extent(data, function(d) { return parseTime(d.day); }));
	    	  y.domain(d3.extent(data, function(d) { return d.pages_uniques; }));

	    	  g.append("g")
	    	      .attr("transform", "translate(0," + height + ")")
	    	      .call(d3.axisBottom(x))
	    	      .select(".domain")
	    	      .remove();

	    	  g.append("g")
	    	      .call(d3.axisLeft(y))
	    	      .append("text")
	    	      .attr("fill", "#000")
	    	      .attr("transform", "rotate(-90)")
	    	      .attr("y", 6)
	    	      .attr("dy", "0.71em")
	    	      .attr("text-anchor", "end")
	    	      .text("Views");

	    	  g.append("path")
	    	      .datum(data)
	    	      .attr("fill", "none")
	    	      .attr("stroke", "#95172c")
	    	      .attr("stroke-linejoin", "round")
	    	      .attr("stroke-linecap", "round")
	    	      .attr("stroke-width", 0.5)
	    	      .attr("d", line);
	    	
	    }
	},
	openModal : function(title, url, options) {
		this.openModalInsideGrandParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		this.openModalWithContentInsideGrandParentNavigator(title, content, options);
	},
	options: {
		
	}
});