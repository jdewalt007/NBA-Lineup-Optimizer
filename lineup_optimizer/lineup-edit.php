<?php
	session_start();
	if(empty($_SESSION['user'])) {		
		header('location: index.php');
		exit();		
	}
	
	if(empty($_POST['pg_post_name'])) {
		$contest = intval($_GET['cid']); 
		$lineup_id = intval($_GET['lid']);
		$_SESSION['contest_id'] = $contest;	
		$_SESSION['lineup_id'] = $lineup_id;
	}	
	
	$players_exist = false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>TOP SCORE Edit Lineup</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">	
	
	<script src="js/jquery.js"></script>
	<script src="js/math.min.js" type="text/javascript"></script>
		
	<style>
	.bt {
		background-color:black;
		color:white;
		font-weight:bold;		
	}

	.active, .bt:hover {
		
		background-color:orange;
		
		color:black;
	}	
	
	.green{
		font-family: Times, "Times New Roman", serif;
		color:rgb(85,157,7);
		font-size: 18px;
		}
		
	.red{
		font-family: Times, "Times New Roman", serif;
		color: red;
		font-size: 18px;
		}	
				
	.gray{color:rgb(182,180,182);}
	
	.lineup_btn {       
        height:2.5em;
		border-radius: .8em;
		outline: none;
		text-decoration: none;
		border: 1.5px solid black;
		font-size:18px;
		font-weight: bold;
    }
		
	#lineup_save_btn   {
		background-color:green;
		color:white;
	}  
	
	#clear_lineup_bt : focus {		
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
		</div>
			
		<!-- header section -->
		<?php include('includes/header.php'); ?>
		
		<!-- next row -->	
		<div class= "row mb-5">
			<nav class ="col-sm-5 pl-0" >
				<ul class = "nav  flex-row text-dark">
					<?php include('includes/nav-select-lineup.php'); ?>
				</ul>			
			</nav>
			<div class = "col-sm-3 pl-0">
				<h3 class="text-center font-bold mb-3" style="color:black">Edit Lineup</h3>			
			</div>
			
			<div class ="col-sm-4">
					<h5 class="text-center  font-bold mb-2"  style="color:darkblue">
						Member:&nbsp; <?php echo $_SESSION['user']; ?></h5>
											
			</div>  <!-- last  column of row -->			
		</div> <!-- end of row  -->
				
		<?php
			if($_SERVER['REQUEST_METHOD'] == 'POST') {	
			
				require('requires/process-lineup-edit.php');					
			}	
			
			try{
						
				if(empty($_POST['pg_post_name'])) 						
					require('requires/mysqli_connect.php');
				else
					$dbcon = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					
				$query = "SELECT p.id AS id, CONCAT(p.lname,' ',p.fname) AS name, pp.position_1 AS pos_1, pp.position_2 AS pos_2, p.gm1_fpts AS pts1, p.gm2_fpts AS pts2, p.gm3_fpts AS pts3, p.gm4_fpts AS pts4, p.gm5_fpts AS pts5, p.salary AS salary, p.game_count AS gm_count, p.status AS avail, p.last_game AS lgame, ht.abbrev AS ht, ht.id AS ht_id, vt.abbrev AS vt, vt.id AS vt_id, p.team_id AS pt_id FROM CONTEST c JOIN CONTEST_GAME cg ON c.id = cg.contest_id JOIN GAME g ON cg.game_id = g.id JOIN TEAM ht ON ht.id = g.home_id JOIN TEAM vt ON vt.id = g.visit_id JOIN PLAYER p ON (p.team_id = ht.id OR p.team_id = vt.id) JOIN PLAYER_POSITION pp ON p.id = pp.player_id WHERE c.id = ? ORDER BY p.id ASC";
				
				$q= mysqli_stmt_init($dbcon);
				mysqli_stmt_prepare($q,$query);
				mysqli_stmt_bind_param($q, 'i', $_SESSION['contest_id']);
				mysqli_stmt_execute($q);
				$result = mysqli_stmt_get_result($q);
				
				
				$player_count = mysqli_num_rows($result);	
				$count = 0;
				if($result && (mysqli_num_rows($result) >= 1)) {
					
					$player = array();
																				
					while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
						
						$player[$count] = array("id"=>$row[0],"name"=>$row[1],"pos_1"=>$row[2],
												"pos_2"=>$row[3],"pts1"=>$row[4],"pts2"=>$row[5],
												"pts3"=>$row[6],"pts4"=>$row[7],"pts5"=>$row[8],
												"salary"=>$row[9],"gm_count"=>intval($row[10]),
												"avail"=>$row[11],"lgame"=>$row[12],"ht"=>$row[13],
												"ht_id"=>$row[14],"vt"=>$row[15],"vt_id"=>$row[16],
												"pt_id"=>$row[17],"fppg"=>0.0,"dev"=>0.0,"c_pt"=>0.0,
												"rank"=>"N/A");							
						$count += 1;																		
					}
				} else {	
					
					echo' </tbody> </table>';
					include('includes/footer.php');	
										
					echo'<script>
							var cust_alert = new CustomAlert();
							$message = "Players for the contest the lineup belongs to were not found.";
							cust_alert.render($message);
						 </script>';	
					exit();
				}	
				
				// Queries to get the lineup information to input into lineup table
				mysqli_free_result($result);				
				$query = "SELECT fpts_total, remain_salary FROM lineup WHERE id = ?";				
				$q= mysqli_stmt_init($dbcon); //?????????????????
				mysqli_stmt_prepare($q,$query);
				mysqli_stmt_bind_param($q, 'i', $_SESSION['lineup_id']);
				mysqli_stmt_execute($q);
				$result = mysqli_stmt_get_result($q);
				
				if($result && (mysqli_num_rows($result) == 1)) {
					
					$row = mysqli_fetch_array($result, MYSQLI_NUM);
					$fpts_total = $row[0];
					$remain_salary = $row[1];
					mysqli_free_result($result);
					$query = "SELECT DISTINCT p_id, p_position, p_name, opponent, p_fppg, p_salary
							  FROM lineup_position lp JOIN lineup l ON lp.lineup_id = ?";
					mysqli_stmt_prepare($q,$query);
					mysqli_stmt_bind_param($q, 'i', $_SESSION['lineup_id']);		  
					mysqli_stmt_execute($q);
					$result = mysqli_stmt_get_result($q);		  
												
					$lineup_player = array();
					$count = 0;
					if($result && (mysqli_num_rows($result) == 8)) {
						while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
							
							$lineup_player[$count] = array("p_id"=>$row[0],"position"=>$row[1],"name"=>$row[2],"opp"=>$row[3],
													"fppg"=>$row[4],"salary"=>$row[5]);		
							$count+=1;
						}
						echo '<script>console.log('.json_encode($lineup_player).');   </script>';
						mysqli_close($dbcon);
						$retrieved_data = true;
					} else {
						
						echo'<script>
								var cust_alert = new CustomAlert();
								$msg1 = "Unable to retrieve lineup data.";
								$msg2 = "Please try again later";
								cust_alert.render2($msg1, $msg2);
							 </script>';				
						include('includes/footer.php');		
						mysqli_close($dbcon);
						exit();					
					}
				} else {
					
					echo'<script>
							var cust_alert = new CustomAlert();
							$msg = "The system is busy. Please try again later.";
							cust_alert.render($msg);
						 </script>';			
					include('includes/footer.php');		
					mysqli_close($dbcon);
					exit();
				}
			}						
			catch(Exception $e) {
		  
			  // "An Exception occurred. Message: " .$e->getMessage();
			  //print "The system is busy. Please try again later";	
			   echo'	<script>
					var cust_alert = new CustomAlert();
					$msg1 = "The system is busy. Please try again later.";
					cust_alert.render($msg1);
				</script>';		
			}			  
			catch (Error $e) {
				  
			  // "An Error occurred. Message: " .$e->getMessage();
			  //print "The system is busy. Please try again later";
			  echo' <script>
						var cust_alert = new CustomAlert();
						$msg2 = "The system is busy. Please try again later.";
						cust_alert.render($msg2);
					 </script>';							  
			}  		

			if($retrieved_data) {     
		?>		
				<div class="row bg-white ml-2" style="height:25px;" >
						
					<img id="search-icon" src="images/search-icon.png" style="height:29.50px; padding-top:3px;">
					<input type="text" placeholder="Player Search" id="search_name" name="search_name"
						   style="color: rgb(182,180,182); " maxlength=30>
					<input type="button" class="text-white border border-dark bg-dark text-center" 
							id="clear_search" name="clear_search" value = "X" style="width:40px;">  
				</div>
					
				<div class= "row mb-0 mt-4">				
								
					<table id="position-btns-table" class="border border-dark col-sm-6 pl-0 pr-0 " style="margin:0px;"> 	
					
							<tr id="position_row" class="text-white row-bottom-border" style="width:100%; height:20px;">				
								<th class= " rt-border p-0" scope = "col" style="">					
									<button class="bt active border border-0" onclick="update_plist('PG')"
											style="min-width:35px;">PG</button>
								</th>						
								<th class= "rt-border p-0" scope = "col" style="">					
									<button class="bt border border-0" onclick="update_plist('SG')"
											style="min-width:35px;">SG</button>
								</th>
								<th class= "rt-border p-0" scope = "col" style="">					
									<button  class="bt border border-0" onclick="update_plist('SF')"
											 style="min-width:35px;">SF</button>
								</th>
								<th class= "rt-border p-0" scope = "col" style="">					
									<button  class="bt border border-0" onclick="update_plist('PF')"
											 style="min-width:35px;">PF</button>
								</th>
								<th class= "rt-border p-0" scope = "col" style="">					
									<button class="bt border border-0" onclick="update_plist('C')"
											style="min-width:30px;">C</button>
								</th>
								<th class= "rt-border p-0" scope = "col" style="">					
									<button class="bt border border-0" onclick="update_plist('G')"
											style="min-width:30px;">G</button>
								</th>
								<th class= "rt-border p-0" scope = "col" style="">					
									<button class="bt border border-0" onclick="update_plist('F')" 
											style="min-width:30px;">F</button>
								</th>
								<th class= "rt-border p-0" scope = "col" style="">					
									<button class="bt border border-0" onclick="update_plist('UTIL')"
											style="min-width:50px;">UTIL</button>
								</th>				
								<th class= "bg-dark" scope = "col" style="width:100%;"></th>		
							</tr>
					</table>
					<script>
						// Add active class to the current button (highlight it)
						var header = document.getElementById("position_row");
						var btns = header.getElementsByClassName("bt");
						for (var i = 0; i < btns.length; i++) {
						  btns[i].addEventListener("click", function() {
						  var current = document.getElementsByClassName("active");
						  current[0].className = current[0].className.replace(" active", "");
						  this.className += " active";
						  this.blur();
						  });
						}
					</script>			
							
					<table id="salary-table" class="border border-dark col-sm-4 pl-0 pr-0 m-0 ml-4">
						<tr class="text-white bg-dark mb-0" style="width:100%; height:30px; font-size:14px;">
							<th class= "p-0 pl-2 font-weight-bold text-right" scope = "col" style="min-width:90px;
								font-size:17px;">LINEUP</th>
							<th class= "p-0 pl-2  text-right font-weight-light" scope = "col" 
								style="min-width:130px;">Avg. Rem./Player:</th>
							<th class= "p-0 pl-2 green" name="sal_avg" id="sal_avg" scope = "col" style="min-width:80px;"></th>
							<th class= "p-0 pr-2 text-right font-weight-light text-nowrap" scope = "col" style="min-width:80px;">
								<span class="gray">|&nbsp;&nbsp;</span>Rem. Salary:</th>
							<th class= "p-0 green" name="sal_remain" id="sal_remain" scope = "col" 
								style="min-width:105px; width:100%;"></th>							
						</tr>												
					</table>  <!-- last  column of row -->								
				</div>
				
				<div class= "row mb-5 mt-0">
					<table class="col-sm-6 pl-0 pr-0"  id="select-table" style="margin: 0px; 
								display:block; max-height:250px; overflow-y:auto; overflow-x:auto;"> 
						<thead style="width:100%;">
							<tr class= "text-black border border-dark border-top-0 border-bottom-0" 
										style="width:100%; background-color:lightgray;">
							
								<th class= "px-2" scope = "col" style ="width:40px; position: sticky;
									top: 0; background-color:lightgray;">POS</th>	
									
								<th class= "px-2" scope = "col" style ="width:10px; position: sticky;
									top: 0; background-color:lightgray; ">PLAYER</th>
									
								<th class= "px-2 text-center" scope = "col" style ="width:100px; position: sticky;
									top: 0; background-color:lightgray;">OPP</th>
									
								<th class= "px-2" scope = "col" style ="width:50px; position: sticky;
									top: 0; background-color:lightgray;">FPPG</th>
									
								<th class= "px-1 text-nowrap" scope = "col" style ="width:50px; position: sticky;
									top: 0; background-color:lightgray;">STD DEV</th>
									
								<th class= "px-3" scope = "col" style ="width:60px; position: sticky;
									top: 0; background-color:lightgray;">SALARY</th>
									
								<th class= "px-1" scope = "col" style ="width:30px; position: sticky;
									top: 0; background-color:lightgray;">STATUS</th>
									
								<th class= "px-2 text-nowrap" scope = "col" style ="width:80px; position: sticky;
									top: 0; background-color:lightgray;">LAST GAME</th>	
									
								<th class= "px-2" scope = "col" style ="width:60px; position: sticky;
									top: 0; background-color:lightgray;">RANK</th>
									
								<th class= "px-3" scope = "col" style ="color:lightgray;width:100%; position: sticky;
									top: 0; background-color:lightgray;"></th>								
							</tr>	
						</thead>
						<tbody>				
		<?php	
				
						// used to store those players who have a c_pt cost/point value,
						// and their id value. Sort array by c_pt ASC and then assign
						// ranking to players identified by their matched id value from 1 to 
						//number of players ranked							
						$ranked_players = array();
						$rk_count = 0;
						
						for($i=0;$i < $player_count; $i++){
																
								if($player[$i]["lgame"] == "0000-00-00")
									$player[$i]["lgame"] = "N/A";
																																	
								if($player[$i]["gm_count"]== 0) {
									
									$player[$i]["c_pt"]= "N/A";
									$player[$i]["dev"]= "N/A";
									$player[$i]["fppg"]= "N/A";
									$player[$i]["rank"]= "N/A";
									
								} 
								else if($player[$i]["gm_count"] < 5) { //for players with 1 to 4 games played
									
									$pts = 0.0;
									// array to hold pts for 1 to 4 games played
									$gm_pts = array();
									
									for($j=0; $j < $player[$i]["gm_count"]; $j++){
										$temp = "pts";
										$gm = $j + 1;
										$temp.= $gm;
										$pts += $player[$i][$temp];	
										
										$gm_pts[$j] = $player[$i][$temp];											
									}	
									// player fppg average
									$player[$i]["fppg"] = round($pts/$player[$i]["gm_count"], 1);
									$fppg = $player[$i]["fppg"];
									
									// No ranking assigned if less than 5 games played for season
									$player[$i]["c_pt"]= "N/A";
									$player[$i]["rank"]= "N/A";
																												
									// procedure to calc standard deviation for current player
									$k = 0;
									$dev_sum = 0.0;
									while($k < $player[$i]["gm_count"]) {
										$dev_sum += pow($gm_pts[$k] - $fppg, 2);										
										$k++;
									}
									$dev = round(sqrt($dev_sum/$player[$i]["gm_count"]), 1);
									$player[$i]["dev"] = $dev;										
								}
								else { // for players with full 5 games played
									
									$pts = array($player[$i]["pts1"],$player[$i]["pts2"],
												$player[$i]["pts3"], $player[$i]["pts4"],
												$player[$i]["pts5"]); 
												
									$sum_pts = array_sum($pts);	
									$player[$i]["fppg"] = round($sum_pts/5, 1);
									$fppg = $player[$i]["fppg"];
									
									// array to hold pts for the 5 games played
									$gm_pts = array();
									
									for($j=0; $j < 5; $j++){
										$temp = "pts";
										$gm = $j + 1;
										$temp.= $gm;											
										$gm_pts[$j] = $player[$i][$temp];											
									}	
									
									$k = 0;
									$dev_sum = 0.0;
																			
									while($k < 5) {
										$dev_sum += pow($gm_pts[$k] - $fppg, 2);										
										$k++;
									}
									$dev = round(sqrt($dev_sum/5), 1);
									$player[$i]["dev"] = $dev;	
									
									
									$player[$i]["c_pt"] = round(($player[$i]["salary"]/$fppg), 2);
									
									$ranked_players[$rk_count] =  array("c_pt"=> $player[$i]["c_pt"], "p_id"=> $player[$i]["id"], 
																"p_name"=> $player[$i]["name"]);
									$rk_count++;																							
								}
						}		
													
						array_multisort(array_column($ranked_players, "c_pt"), SORT_ASC, $ranked_players);
						
						$rank = 1;
						for($b=0; $b < count($ranked_players); $b++){
							for($c=0; $c < count($player); $c++) {
								if($player[$c]["id"]== $ranked_players[$b]["p_id"])  {
									$player[$c]["rank"] = $rank;
									$rank++;
								}
							}								
						}
						
						$status_colors = array("OK"=>'green', "OUT"=> 'red', "Q"=>'rgb(227,227,0)');
						$pg_array = [];
								
						for($t=0; $t < count($player); $t++)   {
							if(($player[$t]["pos_1"] == "PG") || ($player[$t]["pos_2"] == "PG")) {
								
								$pg_array[] = $player[$t];
							
								if(empty($player[$t]["pos_2"])) 
										$position = $player[$t]["pos_1"];
									else 
										$position = $player[$t]["pos_1"].'/'.$player[$t]["pos_2"];
									
								if($player[$t]["ht_id"] == $player[$t]["pt_id"])
										$opp = $player[$t]["vt"].'@<strong>'.$player[$t]["ht"].'</strong>';
									else
										$opp = '<strong>'.$player[$t]["vt"].'</strong>@'.$player[$t]["ht"];
									
								$player[$t]["salary"] = number_format($player[$t]["salary"]);
								
								
								echo'<tr class="text-nowrap" style="font-size:16px; background-color:white; height:20px;">
									<td class= "px-2 border border-dark" style="display:none;">'.$player[$t]["id"].'</td>
									<td class= "px-2 border border-dark" style="display:none;">'.$player[$t]["pos_1"].'</td>
									<td class= "px-2 border border-dark" style="display:none;">'.$player[$t]["pos_2"].'</td>
									<td class= "px-2 border border-dark">'.$position.'</td>
									<td class= "px-2 border border-dark">'.$player[$t]["name"].'</td>
									<td class= "px-2 border border-dark">'.$opp.'</td>
									<td class= "px-2 border border-dark">'.$player[$t]["fppg"].'</td>
									<td class= "px-2 border border-dark text-center">'.$player[$t]["dev"].'</td>
									<td class= "px-2 border border-dark text-center">&#36;'.$player[$t]["salary"].'</td>';
		?>	
									<td class= "px-1 border border-dark text-center" style="font-weight:bold; color: <?php 
										echo $status_colors[$player[$t]["avail"]];?>"> <?php echo $player[$t]["avail"];?>
									</td>
								<?php	
								echo'<td class= "px-2 border border-dark">'.$player[$t]["lgame"].'</td>
									 <td class= "px-2 border border-dark text-center">'.$player[$t]["rank"].'</td>	
									 <td class= "px-2 border border-dark text-center">
										<img src="images/add-player-icon.png" 
										onclick="add_pg('.$player[$t]["id"].');" 
										style="width:20px; height:20px; cursor: pointer;"></td>
								</tr>';
							}
						}																									
						echo' </tbody> </table>';		
						$players_exist = true;										
								?>	
								
						<script>
																					
							function update_plist(position_selected) {
								var exist = <?php echo json_encode($players_exist); ?>;
								if(exist) {
									var position = position_selected;
									
									var table = document.getElementById("select-table");
									var rowCount = table.rows.length;
									for (var i = rowCount - 1; i > 0; i--) {
										table.deleteRow(i);
									}
									var player = <?php echo json_encode($player); ?> ;
									
									var status_colors = [];									
									status_colors['OK'] = 'green';
									status_colors['OUT'] = 'red';
									status_colors['Q'] = 'rgb(227,227,0)';
																																	
									var tbody = document.getElementById("select-table").getElementsByTagName("tbody")[0];
									
									var lineup_table = document.getElementById("lineup-table");
																			
									switch(position) {
										
										case "PG":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) {
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');							
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													var text_8  = document.createTextNode("$" + player[t]["salary"]);
													cell_8.appendChild(text_8);
													
													
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center', 'img_hover');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[1].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"PG");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("PG selection already made! Please remove current selection first.");	
														}
													});
													cell_12.appendChild(img);	
																																														}
											}	
										
											// prevent selected players from being displayed in select-table
											var pg_table = document.getElementById("select-table");
											var pg_t = pg_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < pg_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(pg_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														pg_t.rows[i].style.display = "none";
														break;
													}
												}
											}
										
											break;
																							
										case "SG":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "SG") || (player[t]["pos_2"] == "SG")) {
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');							
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
																											
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[2].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"SG");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("SG selection already made! Please remove current selection first.");	
														}
													});
													cell_12.appendChild(img);

												}											
											}
											// prevent selected players from being displayed in select-table
											var sg_table = document.getElementById("select-table");
											var sg_t = sg_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < sg_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(sg_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														sg_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											break;
											
										case "SF":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "SF") || (player[t]["pos_2"] == "SF")) {
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');							
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
													
													
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[3].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"SF");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("SF selection already made! Please remove current selection first.");	
														}
													});
													cell_12.appendChild(img);	
												}
											}
											// prevent selected players from being displayed in select-table
											var sf_table = document.getElementById("select-table");
											var sf_t = sf_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < sf_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(sf_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														sf_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											
											
											break;
										
										case "PF":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "PF") || (player[t]["pos_2"] == "PF")) {
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');		
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
													
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													cell_12.appendChild(img);
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[4].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"PF");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("PF selection already made! Please remove current selection first.");	
														}
													});	
												}
											}
											// prevent selected players from being displayed in select-table
											var pf_table = document.getElementById("select-table");
											var pf_t = pf_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < pf_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(pf_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														pf_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											break;
										
										case "C":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "C") || (player[t]["pos_2"] == "C")) {
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');		
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
																											
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													cell_12.appendChild(img);
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[5].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"C");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("C selection already made! Please remove current selection first.");	
														}
													});	
												}
											}
											// prevent selected players from being displayed in select-table
											var c_table = document.getElementById("select-table");
											var c_t = c_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < c_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(c_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														c_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											break;
										
										case "G":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")
													|| (player[t]["pos_1"] == "SG") || (player[t]["pos_2"] == "SG")) {
														
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');		
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
													
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													cell_12.appendChild(img);
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[6].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"G");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("G selection already made! Please remove current selection first.");	
														}
													});	
												}
											}
											// prevent selected players from being displayed in select-table
											var g_table = document.getElementById("select-table");
											var g_t = g_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < g_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(g_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														g_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											break;
											
										case "F":
											for(var t=0; t < player.length; t++)      {
												
												if((player[t]["pos_1"] == "SF") || (player[t]["pos_2"] == "SF")
													|| (player[t]["pos_1"] == "PF") || (player[t]["pos_2"] == "PF")) {
														
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');			
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
													
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													cell_12.appendChild(img);
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[7].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"F");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("F selection already made! Please remove current selection first.");	
														}
													});	
												}
											}
											// prevent selected players from being displayed in select-table
											var f_table = document.getElementById("select-table");
											var f_t = f_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < f_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(f_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														f_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											break;	
											
										case "UTIL":
											for(var t=0; t < player.length; t++)      {
																											
													if(player[t]["pos_2"] == "")
														var e_position = player[t]["pos_1"];
													else
														var e_position = player[t]["pos_1"] + "/" + player[t]["pos_2"];
													
													var newRow = tbody.insertRow();
													newRow.style.fontSize = "medium";
													newRow.classList.add("java_table_row");
													
													var cell_0 = newRow.insertCell(0);
													cell_0.classList.add('px-2', 'border', 'border-dark');
													cell_0.style.display = "none";
													var text_0  = document.createTextNode(player[t]["id"]);
													cell_0.appendChild(text_0);
																										
													var cell_1 = newRow.insertCell(1);
													cell_1.classList.add('px-2', 'border', 'border-dark');
													cell_1.style.display = "none";
													var text_1  = document.createTextNode(player[t]["pos_1"]);
													cell_1.appendChild(text_1);
													
													var cell_2 = newRow.insertCell(2);
													cell_2.classList.add('px-2', 'border', 'border-dark');
													cell_2.style.display = "none";
													var text_2  = document.createTextNode(player[t]["pos_2"]);
													cell_2.appendChild(text_2);
																											
													var cell_3 = newRow.insertCell(3);
													cell_3.classList.add('px-2', 'border', 'border-dark');							
													var text_3  = document.createTextNode(e_position);
													cell_3.appendChild(text_3);
													
													var cell_4 = newRow.insertCell(4);
													cell_4.classList.add('px-2', 'border', 'border-dark');							
													var text_4  = document.createTextNode(player[t]["name"]);
													cell_4.appendChild(text_4);
													
													
													var cell_5 = newRow.insertCell(5);
													cell_5.classList.add('px-2', 'border', 'border-dark');
													var ht  = document.createTextNode(player[t]["ht"]),
														at  = document.createTextNode("@");
														vt  = document.createTextNode(player[t]["vt"]),
														bold = document.createElement('strong');
													if(player[t]["ht_id"] == player[t]["pt_id"]) {
														bold.appendChild(ht);
														cell_5.appendChild(vt);	
														cell_5.appendChild(at);
														cell_5.appendChild(bold);													
													}else {
														bold.appendChild(vt);
														cell_5.appendChild(bold);	
														cell_5.appendChild(at);
														cell_5.appendChild(ht);	
													}
													
													var cell_6 = newRow.insertCell(6);
													cell_6.classList.add('px-2', 'border', 'border-dark');							
													var text_6  = document.createTextNode(player[t]["fppg"]);
													cell_6.appendChild(text_6);
													
													var cell_7 = newRow.insertCell(7);
													cell_7.classList.add('px-2', 'border', 'border-dark', 'text-center');							
													var text_7  = document.createTextNode(player[t]["dev"]);
													cell_7.appendChild(text_7);
													
													var cell_8 = newRow.insertCell(8);
													cell_8.classList.add('px-2', 'border', 'border-dark', 'text-center');	
													if((player[t]["pos_1"] == "PG") || (player[t]["pos_2"] == "PG")) 
														var text_8  = document.createTextNode("$" + player[t]["salary"]);
													else {
														nfObject = new Intl.NumberFormat('en-US'); 
														formatted_sal = nfObject.format(player[t]["salary"]);
														var text_8  = document.createTextNode("$" + formatted_sal);
													}
													cell_8.appendChild(text_8);
																										
													var cell_9 = newRow.insertCell(9);
													cell_9.classList.add('px-1', 'border', 'border-dark', 'text-center');
													cell_9.style.fontWeight = "bold";												
													cell_9.style.color = status_colors[player[t]["avail"]];
													var text_9  = document.createTextNode(player[t]["avail"]);
													cell_9.appendChild(text_9);
													
													var cell_10 = newRow.insertCell(10);
													cell_10.classList.add('px-2', 'border', 'border-dark');
													var text_10  = document.createTextNode(player[t]["lgame"]);
													cell_10.appendChild(text_10);
													
													var cell_11 = newRow.insertCell(11);
													cell_11.classList.add('px-2', 'border', 'border-dark', 'text-center');
													var text_11  = document.createTextNode(player[t]["rank"]);
													cell_11.appendChild(text_11);
													
													var cell_12 = newRow.insertCell(12);
													cell_12.classList.add('px-1', 'border', 'border-dark', 'text-center');
													var img = document.createElement('img');
													img.src = "images/add-player-icon.png";
													img.style.width = "20px";
													img.style.height = "20px";
													img.style.cursor = "pointer";
													cell_12.appendChild(img);
													img.addEventListener("click", function(){
														
														if(lineup_table.rows[8].cells[0].innerHTML == "") {
															var selected_row = $(this).closest('tr');
															add_player(selected_row,"UTIL");											
														}
														else {
															var cust_alert = new CustomAlert();
															cust_alert.render("UTIL selection already made! Please remove current selection first.");	
														}
													});	
											}
											// prevent selected players from being displayed in select-table
											var util_table = document.getElementById("select-table");
											var util_t = util_table;
											var ln_t = lineup_table;
											
											for(var i = 1; i < util_t.rows.length; i++) {
												for(var j = 1; j < ln_t.rows.length; j++) {
													if(util_t.rows[i].cells[0].innerHTML == ln_t.rows[j].cells[0].innerHTML) {
														util_t.rows[i].style.display = "none";
														break;
													}
												}
											}
											break;	
									}
								}																	
							}
							
							function add_player(clicked_row, position) {
								
								var select_table = document.getElementById("select-table");
								var lineup_table = document.getElementById("lineup-table");
								var pts_row = lineup_table.rows[9];
								
								switch(position) {
									
									case "PG" :
										
										lineup_row = lineup_table.rows[1];											
										break;
									
									case "SG" :
										lineup_row = lineup_table.rows[2];
										break;
										
									case "SF" :
										
										lineup_row = lineup_table.rows[3];
										break;
									
									case "PF" :
										
										lineup_row = lineup_table.rows[4];
										break;
										
									case "C" :
										
										lineup_row = lineup_table.rows[5];
										break;
										
									case "G" :
										
										lineup_row = lineup_table.rows[6];
										break;	
										
									case "F" :
										
										lineup_row = lineup_table.rows[7];
										break;
										
									case "UTIL" :
										
										lineup_row = lineup_table.rows[8];
										break;												
								}
								
								clicked_row[0].style.display =  "none";
								
								var p_id = 	clicked_row[0].cells[0].innerHTML;
								var p_pos1 = clicked_row[0].cells[1].innerHTML
								var p_pos2 = clicked_row[0].cells[2].innerHTML;
								var p_name = clicked_row[0].cells[4].innerHTML;
								var opp = 	clicked_row[0].cells[5].innerHTML; 
								var p_fppg = clicked_row[0].cells[6].innerHTML;	
								var p_salary = clicked_row[0].cells[8].innerHTML;
																			
								lineup_row.cells[0].innerHTML = p_id;
								lineup_row.cells[1].innerHTML = p_pos1;
								lineup_row.cells[2].innerHTML = p_pos2;
								lineup_row.cells[4].innerHTML = p_name;
								lineup_row.cells[5].innerHTML = opp;  
								lineup_row.cells[6].innerHTML = p_fppg;
								lineup_row.cells[7].innerHTML = p_salary;
										
								// img listener for click added further below
								var img = document.createElement('img');
								img.src = "images/delete-player-icon.png";
								img.style.width = "20px";
								img.style.height = "20px";
								img.style.cursor = "pointer";
								lineup_row.cells[8].appendChild(img);
								
								
								if(p_fppg == "N/A")
									p_fppg = "0.0";
								
								var cur_pts = pts_row.cells[2].innerHTML;
								var adj_pts =  parseFloat(cur_pts) + parseFloat(p_fppg);
								adj_pts = adj_pts.toFixed(1);
								pts_row.cells[2].innerHTML = adj_pts;
																	
								var players_picked = 0;
								for(var i =1; i < (lineup_table.rows.length - 1); i++){
									if(lineup_table.rows[i].cells[0].innerHTML != "")
										players_picked += 1;
								}
								var rem_picks = 8 - players_picked;			
								
								// remaining salary calculated and updated in salary-table
								var salary = document.getElementById("salary-table");
								var sal_row = salary.rows[0]; 
								var rem_salary = sal_row.cells[4].innerHTML;
								var mod_p_salary = p_salary.slice(1);
								mod_p_salary = mod_p_salary.replace(",","");
								
								if(rem_salary.charAt(0) == "-") {
									rem_salary = rem_salary.replace(",", "");
									rem_salary = rem_salary.slice(2);
									rem_salary = "-" + rem_salary;								
									rem_salary = parseInt(rem_salary) - parseInt(mod_p_salary);
								}
								else {
									rem_salary = rem_salary.slice(1);	
									rem_salary = rem_salary.replace(",", "");
									rem_salary = parseInt(rem_salary) - parseInt(mod_p_salary);
								}
															
								var sal = rem_salary;
								
								//format rem_salary to number with commas
								nfObject = new Intl.NumberFormat('en-US'); 
								rem_salary = nfObject.format(rem_salary); 
																																		
								if(sal < 0) {
									//rem_salary = rem_salary.replace("$-", "-$");
									rem_salary = rem_salary.replace("-", "-$");
									sal_row.cells[4].classList.add('red');									
								}
								else {	
									rem_salary = "$" + rem_salary;
									sal_row.cells[4].classList.add('green');
								}								
								sal_row.cells[4].innerHTML = rem_salary;
								
								
								// avg remaining salary calculated and updated in salary-table
								var avg_sal = sal_row.cells[2].innerHTML;
								
								if(sal <= 0) {
									avg_sal = "$0";
									sal_row.cells[2].innerHTML = avg_sal;
								}
								else {
																
									if(rem_picks == 0) 
										avg_sal = "$0";										
									else {
										var rem_s = rem_salary.slice(1);
										rem_s = rem_s.replace(",", "");
										avg_sal = math.round(parseInt(rem_s)/rem_picks);
										nfObject2 = new Intl.NumberFormat('en-US'); 
										avg_sal = nfObject2.format(avg_sal); 
										avg_sal = "$" + avg_sal;
									}
									sal_row.cells[2].innerHTML = avg_sal;
								}
								
								img.addEventListener("click", function()     {
									
									cur_pts = pts_row.cells[2].innerHTML;
									var player_pts = $(this).closest('tr').children()[6].innerHTML;
									if(player_pts == "N/A")
										player_pts = "0.0";
									adj_pts = parseFloat(cur_pts) -  parseFloat(player_pts);
									adj_pts = adj_pts.toFixed(1);
									pts_row.cells[2].innerHTML = adj_pts;
									
									
									// 1) update remaining salary for lineup selection 
									// 2) player removed from lineup after salary calculations completed
									var p_salary = $(this).closest('tr').children()[7].innerHTML;
									p_salary = p_salary.slice(1);	
									p_salary = p_salary.replace(",", "");	
									
									var cur_sal = sal_row.cells[4].innerHTML;
									if(cur_sal.charAt(0) == "-") {
										cur_sal = cur_sal.replace(",", "");
										cur_sal = cur_sal.slice(2);
										cur_sal = "-" + cur_sal;								
									}
									else {
										
										cur_sal = cur_sal.slice(1);	
										cur_sal = cur_sal.replace(",", "");
									}
									
									var cur_sal_b4_calc = parseInt(cur_sal);
									
									t_rem_salary = parseInt(cur_sal) + parseInt(p_salary);
																		
									var total_sal = t_rem_salary;
									
									nfObject4 = new Intl.NumberFormat('en-US'); 
									t_rem_salary = nfObject4.format(t_rem_salary); 
									
									if(total_sal < 0) {
										t_rem_salary = t_rem_salary.replace("-", "-$");
										sal_row.cells[4].classList.add('red');
									}
									else {
										t_rem_salary = "$" + t_rem_salary;
										if(cur_sal_b4_calc < 0)
											sal_row.cells[4].classList.remove('red');
										sal_row.cells[4].classList.add('green');
									}
									sal_row.cells[4].innerHTML = t_rem_salary;
									
									
									// clear row data except for player position identifier
									var childs = $(this).closest('tr').children();
									var cur_position = childs[3].innerHTML;
									childs.empty();
									childs[3].innerHTML = cur_position;
									
									
									players_picked = 0;
									for(var i =1; i < (lineup_table.rows.length - 1); i++){
										if(lineup_table.rows[i].cells[0].innerHTML != "")
											players_picked += 1;
									}
									
									rem_picks = 8 - players_picked;
									
									// display unselected player again in select-table
									for(var p =1; p < select_table.rows.length; p++) {
										if (select_table.rows[p].cells[0].innerHTML == p_id ) {
											select_table.rows[p].style.display = "";
											break;
										}																		
									}	
																		
									// avg remaining salary calculated and updated in salary-table									
									if(total_sal <= 0) {
										avg_sal = "$0";
										sal_row.cells[2].innerHTML = avg_sal;
									}
									else {
																					
										rem_s = t_rem_salary.slice(1);
										rem_s = rem_s.replace(",", "");
										avg_sal = math.round(parseInt(rem_s)/rem_picks);
										nfObject5 = new Intl.NumberFormat('en-US'); 
										avg_sal = nfObject5.format(avg_sal); 
										avg_sal = "$" + avg_sal;
										sal_row.cells[2].innerHTML = avg_sal;
										
									}							
								});									
							}																									
						</script>	
						<div  class="col-sm-4 pl-0 pr-0 m-0 ml-4">		
							<form class ="pl-0 pr-0 m-0" id="lineup_form" name="lineup_form" 
									action="<?php echo $_SERVER['PHP_SELF'];?>" method= "POST" >
								<table class="pl-0 pr-0 m-0" id="lineup-table"> 
									<thead style="width:100%;">
										<tr class= "text-black border border-dark border-top-0 border-bottom-0" 
													style="width:100%; background-color:lightgray;">
										
											<th class= "px-2" scope = "col" style ="min-width:50px;">POS</th>	
												
											<th class= "px-2" scope = "col" style ="min-width:150px;">PLAYER</th>
												
											<th class= "px-2 text-center" scope = "col" style ="min-width:100px;">OPP</th>
												
											<th class= "px-2  text-center" scope = "col" style ="min-width:70px;">FPPG</th>
																					
											<th class= "px-3 text-center" scope = "col" style ="min-width:86px;">SALARY</th>
																	
											<th class= "px-3" scope = "col" style ="min-width:38px; width:100%;"></th>
										</tr>	
									</thead>
									<tbody>	
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="pg_id" id="pg_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">PG</td>
											<td class= "px-2 border border-dark" name ="pg_name" id="pg_name"></td>
											<td class= "px-2 border border-dark" name="pg_opp" id="pg_opp"></td>
											<td class= "px-2 border border-dark text-center" name="pg_fppg" id="pg_fppg"></td>		
											<td class= "px-2 border border-dark text-center" name="pg_salary" id="pg_salary"></td>		
											<td class= "px-2 border border-dark"></td>	
											
											<input type="hidden" name="pg_post_id" id="pg_post_id" value="">
											<input type="hidden" name="pg_post_name" id="pg_post_name" value="">
											<input type="hidden" name="pg_post_opp" id="pg_post_opp" value="">
											<input type="hidden" name="pg_post_fppg" id="pg_post_fppg" value="">
											<input type="hidden" name="pg_post_salary" id="pg_post_salary" value="">
																											
											<!--  hidden input $_POST for sal_remain apart of salary-table  -->
											<input type="hidden" name="sal_post_remain" id="sal_post_remain" value="">
										</tr>
										
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="sg_id" id="sg_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">SG</td>
											<td class= "px-2 border border-dark" name ="sg_name" id="sg_name"></td>
											<td class= "px-2 border border-dark" name="sg_opp" id="sg_opp"></td>
											<td class= "px-2 border border-dark text-center" name="sg_fppg" id="sg_fppg"></td>			
											<td class= "px-2 border border-dark text-center" name="sg_salary" id="sg_salary"></td>		
											<td class= "px-2 border border-dark"></td>	
											
											<input type="hidden" name="sg_post_id" id="sg_post_id" value="">
											<input type="hidden" name="sg_post_name" id="sg_post_name" value="">
											<input type="hidden" name="sg_post_opp" id="sg_post_opp" value="">
											<input type="hidden" name="sg_post_fppg" id="sg_post_fppg" value="">
											<input type="hidden" name="sg_post_salary" id="sg_post_salary" value="">							
										</tr>
										
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="sf_id" id="sf_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">SF</td>
											<td class= "px-2 border border-dark" name ="sf_name" id="sf_name"></td>
											<td class= "px-2 border border-dark" name="sf_opp" id="sf_opp"></td>
											<td class= "px-2 border border-dark text-center" name="sf_fppg" id="sf_fppg"></td>		
											<td class= "px-2 border border-dark text-center" name="sf_salary" id="sf_salary"></td>		
											<td class= "px-2 border border-dark"></td>		

											<input type="hidden" name="sf_post_id" id="sf_post_id" value="">
											<input type="hidden" name="sf_post_name" id="sf_post_name" value="">
											<input type="hidden" name="sf_post_opp" id="sf_post_opp" value="">
											<input type="hidden" name="sf_post_fppg" id="sf_post_fppg" value="">
											<input type="hidden" name="sf_post_salary" id="sf_post_salary" value="">
										</tr>
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="pf_id" id="pf_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">PF</td>
											<td class= "px-2 border border-dark" name ="pf_name" id="pf_name"></td>
											<td class= "px-2 border border-dark" name="pf_opp" id="pf_opp"></td>
											<td class= "px-2 border border-dark text-center" name="pf_fppg" id="pf_fppg"></td>		
											<td class= "px-2 border border-dark text-center" name="pf_salary" id="pf_salary"></td>		
											<td class= "px-2 border border-dark"></td>	

											<input type="hidden" name="pf_post_id" id="pf_post_id" value="">
											<input type="hidden" name="pf_post_name" id="pf_post_name" value="">
											<input type="hidden" name="pf_post_opp" id="pf_post_opp" value="">
											<input type="hidden" name="pf_post_fppg" id="pf_post_fppg" value="">
											<input type="hidden" name="pf_post_salary" id="pf_post_salary" value="">
										</tr>
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="c_id" id="c_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">C</td>
											<td class= "px-2 border border-dark" name ="c_name" id="c_name"></td>
											<td class= "px-2 border border-dark" name="c_opp" id="c_opp"></td>
											<td class= "px-2 border border-dark text-center" name="c_fppg" id="c_fppg"></td>			
											<td class= "px-2 border border-dark text-center" name="c_salary" id="c_salary"></td>
											<td class= "px-2 border border-dark"></td>	

											<input type="hidden" name="c_post_id" id="c_post_id" value="">
											<input type="hidden" name="c_post_name" id="c_post_name" value="">
											<input type="hidden" name="c_post_opp" id="c_post_opp" value="">
											<input type="hidden" name="c_post_fppg" id="c_post_fppg" value="">
											<input type="hidden" name="c_post_salary" id="c_post_salary" value="">
										</tr>
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="g_id" id="g_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">G</td>
											<td class= "px-2 border border-dark" name ="g_name" id="g_name"></td>
											<td class= "px-2 border border-dark" name="g_opp" id="g_opp"></td>
											<td class= "px-2 border border-dark text-center" name="g_fppg" id="g_fppg"></td>			
											<td class= "px-2 border border-dark text-center" name="g_salary" id="g_salary"></td>
											<td class= "px-2 border border-dark"></td>	

											<input type="hidden" name="g_post_id" id="g_post_id" value="">
											<input type="hidden" name="g_post_name" id="g_post_name" value="">
											<input type="hidden" name="g_post_opp" id="g_post_opp" value="">
											<input type="hidden" name="g_post_fppg" id="g_post_fppg" value="">
											<input type="hidden" name="g_post_salary" id="g_post_salary" value="">
										</tr>
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="f_id" id="f_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">F</td>
											<td class= "px-2 border border-dark" name ="f_name" id="f_name"></td>
											<td class= "px-2 border border-dark" name="f_opp" id="f_opp"></td>
											<td class= "px-2 border border-dark text-center" name="f_fppg" id="f_fppg"></td>			
											<td class= "px-2 border border-dark text-center" name="f_salary" id="f_salary"></td>
											<td class= "px-2 border border-dark"></td>		

											<input type="hidden" name="f_post_id" id="f_post_id" value="">
											<input type="hidden" name="f_post_name" id="f_post_name" value="">
											<input type="hidden" name="f_post_opp" id="f_post_opp" value="">
											<input type="hidden" name="f_post_fppg" id="f_post_fppg" value="">
											<input type="hidden" name="f_post_salary" id="f_post_salary" value="">
										</tr>
										<tr class="text-nowrap" style="background-color:white; height:20px;">
											<td class= "px-2 border border-dark" name="util_id" id="util_id" 
												style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark" style="display:none;"></td>
											<td class= "px-2 border border-dark">UTIL</td>
											<td class= "px-2 border border-dark" name ="util_name" id="util_name"></td>
											<td class= "px-2 border border-dark" name="util_opp" id="util_opp"></td>
											<td class= "px-2 border border-dark text-center" name="util_fppg" id="util_fppg"></td>		
											<td class= "px-2 border border-dark text-center" name="util_salary" id="util_salary"></td>
											<td class= "px-2 border border-dark"></td>	

											<input type="hidden" name="util_post_id" id="util_post_id" value="">
											<input type="hidden" name="util_post_name" id="util_post_name" value="">
											<input type="hidden" name="util_post_opp" id="util_post_opp" value="">
											<input type="hidden" name="util_post_fppg" id="util_post_fppg" value="">
											<input type="hidden" name="util_post_salary" id="util_post_salary" value="">
										</tr>
																	
										<tr class="border border-dark text-nowrap" style="background-color:white; height:20px;">
											<td class= " font-weight-bold text-right" style="background-color:lightgray; 
												border-right-color:lightgray !important;">EST</td>
											<td class= "pl-1 font-weight-bold text-left" style="background-color:lightgray;
												">FANTASY PTS</td>
											<td class= "text-center" name="fpts_total" id="fpts_total"
												style="border-left: 1px solid black;"></td>
											<td class= "px-2" style="background-color:black; border-left: 1px solid black;" ></td>		
											<td class= "px-2" style="background-color:black; "></td>
											<td class= "px-2" style="background-color:black;" ></td>	
											<!-- extra td's to match 6 columns of other rows -->

											<input type="hidden" name="fpts_post_total" id="fpts_post_total" value="">					
										</tr>
									</tbody>			
								</table>	
								<div class="mt-3 pt-2 text-center">	
								
									<button type="button"  class="lineup_btn text-center" id="clear_lineup_btn" 
											onclick="clear_lineup();" >Clear Lineup</button>

									<button type="button"  class="lineup_btn ml-4 text-center"  id="lineup_save_btn" 
											onclick="save_lineup();" >Update Lineup</button>
									
								</div>  
							</form>
							
							
						<?php  
							if ($_SERVER['REQUEST_METHOD'] != 'POST') {
								
								echo '<script>
								
									$(document).ready(function() {
																
										var lineup_player = '.json_encode($lineup_player).';
										var lineup_table = document.getElementById("lineup-table");
										var sal_row = document.getElementById("salary-table").rows[0];
										sal_row.cells[2].innerHTML = "$0";
										sal_row.cells[4].innerHTML = '.json_encode($remain_salary).';
										lineup_table.rows[9].cells[2].innerHTML = '.json_encode($fpts_total).';
																													
										for(var i = 0; i < 8; i++) {
											
											switch(lineup_player[i]["position"]) {
										
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
											
											lineup_row.cells[0].innerHTML = lineup_player[i]["p_id"];
											lineup_row.cells[4].innerHTML = lineup_player[i]["name"];
											lineup_row.cells[5].innerHTML = lineup_player[i]["opp"];
											lineup_row.cells[6].innerHTML = lineup_player[i]["fppg"];
											lineup_row.cells[7].innerHTML = lineup_player[i]["salary"];									
											
											img = document.createElement("img");
											img.src = "images/delete-player-icon.png";
											img.style.width = "20px";
											img.style.height = "20px";
											img.style.cursor = "pointer";
											lineup_row.cells[8].appendChild(img);
											img.addEventListener("click", remove_lineup_player);												
										}
															
										update_plist("PG");	
									}); 
								</script> ';
							} else if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
								
								echo'
									<script>
										$(document).ready(function() {
											
											
												/* page reloads and all data filled back into lineup_table 
												   due to unsucessful update of lineup */
												var sal_row = document.getElementById("salary-table").rows[0];	
												
												sal_row.cells[2].innerHTML = "$0";
												sal_row.cells[4].innerHTML = '.json_encode($_POST["sal_post_remain"]).';
												
												var lineup_table = document.getElementById("lineup-table");
												var img = "";
												for(var i=1, row; i < lineup_table.rows.length; i++)  {
											
													row = lineup_table.rows[i];
													switch(i) {
														
														case 1:  
																
															row.cells[0].innerHTML = '.json_encode($_POST["pg_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["pg_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["pg_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["pg_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["pg_post_salary"]).';
															break;
															
														case 2:
														
															row.cells[0].innerHTML = '.json_encode($_POST["sg_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["sg_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["sg_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["sg_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["sg_post_salary"]).';
															break;
															
														case 3:
														
															row.cells[0].innerHTML = '.json_encode($_POST["sf_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["sf_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["sf_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["sf_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["sf_post_salary"]).';
															break;	
															
														case 4:
														
															row.cells[0].innerHTML = '.json_encode($_POST["pf_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["pf_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["pf_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["pf_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["pf_post_salary"]).';
															break;	
															
														case 5:
														
															row.cells[0].innerHTML = '.json_encode($_POST["c_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["c_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["c_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["c_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["c_post_salary"]).';
															break;	
															
														case 6:
														
															row.cells[0].innerHTML = '.json_encode($_POST["g_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["g_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["g_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["g_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["g_post_salary"]).';
															break;	
															
														case 7:
														
															row.cells[0].innerHTML = '.json_encode($_POST["f_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["f_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["f_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["f_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["f_post_salary"]).';
															break;	
															
														case 8:
														
															row.cells[0].innerHTML = '.json_encode($_POST["util_post_id"]).';
															row.cells[4].innerHTML = '.json_encode($_POST["util_post_name"]).';
															row.cells[5].innerHTML = '.json_encode($_POST["util_post_opp"]).';
															row.cells[6].innerHTML = '.json_encode($_POST["util_post_fppg"]).';
															row.cells[7].innerHTML = '.json_encode($_POST["util_post_salary"]).';
															break;	

														case 9:
														
															row.cells[2].innerHTML = '.json_encode($_POST["fpts_post_total"]).';
															break;		
													}
													
													if(i != (lineup_table.rows.length - 1)) {
														img = document.createElement("img");
														img.src = "images/delete-player-icon.png";
														img.style.width = "20px";
														img.style.height = "20px";
														img.style.cursor = "pointer";
														row.cells[8].appendChild(img);
														img.addEventListener("click", remove_lineup_player);
													}
												}
																							
												update_plist("PG");			
										
										});	
									</script> ';
							}
						?>								
							<script>
							
								function remove_lineup_player() {
										
									pts_row = document.getElementById("lineup-table").rows[9];
									var cur_pts = pts_row.cells[2].innerHTML;
									var player_pts = $(this).closest("tr").children()[6].innerHTML;
									if(player_pts == "N/A")
											player_pts = "0.0";									
									adj_pts = parseFloat(cur_pts) -  parseFloat(player_pts);
									adj_pts = adj_pts.toFixed(1);
									pts_row.cells[2].innerHTML = adj_pts;
									
									
									// 1) update remaining salary for lineup selection 
									// 2) player removed from lineup after salary calculations completed
									var p_salary = $(this).closest("tr").children()[7].innerHTML;
									p_salary = p_salary.slice(1);	
									p_salary = p_salary.replace(",", "");	
									
									
									var sal_row = document.getElementById("salary-table").rows[0];
									var cur_sal = sal_row.cells[4].innerHTML;
									if(cur_sal.charAt(0) == "-") {
										cur_sal = cur_sal.replace(",", "");
										cur_sal = cur_sal.slice(2);
										cur_sal = "-" + cur_sal;								
									}
									else {
										
										cur_sal = cur_sal.slice(1);	
										cur_sal = cur_sal.replace(",", "");
									}
									
									var cur_sal_b4_calc = parseInt(cur_sal);
									
									var t_rem_salary = parseInt(cur_sal) + parseInt(p_salary);
																		
									var total_sal = t_rem_salary;
									
									var nfObject4 = new Intl.NumberFormat("en-US"); 
									t_rem_salary = nfObject4.format(t_rem_salary); 
									
									if(total_sal < 0) {
										t_rem_salary = t_rem_salary.replace("-", "-$");
										sal_row.cells[4].classList.add("red");
									}
									else {
										t_rem_salary = "$" + t_rem_salary;
										if(cur_sal_b4_calc < 0)
											sal_row.cells[4].classList.remove("red");
										sal_row.cells[4].classList.add("green");
									}
									sal_row.cells[4].innerHTML = t_rem_salary;
									
									// get p_id before selected row is emptied
									var selected_row = $(this).closest("tr");
									var p_id = selected_row[0].cells[0].innerHTML;
									
									// clear row data except for player position identifier
									var childs = $(this).closest("tr").children();
									var cur_position = childs[3].innerHTML;
									childs.empty();
									childs[3].innerHTML = cur_position;
									
									
									var players_picked = 0;
									var lineup_table = document.getElementById("lineup-table");
									for(var i =1; i < (lineup_table.rows.length - 1); i++){
										if(lineup_table.rows[i].cells[0].innerHTML != "")
											players_picked += 1;
									}
									
									var rem_picks = 8 - players_picked;
									
									// display unselected player again in select-table
									var select_table = document.getElementById("select-table");
									for(var p =1; p < select_table.rows.length; p++) {
										if (select_table.rows[p].cells[0].innerHTML == p_id ) {
											select_table.rows[p].style.display = "";
											break;
										}																		
									}	
																		
									// avg remaining salary calculated and updated in salary-table									
									if(total_sal <= 0) {
										var avg_sal = "$0";
										sal_row.cells[2].innerHTML = avg_sal;
									}
									else {
																					
										var rem_s = t_rem_salary.slice(1);
										rem_s = rem_s.replace(",", "");
										var avg_sal = math.round(parseInt(rem_s)/rem_picks);
										nfObject5 = new Intl.NumberFormat("en-US"); 
										avg_sal = nfObject5.format(avg_sal); 
										avg_sal = "$" + avg_sal;
										sal_row.cells[2].innerHTML = avg_sal;											
									}																
								}
																
								var lineup_btns = document.getElementsByClassName("lineup_btn");
								for(var i = 0; i < lineup_btns.length; i++) {
									lineup_btns[i].addEventListener('mousedown', e => {e.preventDefault();});						
								}
								
								function requirements_met()  {
									
									var met = false;
									var lineup_table = document.getElementById("lineup-table");
									var sal_row = document.getElementById("salary-table").rows[0];						
									var errors = [];
									
									// make sure all lineup positions are filled
									for(var i = 1; i < (lineup_table.rows.length - 1); i++) {
										if (lineup_table.rows[i].cells[0].innerHTML == "") {
											errors.push("All positions must be filled before lineup can be saved");
											break;
										}	
									}
									
									// check to make sure all lineup players are not playing in same game
									var opp = [];
									for(var i = 1; i < (lineup_table.rows.length -1); i++) {
											opp.push(lineup_table.rows[i].cells[5].innerHTML);								
									}
									
									function checkSameOpp(opponent) {
										
										opponent = opponent.replace("<strong>","");
										opponent = opponent.replace("</strong>","");
																	
										var opponent_standard = lineup_table.rows[1].cells[5].innerHTML;
										opponent_standard =  opponent_standard.replace("<strong>","");
										opponent_standard =  opponent_standard.replace("</strong>","");
										
										return opponent == opponent_standard;
									}
															
									var all_opp_same = opp.every(checkSameOpp);
									
									if (all_opp_same && (lineup_table.rows[1].cells[5].innerHTML != ""))
										errors.push("All selected lineup players can not be from the same game.");
									
									var rem_salary = sal_row.cells[4].innerHTML;
									if (rem_salary.charAt(0) == "-")
										errors.push("Salary cap exceeded. Please revise your lineup.");
									
									if (errors.length) 
										met = false; 
									else 
										met = true;
									
									if (!met) {
										
										if (errors[1] == null)
											errors[1] = "";
										var cust_alert = new CustomAlert();
										cust_alert.render2(errors[0], errors[1]);	
									}												
									return met;
								}
																	
								function save_lineup() {
									
									var req_met = requirements_met();
									if (req_met) {
										
										document.getElementById('sal_post_remain').value = document.getElementById('sal_remain').innerHTML;
										
										document.getElementById('pg_post_id').value = document.getElementById('pg_id').innerHTML;
										document.getElementById('pg_post_name').value = document.getElementById('pg_name').innerHTML;
										document.getElementById('pg_post_opp').value = document.getElementById('pg_opp').innerHTML;
										document.getElementById('pg_post_fppg').value = document.getElementById('pg_fppg').innerHTML;
										document.getElementById('pg_post_salary').value = document.getElementById('pg_salary').innerHTML;
										
										document.getElementById('sg_post_id').value = document.getElementById('sg_id').innerHTML;
										document.getElementById('sg_post_name').value = document.getElementById('sg_name').innerHTML;
										document.getElementById('sg_post_opp').value = document.getElementById('sg_opp').innerHTML;
										document.getElementById('sg_post_fppg').value = document.getElementById('sg_fppg').innerHTML;
										document.getElementById('sg_post_salary').value = document.getElementById('sg_salary').innerHTML;
										
										document.getElementById('sf_post_id').value = document.getElementById('sf_id').innerHTML;
										document.getElementById('sf_post_name').value = document.getElementById('sf_name').innerHTML;
										document.getElementById('sf_post_opp').value = document.getElementById('sf_opp').innerHTML;
										document.getElementById('sf_post_fppg').value = document.getElementById('sf_fppg').innerHTML;
										document.getElementById('sf_post_salary').value = document.getElementById('sf_salary').innerHTML;
										
										document.getElementById('pf_post_id').value = document.getElementById('pf_id').innerHTML;
										document.getElementById('pf_post_name').value = document.getElementById('pf_name').innerHTML;
										document.getElementById('pf_post_opp').value = document.getElementById('pf_opp').innerHTML;
										document.getElementById('pf_post_fppg').value = document.getElementById('pf_fppg').innerHTML;
										document.getElementById('pf_post_salary').value = document.getElementById('pf_salary').innerHTML;
										
										document.getElementById('c_post_id').value = document.getElementById('c_id').innerHTML;
										document.getElementById('c_post_name').value = document.getElementById('c_name').innerHTML;
										document.getElementById('c_post_opp').value = document.getElementById('c_opp').innerHTML;
										document.getElementById('c_post_fppg').value = document.getElementById('c_fppg').innerHTML;
										document.getElementById('c_post_salary').value = document.getElementById('c_salary').innerHTML;
										
										document.getElementById('g_post_id').value = document.getElementById('g_id').innerHTML;
										document.getElementById('g_post_name').value = document.getElementById('g_name').innerHTML;
										document.getElementById('g_post_opp').value = document.getElementById('g_opp').innerHTML;
										document.getElementById('g_post_fppg').value = document.getElementById('g_fppg').innerHTML;
										document.getElementById('g_post_salary').value = document.getElementById('g_salary').innerHTML;
										
										document.getElementById('f_post_id').value = document.getElementById('f_id').innerHTML;
										document.getElementById('f_post_name').value = document.getElementById('f_name').innerHTML;
										document.getElementById('f_post_opp').value = document.getElementById('f_opp').innerHTML;
										document.getElementById('f_post_fppg').value = document.getElementById('f_fppg').innerHTML;
										document.getElementById('f_post_salary').value = document.getElementById('f_salary').innerHTML;
										
										document.getElementById('util_post_id').value = document.getElementById('util_id').innerHTML;
										document.getElementById('util_post_name').value = document.getElementById('util_name').innerHTML;
										document.getElementById('util_post_opp').value = document.getElementById('util_opp').innerHTML;
										document.getElementById('util_post_fppg').value = document.getElementById('util_fppg').innerHTML;
										document.getElementById('util_post_salary').value = document.getElementById('util_salary').innerHTML;
										
										document.getElementById('fpts_post_total').value = document.getElementById('fpts_total').innerHTML;
																	
										document.forms["lineup_form"].submit();								
									}					
								}
																		
								function clear_lineup() {
									
									var lineup_table = document.getElementById("lineup-table");
									var position = "";
									for(var i =1; i < (lineup_table.rows.length - 1); i++) {
										position = lineup_table.rows[i].cells[3].innerHTML;
										for(var j = 0; j < 9; j++) {
											lineup_table.rows[i].cells[j].innerHTML = "";
										}										
										lineup_table.rows[i].cells[3].innerHTML = position;							
									}	
				
									var salary = document.getElementById("salary-table");	
									var sal_row = salary.rows[0];
									sal_row.cells[2].innerHTML = "$6,250";
									sal_row.cells[4].innerHTML = "$50,000";
									sal_row.cells[4].classList.remove('red');
									sal_row.cells[4].classList.add('green');
									
									lineup_table.rows[9].cells[2].innerHTML = "0.0";
								}
								
							</script>								
						</div>   <!-- last  column of row -->		
						
						<script>
							function add_pg(pg_id) {
								
								var lineup_table = document.getElementById("lineup-table");					
								var row = lineup_table.rows[1];	
								var pts_row = lineup_table.rows[9];
								var pg_array = <?php echo json_encode($pg_array); ?> ;
								
								for(var i = 0; i < pg_array.length; i++){
									
									if(pg_id == pg_array[i]["id"]) {
										
										if(row.cells[0].innerHTML == "") {
											var pg_table = document.getElementById("select-table");
											
											// keeps selected pg from being seen in select-table
											for(var p =1; p < pg_table.rows.length; p++) {
												if (pg_table.rows[p].cells[0].innerHTML == pg_id) {
													pg_table.rows[p].style.display = "none";
													break;
												}																		
											}
																																					
											/* 1) selected player at pg position updated in lineup-table
											   2) this code gets implemented ONLY IF player added at pg
											   position before user clicks on a button position 
											   located on the position-row; within the position-btns-table */
											   
											row.cells[0].innerHTML = pg_array[i]["id"];
											row.cells[1].innerHTML = pg_array[i]["pos_1"];
											row.cells[2].innerHTML = pg_array[i]["pos_2"];
																	
											row.cells[4].innerHTML = pg_array[i]["name"];
											
											var ht  = document.createTextNode(pg_array[i]["ht"]),
											at  = document.createTextNode("@");
											vt  = document.createTextNode(pg_array[i]["vt"]),
											bold = document.createElement('strong');
												
											if(pg_array[i]["ht_id"] == pg_array[i]["pt_id"]) {
												bold.appendChild(ht);
												
												row.cells[5].appendChild(vt);	
												row.cells[5].appendChild(at);
												row.cells[5].appendChild(bold);													
											}else {
												bold.appendChild(vt);
												row.cells[5].appendChild(bold);	
												row.cells[5].appendChild(at);
												row.cells[5].appendChild(ht);	
											}
											
											row.cells[6].innerHTML = pg_array[i]["fppg"];
											
											nfObject3 = new Intl.NumberFormat('en-US'); 
											pg_array_salary = nfObject3.format(pg_array[i]["salary"]); 
											
											row.cells[7].innerHTML = "$" + pg_array_salary;
											
											// listener for img click located further below
											row.cells[8].innerHTML = "";
											var img = document.createElement('img');
											img.src = "images/delete-player-icon.png";
											img.style.width = "20px";
											img.style.height = "20px";
											img.style.cursor = "pointer";
											row.cells[8].appendChild(img);
											
											if(pg_array[i]["fppg"] == "N/A")
												pts_row.cells[2].innerHTML = "0.0";
											else
												pts_row.cells[2].innerHTML = pg_array[i]["fppg"];
																			
											var player_id_selected = row.cells[0].innerHTML;
																			
											// remaining salary calculated and updated in salary-table
											var salary = document.getElementById("salary-table");
											var sal_row = salary.rows[0]; 
											var rem_salary = sal_row.cells[4].innerHTML;
											
											if(rem_salary.charAt(0) == "-") {
												rem_salary = rem_salary.replace(",", "");
												rem_salary = rem_salary.slice(2);
												rem_salary = "-" + rem_salary;								
												rem_salary = parseInt(rem_salary) - parseInt(pg_array[i]["salary"]);
											}
											else {
												rem_salary = rem_salary.slice(1);	
												rem_salary = rem_salary.replace(",", "");
												rem_salary = parseInt(rem_salary) - parseInt(pg_array[i]["salary"]);
											}
																		
											var sal = rem_salary;
											
											//format rem_salary to number with commas
											nfObject = new Intl.NumberFormat('en-US'); 
											rem_salary = nfObject.format(rem_salary); 
																																					
											if(sal < 0) {
												rem_salary = rem_salary.replace("$-", "-$");
												sal_row.cells[4].classList.add('red');									
											}
											else {	
												rem_salary = "$" + rem_salary;
												sal_row.cells[4].classList.add('green');
											}								
											sal_row.cells[4].innerHTML = rem_salary;
											
											
											// avg remaining salary calculated and updated in salary-table
											var avg_sal = sal_row.cells[2].innerHTML;
											
											if(sal <= 0) 
												avg_sal = "$0";
											else {
												var rem_s = rem_salary.slice(1);
												rem_s = rem_s.replace(",", "");
												
												
												var players_picked = 0;
												for(var i =1; i < (lineup_table.rows.length - 1); i++){
													if(lineup_table.rows[i].cells[0].innerHTML != "")
														players_picked += 1;
												}
												var rem_picks = 8 - players_picked;		
												
												
												
												
												avg_sal = math.round(parseInt(rem_s)/rem_picks);
												nfObject2 = new Intl.NumberFormat('en-US'); 
												avg_sal = nfObject2.format(avg_sal); 
												avg_sal = "$" + avg_sal;
												sal_row.cells[2].innerHTML = avg_sal;
											}	
																			
											img.addEventListener("click", function()     {
												
												var cur_pts = pts_row.cells[2].innerHTML;									
												var pg_pts;
												
												if(row.cells[6].innerHTML == "N/A")
													pg_pts = "0.0";
												else
													pg_pts = row.cells[6].innerHTML;
												
												var adj_pts = parseFloat(cur_pts) -  parseFloat(pg_pts);
												adj_pts = adj_pts.toFixed(1);
												pts_row.cells[2].innerHTML = adj_pts;
								
												
												var player_id = row.cells[0].innerHTML;
																					
												/*  1) update remaining salary for lineup selection 
													2) player removed from lineup after salary calculations completed */
												var p_salary = row.cells[7].innerHTML;
												p_salary = p_salary.slice(1);	
												p_salary = p_salary.replace(",", "");


												// changed sal to cur_sal
												var cur_sal = sal_row.cells[4].innerHTML;
												
												if(cur_sal.charAt(0) == "-") {
														cur_sal = cur_sal.replace(",", "");
														cur_sal = cur_sal.slice(2);
														cur_sal = "-" + cur_sal;								
													}
													else {
														
														cur_sal = cur_sal.slice(1);	
														cur_sal = cur_sal.replace(",", "");
													}
												
												var cur_sal_b4_calc = parseInt(cur_sal);
																				
												var t_rem_salary = parseInt(p_salary) + parseInt(cur_sal);
																					
												var total_sal = t_rem_salary;
												
												nfObject4 = new Intl.NumberFormat('en-US'); 
												t_rem_salary = nfObject4.format(t_rem_salary); 
												
												if(total_sal < 0) {
													t_rem_salary = t_rem_salary.replace("$-", "-$");
													sal_row.cells[4].classList.add('red');
												}
												else {
													t_rem_salary = "$" + t_rem_salary;
													if(cur_sal_b4_calc < 0)
														sal_row.cells[4].classList.remove('red');										
													sal_row.cells[4].classList.add('green');
												}
												sal_row.cells[4].innerHTML = t_rem_salary;
																					
												// clear row data except for PG identifying position
												$(this).closest('tr').children().empty();
												row.cells[3].innerHTML = "PG";
												
												players_picked = 0;
												// table is the lineup-table
												for(var i =1; i < (lineup_table.rows.length - 1); i++){
													if(lineup_table.rows[i].cells[0].innerHTML != "")
														players_picked += 1;
												}
												
												rem_picks = 8 - players_picked;
												
												// display unselected player again in select-table
												for(var p =1; p < pg_table.rows.length; p++) {
													if (pg_table.rows[p].cells[0].innerHTML == player_id) {
														pg_table.rows[p].style.display = "";
														break;
													}																		
												}	
																					
												// avg remaining salary calculated and updated in salary-table									
												if(total_sal <= 0) {
													avg_sal = "$0";
													sal_row.cells[2].innerHTML = avg_sal;
												} else {
													rem_s = t_rem_salary.slice(1);
													rem_s = rem_s.replace(",", "");
													avg_sal = math.round(parseInt(rem_s)/rem_picks);
													nfObject5 = new Intl.NumberFormat('en-US'); 
													avg_sal = nfObject5.format(avg_sal); 
													avg_sal = "$" + avg_sal;
													sal_row.cells[2].innerHTML = avg_sal;
												}							
											});
										}	
										else {
											var cust_alert = new CustomAlert();
											cust_alert.render("PG selection already made! Please remove current selection first.");	
										
										}
										
										break;							
									}
								}										
							}					
						</script>
				</div> <!--  end of row  -->			
		<?php	
			}			
																		
			include('includes/footer.php');?>
								
	</div> <!-- end container -->
		
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>