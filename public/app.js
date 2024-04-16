// Définition de l'application Sammy.js
const app = Sammy("#content", function () {
  this.get("#/help", function () {
    this.load("templates/help.html");
  });

  this.get("#/login", function () {
    this.load(
      "templates/login.html",
      function () {
        $("#login-form").on(
          "submit",
          function (e) {
            e.preventDefault();
            const username = $("#username").val();
            const password = $("#password").val();
            if (username === "ift3225" && password === "5223tfi") {
              sessionStorage.setItem("user", username);
              this.trigger("loginSuccess");
            } else {
              alert("Identifiants invalides");
            }
          }.bind(this)
        );
      }.bind(this)
    );
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
    $.ajax({
      url: "src/database/get_stats.php",
      success: function (data) {
        this.load("templates/stats.html", function () {
          $("#nb-concepts").text(data.nbConcepts);
          $("#nb-relations").text(data.nbRelations);
          $("#nb-facts").text(data.nbFacts);
          $("#nb-users").text(data.nbUsers);
        });
      }.bind(this),
    });
  });

  this.get('#/dump/faits/:page', function() {
    if (!sessionStorage.getItem('user')) {
      this.redirect('#/login');
      return;
    }
  
    const page = parseInt(this.params['page']) || 1;
    const getFaits = () => {
      $.ajax({
        url: `src/database/get_facts.php?page=${page}`,
        success: data => {
          this.load('templates/dump.html', () => displayData(data));
        }
      });
    };
  
    const displayData = data => {
      const table = $('#faits-table tbody');
      const pagination = $('#pagination');
  
      data.faits.forEach(fait => {
        const row = $('<tr></tr>');
        $('<td></td>').text(fait.id).appendTo(row);
        $('<td></td>').text(fait.description).appendTo(row);
        table.append(row);
      });
  
      displayPagination(page, data.hasNextPage, pagination);
    };
  
    const displayPagination = (page, hasNextPage, pagination) => {
      if (page > 1) {
        const prevLink = $('<a></a>');
        prevLink.attr('href', `#/dump/faits/${page - 1}`);
        prevLink.text('Précédent');
        pagination.append(prevLink);
      }
  
      if (hasNextPage) {
        const nextLink = $('<a></a>');
        nextLink.attr('href', `#/dump/faits/${page + 1}`);
        nextLink.text('Suivant');
        pagination.append(nextLink);
      }
    };
  
    getFaits();
  });
});

$(function () {
  app.run("#/");
});
