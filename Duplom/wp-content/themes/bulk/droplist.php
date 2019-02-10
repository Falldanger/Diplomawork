<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>

<style>

.tap{
  background-color: #4682B4;
  width: 300px;
  height: 40px;
  padding:0;
  color: white;
  font-weight: bold;
  font-size: 20px;
  text-align: center;
  vertical-align:center;
  border-radius: 4px;
  border: 1px solid #4169E1;
  display: inline-block;
  margin:5px;
  position: absolute;
}
.tap1{
  background-color: #4682B4;
  width: 300px;
  height: 40px;
  padding:0;
  color: white;
  font-weight: bold;
  font-size: 20px;
  text-align: center;
  vertical-align:center;
  border-radius: 4px;
  border: 1px solid #4169E1;
  display: inline-block;
  margin:5px;
  position: absolute;
  margin-left: 340px;
}
.hidetap{
  display: none;
}
.hidetap{
  width: 300px;
  margin-top:0;
  border-radius: 4px;
}
.tap:hover div.hidetap{
  display: block;
  background: rgba(70, 130, 180, 0.9);;
}
.tap1:hover div.hidetap{
  display: block;
  background: rgba(70, 130, 180, 0.9);;
}
ul{
  margin:0;
  list-style-type:none;
  padding: 0;
}
li{
  padding:5px 0;
  border-bottom: 1px solid #F0FFFF;
}
li:hover{
  background: #87CEEB;

}
li a.pidkres{
  text-decoration-color:#F0FFFF;
  color: white;
}


.custom-dropdown::before {
    background-color: rgba(0,0,0,.15);
}
.custom-dropdown::before {
    width: 2em;
    right: 0;
    top: 0;
    bottom: 0;
    border-radius: 0 3px 3px 0;
}
.custom-dropdown::before, .custom-dropdown::after {
    content: "";
    position: absolute;
    pointer-events: none;
}
.custom-dropdown::after {
    color: rgba(0,0,0,.4);
}
.custom-dropdown::after {
    content: "\25BC";
    height: 1em;
    font-size: .625em;
    line-height: 1;
    right: 1.2em;
    top: 50%;
    margin-top: -.5em;
}


</style>

<body>
 
  <div class="tap custom-dropdown">-- Web-ресурси --
  <div class="hidetap">
    <ul>
      <li><a class="pidkres" href="http://buvrtysa.gov.ua/newsite/" target="_blank">Дослідницькі центри</a></li>
      <li><a class="pidkres" href="http://saee.gov.ua/uk/ae/hydroenergy" target="_blank">Гідроенергетика</a></li>
      <li><a class="pidkres" href="http://ua-referat.com/%D0%92%D0%BE%D0%B4%D0%BD%D1%96_%D1%80%D0%B5%D1%81%D1%83%D1%80%D1%81%D0%B8_%D0%97%D0%B0%D0%BA%D0%B0%D1%80%D0%BF%D0%B0%D1%82%D1%82%D1%8F" target="_blank">Водні ресурси Закарпаття</a></li>
      <li><a class="pidkres" href="http://geoinf.kiev.ua/monitorynh-pidzemnykh-vod/" target="_blank">ДНВП "ГЕОІНФОРМ УКРАЇНИ" </a></li>
      <li><a class="pidkres" href="https://works.doklad.ru/view/zV_mQgvETog/2.html" target="_blank">Водні ресурси України</a></li>
      <li><a class="pidkres" href="http://www.ukrstat.gov.ua/">ДержКомСтат</a></li>
    </ul>
  </div>
  </div>

<div class="tap1 custom-dropdown">-- Документи --
  <div class="hidetap">
    <ul>
      <li><a class="pidkres" href="get_template_directory_uri() . '/doc/rehdop2018.doc'" download="" title="всплывающая подсказка">Регіональна_доповідь_2018.doc</a></li>
      <li><a class="pidkres" href="get_template_directory_uri() . '/doc/Ecopasport.doc'" download="" title="всплывающая подсказка">Екопаспорт2018.doc</a></li>
      <li><a class="pidkres" href="get_template_directory_uri() . '/doc/Monit2018.doc'" download="" title="всплывающая подсказка">Моніторинг2018.doc</a></li>
      <li><a class="pidkres" href="get_template_directory_uri() . '/doc/statanal2018.pdf'" download="" title="всплывающая подсказка">Стат.аналіз_вод.рес.pdf</a></li>
      <li><a class="pidkres" href="get_template_directory_uri() . '/doc/suchasnstan.pdf'" download="" title="всплывающая подсказка">Сучасн_стан_вод_об'єкт.pdf</a></li>
    </ul>
    </ul>
  </div>
  </div>

</body>
</html>