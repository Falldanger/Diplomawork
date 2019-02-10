<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['На виробничі потреби',     61],
          ['На питні та санітарно-гігієнічні потреби',      20],
          ['На зрошення',  17],
          ['На інші потреби', 2]
        ]);

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data);
      }
    </script>
  </head>
  <body>
  	<p style="color: #483D8B; font-size: 30px; font-weight: bold; text-align: center;">Структура використання свіжої води за видами потреб  у 2018 році, %</p>
    <div id="piechart" style="width: 800px; height: 450px; margin-left: 25%;"></div>
  </body>
</html>