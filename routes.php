<?php namespace Cleanse\League;

use Redirect;
use Route;
use Cleanse\League\Models\Tournament;
use Cleanse\League\Models\Team;

//Route::get('tourney/create', function () {
//    $tourney = Tournament::create([
//        'name' => 'Aether League'
//    ]);
//
//    $newTourney = '/tournament/'.$tourney->id;
//    return Redirect::to($newTourney);
//});
//
//Route::get('tournament/{id}/{slug?}', function ($tourneyId, $tourneySlug = null) {
//
//    $tournament = Tournament::where('id', $tourneyId)->first();
//
//    if (!$tournament) {
//        $notFound = '/404';
//        return Redirect::to($notFound);
//    }
//
//    if (is_null($tourneySlug)) {
//        $url = '/tournament/' . $tournament->id . '/' . $tournament->slug;
//        return Redirect::to($url, 301);
//    }
//
//    if ($tourneySlug !== $tournament->slug) {
//        $url = '/tournament/' . $tournament->id . '/' . $tournament->slug;
//        return Redirect::to($url, 301);
//    }
//
//    return view('cleanse.league::bracket', ['tournament' => $tournament]);
//});
//
//Route::get('team/{id}/{slug?}', function ($teamId, $teamSlug = null) {
//
//    $team = Team::where('id', $teamId)->first();
//
//    if (!$team) {
//        $notFound = '/404';
//        return Redirect::to($notFound);
//    }
//
//    if (is_null($teamSlug)) {
//        $url = '/team/' . $team->id . '/' . $team->slug;
//        return Redirect::to($url, 301);
//    }
//
//    if ($teamSlug !== $team->slug) {
//        $url = '/team/' . $team->id . '/' . $team->slug;
//        return Redirect::to($url, 301);
//    }
//
//    return $team->stats();
//});
