<?php
global $PHP_SELF;

echo "<H2>Login</H2>\n";
echo "<FORM METHOD=\"POST\" ACTION=\"$PHP_SELF?show=main\">\n";
echo "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"submitlogin\">\n";
echo "<INPUT TYPE=\"hidden\" NAME=\"show\" VALUE=\"main\">\n";
echo "<TABLE BORDER=\"0\" CELLSPACING=\"0\">\n";
echo "<TR><TD><B>Username</B></TD><TD><INPUT TYPE=\"text\" NAME=\"c_username\"></TD></TR>\n";
echo "<TR><TD><B>Password</B></TD><TD><INPUT TYPE=\"password\" NAME=\"c_password\"></TD></TR>\n";
echo "</TABLE>\n";
echo "<INPUT TYPE=\"submit\" VALUE=\"Login\">\n";
echo "</FORM>\n";
?>
