<?php
	$MYSQLI_USE_OOB = TRUE;
	$queryArray = array();
	/*
	* The following class is used for DB Interaction only.
	* Only functions/variables that need to be in here should be those that interact with the DB's
	**/
	$connArray = array();
	
	/**
	* db_connect is a PRIMARY FUNCTION that is used to establish a connection with the MySQLi DB
	*
	* @param string		Hostname (IP Address) of server
	* @param string		Authentication username
	* @param string		Authentication password
	* @param string		[opt] Server name of connection (if not set, defaults to auto_increment counter)
	* @return boolean	TRUE if connection is successful, FALSE (or dies) if connection fails
	**/
	function db_connect($hostname, $username, $password, $serverName=NULL) {
		global $connArray;
		global $MYSQLI_USE_OOB;
		
		//	Checking for port #
		$port = '3306';
		if(stristr($hostname, ':')) {
			list($hostname, $port) = explode(':', $hostname);
		}
		
		if($MYSQLI_USE_OOB) {
			$connArray[$serverName] = new mysqli($hostname, $username, $password, NULL, $port);
		} else {
			$connArray[$serverName] = mysqli_connect($hostname, $username, $password);
		}
		
		if($connArray[$serverName] === FALSE) {
			print "Failed to connect to {$hostname} using username {$username}";
			exit;
			return FALSE;
		}
		return TRUE;
	}
	
	function db_check_connection($serverName=NULL) {
		global $connArray;
		global $MYSQLI_USE_OOB;
		
		if($connArray[$serverName] === FALSE) {
			return FALSE;
		}
		return TRUE;
	}

	function db_selected_db($db_name, $thisServerName=NULL) {
		global $connArray;
		global $MYSQLI_USE_OOB;

		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		
		//	Grabbing the current connection into a variable (so we aren't moving around the array key-pair value...
		$currentConn = $connArray[$thisServerName];
		
		if($MYSQLI_USE_OOB) {
			$currentConn->select_db($db_name);
		} else {
			mysqli_select_db($db_name, $currentConn);
		}
	}

	/**
	* db_apull is used to do simple select queries on the DB.
	*
	* @param string		Query to execute against DB
	* @param pointer mixed	Pointer return array (results will be stored/returned here)
	* @param string		[opt] Connection Server name (which DB server to run query on)
	* @param string		[opt] MySQLi query return type (MYSQLI_NUM, MYSQLI_ASSOC, MYSQLI_BOTH default)
	* @return boolean,int		FALSE if query fails, if query is successful then returns number of rows
	**/
	function db_apull($query, &$retArray, $thisServerName=NULL, $queryRetType='MYSQLI_BOTH') {
		global $connArray;
		global $queryArray;
		global $MYSQLI_USE_OOB;
		
		if(empty($query)) {
			return NULL;
		}
		
		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		//	Grabbing the current connection into a variable (so we aren't moving around the array key-pair value...
		$currentConn = $connArray[$thisServerName];

		$STARTQUERYTIME = microtime(TRUE);

		// Now to query the DB
		if($MYSQLI_USE_OOB) {
			$res = $currentConn->query($query);
		} else {
			$res = mysqli_query($currentConn, $query);
		}

		$ENDQUERYTIME = microtime(TRUE);

		// If the query failed to run on the DB
		if($res === FALSE) {
		}

		// Query ran successfully, now to create a multideminisional array to return back to caller
		$retArray = array();
		
		$icnt = 0;

		if($MYSQLI_USE_OOB) {
			if(@$res->num_rows > 0) {
				for($i = 0, $icnt = $res->num_rows; $i < $icnt; $i++) {
					switch($queryRetType) {
						case 'MYSQL_ASSOC':
						case 'MYSQLI_ASSOC':
							$retArray[] = $res->fetch_array(MYSQLI_ASSOC);
							break;
						case 'MYSQL_NUM':
						case 'MYSQLI_NUM':
							$retArray[] = $res->fetch_array(MYSQLI_NUM);
							break;
						default:
							$retArray[] = $res->fetch_array(MYSQLI_BOTH);
							break;
					}
				}
			}
		} else {
			for($i = 0, $icnt = mysqli_num_rows($res); $i < $icnt; $i++) {
				switch($queryRetType) {
					case 'MYSQL_ASSOC':
					case 'MYSQLI_ASSOC':
						$retArray[] = mysqli_fetch_array($res, MYSQLI_ASSOC);
						break;
					case 'MYSQL_NUM':
					case 'MYSQLI_NUM':
						$retArray[] = mysqli_fetch_array($res, MYSQLI_NUM);
						break;
					default:
						$retArray[] = mysqli_fetch_array($res, MYSQLI_BOTH);
						break;
				}
			}
		}
		
		//	Free any memory left hanging around from the PGSQL query...
		if($MYSQLI_USE_OOB) {
//			$currentConn->free($res);
//			$res->free();
		} else {
			mysqli_free_result($res);
		}
		
		return $icnt;
	}


	/**
	* db_apull_assoc is used to do select queries on the DB but is specific that it only returns associative array(s) of the results
	*
	* @param string		Query to execute against DB
	* @param pointer mixed	Pointer return array (results will be stored/returned here)
	* @param string		[opt] Connection Server name (which DB server to run query on)
	* @return boolean,int	FALSE if query fails, if query is successful then returns number of rows
	**/
	function db_apull_assoc($query, &$retArray, $thisServerName=NULL) {
		return db_apull($query, $retArray, $thisServerName, 'MYSQLI_ASSOC');
	}

	/**
	* db_insert function is used to making changes (inserts, updates, deletes, drops, creates, etc.) on the DB
	*
	* @param string		Query to execute against the DB
	* @param string		[opt] Connection Server name (with DB server to run query on)
	* @return boolean,int	FALSE if query fails, if query is successful then get last inserted id and return it
	**/
	function db_insert($query, $thisServerName=NULL) {
		global $connArray;
		global $MYSQLI_USE_OOB;

		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		//	Grabbing the current connection into a variable (so we aren't moving around the array key-pair value...
		$currentConn = $connArray[$thisServerName];

		$STARTQUERYTIME = microtime(TRUE);

		if($MYSQLI_USE_OOB) {
			$res = $currentConn->query($query);
		} else {
			$res = mysqli_query($currentConn, $query);
		}

		$ENDQUERYTIME = microtime(TRUE);
		
		// If the query failed to run on the DB
		if($res === FALSE) {
			// TODO: Should record query error
			//	DRB
			//	2016-07-06
			//	Added option to IGNORE the DB error if one gets produced, so it doesn't include an entry in the QueryErrors table...
			if(strstr($query, 'DB:IGNORE_ERROR')) {
				//	Don't save the query error...
			} else {
			}
			
			return FALSE;
			
		} else {
			//	This is the return value...by DEFAULT it's set to TRUE, but will change if it's an INSERT query...
			$RETURN = TRUE;
			
			//	Check if query is an insert...if it is, then continue below, otherwise return TRUE/FALSE...
			$tmpQuery = trim($query);	//	Remove trailing/starting spaces...
			$tmpQuery = str_replace(array("\n", "\r\n"), array(" ", " "), $tmpQuery);	//	Remove \n and \r\n and make them spaces...
			list($queryType, $null) = explode(" ", $tmpQuery);
			
			if(strtolower($queryType) == 'insert') {
				// Now for an insert, return the latest/newest ID inserted into the DB and return it to caller
				$query2 = "
					SELECT
						LAST_INSERT_ID() AS ID
					";
				if($MYSQLI_USE_OOB) {
					$res2 = $currentConn->query($query2);
				} else {
					$res2 = mysqli_query($currentConn, $query2);
				}
		
				if($res2 === FALSE) {
				} else {
				}
		
				if($MYSQLI_USE_OOB) {
					$newIDArray = $res2->fetch_array();
				} else {
					$newIDArray = mysqli_fetch_array($res2);
				}
				$RETURN = $newIDArray['ID'];
			}
		}
		
		return $RETURN;
	}

	/**
	* db_insert_create_temp_table($cArray) function is used to making changes (inserts, updates, deletes, drops, creates, etc.) on the DB
	*
	* @param mixed		[req] Multi-dimensional array of data to be inserted into a temporary table
	* @param string		[req] Temp table name (MUST be in database.table format)
	* @param string		[opt] Connection Server name (with DB server to run query on)
	**/
	function db_insert_create_temp_table($dataArray, $TempTableName, $thisServerName=NULL) {
		global $connArray;
		global $MYSQLI_USE_OOB;
		
		if(empty($dataArray)) {
			return FALSE;
		}

		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		//	Grabbing the current connection into a variable (so we aren't moving around the array key-pair value...
		$currentConn = $connArray[$thisServerName];
		
		$CreateQuery = "";
		$InsertQuery = "";
		
		//	Get the headers for the temp table...
		$headerArray = NULL;
		foreach($dataArray AS $pos => $eachArray) {
			foreach($eachArray AS $key => $val) {
				$headerArray[] = "{$key}";
			}
			break;
		}
		
		//	Create each query field for the create table query...
		$createTableArray = NULL;
		foreach($headerArray AS $header) {
			$createTableArray[] = "`{$header}` text default NULL";
		}
		
		//	Now to create the table...
		$CreateQuery = "
			CREATE TEMPORARY TABLE {$TempTableName}
			(
				" . implode(",\n", $createTableArray) . "
			);
		";
/*		print "<div align=left><pre>";
		print_r($CreateQuery);
		print "</pre></div>";*/
		
		$insertDataKeysArray = NULL;
		foreach($headerArray AS $header) {
			$insertDataKeysArray[] = "`{$header}`";
		}
		
		//	Now to build a query to populate the data...
		$insertDataArray = NULL;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			$tempInsertDataArray = NULL;
			foreach($headerArray AS $header) {
				$tempInsertDataArray[] = r3a("{$dataArray[$i][$header]}");
			}
			$insertDataArray[] = "(" . implode(', ', $tempInsertDataArray) . ")";
		}
		
		
		$InsertQuery = "
			INSERT INTO {$TempTableName}
				(" . implode(", ", $insertDataKeysArray) . ")
			VALUES
				" . implode(",\n", $insertDataArray) . "
			;
		";
