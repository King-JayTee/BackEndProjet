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

