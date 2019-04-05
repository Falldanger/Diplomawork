<!DOCTYPE HTML>
<html>
<head>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
</head>

<body>
<div style="height: auto;" >
<div id="container" style="min-width: 100%; height: 400px; max-width: 1000px; display: inline-block;"></div>
</div>
<script>
  
Highcharts.chart('container', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title:{
  	text:''
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true,

        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
        style: {
          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
        }
      }
    }
  },
  series: [{
    name: 'Підпорядкованість',
    colorByPoint: true,
    data: [{
      name: 'Державні ліси, 7.6 млн.га.',
      y: 73
      
    }, {
      name: 'Комунальні ліси, 1.3 млн.га.',
      y: 13
    }, {
      name: 'Не надано у користування, 0.8 млн.га',
      y: 7
    }, {
      name: 'Міноборони,',
      y: 1
    }, {
      name: 'Інші',
      y: 6
    }]
  }]
});
</script>
</body>
</html>
<!-- @"DHLM Corp." 2019. All rights reserved. -->