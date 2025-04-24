jQuery(document).ready(function($) {
    // Load categories into select boxes
    function loadCategories() {
        $.ajax({
            url: wpmsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wpms_get_categories',
                nonce: wpmsData.nonce
            },
            success: function(response) {
                if (response.success) {
                    const categories = response.data;
                    const $sourceSelect = $('#wpms-source-category');
                    const $targetSelect = $('#wpms-target-category');
                    
                    $sourceSelect.empty().append('<option value="">Select source category</option>');
                    $targetSelect.empty().append('<option value="">Select target category</option>');
                    
                    categories.forEach(function(category) {
                        $sourceSelect.append(`<option value="${category.id}">${category.name}</option>`);
                        $targetSelect.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }
            }
        });
    }

    // Auto merge button handler
    $('#wpms-auto-merge').on('click', function() {
        if (!confirm('Are you sure you want to merge duplicate categories? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: wpmsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wpms_auto_merge',
                nonce: wpmsData.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadCategories();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Manual mapping button handler
    $('#wpms-map-categories').on('click', function() {
        const sourceId = $('#wpms-source-category').val();
        const targetId = $('#wpms-target-category').val();

        if (!sourceId || !targetId) {
            alert('Please select both source and target categories');
            return;
        }

        if (sourceId === targetId) {
            alert('Source and target categories cannot be the same');
            return;
        }

        if (!confirm('Are you sure you want to merge these categories? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: wpmsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wpms_map_categories',
                nonce: wpmsData.nonce,
                source_id: sourceId,
                target_id: targetId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadCategories();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Initial load
    loadCategories();
}); 