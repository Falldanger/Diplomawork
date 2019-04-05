<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
</head>

<body>
<div id="myfirstchart" style="height: 350px;" ><path fill="#daa520"> </path>	</div>

<script>
	new Morris.Donut({
  // ID of the element in which to draw the chart.
  element: 'myfirstchart',
  // Chart data records -- each entry in this array corresponds to a point on
  // the chart.
  data: [
    {value: 15.66, label: 'Рілля'},
    {value: 2.1, label:   'Багаторічні насадження'},
    {value: 17.63, label: 'Сіножаті та пасовища'},
    {value: 56.8, label:  'Ліси'},
    {value: 0.06, label:  'Відкриті заболочен землі'},
    {value: 1.2, label:  'Без рослинного покриву'},
    {value: 5, label:  'Інші види земель'}
  ],
  formatter: function (x) { return x + "%"}
}).on('click', function(i, row){
  console.log(i, row);
});
</script>
</body>
</html>
<!-- @"DHLM Corp." 2019. All rights reserved. -->