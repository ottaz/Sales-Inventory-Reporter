<?php
/*
 * Administers all SQL database procedures.
 * 
 */

//ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);

require_once 'errorlogs.php';

date_default_timezone_set('America/Los_Angeles');
 
/*
 * deletedb(arg)
 * 
 * arg = mysql db connection info
 * 
 * If customer chooses to Reset their data, this function will execute
 * It deletes both tables from the database but not the database itself
 */

function deletedb($link) {
    
   // if (mysqli_connect_errno($link))
	//exit("Could not connect" . mysqli_connect_error());
    //else {
        //log_action("connected successfully: mysql");
        
        
        mysqli_select_db($link, "sales_db");
        
        //if (mysql_query("DROP TABLE IF EXISTS Invoices", $link))
        if (mysqli_query($link, "DROP TABLE IF EXISTS Invoices"))        
            ;//log_action("deleted table: Invoices");
        else
            log_error("Error deleting table: " . mysqli_error($link));
        
        //if (mysql_query("DROP TABLE IF EXISTS Classes", $link))
        if (mysqli_query($link, "DROP TABLE IF EXISTS Classes"))
            ;//log_action("deleted table: Classes");
        else
            log_error("Error deleting table: " . mysqli_error($link));

        if (mysqli_query($link, "DROP TABLE IF EXISTS Suppliers"))
            ;//log_action("deleted table: Classes");
        else
            log_error("Error deleting table: " . mysqli_error($link));

        //if (mysql_query("DROP TABLE IF EXISTS Lineitems", $link))
        if (mysqli_query($link, "DROP TABLE IF EXISTS Lineitems"))
            ;//log_action("deleted table: Lineitems");
        else
            log_error("Error deleting table: " . mysqli_error($link));
        
        //if (mysql_query("DROP TABLE IF EXISTS Products", $link))
        if (mysqli_query($link, "DROP TABLE IF EXISTS Products"))
            ;//log_action("deleted table: Products");
        else
            log_error("Error deleting table: " . mysqli_error($link));
        
        //if (mysql_query("DROP TABLE IF EXISTS LastUpdated", $link))
        if (mysqli_query($link, "DROP TABLE IF EXISTS LastUpdated"))
            ;//log_action("deleted table: Last Updated");
        else
            log_error("Error deleting table: " . mysqli_error($link));
        
        //if (mysql_query("DROP TABLE IF EXISTS Suppliers", $link))
        if (mysqli_query($link, "DROP TABLE IF EXISTS Suppliers"))
            ;//log_action("deleted table: Suppliers");
        else
            log_error("Error deleting table: " . mysqli_error($link));
            
 //   }
}

/*
 * checkdbexists(arg)
 * 
 * arg = mysql db connection info
 * 
 * Check to ensure mysql can be accessed, that the db
 * exists and that the tables exist. If the db and/or
 * tables do not exist, they are created.
 * 
 */

