<?php
header('Access-Control-Allow-Origin: *');

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
// Get data from the immFamily table and echo it as a json array
// of objects.
$sql = "Select * From immFamily";
$stmt = $pdo->query($sql);

$jAry = array();
$ary  = array();
while ($thisFamily = $stmt->fetch()) // For each record in the immFamily table
{
     $ary['id']           = $thisFamily['ID'];
     $ary['intro']        = $thisFamily['intro'];
     $ary['coupleNames']  = $thisFamily['coupleNames'];
     $ary['relationship'] = $thisFamily['relationship'];

     $sqlPeople = "Select * From immPeople Where famID=" . $thisFamily['ID'];
     $stmtPpl  = $pdo->query($sqlPeople);
     $jPerAry = array();
     while ($thisPerson = $stmtPpl->fetch()) // For each person in the family
     {
          $aryPerson['name']     = $thisPerson['name'];
          $aryPerson['bornDate'] = $thisPerson['bornDate'];
          $aryPerson['arvDate']  = $thisPerson['arvDate'];
          $aryPerson['diedDate'] = $thisPerson['diedDate'];
          $aryPerson['picURL']   = $thisPerson['picURL'];
          $jPerAry[] = (object)$aryPerson;
     }
     $ary['people'] = $jPerAry;

     $jAry[] = (object)$ary;
}

echo json_encode($jAry);

?>