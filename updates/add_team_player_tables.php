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
            $table->integer('match_total')->unsigned()->nullable();
            $table->integer('match_wins')->unsigned()->nullable();
            $table->integer('match_losses')->unsigned()->nullable();
            $table->integer('game_total')->unsigned()->nullable();
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
            $table->integer('event_team_id')->unsigned()->index();
            $table->json('jobs');
            $table->integer('medals')->unsigned()->nullable();
            $table->integer('kills')->unsigned()->nullable();
            $table->integer('deaths')->unsigned()->nullable();
            $table->integer('assists')->unsigned()->nullable();
            $table->integer('damage')->unsigned()->nullable();
            $table->integer('healing')->unsigned()->nullable();
            $table->integer('match_total')->unsigned()->nullable();
            $table->integer('match_wins')->unsigned()->nullable();
            $table->integer('match_losses')->unsigned()->nullable();
            $table->integer('game_total')->unsigned()->nullable();
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
            $table->integer('game_id')->unsigned()->index();
            $table->integer('team_id')->unsigned()->index();
            $table->integer('game_winner_id')->unsigned()->index();
            $table->integer('player_id')->unsigned()->index();
            $table->string('player_job')->nullable();
            $table->integer('medals')->unsigned()->nullable();
            $table->integer('kills')->unsigned()->nullable();
            $table->integer('deaths')->unsigned()->nullable();
            $table->integer('assists')->unsigned()->nullable();
            $table->integer('damage')->unsigned()->nullable();
            $table->integer('healing')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_championship_teams', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('championship_id')->unsigned()->index();
            $table->string('team_id')->index();
            $table->integer('points')->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_championship_points', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('championship_team_id')->unsigned()->index();
            $table->integer('championship_id')->unsigned()->index();
            $table->string('team_id')->index();
            $table->string('source')->nullable();
            $table->integer('value')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('cleanse_league_manager_logs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('admin_id')->unsigned()->index();
            $table->string('ip')->index();
            $table->string('method')->nullable();
            $table->text('values')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cleanse_league_manager_logs');
        Schema::dropIfExists('cleanse_league_championship_points');
        Schema::dropIfExists('cleanse_league_championship_teams');
        Schema::dropIfExists('cleanse_league_match_game_players');
        Schema::dropIfExists('cleanse_league_event_players');
        Schema::dropIfExists('cleanse_league_players');
        Schema::dropIfExists('cleanse_league_event_teams');
        Schema::dropIfExists('cleanse_league_teams');
    }
}