function checkdbexists($link) {
	
//if (mysqli_connect_errno($link))
//	exit("Could not connect" . mysqli_connect_error());
//else {
	//;//log_action("connected successfully: mysql");
        
	//if (mysql_query("CREATE DATABASE IF NOT EXISTS sales_db", $link))
        if (mysqli_query($link, "CREATE DATABASE IF NOT EXISTS sales_db"))
		    //;
		    log_action("connected successfully: sales_db");
	else {
		log_error("Error checking/creating sales_db: " . mysqli_error($link));
		mysqli_close($link);
		log_action("Disconnected Successfully...");
		exit();
	}
			
	mysqli_select_db($link, "sales_db");
	
        $sql = "CREATE TABLE IF NOT EXISTS Classes (" .
                    "rowID int NOT NULL AUTO_INCREMENT, " .
                    "PRIMARY KEY (rowID), " .
                    "resourceID INT UNSIGNED, ".
                    "name VARCHAR(50))";
    
        if (mysqli_query($link, $sql))
		    //;
		    log_action("connected successfully: Classes table");
	else {
		log_error("Error executing Classes table check query: " . mysqli_error($link));
		mysqli_close($link);
		//;
		log_action("Disconnected Successfully...");
		exit();
	}
        
        $sql = "CREATE TABLE IF NOT EXISTS Suppliers (" .
                    "rowID int NOT NULL AUTO_INCREMENT, " .
                    "PRIMARY KEY (rowID), " .
                    "resourceID INT UNSIGNED, ".
                    "name VARCHAR(50))";
    
        if (mysqli_query($link,$sql))
		;//log_action("connected successfully: Suppliers table");
	else {
		log_error("Error executing Suppliers table check query: " . mysqli_error($link));
		mysqli_close($link);
		//;
		log_action("Disconnected Successfully...");
		exit();
	}
        
        $sql = "CREATE TABLE IF NOT EXISTS Products (" .
                    "rowID int NOT NULL AUTO_INCREMENT, " .
                    "PRIMARY KEY (rowID), " .
                    "resourceID INT UNSIGNED, ".
                    "code VARCHAR(50), ".
                    "description VARCHAR(200), ".
                    "classID INT UNSIGNED, ".
                    "family VARCHAR(50), ".
                    "costav FLOAT UNSIGNED, ".
                    "sell_base FLOAT UNSIGNED, ".
                    "supplierID INT UNSIGNED, ".
                    "supplier_code varchar(50), ".
                    "total_qty FLOAT, ".
                    "available_qty FLOAT, ".
                    "warehouse_qty FLOAT)";
                    
        if (mysqli_query($link,$sql))
		    //;
		    log_action("connected successfully: Products table");
	else {
		log_error("Error executing Products table check query: " . mysqli_error($link));
		mysqli_close($link);
		//;
		log_action("Disconnected Successfully...");
		exit();
	}
        
        $sql = "CREATE TABLE IF NOT EXISTS Invoices (" .
                    "rowID int NOT NULL AUTO_INCREMENT, " .
                    "PRIMARY KEY (rowID), " .
                    "resourceID INT UNSIGNED, " .
                    "invoiceid VARCHAR(12), " .
                    "datecreated DATETIME, " .
                    "datemodified DATETIME, " .
                    "items INT UNSIGNED)";
		   	
	if (mysqli_query($link,$sql))
		//;
		log_action("connected successfully: Invoices table");
        
	else {
		log_error("Error executing Invoices table check query: " . mysqli_error($link));
		mysqli_close($link);
		//;
		log_action("<br>Disconnected Successfully...");
		exit();
	}
        
        $sql = "CREATE TABLE IF NOT EXISTS Lineitems (" .
                    "rowID int NOT NULL AUTO_INCREMENT, " .
                    "PRIMARY KEY (rowID), " .
                    "invoiceresID INT UNSIGNED, " .
                    "productresID INT UNSIGNED, ".
                    "resourceID INT UNSIGNED, " .
                    "cost FLOAT, " .
                    "sell FLOAT, " .
                    "quantity FLOAT, " .
                    "totalsell FLOAT, ".
                    "totalcost FLOAT)";
                    
		   	
	if (mysqli_query($link,$sql))
		//;
		log_action("connected successfully: Lineitems table");
        
	else {
		log_error("Error executing Lineitems table check query: " . mysqli_error($link));
		mysqli_close($link);
		//;
		log_action("<br>Disconnected Successfully...");
		exit();
	}
        
        $sql = "CREATE TABLE IF NOT EXISTS LastUpdated (" .
                    "rowID int NOT NULL AUTO_INCREMENT, " .
                    "PRIMARY KEY (rowID), " .
                    "date DATETIME)";
    
        if (mysqli_query($link,$sql))
		//;
		log_action("connected successfully: LastUpdated table");
	else {
		log_error("Error executing LastUpdated table check query: " . mysqli_error($link));
		mysqli_close($link);
		//;
		log_action("Disconnected Successfully...");
		exit();
	}

    //}
}



function updatedbclass($array, $link){
    if ($array)
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error());
    else {
	//log_action('connected successfully: mysql');
        
        mysqli_select_db($link, "sales_db");
        
        $updatequery = "UPDATE Classes SET name='".$array->name."' ".
                        "WHERE resourceID='".$array->attributes()->id."'; ".
                        "SELECT row_count();";
            
	if ($re = mysqli_query($link, $updatequery))
            while ($result[] = mysqli_fetch_array($re, MYSQLI_ASSOC));
            
        if ($result[0][0]==1)
            ;//log_action('Record updated successfully: Class - '.$array->name);

        else {
            
            $insertquery =  "INSERT INTO Classes (resourceID, name) ".
                            "VALUES (".$array->attributes()->id.",".
                                     "'".$array->name."')";
                            
            if (mysqli_query($link, $insertquery))
                ;//log_action('Record added successfully: Class - '.$array->name);
            else
                log_error("Error inserting class: " . mysqli_error($link));
        }
    }
}

