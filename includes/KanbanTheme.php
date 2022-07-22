<?php
	/*
		KanbanTheme SQL class object
		DROP TABLE IF EXISTS KanbanTheme;
		CREATE TABLE KanbanTheme
		(
			`theme` enum('light','darkmode') default 'light'
		);
		INSERT INTO KanbanTheme SET `theme` = 'light';
	*/
	class KanbanTheme {
		var $theme = 'light';
		
		function KanbanTheme() {
			$query = "
				SELECT
					*
				FROM
				" . DATABASE . ".KanbanTheme KT
				WHERE
					1
				LIMIT
					1
			";
			$tmpCnt = db_apull_assoc($query, $tmpArray);
			
			$this->theme = $tmpArray[0]['theme'];
		}
		
		function Save() {
			$query = "
				UPDATE
				" . DATABASE . ".KanbanTheme
				SET
					`theme` = " . r3a($this->theme) . "
				LIMIT
					1
			";
			return db_insert($query);
		}
		
		function Delete() {
			return false;
		}
		
		function GetKanbanTheme() {
			$query = "
				SELECT
					*
				FROM
				" . DATABASE . ".KanbanTheme KT
				WHERE
					1
			";
			$tmpCnt = db_apull_assoc($query, $tmpArray);
			
			return $tmpArray[0]['theme'];
		}
	}
?>