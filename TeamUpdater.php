<?php

/*
	Plugin Name: TeamUpdater
	Plugin URI: http://Bequick28.nl
	Description: Update team in Bequick club page
	Author: K. Zoon & M. Rutgers
	Version: 2.0
	Author URI: http://Bequick28.nl
*/
function teamupdater_admin() {
    include('TeamUpdater_update_admin.php');
}
function teamupdater_admin_actions() {
    add_submenu_page( 'edit.php?post_type=team', 'Teamupdater', 'Teamupdater', '1', 'teamupdater', 'teamupdater_admin' );
}

add_action( 'admin_menu', 'teamupdater_admin_actions' );
add_action( 'wp_ajax_rawDataParser', 'rawDataParser_callback' );

function rawDataParser_callback() {
	global $wpdb; // this is how you get access to the database

	//var_dump($_POST);
	$teamdata = [];
	foreach($_POST['rawData'] as $record) {
		//echo $record['teamnaam'] ."<br>";
		$teamdata[$record['teamnaam']]['teamnaam'] = $record['teamnaam'];

		// check for  empty teamcode
		if($record['teamcode'] == '-1')
			$teamdata[$record['teamnaam']]['teamcode'] = $record['lokaleteamcode'];
		else
			$teamdata[$record['teamnaam']]['teamcode'] = $record['teamcode'];

		// fill poules
		if($record['competitiesoort'] == 'beker')
			$teamdata[$record['teamnaam']]['bekerPoules'][$record['poulecode']] = ["poulecode" => $record['poulecode'], "title" => $record['competitienaam'], "klasse"=> $record['klassepoule'], "type"=> $record['competitiesoort']];
		else
			$teamdata[$record['teamnaam']]['competitiePoules'][$record['poulecode']] = ["poulecode" => $record['poulecode'], "title" => $record['competitienaam'], "klasse"=> $record['klassepoule'], "type"=> $record['competitiesoort']];
	};

	foreach($teamdata as $team){
		asort($teamdata[$team['teamnaam']]['bekerPoules']);
		asort($teamdata[$team['teamnaam']]['competitiePoules']);
	};

 	echo '<pre>';
	print_r($teamdata);
	echo '</pre>';

	//Form data sent
	//for a given post type, return all
	// $post_type = 'team';
	// $tax = 'teamtype';
	// $tax_terms = get_terms($tax);
	// if ($tax_terms) {
	// 	foreach ($tax_terms as $tax_term) {
	// 		$args = array(
	// 			'post_type' => $post_type,
	// 			$tax => $tax_term->slug,
	// 			'post_status' => 'publish',
	// 			'posts_per_page' => -1,
	// 			'caller_get_posts' => 1
	// 		);

	// 		$my_query = null;
	// 		$my_query = new WP_Query($args);

	// 		if ($my_query->have_posts()) {
	// 			echo '<br>List of ' . $post_type . ' where the taxonomy ' . $tax . '  is ' . $tax_term->name . "<br>";
	// 			while ($my_query->have_posts()) : $my_query->the_post();
	 				?>
	<!-- 				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
	-->
	<?php

	// 				$teamnaam = $my_query->post->post_title;
	// 				echo $teamnaam ."</a>";
	// 				$teamnaam = "Be Quick \'28 " . get_post_meta( $my_query->post->ID , 'wpcf-sl-teamnaam' , true );

	// 				// update the Post meta
	// 				if ($teamdata[$teamnaam] != NULL) {
	// 					$result = update_post_meta($my_query->post->ID, 'wpcf-sl-teamcode', $teamdata[$teamnaam]['teamcode']);
	// 					echo "  -> teamid:" . $teamdata[$teamnaam]['teamcode'];

	// 					// insert competitiecode
	// 					foreach($teamdata[$teamnaam]['competitiePoules'] as $poule) {
	// 							$result = update_post_meta($my_query->post->ID, 'wpcf-sl-competitiecode', $poule['poulecode']);
	// 							$titel = $poule['title']. " " . $poule['klasse'];
	// 							if(is_numeric(substr($titel, 0, 1)) OR substr($titel, 0, 1) == 'B'){
	// 								// knip eerste woord eraf
	// 								$titel = strstr($titel," ");
	// 							}
	// 							$result = update_post_meta($my_query->post->ID, 'wpcf-titlecompetitie', $titel );

	// 							echo " -> Competitie: " . $poule['poulecode'];
	// 					}

	// 					//insert poulecodes
	// 					$i = 1;
	// 					foreach ($teamdata[$teamnaam]['bekerPoules'] as $poule) {

	// 							$result = update_post_meta($my_query->post->ID, 'wpcf-sl-poulecode-' . $i, $poule['poulecode']);
	// 							$titel = $poule['title']. " " . $poule['klasse'];
	// 							if(is_numeric(substr($titel, 0, 1)) OR substr($titel, 0, 1) == 'B'){
	// 								// knip eerste woord eraf
	// 								$titel = strstr($titel," ");
	// 							}
	// 							$result = update_post_meta($my_query->post->ID, 'wpcf-titlepoule' . $i, $titel);
	// 							echo " -> Beker Poule" . $i . ": " . $poule['poulecode'] ." ".$titel;
	// 							$i++;
	// 					}

	// 				} else {
	// 					echo "  -> no team data found";
	// 				}

	// 				echo '<br />';
	// 			endwhile;
	// 		}
	// 		wp_reset_query();
	// 	}
	// }
	wp_die(); // this is required to terminate immediately and return a proper response
}
?>
