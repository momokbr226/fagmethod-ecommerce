<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('email');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('description');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('attributes');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('notes');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('currency');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('product_attributes');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('product_attributes');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('comment');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
