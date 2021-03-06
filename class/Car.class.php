<?php
class Car {
	
	private $connection;
	
	function __construct($mysqli){
	
		$this->connection=$mysqli;
	
	}
	
	/*TEISED FUNKTSIOONID*/
	
	function saveCar ($plate, $color) {
		
		$error= "";
		
		$stmt=$this->connection->prepare("INSERT INTO cars_and_colors (plate, color) VALUES (?, ?)");
		
		echo $this->connection->error; 
		
		$stmt->bind_param("ss", $plate, $color);
		
		if($stmt->execute()) {
			
			echo "salvestamine õnnestus";
		
		}else{
			echo "ERROR ".$stmt->error;
		}
	
		$stmt->close();
		
	}

	function getAllCars($q, $sort, $direction) {
		
		//mis sort ja järjekord
		$allowedSortOptions=["id", "plate", "color"]; //ainult need valikud on lubatud (juhul kui keegi peaks URLi hakkama sisestama midagi muud ise)
		
		if(!in_array($sort, $allowedSortOptions)){
			$sort="id";
		}
	
		echo "Sorteerin: ".$sort." ";
		
		$orderBy = "ASC";
		if($direction == "descending"){
			$orderBy = "DESC";
		}
		
		echo "Järjekord: ".$orderBy." ";
		
		if($q==""){
			echo "ei otsi";
			
			$stmt=$this->connection->prepare("
			SELECT id, plate, color
			FROM cars_and_colors
			WHERE deleted IS NULL
			ORDER BY $sort $orderBy
			");
			
		}else{
			echo "Otsib: ".$q;
		
			//teen otsisõna
			//lisan mõlemale poole %
			$searchword="%".$q."%";
			
			$stmt=$this->connection->prepare("
			SELECT id, plate, color
			FROM cars_and_colors
			WHERE deleted IS NULL AND
			(plate LIKE ? OR color LIKE ?)
			ORDER BY $sort $orderBy
			");
			$stmt->bind_param("ss", $searchword, $searchword);
		}
		
		
		$stmt->bind_result($id, $plate, $color);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result=array();
		
		//tee seda seni, kuni on rida andmeid
		//mis vastab select lausele
		while($stmt->fetch()) {
			
			//tekitan objekti
			$car=new StdClass();
			$car->id=$id;
			$car->plate=$plate;
			$car->color=$color;
			
			
			//echo $plate."<br>";
			//iga kord massiivi lisan juuurde numbrimärgi
			array_push($result, $car);
		}
		
		
		
		$stmt->close();
		
		return $result;
		
	}
	
	function getSingleCarData($edit_id){
    
		
		$stmt = $this->connection->prepare("SELECT plate, color FROM cars_and_colors WHERE id=? AND deleted IS NULL");

		$stmt->bind_param("i", $edit_id);
		$stmt->bind_result($plate, $color);
		$stmt->execute();
		
		//tekitan objekti
		$car = new Stdclass();
		
		//saime ühe rea andmeid
		if($stmt->fetch()){
			// saan siin alles kasutada bind_result muutujaid
			$car->plate = $plate;
			$car->color = $color;
			
			
		}else{
			// ei saanud rida andmeid kätte
			// sellist id'd ei ole olemas
			// see rida võib olla kustutatud
			header("Location: data.php");
			exit();
		}
		
		$stmt->close();
		
		return $car;
		
	}

	function updateCar($id, $plate, $color){
		
		$stmt = $this->connection->prepare("UPDATE cars_and_colors SET plate=?, color=? WHERE id=? AND deleted IS NULL");
		$stmt->bind_param("ssi",$plate, $color, $id);
		
		// kas õnnestus salvestada
		if($stmt->execute()){
			// õnnestus
			echo "salvestus õnnestus!";
		}
		
		$stmt->close();
		
	}
	
	function deleteCar($id){
		
		$stmt = $this->connection->prepare("UPDATE cars_and_colors SET deleted=NOW() where id=? AND deleted IS NULL");
		$stmt->bind_param("i",$id);
		
		// kas õnnestus salvestada
		if($stmt->execute()){
			// õnnestus
			echo "kustutamine õnnestus!";
		}
		
		$stmt->close();
		
	}
	
	
	
}?>