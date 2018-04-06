<?php

    /* MariaDB [####]> desc tblZipCode;
    +----------+--------------------------+------+-----+---------+-------+
    | Field    | Type                     | Null | Key | Default | Extra |
    +----------+--------------------------+------+-----+---------+-------+
    | zipcode  | int(5) unsigned zerofill | NO   | PRI | NULL    |       |
    | city     | varchar(50)              | NO   |     | NULL    |       |
    | state    | varchar(2)               | NO   | MUL | NULL    |       |
    | lat      | decimal(15,8)            | NO   |     | NULL    |       |
    | lng      | decimal(15,8)            | NO   |     | NULL    |       |
    | timezone | tinyint(4)               | NO   |     | NULL    |       |
    | dst      | tinyint(1)               | NO   |     | NULL    |       |
    +----------+--------------------------+------+-----+---------+-------+ */


    function getZIPList($args) {
        $zipcode = $args['zipcode'];
        $lat = $args['lat'];
        $lng = $args['lng'];
        $radius = $args['radius'];

        $radian = 180 / 3.1415926;
        $ZipArray = [];

        if ($zipcode && $lat == '' && $lng == '') {
            $zipData = Record::factory()->table('tblZipCode')->select('lat, lng')->where('zipcode = ?', $zipcode)->first();
            $lat = $zipData ? $zipData->lat : 0;
            $lng = $zipData ? $zipData->lng : 0;
        }

        $sql = "
                SELECT
                    distinct(zipcode)
                FROM tblZipCode
                WHERE (3958*3.1415926*sqrt((lat-{$lat})*(lat-{$lat}) + cos(lat/{$radian})*cos({$lat}/{$radian})*(lng-{$lng})*(lng-{$lng}))/180) <= {$radius}
            ";
        $records = Record::factory()->raw($sql)->find();

        foreach ($records as $data) {
            $ZipArray[] = $data->zipcode;
        }
        return $ZipArray;
    }

    $zipcode = $_POST['zipcode'];
    $radius = $_POST['radius'] ?: 25; // radius in miles
    $lat = ''; // may be given and may be not 
    $lng = ''; // may be given and may be not
    $records = [];

    if ($zipcode) {
        $args = [
            'zipcode' => $zipcode,
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
        ];
        $records = $zipcode ? getZIPList($args) : [];
    }
?>