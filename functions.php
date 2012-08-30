<?php 

function checkin_users_in( $args = array() ) {
	
	// Query the users that are checked in and return them.
	
	$args = wp_parse_args( $args, array(
	
		'meta_query' => array(
			
			array(
				'key' => 'p2checkin_currently_checked_in',
				'value' => true // the user is checked in
			)
			
		)
	
	));
	
	$users = get_users( $args );
	
	return $users;

}


function checkin_users_in_ids() {
	
	$users = checkin_users_in();
	
	foreach ( $users as $user ) {
		
		$id = $user->ID;
		
		$userids[] = $id;
		
	}
	
	return $userids;

}


function checkin_users_out_ids() {

	$users = checkin_users_out();
	
	foreach ( $users as $user ) {

		$id = $user->ID;

		$userids[] = $id;

	} 
	
	return $userids;

}


function app_ids_and_emails( $type = 'in' ) {
	
	if ( $type == 'in' ) {
		$ids = checkin_users_in_ids();
	} else if ( $type == 'out' ) {
		$ids = checkin_users_out_ids();
	}
	
	foreach ( $ids as $id ) {
		
		$email = get_the_author_meta( 'user_email', $id );
		$data[] = array( "id" => $id, "email" => $email );
	
	}
	
	return $data;

}


function app_output() {

	switch_to_blog(2);
	
	if ( $currentuser == 24 || current_user_can( 'manage_options' ) ) {
		
		$in = app_ids_and_emails( 'in' );	
		$out = app_ids_and_emails( 'out' );	

		echo '<p>' . json_encode( $in ) . '</p>';
		echo '<p>' . json_encode( $out ) . '</p>';
	
		restore_current_blog();
	
	}
	
}


function checkin_users_out( $args = array() ) {

	// Query the users that are not currently checked in.
	
	$args = wp_parse_args( $args, array(
	
		'exclude' => checkin_users_in_ids()
	
	));
	
	$users = get_users( $args );
	
	return $users;

}


function checkin_users_in_html() {
	
	// Take the users and drop them into an HTML list, echo them.
	
	$users = checkin_users_in();
	
	$html = '';
	
	$i = 0;
	
	foreach( $users as $user ) {
	
		$id = $user->ID;
		$user_info = get_userdata( $id );
		$name = $user_info->display_name;
	
		$i++;
		
		$item = checkin_user_markup( $user, 160 );
		
		$currentuser = get_current_user_id();
		
		$badge_items = get_user_meta( $id, 'simplebadges_badges', false );
		
		shuffle( $badge_items );
		
		$badges = '';
		$badges .= '<ul>';
		
		$b = 0;
		
		foreach ( $badge_items as $badge_item ) if ( $b++ < 2 ) {
			
			if ( class_exists('MultiPostThumbnails') ) {	
				
				$badges .= MultiPostThumbnails::get_the_post_thumbnail( 'simplebadges_badge', 'simplebadges-smaller', $badge_item, array( 40, 40 ) ); 
				
			} else {
				
				$badge .= get_the_post_thumbnail( $badge_item, array(40,40) );
			
			}
			
		}
		
		$badges .= '</ul>';
		$user_id = $user->ID;
		if ( $user_id == 37 ) { $badges = ''; }
		
		$meta = '<div class="meta"><h4>' . $name . '</h4>' . $badges . '</div>';
		
		if ( $currentuser == 24 || current_user_can( 'manage_options' ) ) {
			
			if ( $user_id == 37 ) {
				$i--;
			}
			
			$item = '<li class="user-' . $user->ID . ' number-' . $i . '"><a href="http://checkin.atfounders.com/?checkout=true&checkinuser=' . $user->ID . '">' . $item . $meta . '</a></li>';
		
		} else {
		
			$item = '<li class="user-' . $user->ID . '">' . $item . $meta . '</li>';
		
		}
		
		$html .= $item;
		
	}
	
	echo $html;
	
}


function checkin_users_out_html() {

	// Takes logged out users and puts them in a list.
	
	$users = checkin_users_out();
	
	$html = '';
	
	foreach( $users as $user ) {
		
		$item = checkin_user_markup( $user, 80 );
		$id = $user->ID;		
		$user_info = get_userdata( $id );
		$name = $user_info->display_name;
		
		$currentuser = get_current_user_id();
		
		$checkin_time_out = get_user_meta( $id, 'p2checkin_time_checked_out', true );
		$timenow = current_time( 'timestamp', 1 );
		
		if ( $checkin_time_out != '' ) {
		
			$time_diff = $timenow - $checkin_time_out;
			$time_diff_human = number_format( ( $time_diff / 60 / 60 ), 2, '.', '' );	
			
			$time_diff_human = $time_diff_human . ' hours ago';
		
		} else {
		
			$time_diff_human = 'never!';
		
		}
		
		if ( $currentuser == 24 || current_user_can( 'manage_options' ) ) {
		
			$item = '<li><a target="_self" href="http://checkin.atfounders.com/?checkin=true&checkinuser=' . $id . '">' . $item . '</a><h4>' . $name . '</h4><p><em>Last checked in ' . $time_diff_human . '</em></p></li>';
		
		} else {
		
			$item = '<li>' . $item . '<h4>' . $name . '</h4><p><em>Last checked in ' . $time_diff_human . '</em></p></li>';
		
		}
		
		$html .= $item;
		
	}
	
	echo $html;

}


