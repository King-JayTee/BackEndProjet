// Définition de l'application Sammy.js
const app = $.sammy("#main", function () {
  this.get("#/help", function () {
    this.load("./templates/help.html");
  });

  this.get("#/login", function () {
    this.load("./templates/login.html");

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
        this.load("./templates/stats.html", function () {
          $("#nb-concepts").text(data.nb_concepts);
          $("#nb-relations").text(data.nb_relations);
          $("#nb-facts").text(data.nb_facts);
          $("#nb-users").text(data.nb_users);
        });
      }.bind(this),
    });
  });

  this.get("#/dump/faits/", function () {
    if (!sessionStorage.getItem("user")) {
      this.redirect("#/login");
    }
    this.load("./templates/facts-table.html");
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
});

$(function () {
  app.run("#/");
});
