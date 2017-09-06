<?php namespace Cleanse\League\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddTeamPlayerTables extends Migration
{
    public function up()
    {
        Schema::create('cleanse_league_teams', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id')->unique()->index();
            $table->string('name');
            $table->string('slug');
            $table->string('initials')->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_event_teams', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('team_id')->index();
            $table->integer('match_wins')->unsigned()->nullable();
            $table->integer('match_losses')->unsigned()->nullable();
            $table->integer('game_wins')->unsigned()->nullable();
            $table->integer('game_losses')->unsigned()->nullable();
            $table->integer('game_ties')->unsigned()->nullable();
            $table->string('teamable_id')->index();
            $table->string('teamable_type');
            $table->timestamps();
        });

        Schema::create('cleanse_league_players', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id')->unique()->index();
            $table->string('team_id')->index()->nullable();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('cleanse_league_event_players', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('player_id')->index();
            $table->string('event_team_id')->index();
            $table->string('medals')->nullable();
            $table->integer('kills')->unsigned()->nullable();
            $table->integer('deaths')->unsigned()->nullable();
            $table->integer('assists')->unsigned()->nullable();
            $table->integer('damage')->unsigned()->nullable();
            $table->integer('healing')->unsigned()->nullable();
            $table->integer('match_wins')->unsigned()->nullable();
            $table->integer('match_losses')->unsigned()->nullable();
            $table->integer('game_wins')->unsigned()->nullable();
            $table->integer('game_losses')->unsigned()->nullable();
            $table->integer('game_ties')->unsigned()->nullable();
            $table->string('playerable_id')->index();
            $table->string('playerable_type');
            $table->timestamps();
        });

        Schema::create('cleanse_league_match_game_players', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('game_id')->index();
            $table->string('player_id')->index();
            $table->string('player_job')->nullable();
            $table->integer('medals')->nullable();
            $table->integer('kills')->unsigned()->nullable();
            $table->integer('deaths')->unsigned()->nullable();
            $table->integer('assists')->unsigned()->nullable();
            $table->integer('damage')->unsigned()->nullable();
            $table->integer('healing')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cleanse_league_match_game_players');
        Schema::dropIfExists('cleanse_league_event_players');
        Schema::dropIfExists('cleanse_league_players');
        Schema::dropIfExists('cleanse_league_event_teams');
        Schema::dropIfExists('cleanse_league_teams');
    }
}
