<?php
session_start();
//print_r($_SESSION);
session_unset();
session_destroy();
echo "<script language=\"javascript\">";
echo "alert(\"Successfully logout!\");";
echo "location.href=\"index.html\";";
echo"</script>";

?>