<!doctype html>
<html>
<head>
<link href="style.css" rel="stylesheet"/>
<meta charset="utf-8">
<title>นำสินค้าเข้า</title>
</head>

<body>
<!--<?php phpinfo(); ?>-->
<form method="post" enctype="multipart/form-data">
	<center><div id="top">นำสินค้าเข้า</div></center>
	<label for="fname">อัปโหลดรูปสินค้า (ขนาดประมาณ 100 KB เท่านั้น)</label>
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000"> <!-- ประมาณ 100 KB -->
    <input type="file" name="file"><br><br>
	<label for="fname">ชื่อสินค้า</label>
  	<input type="text" id="fname" name="name_prod"><br><br>
  	<label for="lname">รายละเอียด</label>
  	<input type="text" id="lname" name="details_prod"><br><br>
  	<label for="lname">ราคาสินค้า</label>
  	<input type="text" id="lname" name="price_prod"><br><br>
  	<label for="lname">จำนวนสินค้า</label>
  	<input type="number" id="quantity" name="quantity_prod" min="1" max="10"><br><br> 
  	<center><input type="submit" class='button button1' value="บันทึกข้อมูล"><br><br>
  	<div id="top">
  	<a href='read-img.php ' class='button button2'>เเสดงสินค้า</a></div></center>
	</div>
</form>
<?php
//connect database
$link = @mysqli_connect("localhost", "root", "", "stock") or die(mysqli_connect_error()."</body></html>");
/*เชครีเฟช*/
$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
/*เมื่อเข้าหน้านี้ครั้งเเรก จะข้ามไปก่อน*/
if($pageWasRefreshed ) {

	if(is_uploaded_file($_FILES['file']['tmp_name'])) {
	$e = $_FILES['file']['error'];
		//ถ้าเป็นเลขที่ไม่ใช่ 0 แสดงว่าเกิดข้อผิดพลาด
		if($e != 0) {  
			$msg = "";
			if($e == 1) {$msg = "ไฟล์ที่อัปโหลดมีขนาดเกินกว่าขนาด upload_max_filesize (".ini_get("upload_max_filesize").")";}
			else if($e == 2) {$max = round($_POST['MAX_FILE_SIZE'] /1000);  //โดยประมาณเท่านั้น ความจริงต้องหารด้วย 1024 
							  $msg = "ไฟล์ที่อัปโหลดมีขนาดเกินกว่าค่า  MAX_FILE_SIZE (".$max." KB)";}
			else if($e == 3) {$msg = "ไฟล์ถูกอัปโหลดมาไม่ครบ";}
			else if($e == 4) {$msg = "ไม่มีไฟล์อัปโหลดมา";}
			else {$msg = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";}
				echo '<span class="err">'.$msg.'</span>';
		}else{/*ถ้าไม่มีข้อผิดพลาด*/
			@mkdir("images"); //ถ้ายังไม่มีไดเร็กทอรี ให้สร้างขึ้นใหม่
			
			$target = "images/".$_FILES['file']['name'];
			if(!file_exists($target)) {/*ถ้าไม่เจอไฟล์ หรือชื่อไฟล์ที่ซ้ำกัน*/
				$pic_prod = $_FILES['file']['name'];/*รับชื่อไฟล์รูป*/
				move_uploaded_file($_FILES['file']['tmp_name'], $target);/*ทำการย้ายไฟล์ไปยังโฟลเดอร์*/
			}else{/*ถ้าเจอไฟล์ที่ซ้ำกัน สร้างชื่อไฟล์ขึ้นมาใหม่*/
					$oldname = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);/*ชื่อไฟล์เดิม*/
					$ext =  pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);/*นามสกุลไฟล์เดิม*/
					do{
						$r = rand();
						$newname = $oldname."_".$r.".$ext";
						$target = "images/$newname";
						if(!file_exists($target)){/*ถ้าไม่เจอไฟล์ หรือชื่อไฟล์ที่ซ้ำกัน*/
							$pic_prod = $newname;/*รับชื่อไฟล์รูป(ชื่อใหม่)*/
							move_uploaded_file($_FILES['file']['tmp_name'], $target);
							break;/*ออกจากลูป*/
						}
					}while(file_exists($target));
			}
			//echo "<h3>จัดเก็บไฟล์รูปเรียบร้อยแล้ว</h3>";
		}
	}
	/*ป้องกันการบันทึกข้อมูล เมื่อกดรีเฟช*/
	if($_POST["name_prod"] != ""){
	$name_prod=$_POST["name_prod"];
	$details_prod=$_POST["details_prod"];
	$price_prod=$_POST["price_prod"];
	$quantity_prod=$_POST["quantity_prod"];
	$sql = "INSERT INTO product_tb VALUES ('', '$pic_prod', '$name_prod', '$details_prod', '$price_prod', '$quantity_prod');";
			mysqli_query($link, $sql);
			echo "<h3>บันทึกสินค้าเรียบร้อยแล้ว</h3>";
			mysqli_close($link);
	}

}
?>
</body>
</html>
