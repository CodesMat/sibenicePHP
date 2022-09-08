<?php
include_once "conn.php";

 ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
 <div class="col-md-12" style="width: 40%;">
 	<form method="GET">
<h1>Přidat Slovo:</h1>
 		<div class="col-md-12">
 		<input class="form-control" type="text" name="slovo">
 		</div>
<div class="col-md-12">
 		<input placeholder="zebra" style="float:right;margin-top:2%;" type="submit" class="btn btn-info" name="pridat">
 	</div>
 	</form>
 </div>
 <?php
 if (isset($_GET['pridat'])) {
$slovo = $_GET['slovo'];
$slovov=strtoupper($slovo);
$sql = "INSERT INTO slova (slovo) VALUES ('$slovov');";
if ($conn->query($sql) === TRUE) {
	echo "slovo pridano";
}else{echo "už existuje v db";}

 }
?>