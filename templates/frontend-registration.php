<?php if (! defined('ABSPATH')) exit; ?>
<div class="mx-auto p-4 container">
    <form id="cd-registration-form" method="post" enctype="multipart/form-data" class="bg-white shadow p-6 rounded">
        <?php
        if (is_admin()) {
            wp_nonce_field('cd_admin_registration', 'cd_admin_registration_nonce');
        } else {
            wp_nonce_field('cd_registration', 'cd_registration_nonce');
        }
        ?>
        <h2 class="mb-4 text-2xl"><?php _e('Family Registration', CD_TEXT_DOMAIN); ?></h2>

        <h3 class="mb-2 text-xl"><?php _e('Head of Family Details', CD_TEXT_DOMAIN); ?></h3>
        <div class="mb-4">
            <label class="block"><?php _e('Family Name', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[family_name]" required class="p-2 border w-full">
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Family Head Name', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[name]" required class="p-2 border w-full">
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Contact Number', CD_TEXT_DOMAIN); ?><span
                    class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[mobile]" required class="p-2 border w-full">
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Email', CD_TEXT_DOMAIN); ?></label>
            <input type="email" name="cd_head_details[email]" class="p-2 border w-full">
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Full Address', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <textarea name="cd_head_details[address]" required class="p-2 border w-full"></textarea>
        </div>

        <div class="mb-4">
            <label class="block"><?php _e('City', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[city]" required class="p-2 border w-full">
        </div>

        <div class="mb-4">
            <label class="block"><?php _e('Education', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <select name="cd_head_details[education]" required class="p-2 border w-full">
                <option value=""><?php _e('Select Education', CD_TEXT_DOMAIN); ?></option>
                <?php
                $education_options = get_option('cd_education_options', array('High School', 'Bachelor', 'Master', 'PhD'));
                foreach ($education_options as $option) {
                    echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Occupation Type', CD_TEXT_DOMAIN); ?><span
                    class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label><input type="radio" name="cd_head_details[occupation_type]" value="job" required>
                    <?php _e('Job', CD_TEXT_DOMAIN); ?></label>
                <label><input type="radio" name="cd_head_details[occupation_type]" value="business">
                    <?php _e('Business', CD_TEXT_DOMAIN); ?></label>
            </div>
        </div>
        <div id="cd-job-fields" class="hidden mb-4">
            <label class="block"><?php _e('Job Title', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[job_title]" class="p-2 border w-full">
            <label class="block"><?php _e('Company Name', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[company_name]" class="p-2 border w-full">
            <label class="block"><?php _e('Company Location', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[company_location]" class="p-2 border w-full">
        </div>
        <div id="cd-business-fields" class="hidden mb-4">
            <label class="block"><?php _e('Business Name', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[business_name]" required class="p-2 border w-full">
            <label class="block"><?php _e('Business Industry', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <select name="cd_head_details[business_type]" required class="p-2 border w-full">
                <option value=""><?php _e('Select Business Industry', CD_TEXT_DOMAIN); ?></option>
                <?php
                $business_industry_options = get_option('cd_business_industry_options', array('Furniture', 'Tailor'));
                foreach ($business_industry_options as $option) {
                    echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                }
                ?>
            </select>
            <label class="block"><?php _e('Business City', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[business_city]" required class="p-2 border w-full">
            <label class="block"><?php _e('Business Website', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[business_website]" class="p-2 border border-gray-300 w-full" id="business-website">
            <label class="block"><?php _e('Business Brochure (PDF below 5 MB)', CD_TEXT_DOMAIN); ?></label>
            <input type="file" name="cd_head_details[business_brochure]" accept=".pdf" class="p-2 border w-full" id="business-brochure-input">
            <div id="brochure-preview-container" class="mt-2" style="display: none;">
                <div class="flex items-center gap-2 p-2 bg-gray-50 border rounded">
                    <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900" id="brochure-filename"></p>
                        <p class="text-xs text-gray-500" id="brochure-size"></p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="change-brochure" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600"><?php _e('Change', CD_TEXT_DOMAIN); ?></button>
                        <button type="button" id="remove-brochure" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>
                    </div>
                </div>
            </div>
        </div>
            <div class="mb-4">
                <label class="block"><?php _e('Family Photo', CD_TEXT_DOMAIN); ?></label>
                <input type="file" name="cd_head_details[profile_picture]" accept="image/*" class="p-2 border w-full" id="profile-picture-input">
                <div id="photo-preview-container" class="mt-2" style="display: none;">
                    <img id="photo-preview" src="" alt="Preview" class="w-24 h-24 rounded-full object-cover">
                    <div class="mt-2 flex gap-2">
                        <button type="button" id="edit-photo" class="bg-yellow-500 text-white p-2 rounded"><?php _e('Edit', CD_TEXT_DOMAIN); ?></button>
                        <button type="button" id="remove-photo" class="bg-red-500 text-white p-2 rounded"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>
                    </div>
                </div>
            </div>

        <div class="mb-4">
            <label class="block"><?php _e('Add Mosal Category', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[mosal_category]" class="p-2 border w-full">
        </div>

        <h3 class="mb-2 text-xl"><?php _e('Family Members', CD_TEXT_DOMAIN); ?></h3>
        <div id="cd-family-members">
            <div class="mb-4 p-4 border rounded family-member">
                <p><label class="block"><?php _e('Name', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
                    <input type="text" name="cd_family_members[0][name]" class="p-2 border w-full">
                </p>
                <p><label class="block"><?php _e('Gender', CD_TEXT_DOMAIN); ?><span
                            class="text-red-500">*</span></label>
                    <select name="cd_family_members[0][gender]" class="p-2 border w-full">
                        <option value=""><?php _e('Select Gender', CD_TEXT_DOMAIN); ?></option>
                        <option value="male"><?php _e('Male', CD_TEXT_DOMAIN); ?></option>
                        <option value="female"><?php _e('Female', CD_TEXT_DOMAIN); ?></option>
                    </select>
                </p>
                <p><label class="block"><?php _e('Relation with Head', CD_TEXT_DOMAIN); ?><span
                            class="text-red-500">*</span></label>
                    <input type="text" name="cd_family_members[0][relation]" class="p-2 border w-full">
                </p>
            </div>
        </div>
        <button type="button" id="cd-add-member"
            class="bg-blue-500 mt-4 p-2 rounded text-white"><?php _e('Add Member', CD_TEXT_DOMAIN); ?></button>
        <button type="submit"
            class="bg-green-500 mt-4 p-2 rounded text-white"><?php _e('Submit', CD_TEXT_DOMAIN); ?></button>
    </form>
</div>
<script>
    jQuery(document).ready(function($) {
        // Toggle Job/Business fields
        $('input[name="cd_head_details[occupation_type]"]').change(function() {
            if ($(this).val() === 'job') {
                $('#cd-job-fields').removeClass('hidden');
                $('#cd-business-fields').addClass('hidden');
            } else {
                $('#cd-job-fields').addClass('hidden');
                $('#cd-business-fields').removeClass('hidden');
            }
        });

        // Show preview and buttons when a photo is selected
        $('#profile-picture-input').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo-preview').attr('src', e.target.result);
                    $('#photo-preview-container').show();
                }
                reader.readAsDataURL(file);
            } else {
                $('#photo-preview-container').hide();
            }
        });

        // Remove photo button functionality
        $('#remove-photo').on('click', function() {
            $('#profile-picture-input').val('');
            $('#photo-preview').attr('src', '');
            $('#photo-preview-container').hide();
        });

        // Business website validation
        $('#business-website').on('input', function() {
            const value = $(this).val().trim();
            // Simple and very permissive regex for domain validation
            const domainRegex = /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            console.log('Business Website Validation:');
            console.log('Input value:', '"' + value + '"');
            console.log('Trimmed length:', value.length);
            console.log('Regex test result:', domainRegex.test(value));
            console.log('Regex pattern:', domainRegex.toString());

            if (value && !domainRegex.test(value)) {
                console.log('❌ Validation FAILED - adding red border');
                $(this).addClass('border-red-500').removeClass('border-gray-300');
                console.log('Current classes after FAILED:', $(this).attr('class'));
            } else {
                console.log('✅ Validation PASSED - removing red border');
                $(this).removeClass('border-red-500').addClass('border-gray-300');
                console.log('Current classes after PASSED:', $(this).attr('class'));
            }
        });

        // Form submission validation
        $('#cd-registration-form').on('submit', function(e) {
            const websiteField = $('#business-website');
            const value = websiteField.val().trim();
            // Simple and very permissive regex for domain validation
            const domainRegex = /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            console.log('Form Submission - Business Website Validation:');
            console.log('Input value:', '"' + value + '"');
            console.log('Regex test result:', domainRegex.test(value));

            if (value && !domainRegex.test(value)) {
                console.log('❌ Form submission blocked due to invalid domain');
                e.preventDefault();
                websiteField.focus();
                return false;
            } else {
                console.log('✅ Form submission allowed');
            }
        });
    });
</script>
