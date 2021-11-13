
<?php

//** Search Target subjects arounnd a given zip code on certain radius */
// Sample Zip Code table will be attached to this example to give some idea of how the structure of the table and data should look like.

// Obtain Zip Code provided by User
$zipCode = isset($_POST['ZipCode']) && $_POST['ZipCode'] != '' ? $_POST['ZipCode'] : '';
// Set the radius if provided or set it to 0 by default.
$radius = isset($_POST['radius']) && (float)$_POST['radius'] > 0 ? (float)$_POST['radius'] : 0;

if ($zipCode && $radius) {
    //Defining the radian
    $radian = 180/3.1415926;
    // Considering there is tblZipCode table defined, gather latitude and longitude based on Zip Code.
    // Helper is the factory class that lets us to make queries on certain tables in database. You may use your own approach to query and get the data.
    $zipData = Helper::factory()->table('tblZipCode')->select('lat, lng')->where('zipcode = ?', $zipCode)->first();
    $lat = $zipData ? $zipData->lat : 0;
    $lng = $zipData ? $zipData->lng : 0;
    // Main Formula that calculates other targeted ZIP Codes on a given radius and original Zip Code.
    $target = "
        AND (3958*3.1415926*sqrt((tblZipCode.lat-{$lat})*(tblZipCode.lat-{$lat}) + cos(tblZipCode.lat/{$radian})*cos({$lat}/{$radian})*(tblZipCode.lng-{$lng})*(tblZipCode.lng-{$lng}))/180) <= {$radius}
    ";

    // Below you cann further append above query and innner join with other table to get desired data.
    // For instance: $mainQuery .= " INNER JOIN tblZipCode ON tblTarget.ZipCode = tblZipCode.zipcode ";
    // he above will give you the exact inner joined result of data that corresponds to a Zip Code on a given radius.
}

?>