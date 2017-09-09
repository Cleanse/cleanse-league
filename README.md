# League Plugin
**Not functional - yet, but close.**  
If you have an idea on features to add, send me an email: plovato@gmail.com  

To-do:
- Add Event dispatching for logging of admin / staff actions.  
- Event Team's Season+Season tourney stats can be combined as a third entity if needed.

## Components
**cleanseLeagueHome** (/league) - League home page containing upcoming matches and top team/player stats.  
**cleanseLeagueAbout** (/league/about) - An about page for the league. (optional)  

/league/rules & /league/championship/rules - Rules for the current Championship Series.  
/league/championship/:champ-slug/teams - Team list ordered by Championship Series points. 
**cleanseLeagueRules** (/league/rules) - Setup a rules page for your current championship. (optional)   

/league/season/:season-slug/teams - team list ordered by win loss

/l/schedule - schedule for current week  
/l/schedule/:season/:week - schedule for specific week

/league/match/:id

/league/team/:id  
/league/team/:id/schedule  

/manage/league - Manage the league name, slug (option to force slug rename), and about page (can be null).  

/manage/championship - Manage the championship name and slug (option to force slug rename).  
/manage/championship/rules - Manage the rules for the championship series.  
/manage/championship/points - Manage the point system for the championship series.

/manage/season   

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

===Stats  
===Schedule  
===Standings  
===Rules  
==== League Fans  
Way to generate prizes.  
Log tourney admins