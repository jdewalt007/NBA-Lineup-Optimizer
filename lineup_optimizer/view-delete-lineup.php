<?php
	session_start();
	if(empty($_SESSION['user'])) {		
		header('location: index.php');
		exit();		
	}
	if(empty($_POST['yes-delete-btn'])) {
		$lineup_id = intval($_GET['l_id']);
		$_SESSION['lineup_id'] = $lineup_id;
	}
	
	$user_lineups = $_SESSION['user_lineups'];	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>TOP SCORE View/Delete Lineup</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">	
	
	<script src="js/jquery.js"></script>
	<script src="js/math.min.js" type="text/javascript"></script>
		
<style>

	.green {
		font-family: Times, "Times New Roman", serif;
		color:rgb(85,157,7);
		font-size: 18px;
		}
		
	.gray-btn {
		background-color: lightgray;
		color:gray;
	}
		
	.btn-2 {       
		height:2.5em;
		border-radius: .8em;
		outline: none;
		text-decoration: none;
		border: 1.5px solid black;
		font-size:18px;
		font-weight: bold;
	}
			
	#delete_lineup_btn : focus {		
		background-color:white;
		color:black;
	}		
</style>	
<script>
function CustomAlert()  { 
	this.render = function(dialog){
		var winW = window.innerWidth;
		var winH = window.innerHeight;
		var dialogoverlay = document.getElementById('dialogoverlay');
		var dialogbox = document.getElementById('dialogbox');
		dialogoverlay.style.display = "block";
		dialogoverlay.style.height = winH+"px";
		dialogbox.style.left = (winW/2) - (550 * .5)+"px";
		dialogbox.style.top = "100px";
		dialogbox.style.display = "block";
		document.getElementById('dialogboxhead').innerHTML = "Acknowledge This Message";
		document.getElementById('dialogboxbody').innerHTML = dialog;
		document.getElementById('dialogboxfoot').innerHTML = '<button onclick="Alert.ok()">OK</button>';
	}
	this.render2 = function(dialog, dialog2){
		var winW = window.innerWidth;
		var winH = window.innerHeight;
		var dialogoverlay = document.getElementById('dialogoverlay');
		var dialogbox = document.getElementById('dialogbox');
		dialogoverlay.style.display = "block";
		dialogoverlay.style.height = winH+"px";
		dialogbox.style.left = (winW/2) - (550 * .5)+"px";
		dialogbox.style.top = "100px";
		dialogbox.style.display = "block";
		document.getElementById('dialogboxhead').innerHTML = "Acknowledge This Message";
		if(dialog2 != "") {dialog2 = "<br>" + dialog2;}
		document.getElementById('dialogboxbody').innerHTML = dialog + dialog2;
		document.getElementById('dialogboxfoot').innerHTML = '<button onclick="Alert.ok()">OK</button>';
	}
	this.ok = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
}
var Alert = new CustomAlert();

</script>	
</head>
	
