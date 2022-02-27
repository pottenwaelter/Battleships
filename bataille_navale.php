<?php


	echo '<div id="conteneurGrille">
	<form method="post" action ="index.php?page=bataille">
	<p id="selectTitle">Ajoutez vos bateaux sur la grille</p>
	<select name="selectBoat" id="selectBoat" onchange="chooseBoat()">
	<option value="0" selected> Un de chaque catégorie, les quatres doivent être placés</option>
	<option value="1">Torpilleur (longueur 1 case)</option>
	<option value="2">Sous-marin (longueur 2 cases)</option>
	<option value="3">Croiseur (longueur 3 cases)</option>
	<option value="4">Porte-avions (longueur 4 cases)</option>
	</select>
	</form>
	<div id="addBoatText"></div>
	<table id="grilleBatailleNavale">';
	echo '<div id="playButton"></div>';
	echo '<div id="validateButton"></div>';
	echo '<div id="replayButton"></div>';
	$arrayNomDesCases = array();
	$nomDeCase = 0;
	$indiceAlphabetique = "A";
	$indiceAbscisse = 0;
	$lettreOrdonnee = "A";
	for($i = 0; $i < 11; $i++)
	{
		$indiceNumerique = 1;
		echo '<tr>';
		for($j = 0; $j < 11; $j++)
		{
			if($i == 0 && $j == 0)
			{
				echo '<th>' . '</th>';
			}
			elseif($i == 0 && $j != 0)
			{
				$indiceAbscisse++;
				echo '<th>' . $indiceAbscisse . '</th>';
				
			}
			elseif($i != 0 && $j == 0)
			{
				
				echo '<th>' . $lettreOrdonnee . '</th>';
				$lettreOrdonnee++;
			}
			else
			{
				$nomDeCase = $indiceAlphabetique . $indiceNumerique;
				$indiceNumerique++;
				array_push($arrayNomDesCases, $nomDeCase);
				echo '<td class="unclickable" id="' . $nomDeCase . '" onclick="placeBox(this)"></td>';		
			}
		}
		echo '</tr>';
		if($i != 0)
		{
			$indiceAlphabetique++;
		}
	}
	echo '</table></div>';

?>


<script type="text/javascript">
let wholeGrid = document.getElementsByTagName("td");
let boatClass = document.getElementsByClassName("bateau");
let boatArray = [];
let torpedoLauncher = [], submarine = [], cruiser = [], carrier = [];
let numberOfTries = 0;
let carrierHp = 4;
let cruiserHp = 3;
let submarineHp = 2;
let torpedoLauncherHp = 1;
let player = 1;

function chooseBoat()
{
	document.getElementById("validateButton").innerHTML = "";
	boatOptions = document.getElementById("selectBoat");		
	if(boatOptions.value == 1)
	{
		document.getElementById("addBoatText").innerHTML = "Choisissez la position du torpilleur sur la grille";
		for(let i = 0; i < wholeGrid.length; i++)
		{
			if(wholeGrid[i].className != "bateau")
			{
				wholeGrid[i].setAttribute("class", "eau");
			}
		}
	}
	else if(boatOptions.value == 2)
	{
		document.getElementById("addBoatText").innerHTML = "Choisissez la position du sous-marin sur la grille";
		for(let i = 0; i < wholeGrid.length; i++)
		{
				wholeGrid[i].setAttribute("class", "eau");
		}
	}
	else if(boatOptions.value == 3)
	{
		document.getElementById("addBoatText").innerHTML = "Choisissez la position du croiseur sur la grille";
		for(let i = 0; i < wholeGrid.length; i++)
		{
				wholeGrid[i].setAttribute("class", "eau");
		}
	}
	else if(boatOptions.value == 4)
	{
		document.getElementById("addBoatText").innerHTML = "Choisissez la position du porte-avions sur la grille";
		for(let i = 0; i < wholeGrid.length; i++)
		{
				wholeGrid[i].setAttribute("class", "eau");
		}
	}
	else
	{
		for(let i = 0; i < wholeGrid.length; i++)
		{
			wholeGrid[i].setAttribute("class", "unclickable")
		}
	}
	return boatOptions.value;
}


