jQuery(document).ready(function($) {
    // Handle approval toggle
    $('.cd-approval-toggle').on('change', function() {
        var $toggle = $(this);
        var postId = $toggle.data('post-id');
        var approved = $toggle.is(':checked') ? '1' : '0';
        var $statusText = $toggle.closest('td').find('.cd-status-text');

        // Update status text immediately
        $statusText.text(approved === '1' ? 'Approved' : 'Pending');

        // Send AJAX request
        $.ajax({
            url: cd_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cd_toggle_approval',
                nonce: cd_ajax.nonce,
                post_id: postId,
                approved: approved
            },
            success: function(response) {
                if (response.success) {
                    // Status updated successfully
                    console.log('Approval status updated');
                } else {
                    // Revert the toggle if there was an error
                    $toggle.prop('checked', !$toggle.is(':checked'));
                    $statusText.text($toggle.is(':checked') ? 'Approved' : 'Pending');
                    alert('Error updating approval status: ' + response.data.message);
                }
            },
            error: function() {
                // Revert the toggle on AJAX error
                $toggle.prop('checked', !$toggle.is(':checked'));
                $statusText.text($toggle.is(':checked') ? 'Approved' : 'Pending');
                alert('AJAX error occurred while updating approval status');
            }
        });
    });
});
