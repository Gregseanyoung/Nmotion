<?php
//print_r($_REQUEST);

$orderId=$_POST['orderId'];

/*$db = new PDO('mysql:host=192.168.1.17;dbname=nmotion;charset=utf8', 'root', 'admin41**00');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
*/

//$link = mysql_connect('localhost', 'root', 'root');
$link = mysql_connect('localhost', 'nmotion', 'NSnkun0REd');
mysql_select_db('nmotion', $link);
mysql_set_charset('UTF-8', $link);

header('Content-Type: application/json; Charset=UTF-8');

$sqlGetProduct="SELECT OM.id,OM.name,OM.price, OM.quantity, OM.meal_comment, CU.sign as currency_sign, OM.meal_option_name, OM.meal_option_price
FROM nmtn_order O
LEFT JOIN nmtn_order_meal OM ON O.id = OM.order_id
LEFT JOIN nmtn_restaurant R ON R.id = O.restaurant_id
LEFT JOIN nmtn_currency CU ON R.currency_id = CU.id
where OM.order_id = '".$_POST['orderId']."' order by OM.name asc" ;

$exec=mysql_query($sqlGetProduct,$link);

//print("<pre>");
$product=array();
$i=0;
while($row = mysql_fetch_array($exec, MYSQL_ASSOC)){
	//print_r($row); htmlentities($row['name'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["name"] =htmlentities($row['name'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["price"] =htmlentities($row['price'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["currency_sign"] =htmlentities($row['currency_sign'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["quantity"] =htmlentities($row['quantity'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["meal_comment"] =htmlentities($row['meal_comment'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["meal_option_name"] =htmlentities($row['meal_option_name'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
	$product[$i]["meal_option_price"] =htmlentities($row['meal_option_price'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");


				
	$extaIndi="SELECT *
FROM `nmtn_order_meal_extra_ingredient`
WHERE order_meal_id =".$row['id'];

	$exec1=mysql_query($extaIndi,$link);
	$j=0;
	 while($row1 = mysql_fetch_array($exec1, MYSQL_ASSOC)){
		$product[$i]['extra'][$j]['name']=htmlentities($row1['name'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
		$product[$i]['extra'][$j]['price']=htmlentities($row1['price'], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-15");
$j++;
	}
	$i++;
}




//$returnData = array_map('utf8_encode', $returnData);
echo json_encode($product, JSON_UNESCAPED_UNICODE);
exit;
?>
