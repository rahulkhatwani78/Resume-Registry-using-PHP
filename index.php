<?php 
	session_start();
	require_once "pdo.php";
?>

<html>
	<head>
		<title>Rahul Anilkumar Khatwani's Resume Registry</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
		<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	</head>
	<body>
		<h1>Rahul Anilkumar Khatwani's Resume Registry</h1>
		<?php
			if(isset($_SESSION['success']))
			{
				echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
				unset($_SESSION['success']);
			}
			if(isset($_SESSION['error']))
			{
				echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
				unset($_SESSION['error']);
			}
			if(!isset($_SESSION['name']))
			{
				echo '<p><a href="login.php">Please log in</a></p>';
			}
			else
			{
				echo '<p><a href="logout.php">Logout</a></p>';	
			}
			$flag = true;
			$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				if($flag === true)
				{
					echo '<table border="1" style="border-collapse:collapse">';
					echo "<tr>";
					echo "<th>Name</th>";
					echo "<th>Headline</th>";
					if(isset($_SESSION['name']))
					{
						echo "<th>Action</th>";
					}
					echo "</tr>";
					$flag = false;
				}
				echo "<tr><td>";
				echo '<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name']." ".$row['last_name']."</a>";
				echo "</td><td>";
				echo $row['headline'];
				if(isset($_SESSION['name']))
				{
					echo "</td><td>";
					echo '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ';
					echo '<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>';
				}
				echo "</td></tr>";
			}
			echo "</table>";
			if(isset($_SESSION['name']))
			{
				echo '<p><a href="add.php">Add New Entry</a></p>';
			}
		?>
	</body>
</html>