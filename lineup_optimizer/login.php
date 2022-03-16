<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
</head>
	
<body>
	<div class="container mw-100 px-5 py-3">
			
		<!-- header section -->
		<?php include('includes/header.php'); ?>
		
		<!-- next row -->	
		<div class= "row mb-5">
			<nav class ="col-sm-2 pl-0">
				<ul class = "nav nav-pills flex-column text-dark" style="max-width:160px;">
					<?php include('includes/nav-signup-login.php'); ?>
				</ul>			
			</nav>
			
			<!-- validate login request -->
			<?php
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					require('requires/process-login.php');	
									
				} // condition to submit form data to database	
			?>
			
			<div class = "col-sm-8 pl-0">
				
				<h3 class="text-center justify-content-center font-bold text-dark mb-2">Login</h3>
				
				<form action="<?php echo $_SERVER['PHP_SELF'];?>" method= "POST" >
					<div class="row form-group">
						<label for="user_name" class="col-sm-4 col-form-label text-right">User Name:</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name"
                             maxlength="50" required onfocus="this.removeAttribute('readonly');"
							 value = "<?php if(isset($_POST['user_name'])) echo $_POST['user_name'];       ?>">
							 					
						</div>						
					</div>	
																							
					<div class="row form-group">
						<label for="password" class="col-sm-4 col-form-label text-right">Password:</label>
						<div class="col-sm-7">
							<input style="float:left; width:140px !important;" type="password" class="form-control" id="password" name="password"
                             minlength="8" maxlength="50"  required
							 value = "<?php if(isset($_POST['password'])) echo $_POST['password'];  ?>">
							<span style="clear:both; color:darkblue;" class="ml-2 align-middle"><strong>Minimum of 8 characters</strong></span>						 					
						</div>							
					</div>
									
					<div class="row form-group">
						<label for="login_button" class="col-sm-4 col-form-label text-right"></label>
						<div class="col-sm-3">
							<input type="submit" class="gray-btn text-center border border-dark form-control"
									id="login_button" name="login_button" value = "Login"
									style="border-radius:.3em; width:70px !important; height:35px;">	
						</div>		
						<div class="col-sm-5">
							<!-- used to keep column sections uniform -->						 					
						</div>						
					</div>						
				</form>
				
			</div> <!-- end of middle section of document -->
			
			<!-- lists any errors found with login data entered -->
			<div class ="col-sm-2">
				<?php
			
				if(isset($errorstring)) {
					
					echo "<p class='text-center' style='color:red'>$errorstring</p>";				
				}
				
				?>			
						
			</div>  <!-- last  column of row -->		

		</div>  <!-- end of row  -->
		
		
		<?php include('includes/footer.php');?>
			
					
	</div> <!-- end container -->
	
	
	<script src="js/jquery.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>   
</body>
</html>