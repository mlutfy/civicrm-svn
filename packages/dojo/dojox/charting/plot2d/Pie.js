if(!dojo._hasResource["dojox.charting.plot2d.Pie"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojox.charting.plot2d.Pie"] = true;
dojo.provide("dojox.charting.plot2d.Pie");

dojo.require("dojox.charting.Element");
dojo.require("dojox.charting.axis2d.common");
dojo.require("dojox.charting.plot2d.common");

dojo.require("dojox.lang.functional");
dojo.require("dojox.gfx");

(function(){
	var df = dojox.lang.functional, du = dojox.lang.utils,
		dc = dojox.charting.plot2d.common,
		da = dojox.charting.axis2d.common,
		g = dojox.gfx,
		labelFudgeFactor = 0.8;		// in percents (to convert font's heigth to label width)

	dojo.declare("dojox.charting.plot2d.Pie", dojox.charting.Element, {
		defaultParams: {
			labels:			true,
			ticks:			false,
			fixed:			true,
			precision:		1,
			labelOffset:	20,
			labelStyle:		"default",	// default/rows/auto
			htmlLabels:		true		// use HTML to draw labels
		},
		optionalParams: {
			font:		"",
			fontColor:	"",
			radius:		0
		},

		constructor: function(chart, kwArgs){
			this.opt = dojo.clone(this.defaultParams);
			du.updateWithObject(this.opt, kwArgs);
			du.updateWithPattern(this.opt, kwArgs, this.optionalParams);
			this.run = null;
			this.dyn = [];
		},
		clear: function(){
			this.dirty = true;
			this.dyn = [];
			return this;
		},
		setAxis: function(axis){
			// nothing
			return this;
		},
		addSeries: function(run){
			this.run = run;
			return this;
		},
		calculateAxes: function(dim){
			// nothing
			return this;
		},
		getRequiredColors: function(){
			return this.run ? this.run.data.length : 0;
		},
		render: function(dim, offsets){
			if(!this.dirty){ return this; }
			this.dirty = false;
			this.cleanGroup();
			var s = this.group, color, t = this.chart.theme;

			// calculate the geometry
			var rx = (dim.width  - offsets.l - offsets.r) / 2,
				ry = (dim.height - offsets.t - offsets.b) / 2,
				r  = Math.min(rx, ry),
				taFont = "font" in this.opt ? this.opt.font : t.axis.font,
				taFontColor = "fontColor" in this.opt ? this.opt.fontColor : t.axis.fontColor,
				sum = df.foldl1(this.run.data, "+"), start = 0, step,
				slices = dojo.map(this.run.data, function(x){ return x / sum; }),
				shift, size, labelR;
			if(this.opt.labels){
				var labels = dojo.map(slices, function(x){
					return this._getLabel(x * 100) + "%";
				}, this);
				shift = df.foldl1(dojo.map(labels, df.pluck("length")), "x, y -> Math.max(x, y)");
				size = taFont ? g.normalizedLength(g.splitFontString(taFont).size) : 0;
				shift = Math.max(shift * labelFudgeFactor, 1) / 2 * size;
				if(this.opt.labelOffset < 0){
					r = Math.min(rx - 2 * shift, ry - size) + this.opt.labelOffset;
				}
				labelR = r - this.opt.labelOffset;
			}
			if("radius" in this.opt){
				r = this.opt.radius;
				labelR = r - this.opt.labelOffset;
			}
			var	circle = {
					cx: offsets.l + rx,
					cy: offsets.t + ry,
					r:  r
				};

			this.dyn = [];			
			if(!this.run || !this.run.data.length){
				return this;
			}
			if(this.run.data.length == 1){
				// need autogenerated color
				color = new dojo.Color(t.next("color"));
				var shape = s.createCircle(circle).
						setFill(dc.augmentFill(t.run.fill, color)).
						setStroke(dc.augmentStroke(t.series.stroke, color));
				this.dyn.push({color: color, fill: shape.getFill(), stroke: shape.getStroke()});
				if(this.opt.labels){
					var shift = 4,
						taFont = "font" in this.opt ? this.opt.font : t.axis.font,
						taFontColor = "fontColor" in this.opt ? this.opt.fontColor : t.axis.fontColor,
						size = taFont ? g.normalizedLength(g.splitFontString(taFont).size) : 0;
					shift = Math.max(shift * labelFudgeFactor, 1) / 2 * size;
					// draw the label
					var elem = da.createText[this.opt.htmlLabels ? "html" : "gfx"]
									(this.chart, s, circle.cx, circle.cy + size / 2, "middle",
										"100%", taFont, taFontColor);
					if(this.opt.htmlLabels){ this.htmlElements.push(elem); }
				}
				return this;
			}
			// draw slices
			dojo.forEach(slices, function(x, i){
				// calculate the geometry of the slice
				var end = start + x * 2 * Math.PI;
				if(i + 1 == slices.length){
					end = 2 * Math.PI;
				}
				var	step = end - start,
					x1 = circle.cx + r * Math.cos(start),
					y1 = circle.cy + r * Math.sin(start),
					x2 = circle.cx + r * Math.cos(end),
					y2 = circle.cy + r * Math.sin(end);
				// draw the slice
				color = new dojo.Color(t.next("color"));
				var shape = s.createPath({}).
						moveTo(circle.cx, circle.cy).
						lineTo(x1, y1).
						arcTo(r, r, 0, step > Math.PI, true, x2, y2).
						lineTo(circle.cx, circle.cy).
						closePath().
						setFill(dc.augmentFill(t.series.fill, color)).
						setStroke(dc.augmentStroke(t.series.stroke, color));
				this.dyn.push({color: color, fill: shape.getFill(), stroke: shape.getStroke()});
				start = end;
			}, this);
			// draw labels
			if(this.opt.labels){
				start = 0;
				dojo.forEach(slices, function(x, i){
					// calculate the geometry of the slice
					var end = start + x * 2 * Math.PI;
					if(i + 1 == slices.length){
						end = 2 * Math.PI;
					}
					var	labelAngle = (start + end) / 2,
						x = circle.cx + labelR * Math.cos(labelAngle),
						y = circle.cy + labelR * Math.sin(labelAngle) + size / 2;
					// draw the label
					var elem = da.createText[this.opt.htmlLabels ? "html" : "gfx"]
									(this.chart, s, x, y, "middle",
										labels[i], taFont, taFontColor);
					if(this.opt.htmlLabels){ this.htmlElements.push(elem); }
					start = end;
				}, this);
			}
			return this;
		},
		
		// utilities
		_getLabel: function(number){
			return this.opt.fixed ? number.toFixed(this.opt.precision) : number.toString();
		}
	});
})();

}
