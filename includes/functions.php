<?php
	function DumpHeaders($filename) {
		global $HTTP_USER_AGENT;
		
		$isIE = 0;
		
		if(strstr($HTTP_USER_AGENT, 'compatible; MSIE ') !== FALSE && strstr($HTTP_USER_AGENT, 'Opera') === FALSE) {
		    $isIE = 1;
		}

		// A Pox on Microsoft and it's Office!

		    // Try to pop up the "save as" box
		    // IE makes this hard.  It pops up 2 save boxes, or none.
		    // http://support.microsoft.com/support/kb/articles/Q238/5/88.ASP
		    // But, accordint to Microsoft, it is "RFC compliant but doesn't
		    // take into account some deviations that allowed within the
		    // specification."  Doesn't that mean RFC non-compliant?
		    // http://support.microsoft.com/support/kb/articles/Q258/4/52.ASP
		    //
		    // The best thing you can do for IE is to upgrade to the latest
		    // version
		    if($isIE) {
		       header("Cache-Control: ");
				header("Pragma: ");
			   
				// http://support.microsoft.com/support/kb/articles/Q182/3/15.asp
		        // Do not have quotes around filename, but that applied to
		        // "attachment"... does it apply to inline too?
		        //
		        // This combination seems to work mostly.  IE 5.5 SP 1 has
		        // known issues (see the Microsoft Knowledge Base)
		        header("Content-Disposition: inline; filename=$filename");

		        // This works for most types, but doesn't work with Word files
		        header("Content-Type: application/download");

		        // These are spares, just in case.  :-)
		        //header("Content-Type: $type0/$type1; name=\"$filename\"");
		        //header("Content-Type: application/x-msdownload; name=\"$filename\"");
		        //header("Content-Type: application/octet-stream; name=\"$filename\"");
		    } else {
		        header("Content-Disposition: attachment; filename=" . $filename . "");
		        // application/octet-stream forces download for Netscape
		        header("Content-Type: application/octet-stream");
		    }
	} // end DumpHeaders()
?>