<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

if(!isset($_SESSION['uid'])){
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: 'Successful',
            text: 'For View Invoice Please Login.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = "login.php";
        });
    </script>
    </body>
    </html>
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
	<title>Invoice Spark</title>
	<link href="assets/images/icon.png" rel="icon">
	<link href="assets/fonts/css2-10?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="invoiceassets/css/custom-9.css">
	<link rel="stylesheet" href="invoiceassets/css/media-query-9.css">
</head>
<body>
	<!--Invoice wrap start here -->
	<div class="invoice_wrap car-invoice">
		<div class="invoice-container">
			<div class="invoice-content-wrap" id="download_section">
				<!--Header start here -->
				<header class="car-header-img" id="invo_header">
					<div class="invoice-logo-content invoice-logo-content-car ">
						<div class="invoice-logo width-70">
							<a href="#" class="logo-car"><?php
$logo = $dataInvoice[0]['ilogo'];
$ext = pathinfo($logo, PATHINFO_EXTENSION);
$imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (in_array(strtolower($ext), $imageExts)) {
    // It's an image
    echo '<img src="' . htmlspecialchars($logo) . '" style="max-width: 170px;" alt="logo">';
} else {
    // Not an image - show text
    echo '<div style="font-size: 24px; color: black; font-weight: bold;">' . htmlspecialchars($logo) . '</div>';
}
?>
</a>
							<div class="invo-to-wrap pt-40">
								<div class="invoice-to-content">
									<p class="font-md color-light-black">Form:</p>
                                    <?php
                                    $lines = explode("\n", $dataInvoice[0]['ifrom']);
                                    ?>
									<p class="font-md-grey color-grey pt-10"><?= htmlspecialchars($lines[0]); ?></p>
									<p class="font-md-grey color-grey pt-10">
                                        <?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?>
                                    </p>
								</div>
							</div>
							<div class="pt-20">
								<div class="invoice-pay-content">
                                    <p class="font-md color-light-black">Bill To:</p>
                                    <?php
                                    $lines = explode("\n", $dataInvoice[0]['ibillto']);
                                    ?>
                                    <p class="font-md-grey color-grey pt-10"><?= htmlspecialchars($lines[0]); ?></p>
                                    <p class="font-md-grey color-grey pt-10">
                                        <?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?>
                                    </p>
								</div>
							</div>
						</div>
						<div class="invo-head-content width-30">
							<h1 class="car-txt">INVOICE</h1>
							<div class="invo-head-wrap pt-15">
								<div class="font-md color-light-black">Invoice No:</div>
								<div class="font-md-grey color-grey">#<?= $dataInvoice[0]['iinv_no']; ?></div>
							</div>
							<div class="invo-head-wrap invoi-date-wrap pt-10">
								<div class="font-md color-light-black">Invoice Date:</div>
								<div class="font-md-grey color-grey"><?= date("d/m/Y",strtotime($dataInvoice[0]['inserted_at'])); ?></div>
							</div>
						</div>
					</div>
				</header>
				<!--Header end here -->
				<!--Invoice content start -->
				<section class="agency-service-content car-invoice-content" id="car_booking">
					<div class="container">

						<!--Hire details start -->
						<div class="money-send-title-wrap hire-mt pt-40">
							<h3 class="font-lg color-dark-yellow transfer-title">Ship To</h3>
							<div class="mon-sent-content-wrap">
                                <?php
                                $lines = explode("\n", $dataInvoice[0]['ishipto']);
                                ?>
								<div class="mon-send-left-data">
									<div class="mon-send-col-one">
										<span class="font-sm-500 color-light-black">
                                            <?= htmlspecialchars($lines[0]); ?>
                                        </span>
									</div>
									<div class="mon-send-col-one">
										<span class="font-sm-500 color-light-black">
                                            <?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?>
                                        </span>
									</div>
								</div>
							</div>
						</div>
						<!--Hire details end -->
						<!--Car detail table start here -->
						<div class="table-wrapper pt-40">
							<table class="invoice-table car-detail-table">
								<thead>
									<tr class="invo-tb-header">
										<th class="font-md color-light-black font-sm flight-details ">Product</th>
										<th class="font-md color-light-black flight-re-price-wid ">Price</th>
										<th class="font-md color-light-black flight-re-price-wid  ">Quantity</th>
										<th class="font-md color-light-black flight-re-price-wid text-right ">Total</th>
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
                                            <td class="font-sm"><?= htmlspecialchars($item['pname']); ?></td>
                                            <td class="font-sm text-center"><?= $dataInvoice[0]['icurrency']; ?> <?= number_format($item['price'], 2); ?></td>
                                            <td class="font-sm text-center"><?= htmlspecialchars($item['qty']); ?></td>
                                            <td class="font-sm text-right pr-10"><?= $dataInvoice[0]['icurrency']; ?> <?= number_format($total, 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
								</tbody>
							</table>
						</div>
						<!--Car detail table end here -->
						<!--Invoice additional info start here -->
						<div class="invo-addition-wrap invo-addition-wrap-car pt-20">
							<div class="invo-add-info-content width-50">
								<h3 class="font-md color-light-black">Terms and Condition:</h3>
								<p class="add-info-desc inter-400 mtb-0 font-sm pt-10"><?= $dataInvoice[0]['itoc']; ?></p>
							</div>
							<div class="invo-bill-total width-30">
								<table class="invo-total-table">
                                    <?php
                                    $grandTotal = $subTotal + $tax;
                                    ?>
									<tbody>
										<tr>
											<td class="font-md color-light-black">Sub Total:</td>
											<td class="ifont-md-grey color-grey text-right"><?= $dataInvoice[0]['icurrency']; ?> <?= number_format($subTotal, 2); ?></td>
										</tr>
										<tr class="tax-row bottom-border">
											<td class="font-md color-light-black">Tax <span class="invo-total-data inter-700 medium-font second-color">(<?= number_format(($tax/$subTotal)*100, 2); ?>%)</span></td>
											<td class="font-md-grey color-grey text-right"><?= $dataInvoice[0]['icurrency']; ?> <?= number_format($tax, 2); ?></td>
										</tr>
										<tr class="invo-grand-total">
											<td class="color-dark-yellow font-18-700 pt-20">Grand Total:</td>
											<td class="font-md-grey color-grey text-right pt-20"><?= $dataInvoice[0]['icurrency']; ?> <?= number_format($grandTotal, 2); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!--Invoice additional info end here -->
						<!--Invoice additional info end here -->
						<div class="signature-wrap-flight">
							<div class="sign-img">
<?php
$signature = $dataInvoice[0]['isignature'];
$ext = pathinfo($signature, PATHINFO_EXTENSION);
$imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (in_array(strtolower($ext), $imageExts)) {
    // It's an image file
    echo '<img src="' . htmlspecialchars($signature) . '" style="max-width: 200px;" alt="signature image">';
} else {
    // It's text (not an image file)
    echo '<div style="font-family: Pacifico, cursive; font-size: 22px; color: #444;">' . htmlspecialchars($signature) . '</div>';
}
?>
</div>

						</div>
						<!--Flight contact us detail start here -->
					</div>
					<!--Car contact us detail start here -->
					<div class="car-bottom-sec">
						<div class="text-center pt-30 car-bottom">
							<p class="font-sm color-light-black">Thank you for choosing to 🚗 travelling 🚗 with us. See you soon 🙂</p>
						</div>
					</div>
					<!--Car contact us detail end here -->
				</section>
				<!--Invoice content end  -->
			</div>
			<!--Bottom content start here -->
			<section class="agency-bottom-content d-print-none" id="agency_bottom">
				<!--Print-download content start here -->
                    <div class="invo-buttons-wrap">
                        <div class="invo-print-btn invo-btns">
                            <a href="javascript:window.print()" class="print-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                     fill="none">
                                    <g clip-path="url(#clip0_10_61)">
                                        <path d="M17 17H19C19.5304 17 20.0391 16.7893 20.4142 16.4142C20.7893 16.0391 21 15.5304 21 15V11C21 10.4696 20.7893 9.96086 20.4142 9.58579C20.0391 9.21071 19.5304 9 19 9H5C4.46957 9 3.96086 9.21071 3.58579 9.58579C3.21071 9.96086 3 10.4696 3 11V15C3 15.5304 3.21071 16.0391 3.58579 16.4142C3.96086 16.7893 4.46957 17 5 17H7"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"></path>
                                        <path d="M17 9V5C17 4.46957 16.7893 3.96086 16.4142 3.58579C16.0391 3.21071 15.5304 3 15 3H9C8.46957 3 7.96086 3.21071 7.58579 3.58579C7.21071 3.96086 7 4.46957 7 5V9"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"></path>
                                        <path d="M7 15C7 14.4696 7.21071 13.9609 7.58579 13.5858C7.96086 13.2107 8.46957 13 9 13H15C15.5304 13 16.0391 13.2107 16.4142 13.5858C16.7893 13.9609 17 14.4696 17 15V19C17 19.5304 16.7893 20.0391 16.4142 20.4142C16.0391 20.7893 15.5304 21 15 21H9C8.46957 21 7.96086 20.7893 7.58579 20.4142C7.21071 20.0391 7 19.5304 7 19V15Z"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"></path>
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
                        <div class="invo-print-btn invo-btns">
                            <a href="viewInvoice.php" class="back-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512">
                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M64 128l177.6 0c-1 5.2-1.6 10.5-1.6 16l0 16-32 0L64 160c-8.8 0-16-7.2-16-16s7.2-16 16-16zm224 16c0-17.7 14.3-32 32-32c0 0 0 0 0 0l24 0c66.3 0 120 53.7 120 120l0 48c0 52.5-33.7 97.1-80.7 113.4c.5-3.1 .7-6.2 .7-9.4c0-20-9.2-37.9-23.6-49.7c4.9-9 7.6-19.4 7.6-30.3c0-15.1-5.3-29-14-40c8.8-11 14-24.9 14-40l0-40c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-40 0-40zm32-80s0 0 0 0c-18 0-34.6 6-48 16L64 80C28.7 80 0 108.7 0 144s28.7 64 64 64l82 0c-1.3 5.1-2 10.5-2 16c0 25.3 14.7 47.2 36 57.6c-2.6 7-4 14.5-4 22.4c0 20 9.2 37.9 23.6 49.7c-4.9 9-7.6 19.4-7.6 30.3c0 35.3 28.7 64 64 64l64 0 24 0c92.8 0 168-75.2 168-168l0-48c0-92.8-75.2-168-168-168l-24 0zM256 400c-8.8 0-16-7.2-16-16s7.2-16 16-16l48 0 16 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-64 0zM240 224c0 5.5 .7 10.9 2 16l-2 0-32 0c-8.8 0-16-7.2-16-16s7.2-16 16-16l32 0 0 16zm24 64l40 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-48 0-16 0c-8.8 0-16-7.2-16-16s7.2-16 16-16l24 0z"/>
                                </svg>
                                <span class="inter-700 medium-font">Back</span>
                            </a>
                        </div>
                        <div class="invo-down-btn invo-btns">
                            <a class="download-btn" id="generatePDF">
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_5_246)">
                                        <path d="M4 17V19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21H18C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19V17"
                                              stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"></path>
                                        <path d="M7 11L12 16L17 11" stroke="white" stroke-width="2"
                                              stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M12 4V16" stroke="white" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"></path>
                                    </g>
                                    <defs>
                                        <clippath id="clip0_5_246">
                                            <rect width="24" height="24" fill="white"></rect>
                                        </clippath>
                                    </defs>
                                </svg>
                                <span class="inter-700 medium-font">Download</span>
                            </a>
                        </div>
                    </div>
                    <!--Print-download content end here -->
				<!--Note content start -->
				<div class="invo-note-wrap">
					<div class="note-title">
						<svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_8_240)"><path d="M14 3V7C14 7.26522 14.1054 7.51957 14.2929 7.70711C14.4804 7.89464 14.7348 8 15 8H19" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17 21H7C6.46957 21 5.96086 20.7893 5.58579 20.4142C5.21071 20.0391 5 19.5304 5 19V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H14L19 8V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21Z" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 7H10" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 13H15" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13 17H15" stroke="#12151C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clippath id="clip0_8_240"><rect width="24" height="24" fill="white"></rect>
						</clippath></defs></svg>
						<span class="font-md color-light-black">Note:</span>
					</div>
					<h3 class="font-md-grey color-grey note-desc">This is computer generated receipt and does not require physical signature.</h3>
				</div>
				<!--Note content end -->
			</section>
			<!--Bottom content end here -->
		</div>
	</div>
	<!--Invoice wrap end here -->
	<script src="invoiceassets/js/jquery.min-9.js"></script>
	<script src="invoiceassets/js/jspdf.min-9.js"></script>
	<script src="invoiceassets/js/html2canvas.min-9.js"></script>
	<script src="invoiceassets/js/custom-9.js"></script>
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