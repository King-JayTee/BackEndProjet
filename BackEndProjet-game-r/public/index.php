<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mon application ConceptNet</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.4/css/dataTables.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sammy.js/0.7.6/sammy.min.js"></script>
</head>
<body>
    <div id="main"></div>
    <script src="app.js"></script>
    <div id="scores-page">
  <h2>Page des scores</h2>
  <ul id="scores-list"></ul>
  <button id="play-again">Rejouer</button>
</div>
    
    <div id="jeu-quisuisje">
        <h2>Jeu : Qui suis-je ?</h2>
        <p>Temps restant : <span id="timer"></span></p>
        <div id="indices"></div>
        <form id="reponse-form">
          <input type="text" id="reponse" placeholder="Entrez votre réponse">
          <button type="submit">Valider</button>
        </form>
      </div>
    <div id="jeu-related">
  <h2>Jeu : Mot relié</h2>
  <p>Concept : <span id="concept"></span></p>
  <p>Temps restant : <span id="timer"></span></p>
  <form id="reponse-form">
    <input type="text" id="reponse" placeholder="Entrez les mots reliés séparés par des virgules">
    <button type="submit">Valider</button>
  </form>
  <div id="resultats"></div>
</div>
</body>
</html>