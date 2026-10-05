<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->nullable()->index();
            $table->string('erp_person_uuid', 64)->nullable()->unique();
            $table->string('profile_image')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->text('social_links')->nullable();
            $table->boolean('active')->default(false)->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('author_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('author_id')->index();
            $table->string('locale', 10);
            $table->string('slug', 191);
            $table->string('name', 191);
            $table->string('professional_title')->nullable();
            $table->text('biography')->nullable();
            $table->text('expertise')->nullable();
            $table->timestamps();
            $table->unique(['locale', 'slug']);
            $table->unique(['author_id', 'locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('author_translations');
        Schema::dropIfExists('authors');
    }
}
