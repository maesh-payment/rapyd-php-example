<?php
require_once("rapyd_utils.php");
require_once("dbcontroller.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "showqr":
		if(!empty($_POST["quantity"])) {
			$body = [
				"amount" => $_POST["quantity"],
				"currency" => "SGD",
				"payment_method" => [
					"type"=> "sg_paynow_bank"
					]
				];

			try {
			    $object = make_request('post', '/v1/payments', $body);
			    $_SESSION['qr'] = $object['data']['visual_codes']['PayNow QR'];
			    $_SESSION['payment_id'] = $object['data']['id'];
			    $_SESSION['param_two'] = $object['data']['original_amount'];
			} catch (Exception $e) {
			    echo "Error: $e";
			}
		}
	break;

	case 'complete-payment':
		$body = [
				"token" => $_POST["payment_id"],
				"param2" => $_POST["param_two"]
				];

			try {
			    $object = make_request('post', '/v1/payments/completePayment', $body);
			    $_SESSION['status'] = $object['status']['status'];
			} catch (Exception $e) {
			    echo "Error: $e";
			}
		break;
}
}
?>
<html>
<head>
<title>Simple PHP Shopping Cart</title>
<link href="style.css" type="text/css" rel="stylesheet" />
</head>
<body>
		<div class="product-item">
			<form method="post" action="paynow-qr.php?action=showqr">
			<div class="product-tile-footer">
			<div class="product-title">Enter amount to generate PayNow QR</div>
			<div class="cart-action" style="float: none;"><input type="number" class="product-quantity" name="quantity" value="1" size="8" /><input type="submit" value="Generate QR" class="btnAddAction" /></div>
			</div>
			</form>

<?php
if(isset($_SESSION["qr"])){
?>
<div id="show-qr" style="text-align: center;">
<img src="<?php echo $_SESSION["qr"]; ?>" />
<form method="post" action="paynow-qr.php?action=complete-payment">
<input type="hidden" name="payment_id" value="<?php echo $_SESSION["payment_id"]; ?>"/>
<input type="hidden" name="param_two" value="<?php echo $_SESSION["param_two"]; ?>"/>
<div class="product-tile-footer">
	<input type="submit" value="Complete Payment" class="btnAddAction" />
</div>
</form>
</div>
<?php
}
?>

<?php
if(isset($_SESSION["status"])){
?>
<div id="status" class="product-tile-footer">Payment completion : <?php echo $_SESSION["status"]; ?></div>
<?php
}
?>
</div>
</body>
</html>