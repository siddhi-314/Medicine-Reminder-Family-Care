<?php
require 'config.php'; $error="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']); $email=trim($_POST['email']); $password=$_POST['password'];
    if(strlen($password)<6) $error="Password must be at least 6 characters.";
    else{
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $stmt=$conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
        $stmt->bind_param("sss",$name,$email,$hash);
        if($stmt->execute()){ header("Location: login.php"); exit; }
        $error="Could not create account. Email may already exist.";
    }
}
?>
<!DOCTYPE html><html><head><title>Register</title><link rel="stylesheet" href="style.css"></head><body>
<div class="login card"><h2>Create Account</h2>
<?php if($error): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post"><label>Name</label><input name="name" required>
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required minlength="6">
<button class="btn" type="submit">Register</button></form>
<p><a href="login.php">Back to Login</a></p></div></body></html>
