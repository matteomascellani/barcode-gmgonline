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

                if(!isset($_SESSION['eans'])) {
                    echo json_encode(["error"=>"Devi prima scansionare un ordine!"]);
                    die();
                }
                $result = $db->Query("SELECT id, title, ean FROM jos_rkcommerce_products WHERE ean = " . $ean);
                $item = $db->Result($result);
                if(!$item) {
                    echo json_encode(["error"=>"Nessun prodotto con questo EAN!"]);
                    die();
                }
                $_SESSION['ean'] = $item["ean"];
                if(isset($_SESSION["eans"])) {
                    $_SESSION["is_ean"] = in_array($_SESSION["ean"],$_SESSION["eans"]) ? true : false;
                } 
                echo json_encode($_SESSION);
            } elseif($type == "order") {
                session_start();
                $eans = [];
                $id = substr(substr($ean, -7), 0, -1);
                $result = $db->Query("SELECT id, string FROM jos_rkcommerce_gross_orders WHERE id = " . $id);
                $item = $db->Result($result);
                if(!$item) {
                    echo json_encode(["error"=>"Nessun ordine con codice " . $id]);
                    die();
                }
                $products = json_decode($item["string"],1);
                foreach($products as $product) {
                    $result = $db->Query("SELECT id, ean FROM jos_rkcommerce_products WHERE id = " . $product["itemid"]);
                    $item = $db->Result($result);    
                    if($item && $item["ean"] != "") {
                        $eans[] = $item["ean"];
                    }
                }
                if(!count($eans)) {
                    echo json_encode(["error"=>"Nessun EAN trovato nell'ordine!"]);
                    die();
                }
                $_SESSION['eans'] = $eans;
                if(isset($_SESSION["ean"])) {
                    $_SESSION["is_ean"] = in_array($_SESSION["ean"],$_SESSION["eans"]) ? true : false;
                }                
                echo json_encode($_SESSION);
            }
        break;
        default:
        break;
    }


}