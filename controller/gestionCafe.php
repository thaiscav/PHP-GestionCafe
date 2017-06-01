<?php
echo '<meta charset="UTF-8">';

function openFich(&$fic,&$ligne){

	if(!$fic=fopen("../donnees/programmeurs.txt","r")){
		echo "Impossible d'ouvrir le fichier";
		exit;
	}

	$ligne=fgets($fic);

}//fin openFich

function buildArray(&$taille,&$table){

	$table=array();
	$taille=0;
	$i = 0;

	//Fichier
	if(!$fic=fopen("../donnees/programmeurs.txt","r")){
		echo "Impossible d'ouvrir le fichier";
		exit;
	}

	$ligne=fgets($fic);
	$tabNom=array();

	//Fichier read and array building
	while(!feof($fic)){

		$tab=explode(";",$ligne);	 
		$tabNom[]=$tab[0];
		
		//Array nomal
		$table[$i][0] = $tab[0]; //00
		$table[$i][1] = $tab[1]; //01
		$table[$i][2] = $tab[2]; //02

		//echo $table[$i][0].' - '.$table[$i][1].' - '.$table[$i][2].'<br>';	

		$i++;		
		$ligne=fgets($fic);
	}	

	$taille = count($tabNom);		//echo $taille;
	fclose($fic);							
}//fin listerTable

function buildArray_kv(&$taille,&$table){

	$table=array();
	$taille=0;
	$i = 0;

	//File open
	if(!$fic=fopen("../donnees/programmeurs.txt","r")){
		echo "Impossible d'ouvrir le fichier";
		exit;
	}

	$ligne=fgets($fic);
	$tabNom=array();

	//Fichier read and array building
	while(!feof($fic)){

		$tab=explode(";",$ligne);	 
		$tabNom[]=$tab[0];

		//TABLEAUX ASSOCIATIF
		$table[$i]['nom'] = $tab[0];
		$table[$i]['jour'] = $tab[1];
		$table[$i]['qnt'] = $tab[2];
				
		//echo $table[$i]['nom'].' - '.$table[$i]['jour'].' - '.$table[$i]['qnt'].'<br>';	

		$i++;		
		$ligne=fgets($fic);
	}	

	//print_r($table);
	$taille = count($tabNom);//echo $taille;
	fclose($fic);			

	return $table;

}//fin listerTable

function aficherArray($table){

	//echo $table[$key].' - '.$table[$key].' - '.$tabltableeGroup[$key].'<br>';
	//Testar com um FOR

	foreach($table as $key => $value){

		foreach($value as $item){
			
		echo $item.' | ';

		}
		
		echo '<br>';

	}

}//readArray

function listerTout(){//mostrar o table associativo com key value

	openFich($fic,$ligne);

	$rep="<h3>Rapport hebdomadaire de consumation:</h3>";
	$rep.="<table class='table table-striped'>";
	$rep.="<tr><th>Nom</th><th>Jour</th><th>Nb Tasses</th></tr>";

	while(!feof($fic)){

		$tab=explode(";",$ligne);
		$rep.="<tr><td>".$tab[0]."</td><td>".$tab[1]."</td><td>".$tab[2]."</td></tr>";
		$ligne=fgets($fic);
	}

	$rep.="</table>";
	fclose($fic);	
	
	echo $rep;

}//fin listerTout

function listerConsParJourSem($jour){
	
	openFich($fic,$ligne);

	$rep="<h3>Liste de tous les programmeurs qui ont consommé des tasses de café ".$jour.":</h3>";
	$rep.="<table class='table table-striped'><tr><th>Nom</th></tr>";

	while(!feof($fic)){
		$tab=explode(";",$ligne);

		if ($tab[1] == $jour){
			$rep.="<tr><td>".$tab[0]."</td></tr>";
		}
		
		$ligne=fgets($fic);

	}
	$rep.="</table>";
	fclose($fic);

	echo $rep;

}//fin listerParJourSem

function listerPlusGrandCons(){

	agrouperParNom($tableGroup,null);

	usort($tableGroup,function($a,$b){
		$c = $b['qnt'] - $a['qnt'];
		return $c;
	});

	/*
	asort($tableGroup);//asc val
	arsort($tableGroup);//desc val
	ksort($tableGroup);//asc key
	krsort($tableGroup);//desc key
	*/
	echo '<h3>Le programmeur qui a consommé le plus de tasses de café était : '.$tableGroup[0]['nom'].'</h3>';	
	
	aficherArray($tableGroup);
	//print_r($tableGroup);

}//fin listerPlusGrandCons

function listerMoyenneParJourSem(){
	
	agrouperParJour($tableGroup);

	echo '<h3>La moyenne de tasses de café consommés par jour de la semaine est:</h3>';	

	aficherArray($tableGroup);
	//print_r($tableGroup);
	//echo $tableGroup[0]['jour']; //=LUNDI
		
}//fin listerMoyenneParJourSem

