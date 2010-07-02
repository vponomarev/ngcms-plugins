<?php

// CLASS DEFINITION: User's profile filter
class p_uprofileFilter {
	// Show profile call :: preprocessor (call directly after profile fetch)
	function showProfilePre($userID, &$SQLrow) { return 1; }

	// Show profile call :: processor  (call after all processing is finished and before show)
	function showProfile($userID, $SQLrow, &$tvars) { return 1; }

	// Edit profile FORM call :: preprocessor (call directly after profile fetch)
	function editProfileFormPre($userID, &$SQLrow) { return 1; }

	// Edit profile FORM call :: processor  (call after all processing is finished and before show)
	function editProfileForm($userID, $SQLrow, &$tvars) { return 1; }

	// Edit profile call :: processor  (call after all processing is finished and before real SQL update)
	function editProfile($userID, $SQLrow) { return 1; }

	// Edit profile call :: notifier (call after successful editing )
	function editProfileNotify($userID, $SQLrow, &$SQLnew) { return 1; }

}
