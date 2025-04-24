# WP Merge Subcategories

A WordPress plugin that helps you merge duplicate WooCommerce product categories and subcategories with ease.

## Description

WP Merge Subcategories provides a simple and efficient way to manage your WooCommerce product categories by:

1. Automatically merging duplicate categories with the same name
2. Providing a manual mapping interface to merge specific categories
3. Ensuring product relationships are maintained during the merge process

## Installation

1. Upload the `wp-merge-subcategories` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to WooCommerce > Products > Merge Categories to access the tools

## Usage

### Auto Merge Duplicates

1. Navigate to WooCommerce > Products > Merge Categories
2. Review the warning message and ensure you have a backup
3. Click the "Auto Merge Categories" button
4. The plugin will automatically merge categories with the same name

### Manual Category Mapping

1. Navigate to WooCommerce > Products > Merge Categories
2. Select a source category from the dropdown
3. Select a target category from the dropdown
4. Click "Map Categories" to merge the selected categories

## Important Notes

- Always create a backup of your products before using this plugin
- Test the merging process on a staging site first if possible
- The merging process cannot be undone
- Make sure you have the necessary permissions to manage WooCommerce products

## Requirements

- WordPress 5.8 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher

## Support

If you encounter any issues or have questions, please create an issue in the GitHub repository.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.0
- Initial release
- Auto merge duplicate categories
- Manual category mapping interface
- Basic security checks and validations 
