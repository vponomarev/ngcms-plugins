<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// ==================================================================
// Comments actions interceptors
// ==================================================================

class FilterComments {
	// Form generator
	function addCommentsForm(&$tvars) { return 1;}

	// Adding executor [ done BEFORE actual add and CAN block adding ]
	function addComments($userRec, $newsRec, &$tvars, &$SQL) { return 1; }

	// Adding notificator [ after successful adding ]
	function addCommentsNotify($userRec, $newsRec, &$tvars, $SQL, $commID) { return 1; }

	// Show comments
	function showComments($newsID, $commRec, $comnum, &$tvars) { return 1; }

}