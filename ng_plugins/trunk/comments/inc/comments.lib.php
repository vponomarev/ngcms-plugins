<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// ==================================================================
// Comments actions interceptors
// ==================================================================

class FilterComments {
	// Form generator
	function addCommentsForm($newsID, &$tvars) { return 1;}

	// Adding executor [ done BEFORE actual add and CAN block adding ]
	// Returning values in simple mode:
	// 0 - block adding comment
	// 1 - allow adding comment
	// Returning values in advanced mode:
	// array(
	//	'result' = 0 - block adding comments
	//		 = 1 - allow adding comment
	//	'errorText' = text message (that will be showed) in case if adding was blocked
	// )
	function addComments($userRec, $newsRec, &$tvars, &$SQL) { return 1; }

	// Adding notificator [ after successful adding ]
	function addCommentsNotify($userRec, $newsRec, &$tvars, $SQL, $commID) { return 1; }

	// Show comments
	function showComments($newsID, $commRec, $comnum, &$tvars) { return 1; }

	// Show comments external table join initiator.
	// Function should return specially formatted array:
	// Each array antry is a name of table for joining [ currenly only `users` is supported ] with value of configuration sub array
	// Sub array should contain configuration parameters:
	//		fields - list of fields to join
	// example: array ( 'users' => array ( 'fields' => array ( 'avatar' )))
	function commentsJoinFilter(){ return array(); }
}