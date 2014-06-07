<?php

    //Session Initialization
    if(isset($_SESSION)) {
      session_start();  
    }
    
    //Include functions.php
    require 'functions.php';

    //Globals
    $script_file = '';
    $display_book_id = '';
    $display_card_no = '';
    $display_branch_id = '';
    $display_due_date_year = '';
    $display_due_date_month = '';
    $display_due_date_day = '';

    //Due date
    $due_date = date_create(date("Y-m-d"));
    date_add($due_date, date_interval_create_from_date_string('14 days'));

?>

<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Book Worm - Checkout</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

  
</head>
<body>

  <!-- Error & Suggestion hidden mark-up -->
  <!-- All fields required modal -->
  <div id="myModal1" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Fields cannot be left blank, all fields are required!.</p>
  </div>

  <!-- Max checkouts by a borrower -->
  <div id="myModal5" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>You have reached maximum checkout limit!
      <div style="color: SlateGray;">Please checkin your previous books in order to borrow new ones.</div></p>
  </div>

  <!-- Successful checkout modal -->
  <div id="myModal6" class="reveal-modal" style="color: green; text-align: center;">
     <p>You have successfully checked out the book!
      <div style="color: SlateGray;">Please note the due date: <b style="color: OrangeRed;"><?php echo date_format($due_date, 'Y-m-d'); ?></b> </div></p>
  </div>

  <!-- Query failed modal -->
  <div id="myModal7" class="reveal-modal" style="color: green; text-align: center;">
     <p>Checkout failed, please try again!</p>
  </div>



  

  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>Check Out</h2>
        <p>Checkout your books by <b>Book ID</b>, <b>Branch ID</b> of the book and <b>Borrower's Card-No</b>.</p>

        <form class="form1" method="POST" action="checkout.php">
            <input type="text" name="checkout_book_id" placeholder="Book ID"  title="Please enter the Book ID" >
            <input type="text" name="checkout_branch_id" placeholder="Branch ID"  title="Please enter the Branch ID">
            <input type="text" name="checkout_card_no" placeholder="Card No"  title="Please enter Borrower's Card No">


            <input type="submit" value="Check Out" name="searchAction" title="Click to Check Out!">
             
        </form>
        
    </article>

<?php

      //Retrieving input values and trimming leading & ending whitespaces off
      if(!empty($_POST)) {

          //Extracting input field values
          $book_id = trim($_POST['checkout_book_id']);
          $branch_id = trim($_POST['checkout_branch_id']);
          $card_no = trim($_POST['checkout_card_no']);

          //Blank field modal
          if( (($book_id == '') && ($branch_id == '') && ($card_no == '')) || (($book_id == '') || ($branch_id == '') || ($card_no == '')) ) {
            $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal1').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";

          } else {

            //Calling the checkout() function
            $checkout_status = checkout_book($book_id, $branch_id, $card_no);

            switch($checkout_status[0]) {
              
              //Book unavailable
              case 'book_unavailable':

                //Setting book_id for display
                $GLOBALS['display_book_id'] = $checkout_status[1];

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

              break;

              //Borrower unavailable
              case 'borrower_unavailable':

                //Setting card_no for display
                $GLOBALS['display_card_no'] = $checkout_status[1];

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
              break;

              //Branch unavailable
              case 'branch_unavailable':

                //Setting branch no for display
                $GLOBALS['display_branch_id'] = $checkout_status[1];

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

              //Maximum checkouts reached by the borrower
              case 'max_checkouts':

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

              //Success
              case 'success':

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

              //Failure
              case 'failure':

                $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal7').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";
              break;

              //Not found at specified branch, suggest branches where available
              case 'not_found_at_branch':

                $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal8').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";
              break;

              //Duplicate book loan entry
              case 'duplicate_book_entry':

              //Setting display variables
              $GLOBALS['display_book_id'] = $checkout_status[1];
              $GLOBALS['display_branch_id'] = $checkout_status[2];
              $GLOBALS['display_card_no'] = $checkout_status[3];
              $GLOBALS['display_due_date_year'] = $checkout_status[4];
              $GLOBALS['display_due_date_month'] = $checkout_status[5];
              $GLOBALS['display_due_date_day'] = $checkout_status[6];


              $script_file = "<script>
                              $(document).ready(function(){
                                    $('#myModal9').reveal({
                                         animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                                         animationspeed: 300,                       //how fast animtions are
                                         closeonbackgroundclick: true,              //if you click background will modal close?
                                         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                                    });     
                              });
                            </script>";
            }

          }
      }
?>

  <!-- Modals with variables -->
  <!-- Book unavailable modal -->
  <div id="myModal2" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The book having Book ID: <b style="color: SlateGray;"><?php echo $display_book_id; ?></b> that you are searching for is not available in our library.</p>
  </div>

  <!-- Borrower unavailable modal -->
  <div id="myModal3" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Borrower with Card No: <b style="color: SlateGray;"><?php echo $display_card_no; ?></b> is not registered.</p>
  </div>

  <!-- Branch unavailable modal -->
  <div id="myModal4" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>We do not have a branch with Branch ID: <b style="color: SlateGray;"><?php echo $display_branch_id; ?></b></p>
  </div>

  <!-- Branch suggestion modal -->
  <div id="myModal8" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The book is unavailable at the branch you selected.</p>
     <hr>
     <div style="color: SlateGray;">You may find them at the following branches: <b style="color: green;">
      <?php 
        if(count($checkout_status) > 1) {
          for($i = 1; $i < count($checkout_status); $i++) {
            echo $checkout_status[$i] . " , ";    
          }
        } else {
          echo "All checked out! Check back later!";
        }
         
      ?></b>
     </div>
  </div>

  <!-- Branch suggestion modal -->
  <div id="myModal9" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>You have already borrowed this book.</p>
     <hr>
     <div style="color: SlateGray;">Your checkout details:
      <?php
         echo "<div style='text-align: left; margin-left: 140px;'>";
         echo "<br>" . "Book ID: " . "<b style='color: green;'>" . $display_book_id . "</b><br>";
         echo "Branch ID: " . "<b style='color: green;'>" . $display_branch_id . "</b><br>";
         echo "Card No: " . "<b style='color: green;'>" . $display_card_no . "</b><br>";
         echo "Due Date: " . "<b style='color: green;'>" . $display_due_date_year . "-" . $display_due_date_month . "-" . $display_due_date_day . "</b>";
         echo "</div>";
      ?>
     </div>
  </div>
    

  </div> <!-- End of Main Content -->
  
  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>

</body>
</html>
