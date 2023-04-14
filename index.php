<?php
	/*
		Dean Berman
		2022-07-21
		Main page - need to include PHP connection information to grab data, settings, etc.
	*/
	
	require_once('./includes/define.php');

	//	Need to query the KanbanSettings table to get the list of columns
	//	These columns will be put into JS format below for the scripts.js file to use
	$KS = new KanbanSettings(NULL);
	$ksCnt = count($ksArray = $KS->GetKanbanSettings());
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <link rel="stylesheet" href="style.css">
	    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	    <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	    <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700,800,900" rel="stylesheet">
	    <title>Kanban Board</title>

		<script>
			let dataColors = [
<?php
				for($i = 0; $i < $ksCnt; $i++) {
					print "{color:\"{$ksArray[$i]['color']}\", title:\"{$ksArray[$i]['title']}\"}, ";
				}
?>
			];
		</script>
	</head>
	<body>
	    <div id="loadingScreen">
	        <div class="loader"></div>
	    </div>
	    
	    <br />
	    
	    <div class="header">
		    <div class="controls p-3">
		        <form class="form-inline" style="padding-top:10px">
					<input type="text" name="primaryid" id="primaryidInput" value="">
					<input type="text" name="position" id="positionInput" value="">

		            <label for="titleInput">Title:</label>
		            <input class="form-control form-control-sm" type="text" name="title" id="titleInput" autocomplete="off">
		            <label for="descriptionInput">Description:</label>
		            <input class="form-control form-control-sm" style="width:500px;" type="text" name="description" id="descriptionInput" autocomplete="off">
		            <button class="btn btn-success" id="add">Save</button>
		            &nbsp;&nbsp;
		            <!--
						<button class="btn btn-danger mx-2" id="deleteAll">Delete All</button>
					-->
		            <button class="btn btn-danger mx-2" id="clear">Clear</button>
		            &nbsp;&nbsp;
		            <button class="btn" id="theme-btn">Dark/Light</button>
		        </form>
		    </div>
		    
		    <br />
		    
		    <div class="boards overflow-auto p-0" id="boardsHeader"></div>
	    </div>
	    <br /><br /><br /><br /><br />
	    <div class="boards overflow-auto p-0" id="boardsContainer" style="z-index:2;">
	    </div>

	    <script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
	    <script src="script.js" defer></script>
	    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	    <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
	    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	    <script>if (window.module) module = window.module;</script>
		<script type="text/javascript">
			
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-36251023-1']);
			_gaq.push(['_setDomainName', 'jqueryscript.net']);
			_gaq.push(['_trackPageview']);

			(function() {
			//var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			//ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			//var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
			
			$(document).ready(function() {
				
			});
		</script>
	</body>
</html>
