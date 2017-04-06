<?php

header("Content-Type: text/plain");

session_start();

if (isset($_SESSION['id']) && isset($_GET["newNote"]) && isset($_GET["idTopic"]) && isset($_GET["sPathTreeItemToInsert"]) && (preg_match("#^[0-9]{2}([a-b][0-9]{2})*$#", $_GET["sPathTreeItemToInsert"]))) {
	
	require '../../log_in_bdd.php';

	require '../../isIdTopicSafeAndMatchUser.php';
	
	$newNote = htmlspecialchars($_GET["newNote"]);
	
	// inserer la note
	$reqInsertNote = $bdd -> prepare('INSERT INTO notes(idUser,idTopic,idNote,content,dateCreation) VALUES (:idUser,:idTopic,:idNote,:newNote,NOW())');
		$reqInsertNote -> execute(array(
		'idUser' => $_SESSION['id'],
		'idTopic' => $_GET["idTopic"], 
		'idNote' => $_GET["sPathTreeItemToInsert"],
		'newNote' => $newNote)) or die(print_r($reqInsertNote->errorInfo()));
	//echo ('<br>'.$reqInsertNote->rowCount().' rangs affect�s');
	$reqInsertNote -> closeCursor();	
}

else {
	echo 'Une des variables n\'est pas d�finie ou la session n\'est pas ouverte !!!';	
}
?>