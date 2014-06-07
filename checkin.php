<?php

    //Session Initialization
    if(isset($_SESSION)) {
      session_start();  
    }
    
    //Include functions.php
    require 'functions.php';

    //Globals
    $script_file = '';


?>

<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Book Worm - Checkin</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

  <style>

    #checkin_button {
      background-color: rgb(38,128,255);
      width: 95px;
      height: 30px;
      color: white;
      font-weight: bold;
      border: none;
      margin-left: 2px;
      margin-top: 10px;
      border-radius: 5px;

      -webkit-transition: background-color 0.5s ease-out;
      -moz-transition: background-color 0.5s ease-out;
      -o-transition: background-color 0.5s ease-out;
      -ms-transition: background-color 0.5s ease-out;
      transition: background-color 0.5s ease-out;
    }

    #checkin_button:hover {
      background-color: #aaa;
      color: #333;
      cursor: pointer;
    }

    #checkin_button:disabled {
      background-color: rgba(38,128,255,0.4);
      color: white;
      cursor: auto;
    }


  </style>

  <!-- Checkin Button disable/enable script -->
  <script type="text/javascript">

    EnableSubmit = function(val)
    {
        var button_state = document.getElementById("checkin_button");

        if (val.checked == true)
        {
            button_state.disabled = false;
        }
        else
        {
            button_state.disabled = true;
        }
    } 

  </script>

</head>
<body onload="EnableSubmit(this)">

  <!-- Error & Suggestion hidden mark-up -->
  <!-- book_id & card_no are required modal -->
  <div id="myModal1" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>All fields cannot be left blank, please enter atleast one item!</p>
  </div>

  <!-- name format > 2 modal -->
  <div id="myModal2" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Please follow the format for borrower's name: <hr><div style="color: SlateGray;">Borrower Name: <b>First</b> <b>Last</b></div></p>
  </div>

  <!-- Query failed modal -->
  <div id="myModal3" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The operation failed, please try again!</p>
  </div>


  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>Check In</h2>
        <p>Checkin your books by <b>Book ID</b> of the book, <b>Card-No</b> and <b>Name</b> of the borrower.</p>

        <form class="form1" method="POST" action="checkin.php">
            <input type="text" name="checkin_book_id" placeholder="Book ID" title="Please enter the Book ID">
            <input type="text" name="checkin_card_no" placeholder="Card No" title="Please enter the Borrower's Card No">
            <input type="text" name="checkin_name" placeholder="Borrower's Name" title="Please enter Borrower's Name">


            <input type="submit" value="Get List" name="searchAction" title="Click to get your list!">
             
        </form>
        
    </article>

  </div>

  <!-- Table row -->
  <div class="row">

  <div class="main2 twelve columns">

    <table>
      <thead>
        <tr>
          <th style="width: 15%;">Book ID</th>
          <th style="width: 10%;">Branch ID</th>
          <th style="width: 10%;">Card No</th>
          <th style="width: 25%;">Borrower's Name</th>
          <th style="width: 15%;">Out Date</th>
          <th style="width: 15%;">Due On</th>
          <th style="width: 10%;">Check In</th>
        </tr>
      </thead>

      <?php

        //Retrieving input values and trimming leading & ending whitespaces off
        if(!empty($_POST['searchAction'])) {

            

            //Function calling status
            $function_call_status = 1;

            //Extracting input field values
            $book_id = trim($_POST['checkin_book_id']);
            $card_no = trim($_POST['checkin_card_no']);
            $name = trim($_POST['checkin_name']);

            //Name field check for no. of values
            $name_explode = explode(" ", $name);
            $name_array_size = count($name_explode);

            $name_1 = '';
            $name_2 = '';


            //Blank field check
            if( ($book_id == '') && ($name == '') && ($card_no == '') ) {
              
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

              echo "<tfoot>";
              echo "<td colspan='7'>No content to display, you haven't run a query yet!</td>";
              echo "</tfoot>";

              //To avoid querying since modal is displayed
              $function_call_status = 0;

            } else if($name_array_size > 2) {

                //Name format modal
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

                echo "<tfoot>";
                echo "<td colspan='7'>No content to display, you haven't run a query yet!</td>";
                echo "</tfoot>";

                //Setting variable to check function call
                $function_call_status = 0;

            } else if($name_array_size == 2) {

                //Setting fname and lname
                $name_1 = $name_explode[0];
                $name_2 = $name_explode[1];   

            } else {

                //Setting name to one entry or ""
                $name_1 = $name_explode[0];
                $name_2 = '';

            }

            //Calling function checkin_book() based on $function_call_status variable

            if($function_call_status == 1) {
              
              //Calling checkin_book()
              $checkin_status = checkin_book($book_id, $card_no, $name_1, $name_2);

              //Creating display on return
              if($checkin_status == 'Fail') {

                //Failure modal script
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

                echo "<tfoot>";
                echo "<td colspan='7'>No content to display, you haven't run a query yet!</td>";
                echo "</tfoot>";

              } else if($checkin_status == 'book_unavailable') {

                  //Book unavailable modal
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

                  echo "<tfoot>";
                  echo "<td colspan='7'>No content to display, please try again with a different combination!</td>";
                  echo "</tfoot>";

              } else if($checkin_status == 'card_unavailable') {

                  //Card unavailable modal
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

                  echo "<tfoot>";
                  echo "<td colspan='7'>No content to display, please try again with a different combination!</td>";
                  echo "</tfoot>";

              } else if($checkin_status == 'borrower_unavailable') {

                  //Borrower_Name unavailable modal
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

                  echo "<tfoot>";
                  echo "<td colspan='7'>No content to display, please try again with a different combination!</td>";
                  echo "</tfoot>";
        
              } else if( count($checkin_status) > 0 ) {

                //Echoing out the query results
                  echo "<form method='POST' action='checkin.php'>";
                  echo "<tbody>";
                  
                  
                  foreach ($checkin_status as $row) {
                    echo "$rowStart";
                      echo "$row[book_id]";
                    echo "$insertColumn";
                      echo "$row[branch_id]";
                    echo "$insertColumn";
                      echo "$row[card_no]";
                    echo "$insertColumn";
                      echo "$row[fname]" . " " . "$row[lname]";
                    echo "$insertColumn";
                      echo "$row[date_out]";
                    echo "$insertColumn";
                      echo "$row[due_date]"; 
                    echo "$insertColumn";
                      //Checkbox creation
                      $checkin_recall_value = "$row[book_id]" . " " . "$row[branch_id]" . " " . "$row[card_no]";
                      echo "<div style='text-align: center;'><input type='checkbox' name='checkin[]' value='$checkin_recall_value' onClick='EnableSubmit(this)'></div>";
                    echo "$rowEnd";
                  }

                  echo "</tbody>";

                  //Checkin button in foot
                  echo "<tfoot>"; 
                  echo "<td colspan='7' style='text-align: center; padding-right: 20px;'><input id='checkin_button' type='submit' value='Check In' title='Click to check in!'></td>";
                  echo "</tfoot>";

                  echo "</form>";

              } else {

                //Empty set returned
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

                echo "<tfoot>";
                echo "<td colspan='7'>No content to display, please try again with a different combination!</td>";
                echo "</tfoot>";
              }

            } //End of Function call status if block
             
             
        } else {

              echo "<tfoot>";
              echo "<td colspan='7'>No content to display, you haven't run a query yet!</td>";
              echo "</tfoot>";
            
        } //End of entire if-else block


        //Recall to checkin
        if(!empty($_POST['checkin'])) {

          //Retrieving the recall variables as an array from POST and assigning to $main_array
          $main_array = array();


            if(is_array($_POST['checkin'])) {
              foreach($_POST['checkin'] as $value)
              {
                $sub_array = explode(" ", $value);
                array_push($main_array, $sub_array);
              }
            }

            
            //Calling checkin_recall() to update book_loans
            $checkin_recall_status = checkin_recall($main_array);

            if($checkin_recall_status = 'delete_success') {

                //Script for successfull checkin
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

                  echo "<tfoot>";
                  echo "<td colspan='7'>Please run another Checkin search!</td>";
                  echo "</tfoot>";

            } else {

              //Script for checkin fail
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

                  echo "<tfoot>";
                  echo "<td colspan='7'>Please run another Checkin search!</td>";
                  echo "</tfoot>";

            }

      }//End of recall


      ?>


    </table>

  </div>
