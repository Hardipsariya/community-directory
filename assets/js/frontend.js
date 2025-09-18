jQuery(document).ready(function ($) {
    // Form validation
    $('#cd-registration-form').validate({
        rules: {
            'cd_head_details[name]': { required: true },
            'cd_head_details[mobile]': { required: true, digits: true, minlength: 10, maxlength: 10 },
            'cd_head_details[email]': { email: true },
            'cd_head_details[address]': { required: true },
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
            'cd_head_details[name]': 'Please enter a name',
            // Add other messages as needed, using static strings instead of PHP _e
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "cd_head_details[occupation_type]") {
                error.insertAfter(element.closest('.flex'));
            } else {
                error.insertAfter(element);
            }
        }
    });

    // Toggle Job/Business fields
    $('input[name="cd_head_details[occupation_type]"]').change(function () {
        if ($(this).val() === 'job') {
            $('#cd-job-fields').removeClass('hidden');
            $('#cd-business-fields').addClass('hidden');
            $('#business_name, #business_type, #business_city').removeAttr('required');
        } else {
            $('#cd-job-fields').addClass('hidden');
            $('#cd-business-fields').removeClass('hidden');
            $('#business_name, #business_type, #business_city').attr('required', 'required');
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
                <p><label class="block">Relation with Head<span class="text-red-500">*</span></label>
                <select name="cd_family_members[${memberCount}][relation]" required class="border p-2 w-full">
                    <option value="">Select Relation</option>
                    <option value="Mother">Mother</option>
                    <option value="Father">Father</option>
                    <option value="Son">Son</option>
                    <option value="Daughter">Daughter</option>
                    <option value="Wife">Wife</option>
                    <option value="Husband">Husband</option>
                    <option value="Brother">Brother</option>
                    <option value="Sister">Sister</option>
                    <option value="Grandmother">Grandmother</option>
                    <option value="Grandfather">Grandfather</option>
                    <option value="Uncle">Uncle</option>
                    <option value="Aunt">Aunt</option>
                    <option value="Nephew">Nephew</option>
                    <option value="Niece">Niece</option>
                </select></p>
                <button type="button" class="remove-member bg-red-500 text-white p-2 rounded">Remove Member</button>
            </div>
        `);
        memberCount++;
    });

    $(document).on('click', '.remove-member', function () {
        $(this).closest('.family-member').remove();
    });

    // Enhanced file validation for business brochure
    $('input[name="cd_head_details[business_brochure]"]').change(function() {
        var file = this.files[0];
        var $input = $(this);
        var $errorContainer = $input.closest('.mb-4').find('.file-error');

        // Remove any existing error messages
        if ($errorContainer.length === 0) {
            $errorContainer = $('<div class="file-error text-red-500 text-sm mt-1"></div>');
            $input.closest('.mb-4').append($errorContainer);
        }
        $errorContainer.text('');

        if (file) {
            // Validate file type
            var allowedTypes = ['application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                $errorContainer.text('Error: Only PDF files are allowed for Business Brochure.');
                this.value = '';
                $('#brochure-preview-container').hide();
                $('#brochure-filename').text('');
                $('#brochure-size').text('');
                return;
            }

            // Validate file size (5 MB)
            if (file.size > 5 * 1024 * 1024) {
                $errorContainer.text('Error: Business Brochure file size must be below 5 MB.');
                this.value = '';
                $('#brochure-preview-container').hide();
                $('#brochure-filename').text('');
                $('#brochure-size').text('');
                return;
            }

            // Validate file size is not zero
            if (file.size === 0) {
                $errorContainer.text('Error: The selected file is empty.');
                this.value = '';
                $('#brochure-preview-container').hide();
                $('#brochure-filename').text('');
                $('#brochure-size').text('');
                return;
            }

            // Show preview
            $('#brochure-filename').text(file.name);
            $('#brochure-size').text((file.size / 1024).toFixed(2) + ' KB');
            $('#brochure-preview-container').show();
        } else {
            $('#brochure-preview-container').hide();
            $('#brochure-filename').text('');
            $('#brochure-size').text('');
        }
    });

    $('#change-brochure').on('click', function() {
        $('#business-brochure-input').click();
    });

    $('#remove-brochure').on('click', function() {
        $('#business-brochure-input').val('');
        $('#brochure-preview-container').hide();
        $('#brochure-filename').text('');
        $('#brochure-size').text('');
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

    // Family photo preview and edit controls
    const fileInput = $('#profile-picture-input');
    const previewContainer = $('#photo-preview-container');
    const previewImage = $('#photo-preview');
    const editButton = $('#edit-photo');

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

    // Family Tree Modal and AJAX loading
    $(document).on('click', '.cd-view-tree', function(e) {
        e.preventDefault();
        var familyId = $(this).data('family-id');
        if (!familyId) {
            alert('Invalid family ID');
            return;
        }

        // Show modal
        $('#cd-family-tree-modal').removeClass('hidden');

        // Set modal title
        var familyName = $(this).closest('.cd-family-row, .bg-white').find('h3').first().text();
        $('#modal-family-title').text(familyName + "'s Family Tree");

        // Show loading spinner
        $('#cd-family-tree-container').html('<div class="flex items-center justify-center h-full"><div class="text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div><p class="text-gray-600">Loading family tree...</p></div></div>');

        // AJAX request to get family tree data
        $.ajax({
            url: cd_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'cd_get_family_tree',
                family_id: familyId,
                nonce: cd_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var treeData = response.data;
                    renderFamilyTree(treeData);
                } else {
                    $('#cd-family-tree-container').html('<p class="text-red-600 text-center p-4">' + response.data + '</p>');
                }
            },
            error: function() {
                $('#cd-family-tree-container').html('<p class="text-red-600 text-center p-4">Error loading family tree.</p>');
            }
        });
    });

    // Close modal
    $('#cd-close-modal').on('click', function() {
        $('#cd-family-tree-modal').addClass('hidden');
        $('#cd-family-tree-container').empty();
    });

    // Function to render family tree using Treant.js
    function renderFamilyTree(treeData) {
        // Clear previous tree
        $('#cd-family-tree-container').empty();

        // Prepare Treant config
        var config = {
            chart: {
                container: "#cd-family-tree-container",
                levelSeparation: 40,
                nodeAlign: "CENTER",
                connectors: {
                    type: "step",
                    style: {
                        "stroke-width": 2,
                        "stroke": "#94a3b8"
                    }
                },
                node: {
                    HTMLclass: "family-node",
                    collapsable: true
                }
            },
            nodeStructure: treeData
        };

        // Initialize Treant
        try {
            new Treant(config);
        } catch (e) {
            $('#cd-family-tree-container').html('<p class="text-red-600 text-center p-4">Error rendering family tree: ' + e.message + '</p>');
        }
    }
});
