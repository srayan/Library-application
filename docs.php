<?php

  //Session Initialization
  if(isset($_SESSION)) {
    session_start();  
  }
  
  //Include functions.php
  require 'functions.php';

  //Global
  $script_file = '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  
  <title>Book Worm - Search Books</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

  <style>

      .manualContatiner {
        background-color: #fff;
        border-radius: 10px;
        width: 100%;

        padding-top: 20px;
        padding-bottom: 20px;

        text-align: center;
        box-shadow: 1px 1px 10px #ccc;
      }

      .manualContatiner embed {
        width: 100%;
        height: 600px;
        padding: none;
      }

  </style>
</head>
<body>


  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>Book Worm Manual</h2>
        <p>Know about our services &amp; rules from the manual below.</p>

        <div class="manualContatiner">
            <embed src="_docs/eLibrary Manual.pdf">
        </div>
    </article>

  </div>

</body>
</html>
