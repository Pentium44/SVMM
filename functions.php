<?php
//      SVMM - Simple VM Manager - for Qemu KVM
//      (C) Chris Dorman, 2020
//      License: CC-BY-NC-SA version 3.0
//      http://github.com/Pentium44/SVMM

function loginForm() {
?>
        <br />
        <div class="login">
                <a class="button" href="<?php echo $_SERVER['PHP_SELF']; ?>?forms=register">Register</a>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?do=login" method="post">
			<table><tr><td>
                        Username:</td><td> <input class="text" type="text" name="username"></td></tr><tr><td>
                        Password:</td><td> <input class="text" type="password" name="password"></td></tr></table>
                        <input style="padding: 2px;" class="text" type="submit" name="submitBtn" value="Login">
                </form>
        </div>
<?php
}

function changePassForm() {
?>
        <br />
        <div class="chgpass">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?do=changepass" method="post">
			<table><tr><td>
                        Old password:</td><td> <input class="text" type="password" name="oldpass"></td></tr><tr><td>
                        Password:</td><td> <input class="text" type="password" name="password"></td></tr><tr><td>
			Password Again:</td><td> <input class="text" type="password" name="password_again"></td></tr>
			</table>
                        <input class="text" type="submit" name="submitBtn" value="Change">
                </form>
        </div>
<?php
}

/*function uploadForm() {

       	print <<<EOD
			Upload
			<table style="margin:auto;">
				
				<form action="upload.php" method="post" enctype="multipart/form-data">
				<tr>
					<td>
					<input type="file" name="file[]" id="file" multiple><br>
					</td>
					<td>
					<input type="submit" name="submit" value="Upload">
					</td>
				</tr>
				</form>
				
				</table>
EOD;

}*/

function registerForm() {
?>
        <br />
        <div class="login">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?do=register" method="post">
					<table><tr><td>
					Username:</td><td> <input class="text" type="text" name="username"></td></tr><tr><td>
					Full name:</td><td> <input class="text" type="text" name="fullname"></td></tr><tr><td>
					Email:</td><td><input class="text" type="text" name="email"></td></tr><tr><td>
					Password:</td><td> <input class="text" type="password" name="password"></td></tr><tr><td>
					Password Again:</td><td> <input class="text" type="password" name="password-again"></td></tr><tr><td>
						
                        <input class="text" type="submit" name="submitBtn" value="Register">
			</td></tr></table>
                </form>
        </div>
<?php
}
?>