function updatedbsuppliers($array, $link){
    if ($array)
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error());
    else {
	//log_action('connected successfully: mysql');

        mysqli_select_db($link, "sales_db");

        $updatequery = "UPDATE Suppliers SET name=? ".
                        "WHERE resourceID=?;";

        $stmt = mysqli_stmt_init($link);
        $check = mysqli_stmt_prepare($stmt, $updatequery);

        if ($check)
            mysqli_stmt_bind_param($stmt, 'si',
                                        $array['name'],
                                        $array['resourceID']
                                  );
        else
            die(mysqli_error ($link));

        $checkquery = "SELECT rowID FROM Suppliers WHERE resourceID=".$array['resourceID'].";";
        $cq = mysqli_query($link, $checkquery);
        $row = mysqli_fetch_assoc($cq);

        //if (!mysqli_query($link, $updatequery))
        if (!mysqli_stmt_execute($stmt))
            log_error("Error with Product update query: " . mysqli_error($link));
        else
            if ($row['rowID'])
                ;//log_action('Record updated successfully: Product '.$array->code);
            else {

                $insertquery =  "INSERT INTO Suppliers (resourceID, name) ".
                                "VALUES (?,?);";

                $stmt = mysqli_stmt_init($link);
                $check = mysqli_stmt_prepare($stmt, $insertquery);

                if ($check)
                    mysqli_stmt_bind_param($stmt, 'is',
                        $array['resourceID'],
                        $array['name']
                    );
                else
                    die(mysqli_error ($link));

                if (mysqli_stmt_execute($stmt))
                    ;//log_action('Record added successfully: Class - '.$array->name);
                else
                    log_error("Error inserting supplier: " . mysqli_error($link));
        }
    }
}

