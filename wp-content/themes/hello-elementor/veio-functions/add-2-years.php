<?php

function generate_premium_listings_form_2() {
	ob_start();
	?>
	<form method="post" class="premium_form" enctype="multipart/form-data">
	<input type="text" name="years" id="years" style="display:none;" value="2">
	  <label for="title">Company Name:</label>
	  <input type="text" name="title" id="title" required>
	  
	  <label for="company_url">Company URL:</label>
	  <input type="url" name="company_url" id="company_url" required>
	  
	  <label for="company_phone">Company Phone:</label>
	  <input type="tel" name="company_phone" id="company_phone" required>
	  <label for="province">Province:</label>
<select name="province" id="province">

  <option value="">-- Choose province --</option>
  <option value="Ontario">Ontario</option>
  <option value="Alberta">Alberta</option>
</select>

<label for="city[]">City:</label>
<select name="city[]" id="city" multiple>
  <option value="Guelph">Guelph</option>
  <option value="Kitchener">Kitchener</option>
  <option value="Cambridge">Cambridge</option>
  <option value="Calgary">Calgary</option>
  <option value="Edmonton">Edmonton</option>
</select>


	  
	  <label for="profile_image">Profile Image:</label><br/>
	  <input type="file" name="profile_image" id="profile_image" accept="image/*" required><br/>


	  <label for="gallery">Gallery:</label><br/>
	  <input type="file" name="gallery[]" id="gallery" multiple accept="image/*"><br/>

	  <label for="categories">Categories:</label>
		<select name="categories" id="categories">
		<option value="Maternity">Maternity</option>
		<option value="Birth">Birth</option>
		<option value="Fresh 48">Fresh 48</option>
		<option value="Newborn">Newborn</option>
		<option value="Sitter">Sitter</option>
		<option value="1 year cake smash">1 year cake smash</option>
		<option value="Birthday">Birthday</option>
		<option value="Family">Family</option>
		<option value="Holiday">Holiday</option>
		</select>


		<script>
		jQuery(document).ready(function($) {
		// Definește opțiunile pentru fiecare provincie
		var provinceOptions = {
			'Ontario': ['Guelph', 'Kitchener', 'Cambridge'],
			'Alberta': ['Calgary', 'Edmonton']
		};

		// Actualizează opțiunile pentru câmpul "city" la fiecare schimbare a câmpului "province"
		$('#province').change(function() {
			var province = $(this).val();
			var cities = provinceOptions[province] || [];
			var options = '';
			cities.forEach(function(city) {
			options += '<option value="' + city + '">' + city + '</option>';
			});
			$('#city').html(options);
		});

		// Limitează numărul de orașe selectate la 3
		$('#city').on('change', function() {
			var selectedCities = $('#city option:selected');
			if (selectedCities.length > 3) {
			selectedCities.prop('selected', false);
			var firstThree = selectedCities.slice(0, 3);
			firstThree.prop('selected', true);
			}
		});
		});
		</script>


	  <?php wp_nonce_field( 'premium_listings_form', 'premium_listings_form_nonce' ); ?>
	  <br/>
	  <input type="submit" value="Submit">
	</form>
	<?php
	return ob_get_clean();
  }
  function process_premium_listings_form_2() {
	// Verifică dacă formularul a fost trimis și dacă nonce-ul este valid
	if (isset($_POST['premium_listings_form_nonce']) && wp_verify_nonce($_POST['premium_listings_form_nonce'], 'premium_listings_form')) {
	  // Verifică dacă toate câmpurile obligatorii au fost completate
	  if (!empty($_POST['title']) && !empty($_POST['company_url']) && !empty($_POST['company_phone']) && !empty($_FILES['profile_image'])) {
		// Salvează datele din formular în variabile
		$title = sanitize_text_field($_POST['title']);
		$company_url = esc_url($_POST['company_url']);
		$company_phone = sanitize_text_field($_POST['company_phone']);
		$profile_image = $_FILES['profile_image'];
		$cities = isset($_POST['city']) ? $_POST['city'] : array();
    	$num_cities_selected = count($cities);
		// Creează postul în post type-ul "premium_listings"
		$post_id = wp_insert_post(array(
		  'post_title' => $title,
		  'post_type' => 'premium_listings',
		  'post_status' => 'draft'
		));
		
		// Adaugă câmpurile personalizate ACF la postul nou creat
		update_field('company_url', $company_url, $post_id);
		update_field('company_phone', $company_phone, $post_id);
		update_field('categories', sanitize_text_field($_POST['categories']), $post_id);
		update_field('province', sanitize_text_field($_POST['province']), $post_id);
		update_field('city', $cities, $post_id);


		
		// Încarcă imaginea de profil și o adaugă ca featured image pentru postul nou creat
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents($profile_image['tmp_name']);
		$filename = basename($profile_image['name']);
		if (wp_mkdir_p($upload_dir['path'])) {
		  $file = $upload_dir['path'] . '/' . $filename;
		} else {
		  $file = $upload_dir['basedir'] . '/' . $filename;
		}
		file_put_contents($file, $image_data);
		$wp_filetype = wp_check_filetype($filename, null);
		$attachment = array(
		  'post_mime_type' => $wp_filetype['type'],
		  'post_title' => sanitize_file_name($filename),
		  'post_content' => '',
		  'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment($attachment, $file, $post_id);
		set_post_thumbnail($post_id, $attach_id);
		
		// Salvează ID-urile imaginilor din galerie într-un array
		$gallery_images = array();
		if (!empty($_FILES['gallery'])) {
		  $gallery_files = $_FILES['gallery'];
		  foreach ($gallery_files['name'] as $key => $value) {
			if ($gallery_files['name'][$key]) {
			  $file = array(
				'name' => $gallery_files['name'][$key],
				'type' => $gallery_files['type'][$key],
				'tmp_name' => $gallery_files['tmp_name'][$key],
				'error' => $gallery_files['error'][$key],
				'size' => $gallery_files['size'][$key]
			  );
			  $uploaded_image = wp_handle_upload($file, array('test_form' => false));
			  if ($uploaded_image && !isset($uploaded_image['error'])) {
				$attachment = array(
				  'post_mime_type' => $uploaded_image['type'],
				  'post_title' => sanitize_file_name($uploaded_image['file']),
				  'post_content' => '',
				  'post_status' => 'inherit'
				);
				$attach_id = wp_insert_attachment($attachment, $uploaded_image['file'], $post_id);
				$gallery_images[] = $attach_id;
			  }
			}
		  }
		}
		
		// Adaugă ID-urile imaginilor din galerie în câmpul personalizat ACF "gallery"
		update_field('gallery', implode(',', $gallery_images), $post_id);
		
		// Redirect către pagina de succes
		wp_redirect(home_url('/success'));
		exit;
	  }
	}
}	  
				  



add_action('init', 'process_premium_listings_form_2');

function premium_listings_shortcode_2() {
	$form = generate_premium_listings_form_2();
	return $form;
  }
  add_shortcode('premium_listings_2', 'premium_listings_shortcode_2');