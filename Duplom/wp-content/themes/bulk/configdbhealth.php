<!DOCTYPE html>
<html>
<head>
	<style>
		table {
font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
font-size: 14px;
text-align: center;
}
th {
background: rgba(178, 34, 34,0.65);
color: white;
padding: 10px 20px;
}
th, td {
border-style: solid;
border-width: 0 1px 1px 0;
border-color: white;
border-radius: 5px;
}
td {
background: rgba(255, 0, 0,0.4);
}
th:first-child, td:first-child {
text-align: center;
}
td:hover{
	background: #FFE4E1;
	}
	</style>
</head>
<body>
<?php
$host = 'localhost'; // адрес сервера 
$database = 'duplom'; // имя базы данных
$user = 'root'; // имя пользователя
$password = ''; // пароль
$link = mysqli_connect($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
$table=$_SESSION['table'];// передаємо назву таблиці
$query = "SELECT * FROM $table";
$query2 = "SHOW COLUMNS FROM `$table` WHERE FIELD != 'id'";
$result2 = mysqli_query($link, $query2) or die("Ошибка " . mysqli_error($link));
if($result2)
{	
	
    $rows = mysqli_num_rows($result2); // количество полученных строк
    echo "<table>";
    for ($i = 0 ; $i < $rows ; $i++)
    {
        $row = mysqli_fetch_row($result2);
        echo "<th>";
            for ($j = 0 ; $j < 1 ; $j++) echo "$row[$j]";
        echo "</th>";
    }
}
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
if($result)
{	
	
    $rows = mysqli_num_rows($result); // количество полученных строк
    for ($i = 0 ; $i < $rows ; $i++)
    {
        $row = mysqli_fetch_row($result);
        echo "<tr>";
            for ($j = 1 ; $j < mysqli_num_fields($result) ; $j++) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
    echo "</table>";}

// закрываем подключение
mysqli_close($link);
$link = mysqli_connect($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
// выполняем операции с базой данных
if(mysqli_connect_errno()){
echo 'Ошибка в подключении к БД ('.mysqli_connect_errno().'): '. mysqli_connect_error();
exit();
}
?>
</body>
</html>