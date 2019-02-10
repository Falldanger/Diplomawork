<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Стан водокористування в Закарпатській області"
	},
	axisX:{
		valueFormatString: "YYYY",
		crosshair: {
			enabled: true,
			snapToDataPoint: true
		}
	},
	axisY: {
		title: "Млн.м3",
		crosshair: {
			enabled: true
		}
	},
	toolTip:{
		shared:true
	},  
	legend:{
		cursor:"pointer",
		verticalAlign: "bottom",
		horizontalAlign: "left",
		dockInsidePlotArea: true,
		itemclick: toogleDataSeries
	},
	data: [{
		type: "line",
		showInLegend: true,
		name: "Скидання забруднених стічних вод",
		markerType: "square",
		xValueFormatString: "YYYY",
		color: "#4682B4",
		dataPoints: [
			{ x: new Date(1990,01,01), y: 29.31 },
			{ x: new Date(2000,01,01), y: 13.02 },
			{ x: new Date(2005,01,01), y: 12.99 },
			{ x: new Date(2010,01,01), y: 7.78 },
			{ x: new Date(2011,01,01), y: 3.11 },
			{ x: new Date(2012,01,01), y: 2.42 },
			{ x: new Date(2013,01,01), y: 2.39 },
			{ x: new Date(2014,01,01), y: 2.41 },
			{ x: new Date(2015,01,01), y: 2.43 },
			{ x: new Date(2016,01,01), y: 4.02 },
			{ x: new Date(2017,01,01), y: 4.24 },
			{ x: new Date(2018,01,01), y: 4.31 }
		]
	},
	{
		type: "line",
		showInLegend: true,
		name: "Використання свіжої води",
		lineDashType: "dash",
		color: "#FF6347",
		dataPoints: [
			{ x: new Date(1990,01,01), y: 143.5 },
			{ x: new Date(2000,01,01), y: 70.2 },
			{ x: new Date(2005,01,01), y: 43.82 },
			{ x: new Date(2010,01,01), y: 32.85 },
			{ x: new Date(2011,01,01), y: 30.83 },
			{ x: new Date(2012,01,01), y: 29.98},
			{ x: new Date(2013,01,01), y: 29.91},
			{ x: new Date(2014,01,01), y: 29.94 },
			{ x: new Date(2015,01,01), y: 29.75 },
			{ x: new Date(2016,01,01), y: 29.49 },
			{ x: new Date(2017,01,01), y: 21.8 },
			{ x: new Date(2018,01,01), y: 21.12 }
		]
	}]
});
chart.render();

function toogleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	} else{
		e.dataSeries.visible = true;
	}
	chart.render();
}

}
</script>
</head>

<style>
	a.canvasjs-chart-credit{ display: none;}
</style>

<body>

<div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


</body>
</html>