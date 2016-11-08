<?php
	
	require("../functions.php");
	
	
	//kui ei ole kasutaja id'd
	
	if(!isset ($_SESSION["userId"])){
	
		//suunan sisselogimise lehele 
		header("Location: logi.php");
		exit();
		
	}

	//kui on ?logout aadressireal siis login välja
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: logi.php");
		exit();
	}

	
	$msg="";
	if(isset($_SESSION["message"])){
		$msg=$_SESSION["message"];
		
		//Üks kord näitab, siis pärast värskendamist ei näita
		unset($_SESSION["message"]);
	}
	
	if(isset($_POST["plate"])&&
	isset($_POST["color"])&&
	!empty($_POST["plate"])&&
	!empty($_POST["color"])
	){
		$Car->saveCar($Helper->cleanInput($_POST["plate"]), $_POST["color"]);
	}

	//sorteerib
	if(isset($_GET["sort"]) && (isset($_GET["direction"]))){
		$sort=$_GET["sort"];
		$direction=$_GET["direction"];
	}else{
		//kui pole määratud, siis vaikimisi id ja ascending
		$sort="id";
		$direction="ascending";
	}
	
	
	//saan kõik auto andmed
	if(isset($_GET["q"])){
		
		$q=$Helper->cleanInput($_GET["q"]);
		$carData=$Car->getAllCars($q, $sort, $direction);
	}else{
		$q="";
		$carData=$Car->getAllCars($q, $sort, $direction);
	}
	
	
	//echo "<pre>";
	//var_dump($carData);
	//echo "</pre>";
	
	
	
?>

<?php require("../header.php"); ?>
<div class="container">

<center><h1>Data</h1>





<?=$msg;?>

<p>Tere tulemast <a href="user.php"><?=$_SESSION["userEmail"];?>!</a>
<a href="?logout=1">Logi välja</a>
</p>



<form method="POST">

<h2>Salvesta auto</h2>



<input name="plate" placeholder="123ABC" type="text">
<br><br>
<input name="color" type="color">
<br><br>
<input type="submit" value="Salvesta">

</form>

<h2>Autod</h2>

<form>
	<input type="search" name="q" value="<?=$q;?>" >
	<input type="submit" value="Otsi">
</form>
</div>
<?php require("../footer.php"); ?>



<?php

	$direction="ascending";
	if(isset($_GET["direction"])){
		if($_GET["direction"] == "ascending"){
			$direction = "descending";
		}
	}
	
	$html = "<table class='table table-striped table-bordered table-hover table-condensed'>";
	
	$html.="<tr>";
		$html.="<th><a href='?q=".$q."&sort=id&direction=".$direction."'>id</a></th>";
		$html.="<th><a href='?q=".$q."&sort=plate&direction=".$direction."'>plate</a></th>";
		$html.="<th><a href='?q=".$q."&sort=color&direction=".$direction."'>color</a></th>";
	$html.="</tr>";

	//iga liikme kohta massiivis
	foreach($carData as $c){
		//iga auto on $c
		//echo $c->plate."<br>";
		
		$html.="<tr>";
			$html.="<td>".$c->id."</td>";
			$html.="<td>".$c->plate."</td>";
			$html.="<td style='background-color:".$c->color."'>".$c->color."</td>";
			$html .= "<td><a class='btn btn-default' href='edit.php?id=".$c->id."'><span class='glyphicon glyphicon-pencil'></span></a></td>";
		$html.="</tr>";
		
	}
	
	$html .= "</table>";

	echo $html;
	
	$listHtml="<br><br>";
	foreach($carData as $c){
		
		$listHtml.="<h1 style='color:".$c->color."'>".$c->plate."</h1>";
		$listHtml.="<p>color=".$c->color."</p>";
		
	}
	
	echo $listHtml;
	
		

?>