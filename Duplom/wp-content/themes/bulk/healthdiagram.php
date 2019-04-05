<!DOCTYPE HTML>
<html>
<head>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
</head>

<body>
<div style="height: auto;" >
<div id="container" style="height: 400px; display: inline-block; margin: auto; width: 100%;"></div>
<script>
  
Highcharts.chart('container', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: {
    text: ''
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
    name: 'Розподіл',
    colorByPoint: true,
    data: [{
      name: 'Вуглекислi хлоридно-натрієвi',
      y: 27
      
    }, {
      name: 'Вуглекислi хлоридно-гідрокарбонатнi та гідрокарбонатно-хлоридно-натрієвi',
      y: 22
    }, {
      name: 'Вуглекислi гідрокарбонатно натрієвi',
      y: 18
    }, {
      name: 'Вуглекислi гідрокарбонатнi натрієво-кальцієво-магнієвo металевi',
      y: 15
    }, {
      name: 'Сульфіднi',
      y: 6
    }, {
      name: 'Миш’яковистi',
      y: 6
    }, {
      name: 'Кременистi',
      y: 6
    }]
  }]
});
</script>
</body>
</html>
<!-- @"DHLM Corp." 2019. All rights reserved. -->