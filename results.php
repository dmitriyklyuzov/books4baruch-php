<?php
session_start();

$searchBox="";

if (isset($_GET['s'])) {
	$searchBox = $_GET['s'];
}

//connect to the database
include 'controller/dataconnect_movedb.php';

//include ordinal number formatter
include 'controller/ordinalFormatter.php';

//include search query generator
include 'controller/generateSearchQuery.php';

?>

<!DOCTYPE html>

<head>
	
	<title>Results - Books4Baruch</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="bootstrap-3.3.6-dist/css/bootstrap.min.css">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css/newstylesheet.css" rel="stylesheet">
  	
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script charset="utf-8"></script>

</head>

<html lang="en">
	<body >

		<?php include 'navbar.php'; ?>



		<br>

		<div class="container" >

			<?php

			//check if a user entered something
			if ($searchBox!=""){

			//protect from injection attacks
			$searchBox = stripslashes($searchBox);

			//escape special characters
			$searchBox = $mysqli->real_escape_string($searchBox);

			//Query the database with the searchbox value - keyword entered
			$resultSet = $mysqli -> query (generateSearchQuery($searchBox));

			//Count the returned rows and store in a new variable
			if($resultSet!=FALSE){
				$row_count = $resultSet -> num_rows;
			}

			//resultset is false
			else header("Location: 404.php");

			//if there are rows returned, print the number of rows
			if($resultSet -> num_rows != 0){
				
				$row_count = $resultSet -> num_rows;
				
				//print how many rows were found for the keyword entered
				echo "
					<div class='row'>
						<div class='msg
									col-lg-12 col-lg-offset-0
									col-md-12 col-md-offset-0
									col-sm-12 col-sm-offset-0
									col-xs-12 col-xs-offset-0'>
							<p>".$row_count." results found for '".$searchBox."'</p>
						</div>
					</div>

					<hr>

					<br>
				";

				//Turn the results into an array
				while($rows = $resultSet -> fetch_assoc())
				{
					$book_title = $rows['book_title'];
					$book_author = $rows['book_author'];
					$book_ISBN = $rows['book_ISBN'];
					$book_edition = $rows['book_edition'];
					$price = $rows['price'];
					$seller_name = $rows['first_name'];
					$seller_email = $rows['student_email'];
					$seller_phone = $rows['phone_number'];
					$listing_id = $rows['listing_id'];
					$listing_condition = $rows['listing_condition'];

					if(is_int($book_edition)){
						$book_edition = getOrdinalSuffix($book_edition);
					}

			?>

			<div class="row">
					
					<div class="cover 
									col-lg-2 col-lg-offset-1
									col-md-2 col-md-offset-1
									col-sm-2 col-sm-offset-1
									hidden-xs" >
						<a href="details.php?listing_id=<?php echo $listing_id; ?>"><img class="img-responsive img-thumbnail" src='img/covers/<?php echo $book_ISBN; ?>.jpg' onError="this.src='img/covers/backupImg.jpg'"></a>
					</div>
					
					<div class="details 
										col-lg-5
										col-md-6
										col-sm-7
										col-xs-8">
						<p><b><?php echo $book_title; ?> | <?php echo $book_edition; ?> edition</b></p>
						<p><?php echo "By ".$book_author; ?></p>
						<p>ISBN: <?php echo $book_ISBN; ?></p>
						<p>Condition: <?php echo $listing_condition; ?>/10</p>
						<p><a id="details" href="details.php?listing_id=<?php echo $listing_id; ?>">Details</a></p>
					</div>

					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
						<div id="price"><h2><?php echo "$".$price; ?></h2></div>
					</div>
				
			</div> <!-- row -->

			<?php
			
			if($row_count>1){
			
				echo "<hr>";
				
				echo "<br>";
								
			}

				} //end while loop - "Turn the results into an array"
			} //end if - "if there are rows returned, print the number of rows"
		//if no rows are returned, display a message to try again
		else echo "
					<div class='row'>
						<div class='msg
								col-lg-12 col-lg-offset-0
								col-md-12 col-md-offset-0
								col-sm-12 col-sm-offset-0
								col-xs-12 col-xs-offset-0'>
							<p>Nothing found for '".$searchBox."'. Try again!</p>
						</div>
					</div>
			";

	} //end "Check if user entered something"
	else echo "
					<div class='row'>
						<div class='msg
								col-lg-12 col-lg-offset-0
								col-md-12 col-md-offset-0
								col-sm-12 col-sm-offset-0
								col-xs-12 col-xs-offset-0'>
							<p>Looks like you haven't entered anything. Try again!</p>
						</div>
					</div>
			";

			?>
			
		</div> <!-- container -->

	</body>
</html>