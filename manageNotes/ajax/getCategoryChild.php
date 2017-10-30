<?php

header("Content-Type: application/json; charset=UTF-8");
//header("Content-Type: text/plain");


session_start();

if (isset($_SESSION['id'])) {
	
	if (isset($_GET["idTopic"]) && isset($_GET["sPathParent"])) {

		if (preg_match("#^[0-9]{2}([a-b][0-9]{2})*$#", $_GET["sPathParent"])) {
		
			require '../../log_in_bdd.php';

			require '../../isIdTopicSafeAndMatchUser.php';
			
			$idTopic = htmlspecialchars($_GET["idTopic"]);
			$sPathParent = htmlspecialchars($_GET["sPathParent"]);
			
			$reqRetrieveChildren = $bdd -> prepare('SELECT idNote, content, dateCreation FROM notes WHERE idUser=:idUser AND idTopic=:idTopic AND idNote REGEXP :toFind AND idNote NOT LIKE :pathParent ORDER BY idNote');
			$reqRetrieveChildren -> execute(array(
			'idUser' => $_SESSION['id'],
			'idTopic' => $idTopic, 
			'toFind' => '^'.$sPathParent.'([a-b][0-9]{2}$)',
			'pathParent' => $sPathParent));

			//echo $reqRetrieveChildren->rowCount();
			
			$isFirstRowOfFolderFetched = false;
			$isFirstRowOfNoteFetched = false;
			$sTreeItemsFetched = "";
			
			while ($data = $reqRetrieveChildren->fetch()) {
				$sTreeItemsTypeFetched = substr($data['idNote'], -3, 1);
				if ($sTreeItemsTypeFetched === "a" and !$isFirstRowOfFolderFetched) {
					$sTreeItemsFetched = '{"a":[["'.$data['content'].'","'.$data['dateCreation'].'"],';
					$isFirstRowOfFolderFetched = true;
				}
				else if ($sTreeItemsTypeFetched === "b" and !$isFirstRowOfNoteFetched) {
				$sTreeItemsFetched = (!$isFirstRowOfFolderFetched ? "{" : substr($sTreeItemsFetched, 0, -1).'],').'"b":[["'.$data['content'].'","'.$data['dateCreation'].'"],';
					$isFirstRowOfNoteFetched = true;
				}
				else {
					$sTreeItemsFetched .= '["'.$data['content'].'","'.$data['dateCreation'].'"],';
				}
			}

			echo htmlspecialchars_decode($sTreeItemsFetched == "" ? "" : substr($sTreeItemsFetched,0,-1)."]}"); //il faut enlever le dernier ","

			$reqRetrieveChildren->closeCursor();	
		}
	}
	else {
		echo 'Une des variables n\'est pas définie.';
	}
}
else {
	echo 'disconnected';
}
?>