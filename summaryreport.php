<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title>LSS API Custom Reporter</title>
</head>
<style>
    .serif{font-family:"Times New Roman", Times, serif; font-size:12px;}
    .sansserif{font-family:Verdana, Geneva, sans-serif;}
    .footer{font-family:"Times New Roman", Times, serif; font-size:12px;}
</style>
<body>

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//ini_set('display_errors', 'Off');
error_reporting(E_ALL ^ E_NOTICE);

require_once 'includes/rest_connector2.php';
require_once 'includes/session.php';
require_once 'includes/sqldb1.php';

date_default_timezone_set('America/Los_Angeles');

// check to see if we start a new session or maintain current one
checksession();

$total=null; //needs to be global
// our rest object
$rest = new RESTConnector();

$config = array();

if (file_exists("config/config.php"))
{
    $tmpconfig = require("config/config.php");
    if (isset($tmpconfig['urlbase']))
        $config['urlbase'] = $tmpconfig['urlbase'];

    if (isset($tmpconfig['user']))
        $config['user'] = $tmpconfig['user'];

    if (isset($tmpconfig['pass']))
        $config['pass'] = $tmpconfig['pass'];

    if (isset($tmpconfig['mysqlserver']))
        $config['mysqlserver'] = $tmpconfig['mysqlserver'];

    if (isset($tmpconfig['mysqluser']))
        $config['mysqluser'] = $tmpconfig['mysqluser'];

    if (isset($tmpconfig['mysqlpass']))
        $config['mysqlpass'] = $tmpconfig['mysqlpass'];
}
else
    die("ERROR: Missing configuration parameters.");


// mysql login info
$link = mysqli_connect($config['mysqlserver'], $config['mysqluser'], $config['mysqlpass']) or die(mysqli_error($link));

checkdbexists($link);

$lup = getdatelastupdatedb($link);

$urlbase = $config['urlbase'];
//$urlbase = "https://10.70.0.95:9630/api/";

$sales = array();
$sales['all'] = 'invoices/';
$filter = "(datetime_cre > '".$lup."' OR datetime_mod > '".$lup."')";
//$filter = "(date_cre > '2013-09-01' OR datetime_mod > '2013-09-01')";
$sales['new'] = 'invoices/?filter='.rawurlencode($filter);

$allproducts = array();
$allproducts['all'] = 'products/';
$allproducts['new'] = 'products/?filter='.rawurlencode($filter);

$salesurl = $productsurl = null;

$classesurl = $urlbase.'classes/';

