<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<title>Графік</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Кругова діаграма -->
    <script>
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
	exportEnabled: true,
	animationEnabled: true,
	title:{
		text: " Структура використання свіжої води за видами потреб  у 2018 році, % "
	},
	legend:{
		cursor: "pointer",
		itemclick: explodePie
	},
	data: [{
		type: "pie",
		showInLegend: true,
		toolTipContent: "{name}: <strong>{y}%</strong>",
		indexLabel: "{name} - {y}%",
		dataPoints: [
			{ y: 61, name: "На виробничі потреби", exploded: true },
			{ y: 20, name: "На питні та санітарно-гігієнічні потреби" },
			{ y: 17, name: "На зрошення" },
			{ y: 2, name: "На інші потреби" }
		]
	}]
});
chart.render();
}

function explodePie (e) {
	if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
	} else {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
	}
	e.chart.render();

}
</script>


<!-- Styles -->
<style>
	a.canvasjs-chart-credit{ display: none;}
	button{
		display: none;
	}
</style>

</head>
<body>

	<!-- Diagram -->

	<div id="chartContainer" style="height: 400px; max-width: 80%; margin: 0px auto;"></div>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>



</body>
</html>