/*		print "<div align=left><pre>";
		print_r($InsertQuery);
		print "</pre></div>";*/
		
		
		//	Running CreateQuery...
		$STARTQUERYTIME = microtime(TRUE);
		if($MYSQLI_USE_OOB) {
			$res = $currentConn->query($CreateQuery);
		} else {
			$res = mysqli_query($currentConn, $CreateQuery);
		}
		$ENDQUERYTIME = microtime(TRUE);

		// If the query failed to run on the DB
		if($res === FALSE) {
			print "<pre>";
			print $CreateQuery;
			print "<pre>";
			// TODO: Should record query error
			return FALSE;
		} else {
		}
		
		
		//	Running InsertQuery...
		$STARTQUERYTIME = microtime(TRUE);
		if($MYSQLI_USE_OOB) {
			$res = $currentConn->query($InsertQuery);
		} else {
			$res = mysqli_query($currentConn, $InsertQuery);
		}
		$ENDQUERYTIME = microtime(TRUE);

		// If the query failed to run on the DB
		if($res === FALSE) {
			// TODO: Should record query error
			return FALSE;
		} else {
		}
		
		//	If it gets to here, then everything should be GTG...so return TRUE...
		return TRUE;
	}

	/**
	* db_drop_temp_table($TempTableName) function is used drop a temporary table if it exists...
	*
	* @param string		[req] Temp table name (MUST be in database.table format)
	**/
	function db_drop_temp_table($TempTableName) {
		global $connArray;
		global $MYSQLI_USE_OOB;

		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		
		//	Grabbing the current connection into a variable (so we aren't moving around the array key-pair value...
		$currentConn = $connArray[$thisServerName];
		
		$DropQuery = "";
		
		//	Now to create the table...
		$DropQuery = "
			DROP TABLE IF EXISTS {$TempTableName};
		";
		
		//	Running CreateQuery...
		$STARTQUERYTIME = microtime(TRUE);
		if($MYSQLI_USE_OOB) {
			$res = $currentConn->query($DropQuery);
		} else {
			$res = mysqli_query($currentConn, $DropQuery);
		}
		$ENDQUERYTIME = microtime(TRUE);

		// If the query failed to run on the DB
		if($res === FALSE) {
			print "<pre>";
			print $DropQuery;
			print "<pre>";
			// TODO: Should record query error
			return FALSE;
		} else {
		}
		
		//	If it gets to here, then everything should be GTG...so return TRUE...
		return TRUE;
	}

	/**
	* db_error is called from the user if they want to print out any query errors
	*
	* @param string		[opt] Connection Server the error occurred from
	* @return void
	*/
	function db_error($thisServerName=NULL) {
		global $MYSQLI_USE_OOB;
		
//		if(empty($thisServerName)) {
			$thisServerName = 'master';
//		}
		
		//	Grabbing the current connection into a variable (so we aren't moving around the array key-pair value...
		$currentConn = $connArray[$thisServerName];
		
		if($MYSQLI_USE_OOB) {
			print "ERR# [" . $currentConn->errno . "] ERR [" . $currentConn->error . "]";
		} else {
			print "ERR# [" . mysqli_errno($currentConn) . "] ERR [" . mysqli_error($currentConn) . "]";
		}
	}

	/**
	* notify_db_error function is used to record in the DB the errors to occur from any MySQLi queries
	*
	* @param string		Query to execute against the DB
	* @param string		Connection Server
	* @return void
	**/
	function notify_db_error($query, $currentConn) {
		global $connArray;
		global $MYSQLI_USE_OOB;

		/*
		if(IS_DEV) {
			print "<pre>";
			print $query;
			print "</pre>";
			print "ERROR#: {$errorNumber}<br />";
			print "MSG: {$errorMessage}<br />";
		} else {
			print '<div class="error_msg">There was a problem processing your request. Please reset your selection and try again.</div>';
		}
		*/
	}

	function setup_query_debug_array($query, $status, $resCnt, $resError, $STARTQUERYTIME, $ENDQUERYTIME) {
		return array(
			'QUERY' => $query,
			'STATUS' => $status,
			'ROWS' => $resCnt,
			'ERROR' => $resError,
			'STARTQUERYTIME' => $STARTQUERYTIME,
			'ENDQUERYTIME' => $ENDQUERYTIME,
			'TOTALQUERYTIME' => $ENDQUERYTIME - $STARTQUERYTIME,
		);
	}
?>