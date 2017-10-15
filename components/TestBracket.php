<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Tournament;
use Cleanse\League\Models\Team;

class TestBracket extends ComponentBase
{
    public $matches = [];
    public $tourney;

    //test
    public $format = false;

    //old
    public $bracket;
    public $groups;
    public $grouped;
    public $winner;

    public function componentDetails()
    {
        return [
            'name'        => 'Bracket',
            'description' => 'League Bracket.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->tourney = $this->page['tourney'] = $this->getTourney();
        $this->matches = $this->page['matches'] = $this->getMatches();

        //test
        $this->format = $this->page['format'] = true;

        //old
//        $this->bracket = $this->page['bracket'] = $this->getBracket();
//        $this->groups = $this->page['groups'] = $this->getGroups();
//        $this->grouped = $this->page['grouped'] = $this->getGrouped();
//        $this->winner = $this->page['winner'] = $this->getWinner();
    }

    public function getTourney()
    {
        return Tournament::where('id', 'yvdkmtpa')->with('teams')->first();
    }

    public function getMatches()
    {
        return $matches = [
            [
                'number' => '1',
                'x' => '0', 'y' => '35',
                'progress' => [
                    'next_w' => '7',
                    'next_l' => '5'
                ],
                'winner' => 1,
                'teams' => [
                    [
                        'id' => 1,
                        'score' => 2,
                        'name' => 'Insomnia',
                        'home' => true,
                        'seed' => 1,
                        'logo' => '/themes/pvpaissa/assets/images/discord-logo-white.png',
                    ],
                    [
                        'id' => 2,
                        'score' => 1,
                        'name' => 'Exiled',
                        'home' => false,
                        'seed' => 8,
                        'logo' => false,
                    ]
                ]
            ],
            ['number' => '2', 'x' => '0', 'y' => '89', 'winner' => 3, 'teams' => [
                [
                    'id' => 3,
                    'score' => 2,
                    'name' => 'Cats',
                    'home' => true,
                    'seed' => 4,
                    'logo' => false,
                ],
                [
                    'id' => 4,
                    'score' => 0,
                    'name' => 'RareX',
                    'home' => false,
                    'seed' => 5,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png',
                ]
            ]],
            ['number' => '3', 'x' => '0', 'y' => '143', 'winner' => 6, 'teams' => [
                [
                    'id' => 5,
                    'score' => '0',
                    'name' => 'Spicy',
                    'home' => true,
                    'seed' => 2,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png',
                ],
                [
                    'id' => 6,
                    'score' => '2',
                    'name' => 'MAGA',
                    'home' => false,
                    'seed' => 7,
                    'logo' => false,
                ]
            ]],
            ['number' => '4', 'x' => '0', 'y' => '197', 'winner' => 8, 'teams' => [
                [
                    'id' => 7,
                    'score' => '0',
                    'name' => 'Nacl',
                    'home' => true,
                    'seed' => 3,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png',
                ],
                [
                    'id' => 8,
                    'score' => '2',
                    'name' => 'EM',
                    'home' => false,
                    'seed' => 6,
                    'logo' => false,
                ]
            ]],
            ['number' => '5', 'x' => '0', 'y' => '340', 'winner' => 0, 'teams' => [
                [
                    'id' => 2,
                    'score' => '0',
                    'name' => 'Exiled',
                    'home' => true,
                    'seed' => 8,
                    'logo' => false,
                ],
                [
                    'id' => 4,
                    'score' => '0',
                    'name' => 'RareX',
                    'home' => false,
                    'seed' => 5,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png',
                ]
            ]],
            ['number' => '6', 'x' => '0', 'y' => '394', 'winner' => 0, 'teams' => [
                [
                    'id' => 5,
                    'score' => '0',
                    'name' => 'Spicy',
                    'home' => true,
                    'seed' => 2,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png',
                ],
                [
                    'id' => 7,
                    'score' => '0',
                    'name' => 'Nacl',
                    'home' => false,
                    'seed' => 3,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png',
                ]
            ]],
            ['number' => '7', 'x' => '244', 'y' => '62', 'winner' => 0, 'teams' => [
                [
                    'id' => 1,
                    'score' => '0',
                    'name' => 'Insomnia',
                    'home' => true,
                    'seed' => 1,
                    'logo' => '//lh3.googleusercontent.com/-dUhXV0hheB8/VlH91JfzbJI/AAAAAAAAABE/aB2UPsrSpys/w800-h800/2000px-V_%2528logo_2010%2529.svg.png'
                ],
                [
                    'id' => 3,
                    'score' => '0',
                    'name' => 'Cats',
                    'home' => false,
                    'seed' => 4,
                    'logo' => false
                ]
            ]],
            ['number' => '8', 'x' => '244', 'y' => '170', 'winner' => 0, 'teams' => [
                [
                    'id' => 6,
                    'score' => '0',
                    'name' => 'MAGA',
                    'home' => true,
                    'seed' => 7,
                    'logo' => false
                ],
                [
                    'id' => 8,
                    'score' => '0',
                    'name' => 'EM',
                    'home' => false,
                    'seed' => 6,
                    'logo' => false
                ]
            ]],
            ['number' => '9', 'x' => '244', 'y' => '367', 'winner' => 0, 'teams' => [
                [
                    'name' => 'Loser of Match 7',
                    'home' => true,
                ],
                [
                    'name' => ''
                ]
            ]],
            ['number' => '10', 'x' => '244', 'y' => '313', 'winner' => 0, 'teams' => [
                [
                    'name' => 'Loser of Match 8',
                    'home' => true,
                ],
                [
                    'name' => ''
                ]
            ]],
            ['number' => '11', 'x' => '488', 'y' => '340', 'winner' => 0,  'teams' => [
                [
                    'name' => '',
                    'home' => true,
                ],
                [
                    'name' => ''
                ]
            ]],
            ['number' => '12', 'x' => '488', 'y' => '115', 'winner' => 0,  'teams' => [
                [
                    'name' => '',
                    'home' => true,
                ],
                [
                    'name' => ''
                ]
            ]],
            ['number' => '13', 'x' => '732', 'y' => '313', 'winner' => 0,  'teams' => [
                [
                    'name' => 'Loser of Match 12',
                    'home' => true,
                ],
                [
                    'name' => ''
                ]
            ]],
            ['number' => '14', 'x' => '732', 'y' => '143', 'winner' => 0,  'teams' => [
                [
                    'name' => '',
                    'home' => true,
                ],
                [
                    'name' => ''
                ]
            ]]
        ];
    }

    private function getWinner()
    {
        return "";
    }

    private function getGrouped()
    {
        $bracket = $this->getBracketOld(); //old

        $result = [];
        foreach ($bracket as $data) {
            $id = $data['roundNo'];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }

        return $result;
    }

    private function getGroups()
    {
        $bracket = $this->getBracketOld(); //old

        return $this->groups = $groupCount = count(array_unique(array_map(function($s) {
            return $s['roundNo'];
        }, $bracket)));
    }

    private function getBracket()
    {
        //Double Elim
        $double = [
            ['bracketNo' => 1, 'lastGames' => null, 'nextGame' => 7, 'roundNo' => 1, 'teamnames' => ["Cleanse", "Ikai"]],
            ['bracketNo' => 2, 'lastGames' => null, 'nextGame' => 7, 'roundNo' => 1, 'teamnames' => ["Scheme", "Dead"]],
            ['bracketNo' => 3, 'lastGames' => null, 'nextGame' => 8, 'roundNo' => 1, 'teamnames' => ["Wj", "Salma"]],
            ['bracketNo' => 4, 'lastGames' => null, 'nextGame' => 8, 'roundNo' => 1, 'teamnames' => ["Orth", "Nadagast"]],
            ['bracketNo' => 5, 'lastGames' => [1, 2], 'nextGame' => 10, 'roundNo' => 1, 'teamnames' => ["Ikai", "Scheme"]],
            ['bracketNo' => 6, 'lastGames' => [3, 4], 'nextGame' => 9, 'roundNo' => 1, 'teamnames' => ["Salma", "Orth"]],

            ['bracketNo' => 7, 'lastGames' => [1, 2], 'nextGame' => 12, 'roundNo' => 2, 'teamnames' => ["Cleanse", "Dead"]],
            ['bracketNo' => 8, 'lastGames' => [3, 4], 'nextGame' => 12, 'roundNo' => 2, 'teamnames' => ["Wj", "Nadagast"]],
            ['bracketNo' => 10, 'lastGames' => [5, 6], 'nextGame' => 11, 'roundNo' => 2, 'teamnames' => ["Nadagast", "Ikai"]],
            ['bracketNo' => 9, 'lastGames' => [5, 7], 'nextGame' => 11, 'roundNo' => 2, 'teamnames' => ["Cleanse", "Salma"]],

            ['bracketNo' => 11, 'lastGames' => [6, 8], 'nextGame' => 12, 'roundNo' => 3, 'teamnames' => ["Ikai", "Salma"]],
            ['bracketNo' => 12, 'lastGames' => [9, 33], 'nextGame' => 13, 'roundNo' => 3, 'teamnames' => ["Dead", "Wj"]],

            ['bracketNo' => 13, 'lastGames' => [11, 12], 'nextGame' => null, 'roundNo' => 4, 'teamnames' => ["Dead", "Ikai"]],
            ['bracketNo' => 14, 'lastGames' => [11, 13], 'nextGame' => null, 'roundNo' => 4, 'teamnames' => ["Wj", "Ikai"]],

            ['bracketNo' => 15, 'lastGames' => [11, 13], 'nextGame' => null, 'roundNo' => 5, 'teamnames' => ["Wj", "Ikai"]] //reset, grand-grand finals
        ];

        //Single Elim
        $single = [
            ['bracketNo' => 1, 'lastGames' => null, 'nextGame' => 5, 'roundNo' => 1, 'teamnames' => ["Cleanse", "Ikai"]],
            ['bracketNo' => 2, 'lastGames' => null, 'nextGame' => 5, 'roundNo' => 1, 'teamnames' => ["Scheme", "Dead"]],
            ['bracketNo' => 3, 'lastGames' => null, 'nextGame' => 6, 'roundNo' => 1, 'teamnames' => ["Wj", "Salma"]],
            ['bracketNo' => 4, 'lastGames' => null, 'nextGame' => 6, 'roundNo' => 1, 'teamnames' => ["Orth", "Nadagast"]],
            ['bracketNo' => 5, 'lastGames' => [1, 2], 'nextGame' => 7, 'roundNo' => 2, 'teamnames' => ["Cleanse", ""]],
            ['bracketNo' => 6, 'lastGames' => [3, 4], 'nextGame' => 7, 'roundNo' => 2, 'teamnames' => ["Wj", ""]],
            ['bracketNo' => 7, 'lastGames' => [5, 6], 'nextGame' => null, 'roundNo' => 3, 'teamnames' => ["", ""]]
        ];

        return $single;
    }
}
