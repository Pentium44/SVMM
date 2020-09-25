<?php
//	SVMM - Simple VM Manager - For Qemu KVM
//	(C) Chris Dorman, 2020
//	License: CC-BY-NC-SA version 3.0
//	http://github.com/Pentium44/SVMM

session_start();
include "config.php";
include "functions.php";

// check if flatfile database location is populated
if(!file_exists("svmm_db"))
{
	mkdir("svmm_db", 0777);
}

if(!file_exists("svmm_db/events"))
{
	mkdir("svmm_db/events", 0777);
}

if(!file_exists("svmm_db/disks"))
{
	mkdir("svmm_db/disks", 0777);
}

if(!file_exists("svmm_db/pids"))
{
	mkdir("svmm_db/pids", 0777);
}

if(!file_exists("svmm_db/users"))
{
	mkdir("svmm_db/users", 0777);
}

if(!file_exists("svmm_db/users/usercount"))
{
	file_put_contents("svmm_db/users/usercount", "9");
}

$username = $_SESSION['svmm-user'];

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title><?php echo $svmmtitle; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=.55, shrink-to-fit=yes"><meta name="description" content="<?php echo htmlentities($svmmtitle) . " - " . $desc; ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="maincontain">
<div id="navcontainer">
        <div id="navbar"><!--
        <?php if(isset($_SESSION['svmm-user']) && isset($_SESSION['svmm-pass'])) { ?>
	--><a href="index.php">Create</a><!--
        --><a href="?do=manage">Manage</a><!--
        --><a href="?do=about">About</a><!--
        --><a href="?do=logout">Logout</a><!--
	<?php } else {?>
        --><a href="?forms=login">Login</a><!--
        --><a href="?do=about">About</a><!--
        <?php } ?>
        --></div>
</div>
<div class='contain'>
<div class='title'><?php echo $svmmtitle; ?></div>

<?php

