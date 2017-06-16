<?php
require_once('rdb/rdb.php');
$conn = r\connect('192.168.1.172', 28015, 'nmotion');

echo "<pre>";

// Insert a document
$document = array('someKey' => 'someValue');
$result = r\table("orders")->insert($document)->run($conn);
echo "Insert result:\n";
print_r($result);

// How many documents are in the table?
$result = r\table("orders")->count()->run($conn);
echo "Count: $result\n";

// List the someKey values of the documents in the table
// (using a mapping-function)
$result = r\table("orders")->map(function($x) {
	return $x('someKey');
})->run($conn);

foreach ($result as $doc) {
	print_r($doc);
}
