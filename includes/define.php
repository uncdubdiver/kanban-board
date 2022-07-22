<?php
	/*
		Including all necessary files
	*/
	
	//	CHANGE ME AS NECESSARY
	define('CONN_DATABASE_HOST', 				'');			//	SET ME
	define('CONN_DATABASE_USERNAME', 			'');			//	SET ME
	define('CONN_DATABASE_PASSWORD', 			'');			//	SET ME
	define('CONN_DATABASE_NAME', 				'');			//	SET ME
	define('CONN_DATABASE_CONNECTION_NAME', 	'master');
	
	define('DATABASE',							CONN_DATABASE_NAME);	//	This is setup to be used within the class files
	
	require_once('db.functions.php');		//	DB functions
	require_once('r3functions.php');		//	r3 PHP functions
	require_once('functions.php');			//	Standard PHP functions
	require_once('KanbanTheme.php');			//	KanbanTheme PHP class object
	require_once('KanbanSettings.php');				//	KanbanSettings PHP class object	
	require_once('Kanban.php');				//	Kanban PHP class object	

	db_connect(CONN_DATABASE_HOST, CONN_DATABASE_USERNAME, CONN_DATABASE_PASSWORD, CONN_DATABASE_CONNECTION_NAME);
?>