function updatedbproducts($array, $link){
    if ($array){
   // if (mysqli_connect_errno($link))
	//exit("Could not connect" . mysqli_connect_error());
    //else {
	//log_action('connected successfully: mysql');
        
        mysqli_select_db($link, "sales_db");
        
        if ($array->supplier->attributes()->id)
            $supid = $array->supplier->attributes()->id;
        else
            $supid = "''";
        /*
        $updatequery = "UPDATE Products SET code='".$array->code."', ".
                                            "description='".str_replace("'","''",$array->description)."', ".
                                            //"description='".$array->description."', ".
                                            "sell_base=".$array->sell_price.", ".
                                            "costav=".$array->costs->average.", ".
                                            "classID=".$array->class->attributes()->id.", ".
                                            "family='".$array->family."', ".
                                            "supplierID=".$supid. ", ".
                                            "supplier_code='".$array->supplier_code."', ".
                                            "total_qty=".$array->inventory->total.", ".
                                            "available_qty=".$array->inventory->available.", ".
                                            "warehouse_qty=".$array->inventory->warehouses." ".
                        "WHERE resourceID=".$array->attributes()->id."; ";
        //*/
        $updatequery = "UPDATE Products SET code=?, ".
                                           "description=?, ".
                                           "sell_base=?, ".
                                           "costav=?, ".
                                           "classID=?, ".
                                           "family=?, ".
                                           "supplierID=?, ".
                                           "supplier_code=?, ".
                                           "total_qty=?, ".
                                           "available_qty=?, ".
                                           "warehouse_qty=? ".
                       "WHERE resourceID=?;";
                
        $stmt = mysqli_stmt_init($link);
        $check = mysqli_stmt_prepare($stmt, $updatequery);
        
        if ($check)
        mysqli_stmt_bind_param($stmt, 'ssddisisdddi',
                                $array->code,
                                $array->description,
                                $array->sell_price,
                                $array->costs->average,
                                $array->class->attributes()->id,
                                $array->family,
                                $supid,
                                $array->supplier_code,
                                $array->inventory->total,
                                $array->inventory->available,
                                $array->inventory->warehouses,
                                $array->attributes()->id
                              );
        else
            die(mysqli_error ($link));
        
        //echo $updatequery.'<br>';
        //mysqli_stmt_execute($updatequery);
        
	    $checkquery = "SELECT rowID FROM Products WHERE resourceID=".$array->attributes()->id.";";
        $cq = mysqli_query($link, $checkquery);
        $row = mysqli_fetch_assoc($cq);
        
        //if (!mysqli_query($link, $updatequery))
        if (!mysqli_stmt_execute($stmt))
            log_error("Error with Product update query: " . mysqli_error($link));
        else
        if ($row['rowID']) 
            ;//log_action('Record updated successfully: Product '.$array->code);
        else {
            /*
            $insertquery =  "INSERT INTO Products (resourceID, code, description, sell_base, costav, classID, family, supplierID, supplier_code, total_qty, available_qty, warehouse_qty) ".
                            "VALUES (".$array->attributes()->id.",".
                                     "'".$array->code."', ".
                                     "'".str_replace("'","''",$array->description)."',".
                                     //"'".$array->description."', ".
                                         $array->sell_price.", ".
                                         $array->costs->average.", ".
                                         $array->class->attributes()->id.", ".
                                     "'".$array->family."', ".
                                         $array->supplier->attributes()->id.", ".
                                     "'".$array->supplier_code."', ".
                                         $array->inventory->total.", ".
                                         $array->inventory->available.", ".
                                         $array->inventory->warehouses."); ";
            //*/
            //echo $insertquery.'<br><br>';
            
            $insertquery = "INSERT INTO Products (resourceID,
                                                  code,
                                                  description,
                                                  sell_base,
                                                  costav,
                                                  classID,
                                                  family,
                                                  supplierID,
                                                  supplier_code,
                                                  total_qty,
                                                  available_qty,
                                                  warehouse_qty) 
                           VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            
            $stmt = mysqli_stmt_init($link);
            $check = mysqli_stmt_prepare($stmt, $insertquery);
            
            if ($check)
            mysqli_stmt_bind_param($stmt, 'issddisisddd',
                                $array->attributes()->id,
                                $array->code,
                                $array->description,
                                $array->sell_price,
                                $array->costs->average,
                                $array->class->attributes()->id,
                                $array->family,
                                $supid,
                                $array->supplier_code,
                                $array->inventory->total,
                                $array->inventory->available,
                                $array->inventory->warehouses
                              );
            else
                die(mysqli_error ($link));
            
            //if (mysqli_query($link, $insertquery))
            if (mysqli_stmt_execute($stmt))
                ;//log_action('Record added successfully: Product - '.$array->code);
            else
                log_error("Error inserting product: " . mysqli_error($link));
        }
        
    }
}


/*
 * updatedbinvoices
 * 
 * arg - array = array of information to update in SQL db
 * 
 * Update existing invoices and add any new ones to db
 * 
 */