</div>


  <!-- Empty set returned -->
  <div id="myModal4" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>No checkout(s) made by this combination!
        <div style="color: SlateGray;">
            <?php 
              if( !($book_id == '') ) {
                echo "Book ID: " . "<b>" . $book_id . "</b>" . " ";
              }

              if( !($card_no == '') ) {
                echo "Card No: " . "<b>" . $card_no . "</b>" . " ";
              }

              if( !($name == '') ) {
                echo "Name: " . "<b>" . $name . "</b>" . " ";
              }
            ?>
        </div>
     </p>
  </div>

  <!-- Book unavailable modal -->
  <div id="myModal5" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The book having Book ID: <b style="color: SlateGray;"><?php echo $book_id; ?></b> that you are searching for is not available in our library.</p>
  </div>

  <!-- Card No unavailable modal -->
  <div id="myModal6" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Borrower with Card No: <b style="color: SlateGray;"><?php echo $card_no; ?></b> is not registered.</p>
  </div>

  <!-- Borrower_Name unavailable modal -->
  <div id="myModal7" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p><b style="color: SlateGray;"><?php echo $name_1 . " " . $name_2; ?></b> is not a registered borrower in our library.</p>
  </div>

  <!-- Checkin successful modal -->
  <div id="myModal8" class="reveal-modal" style="color: green; text-align: center;">
     <p>You have successfully checked in the books!</p>
  </div>

  <!-- Checkin failed modal -->
  <div id="myModal9" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Checkin failed, please try again!</p>
  </div>


  
  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>
</body>
</html>
