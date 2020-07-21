<nav class="navbar row sticky-top py-3 d-none" style="background-color:#262456;color:#ffffff;">
	<div class="container px-0">
		<div class="col-lg-3">
			Business
		</div>
		<div class="col-lg-9  d-none d-lg-block">
			<div class="row">
				<div class="col text-center">
					Pickup
				</div>
				<div class="col text-center">
					Delivery
				</div>
				<div class="col text-center">
					Uber Eats
				</div>
				<div class="col text-center">
					Doordash
				</div>
				<div class="col text-center">
					Grubhub
				</div>
				<div class="col text-center">
					Postmates
				</div>
				<div class="col text-center">
					Slice
				</div>
			</div>
		</div>
	</div>
</nav>

<div class="bg-white d-none">
	<div class="container">
		<?php	
			$bizes = fetch_experiences_by_cat(2);
			echo '<h2 id="food" class="py-5"><i class="fas fa-utensils mr-3"></i>Restaurants / Food</h2>';
			foreach($bizes as  $id => $data) {
				biz_row($data->ID);
			}
			$bizes = fetch_experiences_by_cat(1);
			echo '<h2 id="other" class="py-5"><i class="fas fa-cash-register mr-3"></i>Other Businesses</h2>';
			foreach($bizes as  $id => $data) {
				biz_row($data->ID);
			}
		?>
	</div>
</div>