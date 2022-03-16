<!DOCTYPE html>
<html lang="en">
<head>
	<title>Test</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
	<script src="js/jquery.js"></script>
	
	
	<script> 
	$(document).ready(function() {
		console.log("Hello");
	});
	</script> 	
</head>
	
<body>
	<div class="container mw-100 px-5 py-3">
			
		<?php
     
			// Create an array 
			$sampleArray = array( 
				0 => "Geeks",  
				1 => "for",  
				2 => "Geeks",  
			); 
			
			// Creating an associative array 
			$name_one = array("Zack"=>"Zara", "Anthony"=>"Any",  
								"Ram"=>"Rani", "Salim"=>"Sara",  
								"Raghav"=>"Ravina"); 
			  
			// Looping through an array using foreach 
			echo "Looping using foreach: <br>"; 
			foreach ($name_one as $val => $val_value){ 
				echo "Husband is ".$val." and Wife is ".$val_value."<br>"; 
			} 
		
		?> 
		
		<script> 
			
					
					function geeks() {
							// Access the array elements 
							var passedArray =  
								<?php echo json_encode($sampleArray); ?>; 
								   
							// Display the array elements 
							for(var i = 0; i < passedArray.length; i++){ 
								document.write(passedArray[i]); 
							} 
					}
			
		</script> 	
			
					
	</div> <!-- end container -->
	
	<script type="text/javascript"> 
		geeks();
	</script> 
	
	
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>   
</body>
</html>