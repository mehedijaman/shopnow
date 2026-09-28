<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared Language Lines
    |--------------------------------------------------------------------------
    |
    | Strings used on both surfaces (storefront and admin). Keep this file
    | small: surface-specific text belongs in the owning module's
    | site.php / admin.php files.
    |
    */

    'language' => 'Language',
    'switch_language' => 'Switch language',

    // Shared chrome & widgets
    'search' => 'Search',
    'breadcrumb' => 'Breadcrumb',
    'follow_us' => 'Follow Us',
    'no_results' => 'No results found',
    'try_different_search' => 'Try a different search term',
    'edit_profile' => 'Edit Profile',
    'sign_out' => 'Sign Out',
    'upload_file' => 'Upload File',
    'remove_file' => 'Remove File',
    'file_name' => 'File Name:',
    'file_size' => 'File Size:',
    'bytes' => 'bytes',

    // Pagination summary
    'pagination_summary' => 'Showing :from to :to of :total results',

    // Generic actions
    'cancel' => 'Cancel',
    'confirm' => 'Confirm',
    'delete' => 'Delete',
    'restore' => 'Restore',

    // Quantity steppers
    'decrease_quantity' => 'Decrease quantity',
    'increase_quantity' => 'Increase quantity',

    // Confirmation dialogs
    'confirmation' => 'Confirmation',
    'confirm_message' => 'Are you sure you want to proceed?',
    'delete_confirmation' => 'Delete Confirmation',
    'delete_message' => 'Are you sure you want to permanently delete this item? This action cannot be undone.',
    'restore_confirmation' => 'Restore Confirmation',
    'restore_message' => 'Are you sure you want to restore this item?',
    'restore_help' => 'This action will restore the item and all associated data.',
    'delete_help' => 'This action is permanent and cannot be undone.',

    'menu' => [
        'dashboard' => 'Dashboard',
        'contact_messages' => 'Contact Messages',
        'order_management' => 'Order Management',
        'order_list' => 'Order List',
        'create_order' => 'Create Order',
        'order_report' => 'Order Report',
        'promo_codes' => 'Promo Codes',
        'product_management' => 'Product Management',
        'new_product' => 'New Product',
        'products' => 'Products',
        'product_report' => 'Product Report',
        'product_categories' => 'Product Categories',
        'product_tags' => 'Product Tags',
        'product_brands' => 'Product Brands',
        'product_attributes' => 'Product Attributes',
        'customer_management' => 'Customer Management',
        'customers' => 'Customers',
        'customer_report' => 'Customer Report',
        'blog' => 'Blog',
        'posts' => 'Posts',
        'categories' => 'Categories',
        'tags' => 'Tags',
        'authors' => 'Authors',
        'sliders' => 'Sliders',
        'pages' => 'Pages',
        'access_control_list' => 'Access Control List',
        'users' => 'Users',
        'permissions' => 'Permissions',
        'roles' => 'Roles',
        'settings' => 'Settings',
        'my_profile' => 'My Profile',
    ],

    'field' => [
        'summary' => 'Summary',
        'sku' => 'SKU',
        'slug' => 'Slug',
        'attributes' => 'Attributes',
        'tags' => 'Tags',
        'sale' => 'Sale',
        'image' => 'Image',
        'values' => 'Values',
        'virtual_short' => 'Virtual',
        'publish_date' => 'Publish Date',
        'inactive' => 'Inactive',
        'brand' => 'Brand',
        'category' => 'Category',
        'status' => 'Status',
        'type' => 'Type',
        'stock' => 'Stock',
        'tag' => 'Tag',
        'attribute' => 'Attribute',
        'attribute_value' => 'Attribute Value',
        'min_order' => 'Min Order',
        'stock_qty' => 'Stock Qty',
        'short_summary' => 'Short Summary',
        'product_name' => 'Product Name',
        'featured_image' => 'Featured Image',
        'gallery' => 'Gallery',
        'downloadable_files' => 'Downloadable Files',
        'virtual' => 'Virtual (no shipping)',
        'downloadable' => 'Downloadable',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'display_name' => 'Display Name',
        'file' => 'File',
        'author' => 'Author',
        'name' => 'Name',
        'description' => 'Description',
        'price' => 'Price',
        'sale_price' => 'Sale Price',
        'quantity' => 'Quantity',
        'unit' => 'Unit',
        'active' => 'Active',
        'featured' => 'Featured',
        'featured_brand' => 'Featured Brand',
        'featured_category' => 'Featured Category',
        'product_summary' => 'Product Summary',
        'email_address' => 'Email address',
        'password' => 'Password',
        'remember_me' => 'Remember me',
        'new_password' => 'New password',
        'confirm_new_password' => 'Confirm new password',
    ],

    'date_range' => [
        'all_time' => 'All Time',
        'this_month' => 'This Month',
        'last_12_months' => 'Last 12 Months',
    ],

    'status' => [
        'all' => 'All Statuses',
    ],

    'seo' => [
        'preview_heading' => 'SEO - Preview of how it will be listed on Google',
        'edit' => 'Edit SEO content',
        'fill_hint' => '(fill the title and description to see a preview)',
        'site_name' => 'Your Site Name',
        'meta_tag_title' => 'Meta Tag Title',
        'meta_tag_description' => 'Meta Tag Description',
        'of_limit' => ':remaining of :limit',
    ],

    'save' => 'Save',
    'saving' => 'Saving…',
    'back' => 'Back',
    'add' => 'Add',
    'upload' => 'Upload',
    'report' => 'Report',
    'recycle_bin' => 'Recycle Bin',
    'restore_all' => 'Restore All',
    'empty_bin' => 'Empty Bin',
    'recycle_bin_empty' => 'The recycle bin is empty.',
    'clear_filter' => 'Clear Filter',
    'apply_filter' => 'Apply Filter',
    'home' => 'Home',
    'create' => 'Create',
    'back_to_list' => 'Back to List',
    'yes' => 'Yes',
    'no' => 'No',
    'optional' => 'Optional',
    'required' => 'Required',
    'preview' => 'Preview:',
    'remove' => 'Remove',
    'delete_permanently' => 'Delete Permanently',
    'edit' => 'Edit',

    'filter' => [
        'all_status' => 'All Status',
        'all_stock' => 'All Stock',
        'low_stock' => 'Low Stock (<10)',
        'out_of_stock' => 'Out of Stock',
        'all_products' => 'All Products',
        'featured_only' => 'Featured Only',
        'all_brands' => 'All Brands',
        'all_categories' => 'All Categories',
        'all_tags' => 'All Tags',
        'all_attributes' => 'All Attributes',
        'all_values' => 'All Values',
    ],

    'header' => [
        'products' => 'Products',
        'input_type' => 'Input Type',
        'qty_sold' => 'Qty Sold',
        'revenue' => 'Revenue',
        'action' => 'Action',
        'qty' => 'Qty',
        'sl' => 'SL',
        'product' => 'Product',
        'category' => 'Category',
        'brand' => 'Brand',
        'price' => 'Price',
        'stock' => 'Stock',
        'status' => 'Status',
        'actions' => 'Actions',
        'name' => 'Name',
    ],

    'tooltip' => [
        'view_details' => 'View Details',
    ],

];
