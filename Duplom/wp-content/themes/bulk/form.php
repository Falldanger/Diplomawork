<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <style>
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
            <form action="" name="form" class="form">
            <fieldset>
            <div class="result">
            <p><!-- Кнопка для вычисления и Поле для вывода результата --><span><b>До сплати: </b></span><output style="display: inline; font-weight: 600; color: #800000;" for="masa terytories period vud_zabrud source" name="pp"><b><span style="color: #800000;font-weight: 600;"> 1000 </span></b></output><span><b> грн.</b></span>
			</p>
            </div>
                    <div class="kolon kolon1 kolon2">
                        <div class="">
                            <label>
                                Маса викинутої речовини(т):
                                <input type="number" name="masa" value="10" placeholder="min=0 and max=99999" class="inp" min="0,001" max="99999" maxlength="5" oninput="validity.valid||(value='');"/>
                            </label>
                        </div>
                       
                        <div class="similar">    
                            <label>
                                Територія розповсюдження:        
                                <select name="terytories" class="full">
                                    <option value="4">Від 1 до 200 метрів</option>
                                    <option value="6">Від 200 до 1000 метрів</option>
                                    <option value="7">Від 1го до 3 кілометрів</option>
                                    <option value="11">Більше 3ох кілометрів</option>
                                </select>
                            </label>
                        </div>
                        </div>
                        <div class="kolon">
                        <div class="similar similar1">    
                            <label>
                                Термін розповсюдження:        
                                <select name="period" class="full">
                                    <option value="3">0-2 тижні</option>
                                    <option value="4">Від 2 тижнів до 1 місяця</option>
                                    <option value="5">Від 1го до 6ти місяців </option>
                                    <option value="9">Більше 6ти місяців </option>
                                </select>
                            </label>
                        </div>
                       
                        <div class="similar">    
                            <label>
                                Вид забруднення:        
                                <select name="vud_zabrud" class="full">
                               		<option value="4">Тепловий</option>
                               		<option value="5">Механічний</option>
                                    <option value="7">Хімічний</option>
                                    <option value="10">Біологічний</option>
                                    <option value="11">Радіоактивний</option>
                                </select>
                            </label>
                        </div>
                        </div>
                        <div class="kolon">
                        <div class="similar up-point">   
                            <label>
                                Джерело забруднення:        
                                <select name="source" class="full">
                                    <option value="8">Промислове</option>
                                    <option value="5">Господарсько-побутове</option>
                                    <option value="4">Сільськогосподарське</option>
                                </select>
                            </label>
                        </div>
                      
                        <input type="button" class="inp btn-inp" value="Рoзрахувати" onclick="pp.value=masa.value*terytories.value*period.value*vud_zabrud.value*source.value"/>
                        </div>
                </fieldset>
            </form>
        </div>
    </body>
    </html>
    <!-- @"DHLM Corp." 2019. All rights reserved. -->