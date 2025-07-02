<html>
<head><title>hello</title></head>
<body>

<!-- Form-->
<form action="" method="post" onsubmit="return check()">
Name:<input type="text" name="n" id="n1"><br> <!--  name -->
Age:<input type="number" name="a" id="a1"><br> <!-- age -->
Course:<input type="text" name="c" id="c1"><br> <!--  course -->
Gender:<input type="text" name="g" id="g1"><br> <!-- gender -->

<input type="submit" name="s" id="s1" value="submit"> <!-- Submit button -->
</form>

<!-- Form to display records -->
<form method="post" action="display.php">
<input type="submit" name="s1" id="s2" value="display"> <!-- Button to display -->
</form>

<!-- Form to update records -->
<form action="update.php">
<input type="submit" value="update?"> <!-- Button to update records -->
</form>

<!-- Form to delete records -->
<form action="delete.php">
<input type="submit" value="delete?"> <!-- Button to delete records -->
</form>

<script>
// form validation
function check() {
	let n=document.getElementById('n1').value; // Get name value
	let a=document.getElementById('a1').value; // Get age value
	let c=document.getElementById('c1').value; // Get course value
	let g=document.getElementById('g1').value; // Get gender value
	
	if(n=="" || n==null) { // Check if name is empty
		alert("enter name");
		return false;
	}else if(a<18) { // Check if age is less than 18
		alert("you can't apply for this course");
		return false;
	} else if (c=="") { // Check if course is empty
		alert("enter the course");
		return false;
	}
	return true; // If all validations pass
}
</script>

<?php
//  Check if form was submitted
if(isset($_POST["s"])) {
	$n=$_POST["n"]; // Get name
	$a=$_POST["a"]; // Get age
	$c=$_POST["c"]; // Get course
	$g=$_POST["g"]; // Get gender
	
	// Connect to MySQL database
	$conn=new mysqli("localhost","root","","mydb");
	if($conn->connect_error) {
		echo "<script>alert('unsuccessful')</script>"; // Connection failed
	}else {
		// SQL to insert record into table
		$sql="INSERT INTO stu (name,age,course,gender) VALUES('$n','$a','$c','$g')";
		if($conn->query($sql)===TRUE) {
			echo"<script>alert('successful')</script>"; // Insert successful
		} else {
			echo "<script>alert('unsuccessful')</script>"; // Insert failed
		}
	}
}
?>
</body>
</html>