// START UPDATE DB
if (isset($_POST['reset']) || isset($_POST['update']) ) {

?>
<!-- Progress bar Classes -->
<div id="classprogress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress bar Classes -->
<div id="classinformation" ></div>

<!-- Progress bar Suppliers -->
<br />
<div id="supplierprogress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress bar Suppliers -->
<div id="supplierinformation" ></div>

<!-- Progress bar Product 1 -->
<br />
<div id="product1progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information Product 1 -->
<div id="product1information" ></div>

<!-- Progress bar Product 2 -->
<br />
<div id="product2progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information Product 2 -->
<div id="product2information" ></div>

<!-- Progress bar Invoices 1 -->
<br />
<div id="invoicesprogress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress bar Invoices Product 1 -->
<div id="invoicesinfo" ></div>

<!-- Progress bar Invoices 2 -->
<br />
<div id="invoices2progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress bar Invoices Product 2 -->
<div id="invoices2information" ></div>

<!-- Progress bar Invoices 2 -->
<br />
<div id="lineitemsprogress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress bar Invoices Product 2 -->
<div id="lineitemsinformation" ></div>

<br /><br />
<div id="completeinfo" ></div>
<?php


if (isset($_POST['reset'])) {
    deletedb($link);
    checkdbexists($link);
    $_POST['fromdate'] = null;
    //$_POST['todate'] = null;
    $salesurl = $urlbase.$sales['all'];
    $productsurl = $urlbase.$allproducts['all'];

}
elseif (isset($_POST['update'])) {
    $salesurl = $urlbase.$sales['new'];
    $productsurl = $urlbase.$allproducts['new'];
}

    //GET CLASSES
    $rest->createRequest($classesurl,"GET", null, $_SESSION['cookies'][0],$config['user'],$config['pass']);
    $rest->sendRequest();
    $response = $rest->getResponse();
    $error = $rest->getError();
    $exception = $rest->getException();

// save our session cookies
    if ($_SESSION['cookies']==null)
        $_SESSION['cookies'] = $rest->getCookies();

// display any error message and stop script
    if ($error!=null)
        die('CLASSES REST ERROR: '.$error);

// display any exception and stop script
    if ($exception!=null)
        die('CLASSES REST EXCEPTION: '.$exception);

    if ($response!=null || $response!="") {

        $temp = simplexml_load_string($response);
        $total = count($temp);
        $x=0;

        while ($temp->class[$x]){
//            $classestemp['resourceID'] = $temp->class[$x]->attributes()->uri;
//            $classestemp['name'] = $temp->class[$x]->name;
            updatedbclass($temp->class[$x],$link);

            $x++;

            // Calculate the percentation
            $percent = intval($x/$total * 100)."%";

            // Javascript for updating the progress bar and information
            echo '<script language="javascript">
    document.getElementById("classprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("classinformation").innerHTML="Classes added to database: '.$x.' of '.$total.'";
    </script>';

            // This is for the buffer achieve the minimum size in order to flush data
            echo str_repeat(' ',1024*64);

            // Send output to browser immediately
            flush();

            // Sleep one second so we can see the delay
//            usleep(2500);
        }
    }
    else
        echo "There was no response.";

    //ADD SUPPLIERS
    $suppliers = array( array('resourceID'=>13,'name'=>'Big Fireworks'),
                        array('resourceID'=>24,'name'=>'Brothers Pyrotechnics'),
                        array('resourceID'=>14,'name'=>'Hale Fireworks'),
                        array('resourceID'=>15,'name'=>'Hubbard Wholesale'),
                        array('resourceID'=>17,'name'=>"Jake's Fireworks"),
                        array('resourceID'=>18,'name'=>"Kellner's Fireworks"),
                        array('resourceID'=>19,'name'=>'North Central Industries'),
                        array('resourceID'=>12,'name'=>'Panda'),
                        array('resourceID'=>26,'name'=>'Pyro Planet'),
                        array('resourceID'=>20,'name'=>'Red Rhino'),
                        array('resourceID'=>21,'name'=>'Snap Fireworks'),
                        array('resourceID'=>22,'name'=>'Winco Fireworks')
                     );

        $total = count($suppliers);
        $x=0;

        while ($suppliers[$x]){
//            $classestemp['resourceID'] = $temp->class[$x]->attributes()->uri;
//            $classestemp['name'] = $temp->class[$x]->name;
            updatedbsuppliers($suppliers[$x],$link);

            $x++;

            // Calculate the percentation
            $percent = intval($x/$total * 100)."%";

            // Javascript for updating the progress bar and information
            echo '<script language="javascript">
    document.getElementById("supplierprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("supplierinformation").innerHTML="Suppliers added to database: '.$x.' of '.$total.'";
    </script>';

            // This is for the buffer achieve the minimum size in order to flush data
            echo str_repeat(' ',1024*64);

            // Send output to browser immediately
            flush();

            // Sleep one second so we can see the delay
//            usleep(2500);
        }

//GET PRODUCTS
$productstemp=array();
$rest->createRequest($productsurl,"GET", null, $_SESSION['cookies'][0],$config['user'],$config['pass']);
$rest->sendRequest();
$response = $rest->getResponse();
$error = $rest->getError();
$exception = $rest->getException();

// save our session cookies
if ($_SESSION['cookies']==null)
	$_SESSION['cookies'] = $rest->getCookies();

// display any error message and stop script
if ($error!=null)
	die('PRODUCTS REST ERROR: '.$error);

// display any exception and stop script
if ($exception!=null)
	die('PRODUCTS REST EXCEPTION: '.$exception);

if ($response!=null || $response!="") {

    $temp = simplexml_load_string($response);
    $total = count($temp);
    $x=0;

    while ($temp->product[$x]){
        $productstemp[$x] = $temp->product[$x]->attributes()->uri;

        $x++;

        // Calculate the percentation
        $percent = intval($x/$total * 100)."%";

        // Javascript for updating the progress bar and information
        echo '<script language="javascript">
    document.getElementById("product1progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("product1information").innerHTML="'.$x.' products counted";
    </script>';

        // This is for the buffer achieve the minimum size in order to flush data
        echo str_repeat(' ',1024*64);

        // Send output to browser immediately
        flush();

        // Sleep one second so we can see the delay
        // usleep(2500);
    }

    unset($temp);
}
else
	echo "There was no response.";



//GET EACH PRODUCT
foreach ($productstemp as $k => $r){
$rest->createRequest((string)$r,"GET", null, $_SESSION['cookies'][0],$config['user'],$config['pass']);
$rest->sendRequest();
$response = $rest->getResponse();
$error = $rest->getError();
$exception = $rest->getException();

// save our session cookies
if ($_SESSION['cookies']==null)
	$_SESSION['cookies'] = $rest->getCookies();

// display any error message and stop script
if ($error!=null)
	die('PRODUCT REST ERROR: '.$error);

// display any exception and stop script
if ($exception!=null)
	die('PRODUCT REST EXCEPTION: '.$exception);

if ($response!=null || $response!="") {

    $temp = simplexml_load_string($response);
    updatedbproducts($temp, $link);

    // Calculate the percentation
    $percent = intval(($k+1)/$total * 100)."%";

    // Javascript for updating the progress bar and information
    echo '<script language="javascript">
    document.getElementById("product2progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("product2information").innerHTML="Adding products to database: '.($k+1).' of '.$total.'";</script>';

    // This is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);

    // Send output to browser immediately
    flush();

    // Sleep one second so we can see the delay
    // usleep(2500);

}
else
	echo "There was no response.";
}

// GET INVOICES
$invoicestemp=array();
$rest->createRequest($salesurl,"GET", null, $_SESSION['cookies'][0],$config['user'],$config['pass']);
$rest->sendRequest();
$response = $rest->getResponse();
$error = $rest->getError();
$exception = $rest->getException();

// save our session cookies
if ($_SESSION['cookies']==null) 
	$_SESSION['cookies'] = $rest->getCookies();

// display any error message and stop script
if ($error!=null)
	die('INVOICES REST ERROR: '.$error);

// display any exception and stop script
if ($exception!=null)
	die('INVOICES REST EXCEPTION: '.$exception);

if ($response!=null || $response!="") {
    
    $temp = simplexml_load_string($response);
    $x=0;
    $total = count($temp);
//    echo 'Total invoices: '.$total.'<br><br>';

    while ($temp->invoice[$x]){
        $invoicestemp[$x] = $temp->invoice[$x]->attributes()->uri;
        $x++;

        // Calculate the percentation
        $percent = intval($x/$total * 100)."%";

        // Javascript for updating the progress bar and information
        echo '<script language="javascript">
    document.getElementById("invoicesprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("invoicesinfo").innerHTML="'.$x.' invoices counted";
    </script>';

        // This is for the buffer achieve the minimum size in order to flush data
        echo str_repeat(' ',1024*64);

        // Send output to browser immediately
        flush();

        // Sleep one second so we can see the delay
        // usleep(2500);
    }

    unset($temp);
}
else
	echo "There was no response.";


//$y=$z=0;
// WRITE SALES TO MYSQL DB

$j=0;
$lineitemstemp=array();
foreach ($invoicestemp as $k => $r){
$rest->createRequest((string)$r ,"GET", null, $_SESSION['cookies'][0],$config['user'],$config['pass']);
$rest->sendRequest();
$response = $rest->getResponse();
$error = $rest->getError();
$exception = $rest->getException();

// save our session cookies
if ($_SESSION['cookies']==null) 
	$_SESSION['cookies'] = $rest->getCookies();

// display any error message
if ($error!=null)
	die('INVOICE REST ERROR: '.$error);

if ($exception!=null)
	die('INVOICE REST EXCEPTION: '.$exception);

if ($response!=null || $response!="") {
    
    $temp = simplexml_load_string($response);
    
    $i=0;
    while ($temp->lineitems->lineitem[$i]){
        $lineitemstemp[$j]['uri'] = $temp->lineitems->lineitem[$i++]->attributes()->uri;
        $lineitemstemp[$j++]['invresid'] = $temp->attributes()->id;
        //updatedblineitems($temp->lineitems->lineitem[$i++], $link, $temp->attributes()->id);
    }
    
    updatedbinvoices($temp, $link, $i);

    // Calculate the percentation
    $percent = intval(($k+1)/$total * 100)."%";

    // Javascript for updating the progress bar and information
    echo '<script language="javascript">
    document.getElementById("invoices2progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("invoices2information").innerHTML="Adding invoices to database: '.($k+1).' of '.$total.'";</script>';

    // This is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);

    // Send output to browser immediately
    flush();
}
else
	echo "There was no response.";
}


// WRITE LINE ITEMS TO MYSQL DB

foreach ($lineitemstemp as $k => $r){
$rest->createRequest((string)$r['uri'] ,"GET", null, $_SESSION['cookies'][0],$config['user'],$config['pass']);
$rest->sendRequest();
$response = $rest->getResponse();
$error = $rest->getError();
$exception = $rest->getException();

// save our session cookies
if ($_SESSION['cookies']==null) 
	$_SESSION['cookies'] = $rest->getCookies();

// display any error message
if ($error!=null)
	die('LINEITEM REST ERROR: '.$error);

if ($exception!=null)
	die('LINEITEM REST EXCEPTION: '.$exception);

if ($response!=null || $response!="") {
    
    $temp = simplexml_load_string($response);
    
    updatedblineitems($temp, $link, $r['invresid']);

    // Calculate the percentation
    $percent = intval(($k+1)/$j * 100)."%";

    // Javascript for updating the progress bar and information
    echo '<script language="javascript">
    document.getElementById("lineitemsprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("lineitemsinformation").innerHTML="Adding lineitems to database: '.($k+1).' of '.$j.'";</script>';

    // This is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);

    // Send output to browser immediately
    flush();
   
}
else
	echo "There was no response.";
    
}

updatedbdate($link);

//    echo '<script language="javascript">
//    document.getElementById("invoices1progress").innerHTML="<div>&nbsp;</div>";
//    document.getElementById("invoices1information").innerHTML="'.null.'";</script>';
//    echo '<script language="javascript">
//    document.getElementById("product1sprogress").innerHTML="<div>&nbsp;</div>";
//    document.getElementById("products1information").innerHTML="'.null.'";</script>';
//    echo '<script language="javascript">
//    document.getElementById("product2sprogress").innerHTML="<div>&nbsp;</div>";
//    document.getElementById("products2information").innerHTML="'.null.'";</script>';
//    echo '<script language="javascript">
//    document.getElementById("invoices2progress").innerHTML="<div>&nbsp;</div>";
//    document.getElementById("invoices2information").innerHTML="'.null.'";</script>';
//    echo '<script language="javascript">
//    document.getElementById("lineitemsprogress").innerHTML="<div>&nbsp;</div>";
//    document.getElementById("lineitemsinformation").innerHTML="'.null.'";</script>';

//Tell user that the process is completed
    echo '<script language="javascript">
    document.getElementById("lineitemsinformation").innerHTML="'.null.'Process Complete! Click Back in browser to return to main menu.";</script>';

}
// END UPDATE DB

if (isset($_POST['generate'])){
$desc = array(0=>$_POST['desc'], 1=>$_POST['desc-comp']);
//$class = array(0=>$_POST('class'), 1=>$_POST('class-comp'));
//$family = array(0=>$_POST('family'), 1=>$_POST('family-comp'));
    
$summary = getsummary($link, $_POST['fromdate'], $_POST['todate'], $desc, null, null);

$i=0;
foreach ($summary as $k => $r) {
    
    $code[$i]= $r['code'];
    $des[$i]=  $r['description'];
    $sup[$i]=  $r['supplier'];
    //$sup[$i]=  'n/a';
    $supc[$i]= $r['supplier_code'];
    $clss[$i]= $r['classname'];
    //$clss[$i]= 'n/a';
    //$fam[$i]=  $r['family'];
    $fam[$i]=  $r['family'];
    $cost[$i]= $r['costav'];
    $onh[$i]= $r['onh'];
    $qty[$i]=  $r['quantity'];
    $totalsell[$i]=$r['totalsell'];
    $totalcost[$i]=$r['totalcost'];
    
    $i++;
}

array_multisort($des,      SORT_ASC,
                $code,       SORT_ASC,
                $sup,       SORT_ASC,
                $supc,      SORT_ASC,
                $clss,      SORT_ASC,
                $fam,       SORT_ASC,
                $cost,      SORT_ASC,
                $onh,       SORT_ASC,
                $qty,       SORT_ASC,
                $totalsell, SORT_ASC,
                $totalcost, SORT_ASC,
                $summary);
 
$totals=array();
 
 foreach ($summary as $k => $r) {
    if ($r['code']){
        $totals['onh'] += $r['onh'];
        $totals['quantity'] += $r['quantity'];
        $totals['total'] += $r['totalsell'];
        $totals['cogs'] += $r['totalcost'];
    }
 }
 
 $totals['margin'] = (($totals['total']-$totals['cogs'])*100.0)/$totals['total'];

$file=null;

//print_r($summary); die();

if (isset($_POST['export'])){
    
    $folder = 'export/';
    $file = 'summarysales-export-'.date('Ymd-His').'.txt';
    $textfile = $folder.$file;
    
    $tab = "\t"; $newline = "\r";
    $success = true;
    
    if (!$handle = fopen($textfile, 'a')) 
            log_error('Cannot open file: '.$textfile);
    
    if (fwrite($handle, 'Product Code'.utf8_encode($tab).
                        'Description'.utf8_encode($tab).
                        'Vendor'.utf8_encode($tab).
                        'Vendor Part #'.utf8_encode($tab).
                        'Class'.utf8_encode($tab).
                        'Family'.utf8_encode($tab).
                        'Qty on Hand'.utf8_encode($tab).
                        'Cost Av.'.utf8_encode($tab).
                        'Qty Sold'.utf8_encode($tab).
                        'COGS'.utf8_encode($tab).
                        'Total Sell'.utf8_encode($tab).
                        'Margin'.utf8_encode($newline)) === false){
       log_error('Cannot write to file: '.$file);
       $success = false;
    }

    $x=0;

//    print_r($summary[$x]);
//    echo "<br><br>";

//    while (isset($summary[$x]['code'])){
//        if (fwrite($handle, $summary[$x]['code'].utf8_encode($tab).
//                $summary[$x]['description'].utf8_encode($tab).
//                $summary[$x]['supplier'].utf8_encode($tab).
//                $summary[$x]['supplier_code'].utf8_encode($tab).
//                $summary[$x]['classname'].utf8_encode($tab).
//                $summary[$x]['family'].utf8_encode($tab).
//                $summary[$x]['onh'].utf8_encode($tab).
//                $summary[$x]['costav'].utf8_encode($tab).
//                $summary[$x]['quantity'].utf8_encode($tab).
//                $summary[$x]['totalcost'].utf8_encode($tab).
//                $summary[$x]['totalsell'].utf8_encode($tab).
//                $summary[$x]['totalsell']-$summary[$x]['totalcost'])*100.0/$summary[$x]['totalsell'].utf8_encode($tab).utf8_encode($newline) === false) {
//            log_error('Cannot write to file: '.$file);
//            $success = false;
//                        }
//        $x++;
//    }

    foreach ($summary as $k => $v) {
        if (isset($v['code']))
        if (fwrite($handle, $v['code'].utf8_encode($tab).
                        $v['description'].utf8_encode($tab).
                        $v['supplier'].utf8_encode($tab).
                        $v['supplier_code'].utf8_encode($tab).
                        $v['classname'].utf8_encode($tab).
                        $v['family'].utf8_encode($tab).
                        $v['onh'].utf8_encode($tab).
                        $v['costav'].utf8_encode($tab).
                        $v['quantity'].utf8_encode($tab).
                        $v['totalcost'].utf8_encode($tab).
                        $v['totalsell'].utf8_encode($tab).
                ($v['totalsell']-$v['totalcost'])*100.0/$v['totalsell'].utf8_encode($tab).utf8_encode($newline)) === false) {
            log_error('Cannot write to file: '.$file);
            $success = false;
                        }
    $x++;
    }

    if (fwrite($handle, utf8_encode($newline).
            utf8_encode($tab).utf8_encode($tab).utf8_encode($tab).
            utf8_encode($tab).utf8_encode($tab).
            'TOTALS'.utf8_encode($tab).
            $totals['onh'].utf8_encode($tab).
                           utf8_encode($tab).
            $totals['quantity'].utf8_encode($tab).
            $totals['cogs'].utf8_encode($tab).
            $totals['total'].utf8_encode($tab).
            (($totals['total']-$totals['cogs'])*100.0)/$totals['total'].utf8_encode($newline)) === false){
        log_error('Cannot write to file: '.$file);
        $success = false;
        $x += 2;
    }

    if ($success === true){
        log_action($x . ' rows added to file: '.$file);
        //echo 'Exported file: <b>'.$file.'</b><br><br>';
    }

    fclose($handle);
    
}

$y=0;
?>
    <table>
        <tr width="1000px">
            <td width="1000px">
                <h1>API Summarized Sales and Inventory by Product Report</h1>
            </td>
        </tr>
        <tr>
            <table>
                <tr width="100%">
                    <td width="54%">
                        <?php if (!($_POST['fromdate']=="") && !($_POST['todate'])=="") { ?>
                            Between <b><?php echo $_POST['fromdate']; ?></b> and <b><?php echo $_POST['todate']; ?></b>
                        <?php } else
                                if (!($_POST['fromdate']=="")) { ?>
                                    Between <b><?php echo $_POST['fromdate']; ?></b> and <b>Today</b>
                        <?php } else
                                if (!($_POST['todate']=="")) { ?>
                                    From the beginning until <b><?php echo $_POST['todate']; ?></b>
                        <?php } else
                                   { ?><b>All Dates</b><?php } ?>
                    </td>
                    <td style="float:right;">
                        Database last updated: <?php echo date('D jS M Y, g:i a', strtotime(getdatelastupdatedb($link))); ?>
                    </td>
                </tr>
            </table>
        </tr>
    </table>
    <br><br>
    <table class="serif">
        <tr style="background-color: #b0b0b0;">
            <td style="padding-left:5px;">Code&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Description&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Vendor&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Vendor Part #&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Class&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Family&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Qty On Hand&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Cost Av.&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Qty Sold&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">COGS&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Total Sell&nbsp;&nbsp;</td>
            <td style="padding-left:5px;">Margin %&nbsp;&nbsp;</td>
        </tr>
         <?php foreach ($summary as $k => $v){
             if ($v['code']){
             if ($y % 2 == 1) 
                 echo '<tr width="1000px" style="background-color: #ebebeb;">';
             else 
                 echo '<tr width="1000px">'; ?>
        <td style="padding-left:5px;padding-right:10px;"><?php echo $v['code']; ?></td>
        <td style="padding-left:5px;padding-right:10px;"><?php echo $v['description']; ?></td>
        <td style="padding-left:5px;padding-right:10px;"><?php echo $v['supplier']; ?></td>
        <td style="padding-left:5px;padding-right:10px;"><?php echo $v['supplier_code']; ?></td>
        <td style="padding-left:5px;padding-right:10px;"><?php echo $v['classname']; ?></td>
        <td style="padding-left:5px;padding-right:10px;"><?php echo $v['family']; ?></td>
        <td style="padding-left:5px;padding-right:5px;text-align:right;"><?php echo $v['onh']; ?></td>
        <td style="padding-left:5px;padding-right:5px;text-align:right;"><?php echo sprintf('%.2f', $v['costav']); ?></td>
        <td style="padding-left:5px;padding-right:5px;text-align:right;"><?php echo $v['quantity']; ?></td>
        <td style="padding-right:5px;padding-left:5px;text-align:right;"><?php echo sprintf('%.2f', $v['totalcost']); ?></td>
        <td style="padding-right:5px;padding-left:5px;text-align:right;"><?php echo sprintf('%.2f', $v['totalsell']); ?></td>
        <td style="padding-right:5px;padding-left:5px;text-align:right;"><?php if ($v['totalsell'] == 0) echo ''; else echo sprintf('%.2f', ($v['totalsell']-$v['totalcost'])*100.0/$v['totalsell']); ?></td>
        </tr>
        <?php $y++; }} ?>
        <tr>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td></td><td></td><td></td><td></td><td></td>
            <td><b>TOTALS</b></td>
            <td style="padding-right:5px;padding-left:5px;text-align:right;"><b><?php echo $totals['onh']; ?></b></td>
            <td></td>
            <td style="padding-right:5px;padding-left:5px;text-align:right;"><b><?php echo $totals['quantity']; ?></b></td>
            <td style="padding-right:5px;padding-left:5px;text-align:right;"><b><?php echo sprintf('%.2f', $totals['cogs']); ?></b></td>
            <td style="padding-right:5px;padding-left:5px;text-align:right;"><b><?php echo sprintf('%.2f', $totals['total']); ?></b></td>
            <td style="padding-right:5px;padding-left:5px;text-align:right;"><b><?php echo sprintf('%.2f', $totals['margin']); ?></b></td>
        </tr>
    </table>
    </table>
    <?php if (isset($_POST['export'])) { ?>
        <script type="text/javascript">window.alert("Exported file: <?php echo $file; ?>")</script>
    <?php } ?>
</body>
</html>
<?php } ?>