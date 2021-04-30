<?php
header('Access-Control-Allow-Origin: *');

/*
    immFamilies.php
    January 2021

    A backend API: provides json formatted family data to the svelte project that
    generates a web app displaying the 14 immigrant ancestors of Bruce and Paula.

    The data is retrieved from two MySQL tables in the ancestry database.
*/

//--------------------------------------------------------------------
// Open the ancestry database
$host    = 'db122b.pair.com';
$db      = 'ecurb_ancestry';
$user    = 'ecurb_11';
$pass    = 'FumGKbdn';
$charset = 'utf8mb4';
$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

//--------------------------------------------------------------------
// Walk thru all the records in the family table.
$sql = "Select * From immFamily";
$stmt = $pdo->query($sql);

$jAry = array();    // This is what we will send back to the caller (in JSON)

$ary  = array();
$famNum = -1;
while ($thisFamily = $stmt->fetch()) // For each record in the immFamily table
{
     $famNum++;
     $ary['id']           = $thisFamily['ID'];
     $ary['intro']        = $thisFamily['intro'];
     $ary['coupleNames']  = $thisFamily['coupleNames'];
     $ary['relationship'] = $thisFamily['relationship'];

    //----------------------------------------------------------------
    // Walk thru all the people records for this family record.
     $sqlPeople = "Select * From immPeople Where famID=" . $thisFamily['ID'];
     $stmtPpl  = $pdo->query($sqlPeople);

     // Create an array of the people in this family.
     $jPerAry = array();
     $perNum = -1;
     while ($thisPerson = $stmtPpl->fetch()) // For each person in the family
     {
          $perNum++;
          $aryPerson['perID'] = strval($famNum) . 'x' . strval($perNum);

          $aryPerson['name'] = $thisPerson['name'];
          
          $aryPerson['bornDate']    = $thisPerson['bornDate'];
		$aryPerson['bornDateMod'] = $thisPerson['bornDateMod'];
          $aryPerson['bornWhere']   = $thisPerson['bornWhere'];
          
		$aryPerson['arvDate']     = $thisPerson['arvDate'];
		$aryPerson['arvDateMod']  = $thisPerson['arvDateMod'];
		  
		$aryPerson['diedDate']    = $thisPerson['diedDate'];
		$aryPerson['diedDateMod'] = $thisPerson['diedDateMod'];
		  
		$aryPerson['diedWhere'] = $thisPerson['diedWhere'];
          $aryPerson['picURL']    = $thisPerson['picURL'];
		$aryPerson['bio']       = $thisPerson['bio'];
		$jPerAry[] = (object)$aryPerson;
     }
     $ary['people'] = $jPerAry;

     $jAry[] = (object)$ary;
}

//--------------------------------------------------------------------
// Encode the PHP array as a json array, and print the resulting text,
// which returns it to the asych request issued by the svelte app.
echo json_encode($jAry);

?>