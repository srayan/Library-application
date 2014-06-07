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
  
  <title>Book Worm - Fines &amp; Dues</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

  <style type="text/css">
    #duesBlock {
      background-color: #fff;
      border-radius: 10px;
      font-family: consolas;

      width: 100%;

      padding-top: 20px;
      padding-left: 20px;
      padding-right: 20px;
      padding-bottom: 10px;
      
      box-shadow: 1px 1px 10px #ccc;
    }

    #pay_button:disabled {
      background-color: rgba(38,128,255,0.4);
      color: white;
      cursor: auto;
    }

  </style>

</head>
<body>

  <!-- Error & Suggestion hidden mark-up -->
  <div id="myModal" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Field <b style="color: SlateGray;">Card No</b> is required, please enter to proceed.</p>
  </div>

  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>Fines &amp; Dues</h2>
        <p>View fine amount and dues of borrowers searching on their <b>Card No</b>.</p>

        
          <form class="form1" method="POST" action="fines.php">
            
            <input type="text" name="fines_card_no" id="fines_input1" placeholder="Card No"  title="Please enter the Card No">

            <input type="submit" id="submitButton" data-reveal-id="myModal" value="Calculate" name="search_action" title="Click to calculate dues!">
            <input type="reset" value="Reset" name="search_reset"> 
          </form>

          <br><br>


          <?php

            //Retrieving input value
            if(!empty($_POST)) {

                //Extracting input field values
                $card_no = trim($_POST['fines_card_no']);
                

                //Blank field modal
                if($card_no == '') {

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

                    //Calling function to execute the query
                    $results_fines = fines($card_no);

                    if($results_fines == 'card_unavailable') {

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

                    } else if(count($results_fines) > 0) {

                      //displaying the dues info
          ?>
                      <div id='duesBlock'>
                        <div style="font-weight: bold; font-size: 24px; text-align: center;">Dues Report</div>
                        <hr>
                      
                        <div style="padding-left: 50px;">
                          <span style="color: SlateGray;">Card No: </span> <span><?php echo $card_no; ?></span><br>
                          <span style="color: SlateGray;">Name: </span> <span><?php echo $results_fines[0]['fname'] . " " . $results_fines[0]['lname']; ?></span><br>
                          <span style="color: SlateGray;">Address: </span> <span><?php echo $results_fines[0]['address']; ?></span><br>
                        </div>

                        <hr>

                        <div style="text-align: right; padding-right: 50px;">
                          You have a due of <b>$<?php echo $results_fines[0]['dues']; ?>.00</b>
                          <form class="form1" method="POST" action="dues_processing.php">
                            <?php 
                            //Button
                              $fine_amount = $results_fines[0]['dues'];

                              if($fine_amount == 0) { 
                                $disable_status = "disabled";
                              } else {
                                $disable_status = "";
                              }
                            ?>
                            <?php echo '<input type="hidden" name="fine_amount" value=' . $fine_amount . '>'; ?>
                            <?php echo '<input type="hidden" name="fine_card_no" value=' . $card_no . '>'; ?>
                            <?php echo '<input type="submit" id="pay_button" data-reveal-id="myModal" value="Pay Now" name="pay_action"' . " " . $disable_status . '>'; ?>  
                          </form> 
                        </div>
                      
                      </div>

          <?php
                    }
                }
            }

          ?>

          <?php

            if( !empty($_GET) ) {
              $payment_status_message = $_GET['payment'];
              $payment_amount = $_GET['amount'];

              if($payment_status_message == "pay_success") {

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
              }
            }

          ?>



          
      
        
    </article>

  </div>

  <!-- Card No unavailable modal -->
  <div id="myModal2" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Borrower with Card No: <b style="color: SlateGray;"><?php echo $card_no; ?></b> is not registered.</p>
  </div>

  <!-- Redirected payment success -->
  <div id="myModal3 " class="reveal-modal" style="color: green; text-align: center;">
     <p>We have successfully received your payment of <b style="color: SlateGray;">$<?php echo $payment_amount; ?>.00</b></p>
  </div>

  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>

</body>
</html>