function listerTassesParProg($nom){

	agrouperParNom($tableGroup,$nom);

	//print_r($tableGroup);

	echo '<h3>Le nombre de tasses consommées de '.$nom.' est: </h3>';	

	aficherArray($tableGroup);

}//listerTassesParProg

function listerJourParMoy(){

	agrouperParJour($tableGroup);

	usort($tableGroup,function($a,$b){
		$c = $b['moy'] - $a['moy'];
		return $c;
	});

	/*
	asort($tableGroup);//asc val
	arsort($tableGroup);//desc val
	ksort($tableGroup);//asc key
	krsort($tableGroup);//desc key
	*/

	echo '<h3>Le jour de la semaine qui a la moyenne de consommation la plus élevée est: '.$tableGroup[0]['jour'].'</h3>';	

	//print_r($tableGroup);
	aficherArray($tableGroup);

}//fin listerJourParMoy

function agrouperParNom(&$tableGroup,$nomDonne){//$nom

	buildArray_kv($taille,$table);
	$tableGroup = array();
	
	//se tiver nome, procurar por nome e agrupar apenas esse nome

	//se nao tiver nome, agrupar todos os nomes = < processo normal

	//percorre a tabela para pegar o nome
	for($i = 0 ; $i < $taille  ; $i++){
	
		$total = 0;
		$count = 0;
		$nom = $table[$i]['nom'];
		if($nomDonne != null){
			$nom = $nomDonne;
		}

		//Percorre a tabela para comparar
		for($j = 0 ; $j < $taille ; $j++){

			//Por nome 

			if ($nom === $table[$j]['nom']){

				$total += $table[$j]['qnt'];

				$count ++;

					//Calculer Moyenne
					$moy = $total / $count;
			}
		}

		$tableGroup[$i]['nom'] = $nom;
		$tableGroup[$i]['qnt'] = $total;
		//$tableGroup[$i]['moy'] = $moy;
	
	}

	//Refina
	unique($tableGroup,'nom');
	//print_r($tableGroup);
	return $tableGroup;

}//fin agrouper

function agrouperParJour(&$tableGroup){

	buildArray_kv($taille,$table);

	$tableGroup = array();

	//percorre a tabela para pegar o nome
	for($i = 0 ; $i < $taille  ; $i++){
		
		$jour = $table[$i]['jour'];
		$total = 0;
		$count = 0;

		//Percorre a tabela para comparar
		for($j = 0 ; $j < $taille ; $j++){

			//Por nome 

				if ($jour === $table[$j]['jour']){

					$total += $table[$j]['qnt'];
					$count ++;

					//Calculer Moyenne
					$moy = $total / $count;
				}
		}

		$tableGroup[$i]['jour'] = $jour;
		$tableGroup[$i]['qnt'] = $total;
		$tableGroup[$i]['moy'] = $moy;

	}	

	//Refina
	unique($tableGroup,'jour');
	//print_r($tableGroup);
	return $tableGroup;
			
}//fin agrouperParJour

function unique(&$array,$key){

   $temp_array = array();

   foreach ($array as &$v) {

       if (!isset($temp_array[$v[$key]]))

       $temp_array[$v[$key]] =& $v;

   }

   $array = array_values($temp_array);

   return $array;
}//fin unique

//Controleur

$action = $_POST['action'];

switch($action){
	case "tout";
		listerTout();
	break;
	case "cons_jour":
		$jour = $_REQUEST['jourSem'];
		if ($jour == null){
			header('Location: ../index.html');
			exit;
		}
		listerConsParJourSem($jour);
	break;
	case "plusGrandCons":
		listerPlusGrandCons();
	break;
	case "moy_jour":
		listerMoyenneParJourSem();
	break;
	case "tasse_prog":
		$nom = $_REQUEST['nom'];
		if ($nom == null){
			header('Location: ../index.html');
			exit;
		}
		listerTassesParProg($nom);
	break;
	case "jour_moy":
		listerJourParMoy();
	break;
}
?>
<br><br>
<a href="../index.html">Retour a la page d'accueil</a>

<!--
	while(!feof($fic)){

		$tab=explode(";",$ligne);	
		$ligne=fgets($fic);

		//Separa informacaoes
		$tabNom[]=$tab[0];
		$tabJour[]=$tab[1];
		$tabQnt[]=$tab[2];
	}
		
	for($i = 0 ; $i < count($tabNom)  ; $i++){
		
		$totalParNom = 0;

		for($j = 0 ; $j < count($tabNom) ; $j++){

			if ($tabNom[$i] == $tabNom[$j]){

				$totalParNom+=$tabQnt[$j];					
			}
		}
		
		//add nome e qntd no tab
		//$tableGroup[$i][0] = $nom;
		//$tableGroup[$i][2] = $totalParNom;
		
		echo '<br>'.$tabNom[$i].' - '. $totalParNom;
	}	
-->