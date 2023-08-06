<?php
session_start();
require '../config/dbconfig.php';
require_once '../class/class.user.php';
require_once '../class/class.money.php';
require_once '../class/class.error.php';

$user_home = new USER();
$money_home = new Money();
$error_home = new ErrorRep();
if(!$user_home->is_logged_in())
{
	$user_home->redirect('../index.php');
}

$stmt = $user_home->runQuery("SELECT * FROM tbl_users WHERE userID=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);


if(isset($_POST['transfer']))
	if (filter_var(addslashes($_POST['email']),FILTER_VALIDATE_EMAIL)) 
    {
		if ((filter_var(addslashes($_POST['balance']), FILTER_VALIDATE_INT)) AND ($_POST['balance']>0))
		{
			$sentemail = addslashes($row['userEmail']);
			$pass = addslashes($_POST['password']);
			$receemail = addslashes($_POST['email']);
			$ccash = addslashes($row['userBalance']);
			$balance = addslashes($_POST['balance']);
			$content = addslashes($_POST['content']);
			$money_home->transfercash($sentemail,$pass,$receemail,$ccash,$balance,$content);
		}
		else 
		{
			header("Location: transfer.php?error=3C");
		}
	}
	else
	{
		    header("Location: transfer.php?error=2C");
	}
	
   
?>

<!DOCTYPE html>
<html class="no-js">
    
    <head>
        <title>Chuyển tiền & thanh toán - PiggyBank</title>
        <?php include("../template/header.php") ?>
    </head>
    <body>
		<?php include("../template/menu_ac.php") ?>
		 <div class="container">
	</br></br>
    <center><h1 style="font-size: 30px; ">Chuyển tiền & thanh toán</h1>
	<p style="font-weight: 400; width:50%;">Chuyển tiền cho người thân, bạn bè,... của bạn bằng cách nhập email tài khoản PiggyBank của người nhận. Việc chuyển tiền trong hệ thống hoàn toàn miễn phí.</p></center>
    </br>
   <?php
        if(isset($_GET['error']))
			if ($error_home->exist($_GET['error'])) 
				{
					?>
						<div class='alert alert-warning'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<strong><?php $error_home->ectt($_GET['error']); ?></strong> 
						</div>
					<?php
				}
					?>
	   <?php
        if(isset($_GET['success']))
			if ($error_home->exist($_GET['success'])) 
				{
					?>
						<div class='alert alert-success'>
						<button class='close' data-dismiss='success'>&times;</button>
						<strong><?php $error_home->ectt($_GET['success']); ?></strong> 
						</div>
					<?php
				}
					?>
  <form method="post">
  <div class="form-group">
    <label for="email">Email người nhận</label>
    <input type="email" class="form-control" name="email" required autocomplete="off">
  </div>
  <div class="form-group">
    <label>Tiền chuyển</label>
	<div class="input-group">
        <input type="number" class="form-control" name="balance" required autocomplete="off">
        <span class="input-group-addon">đ</span>
    </div>
  </div>
  <div class="form-group">
  <label>Nội dung chuyển</label>
  <textarea class="form-control" rows="5" name="content" required></textarea>
</div>
  <div class="form-group">
    <label>Mật khẩu xác minh</label>
    <input type="password" class="form-control" name="password" required>
  </div>
  <button type="submit" name="transfer" class="btn btn-default">Chuyển tiền</button>
</form>
</br>
<p style="color: #01AB4F"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Giao dịch của bạn hiện đang được bảo mật bởi SSL </p>
  
  </div>
<?php include("../template/footer_ac.php") ?>
<?php include("../template/misc.php") ?>
        
    </body>

</html>