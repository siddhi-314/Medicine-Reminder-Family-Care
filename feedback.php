<?php require 'config.php'; require 'header.php'; $uid=$_SESSION['user_id']; $msg="";
if(isset($_POST['submit'])){ $s=$conn->prepare("INSERT INTO feedback(user_id,rating,message) VALUES(?,?,?)"); $s->bind_param("iis",$uid,$_POST['rating'],$_POST['message']); $s->execute(); $msg="Thank you for your feedback."; }
?>
<div class="container"><div class="form-card"><h2>Feedback</h2><?php if($msg): ?><div class="notice successmsg"><?=htmlspecialchars($msg)?></div><?php endif; ?>
<form method="post"><label>Rating</label><select name="rating"><option value="5">5 - Excellent</option><option value="4">4 - Very Good</option><option value="3">3 - Good</option><option value="2">2 - Needs Improvement</option><option value="1">1 - Poor</option></select><label>Message</label><textarea name="message" rows="5" required></textarea><button class="btn" name="submit">Submit Feedback</button></form></div></div>
