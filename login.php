<?php
	session_start();
	if(isset($_POST['cancel']))
	{
	    header("Location: index.php");
	    return;
	}

	require_once "pdo.php";
	
	if(isset($_POST['email']) && isset($_POST['pass']))
	{
		$salt = 'XyZzy12*_';
	    $check = hash('md5', $salt.$_POST['pass']);
		$stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
		$stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row !== false)
		{
			$_SESSION['name'] = $row['name'];
			$_SESSION['user_id'] = $row['user_id'];
			header("Location: index.php");
			return;
		}

	    else
	    {
	        $_SESSION['error'] = "Incorrect password";
	        header("Location: login.php");
	        return;
	    }
	}
?>

<html>
	<head>
		<title>Rahul Anilkumar Khatwani's Login Page</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
		<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	</head>
	<body>
		<h1>Please Log In</h1>
		<?php
			if(isset($_SESSION['error']))
			{
				echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
				unset($_SESSION['error']);
			}
		?>
		<form method="post">
			<p>Email <input type="text" name="email" id="email"></p>
			<p>Password <input type="password" name="pass" id="pass"></p>
			<p>
				<input type="submit" value="Log In" onclick="return doValidate();">
				<input type="submit" value="Cancel" name="cancel">
			</p>
		</form>
		<p>For a password hint, view source and find an account and password hint in the HTML comments.</p>
		<!-- The password is the programming language of this specialization (all lower case) followed by 123 -->
		<script type="text/javascript">
			function doValidate()
			{
				var email = document.getElementById("email").value;
				var pass = document.getElementById("pass").value;
				if(email.length<1 || pass.length<1)
				{
					alert("Both fields must be filled out");
					return false;
				}
				else if(!email.includes("@"))
				{
					alert("Invalid email address");
					return false;
				}
				else
				{
					return true;
				}
			}
		</script>
	</body>
</html>