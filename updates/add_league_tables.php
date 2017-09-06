<?php namespace Cleanse\League\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddLeagueTables extends Migration
{
    public function up()
    {
        Schema::create('cleanse_league_events', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('information')->nullable();
            $table->text('information_html')->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_leagues', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('about')->nullable();
            $table->text('about_html')->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_championships', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('league_id')->unsigned()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('championship_rules')->nullable();
            $table->text('rules_html')->nullable();
            $table->integer('winner_id')->unsigned()->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->foreign('league_id')->references('id')->on('cleanse_league_leagues');
        });

        Schema::create('cleanse_league_seasons', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('championship_id')->unsigned()->index();
            $table->integer('winner_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->foreign('championship_id')->references('id')->on('cleanse_league_championships');
        });

        Schema::create('cleanse_league_tournaments', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id')->unique()->index();
            $table->string('name');
            $table->string('slug');
            $table->json('brackets')->nullable();
            $table->integer('winner_id')->nullable()->unsigned();
            $table->integer('tourneyable_id')->unsigned()->index();
            $table->string('tourneyable_type');
            $table->timestamps();
        });

        Schema::create('cleanse_league_matches', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id')->unique()->index();
            $table->integer('team_one')->unsigned();
            $table->integer('team_two')->unsigned();
            $table->integer('team_one_score')->unsigned()->nullable();
            $table->integer('team_two_score')->unsigned()->nullable();
            $table->integer('winner_id')->unsigned()->nullable();
            $table->string('matchable_id')->index();
            $table->string('matchable_type');
            $table->timestamp('takes_place_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_match_games', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('match_id')->index();
            $table->integer('team_one')->unsigned();
            $table->integer('team_two')->unsigned();
            $table->integer('winner_id')->unsigned()->nullable();
            $table->string('vod')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cleanse_league_match_games');
        Schema::dropIfExists('cleanse_league_matches');
        Schema::dropIfExists('cleanse_league_tournaments');
        Schema::dropIfExists('cleanse_league_seasons');
        Schema::dropIfExists('cleanse_league_championships');
        Schema::dropIfExists('cleanse_league_leagues');
        Schema::dropIfExists('cleanse_league_events');
    }
}
