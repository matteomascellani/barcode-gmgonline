<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
define("DB_HOST","46.4.41.134");
define("DB_USER","brandazza_gmg");
define("DB_PASSWORD","5*Dejh43");
define("DB_DATABASE","brandazza_gmgonline");
define("AUTH","Ls8asL:as");
require_once("../classes/database.php");

extract($_REQUEST);

if($task) {

    switch($task) {

        case "check_code":
            $db = new Database;
            if($type == "product") {
                $result = $db->Query("SELECT id, name, ean FROM jos_rkcommerce_products WHERE ean = " . $ean);
            } elseif($type == "order") {
                $result = $db->Query("SELECT id, string FROM jos_rkcommerce_gross_orders WHERE id = " . substr($ean, -7));
            }
            $item = $db->Result($result);
            print_r($item);
        break;
        default:
        break;

    }


}