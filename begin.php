<?php
/*
 *	srindex.php
 *
 *	user interface page
 */

    ini_set('display_errors', 'On');

    require_once 'includes/sqldb1.php';
    require_once 'includes/session.php';
    require_once 'includes/rest_connector2.php';

    checksession();

    $rest = new RESTConnector;

    $urlbase = 'https://'.$_POST['server'].':'.$_POST['port'].'/api/';

    $url = $urlbase.'users/?filter=';
    $filter = '(username CONTAINS[cd] "'.$_POST['user'].'")';
    $rest->createRequest($url.rawurlencode($filter),"GET",null,$_SESSION['cookies'][0],$_POST['user'],$_POST['pass']);
    $rest->sendRequest();
    $response = $rest->getResponse();
    $error = $rest->getError();
    $exception = $rest->getException();

    // save our session cookies
    if ($_SESSION['cookies']==null)
        $_SESSION['cookies'] = $rest->getCookies();

    // display any error message
    if ($error!=null)
        die('LOGIN ERROR: '.$error);

    // display any error message
    if ($error!=null)
        die('LOGIN EXCEPTION: '.$exception);

    if ($response!=null || $response!="")
    {
        $temp = simplexml_load_string($response);

        writeconfig($_POST['server'],
                    $_POST['port'],
                    $_POST['user'],
                    $_POST['pass'],
                    $_POST['mysqlserver'],
                    $_POST['mysqluser'],
                    $_POST['mysqlpass'],
                    $temp->user->name->first,
                    $temp->user->name->last);
    }

    // Connect to mysql
    $link = mysqli_connect($_POST['mysqlserver'], $_POST['mysqluser'], $_POST['mysqlpass']) or die(mysqli_error($link));

    $tempdate = getdatelastupdatedb($link);

    if ($tempdate!='n/a')
        $datelastupdated = date('D jS M Y, g:i a', strtotime($tempdate));
    else
        $datelastupdated = $tempdate;

    $today = getdate();

    mysqli_close($link);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>LSS API Custom Reporter</title>
	<style>
		.serif{font-family:"Times New Roman", Times, serif;}
		.sansserif{font-family:Verdana, Geneva, sans-serif;}
		.footer{font-family:"Times New Roman", Times, serif;
				font-size:12px;}
	</style>
        <script language="JavaScript" src="htmlDatePicker004/htmlDatePicker.js" type="text/javascript"></script>
        <link href="htmlDatePicker004/htmlDatePicker.css" rel="stylesheet" />
</head>
<body>
<form action="summaryreport.php" method="post">
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
                                <h2 class="sansserif" align="center">Summarized Sales and Inventory</h2>
			</td>
		</tr>
		<tr>
			<td>
			<table border="0"  width="100%">
				<tr>
					<td style="padding-right:20px; width:50%">
					<p align="right" class="sansserif"><i>database last updated</i></p>
					</td>
					<td style="padding-left:20px; width:50%">
					<p class="sansserif"><?php echo $datelastupdated; ?></p>
					</td>
				</tr>
			</table>
			</td>
		</tr>
                <tr>
                    <td>
                        <table border="0" width="100%">
                            <tr>
                                <td style="padding-right:20px; width:25%">
				<p align="center" class="sansserif">From</p>
                                </td>
                                <td style="width:25%;">
                                    <input type="text" name="fromdate" id="FromDate" value="<?php echo $today['year'].'/'.$today['mon'].'/'.$today['mday']; ?>" readonly onClick="GetDate(this);" />
                                </td>
                                <td style="padding-left:20px; width:25%">
				<p align="center" class="sansserif">To</p>
                                </td>
                                <td style="width:25%;">
                                    <input type="text" name="todate" id="ToDate" value="<?php echo $today['year'].'/'.$today['mon'].'/'.$today['mday']; ?>" readonly onClick="GetDate(this);" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
		<tr>
			<td>
			<table border="0" width="100%">
				<tr>
					<td style="padding-right:20px; width:33%">
						<p align="right" class="sansserif"><i>product description</i></p>
					</td>
					<td>
						<p align="center">
						<select name="desc-comp">
							<option value="contains">contains</option>
							<option value="notcontain">does not contain</option>
						</select>
						</p>
					</td>
					<td style="padding-left:20px; width:33%">
						<input type="text" name="desc" maxlength="40" size="30"/>
					</td>
				</tr>
			</table>
			</td>
		</tr><!--
		<tr>
			<td>
			<table border="0" width="100%">
				<tr>
					<td style="padding-right:20px; width:33%">
						<p align="right" class="sansserif"><i>class</i></p>
					</td>
					<td>
						<p align="center">
						<select name="class-comp">
							<option value="contains">contains</option>
						<option value="notcontain">does not contain</option>
						</select>
						</p>
					</td>
					<td style="padding-left:20px; width:33%">
						<input type="text" name="class" maxlength="40" size="30"/>
					</td>
				</tr>
			</table>
			</td>
		</tr>
                <tr>
			<td>
			<table border="0" width="100%">
				<tr>
					<td style="padding-right:20px; width:33%">
						<p align="right" class="sansserif"><i>family</i></p>
					</td>
					<td>
						<p align="center">
						<select name="family-comp">
							<option value="contains">contains</option>
						<option value="notcontain">does not contain</option>
						</select>
						</p>
					</td>
					<td style="padding-left:20px; width:33%">
						<input type="text" name="family" maxlength="40" size="30"/>
					</td>
				</tr>
			</table>
			</td>
		</tr>-->
		<tr>
			<td>
			<table border="0"  width="100%">
				<tr>
					<td style="padding-right:20px; width:33%">
					<p align="right" class="sansserif"><i>export report</i></p>
					</td>
					<td style="padding-left:43px;">
						<input type="checkbox" name="export">
					</td>
				</tr>
			</table>
			</td>
		</tr>
        <!--
		<tr>
			<td>
			<table border="0"  width="100%">
				<tr>
					<td style="padding-right:20px; width:33%">
					<p align="right" class="sansserif"><i>update products/invoices</i></p>
					</td>
					<td style="padding-left:43px;">
						<input type="checkbox" name="update" checked>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table border="0"  width="100%">
				<tr>
					<td style="padding-right:20px; width:33%">
					<p align="right" class="sansserif"><i>reset database</i></p>
					</td>
					<td style="padding-left:43px;">
						<input type="checkbox" name="reset">
					</td>
				</tr>
			</table>
			</td>
		</tr>
        -->
        <tr>
            <td>
                <p align="center">
                    <input type="submit" value="Update database" name="update">
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <p align="center">
                    <input type="submit" value="Reset database" name="reset">
                </p>
            </td>
        </tr>
		<tr>
			<td>
				<p align="center">
				<input type="submit" value="GENERATE REPORT" name="generate">
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<p align="center" class="footer">About | <a href="mailto:ldx@lightspeedretail.com">Support</a></p>
			</td>
		</tr>
	</table>
</form>
</body>
</html>