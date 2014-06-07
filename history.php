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
  
  <title>Book Worm - History</title>

  <link rel="stylesheet" href="_css/foundation.css">
  <link rel="stylesheet" href="_css/reveal.css">
  <link rel="stylesheet" href="_css/main.css">
  <link rel="Shortcut icon" href="_images/book_worm.ico">

</head>
<body>

  <!-- Modals -->
  <!-- Blank fields -->
  <div id="myModal" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>All the fields cannot be left blank, please enter a search item!.</p>
  </div>

  <!-- Query failed modal -->
  <div id="myModal2" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The operation failed, please try again!</p>
  </div>


  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>History</h2>
        <p>Check history of all checkouts, checkins by <b>Book ID</b> or <b>Branch ID</b> or <b>Card No</b>.</p>

        
          <form class="form1" method="POST" action="history.php">
            
            <input type="text" name="history_book_id" id="history_input1" placeholder="Book ID"  title="Please enter the Book ID">
            <input type="text" name="history_branch_id" id="history_input2" placeholder="Branch ID"  title="Please enter the Branch ID">
            <input type="text" name="history_card_no" id="history_input3" placeholder="Card No"  title="Please enter Card No">

            <input type="submit" id="submitButton" data-reveal-id="myModal" value="Show" name="history_action" title="Click to show history!">
            <input type="reset" value="Reset" name="history_reset"> 
          </form>
      
        
    </article>

  </div>

<!-- History Table display -->
  <div class="row">

  <div class="main2 twelve columns">

    <table>
      <thead>
        <tr>
          <th style="width: 16%;">Book ID</th>
          <th style="width: 14%;">Branch ID</th>
          <th style="width: 14%;">Card No</th>
          <th style="width: 14%;">Out Date</th>
          <th style="width: 14%;">Due Date</th>
          <th style="width: 14%;">In Date</th>
          <th style="width: 14%;">Type</th>
        </tr>
      </thead>

<?php

      if(!empty($_POST)) {

            //Extracting input field values
            $book_id = trim($_POST['history_book_id']);
            $branch_id = trim($_POST['history_branch_id']);
            $card_no = trim($_POST['history_card_no']);

            //Blank field modal
            if( ($book_id == '') && ($branch_id == '') && ($card_no == '') ) {
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

              echo "<tfoot>";
              echo "<td colspan='7'>No content to display, you haven't run a query yet!</td>";
              echo "</tfoot>";

            } else {

              //Calling history() to run the query
              $results_history = history($book_id, $branch_id, $card_no);

              //Creating display on return
              //book_id fail check
              if($results_history == 'Fail') {

                //Failure modal script
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

              } else if($results_history == 'book_unavailable') {

                  //Book unavailable modal
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
                  echo "<td colspan='7'>No content to display, please try again with a different combination!</td>";
                  echo "</tfoot>";

              } else if($results_history == 'card_unavailable') {

                  //Card unavailable modal
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

              } else if($results_history == 'branch_unavailable') {

                  //Branch_ID unavailable modal
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
        
              } else if( count($results_history) > 0 ) {

                //Echoing out the query results
                  
                  echo "<tbody>";
                  
                  foreach ($results_history as $row) {
                    
                    echo "$rowStart";
                      echo "$row[1]";
                    echo "$insertColumn";
                      echo "$row[2]";
                    echo "$insertColumn";
                      echo "$row[3]";
                    echo "$insertColumn";
                      echo "$row[4]";
                    echo "$insertColumn";
                      echo "$row[5]";
                    echo "$insertColumn";
                      if("$row[6]" == NULL) {
                        echo "N/A";
                      } else {
                        echo "$row[6]";  
                      }
                    echo "$insertColumn";
                      echo "$row[7]";
                    echo "$rowEnd";
                  }

                  echo "</tbody>";


              } else {

                //Empty set returned
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
              }

            }
      } else {
            echo "<tfoot>";
            echo "<td colspan='7'>No content to display, you haven't run a query yet!</td>";
            echo "</tfoot>";
      }//End of main if() block
?>

</table>
  </div> <!-- End of main2 (12 columns) -->

</div> <!-- End of row -->


  <!-- Book unavailable modal -->
  <div id="myModal3" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>The book having Book ID: <b style="color: SlateGray;"><?php echo $book_id; ?></b> that you are searching for is not available in our library.</p>
  </div>

  <!-- Card No unavailable modal -->
  <div id="myModal4" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Borrower with Card No: <b style="color: SlateGray;"><?php echo $card_no; ?></b> is not registered.</p>
  </div>

  <!-- Branch_ID unavailable modal -->
  <div id="myModal5" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>Branch bearing ID: <b style="color: SlateGray;"><?php echo $branch_id; ?></b> is not a valid branch of our library.</p>
  </div>

  <!-- Empty set returned -->
  <div id="myModal6" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>No history information found for this combination!
        <div style="color: SlateGray;">
            <?php 
              if( !($book_id == '') ) {
                echo "Book ID: " . "<b>" . $book_id . "</b>" . " ";
              }

              if( !($card_no == '') ) {
                echo "Card No: " . "<b>" . $card_no . "</b>" . " ";
              }

              if( !($branch_id == '') ) {
                echo "Branch ID: " . "<b>" . $branch_id . "</b>" . " ";
              }
            ?>
        </div>
     </p>
  </div>



  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>
</body>
</html>
