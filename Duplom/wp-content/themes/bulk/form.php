<?php
if(!empty($_POST['calculate']))
{
    $masa     = floatval($_POST['masa']);
    $terytories = floatval($_POST['terytories']);
    $period  = floatval($_POST['period']);
    $vud_zabrud      = intval($_POST['vud_zabrud']);
    $source    = floatval($_POST['source']);
    
   //result
    $result = abs(round($masa*$terytories*$period*$vud_zabrud*$source))." грн.";
}
else{
    $masa     = 10;
    $terytories = 1.2;
    $period  = 1.5;
    $vud_zabrud  = 2.8;
    $source     = 4.5;  
}

if($masa<=0){
	$message = "Маса викинутої речовини(т) має бути більше 0";
echo "<script type='text/javascript'>alert('$message');</script>"." - Дані не вірні(присутнє від'ємне значення або 0)";
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <style>
    	.kolon{
    		width: 30%;
    		display: inline-block;
    		margin-right:19px;
    		margin-left: 19px;
    	}
    	.kolon1{
    		margin-left: 20px;
    	}
    	.kolon2{
    		padding: 10px;
    	}
    	.form{
    		width: 100%;
    		background: rgba(70, 130, 180 ,0.2);
    		padding: 0;
    		margin: 0;
    	}
    	.inp{
    		height: 34px;
    		font-size: 16px;
    		color: black;
    		width: 100%
    	}
    	input.inp{
    		border-radius:5px;
    		padding: 0;
    		font-weight: 300;
    	}
    	.similar{
    		width: 100%;
    	}
    	.similar1{
    		padding: 10px 0;
    	}
    	select.full{
    		width: 100%;
    		margin:0;
    		border-radius:5px;
    		font-weight: 300;
    	}
    	div.result{
    		border-radius:5px 5px 0 0;
    		background: #FA8072;
    		height: 44px;
    		padding: 8px 30px;
    	}
    	input.btn-inp{
    		background-color: #5cb85c;
            border-color: #4cae4c;
    		color: white;
    	}
    	div.similar.up-point{
    		padding: 0 0 38px 0;

    	}
    </style>
    <body>
        <div class="">
            <?php if(!empty($result)):?>
                <div class="result">
                    <strong>До сплати: </strong> <?= $result ?>
                </div> 
            <?php endif ?>
            <form action="" method="post" name="form" class="form" id="shtraf">
                    <div class="kolon kolon1 kolon2">
                        <div class="">
                            <label>
                                Маса викинутої речовини(т):
                                <input type="text" name="masa" value="<?= $masa ?>" class="inp" />
                            </label>
                        </div>
                       
                        <div class="similar">    
                            <label>
                                Територія розповсюдження:        
                                <select name="terytories" class="full">
                                    <option value='3.6' <?= 3.6 == $terytories ? 'selected' : '' ?>>Від 1 до 200 метрів</option>
                                    <option value='5.4' <?= 5.4 == $terytories ? 'selected' : '' ?>>Від 200 до 1000 метрів</option>
                                    <option value='6.8' <?= 6.8 == $terytories ? 'selected' : '' ?>>Від 1го до 3 кілометрів</option>
                                    <option value='10.5' <?= 10.5 == $terytories ? 'selected' : '' ?>>Більше 3ох кілометрів</option>
                                </select>
                            </label>
                        </div>
                        </div>
                        <div class="kolon">
                        <div class="similar similar1">    
                            <label>
                                Термін розповсюдження:        
                                <select name="period" class="full">
                                    <option value='3.5' <?= 3.5 == $period ? 'selected' : '' ?>>0-2 тижні</option>
                                    <option value='4.4' <?= 4.4 == $period ? 'selected' : '' ?>>Від 2 тижнів до 1 місяця</option>
                                    <option value='5.9' <?= 5.9 == $period ? 'selected' : '' ?>>Від 1го до 6ти місяців </option>
                                    <option value='9' <?= 9 == $period ? 'selected' : '' ?>>Більше 6ти місяців </option>
                                </select>
                            </label>
                        </div>
                       
                        <div class="similar">    
                            <label>
                                Вид забруднення:        
                                <select name="vud_zabrud" class="full">
                               		<option value='4.8' <?=4.8  == $vud_zabrud ? 'selected' : '' ?>>Тепловий</option>
                               		<option value='5.4' <?= 5.4 == $vud_zabrud ? 'selected' : '' ?>>Механічний</option>
                                    <option value='7.2' <?=7.2  == $vud_zabrud ? 'selected' : '' ?>>Хімічний</option>
                                    <option value='10.2' <?=10.2  == $vud_zabrud ? 'selected' : '' ?>>Біологічний</option>
                                    <option value='11.7' <?=11.7  == $vud_zabrud ? 'selected' : '' ?>>Радіоактивний</option>
                                </select>
                            </label>
                        </div>
                        </div>
                        <div class="kolon">
                        <div class="similar up-point">   
                            <label>
                                Джерело забруднення:        
                                <select name="source" class="full">
                                    <option value='7.2' <?= 7.2 == $source ? 'selected' : '' ?>>Промислове</option>
                                    <option value='5.8' <?= 5.8 == $source ? 'selected' : '' ?>>Господарсько-побутове</option>
                                    <option value='3.9' <?= 3.9 == $source ? 'selected' : '' ?>>Сільськогосподарське</option>
                                </select>
                            </label>
                        </div>
                      
                        <a href="#shtraf"><input type="submit" name="calculate" value="Рoзрахувати" class="inp btn-inp"/></a>
                        </div>
            </form>
        </div>
    </body>
    </html>