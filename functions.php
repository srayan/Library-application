<?php

	//Session Initialization
	if(isset($_SESSION)) {
    	session_start();  
  	}
	
	//Globals
	$new_card_no = 9000;	//The global for new card_no
	$rowCount = 0;

	//Table elements
	$rowStart = "<tr><td>";
    $insertColumn = "</td><td>";
    $rowEnd = "</td></tr>";

	//Connection using PDO

	function connect_db() {
		//Include the config.php file
		require 'config.php';

		try {
			$conn = new PDO('mysql:host=localhost;dbname=library', $config['DB_USERNAME'], $config['DB_PASSWORD']);	
			return $conn;
		} catch(PDOException $e) {
			echo 'ERROR: Connection failed!';
		}
	}


	//Search Book (search.php)
	//Defining search_book() function

	function search_book($x, $y, $z) {
		$conn = connect_db();

		//Pre-processing the variables
		$book_id = $x;

		if(!empty($y)) {
			$title = '%' . $y . '%';
		} else {
			$title = '';
		}

		if(!empty($z)) {
			$author_name = '%' . $z . '%';
		} else {
			$author_name = '';
		}
		
	
		try {

			//Preapring the query
			$results = $conn->prepare(" SELECT 
										A.BOOK_ID,
										TITLE, 
										AUTHOR_NAME,
										A.BRANCH_ID,
										A.BRANCH_NAME,
										A.BRANCH_ID, 
										NO_OF_COPIES, 
										COUNT(BOOK_LOANS.BOOK_ID) AS NUM_OUT, 
										NO_OF_COPIES - COUNT(BOOK_LOANS.BOOK_ID) AS NUM_AVAIL
										
										FROM
											(SELECT DISTINCT TITLE, BOOK.BOOK_ID, BOOK_COPIES.BRANCH_ID, BOOK_AUTHORS.AUTHOR_NAME, LIBRARY_BRANCH.BRANCH_NAME, NO_OF_COPIES
											FROM BOOK,BOOK_COPIES,LIBRARY_BRANCH, BOOK_AUTHORS
											WHERE BOOK_COPIES.BOOK_ID=BOOK.BOOK_ID
											AND BOOK_COPIES.BRANCH_ID=LIBRARY_BRANCH.BRANCH_ID
											AND BOOK.BOOK_ID=BOOK_AUTHORS.BOOK_ID
											AND (BOOK.BOOK_ID = :book_id OR BOOK_AUTHORS.AUTHOR_NAME LIKE :author_name OR BOOK.TITLE LIKE :title)) AS A
											LEFT OUTER JOIN BOOK_LOANS ON A.BOOK_ID=BOOK_LOANS.BOOK_ID AND A.BRANCH_ID=BOOK_LOANS.BRANCH_ID
											GROUP BY A.BOOK_ID, A.BRANCH_ID");
			

			//Executing the query
			$results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
			$results->bindParam(':title', $title, PDO::PARAM_STR);
			$results->bindParam(':author_name', $author_name, PDO::PARAM_STR);
			$results->execute();

			$data = $results->fetchAll();
            return $data;

		} catch(PDOException $e) {
			echo 'ERROR: Book Search query failed!';
		}
	} //End of search_book()
		


	//Add Borrower (add_borrower.php)
	//Defining add_borrower() function

	function add_borrower($x, $y, $z) {
		$conn = connect_db();

		//Pre-processing the variables
		$fname = $x;
		$lname = $y;
		$address = $z;

		try {

			//Preapring the initial query to check prior existence of the borrower
			$prior_results = $conn->prepare(   "SELECT *
												FROM borrower
												WHERE fname = :fname AND lname = :lname AND address = :address");
			
			//Executing the initial query
			$prior_results->bindParam(':fname', $fname, PDO::PARAM_STR);
			$prior_results->bindParam(':lname', $lname, PDO::PARAM_STR);
			$prior_results->bindParam(':address', $address, PDO::PARAM_STR);
			$prior_results->execute();

			$data = $prior_results->fetchAll();
            
            //Checking the inital query results
            if(count($data) > 0) {
            	return array("exists");
            } else {

            	//Calculating the new_card_no
            	$card_no_stmt = $conn->query("SELECT * FROM borrower");
            	$rowCount = $card_no_stmt->rowCount();

            	global $new_card_no;
            	$rowCount++;
            	$new_card_no += $rowCount; 

            	//Preparing the insert query to add the borrower
            	$results = $conn->prepare( "INSERT INTO borrower (card_no, fname, lname, address)
            								VALUES ($new_card_no, :fname, :lname, :address)" );

            	//Executing the insert query
				$results->bindParam(':fname', $fname, PDO::PARAM_STR);
				$results->bindParam(':lname', $lname, PDO::PARAM_STR);
				$results->bindParam(':address', $address, PDO::PARAM_STR);
				
				if($results->execute()) {
					return array("success", "$new_card_no");
				} else {
					return array("fail");
				}

            }//End of Checking the inital query results

		} catch(PDOException $e) {
			echo 'ERROR: Adding borrower failed!';
		}


	} //End of add_borrower() function




	//Checkout (checkout.php)
	//Defining the checkout_book() function

	function checkout_book($x, $y, $z) {
		$conn = connect_db();

		//Pre-processing the variables
		$book_id = $x;
		$branch_id = $y;
		$card_no = $z;

		try {

			//Preparing queries to check book_id, card_no & branch_id for availability
			$prior_book_id_results = $conn->prepare("SELECT *
													 FROM book
													 WHERE book_id = :book_id");

			$prior_card_no_results = $conn->prepare("SELECT *
													 FROM borrower
													 WHERE card_no = :card_no");

			$prior_card_no_count_results = $conn->prepare( "SELECT COUNT(*) AS num
															FROM book_loans
															WHERE card_no = :card_no" );

			$prior_branch_id_results = $conn->prepare( "SELECT BC.book_id AS book_id, BC.branch_id AS branch_id, no_of_copies, COUNT(BL.book_id) AS num_out, no_of_copies - COUNT(BL.book_id) AS num_avail
														FROM book_copies as BC left OUTER JOIN book_loans as BL
														ON BC.book_id = BL.book_id AND BC.branch_id = BL.branch_id
														WHERE BC.book_id = :book_id
														GROUP BY BC.book_id, BC.branch_id" );

			$prior_duplicate_count_results = $conn->prepare("SELECT COUNT(*) AS num, YEAR(due_date) AS yyyy, MONTH(due_date) AS mm, DAY(due_date) AS dd
															 FROM book_loans
															 WHERE book_id = :book_id AND branch_id = :branch_id AND card_no = :card_no");
			$prior_duplicate_count_results->setFetchMode(PDO::FETCH_ASSOC);


			//Binding params and executing for book_id
			$prior_book_id_results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
			$prior_book_id_results->execute();

			$data_book_id = $prior_book_id_results->fetchAll();

			//Binding params and executing for card_no
			$prior_card_no_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
			$prior_card_no_results->execute();

			$data_card_no = $prior_card_no_results->fetchAll();

			//Binding params and executing for calculating no of checkouts for each borrower 
			$prior_card_no_count_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
			$prior_card_no_count_results->execute();

			$data_card_no_count = $prior_card_no_count_results->fetchAll();

			//Binding params and executing for branch_id
			$prior_branch_id_results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
			$prior_branch_id_results->execute();

			$data_branch_id = $prior_branch_id_results->fetchAll();

			//Binding params and executing to check for duplicate book loan entry
			$prior_duplicate_count_results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
			$prior_duplicate_count_results->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
			$prior_duplicate_count_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
			$prior_duplicate_count_results->execute();

			$data_duplicate_count = $prior_duplicate_count_results->fetchAll();
			$year = $data_duplicate_count[0]['yyyy'];
			$month = $data_duplicate_count[0]['mm'];
			$day = $data_duplicate_count[0]['dd'];

			//Checking iteratively for book_id > card_no > branch_id
			if(count($data_book_id) == 0) {
				return array("book_unavailable","$book_id");
			} else if(count($data_card_no) == 0) {
				return array("borrower_unavailable", "$card_no");
			} else if( ($branch_id < 1) || ($branch_id > 5) ) {
				return array("branch_unavailable", "$branch_id");
			} else if($data_card_no_count[0]['num'] == 3) {
				return array("max_checkouts");
			} else if($data_duplicate_count[0]['num'] == 1) {
				return array("duplicate_book_entry", "$book_id", "$branch_id", "$card_no", "$year", "$month", "$day");
			} else {

				//Global for $checkout_query_status
				$checkout_query_status = 0;
				$branch_id_return = array("not_found_at_branch");

				foreach($data_branch_id as $row) {
					if( ($row[1] == $branch_id) && ($row[4] > 0) ) {
						
						//Query to check out the book
						$results = $conn->prepare( "INSERT INTO book_loans (book_id, branch_id, card_no, date_out, due_date)
													VALUES (:book_id, :branch_id, :card_no, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))");

						
						//Parallel query to insert record into loan_history table
						$parallel_checkout_results = $conn->prepare( "INSERT INTO loan_history (book_id, branch_id, card_no, date_out, due_date, date_in, type)
															 VALUES (:book_id, :branch_id, :card_no, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), NULL, 'Check Out')" );
						
						
						//Binding params and executing insert for checking out
						$results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
						$results->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
						$results->bindParam(':card_no', $card_no, PDO::PARAM_STR);

						
						//Binding params and executing insert for parallel - loan_history table
						$parallel_checkout_results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
						$parallel_checkout_results->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
						$parallel_checkout_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
						
						
						if($results->execute() && $parallel_checkout_results->execute()) {
							$checkout_query_status = 1;
						} else {
							return array("failure");
						}


					} else if($row[4] > 0) {
						//Pushing other branches for suggestion
						array_push($branch_id_return, "$row[1]");
					}

				} //End of foreach loop

				//Returning suggestion array
				if($checkout_query_status == 0) {
					return $branch_id_return;
				} else {
					return array("success");
				}
				
			} //End of iterative checking

		} catch(PDOException $e) {
			echo 'ERROR: Checkout failed!';
		}

	} //End of checkout_book() function




	//Checkin (checkin.php)
	//Defining checkin_book() function

	function checkin_book($x, $y, $z1, $z2) {
		$conn = connect_db();

		//Pre-processing the variables
		$book_id = $x;
		$card_no = $y;
		$name_1 = $z1;
		$name_2 = $z2;

		try {

			//Book exixts? (is a valid book_id?)
			if( !($book_id == '') ) {

				$prior_book_id_results = $conn->prepare("SELECT *
													 FROM book
													 WHERE book_id = :book_id");

				//Binding params and executing for book_id
				$prior_book_id_results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
				$prior_book_id_results->execute();

				$data_book_id = $prior_book_id_results->fetchAll();

				if( count($data_book_id) == 0 ) {
					return "book_unavailable";
				}
			}

			//Card No exists? (is a valid card_no?)
			if( !($card_no == '') ) {
				
				$prior_card_no_results = $conn->prepare("SELECT *
													 FROM borrower
													 WHERE card_no = :card_no");

				//Binding params and executing for card_no
				$prior_card_no_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
				$prior_card_no_results->execute();

				$data_card_no = $prior_card_no_results->fetchAll();

				if( count($data_card_no) == 0 ) {
					return "card_unavailable";
				}
			}

			//Borrower exists? (is a valid borrower)
			if( !($name_1 == '') || !($name_2 == '') ) {
				
				$prior_name_results = $conn->prepare("SELECT *
													 FROM borrower
													 WHERE fname = :name_1 OR fname = :name_2 OR lname = :name_1 OR lname = :name_2");

				//Binding params and executing for card_no
				$prior_name_results->bindParam(':name_1', $name_1, PDO::PARAM_STR);
				$prior_name_results->bindParam(':name_2', $name_2, PDO::PARAM_STR);
				$prior_name_results->execute();

				$data_name = $prior_name_results->fetchAll();

				if( count($data_name) == 0 ) {
					return "borrower_unavailable";
				}
			}


			//Preparing query to checkin books
			$results = $conn->prepare( "SELECT book_loans.book_id AS book_id, book_loans.card_no AS card_no, book_loans.branch_id AS branch_id, fname, lname, date_out, due_date
										FROM book_loans, borrower, book
										WHERE book_loans.card_no = borrower.card_no AND book.book_id = book_loans.book_id
											  AND ( book_loans.book_id = :book_id OR 
											  		book_loans.card_no = :card_no OR 
											  		borrower.fname = :name_1 OR 
											  		borrower.fname = :name_2 OR 
											  		borrower.lname = :name_1 OR 
											  		borrower.lname = :name_2)
										ORDER BY book_loans.card_no" );


			//Binding params and executing
			$results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
			$results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
			$results->bindParam(':name_1', $name_1, PDO::PARAM_STR);
			$results->bindParam(':name_2', $name_2, PDO::PARAM_STR);

			if($results->execute()) {
				$data = $results->fetchAll();
				return $data;
			} else {
				return array("Fail");
			}


		} catch(PDOException $e) {
			echo "ERROR: Operation failed!";
		}
	}//End of checkin_book()



	//Checkin (checkin.php)
	//Defining checkin_recall()

	function checkin_recall($arr) {
		$conn = connect_db();

		//Variables to be used
		$main_array = $arr;
		$counter = 0;
		$counter_initialized = count($main_array);


		//Preparing query to delete matching tuples in book_loans
		$results = $conn->prepare( "DELETE FROM book_loans
									WHERE book_id = :book_id AND branch_id = :branch_id AND card_no = :card_no" );

		//Preparing query to insert checkin records into loan_history table
		$parallel_checkin_results = $conn->prepare( "INSERT INTO loan_history (book_id, branch_id, card_no, date_out, due_date, date_in, type)
													 VALUES (:book_id, :branch_id, :card_no, :date_out, :due_date, CURDATE(), 'Check In')" );

		//Intermediate query to fetch date_out & due_date from book_loans
		$intermediate_checkin_results = $conn->prepare( "SELECT date_out, due_date
														 FROM book_loans
														 WHERE book_id = :book_id AND branch_id = :branch_id AND card_no = :card_no" );

		//Preparing query to add fine
		$intermediate_dues_results = $conn->prepare( "UPDATE borrower
													  SET dues = dues + :calc_fine
													  WHERE card_no = :card_no" );


		//Binding params and executing for all the cases returned
		foreach($main_array as $row) {

			//Intermediate query
			$intermediate_checkin_results->bindParam(':book_id', $row[0], PDO::PARAM_STR);
			$intermediate_checkin_results->bindParam(':branch_id', $row[1], PDO::PARAM_STR);
			$intermediate_checkin_results->bindParam(':card_no', $row[2], PDO::PARAM_STR);

			$intermediate_checkin_results->execute();
			$intermediate_checkin_data = $intermediate_checkin_results->fetchAll();

			$intermediate_date_out = $intermediate_checkin_data[0]['date_out'];
			$intermediate_due_date = $intermediate_checkin_data[0]['due_date'];


			//Fine calculation
			$date1 = new DateTime($intermediate_date_out);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = $date1->diff($date2);

			if( ($interval->days) > 14 ) {
				$calc_fine = 1 * ( ($interval->days) - 14 );
				
				//Binding params for adding fine query
				$intermediate_dues_results->bindParam(':calc_fine', $calc_fine, PDO::PARAM_INT);
				$intermediate_dues_results->bindParam(':card_no', $row[2], PDO::PARAM_STR);

				//Executing query to add fine to applicable records
				$intermediate_dues_results->execute();	
			}
			

			//Parallel query binding params
			$parallel_checkin_results->bindParam(':book_id', $row[0], PDO::PARAM_STR);
			$parallel_checkin_results->bindParam(':branch_id', $row[1], PDO::PARAM_STR);
			$parallel_checkin_results->bindParam(':card_no', $row[2], PDO::PARAM_STR);
			$parallel_checkin_results->bindParam(':date_out', $intermediate_date_out, PDO::PARAM_STR);
			$parallel_checkin_results->bindParam(':due_date', $intermediate_due_date, PDO::PARAM_STR);


			//Actual checkin params
			$results->bindParam(':book_id', $row[0], PDO::PARAM_STR);
			$results->bindParam(':branch_id', $row[1], PDO::PARAM_STR);
			$results->bindParam(':card_no', $row[2], PDO::PARAM_STR);

			if($results->execute() && $parallel_checkin_results->execute()) {
				$counter++;
			} else {
				echo "ERROR: Delete failed!";
			}

		}//End of foreach() loop

		if($counter == $counter_initialized) {
			return "delete_success";
		} else {
			return "delete_fail";
		}


	}//End of checkin_recall()




	//History (history.php)
	//Defining history()

	function history($x, $y, $z) {
		$conn = connect_db();

		//Initializing the variables
		$book_id = $x;
		$branch_id = $y;
		$card_no = $z;

		try {

			//Book exists? (is a valid book_id?)
			if( !($book_id == '') ) {

				$prior_book_id_results = $conn->prepare("SELECT *
													 FROM book
													 WHERE book_id = :book_id");

				//Binding params and executing for book_id
				$prior_book_id_results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
				$prior_book_id_results->execute();

				$data_book_id = $prior_book_id_results->fetchAll();

				if( count($data_book_id) == 0 ) {
					return "book_unavailable";
				}
			}

			//Card No exists? (is a valid card_no?)
			if( !($card_no == '') ) {
				
				$prior_card_no_results = $conn->prepare("SELECT *
													 FROM borrower
													 WHERE card_no = :card_no");

				//Binding params and executing for card_no
				$prior_card_no_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
				$prior_card_no_results->execute();

				$data_card_no = $prior_card_no_results->fetchAll();

				if( count($data_card_no) == 0 ) {
					return "card_unavailable";
				}
			}

			//Branch exists? (is a valid borrower)
			if( !($branch_id == '') ) {
				
				$prior_branch_id_results = $conn->prepare("SELECT *
													 FROM library_branch
													 WHERE branch_id = :branch_id");

				//Binding params and executing for card_no
				$prior_branch_id_results->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
				$prior_branch_id_results->execute();

				$data_branch_id = $prior_branch_id_results->fetchAll();

				if( count($data_branch_id) == 0 ) {
					return "branch_unavailable";
				}
			}

			//Preparing query to pull history records
			$results = $conn->prepare( "SELECT *
										FROM loan_history
										WHERE book_id = :book_id OR
											  card_no = :card_no OR
											  branch_id = :branch_id" );

			$results->bindParam(':book_id', $book_id, PDO::PARAM_STR);
			$results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
			$results->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);

			if($results->execute()) {
				$data = $results->fetchAll();
				return $data;
			} else {
				return "Fail";
			}


		} catch(PDOException $e) {
			echo "ERROR: Operation failed!";
		}
	}//End of history()




	//Fines & Dues (fines.php)
	//Defining fines() function

	function fines($x) {

		$conn = connect_db();

		//Initializing the variables
		$card_no = $x;

		try {

			//Card No exists? (is a valid card_no?)
			if( !($card_no == '') ) {
				
				$prior_card_no_results = $conn->prepare("SELECT *
														 FROM borrower
														 WHERE card_no = :card_no");

				//Binding params and executing for card_no
				$prior_card_no_results->bindParam(':card_no', $card_no, PDO::PARAM_STR);
				$prior_card_no_results->execute();

				$data_card_no = $prior_card_no_results->fetchAll();

				if( count($data_card_no) == 0 ) {
					return "card_unavailable";
				}
			}

			//Preparing query to fetch dues
			$results = $conn->prepare( "SELECT *
										FROM borrower
										WHERE card_no = :card_no" ); 

			//Binding params
			$results->bindParam(':card_no', $card_no, PDO::PARAM_STR);

			if($results->execute()) {
				$data = $results->fetchAll();
				return $data;
			} else {
				echo "ERROR: Execution Failed!";
			}


		} catch(PDOException $e) {
			echo "ERROR: Operation Failed";
		}


	}



	//Update Dues (dues_processing.php)
	//Defining update_dues()

	function update_dues($x, $y) {
		$conn = connect_db();

		//Variables
		$paid_amount = $x;
		$card_no = $y;

		//Prepaing query to update dues
		$results = $conn->prepare( "UPDATE borrower
									SET dues = dues - :paid_amount
									WHERE card_no = :card_no" );

		//Binding params
		$results->bindParam(':paid_amount', $paid_amount, PDO::PARAM_INT);
		$results->bindParam(':card_no', $card_no, PDO::PARAM_STR);

		if($results->execute()) {
			return array("Success", $paid_amount);
		} else {
			echo "ERROR: Update dailed!";
		}


	}//End of update_dues() function
?>