function checkin_show_users() {
	
	// Make a <ul> around the HTML list.
	
	echo '<ul class="checked-in">';
	
	switch_to_blog(2);	
	echo checkin_users_in_html();
	
	echo '</ul>';
	echo '<hr />';
	echo '<h3>Checked out:</h3>';
	echo '<ul class="checked-out">';
	
	echo checkin_users_out_html();
	restore_current_blog();
	
	//echo '<li class="guest"><a target="_self" href="http://chat.atfounders.com/join-and-chat/">Guest</a></li>';
	echo '</ul>';
	
}


function checkin_user_markup( $user, $size ) {

	$output = '';
	
	$name = $user->display_name;
	
	$id = $user->ID;
	
	$avatar = get_avatar( $id, $size );
	
	$output .= $avatar;
	
	return $output;

}


function checkin_action() {
	
	// Check to make sure the user is logged in as an admin.
	// If they are, check to see if they have performed an action.
	// If they have, perform the action and save the data.
	
	$currentuser = get_current_user_id();
		
	if ( $currentuser == 24 || current_user_can( 'manage_options' ) ) {
		
		if ( isset( $_GET[checkout] ) && isset( $_GET[checkinuser] ) ) {
		
			$user = $_GET[checkinuser];
			
			switch_to_blog(2);
			
			$checkin_time_in = get_user_meta( $user, 'p2checkin_time_checked_in', true );
			$checkin_time_out = get_user_meta( $user, 'p2checkin_time_checked_out', true );
			
			// Only proceed if the checked in time is greater than the checked out time
			// This means they're currently checked in.
			
			if ( $checkin_time_in > $checkin_time_out ) {
			
				// Checked them out
				update_user_meta( $user, 'p2checkin_currently_checked_in', false );
				
				// Save their checked out time
				update_user_meta( $user, 'p2checkin_time_checked_out', current_time( 'timestamp', 1 ) );
				
				// Update running total of checked in time.
				$checkin_time_out_now = get_user_meta( $user, 'p2checkin_time_checked_out', true );
				$checkin_time_sofar = get_user_meta( $user, 'p2checkin_totaltime', true );
				$checkin_time_session = ( $checkin_time_out_now - $checkin_time_in );
				$checkin_time_sofar += $checkin_time_session;
				
				update_user_meta( $user, 'p2checkin_totaltime', $checkin_time_sofar );
				
				do_action( 'founders_checkin_checkedout', $user );
				
			}
			
			restore_current_blog();
		
		}
		
		// If checked in
		if ( isset( $_GET[checkin] ) && isset( $_GET[checkinuser] ) ) {
		
			$user = $_GET[checkinuser];
		
			switch_to_blog(2);
		
			//  Prevent cheating
			$checkin_time_in = get_user_meta( $user, 'p2checkin_time_checked_in', true );
			$checkin_time_out = get_user_meta( $user, 'p2checkin_time_checked_out', true );
			
			// Make sure the checked out time is greater than the checked in time.
			if ( $checkin_time_out > $checkin_time_in || $checkin_time_in == '' ) {
				
				update_user_meta( $user, 'p2checkin_currently_checked_in', true );
				
				update_user_meta( $user, 'p2checkin_time_checked_in', current_time( 'timestamp', 1 ) );
			
				do_action( 'founders_checkin_checkedin', $user );
				
			}
			
			restore_current_blog();
			
		}
	
	}
	
}


// Stay logged in longer

add_filter( 'auth_cookie_expiration', 'keep_me_logged_in_for_1_year' );

function keep_me_logged_in_for_1_year( $expirein ) {

	return 31556926; // 1 year in seconds

}


// If it isn't logged in, make it clear that's the problem.

function checkin_login_check() {

	if ( is_user_logged_in() ) {
	
	} else {
	
		echo '<div id="login-alert"><h3>Logged out.</h3><p>Yell at Ryan to get this logged back on.</p></div>';
	
	}

}

