<?php
	/**
	* r3a is used to create single quote characters around a string and escaping any quotes inside a string
	*
	* @param string		String to add single quotes around
	* @param return		String surrounded by single quotes and with escaped quotes
	*/
	function r3a($string, $thisServerName=NULL) {
		global $connArray;
		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		$currentConn = $connArray[$thisServerName];
		//$string = strip_tags($string);
		$string = htmlspecialchars($string);
		return "'" . $currentConn->real_escape_string($string) . "'";
		//return "'" . mysql_real_escape_string($string) . "'";
	}

	/**
	* r3an (similar to r3a) is used to create single quote characters around a string (THAT IS NOT A NULL VALUE) and escaping any quotes inside a string
	*
	* @param string		String to add single quotes around
	* @param return		String surrounded by single quotes and with escaped quotes
	*/
	function r3an($string, $AllowZero=FALSE, $thisServerName=NULL) {
		global $connArray;
		if(empty($thisServerName)) {
			$thisServerName = 'master';
		}
		$currentConn = $connArray[$thisServerName];
		$string = trim($string);
		if($AllowZero) {
			if($string === 0 || $string === '0') {
				return "'" . $currentConn->real_escape_string($string) . "'";
				//return "'" . mysql_real_escape_string($string) . "'";
			} else {
				if(!empty($string)) {
					return "'" . $currentConn->real_escape_string($string) . "'";
					//return "'" . mysql_real_escape_string($string) . "'";
				} else {
					return 'null';
				}
			}
		
		//	DEFAULT
		} else {
			if(!empty($string)) {
				return "'" . $currentConn->real_escape_string($string) . "'";
				//return "'" . mysql_real_escape_string($string) . "'";
			} else {
				return 'null';
			}
		}
	}

	/**
	* r3e is used to encrypt a single string
	*
	* @param string		Text string that is going to be encrypted
	* @return string	Encrypted string of the parameter
	*/
	function r3e($data) {
		global $Enc;
		if(!empty($data)) {
			return $Enc->encrypt($data);
		}
		
		return '';
	}

	/**
	* r3d is used to decrypt a single string
	*
	* @param string		Encrypted Text string that is going to be decrypted
	* @return string	Decrypted string of the parameter
	*/
	function r3d($data) {
		global $Enc;
		if(!empty($data)) {
			return $Enc->decrypt($data);
		}
		
		return '';
	}
	
	/**
	* r3hash is used to take a string and hash it so it can't be reverted
	* 
	* @param string		Text string that is going to be hashed
	* @return string	Hashed output of the given string
	*/
	function r3hash($data) {
		return hash('sha512', $data);
	}

	/**
	* get_id_action is a function to call to get the global variables 'id' and 'action' to be instantiated and stored (used for post and post2 pages)
	*
	* @param void
	* @return void
	*/
	function get_id_action() {
		global $action;
		global $id;
		global $id2;
		
		$id = r3d($_REQUEST['id']);
		$id2 = r3d($_REQUEST['id2']);
		$action = r3d($_REQUEST['action']);
	}

	/**
	* get_action_id is the reverse name of function call get_id_action and calls it as well
	*
	* @param void
	* @return void
	*/
	function get_action_id() {
		get_id_action();
	}

	/**
	* Function is used to format a phone number properly when viewing it, depending on the number of digits in the phone number.
	*
	* @param	string	Phone number any particular format to be setup
	* @return	string	Properly formatted phone number (ex. xxx-xxxx, xxx-xxx-xxxx, x-xxx-xxx-xxxx)
	*/
	function r3formatphonenumber($phoneNumber) {
		$phoneNumber = trim($phoneNumber);

		if(strlen($phoneNumber) == 0 || empty($phoneNumber)) {
			return $phoneNumber;
		}

		$tmpPhoneNumber = str_replace(array('-', '(', ')', ' '), '', $phoneNumber);
		if(strlen($tmpPhoneNumber) == 7) {
			$tmpPhoneNumber = substr($tmpPhoneNumber, 0, 3) . '-' . substr($tmpPhoneNumber, 3, 4);
		} else if(strlen($tmpPhoneNumber) == 10) {
			$tmpPhoneNumber = substr($tmpPhoneNumber, 0, 3) . '-' . substr($tmpPhoneNumber, 3, 3) . '-' . substr($tmpPhoneNumber, 6, 4);
		} else if(strlen($tmpPhoneNumber) == 11) {
			$tmpPhoneNumber = substr($tmpPhoneNumber, 0, 1) . '-' .  substr($tmpPhoneNumber, 1, 3) . '-' . substr($tmpPhoneNumber, 4, 3) . '-' . substr($tmpPhoneNumber, 7, 4);
		} else {
			$tmpPhoneNumber = $tmpPhoneNumber;
		}

		return $tmpPhoneNumber;
	}

	/**
	* This function is used to get the difference between 2 dates.
	*
	* @param    string   Date (date format) of the earlier date (ex. 2008-02-01)
	* @param    string   Date (date format) of the later date (ex. 2008-02-15)
	* @param    boolean  Option to send back number of days in between (and not just time in seconds).
	* @return   int      Return the number of seconds in between 2 dates or number of days (based on 3rd parameter).
	*/
    function r3datediff($date1, $date2, $retDays=NULL) {
    	$date1 = strtotime($date1);
    	$date2 = strtotime($date2);
		$timeDifference = $date2 - $date1;
		$corr = date("I", $date2) - date("I", $date1);
		$timeDifference += $corr * 3600;
		if($retDays) {
			return $timeDifference / 86400;
		}
		return $timeDifference;
	}
	
	function r3getdaterange($date_start, $date_end, $format='Y-m-d') {
		$date_range = range(
			strtotime($date_start),
			strtotime($date_end),
			86400 // seconds in a day
		);
		$retArray = array_unique(
			array_map(
				'date',
				array_fill(0, count($date_range), $format),
				$date_range
			)
		);
		
		//	Adding the final/last day in if it is missing at all...
		if(date($format, strtotime($date_end)) != date($format, strtotime($retArray[count($retArray)-1]))) {
			$retArray[] = date($format, strtotime($date_end));
		}
		
//		return $retArray;
		return array_values(array_unique($retArray));
	}
	
	function r3timediff($firstTime, $lastTime) {
		// convert to unix timestamps
		$firstTime = strtotime($firstTime);
		$lastTime = strtotime($lastTime);

		// perform subtraction to get the difference (in seconds) between times
		$timeDiff = $lastTime - $firstTime;

		// return the difference
		return $timeDiff;
	}
	
	function r3secondstostring($timeInSeconds=0, $shortOutput=FALSE) {
		if($timeInSeconds == 0) {
			return '0 sec';
		} else {
			
			//	Checking for days...
			if($timeInSeconds >= 86400) {
				$timeDiff1 = floor($timeInSeconds / 86400);
				$timeInSeconds = ($timeInSeconds % 86400);
				
				$timeDiff2 = floor($timeInSeconds / 3600);
				$timeInSeconds = ($timeInSeconds % 3600);
				
				$timeDiff3 = ($timeInSeconds % 60);
				$timeDiff4 = floor($timeInSeconds / 60);
				
				if($shortOutput) {
					$timeDiffFinal = "{$timeDiff1} day";
				} else {
					$timeDiffFinal = "{$timeDiff1} day {$timeDiff2} hr {$timeDiff3} min {$timeDiff4} sec";
				}
			
			//	Checking for hours...
			} else if($timeInSeconds >= 3600) {
				$timeDiff1 = floor($timeInSeconds / 3600);
				$timeInSeconds = ($timeInSeconds % 3600);
				
				$timeDiff2 = ($timeInSeconds % 60);
				$timeDiff3 = floor($timeInSeconds / 60);
				
				if($shortOutput) {
					$timeDiffFinal = "{$timeDiff1} hr {$timeDiff2} min";
				} else {
					$timeDiffFinal = "{$timeDiff1} hr {$timeDiff2} min {$timeDiff3} sec";
				}
				
			} else {
				$timeDiff1 = floor($timeInSeconds / 60);
				$timeDiff2 = ($timeInSeconds % 60);
				
				if($shortOutput) {
					$timeDiffFinal = "{$timeDiff1} min";
				} else {
					$timeDiffFinal = "{$timeDiff1} min {$timeDiff2} sec";
				}
			}
		}

		return $timeDiffFinal;
	}
	
	/*
	*	r3prettyformattime() - format pretty output from seconds
	*	@param int total number of seconds to be reformatted pretty
	* */
	function r3prettyformattime($timeInSeconds=NULL) {
		return r3secondstostring($timeInSeconds, FALSE);
		
/*		if(empty($timeInSeconds)) {
			return NULL;
		}
		
		$NumDays = 0;
		$NumHours = 0;
		$NumMinutes = 0;
		$NumSeconds = 0;
		
		$tmpTimeInSeconds = $timeInSeconds;
		if($tmpTimeInSeconds >= 86400) {
			$NumDays = floor($tmpTimeInSeconds / 86400);
			$tmpTimeInSeconds = $tmpTimeInSeconds - ($NumDays * 86400);
		}
		if($tmpTimeInSeconds >= 3600) {
			$NumHours = floor($tmpTimeInSeconds / 3600);
			$tmpTimeInSeconds = $tmpTimeInSeconds - ($NumHours * 3600);
		}
		if($tmpTimeInSeconds >= 60) {
			$NumMinutes = floor($tmpTimeInSeconds / 60);
			$tmpTimeInSeconds = $tmpTimeInSeconds - ($NumMinutes * 60);
		}
		if($tmpTimeInSeconds <= 60) {
			$NumSeconds = $tmpTimeInSeconds;
		}
		
		$retArray = NULL;
		if(!empty($NumDays)) {
			if($NumDays > 1) {
				$retArray[] = "{$NumDays} days";
			} else {
				$retArray[] = "{$NumDays} day";
			}
		}
		if(!empty($NumHours)) {
			if($NumHours > 1) {
				$retArray[] = "{$NumHours} hrs";
			} else {
				$retArray[] = "{$NumHours} hr";
			}
		}
		if(!empty($NumMinutes)) {
			if($NumMinutes > 1) {
				$retArray[] = "{$NumMinutes} mins";
			} else {
				$retArray[] = "{$NumMinutes} min";
			}
		}
		if(!empty($NumSeconds)) {
			if($NumSeconds > 1) {
				$retArray[] = "{$NumSeconds} secs";
			} else {
				$retArray[] = "{$NumSeconds} sec";
			}
		}
		
		return implode(' ', $retArray);
//		return $timeInSeconds . ' -> ' . implode(' ', $retArray);*/
	}
	
	
	
	
	
	
	
	function r3cleanpath($path) {
		//	We want to keep the // for http and https, so we'll exclude them from being cleaned, then send them back as needed...
		$prepath = '';
		if(substr($path, 0, 7) == 'http://') {
			$path = substr($path, 7);
			$prepath = 'http://';
		}
		if(substr($path, 0, 8) == 'https://') {
			$path = substr($path, 8);
			$prepath = 'https://';
		}
		
		$path = str_replace(
			array('//////', '/////', '////', '///', '//'),
			array('/',      '/',     '/',    '/',   '/' ),
			$path
		);
		
		return $prepath . $path;
	}
	
	function r3cleandirectory($directory=NULL) {
		return r3cleanpath($directory);
	}
	
	/**
	* @name		directory_to_array
	* @desc		Function which will recursively scan a directory and find all the directories and files which exist within it
	* 
	* @param	string	Directory to be read in as the root/parent
	* @param	boolean	TRUE=directory contains NUMBERED directories, so it will return the file name as an array field, FALSE=return file names as values and the fields will auto-increment inside the array
	* @return	mixed	Returned array of directories and files that were scanned/found
	*/
	function r3directorytoarray($dir, $NUM_DIRECTORIES=FALSE) {
		$result = array();
		
		$cdir = scandir($dir);
		foreach($cdir as $key => $value) {
			if(!in_array($value, array(".", ".."))) {
				if(is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$result[$value] = r3directorytoarray($dir . DIRECTORY_SEPARATOR . $value, $NUM_DIRECTORIES);
				} else {
					if($NUM_DIRECTORIES) {
						$result[$value] = 1;
					} else {
						$result[] = $value;
					}
				}
			}
		}
		
		return $result;
	}
	
	function r3filetoarray($file) {
		if(!is_file($file)) {
			return NULL;
		}
		
		$tmpArray = file($file);
		
		$lineArray = NULL;
		//	Now to go through the lines and run them through a trim...
		foreach($tmpArray AS $lineNum => $lineContent) {
			$lineArray[$lineNum] = trim($lineContent);
		}
		
		return $lineArray;
	}
	
	function r3csvfiletoarray($file) {
		if(!is_file($file)) {
			return NULL;
		}
		
		return array_map('str_getcsv', file($file));
	}
	
	
	
	
	function r3cleanexp($val) {
		return '"' . trim(str_replace(array('"'), array('\"'), $val)) . '"';
	}
	
	
	/*
	*	r3cleannull - Cleans a value and replaces the NULL values with something particullary sent
	* 
	* 	@desc	This function will replace any blank or NULL values with a particular value sent, otherwise it will just return the same value passed to it
	*	@param	string	Value to be checked
	* 	@param	string	Value to be returned if the date value is blank, empty, incorrectly formatted, etc... (default = '')
	* 	@return	string	Any value passed in
	* */
	function r3cleannull($value, $blankRetVal='') {
		$value = trim(str_replace(
			array("&nbsp\\;"),
			array(''),
			$value
		));
		
		if(empty($value)) {
			return $blankRetVal;
		}
		
		return $value;
	}
	
	function r3print($output='', $blankRetVal='', $AddlOptionsArray=NULL) {
		$output = trim(str_replace(
			array("&nbsp;", "&nbsp\\;"),
			array('', ''),
			$output
		));
		$output = trim(str_replace(
			array("&nbsp;"),
			array(''),
			$output
		));
		
		if(empty($output)) {
			return $blankRetVal;
		}
		
		return $AddlOptionsArray['pre'] . $output . $AddlOptionsArray['post'];
	}
	
	function r3sort($dataArray, $key) {
		$sorterArray = array();
		$returnArray = array();
		
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			$sorterArray[$dataArray[$i][$key]] = 1;
		}
		
		ksort($sorterArray);
		
		foreach($sorterArray AS $sortBy => $null) {
			for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
				if($sortBy == $dataArray[$i][$key]) {
					$returnArray[] = $dataArray[$i];
				}
			}
		}
		
		return $returnArray;
	}
	
	function r3multisort($dataArray, $key, $key2) {
		$sorterArray = array();
		$sorter2Array = array();
		$returnArray = array();
		
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			$sorterArray[$dataArray[$i][$key]] = 1;
		}
		
		ksort($sorterArray);
		
		foreach($sorterArray AS $sortBy => $null) {
			//	Now to take key2 and sort these results further...
			$sorterArray2 = NULL;
			for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
				if($sortBy == $dataArray[$i][$key]) {
//					$returnArray[] = $dataArray[$i];
					$sorterArray2[$dataArray[$i][$key2]] = 1;
				}
			}
			uksort($sorterArray2, 'strnatcasecmp');
			
			//	Now that it's sorted...add it into the $returnArray...
			foreach($sorterArray2 AS $sortBy2 => $null2) {
				for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
					if($sortBy == $dataArray[$i][$key] && $sortBy2 == $dataArray[$i][$key2]) {
						$returnArray[] = $dataArray[$i];
					}
				}
			}
		}
		
		//	Now to take the 2nd key to sort again...
		
		return $returnArray;
	}
	
	/*
	*	r3date - Print formatted date, even if it's a nasty date
	* 
	* 	@desc	This function will be used with the reporting class and the database to print formatted dates to the page without seeing nasty 1969-12-31 or 2999-12-31 date errors
	*	@param	string	Date format to have the value/date returned in (ex. m/d/Y)
	* 	@param	string	Date value to be checked (ex. 2013-05-14)
	* 	@param	string	Value to be returned if the date value is blank, empty, incorrectly formatted, etc... (default = '')
	* 	@return	string	Date formatted value
	* */
	function r3date($format, $date, $blankRetVal='') {
		$date = trim(str_replace(
			array("&nbsp\\;", "&nbsp;"),
			array('', ''),
			$date
		));
		
		if(
			empty($date)
			|| $date == '1969-12-31'
			|| $date == '2999-12-31'
			|| $date == '0000-00-00'
		) {
			return $blankRetVal;
		}
		
		return date($format, strtotime($date));
	}
	
	
	
	
	
	/*
		Calculating Counter data...
	*/
	function r3getcount($dataArray=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		$Count = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			$Count++;
		}
		
		return $Count;
	}
	
	function r3getsubcount($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		$Count = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] == $SubFieldValue1) {
				$Count++;
			}
		}
		
		return $Count;
	}
	
	function r3getsubsubcount($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL, $SubFieldName2=NULL, $SubFieldValue2=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		$Count = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] == $SubFieldValue1 && $dataArray[$i][$SubFieldName2] == $SubFieldValue2) {
				$Count++;
			}
		}
		
		return $Count;
	}
	
	function r3getsubsubsubcount($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL, $SubFieldName2=NULL, $SubFieldValue2=NULL, $SubFieldName3=NULL, $SubFieldValue3=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		$Count = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] == $SubFieldValue1 && $dataArray[$i][$SubFieldName2] == $SubFieldValue2 && $dataArray[$i][$SubFieldName3] == $SubFieldValue3) {
				$Count++;
			}
		}
		
		return $Count;
	}
	
	/*
		Calculating Total data...
	*/
	function r3gettotal($dataArray=NULL, $Column=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		//	If $Column is NULL, then return NULL...
		if(empty($Column)) {
			return NULL;
		}
		
		$Total = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			$Total += $dataArray[$i][$Column];
		}
		
		return $Total;
	}
	
	function r3getsubtotal($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL, $Column=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		//	If $Column is NULL, then return NULL...
		if(empty($Column)) {
			return NULL;
		}
		
		$Total = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] != $SubFieldValue1) {
				$Total += $dataArray[$i][$Column];
			}
		}
		
		return $Total;
	}
	
	function r3getsubsubtotal($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL, $SubFieldName2=NULL, $SubFieldValue2=NULL, $Column=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		//	If $Column is NULL, then return NULL...
		if(empty($Column)) {
			return NULL;
		}
		
		$Total = 0;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] != $SubFieldValue1 && $dataArray[$i][$SubFieldName2] != $SubFieldValue2) {
				$Total += $dataArray[$i][$Column];
			}
		}
		
		return $Total;
	}
	
	/*
		Calculating the Maximum value in a list of values...
	*/
	function r3getsubmax($dataArray, $whereField, $whereValue, $totalField) {
		$max = 0;
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
			if($dataArray[$i][$whereField] == $whereValue) {
//			print "if({$dataArray[$i][$whereField]} == {$whereValue}) {<br />\n";
				$max = (($dataArray[$i][$totalField] > $max) ? $dataArray[$i][$totalField] : $max);
//				print "{$max}<br />\n";
			}
		}
		return $max;
	}
	
	/*
		Calculating single value from a list of data...
	*/
	function r3getvalues($dataArray, $field) {
		$retArray = NULL;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			$retArray[] = $dataArray[$i][$field];
		}
		
		return $retArray;
	}
	
	function r3getsubvalues($dataArray, $whereField, $whereValue, $returnField) {
		$dataCnt = count($dataArray);

		for($i = 0; $i < $dataCnt; $i++) {
			if($dataArray[$i][$whereField] == $whereValue) {
				return $dataArray[$i][$returnField];
			}
		}

		return NULL;
	}
	
	/*
		Calculating Sub-Array data...
	*/
	function r3getsubdata($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		$retArray = NULL;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] == $SubFieldValue1) {
				$retArray[] = $dataArray[$i];
			}
		}
		
		return $retArray;
	}

	function r3subdatavalues($dataArray, $whereField, $whereValue, $field) {
		$tmpArray = array();
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
			if($dataArray[$i][$whereField] == $whereValue) {
				$tmpArray[] = $dataArray[$i][$field];
			}
		}
		return $tmpArray;
	}
	
	function r3getsubsubdata($dataArray=NULL, $SubFieldName1=NULL, $SubFieldValue1=NULL, $SubFieldName2=NULL, $SubFieldValue2=NULL) {
		if(empty($dataArray)) {
			return NULL;
		}
		
		$retArray = NULL;
		for($i = 0, $iCnt = count($dataArray); $i < $iCnt; $i++) {
			if($dataArray[$i][$SubFieldName1] == $SubFieldValue1 && $dataArray[$i][$SubFieldName2] == $SubFieldValue2) {
				$retArray[] = $dataArray[$i];
			}
		}
		
		return $retArray;
	}
	
	/*
		Calculating Distinct values...
	*/
	function r3getdistinctvalues($dataArray, $distinctField) {
		$countArray = NULL;
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
//			if($dataArray[$i][$whereField] == $whereValue) {
				$countArray[$dataArray[$i][$distinctField]] = 1;
//			}
		}
		return array_keys($countArray);
	}
	
	/*
		Calculating Distinct values...
	*/
	function r3getsubdistinctvalues($dataArray, $whereField, $whereValue, $distinctField) {
		$countArray = NULL;
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
			if($dataArray[$i][$whereField] == $whereValue) {
				$countArray[$dataArray[$i][$distinctField]] = 1;
			}
		}
		return array_keys($countArray);
	}
	
	/*
		Calculating Distinct Counts...
	*/
	function r3getsubdistinctcount($dataArray, $whereField, $whereValue, $distinctField) {
		$countArray = NULL;
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
			if($dataArray[$i][$whereField] == $whereValue) {
				$countArray[$dataArray[$i][$distinctField]] = 1;
			}
		}
		return count($countArray);
	}

	function r3subsubdistinctcount($dataArray, $whereField1, $whereValue1, $whereField2, $whereValue2, $distinctField) {
		$countArray = NULL;
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
			if($dataArray[$i][$whereField1] == $whereValue1 && $dataArray[$i][$whereField2] == $whereValue2) {
				$countArray[$dataArray[$i][$distinctField]] = 1;
			}
		}
		return count($countArray);
	}

	function r3subsubsubdistinctcount($dataArray, $whereField1, $whereValue1, $whereField2, $whereValue2, $whereField3, $whereValue3, $distinctField) {
		$countArray = NULL;
		for($i = 0, $icnt = count($dataArray); $i < $icnt; $i++) {
			if($dataArray[$i][$whereField1] == $whereValue1 && $dataArray[$i][$whereField2] == $whereValue2 && $dataArray[$i][$whereField3] == $whereValue3) {
				$countArray[$dataArray[$i][$distinctField]] = 1;
			}
		}
		return count($countArray);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/*
		Determining file formats, sizes, output, etc.
	*/
	function r3byteconvert($bytes) {
		$bytes = str_replace(
			array('Bytes',	'KB'),
			array('B',		'Kb'),
			$bytes
		);
		
		$s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');
		$e = floor(log($bytes) / log(1024));
		return sprintf('%.2f ' . $s[$e], ($bytes / pow(1024, floor($e))));
	}
	
	function r3tobytesize($p_sFormatted) {
	    $aUnits = array('B'=>0, 'KB'=>1, 'MB'=>2, 'GB'=>3, 'TB'=>4, 'PB'=>5, 'EB'=>6, 'ZB'=>7, 'YB'=>8);
	    $sUnit = strtoupper(trim(substr($p_sFormatted, -2)));
	    if (intval($sUnit) !== 0) {
	        $sUnit = 'B';
	    }
	    if (!in_array($sUnit, array_keys($aUnits))) {
	        return false;
	    }
	    $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
	    if (!intval($iUnits) == $iUnits) {
	        return false;
	    }
	    return $iUnits * pow(1024, $aUnits[$sUnit]);
	}
	
	function r3formatsize($size) {
		$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		
		if($size == 0) {
			return('n/a');
		} else {
				return (round($size/pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i]);
		}
	}
	
	/*
		r3fy - returns an array of the applicable FY's by each year
		Optional parameter to pass in a specific start year and it will return the FY range for that specific year only...
		2nd optional parameter to pass in a specific end year and it will return from the FY start to the end years only...
		...otherwise it will return every FY from 2000 to %CURRENTYEAR%
	*/
	function r3fy($SpecificStartYear=NULL, $SpecificEndYear=NULL) {
		if(!empty($SpecificStartYear) && !empty($SpecificEndYear)) {
			if($SpecificStartYear > $SpecificEndYear) {
				return NULL;
			}
		}
		
		if(!empty($SpecificStartYear) || !empty($SpecificEndYear)) {
			if(!empty($SpecificStartYear)) {
				$StartYear = $SpecificStartYear;
				$EndYear = $SpecificStartYear;
			} else {
				$StartYear = 2000;
			}
			if(!empty($SpecificEndYear)) {
				$EndYear = $SpecificEndYear;
			}
		} else {
			$StartYear = 2000;
			$EndYear = date('Y') + 1;
		}
		
		$retArray = NULL;
		
		for($i = $StartYear-1, $inc = 0; $i < $EndYear; $i++, $inc++) {
			$ThisYear = $StartYear + $inc-1;
			$NextYear = $StartYear + $inc;
			
			$retArray[$NextYear] = array(
				'StartDate' => "{$ThisYear}-10-01",
				'EndDate' => "{$NextYear}-09-30",
			);
		}
		
		return $retArray;
	}
?>