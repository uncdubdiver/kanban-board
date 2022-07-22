<?php
	/*
		Kanban SQL class object
		DROP TABLE IF EXISTS Kanban;
		CREATE TABLE Kanban
		(
			`primaryid` int primary key auto_increment,
			`id` int NOT NULL,
			`title` varchar(100) NOT NULL,
			`description` varchar(255) default NULL,
			`position` varchar(30) default 'yellow',
			`priority` boolean default false
		);
	*/
	class Kanban {
		var $primaryid = '';
		var $id = '';
		var $title = '';
		var $description = '';
		var $position = '';
		var $priority = 0;
		
		function Kanban($id=NULL) {
			if(!empty($id)) {
				$query = "
					SELECT
						*
					FROM
					" . DATABASE . ".Kanban K
					WHERE
						1
						AND K.`id` = " . r3a($id) . "
				";
				$tmpCnt = db_apull_assoc($query, $tmpArray);
				
				$this->id = $tmpArray[0]['id'];
				$this->title = $tmpArray[0]['title'];
				$this->description = $tmpArray[0]['description'];
				$this->position = $tmpArray[0]['position'];
				$this->priority = $tmpArray[0]['priority'];
			}
		}
		
		function FixOrdering() {
			$datacnt = count($dataarray = $this->GetKanban(TRUE));
			
			for($i = 0; $i < $datacnt; $i++) {
				$query = "
					UPDATE
					" . DATABASE . ".Kanban
					SET
						`id` = " . r3a($i) . "
					WHERE
						1
						AND `primaryid` = " . r3a($dataarray[$i]['primaryid']) . "
					LIMIT
						1
				";
				db_insert($query);
			}
		}
		
		function Insert() {
			$query = "
				INSERT INTO
				" . DATABASE . ".Kanban
				SET
					`id` = " . r3a($this->id) . ",
					`title` = " . r3a($this->title) . ",
					`description` = " . r3an($this->description) . ",
					`position` = " . r3a($this->position) . ",
					`priority` = " . r3a($this->priority) . "
			";
			return db_insert($query);
		}
		
		function Update() {
			$query = "
				UPDATE
				" . DATABASE . ".Kanban
				SET
					`title` = " . r3a($this->title) . ",
					`description` = " . r3an($this->description) . ",
					`position` = " . r3a($this->position) . ",
					`priority` = " . r3a($this->priority) . "
				WHERE
					1
					AND `id` = " . r3a($this->id) . "
				LIMIT
					1
			";
			return db_insert($query);
		}
		
		function Delete() {
			$query = "
				DELETE FROM
				" . DATABASE . ".Kanban
				WHERE
					1
					AND `id` = " . r3a($this->id) . "
				LIMIT
					1
			";
			return db_insert($query);
		}
		
		function DeleteAll() {
			$query = "
				DELETE FROM
				" . DATABASE . ".Kanban
				WHERE
					1
			";
			return db_insert($query);
		}
		
		function GetKanban($cleanup=FALSE) {
			if($cleanup) {
				$ORDERBY = "
					K.`primaryid` ASC
				";
			} else {
				$ORDERBY = "
					K.`id` ASC
				";
			}
			
			$query = "
				SELECT
					*
				FROM
				" . DATABASE . ".Kanban K
				WHERE
					1
				ORDER BY
					K.`id` ASC
			";
			$tmpCnt = db_apull_assoc($query, $tmpArray);
			
			return $tmpArray;
		}
	}
?>