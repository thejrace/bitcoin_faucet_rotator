<?php

	class Page {
		public function userHasPermission( $permID, $userperms ){
			return ( in_array( $permID, $userperms ) && $userperms[$permID] );
		}
	}