function placeBox(td)
{
	let boxId = td.id;
	let previousBoxChars;
	let lastColumnPrevious;
	let boxChars = boxId.split('');
	let lastColumn;
	if(boxChars[2]) // si on se trouve sur la 10e colonne, le split renvoie 3 caractères
	{
		lastColumn = parseInt(boxChars[1] + boxChars[2]); // donc pour faire correspondre l'ID on transforme les deux strings "1" et "0" en int 10
		boxChars.splice(1, 2, lastColumn);
	}
	let boxAbove = previousCharacter(boxChars[0]) + boxChars[1];
	let boxBelow = nextCharacter(boxChars[0]) + boxChars[1];
	let rightFigure = parseInt(boxChars[1]) + 1;
	let leftFigure = parseInt(boxChars[1]) - 1;
	let boxRight = boxChars[0] + rightFigure;
	let boxLeft = boxChars[0] + leftFigure;
	if(td.className == "eau" || td.className == "available") // Condition qui change la classe css onclick et applique l'effet désiré
	{
		td.className = "bateau";
	}
	else
	{
		td.className = "eau";
	}

	if(td.className == "bateau") // Si un bloc bateau est posé
	{			
		if(boatClass.length == 1 && boatOptions.value != 1) // Si la longueur du bateau est d'une case ET si le bateau à placer contient plus d'une case
		{
			if(boxChars[1] == 10) // cas où on se trouve sur la 10e colonne
			{
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].id == boxBelow && wholeGrid[i].className != "bateau") // seule la colonne peut devenir disponible pour poser un bateau
					{
						wholeGrid[i].setAttribute("class", "available"); 
					}
					else if(wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "unclickable"); 
					}
				}
			}
			else
			{
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if((wholeGrid[i].id == boxBelow || wholeGrid[i].id == boxRight) && wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "available"); // On attribue la classe available qui permet de continuer à ajouter des blocs de bateau dans 2 directions spécifiées, vers le bas ou vers la droite
					}
					else if(wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "unclickable"); // Et on rend le reste de la grille non cliquable pour qu'on ne puisse pas ajouter des blocs bateau partout
					}
				}
			}
		}
		else if(boatClass.length == 1 && boatOptions.value == 1) // Par contre si on a choisi de poser un bateau d'une case
		{
			if(td.className == "bateau")
			{
				td.className = "validatedTorpedoBoat";			// On applique une nouvelle classe qui désigne le bateau validé
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].className != "validatedTorpedoBoat")
					{
						wholeGrid[i].className = "unclickable";
					}
				}
			}
			document.getElementById("validateButton").innerHTML = "<button type='button' onclick='validateBoat()'>Valider la position</button>"; 
		}																// et on fait apparaître un bouton qui appelle une nouvelle fonction pour garder en mémoire le bateau
		else if(boatClass.length == 2 && boatOptions.value != 2) // Même procédé pour chaque longueur de bateau
		{
			previousBoxChars = boatClass[0].id.split('');
			if(previousBoxChars[2])
			{
				lastColumnPrevious = parseInt(previousBoxChars[1] + previousBoxChars[2]);
				previousBoxChars.splice(1, 2, lastColumnPrevious);
			}
			if(boxChars[0] == previousBoxChars[0] && boxChars[1] != previousBoxChars[1])
			{
				if(boxChars[1] == 10)
				{
					alert("Bord de la grille atteint, placez votre bateau autrement !");
					for(let i = 0; i < wholeGrid.length; i++)
					{
						wholeGrid[i].setAttribute("class", "unclickable");
					}
				}
				else
				{
					for(let i = 0; i < wholeGrid.length; i++)
					{
						if(wholeGrid[i].id == boxRight && wholeGrid[i].className != "bateau")
						{
							wholeGrid[i].setAttribute("class", "available");
						}
						else if(wholeGrid[i].className != "bateau")
						{
							wholeGrid[i].setAttribute("class", "unclickable");
						}
					}
				}
			}
			else if(boxChars[1] == previousBoxChars[1] && boxChars[0] != previousBoxChars[0])
			{
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].id == boxBelow && wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "available");
					}
					else if(wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "unclickable");
					}
				}
			}
		}
		else if(boatClass.length == 2 && boatOptions.value == 2)
		{
			if(td.className == "bateau")
			{
				td.className = "validatedSubmarine";
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].className != "validatedSubmarine" && wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].className = "unclickable";
					}
				}
			}
			if(document.getElementsByClassName("bateau"))
			{
				document.getElementsByClassName("bateau")[0].setAttribute("class", "validatedSubmarine");
				if(document.getElementsByClassName("validatedSubmarine").length == 2)
				{
					document.getElementById("validateButton").innerHTML = "<button type='button' onclick='validateBoat()'>Valider la position</button>";
				}
			}
		}
		else if(boatClass.length == 3 && boatOptions.value != 3)
		{
			previousBoxChars = boatClass[1].id.split('');
			if(previousBoxChars[2])
			{
				lastColumnPrevious = parseInt(previousBoxChars[1] + previousBoxChars[2]);
				previousBoxChars.splice(1, 2, lastColumnPrevious);
			}
			if(boxChars[0] == previousBoxChars[0] && boxChars[1] != previousBoxChars[1])
			{
				if(boxChars[1] == 10)
				{
					alert("Bord de la grille atteint, placez votre bateau autrement !");
					for(let i = 0; i < wholeGrid.length; i++)
					{
						wholeGrid[i].setAttribute("class", "unclickable");
					}
				}
				else
				{
					for(let i = 0; i < wholeGrid.length; i++)
					{
						if(wholeGrid[i].id == boxRight && wholeGrid[i].className != "bateau")
						{
							wholeGrid[i].setAttribute("class", "available");
						}
						else if(wholeGrid[i].className != "bateau")
						{
							wholeGrid[i].setAttribute("class", "unclickable");
						}
					}
				}
			}
			else if(boxChars[1] == previousBoxChars[1] && boxChars[0] != previousBoxChars[0])
			{
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].id == boxBelow && wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "available");
					}
					else if(wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].setAttribute("class", "unclickable");
					}
				}
			}
		}
		else if(boatClass.length == 3 && boatOptions.value == 3)
		{
			if(td.className == "bateau")
			{
				td.className = "validatedCruiser";
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].className != "validatedCruiser" && wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].className = "unclickable";
					}
				}
			}
			if(document.getElementsByClassName("bateau"))
			{
				for(let i = 0; i < 2; i++)
				{
					document.getElementsByClassName("bateau")[0].setAttribute("class", "validatedCruiser");
				}
				if(document.getElementsByClassName("validatedCruiser").length == 3)
				{
					document.getElementById("validateButton").innerHTML = "<button type='button' onclick='validateBoat()'>Valider la position</button>";
				}
			}
		}
		else if(boatClass.length == 4 && boatOptions.value == 4)
		{
			if(td.className == "bateau")
			{
				td.className = "validatedCarrier";
				for(let i = 0; i < wholeGrid.length; i++)
				{
					if(wholeGrid[i].className != "validatedCarrier" && wholeGrid[i].className != "bateau")
					{
						wholeGrid[i].className = "unclickable";
					}
				}
			}
			if(document.getElementsByClassName("bateau"))
			{
				for(let i = 0; i < 3; i++)
				{
					document.getElementsByClassName("bateau")[0].setAttribute("class", "validatedCarrier");
				}
				if(document.getElementsByClassName("validatedCarrier").length == 4)
				{
					document.getElementById("validateButton").innerHTML = "<button type='button' onclick='validateBoat()'>Valider la position</button>";
				}
			}
		}			
	}
}


