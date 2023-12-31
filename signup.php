<?php
session_start();
require 'config/dbconfig.php';
require_once 'class/class.user.php';
require_once 'class/class.error.php';

$reg_user = new USER();
$reg_error = new ErrorRep();

if($reg_user->is_logged_in()!="")
{
	$reg_user->redirect('my/home.php');
}


if(isset($_POST['btn-signup']))
{
	$uname = addslashes($_POST['txtuname']);
	$email = addslashes($_POST['txtemail']);
	$upass = addslashes($_POST['txtpass']);
	$code = md5(uniqid(rand()));
	if(filter_var($email,FILTER_VALIDATE_EMAIL)) 
	{	
	$stmt = $reg_user->runQuery("SELECT * FROM tbl_users WHERE userEmail=:email_id");
	$stmt->execute(array(":email_id"=>$email));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if($stmt->rowCount() > 0)
	{
		header("Location: signup.php?error=4B");
		exit;
	}
	else
	{
		if($reg_user->register($uname,$email,$upass,$code))
		{			
			$id = $reg_user->lasdID();		
			$key = base64_encode($id);
			$id = $key;
			
			$message = "					
						Hello $uname,
						<br /><br />
						Welcome to Coding Cage!<br/>
						To complete your registration  please , just click following link<br/>
						<br /><br />
						<a href='http://localhost/x/verify.php?id=$id&code=$code'>Click HERE to Activate :)</a>
						<br /><br />
						Thanks,";
						
			$subject = "Confirm Registration";
						
			$reg_user->send_mail($email,$message,$subject);	
			header("Location: signup.php?success=5B");
		    exit;
		}
		else
		{
			header("Location: signup.php?error=1C");
		    exit;
		}		
	}
	}
	else
	{
		header("Location: signup.php?error=2C");
	}
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Signup | Coding Cage</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="assets/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  </head>
  <body id="login">
    <div class="container">
      <form class="form-signin" method="post">
	   <?php
        if(isset($_GET['error']))
		{
			?>
            <div class='alert alert-warning'>
				<button class='close' data-dismiss='alert'>&times;</button>
				<strong><?php $reg_error->ectt($_GET['error']); ?></strong> 
			</div>
            <?php
		}
		?>
	<?php
        if(isset($_GET['success']))
		{
			?>
            <div class='alert alert-success'>
				<button class='close' data-dismiss='success'>&times;</button>
				<strong><?php $reg_error->ectt($_GET['success']); ?></strong> 
			</div>
            <?php
		}
		?>
        <h2 class="form-signin-heading">Sign Up</h2><hr />
        <input type="text" class="input-block-level" placeholder="Name" name="txtuname" required />
        <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
        <input type="password" class="input-block-level" placeholder="Password" name="txtpass" required />
     	<hr />
        <button class="btn btn-large btn-primary" type="submit" name="btn-signup">Sign Up</button>
        <a href="login.php" style="float:right;" class="btn btn-large">Sign In</a>
      </form>

    </div> <!-- /container -->
    <?php include("template/misc.php") ?>
  </body>
</html>