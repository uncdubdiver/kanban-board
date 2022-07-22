<?php
	/*
		Kanban SQL class object
		DROP TABLE IF EXISTS KanbanSettings;
		CREATE TABLE KanbanSettings
		(
            `id` int auto_increment primary key,
            `title` varchar(30) NOT NULL,
            `color` varchar(30) NOT NULL default 'white',
            `ordernumber` int default 1
		);
        INSERT INTO KanbanSettings SET `title`='backlog', `color`='yellow', `ordernumber`=1;
        INSERT INTO KanbanSettings SET `title`='in progress', `color`='coral', `ordernumber`=2;
        INSERT INTO KanbanSettings SET `title`='blocked', `color`='red',`oOrdernumber`=3;
        INSERT INTO KanbanSettings SET `title`='testing', `color`='orange', `ordernumber`=4;
        INSERT INTO KanbanSettings SET `title`='completed', `color`='green', `ordernumber`=5;
	*/
	class KanbanSettings {
		var $primaryid = '';
		var $title = '';
		var $color = 'white';
		var $ordernumber = 1;
		
		function KanbanSettings($id=NULL) {
			if(!empty($id)) {
				$query = "
					SELECT
						*
					FROM
						" . DATABASE . ".KanbanSettings KS
					WHERE
						1
						AND K.`id` = " . r3a($id) . "
				";
				$tmpCnt = db_apull_assoc($query, $tmpArray);
				
				$this->id = $tmpArray[0]['id'];
				$this->title = $tmpArray[0]['title'];
				$this->color = $tmpArray[0]['color'];
				$this->ordernumber = $tmpArray[0]['ordernumber'];
			}
		}
		
		function GetKanbanSettings() {
			$query = "
				SELECT
					*
				FROM
                " . DATABASE . ".KanbanSettings KS
				WHERE
					1
				ORDER BY
					KS.`ordernumber` ASC
			";
			$tmpCnt = db_apull_assoc($query, $tmpArray);
			
			return $tmpArray;
		}
	}
?>