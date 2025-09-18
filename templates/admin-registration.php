<?php if (! defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1><?php _e('Add New Family', CD_TEXT_DOMAIN); ?></h1>

    <form id="cd-admin-registration-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('cd_admin_registration', 'cd_admin_registration_nonce'); ?>

        <h2><?php _e('Head of Family Details', CD_TEXT_DOMAIN); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="family_name"><?php _e('Family Name', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td><input type="text" name="cd_head_details[family_name]" id="family_name" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="family_head_name"><?php _e('Family Head Name', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td><input type="text" name="cd_head_details[name]" id="family_head_name" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="contact_number"><?php _e('Contact Number', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td><input type="text" name="cd_head_details[mobile]" id="contact_number" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="email"><?php _e('Email', CD_TEXT_DOMAIN); ?></label></th>
                <td><input type="email" name="cd_head_details[email]" id="email" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="address"><?php _e('Full Address', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td><textarea name="cd_head_details[address]" id="address" required class="large-text" rows="3"></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="city"><?php _e('City', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td><input type="text" name="cd_head_details[city]" id="city" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="education"><?php _e('Education', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td>
                    <select name="cd_head_details[education]" id="education" required>
                        <option value=""><?php _e('Select Education', CD_TEXT_DOMAIN); ?></option>
                        <?php
                        $education_options = get_option('cd_education_options', array('High School', 'Bachelor', 'Master', 'PhD'));
                        foreach ($education_options as $option) {
                            echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label><?php _e('Occupation Type', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                <td>
                    <fieldset>
                        <label><input type="radio" name="cd_head_details[occupation_type]" value="job" required> <?php _e('Job', CD_TEXT_DOMAIN); ?></label><br>
                        <label><input type="radio" name="cd_head_details[occupation_type]" value="business"> <?php _e('Business', CD_TEXT_DOMAIN); ?></label>
                    </fieldset>
                </td>
            </tr>
        </table>

        <div id="cd-job-fields" style="display: none;">
            <h3><?php _e('Job Details', CD_TEXT_DOMAIN); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="job_title"><?php _e('Job Title', CD_TEXT_DOMAIN); ?></label></th>
                    <td><input type="text" name="cd_head_details[job_title]" id="job_title" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="company_name"><?php _e('Company Name', CD_TEXT_DOMAIN); ?></label></th>
                    <td><input type="text" name="cd_head_details[company_name]" id="company_name" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="company_location"><?php _e('Company Location', CD_TEXT_DOMAIN); ?></label></th>
                    <td><input type="text" name="cd_head_details[company_location]" id="company_location" class="regular-text"></td>
                </tr>
            </table>
        </div>

        <div id="cd-business-fields" style="display: none;">
            <h3><?php _e('Business Details', CD_TEXT_DOMAIN); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="business_name"><?php _e('Business Name', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                    <td><input type="text" name="cd_head_details[business_name]" id="business_name" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="business_type"><?php _e('Business Industry', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                    <td>
                        <select name="cd_head_details[business_type]" id="business_type">
                            <option value=""><?php _e('Select Business Industry', CD_TEXT_DOMAIN); ?></option>
                            <?php
                            $business_industry_options = get_option('cd_business_industry_options', array('Furniture', 'Tailor'));
                            foreach ($business_industry_options as $option) {
                                echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="business_city"><?php _e('Business City', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                    <td><input type="text" name="cd_head_details[business_city]" id="business_city" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="business_website"><?php _e('Business Website', CD_TEXT_DOMAIN); ?></label></th>
                    <td><input type="text" name="cd_head_details[business_website]" id="business_website" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="business_brochure"><?php _e('Business Brochure (PDF below 5 MB)', CD_TEXT_DOMAIN); ?></label></th>
                    <td><input type="file" name="cd_head_details[business_brochure]" id="business_brochure" accept=".pdf"></td>
                </tr>
            </table>
        </div>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="profile_picture"><?php _e('Family Photo', CD_TEXT_DOMAIN); ?></label></th>
                <td>
                    <input type="file" name="cd_head_details[profile_picture]" id="profile_picture" accept="image/*">
                    <div id="photo-preview-container" style="display: none; margin-top: 10px;">
                        <img id="photo-preview" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd;">
                        <br>
                        <button type="button" id="edit-photo" class="button" style="margin-top: 5px;"><?php _e('Edit Photo', CD_TEXT_DOMAIN); ?></button>
                        <button type="button" id="clear-photo" class="button" style="margin-top: 5px;"><?php _e('Clear Photo', CD_TEXT_DOMAIN); ?></button>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="mosal_category"><?php _e('Add Mosal Category', CD_TEXT_DOMAIN); ?></label></th>
                <td><input type="text" name="cd_head_details[mosal_category]" id="mosal_category" class="regular-text"></td>
            </tr>
        </table>

        <h2><?php _e('Family Members', CD_TEXT_DOMAIN); ?></h2>
        <div id="cd-family-members">
            <div class="family-member" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php _e('Name', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                        <td><input type="text" name="cd_family_members[0][name]" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Gender', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                        <td>
                            <select name="cd_family_members[0][gender]" required>
                                <option value=""><?php _e('Select Gender', CD_TEXT_DOMAIN); ?></option>
                                <option value="male"><?php _e('Male', CD_TEXT_DOMAIN); ?></option>
                                <option value="female"><?php _e('Female', CD_TEXT_DOMAIN); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Relation with Head', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                        <td>
                            <select name="cd_family_members[0][relation]" required>
                                <option value=""><?php _e('Select Relation', CD_TEXT_DOMAIN); ?></option>
                                <?php
                                $relation_options = get_option('cd_relation_options', array('Mother', 'Father', 'Son', 'Daughter', 'Wife', 'Husband', 'Brother', 'Sister', 'Grandmother', 'Grandfather', 'Uncle', 'Aunt', 'Nephew', 'Niece'));
                                foreach ($relation_options as $option) {
                                    echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <p>
            <button type="button" id="cd-add-member" class="button"><?php _e('Add Member', CD_TEXT_DOMAIN); ?></button>
            <input type="submit" class="button button-primary" value="<?php _e('Submit', CD_TEXT_DOMAIN); ?>">
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Form validation
    $('#cd-admin-registration-form').validate({
        rules: {
            'cd_head_details[family_name]': { required: true },
            'cd_head_details[name]': { required: true },
            'cd_head_details[mobile]': { required: true, digits: true, minlength: 10, maxlength: 10 },
            'cd_head_details[email]': { email: true },
            'cd_head_details[address]': { required: true },
            'cd_head_details[city]': { required: true },
            'cd_head_details[education]': { required: true },
            'cd_head_details[occupation_type]': { required: true },
            'cd_head_details[job_title]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'job'; } },
            'cd_head_details[company_name]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'job'; } },
            'cd_head_details[company_location]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'job'; } },
            'cd_head_details[business_name]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
            'cd_head_details[business_type]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
            'cd_head_details[business_city]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
            'cd_family_members[0][name]': { required: true },
            'cd_family_members[0][gender]': { required: true },
            'cd_family_members[0][relation]': { required: true },
        },
        messages: {
            'cd_head_details[family_name]': 'Please enter a family name',
            'cd_head_details[name]': 'Please enter a name',
            'cd_head_details[mobile]': {
                required: 'Please enter a mobile number',
                digits: 'Please enter only digits',
                minlength: 'Mobile number must be 10 digits',
                maxlength: 'Mobile number must be 10 digits'
            },
            'cd_head_details[email]': 'Please enter a valid email',
            'cd_head_details[address]': 'Please enter an address',
            'cd_head_details[city]': 'Please enter a city',
            'cd_head_details[education]': 'Please select education',
            'cd_head_details[occupation_type]': 'Please select occupation type',
            'cd_head_details[job_title]': 'Please enter job title',
            'cd_head_details[company_name]': 'Please enter company name',
            'cd_head_details[company_location]': 'Please enter company location',
            'cd_head_details[business_name]': 'Please enter business name',
            'cd_head_details[business_type]': 'Please select business type',
            'cd_head_details[business_city]': 'Please enter business city',
            'cd_family_members[0][name]': 'Please enter name',
            'cd_family_members[0][gender]': 'Please select gender',
            'cd_family_members[0][relation]': 'Please enter relation',
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "cd_head_details[occupation_type]") {
                error.insertAfter(element.closest('fieldset'));
            } else {
                error.insertAfter(element);
            }
        }
    });

    // Toggle Job/Business fields
    $('input[name="cd_head_details[occupation_type]"]').change(function() {
        if ($(this).val() === 'job') {
            $('#cd-job-fields').show();
            $('#cd-business-fields').hide();
            $('#business_name, #business_type, #business_city').removeAttr('required');
            $('#job_title, #company_name, #company_location').attr('required', 'required');
        } else {
            $('#cd-job-fields').hide();
            $('#cd-business-fields').show();
            $('#business_name, #business_type, #business_city').attr('required', 'required');
            $('#job_title, #company_name, #company_location').removeAttr('required');
        }
    });

    // Family photo preview and edit controls
    const fileInput = $('#profile_picture');
    const previewContainer = $('#photo-preview-container');
    const previewImage = $('#photo-preview');
    const editButton = $('#edit-photo');
    const clearButton = $('#clear-photo');

    fileInput.on('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.attr('src', e.target.result);
                previewContainer.show();
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.hide();
            previewImage.attr('src', '');
        }
    });

    editButton.on('click', function () {
        fileInput.click();
    });

    clearButton.on('click', function () {
        fileInput.val('');
        previewContainer.hide();
        previewImage.attr('src', '');
    });

    // Add family member functionality
    let memberCount = 1;
    $('#cd-add-member').click(function() {
        const memberHtml = `
            <div class="family-member" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                <h4><?php _e('Family Member', CD_TEXT_DOMAIN); ?> ${memberCount + 1}</h4>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php _e('Name', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                        <td><input type="text" name="cd_family_members[${memberCount}][name]" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Gender', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                        <td>
                            <select name="cd_family_members[${memberCount}][gender]" required>
                                <option value=""><?php _e('Select Gender', CD_TEXT_DOMAIN); ?></option>
                                <option value="male"><?php _e('Male', CD_TEXT_DOMAIN); ?></option>
                                <option value="female"><?php _e('Female', CD_TEXT_DOMAIN); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Relation with Head', CD_TEXT_DOMAIN); ?><span style="color: red;">*</span></label></th>
                        <td>
                            <select name="cd_family_members[${memberCount}][relation]" required>
                                <option value=""><?php _e('Select Relation', CD_TEXT_DOMAIN); ?></option>
                                <?php
                                $relation_options = get_option('cd_relation_options', array('Mother', 'Father', 'Son', 'Daughter', 'Wife', 'Husband', 'Brother', 'Sister', 'Grandmother', 'Grandfather', 'Uncle', 'Aunt', 'Nephew', 'Niece'));
                                foreach ($relation_options as $option) {
                                    echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        `;
        $('#cd-family-members').append(memberHtml);
        memberCount++;
    });
});
</script>
