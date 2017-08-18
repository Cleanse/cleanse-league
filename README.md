# League Plugin
Not functional - yet.

## Components
/l - home  
/l/vision - league vision  
/l/about - league about  

/l/rules & /c/rules - rules for the current championship series  
/c/:champ-slug/teams - team list ordered by cs points  

/s/:season-slug/teams - team list ordered by win loss

/l/schedule - schedule for current week  
/l/schedule/:season/:week - schedule for specific week

/l/match/:id

/l/team/:id  
/l/team/:id/schedule  


# League
 * $id
 * $name
 * $slug
 * $about
 * $vision

## Season
 * $id
 * $league_id
 * $team_id
 * $number
 * $finished_at

## Teams
 * $id
 * $user_id - admin
 * $name
 * $slug

### Members

## Playoffs
### Bracket > Round

## Divisions

Bracket.php
Champion.php
Division.php
Match.php
Member.php
Season.php
Series.php
Team.php

===Stats  
===Schedule  
===Standings  
===Rules  
==== League Fans  
Way to generate prizes.

Log tourney admins

Components:
/league-admin/
/league-admin/logs
/league-admin/league/create-edit-delete(hide)
/league-admin/season/add-edit-delete(hide)
/league-admin/division/add-edit-delete(hide)
/league-admin/team/add-edit-delete(hide)
/league-admin/member/add-edit-delete(hide)


/league/aethercup/schedule/
/league/aether/season/1

/league/signup

/league/player/id

/league/team/team-name