<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Нелісові землі', 'Площа(га)'],
          ["Рілля", 0.2],
          ["Сінокоси", 4.2],
          ["Пасовища", 3.0],
          ["Піски", 0.2],
          ['Яри, схили', 0.3],
          ["Інші нелісові землі", 4.1],
          ["Загальна площа", 12.1]
        ]);

        var options = {
          title: 'Нелісові землі, землі лісогосподарського призначення',
          width: 900,
          legend: { position: 'none' },
          chart: { title: 'Нелісові землі, землі лісогосподарського призначення',
                   subtitle: 'Станом на 01.01.2019' },
          bars: 'horizontal', // Required for Material Bar Charts.
          axes: {
            x: {
              0: { side: 'top', label: 'Площа(га)'} // Top x-axis.
            }
          },
          bar: { groupWidth: "100%" }
        };

        var chart = new google.charts.Bar(document.getElementById('top_x_div'));
        chart.draw(data, options);
        
      };
    </script>
  </head>
  <body>
    <div id="top_x_div" style="width: 100%; height: 450px; margin-left: 120px;"></div>
  </body>
</html>