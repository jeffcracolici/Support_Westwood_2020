<?php
	get_header();
	$q_cat = $_GET['category'];
	$cat_id;
	if(empty($q_cat) || $q_cat == 'all') {
		$cat_id = 0;
	} else {
		$q_cat_term = get_term_by( 'slug', $q_cat, 'category' );
		$cat_id = $q_cat_term->term_id;
	}
	
	$bizs = fetch_businesses_by_cat($cat_id);
	$logo = 'https://support.westwoodnjchamber.com/wp-content/uploads/2020/07/WW-Chamber-logo_final.jpg';
?>
<div class="bg-white py-3">
	<div class="container ">
		<div class="row">
			<div class="col-lg-3 text-center">
				<a href="https://www.westwoodnjchamber.com/">
					<img alt="Westwood Chamber of Commerce" src="<?php echo $logo; ?>" style="width:100%;max-width:250px;">
				</a>
			</div>
			<div class="col-lg-9 text-lg-left text-center">
				<h1>
					Support Westwood, NJ Businesses
				</h1>
				<p>
					Please see below for a list of participating Westwood businesses. Visit their website or call to verify information and hours.
				</p>
			</div>
		</div>	
	</div>
</div>

<?php
	get_template_part('navbars/filter');
?>

<div class="pb-10">
	<div class="container">
		<div class="row">
			<?php
				foreach($bizs as $biz => $data) {
					echo '<div class="col-md-6 my-3">';
					echo '<div class="bg-white p-3 biz-bubble" style="border:3px solid #262456;">';
					biz_row_two($data->ID);
					echo '</div>';
					echo '</div>';
				}
			?>
		</div>
	</div>
</div>

<?php
	get_footer();
?>
