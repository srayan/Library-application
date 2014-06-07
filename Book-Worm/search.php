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

</head>
<body>

  <!-- Error & Suggestion hidden mark-up -->
  <div id="myModal" class="reveal-modal" style="color: OrangeRed; text-align: center;">
     <p>All the fields cannot be left blank, please enter a search item!.</p>
  </div>

  <div id="myModal2" class="reveal-modal" style="color: OrangeRed; text-align: center;">
    <p>Query returned an empty set!.
      <div style="color: SlateGray;">Try another combination of Book ID, Title and/or Author.</div>
    </p>
  </div>



  <!-- Main Content -->
  <div class="row">
    <?php include 'sidebar.php'; ?>

    <article class="main nine columns">
      <h2>Search Books</h2>
        <p>Search books by <b>ID</b>, <b>Title</b> or <b>Author</b> of the book.</p>

        
          <form class="form1" method="POST" action="search.php">
            
            <input type="text" name="search_book_id" id="search_input1" placeholder="Book ID"  title="Please enter the Book ID">
            <input type="text" name="search_title" id="search_input2" placeholder="Title"  title="Please enter the Title">
            <input type="text" name="search_author_name" id="search_input3" placeholder="Author"  title="Please enter Author's name">


            <input type="submit" id="submitButton" data-reveal-id="myModal" value="Search" name="search_action" title="Click to search!">
            <input type="reset" value="Reset" name="search_reset"> 
          </form>
      
        
    </article>

  </div>

<div class="row">

  <div class="main2 twelve columns">

    <table>
      <thead>
        <tr>
          <th style="width: 16.67%;">Book ID</th>
	  <th style="width: 16.67%;">Title</th>
	  <th style="width: 16.67%;">Author Name</th>
          <th style="width: 16.67%;">Branch ID</th>
          <th style="width: 16.67%;">Total Copies</th>
          <th style="width: 16.67%;">Available Copies</th>
        </tr>
      </thead>

      <?php

        //Retrieving input values and assigning them as session variables
        if(!empty($_POST)) {

            //Extracting input field values
            $book_id = trim($_POST['search_book_id']);
            $title = trim($_POST['search_title']);
            $author_name = trim($_POST['search_author_name']);

            //Blank field modal
            if( ($book_id == '') && ($title == '') && ($author_name == '') ) {
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
              echo "<td colspan='4'>No content to display, you haven't run a query yet!</td>";
              echo "</tfoot>";
            } else {

            //Calling function to execute the query
            $results_search_book = search_book($book_id, $title, $author_name);


            //Echoing out the query results
            if(count($results_search_book) > 0) {
  
              echo "<tbody>";
              
              
              foreach ($results_search_book as $row) {

                echo "$rowStart";
                  echo "$row[BOOK_ID]";
                echo "$insertColumn";
		  echo "$row[TITLE]";
		echo "$insertColumn";
		  echo "$row[AUTHOR_NAME]";
		echo "$insertColumn";
                  echo "$row[BRANCH_ID]";
                echo "$insertColumn";
                  echo "$row[NO_OF_COPIES]";
                echo "$insertColumn";
                  echo "$row[NUM_AVAIL]"; 
                echo "$rowEnd";
              }

              echo "</tbody>";
            } else {

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
                echo "<td colspan='4'>No content to display, you haven't run a query yet!</td>";
                echo "</tfoot>";

            }
          }

        } else {
            echo "<tfoot>";
            echo "<td colspan='4'>No content to display, you haven't run a query yet!</td>";
            echo "</tfoot>";
        }
        

      ?>
    </table>
  </div> <!-- End of main2 (12 columns) -->

</div> <!-- End of row -->




  <script src="_scripts/jquery.js"></script>
  <script src="_scripts/foundation.min.js"></script>
  <script src="_scripts/app.js"></script>
  <script src="_scripts/jquery.foundation.reveal.js"></script>

  <?php echo $script_file; ?>
  

</body>
</html>
