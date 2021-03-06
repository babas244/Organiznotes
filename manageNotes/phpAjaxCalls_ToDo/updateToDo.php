<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

if (isset($_SESSION['id'])) {
	if (isset($_GET["idTopic"]) && isset($_POST["toDoContent"]) && isset($_GET["sLabels"]) && isset($_GET["position"]) && isset($_GET["sNewLabels"]) && isset($_GET["dateCreation"])) {
	
		if (preg_match("#^[0-9]{4}$#", $_GET["sLabels"]) && preg_match("#^[0-9]+$#", $_GET["position"]) && preg_match("#^[0-9]{4}$#", $_GET["sNewLabels"]) && preg_match("#^[12][09][0-9]{2}-[01][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$#", $_GET["dateCreation"])) {
			
			require '../../log_in_bdd.php';

			require '../../isIdTopicSafeAndMatchUser.php';

			$idTopic = htmlspecialchars($_GET["idTopic"]);
			$toDoContent = htmlspecialchars($_POST["toDoContent"]);
			$sLabels = htmlspecialchars($_GET["sLabels"]);
			$aLabels = str_split($sLabels);
			$position = htmlspecialchars($_GET["position"]);		
			$dateCreation = htmlspecialchars($_GET["dateCreation"]);		

			$sNewLabels = htmlspecialchars($_GET["sNewLabels"]); // utile ??
			//$aNewLabels = array(); //nécessaire ??
			$aNewLabels = str_split($sNewLabels);

			require 'checkToDoIsTheRightOne.php';
			
			if ($aLabels[0] == $aNewLabels[0] && $aLabels[1] == $aNewLabels[1] && $aLabels[2] == $aNewLabels[2] && $aLabels[3] == $aNewLabels[3]) {
				
				// on n'a donc pas changé de sLabels, on udpate que le content		
				$reqUpdateToDo = $bdd -> prepare('UPDATE todolists SET content=:newNote,dateCreation=:dateCreation
				WHERE Iduser=:idUser AND idTopic=:idTopic AND label0=:label0 AND label1=:label1 AND label2=:label2 AND label3=:label3 AND dateArchive IS NULL AND noteRank=:NoteRank');
					$reqUpdateToDo -> execute(array(
					'idUser' => $_SESSION['id'],
					'idTopic' => $idTopic,
					'newNote' => $toDoContent,
					'dateCreation' => $dateCreation,
					'label0' => $aLabels[0],
					'label1' => $aLabels[1],
					'label2' => $aLabels[2],
					'label3' => $aLabels[3],
					'NoteRank'=> $position)) or die(print_r($reqUpdateToDo->errorInfo()));
				//echo ('<br>'.$reqUpdateToDo->rowCount().' rangs affectés');
				$reqUpdateToDo -> closeCursor();	
			} 
			else { // on a changé de sLabels 
				// Il faut préparer la MAJ des positions : d'abord compter le nombre de toDo avec du nouveau sLabels pour insérer à la fin
				$reqCountToDoNewLabels = $bdd -> prepare("SELECT COUNT(*) AS nbOfToDoNewLabels FROM todolists 
			WHERE idUser=:idUser AND idTopic=:idTopic AND label0=:label0 AND label1=:label1 AND label2=:label2 AND label3=:label3 AND dateArchive IS NULL");
					$reqCountToDoNewLabels -> execute(array(
					'idUser' => $_SESSION['id'],
					'idTopic' => $idTopic,
					'label0' => $aNewLabels[0],
					'label1' => $aNewLabels[1],
					'label2' => $aNewLabels[2],
					'label3' => $aNewLabels[3])) or die(print_r($reqCountToDoNewLabels->errorInfo()));
					//echo ('<br>'.$reqCountToDoNewLabels->rowCount().' rangs affectés');
				while ($data = $reqCountToDoNewLabels->fetch()) {
					$nbOfToDoNewLabels = $data['nbOfToDoNewLabels'];
				}
				$reqCountToDoNewLabels -> closeCursor();
						
				//echo $nbOfToDoNewLabels;
				
				// updater les contenus, avec la nouvelle position
				$reqUpdateToDo = $bdd -> prepare('UPDATE todolists SET noteRank=:noteRank,content=:newNote,label0=:newLabel0,label1=:newLabel1,label2=:newLabel2,label3=:newLabel3,dateCreation=:dateCreation
				WHERE Iduser=:idUser AND idTopic=:idTopic AND label0=:oldLabel0 AND label1=:oldLabel1 AND label2=:oldLabel2 AND label3=:oldLabel3 
				AND dateArchive IS NULL AND noteRank=:oldNoteRank');
					$reqUpdateToDo -> execute(array(
					'idUser' => $_SESSION['id'],
					'idTopic' => $idTopic,
					'noteRank' => $nbOfToDoNewLabels,
					'newNote' => $toDoContent,
					'dateCreation' => $dateCreation,
					'newLabel0' => $aNewLabels[0],
					'newLabel1' => $aNewLabels[1],
					'newLabel2' => $aNewLabels[2],
					'newLabel3' => $aNewLabels[3],
					'oldLabel0' => $aLabels[0],
					'oldLabel1' => $aLabels[1],
					'oldLabel2' => $aLabels[2],
					'oldLabel3' => $aLabels[3],
					'oldNoteRank'=> $position)) or die(print_r($reqUpdateToDo->errorInfo()));
				//echo ('<br>'.$reqUpdateToDo->rowCount().' rangs affectés');
				$reqUpdateToDo -> closeCursor();
				
				// mettre à jour les positions
				$reqUpdateToDo = $bdd -> prepare('UPDATE todolists SET noteRank = noteRank - 1
				WHERE idUser=:idUser AND idTopic=:idTopic AND label0=:label0 AND label1=:label1 AND label2=:label2 AND label3=:label3 AND dateArchive IS NULL AND noteRank > :oldNoteRank');
					$reqUpdateToDo -> execute(array(
					'idUser' => $_SESSION['id'],
					'idTopic' => $idTopic,
					'label0' => $aLabels[0],
					'label1' => $aLabels[1],
					'label2' => $aLabels[2],
					'label3' => $aLabels[3],
					'oldNoteRank'=> $position)) or die(print_r($reqUpdateToDo->errorInfo()));
				//echo ('<br>'.$reqUpdateToDo->rowCount().' rangs affectés');
				$reqUpdateToDo -> closeCursor();
				
			}
		}
	}
	else {
		echo "Une des variables n'est pas définie.";
	}
}
else {
	echo 'disconnected';	
}
?>