function previousCharacter(c) 
{
	return String.fromCharCode(c.charCodeAt(0) - 1);
}


function nextCharacter(c)
{
	return String.fromCharCode(c.charCodeAt(0) + 1);
}
	

function validateBoat()
{
	console.log(torpedoLauncher);
	console.log(submarine);
	console.log(cruiser);
	console.log(carrier);
	if(boatOptions.value == 1) // Pour chaque valeur de longueur de bateau, des vérifications
	{
		let getTorpedoBoatId = document.getElementsByClassName("validatedTorpedoBoat")[0].getAttribute("id");

		if(submarine.includes(getTorpedoBoatId) || cruiser.includes(getTorpedoBoatId) || carrier.includes(getTorpedoBoatId))
		{
			alert("Les bateaux ne peuvent pas se superposer !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else if(torpedoLauncher.length > 0)
		{
			alert("Le torpilleur est déjà placé, choisissez un autre bateau !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else
		{
			torpedoLauncher.push(getTorpedoBoatId);
			alert("Le torpilleur a bien été placé !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		document.getElementById("validateButton").innerHTML = "";
	}
	else if(boatOptions.value == 2)
	{
		let submarineId1 = document.getElementsByClassName("validatedSubmarine")[0].id;
		let submarineId2 = document.getElementsByClassName("validatedSubmarine")[1].id;
		if(torpedoLauncher.includes(submarineId1) || torpedoLauncher.includes(submarineId2) || cruiser.includes(submarineId1) || cruiser.includes(submarineId2) || carrier.includes(submarineId1) || carrier.includes(submarineId2))
		{
			alert("Les bateaux ne peuvent pas se superposer !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else if(submarine.length > 0)
		{
			alert("Le sous-marin est déjà placé, choisissez un autre bateau !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else
		{
			submarine.push(submarineId1, submarineId2);
			alert("Le sous-marin a bien été placé !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		document.getElementById("validateButton").innerHTML = "";			
	}
	else if(boatOptions.value == 3)
	{
		let cruiserId1 = document.getElementsByClassName("validatedCruiser")[0].id;
		let cruiserId2 = document.getElementsByClassName("validatedCruiser")[1].id;
		let cruiserId3 = document.getElementsByClassName("validatedCruiser")[2].id;
		if(torpedoLauncher.includes(cruiserId1) || torpedoLauncher.includes(cruiserId2) || torpedoLauncher.includes(cruiserId3) || submarine.includes(cruiserId1) || submarine.includes(cruiserId2) || submarine.includes(cruiserId3) || carrier.includes(cruiserId1) || carrier.includes(cruiserId2) || carrier.includes(cruiserId3))
		{
			alert("Les bateaux ne peuvent pas se superposer !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}			
		}
		else if(cruiser.length > 0)
		{
			alert("Le croiseur est déjà placé, choisissez un autre bateau !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else
		{
			cruiser.push(cruiserId1, cruiserId2, cruiserId3);
			alert("Le croiseur a bien été placé !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		document.getElementById("validateButton").innerHTML = "";
	}
	else if(boatOptions.value == 4)
	{
		let carrierId1 = document.getElementsByClassName("validatedCarrier")[0].id;
		let carrierId2 = document.getElementsByClassName("validatedCarrier")[1].id;
		let carrierId3 = document.getElementsByClassName("validatedCarrier")[2].id;
		let carrierId4 = document.getElementsByClassName("validatedCarrier")[3].id;
		if(torpedoLauncher.includes(carrierId1) || torpedoLauncher.includes(carrierId2) || torpedoLauncher.includes(carrierId3) || torpedoLauncher.includes(carrierId4) || submarine.includes(carrierId1) || submarine.includes(carrierId2) || submarine.includes(carrierId3) || submarine.includes(carrierId4) || cruiser.includes(carrierId1) || cruiser.includes(carrierId2) || cruiser.includes(carrierId3) || cruiser.includes(carrierId4))
		{
			alert("Les bateaux ne peuvent pas se superposer !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else if(carrier.length > 0)
		{
			alert("Le porte-avions a déjà été placé, choisissez un autre bateau !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		else
		{
			carrier.push(carrierId1, carrierId2, carrierId3, carrierId4);
			alert("Le porte-avions a bien été placé !");
			for(let i = 0; i < wholeGrid.length; i++)
			{
				wholeGrid[i].setAttribute("class", "unclickable");
			}
		}
		document.getElementById("validateButton").innerHTML = "";
	}
	allBoatsReady();
}

function allBoatsReady()
{
	if(torpedoLauncher.length > 0 && submarine.length > 0 && cruiser.length > 0 && carrier.length > 0)
	{
		alert("Tous les bateaux ont été placés !");
		/*if(player == 1) TO DO : MODE 2 JOUEURS
		{
			document.getElementById("playButton").innerHTML = "<button type='button' onclick='startGame()'> Joueur 2, placez vos bateaux !</button>";
		}*/
		document.getElementById("playButton").innerHTML = "<button type='button' onclick='startGame()'> Commencez la partie !</button>";
	}
}


function startGame()
{
	document.getElementById("playButton").innerHTML = "";
	document.getElementById("selectTitle").innerHTML = "Trouvez les bateaux ennemis !";
	document.getElementById("addBoatText").innerHTML = "";
	document.getElementById("selectBoat").setAttribute("hidden", true);
	document.getElementsByTagName("td")
	for(let i = 0; i < 100; i++)
	{
		document.getElementsByTagName("td")[i].setAttribute("class", "eau");
		document.getElementsByTagName("td")[i].setAttribute("onclick", "playerTurn(this)");
	}
}

function playerTurn(td)
{
	numberOfTries++;
	if(!carrier.includes(td.id) && !cruiser.includes(td.id) && !submarine.includes(td.id) && !torpedoLauncher.includes(td.id))
	{
		td.className = "missedShot";
		td.innerHTML = "X";
	}
	else
	{
		if(carrier.includes(td.id))
		{
			carrierHp--;
			document.getElementById(td.id).setAttribute("class", "successfulShot");
			if(carrierHp == 0)
			{
				alert("Porte-avions coulé !");
			}
		}
		else if(cruiser.includes(td.id))
		{
			cruiserHp--;
			document.getElementById(td.id).setAttribute("class", "successfulShot");
			if(cruiserHp == 0)
			{
				alert("Croiseur coulé !");
			}
		}
		else if(submarine.includes(td.id))
		{
			submarineHp--;
			document.getElementById(td.id).setAttribute("class", "successfulShot");
			if(submarineHp == 0)
			{
				alert("Sous-marin coulé !");
			}
		}
		else if(torpedoLauncher.includes(td.id))
		{
			torpedoLauncherHp--;
			document.getElementById(td.id).setAttribute("class", "successfulShot");
			if(torpedoLauncherHp == 0)
			{
				alert("Torpilleur coulé !");
			}
		}
	}
	if(torpedoLauncherHp == 0 && submarineHp == 0 && cruiserHp == 0 && carrierHp == 0)
	{
		alert("Bravo ! Vous avez coulé tous les navires ennemis en " + numberOfTries + " essais !");
		for(let i = 0; i < 100; i++)
		{
			document.getElementById("replayButton").innerHTML = "<button type='button' onclick='newGame()'>Refaire une partie</button>"
		}
	}
}


function newGame()
{
	document.getElementById("replayButton").innerHTML = "";
	document.getElementById("selectTitle").innerHTML = "Ajoutez vos bateaux sur la grille";
	document.getElementById("selectBoat").hidden = false;
	document.getElementsByTagName("td")
	for(let i = 0; i < 100; i++)
	{
		document.getElementsByTagName("td")[i].setAttribute("class", "eau");
		document.getElementsByTagName("td")[i].setAttribute("onclick", "placeBox(this)");
		document.getElementsByTagName("td")[i].innerHTML = "";
	}
	torpedoLauncher = [];
	torpedoLauncherHp = 1;
	submarine = [];
	submarineHp = 2;
	cruiser = [];
	cruiserHp = 3;
	carrier = [];
	carrierHp = 4;
	numberOfTries = 0;
}
</script>








	

