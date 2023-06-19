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
session_start();

if($task) {

    switch($task) {

        case "check_code":
            $db = new Database;
            if($type == "product") {
                $result = $db->Query("SELECT id, title, ean FROM jos_rkcommerce_products WHERE ean = " . $ean);
                $item = $db->Result($result);
                if($item) {
                    $title = $item["title"] . " " . $item["ean"];
                } else {
                    $title = "Nessuno";
                }
                echo json_encode(["title" => $title]);
                $_SESSION['ean'] = $item["ean"];
                echo json_encode($_SESSION);
            } elseif($type == "order") {
                $result = $db->Query("SELECT id, string FROM jos_rkcommerce_gross_orders WHERE id = " . substr(substr($ean, -7), 0, -1));
                $item = $db->Result($result);    
                $products = json_decode($item["string"],1);
                foreach($products as $product) {
                    $result = $db->Query("SELECT id, ean FROM jos_rkcommerce_products WHERE id = " . $product["itemid"]);
                    $item = $db->Result($result);    
                    $eans[] = $item["ean"] ?? "N/A";
                }
                $_SESSION['eans'] = $eans;
                echo json_encode($_SESSION);
            }
        break;
        default:
        break;

    }


}