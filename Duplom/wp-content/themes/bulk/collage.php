<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style>
.hh:hover{border: 2px solid #000;
opacity: 0.3;}
div.hh{border-radius: 4px;}
.work_descr1 {
    position: absolute;
    top:105px;
    left:165px;
    text-align: center;
    margin: auto;
    opacity: 0;
    display: inline-block;
}
.work_descr2 {
    position: absolute;
    top: 105px;
    left: 485px;
    text-align: center;
    margin: auto;
    opacity: 0;
    display: inline-block;
}
.work_descr3 {
    position: absolute;
    top: 105px;
    left: 855px;
    text-align: center;
    margin: auto;
    opacity: 0;
    display: inline-block;
}
.work_descr4 {
    position: absolute;
    top:345px;
    left:160px;
    text-align: center;
    margin: auto;
    opacity: 0;
    display: inline-block;
}
.work_descr5 {
    position: absolute;
    top:345px;
    left: 510px;
    text-align: center;
    margin: auto;
    opacity: 0;
    display: inline-block;
}
.work_descr6 {
    position: absolute;
    top:345px;
    left: 850px;
    text-align: center;
    margin: auto;
    opacity: 0;
    display: inline-block;
}
div.hh:hover + div.work_descr1{
	opacity: 1;
	cursor: pointer;
}
div.hh:hover + div.work_descr2{
	opacity: 1;
	cursor: pointer;
}
div.hh:hover + div.work_descr3{
	opacity: 1;
	cursor: pointer;
}
div.hh:hover + div.work_descr4{
	opacity: 1;
	cursor: pointer;
}
div.hh:hover + div.work_descr5{
	opacity: 1;
	cursor: pointer;
}
div.hh:hover + div.work_descr6{
	opacity: 1;
	cursor: pointer;
}
div.fixposition{
	display: block;
	position: relative;
}

</style>
</head>

<body>
	<div class="fixposition" style="margin:10px 0; padding: 0 50px; width: 100%;">
	<div class="hh" style="width: 33%;margin:2px auto; display: inline-block; padding: 0; height: 250px; background: #3CB371;"><img style="width: 100%; height: 100%;" src="<?php echo get_template_directory_uri(); ?>/img/buk.jpg" alt=""></div><div class="work_descr1"><h3 style="color: black;font-weight: 600;">Бук - 58,2%</h3></div>
	<div class="hh" style="width: 33%;margin:2px auto; display: inline-block; padding: 0; height: 250px; background: #3CB371;"><img style="width: 100%;height: 100%;" src="<?php echo get_template_directory_uri(); ?>/img/smereka.jpg" alt=""></div><div class="work_descr2"><h3 style="color: black;font-weight: 600;">Смерека - 26,4%</h3></div>
	<div class="hh" style="width: 33%;margin:2px auto; display: inline-block; padding: 0; height: 250px; background: #3CB371;"><img style="width: 100%;height: 100%;" src="<?php echo get_template_directory_uri(); ?>/img/dub.jpg" alt=""></div><div class="work_descr3"><h3 style="color: black;font-weight: 600;">Дуб - 8,5%</h3></div>
	<div class="hh" style="width: 33%;margin: auto; display: inline-block; padding: 0; height: 250px; background: #3CB371;"><img style="width: 100%; height: 100%;" src="<?php echo get_template_directory_uri(); ?>/img/yalutcia.jpg" alt=""></div><div class="work_descr4"><h3 style="color: black;font-weight: 600;">Ялиця - 4,4%</h3></div>
	<div class="hh" style="width: 33%;margin: auto; display: inline-block; padding: 0; height: 250px; background: #3CB371;"><img style="width: 100%; height: 100%;" src="<?php echo get_template_directory_uri(); ?>/img/yavir.jpg" alt=""></div><div class="work_descr5"><h3 style="color: black;font-weight: 600;">Явір - 1,1%</h3></div>
	<div class="hh" style="width: 33%;margin: auto; display: inline-block; padding: 0; height: 250px; background: #3CB371;"><img style="width: 100%; height: 100%;" src="<?php echo get_template_directory_uri(); ?>/img/inshi.jpg" alt=""></div><div class="work_descr6"><h3 style="color: black;font-weight: 600;">Інші - 1,4%</h3></div>
</div>
</html>