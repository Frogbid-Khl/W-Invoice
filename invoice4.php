﻿<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

if(!isset($_SESSION['uid'])){
    ?>
    <script>
        alert('For View Invoice Please Login');
        window.location.href="createAccount.php";
    </script>
    <?php
}

if(isset($_GET['id'])){
$sharable_url=$_GET['id'];

$dataInvoiceDetail=$db_handle->selectQuery("select * from invoice_detail where isharable_url='$sharable_url'");
$dataInvoice=$db_handle->selectQuery("select * from invoice where sharable_url='$sharable_url'");

if (!empty($dataInvoice)) {
?>
<!DOCTYPE html>
<html lang="zxx" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Digital Invoica</title>
	<link href="invoiceassets/images/favicon/icon-3.png" rel="icon">
	<link href="assets/fonts/css2-4?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="invoiceassets/css/custom-3.css">
	<link rel="stylesheet" href="invoiceassets/css/media-query-3.css">
</head>
<body>
	<!--Invoice wrap start here -->
	<div class="invoice_wrap bus-invoice">
		<div class="invoice-container">
			<div class="invoice-content-wrap" id="download_section">
				<!--Header start Here -->
				<header class="bus-header " id="invo_header">
					<div class="bus-header-img">
						<img src="invoiceassets/images/bus/black-img1.svg" alt="background-img" class="wid-100">
						<img src="invoiceassets/images/bus/pink-img.svg" alt="background-img" class="pink-img">
						<img src="invoiceassets/images/bus/black-img2.svg" alt="background-img">
					</div>
					<div class="container">
						<div class="bus-header-logo res-contact">
							<div class="wid-50">
								<a href="#" class="logo"><img src="<?= $dataInvoice[0]['ilogo']; ?>" style="max-width: 170px" alt="this is a invoice logo"></a>
							</div>
							<div class="wid-50">
								<h1 class="bus-txt">INVOICE</h1>
								<div class="invoice-agency-details">
									<div class="invo-head-wrap">
										<div class="color-light-black font-md wid-40 ">Invoice No:</div>
										<div class="font-md-grey color-grey wid-20">#<?= $dataInvoice[0]['iinv_no']; ?></div>
									</div>
									<div class="invo-head-wrap invoi-date-wrap invoi-date-wrap-agency">
										<div class="color-light-black font-md wid-40 ">Invoice Date:</div>
										<div class="font-md-grey color-grey wid-20 "><?= date("d/m/Y",strtotime($dataInvoice[0]['inserted_at'])); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<!--Header end Here -->
				<!--Invoice content start here -->
				<section class="bus-booking-content" id="bus_booking">
					<div class="container">
						<!--Invoice owner name content start here -->
						<div class="invoice-owner-conte-wrap pt-40">
							<div class="invo-to-wrap">
								<div class="invoice-to-content">
									<p class="font-md color-light-black">From:</p>
                                    <?php
                                    $lines = explode("\n", $dataInvoice[0]['ifrom']);
                                    ?>

                                    <h2 class="font-lg color-pink pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                    <p class="font-md-grey color-grey pt-10">
                                        <?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?>
                                    </p>
                                </div>
							</div>
							<div class="invo-pay-to-wrap">
								<div class="invoice-pay-content">
									<p class="font-md color-light-black">Bill To:</p>
                                    <?php
                                    $lines = explode("\n", $dataInvoice[0]['ibillto']);
                                    ?>

                                    <h2 class="font-lg color-pink pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                    <p class="font-md-grey color-grey pt-10">
                                        <?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?>
                                    </p>
                                </div>
							</div>
						</div>
						<div class="invoice-owner-conte-wrap pt-40">
							<div class="invo-to-wrap">
								<div class="invoice-to-content">
									<p class="font-md color-light-black">Ship To:</p>
                                    <?php
                                    $lines = explode("\n", $dataInvoice[0]['ishipto']);
                                    ?>

                                    <h2 class="font-lg color-pink pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                    <p class="font-md-grey color-grey pt-10">
                                        <?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?>
                                    </p>
                                </div>
							</div>
						</div>
						<!--Invoice owner name content end here -->
						<!--Invoice table data start here -->
						<div class="table-wrapper pt-40">
							<table class="invoice-table bus-detail-table">
								<thead>
									<tr class="invo-tb-header">
										<th class="font-md color-light-black width-50 ">Details</th>
										<th class="font-md color-light-black re-price-wid text-center">Price</th>
										<th class="font-md color-light-black re-qty-wid text-center">Qty</th>
										<th class="font-md color-light-black tota-wid text-right">Total</th>
									</tr>
								</thead>
								<tbody class="invo-tb-body">
                                <?php
                                $subTotal = 0;
                                $tax=0;
                                if (!empty($dataInvoiceDetail)) {
                                    foreach ($dataInvoiceDetail as $item) {
                                        $total = $item['qty'] * $item['price'];
                                        $tax+=($item['tax']/100)*$total;
                                        $subTotal += $total;
                                        ?>
                                        <tr class="invo-tb-row">
                                            <td class="font-sm color-grey"><?= htmlspecialchars($item['pname']); ?></td>
                                            <td class="font-sm color-grey text-center">Tk <?= number_format($item['price'], 2); ?></td>
                                            <td class="font-sm color-grey text-center"><?= htmlspecialchars($item['qty']); ?></td>
                                            <td class="font-sm color-grey text-right">Tk <?= number_format($total, 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
								</tbody>
							</table>
						</div>
						<!--Invoice table data end here -->
						<!--Invoice additional info start here -->
						<div class="invo-addition-wrap pt-20">
							<div class="invo-add-info-content bus-term-cond-content">
								<h3 class="font-md color-light-black">Terms & Condition:</h3>
								<div class="term-condi-list pt-10">
                                    <p class="font-sm pt-10"><?= $dataInvoice[0]['itoc']; ?></p>
								</div>
							</div>
							<div class="invo-bill-total bus-invo-total  ">
								<table class="invo-total-table">
									<tbody>
                                    <?php
                                    $grandTotal = $subTotal + $tax;
                                    ?>
										<tr>
											<td class="font-md color-light-black">Sub Total:</td>
											<td class="font-md-grey color-grey text-right">Tk <?= number_format($subTotal, 2); ?></td>
										</tr>
										<tr class="tax-row bottom-border">
											<td class="font-md color-light-black">Tax <span class="font-md color-grey">(<?= number_format(($tax/$subTotal)*100, 2); ?>%)</span></td>
											<td class="font-md-grey color-grey text-right">Tk <?= number_format($tax, 2); ?></td>
										</tr>
										<tr class="invo-grand-total">
											<td class="font-18-700 color-pink pt-20">Grand Total:</td>
											<td class="font-18-500 pt-20 color-light-black text-right">Tk <?= number_format($grandTotal, 2); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!--Invoice additional info end here -->
						<!--Invoice additional info end here -->
						<div class="signature-wrap-flight">
							<div class="sign-img">
								<img src="<?= $dataInvoice[0]['isignature']; ?>" style="max-width: 200px" alt="this is signature image">
							</div>
						</div>
						<!--Flight contact us detail start here -->
						<div class="res-contact res-bottom">
							<p class="font-sm color-light-black text-center">Thank you for choosing to dine with us. See you soon 🙂</p>
						</div>
					</div>
				</section>
				<!--Invoice content end here -->
			</div>
			<!--Bottom content start here -->
			<section class="agency-bottom-content d-print-none" id="agency_bottom">
				<!--Print-download content start here -->
				<div class="invo-buttons-wrap">
					<div class="invo-print-btn invo-btns">
						<a href="javascript:window.print()" class="print-btn">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
								<g clip-path="url(#clip0_10_61)">
									<path d="M17 17H19C19.5304 17 20.0391 16.7893 20.4142 16.4142C20.7893 16.0391 21 15.5304 21 15V11C21 10.4696 20.7893 9.96086 20.4142 9.58579C20.0391 9.21071 19.5304 9 19 9H5C4.46957 9 3.96086 9.21071 3.58579 9.58579C3.21071 9.96086 3 10.4696 3 11V15C3 15.5304 3.21071 16.0391 3.58579 16.4142C3.96086 16.7893 4.46957 17 5 17H7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
									<path d="M17 9V5C17 4.46957 16.7893 3.96086 16.4142 3.58579C16.0391 3.21071 15.5304 3 15 3H9C8.46957 3 7.96086 3.21071 7.58579 3.58579C7.21071 3.96086 7 4.46957 7 5V9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
									<path d="M7 15C7 14.4696 7.21071 13.9609 7.58579 13.5858C7.96086 13.2107 8.46957 13 9 13H15C15.5304 13 16.0391 13.2107 16.4142 13.5858C16.7893 13.9609 17 14.4696 17 15V19C17 19.5304 16.7893 20.0391 16.4142 20.4142C16.0391 20.7893 15.5304 21 15 21H9C8.46957 21 7.96086 20.7893 7.58579 20.4142C7.21071 20.0391 7 19.5304 7 19V15Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								</g>
								<defs>
									<clippath id="clip0_10_61">
										<rect width="24" height="24" fill="white"></rect>
									</clippath>
								</defs>
							</svg>
							<span class="inter-700 medium-font">Print</span>
						</a>
					</div>
					<div class="invo-down-btn invo-btns">
						<a class="download-btn" id="generatePDF">
							<svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_5_246)">
								<path d="M4 17V19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21H18C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19V17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7 11L12 16L17 11" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 4V16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clippath id="clip0_5_246"><rect width="24" height="24" fill="white"></rect></clippath></defs>
							</svg>
							<span class="inter-700 medium-font">Download</span>
						</a>
					</div>
				</div>
				<!--Print-download content end here -->
				<!--Note content start-->
				<div class="invo-note-wrap">
					<div class="note-title">
						<svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_8_240)"><path d="M14 3V7C14 7.26522 14.1054 7.51957 14.2929 7.70711C14.4804 7.89464 14.7348 8 15 8H19" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17 21H7C6.46957 21 5.96086 20.7893 5.58579 20.4142C5.21071 20.0391 5 19.5304 5 19V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H14L19 8V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21Z" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 7H10" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 13H15" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13 17H15" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clippath id="clip0_8_240"><rect width="24" height="24" fill="white"></rect>
						</clippath></defs></svg>
						<span class="font-md color-light-black">Note:</span>
					</div>
					<h3 class="font-md-grey color-grey note-desc">This is computer generated receipt and does not require physical signature.</h3>
				</div>
				<!--Note content End-->
			</section> 
			<!--Bottom content end here -->
		</div>
	</div>
	<!--Invoice wrap end here -->
	<script src="invoiceassets/js/jquery.min-3.js"></script>
	<script src="invoiceassets/js/jspdf.min-3.js"></script>
	<script src="invoiceassets/js/html2canvas.min-3.js"></script>
	<script src="invoiceassets/js/custom-3.js"></script>
</body>
</html>
    <?php
}else{
    ?>
    <script>
        alert('Page Not Found');
        window.location.href="index.php";
    </script>
    <?php
}
}else{
    ?>
    <script>
        alert('Page Not Found');
        window.location.href="index.php";
    </script>
    <?php
}
?>