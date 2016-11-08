<?php

	require("/home/karlkruu/config.php");
	
	
	//ühendus
	$database="if16_karlkruu";
	$mysqli=new mysqli($serverHost, $serverUsername, $serverPassword, $database);
	
	//klassid
	require("class/User.class.php");
	$User=new User($mysqli);
	
	require("class/Interest.class.php");
	$Interest=new Interest($mysqli);
	
	require("class/Car.class.php");
	$Car=new Car($mysqli);
	
	require("class/Helper.class.php");
	$Helper=new Helper($mysqli);
	
	
	//see fail peab olema kõigil lehtedel kus tahan kasutada SESSION muutujat
	session_start();
	

?>