<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mon application ConceptNet</title>
</head>

<body>
    <div id="main">
        <h1>Mon application ConceptNet</h1>
        <p>Bienvenue sur l'application ConceptNet. Pour obtenir de l'aide, veuillez consulter la page <a href="#/help">#/help</a>.</p>
    </div>
    <script type="text/javascript">
        function fetchFacts(url, callback) {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var faits = response.edges;
                    var nextUrl = response.view && response.view.nextPage;

                    callback(faits, nextUrl);
                },
                error: function() {
                    callback([], null);
                }
            });
        }

        function displayFacts(faits) {
            var tableHtml = '<table>';
            tableHtml += '<tr><th>Concept de départ</th><th>Relation</th><th>Concept de fin</th></tr>';

            faits.forEach(function(fait) {
                tableHtml += '<tr>';
                tableHtml += '<td>' + fait.start.label + '</td>';
                tableHtml += '<td>' + fait.rel.label + '</td>';
                tableHtml += '<td>' + fait.end.label + '</td>';
                tableHtml += '</tr>';
            });

            tableHtml += '</table>';

            $('#main').html(tableHtml);
        }

        const app = $.sammy("#main", function() {
            this.get("#/help", function() {
                const helpHtml = `
                    <h1>Aide</h1>
                    <p>Cette application permet de gérer une base de données de faits et de relations entre ces faits. Les routes suivantes sont disponibles :</p>
                    <ul>
                        <li><a href="#/login">#/login</a> : page de connexion</li>
                        <li><a href="#/logout">#/logout</a> : page de déconnexion</li>
                        <li><a href="#/stats">#/stats</a> : page d'affichage des statistiques de la base de données</li>
                        <li><a href="#/dump/faits">#/dump/faits</a> : page d'affichage des faits de la base de données avec pagination</li>
                        <li><a href="#/concept/:langue/:concept">#/concept/:langue/:concept</a> : affiche une table avec les faits dans ConceptNet qui commencent par le concept :langue/:concept avec pagination</li>
                        <li><a href="#/relation/:relation/from/:langue/:concept">#/relation/:relation/from/:langue/:concept</a> : affiche les faits ( :langue/ :concept, :relation,x) pour tout x nœud end dans ConceptNet avec pagination</li>
                        <li><a href="#/relation/:relation">#/relation/:relation</a> : affiche les faits sans concept de départ spécifié avec pagination</li>
                        <li><a href="#/jeux/quisuisje/:temps/:indice">#/jeux/quisuisje/:temps/:indice</a> : jeu où le joueur dispose de :temps secondes pour identifier un concept à l'aide d'indices affichés toutes les :indice secondes</li>
                        <li><a href="#/jeux/related/:temps">#/jeux/related/:temps</a> : jeu où un concept est tiré au sort et affiché au joueur qui a alors :temps secondes pour saisir des mots reliés</li>
                    </ul>
                     `;
                $("#main").html(helpHtml);
            });


            this.get("#/login", function() {
                const loginFormHtml = `
                     <h1>Connexion</h1>
                     <form id="login-form">
                         <div>
                         <label for="username">Nom d'utilisateur :</label>
                         <input type="text" id="username" name="username">
                         </div>
                         <div>
                         <label for="password">Mot de passe :</label>
                         <input type="password" id="password" name="password">
                         </div>
                         <div>
                         <button type="submit">Se connecter</button>
                         </div>
                     </form>
                     `;

                $("#main").html(loginFormHtml);

                $("#login-form").on("submit", function(e) {
                    e.preventDefault();
                    const username = $("#username").val();
                    const password = $("#password").val();
                        username,
                        password
                    }, function(response) {
                        console.log(response);
                        if (response === "success") {
                            sessionStorage.setItem("user", username);
                            sammyApp.trigger("loginSuccess");
                        } else {
                            alert("Identifiants invalides");
                        }
                    });
                });
            });

            this.bind("loginSuccess", function() {
                this.redirect("#/stats");
            });

            this.get("#/logout", function() {
                sessionStorage.removeItem("user");
                this.redirect("#/login");
            });

            this.get("#/stats", function() {
                if (!sessionStorage.getItem("user")) {
                    this.redirect("#/login");
                    return;
                }

                const statsHtml = `
                         <h1>Statistiques</h1>
                         <ul>
                         <li>Nombre de concepts différents : <span id="nb-concepts"></span></li>
                         <li>Nombre de relations différentes : <span id="nb-relations"></span></li>
                         <li>Nombre de faits dans la base : <span id="nb-facts"></span></li>
                         <li>Nombre d'utilisateurs : <span id="nb-users"></span></li>
                         </ul>
                         `;

                $.ajax({
                    url: "./database/get_stats.php",
                    success: function(data) {
                        $("#main").html(statsHtml);
                        $("#nb-concepts").text(data.nb_concepts);
                        $("#nb-relations").text(data.nb_relations);
                        $("#nb-facts").text(data.nb_facts);
                        $("#nb-users").text(data.nb_users);
                    }.bind(this),
                });
            });

            this.get("#/dump/faits/", function() {
                if (!sessionStorage.getItem("user")) {
                    this.redirect("#/login");
                }
                const factsTableHtml = `
                         <div id="content">
                         <table id="factsTable" class="display">
                             <thead>
                             <tr>
                                 <th>Column 1</th>
                                 <th>Column 2</th>
                             </tr>
                             </thead>
                             <tbody>
                             <tr>
                                 <td>Row 1 Data 1</td>
                                 <td>Row 1 Data 2</td>
                             </tr>
                             <tr>
                                 <td>Row 2 Data 1</td>
                                 <td>Row 2 Data 2</td>
                             </tr>
                             </tbody>
                         </table>
                         </div>
                         `;

                $("#main").html(factsTableHtml);
                $("#factsTable").DataTable({
                    ajax: {
                        url: "./database/get_facts.php",
                        dataSrc: "",
                    },
                    columns: [{
                            data: "start_concept"
                        },
                        {
                            data: "relation"
                        },
                        {
                            data: "end_concept"
                        },
                    ],
                });
            });
            this.get('#/concept/:langue/:concept', function() {
                var langue = this.params['langue'];
                var concept = this.params['concept'];


                fetchFacts(url, function(faits, nextUrl) {
                    displayFacts(faits);

                    if (nextUrl) {
                        $('#main').append('<button id="load-more">Charger plus</button>');
                        $('#load-more').click(function() {
                            loadMoreFacts(nextUrl);
                        });
                    }
                });

                fetchFacts();
            });

            this.get('#/relation/:relation/from/:langue/:concept', function() {
                var relation = this.params['relation'];
                var langue = this.params['langue'];
                var concept = this.params['concept'];


                fetchFacts(url, function(faits, nextUrl) {
                    displayFacts(faits);

                    if (nextUrl) {
                        $('#main').append('<button id="load-more">Charger plus</button>');
                        $('#load-more').click(function() {
                            loadMoreFacts(nextUrl);
                        });
                    }
                });

                fetchFacts();
            });

            this.get('#/relation/:relation', function() {
                var relation = this.params['relation'];


                fetchFacts(url, function(faits, nextUrl) {
                    displayFacts(faits);

                    if (nextUrl) {
                        $('#main').append('<button id="load-more">Charger plus</button>');
                        $('#load-more').click(function() {
                            loadMoreFacts(nextUrl);
                        });
                    }
                });

                fetchFacts();
            });

            function startTimer(duration, display) {
                var timer = duration,
                    minutes, seconds;
                var interval = setInterval(function() {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.text(minutes + ":" + seconds);

                    if (--timer < 0) {
                        clearInterval(interval);
                        display.text("Temps écoulé !");
                        callback();
                    }
                }, 1000);
            }

            this.get('#/scores', function() {
                var scores = JSON.parse(localStorage.getItem('scores')) || [];

                scores.sort(function(a, b) {
                    return b.score - a.score;
                });

                var scoresList = $('#scores-list');
                scoresList.empty();
                scores.forEach(function(scoreEntry) {
                    var listItem = $('<li>').text(scoreEntry.name + ' : ' + scoreEntry.score);
                    scoresList.append(listItem);
                });

                $('#scores-page').show();

                $('#play-again').on('click', function() {
                    window.location.href = '#/jeux/quisuisje';
                });
            });

            this.get('#/jeux/quisuisje/:temps/:indice', function() {
                var temps = parseInt(this.params['temps']) || 60;
                var indice = parseInt(this.params['indice']) || 10;



                fetchFacts(url, function(faits, nextUrl) {
                    var indices = faits.map(function(fait) {
                        return fait.end.label;
                    });

                    indices.sort(function() {
                        return 0.5 - Math.random();
                    });

                    var timer = $('#timer');
                    timer.text(temps + ":00");

                    var terminerJeu = function() {
                        alert('Le temps est écoulé ! Le concept était : ' + concept);

                        var score = Math.max(0, Math.floor((temps - timer) / indice));
                        alert('Votre score final est : ' + score);

                        var scores = JSON.parse(localStorage.getItem('scores')) || [];
                        scores.push({
                            name: 'Joueur',
                            score: score
                        });
                        localStorage.setItem('scores', JSON.stringify(scores));

                        setTimeout(function() {
                            window.location.href = '#/scores';
                        }, 3000);
                    };

                    startTimer(temps, timer, terminerJeu);

                    var indiceActuel = 0;
                    var afficherIndice = function() {
                        if (indiceActuel < indices.length) {
                            $('#indices').append('<p>' + indices[indiceActuel] + '</p>');
                            indiceActuel++;
                            setTimeout(afficherIndice, indice * 1000);
                        }
                    };
                    afficherIndice();

                    $('#reponse-form').on('submit', function(e) {
                        e.preventDefault();
                        var reponse = $('#reponse').val();
                        if (reponse.toLowerCase() === concept.toLowerCase()) {
                            alert('Bravo, vous avez trouvé le concept !');

                            var score = Math.max(0, Math.floor((temps - timer) / indice));
                            alert('Votre score est : ' + score);

                            var scores = JSON.parse(localStorage.getItem('scores')) || [];
                            scores.push({
                                name: 'Joueur',
                                score: score
                            });
                            localStorage.setItem('scores', JSON.stringify(scores));

                            setTimeout(function() {
                                window.location.href = '#/scores';
                            }, 3000);
                        } else {
                            alert('Désolé, ce n\'est pas le bon concept. Continuez à chercher !');
                        }
                    });
                });
            });

            this.get('#/jeux/related/:temps', function() {
                var temps = parseInt(this.params['temps']) || 60;


                $('#concept').text(concept);
                var timer = $('#timer');
                timer.text(temps + ":00");
                var terminerJeu = function() {
                    alert('Le temps est écoulé !');

                    var motsSaisis = $('#reponse').val().split(',').map(function(mot) {
                        return mot.trim().toLowerCase();
                    });


                    fetchFacts(url, function(faits, nextUrl) {
                        var motsRelies = faits.map(function(fait) {
                            return fait.end.label.toLowerCase();
                        });

                        var motsTrouves = motsSaisis.filter(function(mot) {
                            return motsRelies.includes(mot);
                        });

                        var score = motsTrouves.length;
                        alert('Votre score est : ' + score);

                        var motsTrouvesHtml = motsTrouves.map(function(mot) {
                            return '<span class="found">' + mot + '</span>';
                        }).join(', ');

                        var motsNonTrouvesHtml = motsSaisis.filter(function(mot) {
                            return !motsTrouves.includes(mot);
                        }).map(function(mot) {
                            return '<span class="not-found">' + mot + '</span>';
                        }).join(', ');

                        $('#resultats').html('<p>Mots trouvés : ' + motsTrouvesHtml + '</p><p>Mots non trouvés : ' + motsNonTrouvesHtml + '</p>');

                        var scores = JSON.parse(localStorage.getItem('scores')) || [];
                        scores.push({
                            name: 'Joueur',
                            score: score
                        });
                        localStorage.setItem('scores', JSON.stringify(scores));

                        setTimeout(function() {
                            window.location.href = '#/scores';
                        }, 5000);
                    });
                };

                startTimer(temps, timer, terminerJeu);

                $('#reponse-form').on('submit', function(e) {
                    e.preventDefault();
                    terminerJeu();
                });
            });
    </script>
</body>

</html>