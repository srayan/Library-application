<?php

    //Session Initialization
  if(isset($_SESSION)) {
    session_start();  
  }
  
  //Include functions.php
  require 'functions.php';

  //Globals
  $script_file = '';
  $display_card_no = '';

?>


<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Book Worm - Add Borrower</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

</head>
<body>

  <!-- Error & Suggestion hidden mark-up -->
  <div id="myModal3" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Fields cannot be left blank, all fields are required!.</p>
  </div>

  <div id="myModal4" class="reveal-modal" style="color: OrangeRed; text-align: center;">
    <p>Duplicate borrower cannot be added!
      <div style="color: SlateGray;">A borrower already exixts for the info provided.</div>
    </p>
  </div>

  <div id="myModal6" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The operation failed due to technical issues!</p>
  </div>


  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>Add Borrower</h2>
        <p>Add a <b>Borrower</b> and assign a <b>Card-No</b>.</p>

        <form class="form1" method="POST" action="add_borrower.php">
            <input type="text" name="add_fname" placeholder="First Name" title="Please enter the first name">
            <input type="text" name="add_lname" placeholder="Last Name" title="Please enter the last name">
            <textarea name="add_address" placeholder="Address" title="Please enter the address"></textarea>


            <input type="submit" value="Add" name="searchAction" title="Click to add borrower!">
            <input type="reset" value="Reset" name="searchReset"> 
        </form>
        
    </article>

    <?php

      //Retrieving input values and and trimming leading & ending whitespaces off
      if(!empty($_POST)) {

          //Extracting input field values
          $fname = trim($_POST['add_fname']);
          $lname = trim($_POST['add_lname']);
          $prior_address = trim($_POST['add_address']);
          $address = preg_replace( "/[\r\n]{2,}/", ", ", $prior_address );

          //Blank field modal
          if( (($fname == '') && ($lname == '') && ($address == '')) || (($fname == '') || ($lname == '') || ($address == '')) ) {
            $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal3').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";

          } else {

            //Calling function to execute the query to add the borrower information
            $results_add_borrower = add_borrower($fname, $lname, $address);

            switch ($results_add_borrower[0]) {
               case 'exists':

                  //A borrower already exists for given details
                  $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal4').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";
                 break;
               
               case 'success':

                  //Seting the card-no for display
                  global $display_card_no;
                  $display_card_no = $results_add_borrower[1];

                  //Borrower successfully added
                  $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal5').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";
                 break;

                 case 'fail':

                  //Insert query failed
                  $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal6').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";
                 break;
             } 

          }
        }
    ?>

    <!-- Modal for showing success & card no -->
    <div id="myModal5" class="reveal-modal" style="color: green; text-align: center;">
      <p>Borrower successfully added!
        <div style="color: SlateGray;">Borrower's Card-No: <b><?php echo $GLOBALS['display_card_no']; ?></b></div>
      </p>
    </div>

  </div> <!-- End of Main Content -->
  
  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>

</body>
</html>
