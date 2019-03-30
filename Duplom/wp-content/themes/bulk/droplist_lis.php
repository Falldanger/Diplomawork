<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <style>
    .border-col{
      border-radius: 6px;
      border: 2px solid #228B22;
      background: rgba(34, 139, 34, 0.55);
    }
  </style>
</head>
<body>
  

<div> 
<select class="border-col" onchange="window.location.href=this.options[this.selectedIndex].value">
    <optgroup label="Web">
        <option value="http://dklg.kmu.gov.ua/forest/control/uk/publish/article?art_id=62921">Державне агенство ліс.рес. України</option>
        <option value="https://works.doklad.ru/view/O-88S_VgLo0.html">Лісові та рекреаційні ресурси України</option>
        <option value="https://zakarpatlis.gov.ua/lisy-zakarpattya-zhyttya-pislya-burelomu/">Ліси Закарпаття</option>
        <option value="https://uk.wikipedia.org/wiki/%D0%A4%D0%BB%D0%BE%D1%80%D0%B0_%D0%97%D0%B0%D0%BA%D0%B0%D1%80%D0%BF%D0%B0%D1%82%D1%81%D1%8C%D0%BA%D0%BE%D1%97_%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%96">Флора Закарпатської області"</option>
        <option value="http://www.torgy.dazru.gov.ua/auction">Стан та використання Закарпатських лісів</option>
    </optgroup>
    <optgroup label="Documents">
      <option value="https://drive.google.com/drive/folders/1tYrAf18XkSkuczIQPS-f1diB7sokomsy">Google drive</option>
    </optgroup>
  </select>
 </div>
</body>
</html>