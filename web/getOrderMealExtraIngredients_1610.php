<?php
//print_r($_REQUEST);
$orderId=$_POST['orderId'];
/*$db = new PDO('mysql:host=192.168.1.17;dbname=nmotion;charset=utf8', 'root', 'admin41**00');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
*/

//$link = mysql_connect('192.168.1.17', 'root', 'admin41**00');
$link = mysql_connect('localhost', 'nmotion', 'NSnkun0REd');
mysql_select_db('nmotion', $link);
//mysql_set_charset('UTF-8', $link);


$query = "SELECT MEI. * , CU . * 
FROM nmtn_order O
LEFT JOIN nmtn_order_meal OM ON O.id = OM.order_id
LEFT JOIN nmtn_restaurant R ON R.id = O.restaurant_id
LEFT JOIN nmtn_currency CU ON R.currency_id = CU.id
LEFT JOIN nmtn_order_meal_extra_ingredient OMEI ON OMEI.order_meal_id = OM.id
LEFT JOIN nmtn_meal_extra_ingredient MEI ON OMEI.meal_extra_ingredient_id = MEI.id
LEFT JOIN nmtn_meal_option MO ON MO.meal_id = OM.id
LEFT JOIN nmtn_user U ON U.id = O.user_id
WHERE O.id ='".$_POST['orderId']."' and  MEI.id !='' group by MEI.id";

//print_r($link);
//print($query);
$returnData=array();
$i=0;
$exec=mysql_query($query,$link);
//$row=mysql_fetch_assoc($exec);
 while($row = mysql_fetch_array($exec, MYSQL_ASSOC)){
    $returnData[$i]['name']=$row['name'];
    $returnData[$i]['price']=$row['price'];
    $returnData[$i]['currency_sign']=$row['sign'];
    $i++;
}

echo json_encode($returnData);
exit;
?>
