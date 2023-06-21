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

        case "reset":
            unset($_SESSION["ean"]);    
            unset($_SESSION["eans"]);
            unset($_SESSION);
            session_destroy();
            echo json_encode(["response"=>"stop", "message"=>"Nessun ordine"]);
        break;
        case "check_code":
            $db = new Database;
            if($type == "product") {

                if(!isset($_SESSION['eans'])) {
                    echo json_encode(["response"=>"error", "message"=>"Devi prima scansionare un ordine!"]);
                    die();
                }
                $result = $db->Query("SELECT id, title, ean FROM jos_rkcommerce_products WHERE ean = " . $ean);
                $item = $db->Result($result);
                if(!$item) {
                    echo json_encode(["response"=>"error", "message"=>"Nessun prodotto con questo EAN!"]);
                    die();
                }
                $_SESSION['ean'] = $item["ean"];
                if(isset($_SESSION["eans"])) {
                    $_SESSION["is_ean"] = in_array($_SESSION["ean"],$_SESSION["eans"]) ? 1 : 2;
                } 
                echo json_encode(["response"=>"product", "is_ean" => $_SESSION["is_ean"] ?? null]);

            } elseif($type == "order") {
                $eans = [];
                $id = substr(substr($ean, -7), 0, -1);
                $result = $db->Query("SELECT id, string FROM jos_rkcommerce_gross_orders WHERE id = " . $id);
                $item = $db->Result($result);
                if(!$item) {
                    echo json_encode(["response"=>"error", "message"=>"Nessun ordine con codice " . $id]);
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
                    echo json_encode(["response"=>"error", "message"=>"Nessun EAN trovato nell'ordine!"]);
                    die();
                }
                $_SESSION['eans'] = $eans;
                if(isset($_SESSION["ean"])) {
                    $_SESSION["is_ean"] = in_array($_SESSION["ean"],$_SESSION["eans"]) ? 1 : 2;
                }                
                echo json_encode(["response"=>"order", "message"=>"ID Ordine: " . $item["id"], "is_ean" => $_SESSION["is_ean"] ?? null]);
            }
        break;
        default:
        break;
    }


}