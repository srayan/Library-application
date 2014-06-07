<?php

  //Session Initialization
  if(isset($_SESSION)) {
    session_start();  
  }
  
  //Include functions.php
  require 'functions.php';

  //Global
  $script_file = '';
  $fine_card_no = '';
  $paid_amount = 0;
  $total_fine = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  
  <title>Book Worm - Pay Dues</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

  <style type="text/css">



  </style>

</head>
<body>

  <!-- Error & Suggestion hidden mark-up -->
  <div id="myModal" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Please enter an amount to proceed.</p>
  </div>

  <div class="row">
    <div class="leftSide four columns">&nbsp;</div>
  


    <div class="report four columns">

        <?php
          //Payment processing
          if(!empty($_POST['pay_action'])) {
            $total_fine = $_POST['fine_amount'];
            $card_no = $_POST['fine_card_no']; 

          }

          //Calling the update_dues()
          if( !empty($_POST['paid_action']) ) {
            $paid_amount = trim($_POST['paid_amount']);
            $card_no = $_POST['fine_card_no'];
            $total_fine = $_POST['fine_amount'];


            if($paid_amount == '') {

                $script_file = "<script>
                                    $(document).ready(function(){
                                          $('#myModal').reveal({
                                               animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                               animationspeed: 300,                       //how fast animtions are
                                               closeonbackgroundclick: true,              //if you click background will modal close?
                                               dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                          });     
                                    });
                                  </script>";

            } else {
              
              $results_update_dues = update_dues($paid_amount, $card_no);

              if($results_update_dues[0] = "Success") {

                  $script_file = "<script>
                                      $(document).ready(function(){
                                            $('#myModal2').reveal({
                                                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                                 animationspeed: 300,                       //how fast animtions are
                                                 closeonbackgroundclick: true,              //if you click background will modal close?
                                                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                            });     
                                      });
                                    </script>";

                  header('Refresh: 3; url=http://localhost/Application/fines.php');

                  //header('Refresh: 3; url=http://localhost/Application/fines.php?payment=pay_success&amount='.$results_update_dues[1]);
              }

            }
          }

        ?>

        <div style="text-align: center; font-family: consolas;">
          
            <img src="_images/book_worm.png"><br><br>
            <b style="font-size: 18px;">Book Worm Dues Payment</b>
          
            <hr>

            Your total due is <b>$<?php echo $total_fine; ?>.00</b>

            <hr>

            <div >
              <form class="form1" method="POST" action="dues_processing.php" style="padding-left: 0px;">
                  You will be paying <br><br><input type="text" name="paid_amount" style="width: 50px; margin: 0 auto; padding-left: 0px; text-align: center;">
                  <?php echo '<input type="hidden" name="fine_card_no" value=' . $card_no . '>'; ?>
                  <?php echo '<input type="hidden" name="fine_amount" value=' . $total_fine . '>'; ?>

                  <input type="submit" name="paid_action" value="Pay" title="Click to pay your dues!">
              </form> 

            </div>

        </div>

    </div>




    <div class="rightSide four columns"></div>
  </div>

  <!-- Success -->
  <div id="myModal2" class="reveal-modal" style="color: green; text-align: center;">
     <p>We have successfully received your payment of <b style="color: SlateGray;">$<?php echo $paid_amount; ?>.00</b></p>
     <img src="_images/ajax-loader.gif" height="25px" width="25px">
     <p style="color: SlateGray;">Redirecting to Fines &amp; Dues now!</p>
  </div>

  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>

</body>
</html>
