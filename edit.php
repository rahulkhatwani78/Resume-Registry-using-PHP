<?php
	session_start();

	if(!isset($_SESSION['name']) || !isset($_SESSION['user_id']))
		die("ACCESS DENIED");

	if(isset($_POST['cancel']))
	{
	    header("Location: index.php");
	    return;
	}

	require_once "pdo.php";

	for($i=1; $i<=9; $i++)
	{
		if(isset($_POST['edu_year'.$i]) && isset($_POST['edu_school'.$i]))
		{
			if(strlen($_POST['edu_year'.$i])<1 || strlen($_POST['edu_school'.$i])<1)
			{
				$_SESSION['error'] = "All fields are required";
				header("Location: edit.php?profile_id=".$_GET['profile_id']);
				return;	
			}

			if(!is_numeric($_POST['edu_year'.$i]))
			{
				$_SESSION['error'] = "Education year must be numeric";
				header("Location: edit.php?profile_id=".$_GET['profile_id']);
				return;
			}
		}
	}

	for($i=1; $i<=9; $i++)
	{
		if(isset($_POST['year'.$i]) && isset($_POST['desc'.$i]))
		{
			if(strlen($_POST['year'.$i])<1 || strlen($_POST['desc'.$i])<1)
			{
				$_SESSION['error'] = "All fields are required";
				header("Location: edit.php?profile_id=".$_GET['profile_id']);
				return;	
			}

			if(!is_numeric($_POST['year'.$i]))
			{
				$_SESSION['error'] = "Position year must be numeric";
				header("Location: edit.php?profile_id=".$_GET['profile_id']);
				return;
			}
		}
	}

	if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
	{
		if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1)
		{
			$_SESSION['error'] = "All fields are required";
			header("Location: edit.php?profile_id=".$_GET['profile_id']);
			return;
		}
		else if(strpos($_POST['email'], "@") === false)
		{
			$_SESSION['error'] = "Email address must contain @";
			header("Location: edit.php?profile_id=".$_GET['profile_id']);
			return;
		}
		else
		{
			$stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pi');
			$stmt->execute(array(
				':fn' => $_POST['first_name'],
				':ln' => $_POST['last_name'],
				':em' => $_POST['email'],
				':he' => $_POST['headline'],
				':su' => $_POST['summary'],
				':pi' => $_POST['profile_id']));
			
			$stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
			$stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
			
			$rank = 1;
			for($i=1; $i<=9; $i++)
			{
				if ( ! isset($_POST['year'.$i]) ) continue;
				if ( ! isset($_POST['desc'.$i]) ) continue;
				
				$year = htmlentities($_POST['year'.$i]);
				$desc = htmlentities($_POST['desc'.$i]);
				$stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
					
				$stmt->execute(array(
				':pid' => $_REQUEST['profile_id'],
				':rank' => $rank,
				':year' => $year,
				':desc' => $desc));
					
				$rank++;
			}
			
			$stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
			$stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
			
			$rank = 1;
			for($i=1; $i<=9; $i++)
			{
				if ( ! isset($_POST['edu_year'.$i]) ) continue;
				if ( ! isset($_POST['edu_school'.$i]) ) continue;
				
				$stmt = $pdo->prepare('SELECT * FROM institution WHERE name = :n');
				$stmt->execute(array(':n' => $_POST['edu_school'.$i]));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row === false)
				{
					$stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
					$stmt->execute(array(':name' => $_POST['edu_school'.$i]));
					$iid = $pdo->lastInsertId();
				}
				else
				{
					$iid = $row['institution_id'];
				}

				$stmt = $pdo->prepare('INSERT INTO education VALUES ( :pid, :iid, :rank, :year)');
				$stmt->execute(array(
					':pid' => $_REQUEST['profile_id'],
					':iid' => $iid,
					':rank' => $rank,
					':year' => htmlentities($_POST['edu_year'.$i])));
				$rank++;
			}

			$_SESSION['success'] = "Profile updated";
			header("Location: index.php");
			return;
		}
	}

	$stmt = $pdo->prepare('SELECT profile_id, first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :zip');
	$stmt->execute(array(':zip' => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row === false)
	{
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}
	$first_name = htmlentities($row['first_name']);
	$last_name = htmlentities($row['last_name']);
	$email = htmlentities($row['email']);
	$headline = htmlentities($row['headline']);
	$summary = htmlentities($row['summary']);
	$profile_id = $row['profile_id'];
?>

<html>
	<head>
		<title>Rahul Anilkumar Khatwani's Profile Edit</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
		<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	</head>
	<body>
		<h1>Editing Profile for <?= $_SESSION['name'] ?></h1>
		<?php
			if(isset($_SESSION['error']))
			{
				echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
				unset($_SESSION['error']);
			}
		?>
		<form method="post">
			<p>First Name: <input type="text" name="first_name" size="60" value="<?= $first_name ?>"></p>
			<p>Last Name: <input type="text" name="last_name" size="60" value="<?= $last_name ?>"></p>
			<p>Email: <input type="text" name="email" size="30" value="<?= $email ?>"></p>
			<p>
				Headline:<br/>
				<input type="text" name="headline" size="80" value="<?= $headline ?>">
			</p>
			<p>
				Summary:<br/>
				<textarea name="summary" cols="80" rows="8"><?= $summary ?></textarea>
			</p>
			<p>
				Education: <input type="submit" id="addEdu" value="+">
				<div id="edu_fields">
					<?php
						$countEducation = 0;
						$stmt = $pdo->prepare("SELECT year, institution_id FROM education WHERE profile_id = :pid");
						$stmt->execute(array(':pid' => $_GET['profile_id']));
						while($row = $stmt->fetch(PDO::FETCH_ASSOC))
						{
							$countEducation++;
							$stmt2 = $pdo->prepare("SELECT name FROM institution WHERE institution_id = :i");
							$stmt2->execute(array(':i' => $row['institution_id']));
							$school = $stmt2->fetch(PDO::FETCH_ASSOC);
							echo '<div id="education'.$countEducation.'">'."\n";
							echo '<p>Year: <input type="text" value="'.htmlentities($row['year']).'" name="edu_year'.$countEducation.'"> ';
							echo '<input type="button" value="-" onclick="$(\'#education'.$countEducation.'\').remove();">'."</p>\n";
							echo '<p>School: <input type="text" value="'.$school['name'].'" name="edu_school'.$countEducation.'"'."</p>\n";
							echo "</div>\n";
						}
					?>
				</div>
			</p>
			<p>
				Position: <input type="submit" id="addPos" value="+">
				<div id="position_fields">
					<?php
						$countPosition = 0;
						$stmt = $pdo->prepare("SELECT year, description FROM position WHERE profile_id = :pid");
						$stmt->execute(array(':pid' => $_GET['profile_id']));
						while($row = $stmt->fetch(PDO::FETCH_ASSOC))
						{
							$countPosition++;
							echo '<div id="position'.$countPosition.'">'."\n";
							echo '<p>Year: <input type="text" value="'.htmlentities($row['year']).'" name="year'.$countPosition.'"> ';
							echo '<input type="button" value="-" onclick="$(\'#position'.$countPosition.'\').remove();">'."</p>\n";
							echo '<p><textarea name="desc'.$countPosition.'" rows="8" cols="80">'.htmlentities($row['description']).'</textarea></p>';
							echo "</div>\n";
						}
					?>
				</div>
			</p>
			<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
			<p>
				<input type="submit" value="Save">
				<input type="submit" value="Cancel" name="cancel">
			</p>
		</form>
		<script>
			countEdu = <?= $countEducation ?>;
			countPos = <?= $countPosition ?>;

			$(document).ready(function(){
				window.console && console.log('Document ready called');
				
				$('#addEdu').click(function(event){
        			event.preventDefault();
        			if ( countEdu >= 9 ) {
            			alert("Maximum of nine education entries exceeded");
            			return;
        			}
        			countEdu++;
        			window.console && console.log("Adding education "+countEdu);

       				$('#edu_fields').append(
            			'<div id="edu'+countEdu+'"> \
            			<p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            			<input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            			<p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            			</p></div>'
        			);

        			$('.school').autocomplete({ source: "school.php" });
				});

				$('#addPos').click(function(event){
					event.preventDefault();

					if ( countPos >= 9 ) {
						alert("Maximum of nine position entries exceeded");
						return;
					}
					countPos++;
					window.console && console.log("Adding position "+countPos);
					$('#position_fields').append(
						'<div id="position'+countPos+'"> \
						<p>Year: <input type="text" name="year'+countPos+'" value="" /> \
						<input type="button" value="-" \
							onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
						<textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
						</div>');
				});
			});
		</script>
	</body>
</html>