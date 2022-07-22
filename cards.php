<?php
	/*
		Dean Berman
		2022-02-18
		Cards backend page to execute retrieving json data for stored DB kanban board
	*/
	
	require_once('./includes/define.php');
	
	if(
		$_REQUEST['action'] == 'getdata'
		|| $_REQUEST['action'] == 'add'
		|| $_REQUEST['action'] == 'deleteall'
		|| $_REQUEST['action'] == 'delete'
		|| $_REQUEST['action'] == 'update'
	) {
		$statusmessage = '';
		
		if($_REQUEST['action'] == 'add') {
			//	Saving to DB...
			$K = new Kanban($_REQUEST['id']);
			$K->id = trim($_REQUEST['id']);
			$K->title = trim($_REQUEST['title']);
			$K->description = trim($_REQUEST['description']);
			$K->position = trim($_REQUEST['position']);
			$K->priority = (($_REQUEST['priority'] == "true") ? 1 : 0);
			$K->Insert();
			
			$statusmessage = 'Added successfully!';
			
		} else if($_REQUEST['action'] == 'deleteall') {
			//	Saving to DB...
			$K = new Kanban(NULL);
			$K->DeleteAll();
			
			$statusmessage = 'Deleted all successfully!';
			
		} else if($_REQUEST['action'] == 'delete') {
			//	Saving to DB...
			$K = new Kanban($_REQUEST['id']);
			$K->id = trim($_REQUEST['id']);
			$K->Delete();
			
			$statusmessage = 'Deleted successfully!';
			
		} else if($_REQUEST['action'] == 'update') {
			//	Saving to DB...
			$K = new Kanban($_REQUEST['id']);
			$K->id = trim($_REQUEST['id']);
			$K->title = trim($_REQUEST['title']);
			$K->description = trim($_REQUEST['description']);
			$K->position = trim($_REQUEST['position']);
			$K->priority = (($_REQUEST['priority'] == "true") ? 1 : 0);
			$K->Update();
			
			$statusmessage = 'Updated successfully!';
		}
		
		$KT = new KanbanTheme();
		$theme = $KT->GetKanbanTheme();
		
		$K = new Kanban(NULL);
		
		//	Only fix the ordering on $_REQUEST['action'] == getdata
		if($_REQUEST['action'] == 'getdata') {
			$K->FixOrdering();
		}
		$cardscnt = count($cardsarray = $K->GetKanban());
		
		$dataarray = array(
			'theme' => $theme,
			'cards' => $cardsarray,
		);
		
		/*
		//	Test/temporary...
		$dataarray = array(
			'theme' => 'light',
			'cards' => array(
				array(
					'id' => 0,
					'title' => 'Test title 0',
					'description' => 'Test description here for number 0',
					'position' => 'yellow',
					'priority' => FALSE,
				),
				array(
					'id' => 1,
					'title' => '11111 title',
					'description' => '11111 Test description here for number 1',
					'position' => 'blue',
					'priority' => FALSE,
				),
				array(
					'id' => 2,
					'title' => 'purple test',
					'description' => 'PURPLE HERE!!!',
					'position' => 'purple',
					'priority' => TRUE,
				),
			),
		);
		*/
		
		print json_encode(array('statuscode' => 200, 'statusmessage' => $statusmessage, 'data' => $dataarray));
		exit;
		
	} else if($_REQUEST['action'] == 'savetheme') {
		$statusmessage = '';
		
		//	Saving to DB...
		$KT = new KanbanTheme();
		$KT->theme = trim($_REQUEST['theme']);
		$KT->Save();
		
		$statusmessage = 'Added successfully!';
		
		print json_encode(array('statuscode' => 200, 'statusmessage' => $statusmessage));
		exit;
		
	} else {
		print json_encode(array('statuscode' => 404, 'statusmessage' => 'Error, incorrect page access.'));
		exit;
	}
?>