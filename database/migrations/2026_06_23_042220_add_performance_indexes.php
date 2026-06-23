<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products
        Schema::table('products', function (Blueprint $table) {
            $table->index('active');
            $table->index('featured');
            $table->index(['active', 'featured']);
        });

        // Product categories
        Schema::table('product_categories', function (Blueprint $table) {
            $table->index('active');
            $table->index('featured');
            $table->index('sort_order');
            $table->index(['active', 'featured', 'sort_order', 'name']);
        });

        // Product brands
        Schema::table('product_brands', function (Blueprint $table) {
            $table->index('active');
        });

        // Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index(['status', 'payment_status']);
            $table->index('deleted_at');
        });

        // Order products
        Schema::table('order_products', function (Blueprint $table) {
            $table->index(['order_id', 'product_id']);
        });

        // Blog posts
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index('published_at');
        });

        // Blog authors
        Schema::table('blog_authors', function (Blueprint $table) {
            $table->index('deleted_at');
        });

        // Blog tags
        Schema::table('blog_tags', function (Blueprint $table) {
            $table->index('deleted_at');
        });

        // Sliders
        Schema::table('sliders', function (Blueprint $table) {
            $table->index('active');
            $table->index('order');
        });

        // Contact messages
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index('read_at');
            $table->index('deleted_at');
        });

        // Customers
        Schema::table('customers', function (Blueprint $table) {
            $table->index('active');
            $table->index('phone');
            $table->index('deleted_at');
        });

        // Pages
        Schema::table('pages', function (Blueprint $table) {
            $table->index('active');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['featured']);
            $table->dropIndex(['active', 'featured']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['featured']);
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['active', 'featured', 'sort_order', 'name']);
        });

        Schema::table('product_brands', function (Blueprint $table) {
            $table->dropIndex(['active']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['status', 'payment_status']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'product_id']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
        });

        Schema::table('blog_authors', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('blog_tags', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['order']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex(['read_at']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['deleted_at']);
        });
    }
};
