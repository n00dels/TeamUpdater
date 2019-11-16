<?php
wp_enqueue_media();
?>

<script src="https://dexels.github.io/navajofeeds-json-parser/js/dist/feed-0.0.1.js"></script> 
<script> 
feed.init({ 
	clientId: "q610F4UNDg" 
}); 

var update = function(){
  
	feed.server.get("teams", {gebruiklokaleteamgegevens: "NEE",competitieperiode:"ALLES", client_id: "q610F4UNDg"})
	.done(function(sportlinkData){
			var result = jQuery('#result');
			var data = {
				'action': 'rawDataParser',
				'rawData': sportlinkData
			};

			// since 2.8 ajaxurl is always defined in the admin header and              points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				result.html(response);
				console.log('Got this from the server: ' + response);
			});
	});
};
</script>

<?php

if ($_POST['teamfoto_hidden'] == 'Y') {
    //Form data sent
    //for a given post type, return all
    $post_type = 'team';
    $tax = 'teamtype';
    $tax_terms = get_terms($tax);
    if ($tax_terms) {
        foreach ($tax_terms as $tax_term) {
            $args = array(
                'post_type' => $post_type,
                $tax => $tax_term->slug,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'caller_get_posts' => 1
            );

            $my_query = null;
            $my_query = new WP_Query($args);

            if ($my_query->have_posts()) {
                echo '<br>List of ' . $post_type . ' where the taxonomy ' . $tax . '  is ' . $tax_term->name . "<br>";
                while ($my_query->have_posts()) : $my_query->the_post();
                    ?>
                    <p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
                    <?php
                    if ($_POST['teamfoto_hidden'] == 'Y') {
                        $teamnaam = $my_query->post->post_title;
                        if ($_POST['teamfoto'] != "") {
                            $result = update_post_meta($my_query->post->ID, 'wpcf-teamfoto', $_POST['teamfoto']);
                            $result = update_post_meta($my_query->post->ID, 'wpcf-onderschrift', "");
                            Echo $teamnaam . "  -> teamfoto:" . $_POST['teamfoto'];
                        }
                    }
		echo '<br />';
                endwhile;
            }
            wp_reset_query();
        }
    }
} else {
//Normal page display
	?>
		<div class="wrap">
			<h1>Reset Teamfoto</h1>
			<form name="teamfoto_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<td><label for="upload_image">
				<input type="hidden" name="teamfoto_hidden" value="Y">
				<input id="upload_image" type="text" size="36" name="teamfoto" value="" />
				<input id="upload_image_button" type="button" value="choose Image" />
				</label>
			<input type="submit" name="Submit" value="Reset alle foto's!" />
			</form>
			</td>
		</div>
		<div class="wrap">
			<h1>Update teamcodes from sportlink</h1>
			<button onclick='update();'>Update Teams</button>
		</div>
		<div id="result"> </div>
	<?php
}
?>
