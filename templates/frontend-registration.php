<?php if (! defined('ABSPATH')) exit; ?>
<div class="mx-auto p-4 container">
    <form id="cd-registration-form" method="post" enctype="multipart/form-data" class="bg-white shadow p-6 rounded">
        <?php wp_nonce_field('cd_registration', 'cd_registration_nonce'); ?>
        <h2 class="mb-4 text-2xl"><?php _e('Family Registration', CD_TEXT_DOMAIN); ?></h2>

        <h3 class="mb-2 text-xl"><?php _e('Head of Family Details', CD_TEXT_DOMAIN); ?></h3>
        <div class="mb-4">
            <label class="block"><?php _e('Name', CD_TEXT_DOMAIN); ?><span class="text-red-500">*</span></label>
            <input type="text" name="cd_head_details[name]" required class="p-2 border w-full">
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Mobile Number', CD_TEXT_DOMAIN); ?><span
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
                <option value="high_school"><?php _e('High School', CD_TEXT_DOMAIN); ?></option>
                <option value="bachelor"><?php _e('Bachelor', CD_TEXT_DOMAIN); ?></option>
                <option value="master"><?php _e('Master', CD_TEXT_DOMAIN); ?></option>
                <option value="phd"><?php _e('PhD', CD_TEXT_DOMAIN); ?></option>
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
            <label class="block"><?php _e('Business Name', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[business_name]" class="p-2 border w-full">
            <label class="block"><?php _e('Business Type', CD_TEXT_DOMAIN); ?></label>
            <select name="cd_head_details[business_type]" class="p-2 border w-full">
                <option value=""><?php _e('Select Business Type', CD_TEXT_DOMAIN); ?></option>
                <option value="furniture"><?php _e('Furniture', CD_TEXT_DOMAIN); ?></option>
                <option value="tailor"><?php _e('Tailor', CD_TEXT_DOMAIN); ?></option>
                <!-- Add more business types -->
            </select>
            <label class="block"><?php _e('Business Address', CD_TEXT_DOMAIN); ?></label>
            <textarea name="cd_head_details[business_address]" class="p-2 border w-full"></textarea>
            <label class="block"><?php _e('Business Contact Number', CD_TEXT_DOMAIN); ?></label>
            <input type="text" name="cd_head_details[business_contact]" class="p-2 border w-full">
        </div>
        <div class="mb-4">
            <label class="block"><?php _e('Profile Picture', CD_TEXT_DOMAIN); ?></label>
            <input type="file" name="cd_head_details[profile_picture]" accept="image/*" class="p-2 border w-full">
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
                <p><label class="block"><?php _e('Date of Birth', CD_TEXT_DOMAIN); ?></label>
                    <input type="date" name="cd_family_members[0][dob]" class="p-2 border w-full">
                </p>
                <p><label class="block"><?php _e('Education', CD_TEXT_DOMAIN); ?><span
                            class="text-red-500">*</span></label>
                    <select name="cd_family_members[0][education]" class="p-2 border w-full">
                        <option value=""><?php _e('Select Education', CD_TEXT_DOMAIN); ?></option>
                        <option value="high_school"><?php _e('High School', CD_TEXT_DOMAIN); ?></option>
                        <option value="bachelor"><?php _e('Bachelor', CD_TEXT_DOMAIN); ?></option>
                        <option value="master"><?php _e('Master', CD_TEXT_DOMAIN); ?></option>
                        <option value="phd"><?php _e('PhD', CD_TEXT_DOMAIN); ?></option>
                    </select>
                </p>
                <p><label class="block"><?php _e('Occupation', CD_TEXT_DOMAIN); ?><span
                            class="text-red-500">*</span></label>
                    <input type="text" name="cd_family_members[0][occupation]" class="p-2 border w-full">
                </p>
                <p><label class="block"><?php _e('Relation with Head', CD_TEXT_DOMAIN); ?><span
                            class="text-red-500">*</span></label>
                    <input type="text" name="cd_family_members[0][relation]" class="p-2 border w-full">
                </p>
                <p><label class="block"><?php _e('Photo', CD_TEXT_DOMAIN); ?></label>
                    <input type="file" name="cd_family_members[0][photo]" accept="image/*"
                        class="p-2 border w-full">
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
    });
</script>