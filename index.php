<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>LSS API Login</title>
    <style>
        .serif{font-family:"Times New Roman", Times, serif;}
        .sansserif{font-family:Verdana, Geneva, sans-serif;}
        .footer{font-family:"Times New Roman", Times, serif;
            font-size:12px;}
    </style>
</head>
<body>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: geecue22
 * Date: 2013-10-07
 * Time: 7:43 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<form method="post" action="begin.php">
    <table width="650px">
        <tr>
            <td>
                <p align="center">
                    <img src="images/tiwtterlogo.jpg" width="20%" height="20%"/>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <h3 class="sansserif" align="center">LSS API Custom Reporter</h3>
                <h2 class="sansserif" align="center">Login</h2>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td align="center">
                <table>
                    <tr>
                        <td class="sansserif">LightSpeed Server</td>
                        <td style="padding-left: 18px">
                            <input type="text" maxlength="15" size="15" name="server" value="localhost" />
                        </td>
                    </tr>
                    <tr>
                        <td class="sansserif">LightSpeed Port</td>
                        <td style="padding-left: 18px">
                            <input type="text" maxlength="4" size="4" name="port" value="9630" />
                        </td>
                    </tr>
                    <tr>
                        <td class="sansserif">LightSpeed Username</td>
                        <td style="padding-left: 18px">
                            <input type="text" maxlength="25" size="25" name="user" />
                        </td>
                    </tr>
                    <tr>
                        <td class="sansserif">LightSpeed Password</td>
                        <td style="padding-left: 18px">
                            <input type="password" maxlength="25" size="25" name="pass" />
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="sansserif">MySQL Server</td>
                        <td style="padding-left: 18px">
                            <input type="text" maxlength="15" size="15" name="mysqlserver" value="localhost" />
                        </td>
                    </tr>
                    <tr>
                        <td class="sansserif">MySQL Username</td>
                        <td style="padding-left: 18px">
                            <input type="text" maxlength="25" size="25" name="mysqluser" />
                        </td>
                    </tr>
                    <tr>
                        <td class="sansserif">MySQL Password</td>
                        <td style="padding-left: 18px">
                            <input type="password" maxlength="25" size="25" name="mysqlpass" />
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="LOGIN">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
</body>
</html>