<?php
// THEME SETUP

function theme_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'menus' );
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );
	add_post_type_support( 'page', 'excerpt' );
}

theme_setup();

function labelify($value) {
	$explode = explode("_", $value);
	
	$final = "";
	
	foreach($explode as $word) {
		if($word == "api" || $word == "fb") {
			$final .= strtoupper($word) . ' ';
		} else {
			$final .= ucwords($word) . ' ';
		}
	}
	
	trim($final);	
	return $final;
}

function custom_post_type_list() {
	$custom_post_types = array();
		
	$custom_post_types['businesses'] = array(
		'slug' => 'businesses',
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_icon' => 'dashicons-location',
		'supports' => array('title', 'thumbnail'),
		'taxonomies' => array('category'),
	);
	
	return $custom_post_types;
}

function add_custom_post_types() {
	$custom_post_types = custom_post_type_list();
	foreach($custom_post_types as $id => $settings) {
		$attrs = $settings['attributes'];
		$args = array(
			'label' => labelify($id),
        	'public' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => $settings['slug'] ),
			'menu_icon' => $settings['menu_icon'],
			'capability_type' => $settings['capability_type'],
			'hierarchical' => $settings['hierarchical'],
			'supports' => $settings['supports'],
			'taxonomies' => $settings['taxonomies'],
        );
    	register_post_type( $id, $args );
	}
}
add_action( 'init', 'add_custom_post_types' );

function fetch_businesses() {
	$args = array(
		'post_type' => 'businesses', 
		'orderby' => 'title',
		'order' => 'ASC',
		'numberposts' => -1
	);
		
	return get_posts($args);
}

function fetch_businesses_by_cat($cat_id) {
	$args = array(
		'post_type' => 'businesses', 
		'orderby' => 'title',
		'order' => 'ASC',
		'numberposts' => -1,
		'cat' => $cat_id
	);
	
	return get_posts($args);
}

function fetch_business_cats() {
	 $args = array(
		 'taxonomy' => 'businesses',
		 'orderby' => 'name',
		 'order'   => 'ASC'
	 );
	
	//return get_categories($args);
	return get_terms('category');
}

function meta_boxes() {
	$boxes = array();
	$boxes[] = array('category', 'Business Category', '', 'businesses');
	$boxes[] = array('address', 'Phyiscal Address', '', 'businesses');
	$boxes[] = array('contact_info', 'Contact Info', '', 'businesses');
	$boxes[] = array('biz_info', 'Business Info', '', 'businesses');
	$boxes[] = array('other', 'Other Info', '', 'businesses');
	return $boxes;
}

add_action('add_meta_boxes', 'create_meta_box');
function create_meta_box() {
	$boxes = meta_boxes();	
	foreach($boxes as $id => $box)
	{		
		$box_id = $box[0];
		add_meta_box($box_id, $box[1], function($arg) use ($box_id) {
			$meta_fields = meta_fields($box_id);	
			foreach($meta_fields as $id => $info) {					
				$value = get_post_meta($arg->ID, $id, true);
				if($id == 'category') {
					$cat = get_the_category($arg->ID);
					$value = $cat[0]->term_id;
				}
				// echo $value;
				$type = $info['type'];
				if($type == 'text') {
					echo input_meta($id, $info, $value);
				} else {
					echo select_meta($id, $info, $value);
				}		
			}
		}, $box[3]);
	}	
}

function meta_fields($metabox) {
	$custom_meta_fields = array();	
	
	$custom_meta_fields['address'] = array('metabox' => 'address', 'type' => 'text');
	$custom_meta_fields['address_two'] = array('metabox' => 'address', 'type' => 'text');
	$custom_meta_fields['city'] = array('metabox' => 'address', 'type' => 'text');
	$custom_meta_fields['state'] = array('metabox' => 'address', 'type' => 'select', 'choices' => state_listing());
	$custom_meta_fields['zip'] = array('metabox' => 'address', 'type' => 'text', 'maxlength' => '5');
	
	$custom_meta_fields['phone'] = array('metabox' => 'contact_info', 'type' => 'text');
	$custom_meta_fields['url'] = array('metabox' => 'contact_info', 'type' => 'text');
	
	$custom_meta_fields['note'] = array('metabox' => 'other', 'type' => 'text');
	
	$custom_meta_fields['takeout'] = array('metabox' => 'biz_info', 'type' => 'select', 'choices' => choices_true_false());
	$custom_meta_fields['delivery'] = array('metabox' => 'biz_info', 'type' => 'select', 'choices' => choices_true_false());
	$custom_meta_fields['outdoor_eating'] = array('metabox' => 'biz_info', 'type' => 'select', 'choices' => choices_true_false());

	
	$custom_meta_fields['category'] = array('metabox' => 'category', 'type' => 'select', 'choices' => category_listing());
	
	if($metabox == null) {
		return $custom_meta_fields;
	} else {
		$final = array();
		foreach($custom_meta_fields as $id => $info) {
			if($metabox == $info['metabox']) {
				$final[$id] = $info;
			}
		}
		return $final;
	} 	
}

function choices_true_false() {
	$return = array('T' => 'True', 'F' => 'False');
	return $return;
}

function category_listing() {
	$array = array();
	$terms = get_categories( array('orderby' => 'name','order'   => 'ASC','hide_empty' => false));
	foreach($terms as $term) {
		$array[$term->term_id] = $term->name;
	}
	return $array;
}

