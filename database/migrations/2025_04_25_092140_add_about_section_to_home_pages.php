<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAboutSectionToHomePages extends Migration
{
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('about_title')->nullable()->after('banner_image');
            $table->text('about_text')->nullable()->after('about_title');
        });
    }

    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropColumn(['about_title','about_text']);
        });
    }
}
