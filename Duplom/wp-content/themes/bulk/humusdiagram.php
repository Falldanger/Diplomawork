<!DOCTYPE HTML>
<html>
<head>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
</head>

<body>
<div style="height: auto;" >
<div id="container" style="min-width: 49%; height: 400px; max-width: 1000px; display: inline-block;"></div>
<div id="container2" style="min-width: 49%; height: 400px; max-width: 1000px; display: inline-block; "></div></div>
<script>
  
Highcharts.chart('container', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: {
    text: 'За вмістом гумусу, площа у %'
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
    name: 'Вміст',
    colorByPoint: true,
    data: [{
      name: 'Дуже низький < 1,1',
      y: 2.1
      
    }, {
      name: 'Низький 1,1-2,0',
      y: 38.9
    }, {
      name: 'Середній 2,1-3,0',
      y: 34.4
    }, {
      name: 'Підвищений 3,1-4,0',
      y: 14.6
    }, {
      name: 'Високий 4,1-5,0',
      y: 5.6
    }, {
      name: 'Дуже високий>5,0',
      y: 4.4
    }]
  }]
});
</script>

<script>
  
Highcharts.chart('container2', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: {
    text: 'За вмістом рухомих сполук фосфору'
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
    name: 'Вміст у мг/кг ґрунту',
    colorByPoint: true,
    data: [{
      name: 'Дуже низький < 20',
      y: 35.8
    }, {
      name: 'Низький 21-50',
      y: 19.2
    }, {
      name: 'Середній 51-100',
      y: 22
    }, {
      name: 'Підвищений 101-150',
      y: 10.9
    }, {
      name: 'Високий 151-200',
      y: 9.4
    }, {
      name: 'Дуже високий>200',
      y: 2.7
    }]
  }]
});
</script>
</body>
</html>