function state_listing() {
	
return array(
    'AL'=>'Alabama',
    'AK'=>'Alaska',
    'AZ'=>'Arizona',
    'AR'=>'Arkansas',
    'CA'=>'California',
    'CO'=>'Colorado',
    'CT'=>'Connecticut',
    'DE'=>'Delaware',
    'DC'=>'District of Columbia',
    'FL'=>'Florida',
    'GA'=>'Georgia',
    'HI'=>'Hawaii',
    'ID'=>'Idaho',
    'IL'=>'Illinois',
    'IN'=>'Indiana',
    'IA'=>'Iowa',
    'KS'=>'Kansas',
    'KY'=>'Kentucky',
    'LA'=>'Louisiana',
    'ME'=>'Maine',
    'MD'=>'Maryland',
    'MA'=>'Massachusetts',
    'MI'=>'Michigan',
    'MN'=>'Minnesota',
    'MS'=>'Mississippi',
    'MO'=>'Missouri',
    'MT'=>'Montana',
    'NE'=>'Nebraska',
    'NV'=>'Nevada',
    'NH'=>'New Hampshire',
    'NJ'=>'New Jersey',
    'NM'=>'New Mexico',
    'NY'=>'New York',
    'NC'=>'North Carolina',
    'ND'=>'North Dakota',
    'OH'=>'Ohio',
    'OK'=>'Oklahoma',
    'OR'=>'Oregon',
    'PA'=>'Pennsylvania',
    'RI'=>'Rhode Island',
    'SC'=>'South Carolina',
    'SD'=>'South Dakota',
    'TN'=>'Tennessee',
    'TX'=>'Texas',
    'UT'=>'Utah',
    'VT'=>'Vermont',
    'VA'=>'Virginia',
    'WA'=>'Washington',
    'WV'=>'West Virginia',
    'WI'=>'Wisconsin',
    'WY'=>'Wyoming',
	);
}

function input_meta($id, $info, $value) {
	$return = '<p><label class="post-attributes-label">' . labelify($id) . '</label></p>';
	$return .= '<input type="' . $info['type'] .'" name="' . $id . '" class="widefat" maxlength="' . $info['maxlength'] . '" value="' . $value . '">';
	return $return;
}

function phone_meta($id, $info, $value) {
	$return = '<p><label class="post-attributes-label">' . labelify($id) . '</label></p>';
	$return .= '<input type="' . $info['type'] .'" name="' . $id . '" class="widefat" maxlength="3" value="' . $value . '">';
	return $return;
}

function select_meta($id, $info, $value) {
	$choices = $info['choices'];
	$return = '<p><label class="post-attributes-label">' . labelify($id) . '</label></p>';	
	// $return .= $value;
	$return .= '<select name="' . $id . '" class="widefat">';
	$return .= '<option></option>';
	
	foreach($choices as $c_id => $c_val) {
		if($value == $c_id) {
			$return .= '<option value="' . $c_id . '" selected>' . $c_val .'</option>';
		} else {
			$return .= '<option value="' . $c_id . '">' . $c_val .'</option>';
		}
	}
	
	$return .= '</select>';
	return $return;
}

function save_custom_meta_fields() {
	$post_id = get_the_ID();
	$custom_meta_field =  meta_fields(null);	
	foreach($custom_meta_field as $id => $info) {
		if(array_key_exists($id, $_POST)) {
        	update_post_meta($post_id, $id, $_POST[$id]);
    	}
		
		if($id == 'category') {
			$term = get_term($_POST[$id], 'category');
			if($_POST[$id]) {
				wp_set_object_terms($post_id, $term->name, 'category', false );
			}			
		}
	}
}
add_action('save_post', 'save_custom_meta_fields');

function biz_row_two($id) {
	$biz = get_post($id);
	
	echo '<h3>' . $biz->post_title . '</h3>';
	
	$url = get_post_meta($id, 'url', true);
	$ahref = "";
	if($url) {
		$ahref = '<a target="_blank" href="' . $url . '"><i class="fas fa-external-link-alt mr-2"></i>Website</a>';
	}
	
	$phone = get_post_meta($id, 'phone', true);
	$contact = "";
	if($phone) {
		$para = array("(", ")");
		$phone_link = '1' . str_replace($para, "-", $phone);
		$contact .= '<a href="tel:' . str_replace(" ", "", $phone_link) . '"><i class="fas fa-phone mr-2"></i>' . $phone . '</a>';
	}
	if($ahref != '' && $contact != '') {
		$contact .= '<span class="mx-2">|</span>';
	}
	if($ahref != '') {
		$contact .= $ahref;
	}
		
	echo format_biz_address($id);
		
	echo '<div class="row align-items-center">';
	echo return_checkmark_two(get_post_meta($id, 'takeout', true), 'Takeout / Pick Up');
	echo return_checkmark_two(get_post_meta($id, 'delivery', true), 'Delivery');
	echo return_checkmark_two(get_post_meta($id, 'outdoor_eating', true), 'Outdoor Seating');
	echo '</div>';
	
	
	echo $contact;	
}

function return_checkmark_two($value, $field) {
	if($value == 'T') {
		return '<div class="col-6 py-2"><i class="fas fa-check-circle"></i><span class="ml-3">'. $field . '</span></div>';
		
	}
}

function format_biz_address($id) {
	$biz = get_post($id);
	$addr = get_post_meta($id, 'address', true);
	$addr2 = get_post_meta($id, 'address_two', true);
	$city = get_post_meta($id, 'city', true);
	$state = get_post_meta($id, 'state', true);
	$zip = get_post_meta($id, 'zip', true);
	
	
	if($addr) {		
		$final = $addr;
		if($addr2) {
			$final .= ', ' . $addr2;
		}
		
		$final .= ' ' . $city . ', ' . $state . ' ' . $zip;
		
		$google = urlencode(trim($final));
		return $final;
	}	
}

function hide_admin_menu() {
	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'hide_admin_menu' );


?>
