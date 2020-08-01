<?php
	session_start();

	if(!isset($_GET['profile_id']))
	{
		$_SESSION['error'] = "Missing profile_id";
		header("Location: index.php");
		return;
	}

	require_once "pdo.php";
?>

<html>
	<head>
		<title>Rahul Anilkumar Khatwani's Profile View</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
		<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	</head>
	<body>
		<h1>Profile Information</h1>
		<?php
			$stmt =  $pdo->prepare("SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :xyz");
			$stmt->execute(array(':xyz' => $_GET['profile_id']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row !== false)
			{
				echo "<p>First Name: ".htmlentities($row['first_name'])."</p>\n";
				echo "<p>Last Name: ".htmlentities($row['last_name'])."</p>\n";
				echo "<p>Email: ".htmlentities($row['email'])."</p>\n";
				echo "<p>Headline:<br/>".htmlentities($row['headline'])."</p>\n";
				echo "<p>Summary:<br/>".htmlentities($row['summary'])."</p>\n";

				$stmt =  $pdo->prepare("SELECT year, name FROM education JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id = :xyz");
				$stmt->execute(array(':xyz' => $_GET['profile_id']));
				$flag = true;
				while($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					if($flag==true)
					{
						echo "<p>Education</p><ul>\n";
						$flag = false;
					}
					echo "<li>".htmlentities($row['year']).": ".htmlentities($row['name'])."</li>\n";
				}
				echo "</ul>";

				$stmt =  $pdo->prepare("SELECT year, description FROM position WHERE profile_id = :xyz");
				$stmt->execute(array(':xyz' => $_GET['profile_id']));
				$flag = true;
				while($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					if($flag==true)
					{
						echo "<p>Position</p><ul>\n";
						$flag = false;
					}
					echo "<li>".htmlentities($row['year']).": ".htmlentities($row['description'])."</li>\n";
				}
				echo "</ul>";

				echo '<p><a href="index.php">Done</a></p>';
			}
		?>
	</body>
</html>