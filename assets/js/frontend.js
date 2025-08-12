jQuery(document).ready(function ($) {
    // Form validation
    $('#cd-registration-form').validate({
        rules: {
            'cd_head_details[name]': { required: true },
            'cd_head_details[mobile]': { required: true },
            'cd_head_details[address]': { required: true },
            'cd_head_details[education]': { required: true },
            'cd_head_details[occupation_type]': { required: true },
            'cd_head_details[job_title]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'job'; } },
            'cd_head_details[company_name]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'job'; } },
            'cd_head_details[company_location]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'job'; } },
            'cd_head_details[business_name]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
            'cd_head_details[business_type]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
            'cd_head_details[business_address]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
            'cd_head_details[business_contact]': { required: function () { return $('input[name="cd_head_details[occupation_type]"]:checked').val() === 'business'; } },
        },
        messages: {
            'cd_head_details[name]': 'Please enter a name',
            // Add other messages as needed, using static strings instead of PHP _e
        }
    });

    // Toggle Job/Business fields
    $('input[name="cd_head_details[occupation_type]"]').change(function () {
        if ($(this).val() === 'job') {
            $('#cd-job-fields').removeClass('hidden');
            $('#cd-business-fields').addClass('hidden');
        } else {
            $('#cd-job-fields').addClass('hidden');
            $('#cd-business-fields').removeClass('hidden');
        }
    });

    // Dynamic family member fields
    let memberCount = 1;
    $('#cd-add-member').click(function () {
        $('#cd-family-members').append(`
            <div class="family-member mb-4 border p-4 rounded">
                <p><label class="block">Name<span class="text-red-500">*</span></label>
                <input type="text" name="cd_family_members[${memberCount}][name]" required class="border p-2 w-full"></p>
                <p><label class="block">Gender<span class="text-red-500">*</span></label>
                <select name="cd_family_members[${memberCount}][gender]" required class="border p-2 w-full">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select></p>
                <p><label class="block">Date of Birth</label>
                <input type="date" name="cd_family_members[${memberCount}][dob]" class="border p-2 w-full"></p>
                <p><label class="block">Education<span class="text-red-500">*</span></label>
                <select name="cd_family_members[${memberCount}][education]" required class="border p-2 w-full">
                    <option value="">Select Education</option>
                    <option value="high_school">High School</option>
                    <option value="bachelor">Bachelor</option>
                    <option value="master">Master</option>
                    <option value="phd">PhD</option>
                </select></p>
                <p><label class="block">Occupation<span class="text-red-500">*</span></label>
                <input type="text" name="cd_family_members[${memberCount}][occupation]" required class="border p-2 w-full"></p>
                <p><label class="block">Relation with Head<span class="text-red-500">*</span></label>
                <input type="text" name="cd_family_members[${memberCount}][relation]" required class="border p-2 w-full"></p>
                <p><label class="block">Photo</label>
                <input type="file" name="cd_family_members[${memberCount}][photo]" accept="image/*" class="border p-2 w-full"></p>
                <button type="button" class="remove-member bg-red-500 text-white p-2 rounded">Remove Member</button>
            </div>
        `);
        memberCount++;
    });

    $(document).on('click', '.remove-member', function () {
        $(this).closest('.family-member').remove();
    });

    // AJAX search and filter
    $('#cd-search, #cd-city-filter, #cd-education-filter, #cd-occupation-filter').on('input change', function () {
        $.ajax({
            url: cd_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cd_search_filter',
                nonce: cd_ajax.nonce,
                search: $('#cd-search').val(),
                city: $('#cd-city-filter').val(),
                education: $('#cd-education-filter').val(),
                occupation: $('#cd-occupation-filter').val(),
            },
            success: function (response) {
                $('#cd-family-list').html(response.data);
            }
        });
    });
});