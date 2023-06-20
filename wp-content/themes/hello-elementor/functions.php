<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.7.1' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
*/

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
*/
function hello_register_customizer_functions() {
	if ( is_customize_preview() ) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_register_customizer_functions' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}




/** Custom Code
 * Veio.ro
 */
		  

 function photographers_veio() {

	register_post_type(
	  'free-listings', array(
		'labels' => array('name' => __( 'Free Listings' ), 'singular_name' => __( 'Free Listing' ) ),
		'public' => true,
		'menu_icon'           => 'dashicons-camera',
		'has_archive' => true,
		'menu_position' => 4,
		'taxonomies'          => array( 'category' ),
		'supports' => array('title', 'editor', 'thumbnail')
	  )
	);

	register_post_type(
	  'premium_listings', array(
		'labels' => array('name' => __( 'Premium Listings' ), 'singular_name' => __( 'Premium Listing' ) ),
		'public' => true,
		'menu_icon'           => 'dashicons-camera',
		'menu_position' => 4,
		'has_archive' => true,
		'taxonomies'          => array( 'category' ),
		'supports' => array('title', 'editor', 'thumbnail',)
	  )
	);

  }
  add_action( 'init', 'photographers_veio' );




?>

<?php 







add_shortcode( 'veio_add_business', 'veio_add_business' );
function veio_add_business() {
veio_add_business_function(); ?>

<form id="new_post" class="veio_form_add" name="new_post" method="post"  enctype="multipart/form-data">


	<span class="form_title">Profile photo</span>
	 <p> <input type="file" name="post_image" id="post_image" aria-required="true"></p>
	<span class="form_title">Company Name</span>
	<p> <input type="text" id="title" name="title" /></p>
	<span class="form_title">Company website</span>
	<p> <input type="text" id="title" name="company_url" /></p>
	<span class="form_title">Phone</span>
	<p> <input type="text" id="title" name="company_phone" /></p>
	<span class="form_title">Category</span>
	<p> <select name="categories" multiple>
			<option value="Maternity">Maternity</option>
			<option value="Birth">Birth</option>
		</select></p>
	<span class="form_title">Province</span>
	<p> 
		<select name="province" multiple>
			<option value="Alberta">Alberta</option>
			<option value="Ontario">Ontario</option>
		</select>
	</p>
	<span class="form_title">City</span>
	<p> 
		<select name="city" multiple>
			<option value="Calgary">Calgary</option>
			<option value="Edmonton">Edmonton</option>


			<option value="Guelph">Guelph</option>
			<option value="Kitchener">Kitchener</option>
			<option value="Cambridge">Cambridge</option>
		</select>	
	</p>
	
	
	<span class="form_title">Gallery</span>
	 
		<table>
			<tr>
				<th>
					<input type="file" name="gallery" id="media_image_2" aria-required="true" >
				</th>
				<th>
					<input type="file" name="gallery2" id="media_image_2" aria-required="true" >
				</th>
				<th>
					<input type="file" name="gallery3" id="media_image_2" aria-required="true" >
				</th>
				
			</tr>
			<tr>
				<th>
					<input type="file" name="gallery4" id="media_image_2" aria-required="true" >
				</th>
				<th>
					<input type="file" name="gallery5" id="media_image_2" aria-required="true" >
				</th>
				<th>
					<input type="file" name="gallery6" id="media_image_2" aria-required="true" >
				</th>
				
			</tr>
			<tr>
				<th>
					<input type="file" name="gallery7" id="media_image_2" aria-required="true" >
				</th>
				<th>
					<input type="file" name="gallery8" id="media_image_2" aria-required="true" >
				</th>
				<th>
					<input type="file" name="gallery9" id="media_image_2" aria-required="true" >
				</th>
				
			</tr>
		</table>
	



	  <br/><br/>
	<p><input type="submit" value="Publish" tabindex="6" id="submit" class="veio_button" name="submit" /></p>

</form>

<?php
} ?>
<?php
/**
* Save for the form data
*
* @return string
*/
function veio_add_business_function() {
// Stop running function if form wasn't submitted
if ( !isset($_POST['title']) ) {
return;
}

// echo "<pre>";
// var_dump($_FILES);
// die();

// Add the content of the form to $post as an array
$post = array(
'post_title'    => $_POST['title'],
'meta_input' => array(
	'company_url' => $_POST['company_url'],
	'company_phone' => $_POST['company_phone'],
	'categories' => $_POST['categories'],
	'province' => $_POST['province'],
	'city' => in_array($_POST['city'])
),
'post_category' => array($_POST['cat']), 
'tags_input'    => $_POST['post_tags'],
'post_status'   => 'draft',   // Could be: publish
'post_type' 	=> 'premium_listings' // Could be: 'page' or your CPT
);
$post_id = wp_insert_post($post);

// For Featured Image
if( !function_exists('wp_generate_attachment_metadata')){
require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');
}
if($_FILES) {
$errorsImg = [];
foreach( $_FILES as $file => $array ) {
	if($_FILES[$file]['error'] !== UPLOAD_ERR_OK){
		$errors[] =  "upload error : " . $_FILES[$file]['error'];
		continue;
	}
	$attach_id = media_handle_upload( $file, $post_id );
	if($file == 'post_image'){
		if($attach_id > 0) {
			update_post_meta( $post_id,'_thumbnail_id', $attach_id );
		}
	} else {
		if($attach_id > 0) {
			update_field( $file , $attach_id, $post_id);
		}
	}
}
}
$homeurl = esc_url( home_url( '/' ) );
foreach($errorsImg as $errorImg){
echo $errorImg.' <br/>';
}
echo "<div class=\"veio_alert\">Thank you for creating a profile, please allow us time to approve the listing.";
} 





// Register shortcode for gallery display
add_shortcode( 'premium_listings_gallery', 'premium_listings_gallery_shortcode' );
function premium_listings_gallery_shortcode() {
ob_start();
premium_listings_gallery();
return ob_get_clean();
}

// Function to generate the gallery
function premium_listings_gallery() {
$gallery = get_field( 'gallery' );
if ( $gallery ) {
?>
<div class="gallery">
	<?php
	foreach ( $gallery as $attachment_id ) {
		$image = wp_get_attachment_image( $attachment_id, 'thumbnail' );
		$image_full = wp_get_attachment_image_url( $attachment_id, 'full' );
		?>
		<a href="<?php echo esc_url( $image_full ); ?>" class="gallery-item" data-lightbox="gallery">
			<?php echo $image; ?>
		</a>
		<?php
	}
	?>
</div>
<?php
}
}














///////////////////





function generate_premium_listings_form() {
ob_start();
?>
<form method="post" class="premium_form" enctype="multipart/form-data">
<input type="text" name="years" id="years" style="display:none;" value="1">
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
<label for="gallery">Galerie:</label><br/>
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
function process_premium_listings_form() {
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
		  



add_action('init', 'process_premium_listings_form');

function premium_listings_shortcode() {
$form = generate_premium_listings_form();
return $form;
}
add_shortcode('premium_listings', 'premium_listings_shortcode');

function custom_remove_all_quantity_fields( $return, $product ) {return true;}
add_filter( 'woocommerce_is_sold_individually','custom_remove_all_quantity_fields', 10, 2 );