function updatedbinvoices($array, $link, $count){
    if ($array)
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error());
    else {
	;//log_action('connected successfully: mysql');
        
        mysqli_select_db($link, "sales_db");
        
        /*
        $updatequery = "UPDATE Invoices SET invoiceid='".$array->invoice_id."', ".
                                        "datecreated='".$array->datetime_created."', ".
                                        "datemodified='".$array->datetime_modified."', ".
                                        "items=".$count.
                        " WHERE resourceID=".$array->attributes()->id.";";
        //*/
        
        $updatequery = "UPDATE Invoices SET invoiceid=?, ".
                                           "datecreated=?, ".
                                           "datemodified=?, ".
                                           "items=?".
                       " WHERE resourceID=?;";
        
        $stmt = mysqli_stmt_init($link);
        $check = mysqli_stmt_prepare($stmt, $updatequery);
        
        if ($check)
        mysqli_stmt_bind_param($stmt, 'issdi',
                                $array->invoice_id,
                                $array->datetime_created,
                                $array->datetime_modified,
                                $count,
                                $array->attributes()->id
                              );
        else
            die(mysqli_error ($link));
        //echo $updatequery.'<br><br>';
        
        $checkquery = "SELECT rowID FROM Invoices WHERE resourceID = ".$array->attributes()->id.";";
        $cq = mysqli_query($link, $checkquery);
        $row = mysqli_fetch_assoc($cq);
        
        //if (!mysqli_query($link, $updatequery)) {
        if (!mysqli_stmt_execute($stmt))
            log_error("Error with Invoice update query: " . mysqli_error($link));
            //log_error($updatequery);
        else
        if ($row['rowID']) 
            ;//log_action('Record updated successfully: Invoice '.$array->invoice_id);
        else {
            /*
            $insertquery =  "INSERT INTO Invoices (resourceID, invoiceid, datecreated, datemodified, items) ".
                            "VALUES (".$array->attributes()->id.",".
                                     "'".$array->invoice_id."',".
                                     //"'".str_replace("'","''",$array->description)."',".
                                     "'".$array->datetime_created."',".
                                     "'".$array->datetime_modified."',".
                                         $count.")";
            //*/
            
            $insertquery = "INSERT INTO Invoices (resourceID, invoiceid, datecreated, datemodified, items) ".
                                                 "VALUES (?,?,?,?,?);";
            
            $stmt = mysqli_stmt_init($link);
            $check = mysqli_stmt_prepare($stmt, $insertquery);
        
            if ($check)
            mysqli_stmt_bind_param($stmt, 'iissd', 
                                   $array->attributes()->id,
                                   $array->invoice_id,
                                   $array->datetime_created,
                                   $array->datetime_modified,
                                   $count);
            else
                die(mysqli_error ($link));
            
            //if (mysqli_query($link, $insertquery))
            if (mysqli_stmt_execute($stmt))
                ;//log_action('Record added successfully: Invoice - '.$array->invoice_id);
            else
                log_error("Error inserting Invoice: " . mysqli_error($link));
        }
        
        
       
    }
    //print_r($array); echo '<br>';
}

/*
 * updatedblineitems
 * 
 * arg - array = array of information to update in SQL db
 * 
 * Update existing lineitems and add any new ones to db
 * 
 */

function updatedblineitems($array, $link, $invoiceresid){
    if ($array)
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error());
    else {
	;//log_action('connected successfully: mysql');
        
        mysqli_select_db($link, "sales_db");
        
        if ($array->cost) $cost = $array->cost;
        else $cost = 0.00;
        
        /*
        $updatequery = "UPDATE Lineitems SET invoiceresID=".$invoiceresid.", ".
                                        "cost=".$cost.", ".
                                        "sell=".$array->sells->sell.", ".
                                        "quantity=".$array->quantity.", ".
                                        "totalsell=".$array->sells->total.", ".
                                        "totalcost=".$array->quantity * $cost.", ".
                                        "productresID=".$array->lineitem_product->product->attributes()->id.
                                        //"items='".$array->inventory->total."', ".
                        " WHERE resourceID=".$array->attributes()->id.";";
        
        //*/
        
        $updatequery = "UPDATE Lineitems SET invoiceresID=?, ".
                                                "cost=?, ".
                                                "sell=?, ".
                                                "quantity=?, ".
                                                "totalsell=?, ".
                                                "totalcost=?, ".
                                                "productresID=?".
                                            " WHERE resourceID=?;";
        
        $stmt = mysqli_stmt_init($link);
        $check = mysqli_stmt_prepare($stmt, $updatequery);
        
        //echo $updatequery.'<br><br>'; die('we get to here inside update query');
        $tcost = $cost * $array->quantity;
        
        if ($check)
        mysqli_stmt_bind_param($stmt, 'idddddii',
                                $invoiceresid,
                                $cost,
                                $array->sells->sell,
                                $array->quantity,
                                $array->sells->total,
                                $tcost,
                                $array->lineitem_product->product->attributes()->id,
                                $array->attributes()->id
                              );
                
                
                
        $checkquery = "SELECT rowID FROM Lineitems WHERE resourceID=".$array->attributes()->id.";";
        $cq = mysqli_query($link, $checkquery);
        $row = mysqli_fetch_assoc($cq);
        
	//if (!mysqli_query($link, $updatequery)){
        if (!mysqli_stmt_execute($stmt)){
            log_error("Error with Lineitem update query: " . mysqli_error($link));
            die($updatequery);
        }
        else
        if ($row['rowID'])
            ;//log_action('Record updated successfully: Lineitems '.$array->invoice_id);
        else {
            
            $insertquery = "INSERT INTO Lineitems (invoiceresID, productresID, resourceID, cost, sell, quantity, totalsell, totalcost) ".
                                                "VALUES (?,?,?,?,?,?,?,?);";
            
            $stmt = mysqli_stmt_init($link);
            $check = mysqli_stmt_prepare($stmt, $insertquery);
        
            //echo $insertquery.'<br><br>';                         
            
            if ($check)
            mysqli_stmt_bind_param($stmt, 'iiiddddd',
                                    $invoiceresid,
                                    $array->lineitem_product->product->attributes()->id,
                                    $array->attributes()->id,
                                    $cost,
                                    $array->sells->sell,
                                    $array->quantity,
                                    $array->sells->total,
                                    $tcost
                                  );
            else
                die(mysqli_error ($link));
            
            //if (mysqli_query($link, $insertquery))
            if (!mysqli_stmt_execute($stmt))
            {
                log_error("Error inserting Lineitem test: " . mysqli_error($link));
                die();
            }
        }
        
    }
}

