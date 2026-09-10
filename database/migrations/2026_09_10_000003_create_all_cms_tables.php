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
        // 1. FAQ Categories
        if (!Schema::hasTable('faq_categories')) {
            Schema::create('faq_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 191)->index()->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_parent')->default(0);
                $table->unsignedBigInteger('parent_id')->index()->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('is_active')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. FAQs
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable()->index();
                $table->boolean('is_parent')->nullable()->default(0);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('short_description')->nullable();
                $table->text('tags')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('faq_category_id')->index()->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('is_active')->default(1)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Blog Categories
        if (!Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->string('featured_image')->index()->nullable();
                $table->boolean('is_parent')->default(0);
                $table->unsignedBigInteger('parent_id')->index()->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('is_active')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Blogs
        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable()->index();
                $table->string('slug')->nullable()->index();
                $table->dateTime('publish_date')->nullable();
                $table->string('image')->nullable();
                $table->string('author_name')->nullable();
                $table->string('author_image')->nullable();
                $table->string('custom_slug')->index()->nullable();
                $table->longText('content')->nullable();
                $table->text('main_css')->nullable();
                $table->text('section_css')->nullable();
                $table->string('seo_title')->index()->nullable();
                $table->text('seo_keyword')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('social_share_image')->index()->nullable();
                $table->text('social_share_description')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('type')->nullable();
                $table->boolean('is_active')->default(1)->index()->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 5. Careers
        if (!Schema::hasTable('careers')) {
            Schema::create('careers', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('type')->nullable();
                $table->string('job_level')->nullable();
                $table->integer('no_of_vacancies')->nullable();
                $table->string('employment_type')->nullable();
                $table->string('job_location')->nullable();
                $table->string('offered_salary')->nullable();
                $table->date('apply_before')->nullable();
                $table->date('expiry_date')->nullable();
                $table->string('min_qualification')->nullable();
                $table->string('position')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 6. Career Applications
        if (!Schema::hasTable('career_applications')) {
            Schema::create('career_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('career_id')->nullable()->index();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('file')->nullable();
                $table->string('received_at')->nullable();
                $table->string('is_read')->nullable()->default('0');
                $table->string('is_shortlisted')->nullable()->default('0');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 7. Notices
        if (!Schema::hasTable('notices')) {
            Schema::create('notices', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->longText('description')->nullable();
                $table->string('image')->nullable();
                $table->string('file')->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('is_active')->default(1)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 8. Teams
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->string('designation')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('linkedin')->nullable();
                $table->string('instagram')->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('is_active')->default(1)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 9. Testimonials
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('name')->nullable();
                $table->string('type')->nullable();
                $table->string('job_title')->nullable();
                $table->float('rating')->nullable()->default(5.0);
                $table->string('image')->nullable();
                $table->string('status')->nullable();
                $table->string('position')->nullable()->default(0);
                $table->boolean('is_active')->default(1)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 10. Services
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('type')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->longText('description')->nullable();
                $table->string('image')->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('is_active')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 11. Slider Types & Sliders
        if (!Schema::hasTable('slider_types')) {
            Schema::create('slider_types', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('sliders')) {
            Schema::create('sliders', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('link')->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->boolean('new_tab')->nullable()->default(0);
                $table->longText('description')->nullable();
                $table->string('heading_text')->nullable();
                $table->string('sub_heading_text')->nullable();
                $table->string('button_text')->nullable();
                $table->boolean('show_button')->nullable()->default(1);
                $table->string('image')->nullable();
                $table->boolean('is_active')->nullable()->default(1);
                $table->unsignedBigInteger('slider_type_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 12. Partners
        if (!Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('url')->nullable();
                $table->text('description')->nullable();
                $table->string('featured_photo')->nullable();
                $table->boolean('is_active')->default(1)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 13. Popups
        if (!Schema::hasTable('popups')) {
            Schema::create('popups', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->text('description')->nullable();
                $table->string('link')->nullable();
                $table->string('type')->nullable();
                $table->string('video_url')->nullable();
                $table->string('location')->nullable();
                $table->string('show_location')->nullable();
                $table->string('image')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 14. Pages
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('custom_slug')->nullable();
                $table->longText('content')->nullable();
                $table->unsignedBigInteger('position')->nullable()->default(0);
                $table->string('seo_title')->nullable();
                $table->string('seo_keyword')->nullable();
                $table->text('seo_description')->nullable();
                $table->unsignedBigInteger('views')->nullable()->default(0);
                $table->boolean('is_active')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 15. News & Updates
        if (!Schema::hasTable('news_and_updates')) {
            Schema::create('news_and_updates', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable()->index();
                $table->string('slug')->nullable()->index();
                $table->string('url')->nullable()->index();
                $table->string('published_by')->nullable();
                $table->dateTime('publish_date')->nullable();
                $table->string('social_share_image')->index()->nullable();
                $table->boolean('is_active')->default(1)->index()->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 16. Menus & Menu Items
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('location')->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('type')->nullable();
                $table->string('value')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('menu_id')->nullable()->index();
                $table->unsignedBigInteger('page_id')->nullable();
                $table->unsignedBigInteger('blog_id')->nullable();
                $table->integer('position')->nullable()->default(0);
                $table->integer('depth')->nullable()->default(0);
                $table->boolean('new_tab')->nullable()->default(0);
                $table->boolean('is_active')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 17. Albums & Album Values
        if (!Schema::hasTable('albums')) {
            Schema::create('albums', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('cover_image')->nullable();
                $table->text('description')->nullable();
                $table->string('tags')->nullable();
                $table->string('position')->nullable()->default(0);
                $table->string('event_date')->nullable();
                $table->boolean('is_active')->default(1);
                $table->unsignedBigInteger('album_id')->unsigned()->index()->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('album_values')) {
            Schema::create('album_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('album_id')->nullable()->index();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->string('path')->nullable();
                $table->boolean('is_featured')->default(0);
                $table->integer('position')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 18. Enquiries & Contact Us
        if (!Schema::hasTable('enquiries')) {
            Schema::create('enquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->string('subject')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('message')->nullable();
                $table->string('token')->nullable();
                $table->boolean('mark_as_read')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('contact_us')) {
            Schema::create('contact_us', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->boolean('is_read')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 19. Site Settings
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('company_name')->nullable();
                $table->longText('description')->nullable();
                $table->string('mobile')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->longText('map_url')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('youtube')->nullable();
                $table->string('instagram')->nullable();
                $table->string('linkedin')->nullable();
                $table->string('tiktok')->nullable();
                $table->string('whatsapp')->nullable();
                $table->string('slogan')->nullable();
                $table->string('tagline')->nullable();
                $table->string('website')->nullable();
                $table->string('copy_right_text')->nullable();
                $table->string('fav_icon')->nullable();
                $table->string('logo')->nullable();
                $table->string('app_logo')->nullable();
                $table->string('footer_logo')->nullable();
                $table->string('seo_title')->nullable();
                $table->text('seo_keyword')->nullable();
                $table->text('seo_description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('contact_us');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('album_values');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('news_and_updates');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('popups');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('slider_types');
        Schema::dropIfExists('services');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('career_applications');
        Schema::dropIfExists('careers');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
    }
};
