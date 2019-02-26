<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Рік', 'Порушені', 'Відпрацьовані'],
          ['2010',  0.83,      0.31],
          ['2011',  0.81,      0.32],
          ['2012',  0.8,      0.31],
          ['2013',  0.86,      0.29],
          ['2014',  0.83,      0.29],
          ['2015',  0.85,      0.27],
          ['2016',  0.76,      0.26],
          ['2017',  0.71,       0.24],
          ['2018',  0.7,       0.21]
        ]);

        var options = {
          title: 'Залежність порушення та відпрацювання земель відносно досліджених років',
          hAxis: {title: 'Рік',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 100%; height: 500px;"></div>
  </body>
</html>