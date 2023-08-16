<div class="choose-plan-title">
	<h3 class=""><span>Insert <b>Business details</b></span></h3>
	<h5 class="text-center plan-previous">(You Selected <span class="b_selected_package"></span> Package)</h5>
</div>
<div class="row mt-5 business-form">
	<div class="col-md-6 zts_email_address_cont">
		<label for="usr">Email Address<span>*</span></label>
		<input type="email" class="form-control " name="account_email" id="account_email" />
		<span class="zts_email_address_error zts_error"></span>
	</div>
	<div class="col-md-6 zts_password_cont">
		<label for="usr">Password <span>*</span></label>
		<input type="password" class="form-control " name="account_password" id="account_password" />
		<span class="zts_password_error zts_error"></span>
	</div>
	<div class="col-md-12 zts_comp_name_cont">
		<label for="usr">Company Name <span>*</span></label>
		<input type="text" class="form-control zts_comp_name">
		<span class="zts_comp_name_error zts_error"></span>
		<input type="hidden" class="zts_hidden_product_scope">
		<input type="hidden" class="zts_hidden_product_id">
	</div>
	<div class="col-md-6 zts_comp_url_cont">
		<label for="usr">Company URL <span>*</span></label>
		<input type="text" class="form-control zts_comp_url">
		<span class="zts_comp_url_error zts_error"></span>
	</div>
	<div class="col-md-6 zts_business_phone_cont">
		<label for="usr">Phone No <span>*</span></label>
		<input type="text" class="form-control zts_business_phone">
		<span class="zts_business_error zts_error"></span>
	</div>
	<div class="col-md-6 zts_comp_profile_cont">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<label for="upload-profile">Upload Profile Image<span>*</span></label>

		<span class="zts_file_upload_error zts_error"></span>
		<div class="avatar-upload">
			<div class="avatar-edit">
				<input class="zts_profile_file-input" type='file' id="zts_file_upload" accept=".png, .jpg, .jpeg" />
				<label for="zts_file_upload"></label>
			</div>
			<div class="avatar-preview">
				<div id="imagePreview" style="background-image: url(<?php echo get_site_url(); ?>/wp-content/uploads/2023/06/camera-img.png);">
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12 mt-3 zts_comp_gallery_cont">
		<div class="zts_h_gallery">
			<section class="bg-diffrent">
				<div class="file-upload-contain">
					<label class="elementor-field-label">Gallery Images<span class="alrear-images">(Upload Max. 10 Images)</span></label>
					<span class="get_gallery_error zts_error"></span>
					<input name="multiplefileupload[]" id="multiplefileupload" type="file" accept=".png, .jpg, .jpeg" multiple />
				</div>
			</section>
		</div>
	</div>
	<div class="col-md-6 zts_comp_categories_cont">
		<label class="elementor-field-label">Categories<span>*</span></label>
		<select class="zts_categories_select2 js-states form-control" id="zts_only_categories" multiple="multiple" style="width: 100%; margin-top: 500px;">
			<option disabled>Select Category</option>
			<?php
			if (!empty($categories) && !is_wp_error($categories)) {
				foreach ($categories as $category) {
					echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
				}
			}
			?>
		</select>
		<span class="zts_categories_select2_error zts_error"></span>
	</div>
	<div class="col-md-6 zts_comp_location_cont">
		<label class="elementor-field-label">Location Service<span>*</span></label>
		<select class="get_form_location_id js-states form-control" multiple="multiple">
			<option disabled>Select Location</option>
			<?php
			foreach ($taxonomy_array as $group) {
				echo "<optgroup label='" . $group['parent']['name'] . "'>";

				foreach ($group['children'] as $child) {
					echo "<option value='" . $child['id'] . "'>" . $child['name'] . "</option>";
				}

				echo "</optgroup>";
			}
			?>
		</select>
		<span class="get_form_location_error zts_error"></span>
	</div>
</div>