<?php
	$Psh=false;
	$A=0.5;
	$Tzsh=0.2;
	$Goz=false;
	$Pd=false;
	$Kz=false;
	$Ozr=false;
	$In=false;
	$Kn=false;
	$Keg=false;
	if (isset($_POST['myform'])) {
		$Goz=$_POST['Goz']??false;
		$Pd=$_POST['Pd']??false;
		$Ozr=$_POST['Ozr']??false;
		$In=$_POST['In']??false;
		$Kn=$_POST['Kn']??false;
		$Keg=$_POST['Keg']??false;
		if($Kn==1){
				$Knn=4;
			}
			if($Kn==2){
				$Knn=3;
			}
			if($Kn==3){
				$Knn=2.5;
			}
			if($Kn==4){
				$Knn=1.5;
			}
		if ($Goz !==false && $Pd !==false && $Keg !==false && $Ozr !==false && $In !==false && $Kn !==false && is_numeric($Goz) && is_numeric($Pd) && is_numeric($Keg) && is_numeric($Ozr) && is_numeric($In) && is_numeric($Kn) && ($Goz>=0.1 && $Goz<=0.99)&&($Pd>=100 && $Pd<=25000)&&($Ozr>=10 && $Ozr<=200)&&($In>=0.033 && $In<=0.1)&&($Kn==1 || $Kn==2 || $Kn==3 ||$Kn==4)&&($Keg>=1 && $Keg<=5.5)) {
			$Kz=($Ozr/($Tzsh*$In));
			$Pdd=$Pd*sqrt(15)/21000;
			$Psh=$A*$Goz*$Pdd*$Kz*$Knn*$Keg;
		}
		else{
			echo '<p style="color:red; font-weight:600;">Перевірте правильність введення даних!</p>';
		}
	}
	
?>
<?php if($Psh<=2000&&$Psh !== false){
		echo '<p style="color:ForestGreen; font-weight:600;">Забруднення не несе великої шкоди</p>';
	} ?>
<?php if($Psh>2000&&$Psh<=8000){
		echo '<p style="color:Goldenrod; font-weight:600;">Забруднення середньої важкості</p>';
	} ?>
<?php if($Psh>8000&&$Psh<=50000){
		echo '<p style="color:Chocolate; font-weight:600;">Коефіцієнт забруднення надто високий! Забруднена земельна територія потребує довгого відновлення.</p>';
	} ?>
<?php if($Psh>50000){
		echo '<p style="color:FireBrick; font-weight:600;">Суб\'єкт забруднення не правомірно забруднює земельні території. Потребується негайне втручання задля збереження екології!</p>';
	} ?>
<?php if ($Psh !==false): ?><p style="font-weight: 600;">Вартість штрафу за забруднення земельної ділянки складатиме = <span style="color: #800000;"><?=round($Psh,2)?></span> грн.</p><?php endif ?>

<style>
    input[type="text"]{
    	border-radius: 6px;
    	width: 100%;
    }
	.kolon{
    		width: 30%;
    		display: inline-block;
    		margin-right:17px;
    		margin-left: 17px;
    	}
    	.kolon1{
    		margin-left: 18px;
    	}
    	.kolon2{
    		padding: 10px;
    	}
    	label{
    		width: 100%;
    	}
    	.similar{
    		width: 100%;
    	}
    	.similar1{
    		padding: 10px 0;
    	}
    	.form{
    		width: 100%;
    		background: rgba(210, 105, 30 ,0.4);
    		padding: 0;
    		margin: 0;
    	}
    	div.center_control > input[type="submit"]{
    		width: 95.2%;
    		margin-left: 28px;
    		border-radius: 6px;
    		font-weight: 600;
    	}
    	div.similar > input[type="text"]{
    		width: 100%;}
    	.width100{width: 100%;}
    	
</style>
<div>
<form name="myform" action="" method="post" class="form">
<fieldset>
<div class="kolon kolon1 kolon2">
	<div class="similar similar1">
		<label>Грошова  оцінка  земельної  ділянки:     
		<input class="width100" type="text" name="Goz" placeholder="min=0.1 and max=0.99" value="<?=$Goz?>" >
		</label>
	</div>
	<div class="similar">
		<label>Площа  забрудненої  земельної  ділянки(кв.м):
		<input class="width100" type="text" name="Pd" placeholder="min=100 and max=25000" value="<?=$Pd?>">
		</label>
	</div>
</div>
<div class="kolon">
	<div class="similar similar1">
		<label>Об'єм забруднюючої речовини(куб.м):
		<input class="width100" type="text" name="Ozr" placeholder="min=10 and max=200" value="<?=$Ozr?>" >
		</label>
	</div>
	<div class="similar">
		<label>Індекс поправки до витрат на ліквідацію забруднення:
		<input class="width100" type="text" name="In" placeholder="min=0.033 and max=0.1" value="<?=$In?>">
		</label>
	</div>
</div>
<div class="kolon">
	<div class="similar similar1">
		<label>Коефіцієнт небезпечності забруднюючої речовини:</label>
		<input type="text" name="Kn" placeholder="1, 2, 3 or 4" value="<?=$Kn?>" >
	</div>
	<div class="similar">
		<label>Коефіцієнт  еколого-господарського  значення:</label>
		<input type="text" name="Keg" placeholder="min=1.5 and max=5.5" value="<?=$Keg?>">
	</div>
</div>
	<div class="center_control">
		<input type="submit" name="myform" value="Розрахувати">
	</div>
	</fieldset>
</form>
</div>