if(isset($_GET['forms']))
{
	$forms = $_GET['forms'];
	$id = $_GET['pid'];
	if($forms=="register") {
		registerForm();
	}
	else if($forms=="login") {
		loginForm();
	}
	else if($forms=="friendreq") {
		friendReqForm();
	}
	else if($forms=="changepass") {
		changePassForm();
	}
	else { echo "ERROR: Unknown form-name<br>"; }
}
else if(isset($_GET['notify']))
{
        $notify = $_GET['notify'];
        if($notify=="1") { echo "Error: User not found"; }
        else if($notify=="2") { echo "Error: Incorrect password provided"; }
        else if($notify=="3") { echo "Error: Please fill out all the text boxes"; }
        else if($notify=="4") { echo "Error: The provided passwords did not match"; }
        else if($notify=="5") { echo "Error: Special characters cannot be used in your username"; }
        else if($notify=="6") { echo "Error: This username is already in use"; }
        else { echo "Error: unknown error... this is quite unusual..."; }
}
else if(isset($_GET['do']))
{
	$do = $_GET['do'];
	// Server admin can just delete ssb_db
	/*if($do=="clean")
	{
		if($_POST['password']!="" && $_POST['password']==$pw)
		{
			$db_content = glob("ssb_db/" . '*', GLOB_MARK);
			foreach($db_content as $file)
			{
				unlink($file);
			}
			rmdir("ssb_db");
			echo "Database Cleaned<br>";
		}
		else
		{
			echo "ERROR: Wrong Password<br>";
		}
	}*/


	// grab session values and send friend request functions.
	if($do=="create") {
		if (!isset($_SESSION['svmm-user']) || !isset($_SESSION['svmm-pass'])) { loginForm(); } else {
			include("svmm_db/users/$username.php");
			if(!file_exists("svmm_db/disks/$userid.img")) {
				if(!copy("svmm_db/disks/clean.img", "svmm_db/disks/$userid.img")) 
				{
					echo "Error copying new disk image to user location... Please contact the system administrator!";
				}
				else
				{
					// Trigger event to start VM!
					file_put_contents("svmm_db/events/$userid", "./machine start $userid");
					echo "VM created! Refer to the user management panel for start / up info.";
				}
			} else {
				echo "Error: VM exists, please click &quot;Manage&quot; to start / stop your VM or to download a disk backup.";
			}
		}
	}
	
	if($do=="start") {
		if (!isset($_SESSION['svmm-user']) || !isset($_SESSION['svmm-pass'])) { loginForm(); } else {
			include("svmm_db/users/$username.php");
			if(file_exists("svmm_db/disks/$userid.img")) {
				if(!file_exists("svmm_db/users/$userid.pid.statuscode")) {
					echo "Pending: VM is pending creation, this process shouldn't take longer than 30 seconds...";
				} else {
					$vmstatus = file_get_contents("svmm_db/users/$userid.pid.statuscode");
					if($vmstatus == "false") {
						file_put_contents("svmm_db/events/$userid", "./machine start $userid");
						header("Location: index.php?do=manage");
					} else {
						echo "VM already running...";
					}
				}
			} else {
				echo "ERROR: VM not found!";
			}
		}
	}
	
	if($do=="stop") {
		if (!isset($_SESSION['svmm-user']) || !isset($_SESSION['svmm-pass'])) { loginForm(); } else {
			include("svmm_db/users/$username.php");
			if(file_exists("svmm_db/disks/$userid.img")) {
				if(!file_exists("svmm_db/users/$userid.pid.statuscode")) {
					echo "Pending: VM is pending creation, this process shouldn't take longer than 30 seconds...";
				} else {
					$vmstatus = file_get_contents("svmm_db/users/$userid.pid.statuscode");
					if($vmstatus == "true") {
						file_put_contents("svmm_db/events/$userid", "./machine stop $userid");
						header("Location: index.php?do=manage");
					} else {
						echo "VM already stopped...";
					}
				}
			} else {
				echo "ERROR: VM not found!";
			}
		}
	}
	
	if($do=="manage") {
		if (!isset($_SESSION['svmm-user']) || !isset($_SESSION['svmm-pass'])) { loginForm(); } else {
			include("svmm_db/users/$username.php");
			if(file_exists("svmm_db/disks/$userid.img")) {
				if(!file_exists("svmm_db/users/$userid.pid.status")) {
					echo "Pending: VM is pending creation, this process shouldn't take longer than 30 seconds...";
				} else {
					echo $username . "'s VM<br /> VM status: ";
					$vmstatus = file_get_contents("svmm_db/users/$userid.pid.status");
					echo $vmstatus;
					echo "<br /><a href='index.php?do=start' class='button'>Start</a>&nbsp;<a href='index.php?do=stop' class='button'>Stop</a>";
					echo "<br /><br />";
					echo "<b>Connection information (Via SSH):</b><br />";
					echo "<table><tr><td>IP/Port:</td><td> cddo.cf/" . $userid . "22</td></tr>";
					echo "<tr><td style='padding-right: 30px;'>Default root password: </td><td>root</td></tr></table><br />";
					echo "<b>Available ports for use:</b>";
					echo "<table><tr><td style='padding-right:30px;'>Server side port</td><td>External port (viewable)</td></tr>";
					echo "<tr><td>21</td><td>" . $userid . "21</td></tr>";
					echo "<tr><td>22</td><td>" . $userid . "22</td></tr>";
					echo "<tr><td>25565</td><td>" . $userid . "65</td></tr>";
					echo "<tr><td>6666</td><td>" . $userid . "66</td></tr>";
					echo "<tr><td>6667</td><td>" . $userid . "67</td></tr>";
					echo "<tr><td>80</td><td>" . $userid . "80</td></tr>";
					echo "</table>";
				}
			} else {
				echo "ERROR: VM not found!";
			}
		}
	}

	if($do=="about")
	{
		echo "<h2>About</h2>";
		echo $desc;
	}

	if($do=="login")
	{
		$username = $_POST['username'];
    		if(file_exists("svmm_db/users/$username.php")) {
			include_once("svmm_db/users/$username.php");
			if($user_password==sha1(md5($_POST['password']))) {
				$pass = $user_password;
				$user = $username;
				$color = $user_color;
				$_SESSION['svmm-user'] = $user;
				$_SESSION['svmm-pass'] = $pass;
				header("Location: index.php");
			} else {
				echo "Wrong password!";
			}
		} else {
			echo "User $username not found!";
		}
	}

	if($do=="logout")
	{
	        $_SESSION['svmm-user'] = null;
	        $_SESSION['svmm-pass'] = null;
		header("Location: index.php?forms=login");
	}

	if($do=="register")
	{
		if($_POST['username']!="" && $_POST['password']!="" && $_POST['password-again']!="" && $_POST['fullname']!="" && isset($_POST['email']) && $_POST['email']!="") {
			if($_POST['password']==$_POST['password-again']) {
				if(!preg_match('/[^a-z0-9]/i', $_POST['username'])) {
					if(!file_exists("svmm_db/users/" . $_POST['username'] . ".php")) {
						$vpscount = file_get_contents("svmm_db/users/usercount");
						if($vpscount < $maxvm)
						{
							if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
								$vpscount = $vpscount + 1;
								file_put_contents("svmm_db/users/usercount", $vpscount);
								file_put_contents("svmm_db/users/" . stripslashes(htmlentities($_POST['username'])) . ".php", "<?php\n\$user_handle = \"" . stripslashes(htmlentities($_POST['username'])) . "\";\n\$user_password = \"" . sha1(md5($_POST['password'])) . "\";\n \$user_email = \"" . stripslashes(htmlentities($_POST['email'])) . "\"; \$user_fullname = \"" . stripslashes(htmlentities($_POST['fullname'])) . "\"; \$userid = \"" . $vpscount . "\";\n?>");
								header("Location: index.php");
							} 
							else
							{
								echo "ERROR: Email is invalid!";
							}
						}
						else 
						{
							echo "ERROR: VPS cap reached!";
						}
					} else {
						header("Location: index.php?notify=6");
					}
				} else {
					header("Location: index.php?notify=5");
				}
			} else {
				header("Location: index.php?notify=4");
			}
		} else {
			header("Location: index.php?notify=3");
		}
	}
}
else if (!isset($_SESSION['svmm-user']) || !isset($_SESSION['svmm-pass']))
{
        loginForm();
}
else
{
	include("svmm_db/users/$username.php");
	if(!file_exists("svmm_db/disks/$userid.img"))
	{
		echo "<h3>Free VPS creation</h3>";
		echo "<p>Each user will have the ability to create a VM, and will have a consistent uptime unless FreeBox ends up being disabled due to malicious users improperly using the virtual machines</p>";
		echo "<ul><li>CPU: 10% of 1x Xeon E5649 core</li><li>RAM: 128MB dedicated</li><li>Disk: 10GB dedicated space</li><li>OS: Alpine GNU/Linux</li><li>Network: 50mbps down + 2mbps upload</li><li>Select available ports for server operation</li></ul>";
		echo "<a href='index.php?do=create' class='button'>Create a VPS</a>";
	}
	else
	{
		echo "You've been assigned a VPS, click &quot;Manage&quot; for more information on your server.";
	}
}

?>

<br /><br />
<center style="background-color: #555555; padding 3px;">Powered By SVMM <?php echo $version; ?></center>
</div>
</div> <!-- main contain -->
</body>
</html>
