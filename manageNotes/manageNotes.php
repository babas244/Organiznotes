<?php 
session_start();

require '../log_in_bdd.php';

require '../sessionAuthentication.php';

// il faut vérifier que idTopic est de la bonne forme
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Organiznote</title>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
		<meta name="robots" content="noindex,nofollow">
		<link rel="stylesheet" href="dataTree.css" />
		<link rel="stylesheet" href="toDoList.css" />
		<link rel="stylesheet" href="superFormModale.css" />
    </head>
    <body>
		<!-- -->
		<div id="header">
			<?php
				$reqGetTopic = $bdd -> prepare('SELECT topic FROM topics WHERE idUser=:idUser AND id=:idTopic');
					$reqGetTopic -> execute(array(
					'idUser' => $_SESSION['id'],
					'idTopic' => $_GET['idTopic']));
					$resultat = $reqGetTopic -> fetch();
				$topic = $resultat['topic'];
				if ($reqGetTopic->rowCount() == 0) {
					header("Location: ../logout.php");
				}
			$reqGetTopic -> closeCursor();

				echo "Bonjour <strong>".$_SESSION['user']."</strong>, vous êtes connecté sur le topic : ".$topic;
			?>
			<a href="../logout.php">(se déconnecter)</a>.
			<div id="frameOfSwitchToTreeForMobile">
				<button id="displayAndHideTree">V
				</button>
			</div>
		</div>
		
		<div id="containerOfToDo">
			<div id="frameOfToDo">
				<div id="noScroll">
					<div id="greyLayerOnNoScroll">
					</div>
					<button id="addToDoButton">+</button>			
						<div id="addToDoFrame">
							<button id="cancelAddToDo"><-</button>
							<div id="frameTextareaToDoForm">
								<form id="addToDoForm">
									<input type="textarea" name="toDoTextarea" id="toDoTextarea" placeholder="Ecrire ici" maxlength="1700">
								</form>		
							</div>
							<button id="resetAddToDoForm">x</button>	
						</div>
					<div id="lastAndInvisible">...</div>
				</div>
			</div>
			<div id="frameOfContextMenuToDo">
				<button id="cancelContextMenu">&lt;-</button>	
				<button id="deleteToDo">X</button>
				<button id="StatedToDoDone">7</button>
				<button id="editToDo">Edit</button>
			</div>
		</div>
		
		<div id="containerOfTree">
			<div id="frameOfTree">
				<div id="01" class="folder">   <!--div "racine", à mettre dans dataTree.js ?--> 
				</div>
				<div id="menu">
					<ul class="Niveau1">
						<li>
							<button>Nouveau</button>
							<ul class="Niveau2">
									<li><button>à faire</button></li>
									<li><button>catégorie à faire</button></li>
									<li><button id="NouvelleNote">Référence</button></li>
									<li><button>catégorie référence</button></li>
							</ul>
						</li>
						<li>
							<button>Recherche</button>
							<ul class="Niveau2">
								<li><button>référence</button></li>
								<li><button>catégorie</button></li>
								<li><button>référence +catégorie</button></li>
							</ul>	
						</li>
						<li>
							<button>Affichage</button>
							<ul class="Niveau2">
								<li><button id="displayAllTree">tout</button></li>
								<li><button>fait</button></li>
								<li><button>arborescence</button></li>
								<li><button>archives</button></li>
							</ul>	
						</li>
						<li>
							<button>Naviguer</button>
							<ul class="Niveau2">
								<li><button>déplacer des blocs</button></li>
								<li><button id="importerXML">importer xml</button></li>
								<li><button>exporter en xml</button></li>
							</ul>	
						</li>
						<li>
							<button>Historique</button>
							<ul class="Niveau2">
								<li><button>&lt;-</button></li>
								<li><button>-></button></li>
								<li><button>3 dernières</button></li>
								<li><button>10 dernières</button></li>
								<li><button>24h</button></li>
							</ul>	
						</li>
					</ul>
				</div>
			</div>
			<div id="fondMenuCategorie">
				<div id="insertNewFolder" class="contextMenu isRoot isFolder">Nouvelle catégorie fille</div>		
				<div id="insertNewNote" class="contextMenu isRoot isFolder">Nouvelle note</div>
				<div id="editNote" class="contextMenu isFolder isNote">Editer</div>	
				<div id="deleteFolder" class="contextMenu isFolder">Effacer catégorie</div>
				<div id="deleteNote" class="contextMenu isNote">Effacer note</div>
				<div id="archiveNote" class="contextMenu isFolder isNote">Archiver(maintenant/date (choisie))</div>
				<div id="moveTreeItem" class="contextMenu isFolder isNote">Déplacer</div>
				<div id="pasteHereTreeItem" class="contextMenu isPastingHere">Coller ici</div>
				<div id="DisplayContentFolder" class="contextMenu isRoot isFolder">Afficher l'arbre contenu dedans</div>
				<div id="changeCategoryIntoNote" class="contextMenu isFolder">Transformer catégorie en note</div>							
				<div id="changeNoteIntoCategory" class="contextMenu isNote">Transformer note en catégorie</div>
				<div id="getOutFromHere" class="contextMenu isPastingHere isCancel">Sortir d'ici</div>
				<div id="cancel" class="contextMenu isRoot isFolder isNote isPastingHere isCancel">Annuler</div>
			</div>
		</div>
		<br/>
		
		<div id="fondPageEntrerTexte">
			<div id="textBox">
				<form id="formulaireEntrerNote">
					<textarea name="zoneFormulaireEntrerNote" id="zoneFormulaireEntrerNote" placeholder="Ecrire ici"></textarea>
				</form>
			</div>
			<button id="enregistrerNouvelleNote">Enregistrer</button>
			<button id="reinitialiserFormulaireEntrerNote">Réinitialiser</button>
			<button id="annulerEntrerNote">Annuler</button>
		</div>
		
		
		<input id="chargerfichierXML" type="file" />
		
		<script>
			<?php
			echo "var idUser = ".$_SESSION['id'].";"; 
			echo "var idTopic = ". $_GET['idTopic'].";";
			?>
		</script>
		
		<script src="superFormModale.js"></script>
		<script src="toDoList.js"></script>
		<script src="dataTree.js"></script>	
	</body>
</html> 