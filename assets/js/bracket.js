$(document).on('ready', function () {

    var knownBrackets = [2, 4, 8, 16, 32];
    var exampleTeams = ['BoxeR', 'RareX', 'Cats', 'Elysium', 'Cactuar', 'Faerie', 'Adamantoise', 'Primal'];
    var bracketCount = 0;

    /*
     * Build our bracket "model"
     */
    function getBracket() {
        var bracketZ = [
            {bracketNo: 1, bye: false, lastGames: null,   nextGame: 5,    roundNo: 1, teamnames: ["Cleanse", "Ikai"]},
            {bracketNo: 2, bye: false, lastGames: null,   nextGame: 5,    roundNo: 1, teamnames: ["Scheme", "Dead"]},
            {bracketNo: 3, bye: false, lastGames: null,   nextGame: 6,    roundNo: 1, teamnames: ["Wj", "Salma"]},
            {bracketNo: 4, bye: false, lastGames: null,   nextGame: 6,    roundNo: 1, teamnames: ["Orth", "Nadagast"]},
            {bracketNo: 5, bye: false, lastGames: [1, 2], nextGame: 7,    roundNo: 2, teamnames: ["Cleanse", ""]},
            {bracketNo: 6, bye: false, lastGames: [3, 4], nextGame: 7,    roundNo: 2, teamnames: ["Wj", ""]},
            {bracketNo: 7, bye: false, lastGames: [5, 6], nextGame: null, roundNo: 3, teamnames: ["", ""]}
        ];

        var winner = ""

        renderBrackets(bracketZ, winner);
    }

    /*
     * Inject our brackets
     */
    function renderBrackets(struct, winner) {
        var groupCount = _.uniq(_.map(struct, function (s) {
            return s.roundNo;
        })).length;

        var group = $('<div class="d-flex flex-row group' + (groupCount + 1) + '" id="b' + bracketCount + '"></div>'),
            grouped = _.groupBy(struct, function (s) {
                return s.roundNo;
            });

        for (g = 1; g <= groupCount; g++) {
            var round = $('<div class="r' + g + '"></div>');
            _.each(grouped[g], function (gg) {
                if (gg.bye)
                    round.append('<div></div>');
                else
                    round.append('<div><div class="bracketbox"><span class="info">' + gg.bracketNo + '</span><span class="teama">' + gg.teamnames[0] + '</span><span class="teamb">' + gg.teamnames[1] + '</span></div></div>');
            });
            group.append(round);
        }
        group.append('<div class="p-2 r' + (groupCount + 1) + '"><div class="final"><div class="bracketbox"><span class="teamc">' + winner + '</span></div></div></div>');
        $('#brackets').append(group);

        bracketCount++;
        // $('html,body').animate({
        //     scrollTop: $("#b" + (bracketCount - 1)).offset().top
        // });
    }

    $('#add').on('click', function () {
        var opts = parseInt(prompt('Bracket size (number of teams):', 32));

        if (!_.isNaN(opts) && opts <= _.last(knownBrackets))
            getBracket(opts);
        else
            alert('The bracket size you specified is not currently supported.');
    });

});