<body>
	<div class="container mw-100 px-5 py-3" style="min-width:1130px;">
		<!-- custom alert structure display none until called -->
		<div id="dialogoverlay"></div>
		<div id="dialogbox">
		  <div>
			<div id="dialogboxhead"></div>
			<div id="dialogboxbody"></div>
			<div id="dialogboxfoot"></div>
		  </div>
		
					
		<!-- header section -->
	</div><?php include('includes/header.php');?><div
				
		 class= "row mb-5">		
			<nav class ="col-sm-5 pl-0" >
				<ul class = "nav  flex-row text-dark">
					<?php include('includes/nav-view-delete-lineup.php'); ?>
				</ul>
			</nav>
			
			<div class = "col-sm-3 pl-0">
				<h3 class="font-bold mb-3 pr-2" style="color:black">Lineup (View/Delete)</h3>			
			</div>
			
			<div class ="col-sm-4">
					<h5 class="text-center  font-bold mb-2"  style="color:darkblue">
						Member:&nbsp; <?php echo $_SESSION['user']; ?></h5>											
			</div>  			
		</div> <!-- end of row  -->		
		<?php		
			if($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['yes-delete-btn']))) {
				require('requires/process-delete-lineup.php');	
			}
		?>	
		
		<?php														 				
			try{
				
				$retrieved_data = false;	
				
				if(empty($_POST['yes-delete-btn'])) 
					require('requires/mysqli_connect.php');		
				else 
					$dbcon = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
				
				$query = "SELECT fpts_total, remain_salary FROM lineup WHERE id = ?";				
				$q= mysqli_stmt_init($dbcon);
				mysqli_stmt_prepare($q,$query);
				mysqli_stmt_bind_param($q, 'i', $_SESSION['lineup_id']);
				mysqli_stmt_execute($q);
				$result = mysqli_stmt_get_result($q);
				
				if($result && (mysqli_num_rows($result) == 1)) {
					
					$row = mysqli_fetch_array($result, MYSQLI_NUM);
					$fpts_total = $row[0];
					$remain_salary = $row[1];
					mysqli_free_result($result);
					$query = "SELECT DISTINCT p_position, p_name, opponent, p_fppg, p_salary
							  FROM lineup_position lp JOIN lineup l ON lp.lineup_id = ?";
					mysqli_stmt_prepare($q,$query);
					mysqli_stmt_bind_param($q, 'i', $_SESSION['lineup_id']);		  
					mysqli_stmt_execute($q);
					$result = mysqli_stmt_get_result($q);		  
							  					
					$player = array();
					$count = 0;
					if($result && (mysqli_num_rows($result) == 8)) {
						while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
							
							$player[$count] = array("position"=>$row[0],"name"=>$row[1],"opp"=>$row[2],
													"fppg"=>$row[3],"salary"=>$row[4]);		
							$count+=1;
						}
						echo '<script>console.log('.json_encode($player).');    </script>';
						mysqli_close($dbcon);
						$retrieved_data = true;
					} else {
						
						echo'<p class="text-center h4">Unable to retrieve lineup data. 
								Please try again later</p>';				
						include('includes/footer.php');		
						mysqli_close($dbcon);
						exit();					
					}
										
				} else {
					
					echo'<p class="text-center h4">The system is busy. Please try again later.</p>';				
					include('includes/footer.php');		
					mysqli_close($dbcon);
					exit();
				}

			}			
			catch(Exception $e) {
				  
				  // "An Exception occurred. Message: " .$e->getMessage();
				  print "The system is busy. Please try again later";
		  
			}	  
			catch (Error $e) {
			  
				  // "An Error occurred. Message: " .$e->getMessage();
				  print "The system is busy. Please try again later";
				  
			}  	

			if($retrieved_data) {
		?>						
				<div class= "row mb-0 mt-4">			
					<div class="col-sm-4">
					</div>	
					
					<div class="col-sm-4 pl-0 pr-0 m-0">
						<table id="salary-table" class="border border-dark pl-0 pr-0 m-0" style="width:100%;">
						
							<tr id="salary-row" class="text-white bg-dark mb-0 " style="width:100%; height:30px; font-size:14px;">
								<th class= "p-0 pl-2 font-weight-bold text-right" scope = "col" 
									style="min-width:60px; font-size:17px;">LINEUP</th>
								<th class= "p-0 pl-2  text-right font-weight-light" scope = "col" 
									style="min-width:130px;">Avg. Rem./Player:</th>
								<th class= "p-0 pl-2 green" name="sal_avg" id="sal_avg" scope = "col" 
									style="min-width:80px;">$0</th>
								<th class= "p-0 pr-2 text-right font-weight-light text-nowrap" scope = "col" 
									style="min-width:80px;">
									<span class="gray">|&nbsp;&nbsp;</span>Rem. Salary:</th>
								<th class= "p-0 green" name="sal_remain" id="sal_remain" scope = "col" 
									style="min-width:96px;"><?php echo $remain_salary;?></th>							
							</tr>	
								
						</table> 
					</div>	
				</div>	
				
				<div class= "row mb-0 mt-0">      				
					<div class="col-sm-4" >
					</div>
									
					<div class="col-sm-4 pl-0 pr-0 m-0" >
						<table class="pl-0 pr-0 m-0" id="lineup-table" style="width:100%;"> 
							<thead style="width:100%;">
								<tr class= "text-black border border-dark border-top-0 border-bottom-0" 
											style="width:100%; background-color:lightgray;">
								
									<th class= "px-2" scope = "col" style ="min-width:50px;">POS</th>	
										
									<th class= "px-2" scope = "col" style ="min-width:150px;">PLAYER</th>
										
									<th class= "px-2 text-center" scope = "col" style ="min-width:100px;">OPP</th>
										
									<th class= "px-2  text-center" scope = "col" style ="min-width:70px;">FPPG</th>
																			
									<th class= "px-3 text-center" scope = "col" style ="min-width:86px;">SALARY</th>	
								</tr>	
							</thead>
							<tbody>	
								<tr class="text-nowrap" style="background-color:white; height:20px;">					
									<td class= "px-2 border border-dark" name="pg_pos" id="pg_pos">PG</td>
									<td class= "px-2 border border-dark" name ="pg_name" id="pg_name"></td>
									<td class= "px-2 border border-dark" name="pg_opp" id="pg_opp"></td>
									<td class= "px-2 border border-dark text-center" name="pg_fppg" id="pg_fppg"></td>	
									<td class= "px-2 border border-dark text-center" name="pg_salary" id="pg_salary"></td>				
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="sg_pos" id="sg_pos">SG</td>
									<td class= "px-2 border border-dark" name ="sg_name" id="sg_name"></td>
									<td class= "px-2 border border-dark" name="sg_opp" id="sg_opp"></td>
									<td class= "px-2 border border-dark text-center" name="sg_fppg" id="sg_fppg"></td>					
									<td class= "px-2 border border-dark text-center" name="sg_salary" id="sg_salary"></td>
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="sf_pos" id="sf_pos">SF</td>
									<td class= "px-2 border border-dark" name ="sf_name" id="sf_name"></td>
									<td class= "px-2 border border-dark" name="sf_opp" id="sf_opp"></td>
									<td class= "px-2 border border-dark text-center" name="sf_fppg" id="sf_fppg"></td>					
									<td class= "px-2 border border-dark text-center" name="sf_salary" id="sf_salary"></td>			
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="pf_pos" id="pf_pos">PF</td>
									<td class= "px-2 border border-dark" name ="pf_name" id="pf_name"></td>
									<td class= "px-2 border border-dark" name="pf_opp" id="pf_opp"></td>
									<td class= "px-2 border border-dark text-center" name="pf_fppg" id="pf_fppg"></td>						
									<td class= "px-2 border border-dark text-center" name="pf_salary" id="pf_salary"></td>				
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="c_pos" id="c_pos">C</td>
									<td class= "px-2 border border-dark" name ="c_name" id="c_name"></td>
									<td class= "px-2 border border-dark" name="c_opp" id="c_opp"></td>
									<td class= "px-2 border border-dark text-center" name="c_fppg" id="c_fppg"></td>					
									<td class= "px-2 border border-dark text-center" name="c_salary" id="c_salary"></td>
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="g_pos" id="g_pos">G</td>
									<td class= "px-2 border border-dark" name ="g_name" id="g_name"></td>
									<td class= "px-2 border border-dark" name="g_opp" id="g_opp"></td>
									<td class= "px-2 border border-dark text-center" name="g_fppg" id="g_fppg"></td>					
									<td class= "px-2 border border-dark text-center" name="g_salary" id="g_salary"></td>
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="f_pos" id="f_pos">F</td>
									<td class= "px-2 border border-dark" name ="f_name" id="f_name"></td>
									<td class= "px-2 border border-dark" name="f_opp" id="f_opp"></td>
									<td class= "px-2 border border-dark text-center" name="f_fppg" id="f_fppg"></td>					
									<td class= "px-2 border border-dark text-center" name="f_salary" id="f_salary"></td>
								</tr>
								
								<tr class="text-nowrap" style="background-color:white; height:20px;">
									<td class= "px-2 border border-dark" name="util_pos" id="util_pos">UTIL</td>
									<td class= "px-2 border border-dark" name ="util_name" id="util_name"></td>
									<td class= "px-2 border border-dark" name="util_opp" id="util_opp"></td>
									<td class= "px-2 border border-dark text-center" name="util_fppg" id="util_fppg"></td>				
									<td class= "px-2 border border-dark text-center" name="util_salary" id="util_salary"></td>
								</tr>
															
								<tr class="border border-dark text-nowrap" style="background-color:white; height:20px;">
									<td class= " font-weight-bold text-right" 
										style="background-color:lightgray; border-right-color:lightgray !important;">EST</td>
									<td class= "pl-1 font-weight-bold text-left" style="background-color:lightgray;">FANTASY PTS</td>
									<td class= "text-center" name="fpts_total" id="fpts_total"
										style="border-left: 1px solid black;"><?php echo $fpts_total;?></td>
									<td class= "px-2" style="background-color:black; border-left: 1px solid black;" ></td>				
									<td class= "px-2" style="background-color:black; "></td> 
										<!-- extra td's on this row to match 5 columns of other rows -->
								</tr>
							</tbody>			
						</table>	
						<div class="mt-3 pt-2 text-center">	
							
							<button type="button"  class="btn-2 text-center" id="delete_lineup_btn" 
									onclick="confirm_delete();" >Delete Lineup</button>
									
							<form action="<?php echo $_SERVER['PHP_SELF'];?>" class='text-center' method="POST">		
								<span class="pl-1" style="font-size:18px; display:none;"
											id="question">Are you sure? 
								
									<input type="submit"  value="Yes" class="text-center ml-1 gray-btn border 
											font-weight-bold border-dark text-center" name ="yes-delete-btn"
											id="yes-delete-btn" style="border-radius:.3em; color:black; 
											display:none; height:29px; width:35px; font-size:16px;"> 
									
									<button type="button"  class="ml-2 gray-btn border font-weight-bold border-dark
											text-center" name ="no-delete-btn" id="no-delete-btn"
											style="border-radius:.3em; color:black; display:none;
											font-size:16px; height:29px; width:35px;" onclick="reset_delete()">No
									</button>
								 </span>
							</form> 
						</div>  
					</div>								
				</div> 
				
				<script>
				
					var player = <?php echo json_encode($player);?>;
					var lineup_table = document.getElementById("lineup-table");
					
					for(var i = 0; i < 8; i++) {
						
						switch(player[i]["position"]) {
					
							case "PG":
								
								lineup_row = lineup_table.rows[1];	
								break;
								
							case "SG":
																
								lineup_row = lineup_table.rows[2];	
								break;
							
							case "SF":
								
								lineup_row = lineup_table.rows[3];								
								break;
								
							case "PF":
								
								lineup_row = lineup_table.rows[4];								
								break;
								
							case "C":
								
								lineup_row = lineup_table.rows[5];								
								break;
								
							case "G":
								
								lineup_row = lineup_table.rows[6];								
								break;
								
							case "F":
								
								lineup_row = lineup_table.rows[7];								
								break;
								
							case "UTIL":
								
								lineup_row = lineup_table.rows[8];								
								break;
						}
						
						lineup_row.cells[1].innerHTML = player[i]["name"];
						lineup_row.cells[2].innerHTML = player[i]["opp"];
						lineup_row.cells[3].innerHTML = player[i]["fppg"];
						lineup_row.cells[4].innerHTML = player[i]["salary"];
					}
				
				
				
								
					var btn = document.getElementById("delete_lineup_btn");				
					btn.addEventListener('mousedown', e => {e.preventDefault();});						
					
					var btn1 = document.getElementById("yes-delete-btn");				
					btn1.addEventListener('mousedown', e => {e.preventDefault();});	
					
					var btn2 = document.getElementById("no-delete-btn");				
					btn2.addEventListener('mousedown', e => {e.preventDefault();});	
					
					function confirm_delete()  {
						
						var delete_button = document.getElementById("delete_lineup_btn");
						delete_button.style.display = "none";
						
						var question = document.getElementById("question");
						question.style.display = "";
						
						var confirm_delete_btn = document.getElementById("yes-delete-btn");
						confirm_delete_btn.style.display = "";
						
						var decline_delete_btn = document.getElementById("no-delete-btn");
						decline_delete_btn.style.display = "";													
					}
					
					function reset_delete()  {
						
						var delete_button = document.getElementById("delete_lineup_btn");
						delete_button.style.display = "";
						
						var question = document.getElementById("question");
						question.style.display = "none";
						
						var confirm_delete_btn = document.getElementById("yes-delete-btn");
						confirm_delete_btn.style.display = "none";
						
						var decline_delete_btn = document.getElementById("no-delete-btn");
						decline_delete_btn.style.display = "none";											
					}
					
				</script><?php	}  
													
			include('includes/footer.php'); 
	?>
								
	</div> <!-- end container -->
		
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>