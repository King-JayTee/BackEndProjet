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

function loadMoreFacts(nextUrl) {
  if (nextUrl) {
    fetchFacts(nextUrl, function(faits, nextUrl) {
      displayFacts(faits);
      
      if (nextUrl) {
        $('#main').append('<button id="load-more">Charger plus</button>');
        $('#load-more').click(function() {
          loadMoreFacts(nextUrl);
        });
      }
    });
  }
}


//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

// Définition de l'application Sammy.js
const app = $.sammy("#main", function () {
  this.get("#/help", function () {
    const helpHtml = `
      <h1>Aide</h1>
      <p>Cette application permet de gérer une base de données de faits et de relations entre ces faits. Les routes suivantes sont disponibles :</p>
      <ul>
        <li><a href="#/login">#/login</a> : page de connexion</li>
        <li><a href="#/logout">#/logout</a> : page de déconnexion</li>
        <li><a href="#/stats">#/stats</a> : page d'affichage des statistiques de la base de données</li>
        <li><a href="#/dump/faits/1">#/dump/faits/1</a> : page d'affichage des faits de la base de données avec pagination</li>
      </ul>
    `;

    this.get("#/help", function () {
      $("#main").html(helpHtml);
    });
  });

  this.get("#/login", function () {
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

    $("#login-form").on("submit", function (e) {
      e.preventDefault();
      const username = $("#username").val();
      const password = $("#password").val();
      $.post("src/auth/login.php", { username, password }, function (response) {
        if (response === "success") {
          sessionStorage.setItem("user", username);
          sammyApp.trigger("loginSuccess");
        } else {
          alert("Identifiants invalides");
        }
      });
    });
  });

  $("#login-form").on("submit", function (e) {
    e.preventDefault();
    const username = $("#username").val();
    const password = $("#password").val();

    $.post("src/auth/login.php", { username, password }, function (response) {
      if (response === "success") {
        sessionStorage.setItem("user", username);
        sammyApp.trigger("loginSuccess");
      } else {
        alert("Identifiants invalides");
      }
    });
  });

  this.bind("loginSuccess", function () {
    this.redirect("#/stats");
  });

  this.get("#/logout", function () {
    sessionStorage.removeItem("user");
    this.redirect("#/login");
  });

  this.get("#/stats", function () {
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
      url: "src/database/get_stats.php",
      success: function (data) {
        $("#main").html(statsHtml);
        $("#nb-concepts").text(data.nb_concepts);
        $("#nb-relations").text(data.nb_relations);
        $("#nb-facts").text(data.nb_facts);
        $("#nb-users").text(data.nb_users);
      }.bind(this),
    });
  });

  this.get("#/dump/faits/", function () {
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
        url: "src/database/get_facts.php",
        dataSrc: "",
      },
      columns: [
        { data: "start_concept" },
        { data: "relation" },
        { data: "end_concept" },
      ],
    });
  });
}).run();


//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
// pour #/concept/:langue/:concept
this.get('#/concept/:langue/:concept', function() {
  var langue = this.params['langue'];
  var concept = this.params['concept'];
  
  var url = 'http://api.conceptnet.io/query?node=/c/' + langue + '/' + concept + '&limit=10';
  
  fetchFacts(url, function(faits, nextUrl) {
    displayFacts(faits);
    
    if (nextUrl) {
      $('#main').append('<button id="load-more">Charger plus</button>');
      $('#load-more').click(function() {
        loadMoreFacts(nextUrl);
      });
    }
  });
});

//||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

this.get('#/relation/:relation/from/:langue/:concept', function() {
  var relation = this.params['relation'];
  var langue = this.params['langue'];
  var concept = this.params['concept'];

  var url = 'http://api.conceptnet.io/query?start=/c/' + langue + '/' + concept + '&rel=/r/' + relation + '&limit=10';

  fetchFacts(url, function(faits, nextUrl) {
    displayFacts(faits);

    if (nextUrl) {
      $('#main').append('<button id="load-more">Charger plus</button>');
      $('#load-more').click(function() {
        loadMoreFacts(nextUrl);
      });
    }
  });
});
//|||||||||||||||||||||||||||||||||||||||||||||||||//|||||||||||||||||||||||||||||||||||||||||||||||||//||||||||||||||||||||||||||||||||

this.get('#/relation/:relation', function() {
  var relation = this.params['relation'];

  var url = 'http://api.conceptnet.io/query?rel=/r/' + relation + '&limit=10';

  fetchFacts(url, function(faits, nextUrl) {
    displayFacts(faits);

    if (nextUrl) {
      $('#main').append('<button id="load-more">Charger plus</button>');
      $('#load-more').click(function() {
        loadMoreFacts(nextUrl);
      });
    }
  });
});

//||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