/*
 * updatedbdate
 * 
 * arg = mysql connection info
 * 
 * If the user chooses to update the db with changes, 
 * the new date is added to the db
 * 
 */

function updatedbdate($link){
    
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error());
    else {
    
        mysqli_select_db($link, "sales_db");
        
        $datequery =    "INSERT INTO LastUpdated (date)".
                        "VALUES ('".date('Y-m-d H:i:s')."')";
        
        if (mysqli_query($link,$datequery))
            ;//log_action("Record added successfully: Date - ".date('Y-m-d H:i:s'));
        else
            log_error("Error inserting date: " . mysqli_error($link));
        
//        $updatequery = "UPDATE Lineitems SET totalcost=(cost*quantity);";
        
//        if (mysqli_query($link, $updatequery))
//            ;//log_action('Lineitem Cost Totals updated successfully');
//        else
//            log_error("Error updating Lineitem cost totals: " . mysqli_error($link));
    }
}


function getsummary($link, $datebegin, $dateend, $desc, $class, $family) {
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error()); 
    else {
	//echo "connected successfully: mysql<br>";
        
        mysqli_select_db($link, "sales_db");
           
        if (!$datebegin){
            $datebegin = "2000/1/1";    
        }
        
        if (!$dateend){
            $d = getdate();
            $dateend = $d['year'].'-'.$d['mon'].'-'.$d['mday'];    
        }
        $q1 = "datecreated BETWEEN '".$datebegin."' AND '".$dateend."'";
        
        if ($family){
            if ($family[1]=='contains')
                $fcomp = 'LIKE';
            else
                $fcomp = 'NOT LIKE';
            $q2 = " AND family ".$fcomp." '%".$family[0]."%'";
        }
        else 
            $q2 = null;
        
        if ($class) {
            if ($class[1]=='contains')
                $ccomp = 'LIKE';
            else
                $ccomp = 'NOT LIKE';
            $tq = "SELECT resourceID FROM Classes WHERE name ".$ccomp." '%".$class[0]."%';";
            //echo $tq.'<br>';
            $res = mysqli_query($link, $tq);
            if ($res)
                while ($res1[] = mysqli_fetch_array($res, MYSQLI_ASSOC));
            else
                log_error("Error running SELECT query: ". mysqli_error($link));
            
            //print_r($res1);
            //echo '<br><br>';
            
            $q3 = " AND (classID='".$res1[0][0]."'";
            
            $x=1;
            while ($res1[$x][0])
                $q3 .= " OR classID='".$res1[$x++][0]."'";
            
            $q3 .= ")";
                
        }
        else 
            $q3 = null;
        
        
        $query = "SELECT resourceID FROM Invoices WHERE ".$q1.";";
        
//        echo "<br><br>".$query."<br><br>";
        
        
        $re = mysqli_query($link,$query);
        
        if ($re)
            while ($result[] = mysqli_fetch_array($re, MYSQLI_ASSOC)) ;
        else
            log_error("Error running SELECT query: ". mysqli_error($link));
            //echo "Error running SELECT query: ". mysql_error (). "<br><br>";
        
        $i=0;
        $sum = array();
        
        while ($result[$i]) {
            $query = 'SELECT productresID, quantity, totalsell, totalcost FROM Lineitems WHERE invoiceresID = '.$result[$i]['resourceID'];
            $re = mysqli_query($link,$query);
            $tresult=array();
            if ($re)
                while ($tresult[] = mysqli_fetch_array($re, MYSQLI_ASSOC)) ;
            else
                log_error("Error running SELECT query: ". mysqli_error($link));
            
//            echo $i.' tresult: ';
//            print_r($tresult);
//            echo '<br>';

            foreach ($tresult as $k => $v){
                $sum[$v['productresID']]['quantity'] += $v['quantity'];
                $sum[$v['productresID']]['totalsell'] += $v['totalsell'];
                $sum[$v['productresID']]['totalcost'] += $v['totalcost'];
            }
            
            $i++;
        }

//        echo 'Sum: ';
//        print_r($sum);


        if ($desc) {
            if ($desc[1]=='contains')
                $dcomp = 'LIKE';
            else
                $dcomp = 'NOT LIKE';
            $q4 = " AND description ".$dcomp." '%".$desc[0]."%'";
        }
        else
            $q4 = null;
        
        foreach ($sum as $k => $v) {
            //echo $k.', '.$v['total'].'<br>';
            $query = 'SELECT code, description, classID, family, costav, supplierID, supplier_code, available_qty, warehouse_qty FROM Products WHERE resourceID = '.$k.$q4;
            $re = mysqli_query($link,$query);
            $tresult=array();
            if ($re)
                while ($tresult[] = mysqli_fetch_array($re, MYSQLI_ASSOC)) ;
            else
                log_error("Error running SELECT query: ". mysqli_error($link));
            
            $sum[$k]['code'] =          $tresult[0]['code'];
            $sum[$k]['description'] =   $tresult[0]['description'];
            $sum[$k]['family'] =        $tresult[0]['family'];
            $sum[$k]['classID'] =       $tresult[0]['classID'];
            $sum[$k]['supplierID'] =    $tresult[0]['supplierID'];
            $sum[$k]['costav'] =        $tresult[0]['costav'];
            $sum[$k]['supplier_code'] = $tresult[0]['supplier_code'];
            $sum[$k]['onh'] =           $tresult[0]['available_qty'] + $tresult[0]['warehouse_qty'];
        }

//        print_r($sum);

        foreach ($sum as $k => $v) {
            $query = 'SELECT name FROM Classes WHERE resourceID = '.$v['classID'].';';
            $re = mysqli_query($link,$query);
            $tresult=array();
            if ($re)
                while ($tresult[] = mysqli_fetch_array($re, MYSQLI_ASSOC)) ;
            else
                log_error("Error running Classes SELECT query: ". mysqli_error($link));
            $sum[$k]['classname'] = $tresult[0]['name'];

        }

        foreach ($sum as $k => $v) {
            $query = 'SELECT name FROM Suppliers WHERE resourceID = '.$v['supplierID'].';';
            $re = mysqli_query($link,$query);
            $tresult=array();
            if ($re)
                while ($tresult[] = mysqli_fetch_array($re, MYSQLI_ASSOC)) ;
            else
                log_error("Error running Suppliers SELECT query: ". mysqli_error($link));
            $sum[$k]['supplier'] = $tresult[0]['name'];

        }


        return $sum;
        
    }
}

function getdatelastupdatedb($link){
    
    if (mysqli_connect_errno($link))
	exit("Could not connect" . mysqli_connect_error());
    else {
	//echo "connected successfully: mysql<br>";
        
        mysqli_select_db($link, "sales_db");
        
        $query = "SELECT date FROM LastUpdated ORDER BY rowID DESC LIMIT 1;";
        
        $re = mysqli_query($link, $query);
        
        if ($re) {
            while ($result[] = mysqli_fetch_array($re, MYSQLI_ASSOC)) ;
//            echo 'datelastupdated: <br>';
////            print_r($result);
//            echo $result[0]['date'];
//            echo '<br>';
            return $result[0]['date'];
        }
        else {
            log_error("Error getting date: ". mysqli_error($link));
            //echo "Error getting date: ". mysql_error (). "<br><br>";
            return 'n/a';
        }
    }
}

//*/
?>
