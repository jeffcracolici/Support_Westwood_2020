<?php
	$all_count = count(fetch_businesses());
	$q_cat = $_GET['category'];
	$cats = fetch_business_cats();
	// print_r($cats);
?>

<nav class="navbar row py-3" style="background-color:#262456;color:#ffffff;">
	<div class="container px-0">		
		<div class="col-md-4">
			<select id="cat_filter" class="form-control w-100">				
				<?php
					if(empty($q_cat) || $q_cat == 'all') {
						echo '<option value="all" selected>All Categories (' . $all_count . ') </option>';
					} else {
						echo '<option value="all">All Categories (' . $all_count . ') </option>';
					}
					
					foreach($cats as $cat) {
						if($q_cat == $cat->slug) {
							echo '<option value="' . $cat->slug .'" selected>' . $cat->name . ' (' . count(fetch_businesses_by_cat($cat->term_id)) . ')</option>';
						} else {
							echo '<option value="' . $cat->slug .'">' . $cat->name . ' (' . count(fetch_businesses_by_cat($cat->term_id)) . ')</option>';
						}					
					}
				?>
			</select>
		</div>
	</div>
</nav>
<script>
	$(document).ready(function() {
		$("#cat_filter").change(function() {
			var selection = $("#cat_filter").val();
  			window.location.href = "?category=" + selection;
		});
	});
</script>