function startTimer(duration, display) {
  var timer = duration, minutes, seconds;
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

// Route pour afficher la page des scores
this.get('#/scores', function() {
  // Récupérer les scores depuis le stockage local ou une base de données
  var scores = JSON.parse(localStorage.getItem('scores')) || [];

  // Trier les scores par ordre décroissant
  scores.sort(function(a, b) {
    return b.score - a.score;
  });

  // Afficher les scores dans la liste
  var scoresList = $('#scores-list');
  scoresList.empty();
  scores.forEach(function(scoreEntry) {
    var listItem = $('<li>').text(scoreEntry.name + ' : ' + scoreEntry.score);
    scoresList.append(listItem);
  });

  // Afficher la page des scores
  $('#scores-page').show();

  // Gérer le clic sur le bouton "Rejouer"
  $('#play-again').on('click', function() {
    window.location.href = '#/jeux/quisuisje';
  });
});

// Route pour le jeu "Qui suis-je ?" (⋆)
this.get('#/jeux/quisuisje/:temps/:indice', function() {
  var temps = parseInt(this.params['temps']) || 60;
  var indice = parseInt(this.params['indice']) || 10;

  // Sélectionner un concept aléatoire depuis la base de données
  var concept = 'chat'; // Remplacez par votre logique de sélection de concept

  // Récupérer les indices depuis ConceptNet
  var url = 'http://api.conceptnet.io/query?node=/c/fr/' + concept + '&rel=/r/IsA&limit=10';

  fetchFacts(url, function(faits, nextUrl) {
    var indices = faits.map(function(fait) {
      return fait.end.label;
    });

    // Mélanger les indices de manière aléatoire
    indices.sort(function() { return 0.5 - Math.random(); });

    // Afficher le chronomètre
    var timer = $('#timer');
    timer.text(temps + ":00");

    // Fonction pour terminer le jeu
    var terminerJeu = function() {
      $('#reponse-form').off('submit'); // Désactiver la soumission du formulaire
      $('#reponse').prop('disabled', true); // Désactiver le champ de réponse
      alert('Le temps est écoulé ! Le concept était : ' + concept);
      
      // Calculer le score final
      var score = Math.max(0, Math.floor((temps - timer) / indice));
      alert('Votre score final est : ' + score);
      
      // Enregistrer le score
      var scores = JSON.parse(localStorage.getItem('scores')) || [];
      scores.push({ name: 'Joueur', score: score });
      localStorage.setItem('scores', JSON.stringify(scores));
      
      // Rediriger vers la page des scores après 3 secondes
      setTimeout(function() {
        window.location.href = '#/scores';
      }, 3000);
    };

    startTimer(temps, timer, terminerJeu);

    // Afficher les indices progressivement
    var indiceActuel = 0;
    var afficherIndice = function() {
      if (indiceActuel < indices.length) {
        $('#indices').append('<p>' + indices[indiceActuel] + '</p>');
        indiceActuel++;
        setTimeout(afficherIndice, indice * 1000);
      }
    };
    afficherIndice();

    // Gérer la soumission de la réponse
    $('#reponse-form').on('submit', function(e) {
      e.preventDefault();
      var reponse = $('#reponse').val();
      if (reponse.toLowerCase() === concept.toLowerCase()) {
        clearInterval(interval); // Arrêter le chronomètre
        alert('Bravo, vous avez trouvé le concept !');
        
        // Calculer le score
        var score = Math.max(0, Math.floor((temps - timer) / indice));
        alert('Votre score est : ' + score);
        
        // Enregistrer le score
        var scores = JSON.parse(localStorage.getItem('scores')) || [];
        scores.push({ name: 'Joueur', score: score });
        localStorage.setItem('scores', JSON.stringify(scores));
        
        // Rediriger vers la page des scores après 3 secondes
        setTimeout(function() {
          window.location.href = '#/scores';
        }, 3000);
      } else {
        alert('Désolé, ce n\'est pas le bon concept. Continuez à chercher !');
      }
    });
  });
});
//|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

// Route pour le jeu "Mot relié" 
this.get('#/jeux/related/:temps', function() {
  var temps = parseInt(this.params['temps']) || 60;

  // Sélectionner un concept aléatoire depuis la base de données
  var concept = 'chien'; // Remplacez par votre logique de sélection de concept

  // Afficher le concept et le chronomètre
  $('#concept').text(concept);
  var timer = $('#timer');
  timer.text(temps + ":00");

  // Fonction pour terminer le jeu
  var terminerJeu = function() {
    $('#reponse-form').off('submit'); // Désactiver la soumission du formulaire
    $('#reponse').prop('disabled', true); // Désactiver le champ de réponse
    alert('Le temps est écoulé !');
    
    // Récupérer les mots saisis par le joueur
    var motsSaisis = $('#reponse').val().split(',').map(function(mot) {
      return mot.trim().toLowerCase();
    });
    
    // Récupérer les mots reliés depuis ConceptNet
    var url = 'http://api.conceptnet.io/query?node=/c/fr/' + concept + '&rel=/r/RelatedTo&limit=10';
    
    fetchFacts(url, function(faits, nextUrl) {
      var motsRelies = faits.map(function(fait) {
        return fait.end.label.toLowerCase();
      });
      
      // Comparer les mots saisis avec les mots reliés
      var motsTrouves = motsSaisis.filter(function(mot) {
        return motsRelies.includes(mot);
      });
      
      // Calculer le score
      var score = motsTrouves.length;
      alert('Votre score est : ' + score);
      
      // Afficher les mots trouvés et les mots non trouvés
      var motsTrouvesHtml = motsTrouves.map(function(mot) {
        return '<span class="found">' + mot + '</span>';
      }).join(', ');
      
      var motsNonTrouvesHtml = motsSaisis.filter(function(mot) {
        return !motsTrouves.includes(mot);
      }).map(function(mot) {
        return '<span class="not-found">' + mot + '</span>';
      }).join(', ');
      
      $('#resultats').html('<p>Mots trouvés : ' + motsTrouvesHtml + '</p><p>Mots non trouvés : ' + motsNonTrouvesHtml + '</p>');
      
      // Enregistrer le score
      var scores = JSON.parse(localStorage.getItem('scores')) || [];
      scores.push({ name: 'Joueur', score: score });
      localStorage.setItem('scores', JSON.stringify(scores));
      
      // Rediriger vers la page des scores après 5 secondes
      setTimeout(function() {
        window.location.href = '#/scores';
      }, 5000);
    });
  };

  startTimer(temps, timer, terminerJeu);

  // Gérer la soumission de la réponse
  $('#reponse-form').on('submit', function(e) {
    e.preventDefault();
    terminerJeu();
  });
});