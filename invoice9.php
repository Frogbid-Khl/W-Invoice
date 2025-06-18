<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

if (!isset($_SESSION['uid'])) {
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

if (isset($_GET['id'])) {
    $sharable_url = $_GET['id'];

    $dataInvoiceDetail = $db_handle->selectQuery("select * from invoice_detail where isharable_url='$sharable_url'");
    $dataInvoice = $db_handle->selectQuery("select * from invoice where sharable_url='$sharable_url'");

    if (!empty($dataInvoice)) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Invoice Spark</title>
            <link href="assets/images/icon.png" rel="icon">
            <link href="assets/fonts/css2-1?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
                  rel="stylesheet">
            <link rel="stylesheet" href="invoiceassets/css/custom-8.css">
            <link rel="stylesheet" href="invoiceassets/css/media-query-8.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        </head>
        <body>
        <div class="text-center mt-5">
            <section class="agency-bottom-content d-print-none" id="agency_bottom">
                <!--Print-download content start here -->
                <div class="invo-buttons-wrap">
                    <div class="invo-print-btn invo-btns">
                        <a onclick="printInvoice()" class="print-btn">
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
                        <a class="download-btn" onclick="generateInvoice()">
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
            </section>
        </div>

        <!-- Preview container -->
        <div id="invoicePreview" style="margin-top:20px;"></div>

        <!--Invoice wrap End here -->
        <script>
            function printInvoice() {
                const invoiceEl = document.getElementById("invoicePreview");
                if (!invoiceEl) {
                    alert("Invoice preview not found!");
                    return;
                }

                const printContents = invoiceEl.innerHTML;
                const styleTags = [...document.querySelectorAll('style, link[rel="stylesheet"]')]
                    .map(tag => tag.outerHTML).join('');

                const printWindow = window.open('', '_blank', 'width=900,height=650');

                printWindow.document.write(`
        <html>
        <head>
            <title>Print Invoice</title>
            ${styleTags}
            <style>
                @page {
                    size: A4;
                    margin: 0;
                }

                body {
                    margin: 0;
                    padding: 0;
                    background: white;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    font-family: Arial, sans-serif;
                }

                .page {
                    width: 210mm;
                    min-height: 250mm;
                    padding: 10mm;
                    margin: auto;
                    box-sizing: border-box;
                    background: white;
                    page-break-after: always;
                }

                .page:last-child {
                    page-break-after: auto;
                }

                @media print {
                    html, body {
                        width: 210mm;
                        height: 250mm;
                    }
                }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
    `);

                // Important: wait for the new window to fully load before printing
                printWindow.document.close();
                printWindow.focus();

                printWindow.onload = function () {
                    printWindow.print();

                    // Optional: close window after printing
                    printWindow.onafterprint = function () {
                        printWindow.close();
                    };
                };
            }




            // Call previewInvoice() on page load
            document.addEventListener('DOMContentLoaded', function () {
                previewInvoice();
            });

            const { jsPDF } = window.jspdf;

            function chunkArray(arr, size) {
                const chunks = [];
                for (let i = 0; i < arr.length; i += size) {
                    chunks.push(arr.slice(i, i + size));
                }
                return chunks;
            }


            function previewInvoice() {
                const products = <?= json_encode(array_map(function ($item) use ($dataInvoice) {
                    return [
                        'name' => $item['pname'],
                        'qty' => (int)$item['qty'],
                        'price' => floatval($item['price']),
                        'tax' => floatval($item['tax']),
                        'currency' => $dataInvoice[0]['icurrency']
                    ];
                }, $dataInvoiceDetail)); ?>;

                const firstPageCount = 6;
                const lastPageCount = 7;
                const middlePageCount = 12;

                // Custom function to split products
                function splitProducts(products) {
                    const totalProducts = products.length;
                    const result = [];

                    if (totalProducts <= firstPageCount + lastPageCount) {
                        // Not enough products to fill middle pages
                        result.push(products.slice(0, firstPageCount));
                        if (totalProducts > firstPageCount) {
                            result.push(products.slice(firstPageCount));
                        }
                    } else {
                        result.push(products.slice(0, firstPageCount)); // First page
                        let currentIndex = firstPageCount;
                        const endBeforeLast = totalProducts - lastPageCount;

                        while (currentIndex < endBeforeLast) {
                            result.push(products.slice(currentIndex, currentIndex + middlePageCount));
                            currentIndex += middlePageCount;
                        }

                        result.push(products.slice(currentIndex)); // Last page
                    }

                    return result;
                }

                const pages = splitProducts(products);
                const previewContainer = document.getElementById('invoicePreview');
                previewContainer.innerHTML = ''; // Clear previous previews

                for (let i = 0; i < pages.length; i++) {
                    const isFirstPage = (i === 0);
                    const isLastPage = (i === pages.length - 1);
                    const pageDiv = document.createElement('div');
                    pageDiv.style.width = '210mm';
                    pageDiv.style.minHeight = '297mm';
                    pageDiv.style.border = '1px solid #ccc';
                    pageDiv.style.margin = 'auto';
                    pageDiv.style.marginBottom = '20px';
                    pageDiv.style.padding = '10mm';
                    pageDiv.style.background = '#fff';
                    pageDiv.style.boxShadow = '0 0 5px rgba(0,0,0,0.1)';
                    pageDiv.style.boxSizing = 'border-box';

                    pageDiv.innerHTML =  `
           		<!--Invoice wrap Start here -->
	<div class="invoice_wrap flight">
		<div class="invoice-container">
			<div class="invoice-content-wrap" id="download_section">
				<!--Header start here -->
				<header class="invoice-header flight-header" id="invo_header">
					<div class="flight-bg-top">
						<div class="flight-img1">
							<img src="invoiceassets/images/flight/black-img.svg" alt="background-img">
						</div>
						<div class="flight-img2">
							<img src="invoiceassets/images/flight/purple-img.svg" alt="background-img">
						</div>
					</div>
					<div class="invoice-logo-content invoice-logo-content-flight">
						<div class="invoice-logo">
							<a href="#" class="logo-flight"><?php
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
						</div>
						<div class="flight-img">
							<img src="invoiceassets/images/flight/flight.svg" alt="flight-img">
						</div>
					</div>
				</header>
				<!--Header end here -->
             ${isFirstPage ? `
<!--Invoice content start here -->
				<section class="flight-booking-content" id="flight_booking">
					<div class="container">
						<!--Invoice owner name start here -->
						<div class="invoice-owner-conte-wrap pt-40">
							<div class="invo-to-wrap">
								<div class="invoice-to-content">
									<p class="font-md color-light-black">From:</p>
                                    <?php
                    $lines = explode("\n", $dataInvoice[0]['ifrom']);
                    ?>
									<h1 class="d-none">Hidden</h1>
									<h2 class="color-blue-flight font-lg pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
									<p class="font-md-grey color-grey pt-10"><?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?></p>
								</div>
							</div>
							<div>
								<div class="invo-head-content  ">
									<h1 class="flight-txt">INVOICE</h1>
									<div class="invo-head-wrap pt-20">
										<div class="font-md color-light-black wid-40-hotel">Invoice No:</div>
										<div class="font-md-grey color-grey">#<?= $dataInvoice[0]['iinv_no']; ?></div>
									</div>
									<div class="invo-head-wrap invoi-date-wrap invoi-date-wrap-agency">
										<div class="font-md color-light-black wid-40-hotel">Invoice Date:</div>
										<div class="font-md-grey color-grey"><?= date("d/m/Y",strtotime($dataInvoice[0]['inserted_at'])); ?></div>
									</div>
								</div>
							</div>
						</div>
                        <div class="invoice-owner-conte-wrap pt-40">
                            <div class="invo-to-wrap">
                                <div class="invoice-to-content">
                                    <p class="font-md color-light-black">Bill To:</p>
                                    <?php
                    $lines = explode("\n", $dataInvoice[0]['ibillto']);
                    ?>
                                    <h1 class="d-none">Hidden</h1>
                                    <h2 class="color-blue-flight font-lg pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                    <p class="font-md-grey color-grey pt-10"><?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?></p>
                                </div>
                            </div>
                            <div>
                                <div class="invo-head-content  ">
                                    <div class="invoice-to-content">
                                        <p class="font-md color-light-black">Ship To:</p>
                                        <?php
                    $lines = explode("\n", $dataInvoice[0]['ishipto']);
                    ?>
                                        <h1 class="d-none">Hidden</h1>
                                        <h2 class="color-blue-flight font-lg pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                        <p class="font-md-grey color-grey pt-10"><?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
						<!--Invoice owner name end here -->
                    ` : ''}
                    <!--Flight table data start here -->
					<div class="table-wrapper flight-detail-table pt-32">
                        ${generateTableHTML(pages[i], isLastPage)}
                    </div>
                    <!--Coffee table data end here -->

                    ${isLastPage ? `
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
                    ` : ''}
                </div>
            </section>
            <!--Invoice content end here -->
        </div>
    </div>
</div>
        `;
                    previewContainer.appendChild(pageDiv);
                }
            }

            let allProducts = [];
            let currency='$';

            function generateTableHTML(products, isLastPage = false) {
                let html = `
							<table class="invoice-table  ">
								<thead>
									<tr class="invo-tb-header bg-black">
										<th class="font-md flight-details pl-10">Product Details</th>
										<th class="font-md flight-re-price-wid ">Price</th>
										<th class="font-md flight-re-price-wid ">Qty</th>
										<th class="font-md flight-re-price-wid ">Tax</th>
										<th class="font-md flight-re-price-wid pr-10 text-right">Total</th>
									</tr>
								</thead>
								<tbody class="invo-tb-body">`;
                products.forEach(p => {
                    html += `<tr class="invo-tb-row">
                <td class="font-sm text-left pl-10">${p.name}</td>
                <td class="font-sm pl-10 pr-10">${p.currency} ${p.price}</td>
                <td class="font-sm text-center">${p.qty}</td>
                <td class="font-sm text-center">${p.tax}%</td>
                <td class="font-sm">${p.currency} ${p.price*p.qty}</td>
            </tr>`;
                    allProducts.push(p); // Collect all products here
                    currency=p.currency;
                });

                html += `</tbody></table>`;


                if (isLastPage) {
                    const subtotal = allProducts.reduce((sum, p) => sum + (p.price * p.qty), 0);
                    const tax = (allProducts.reduce((sum, p) => sum + (p.price * p.qty*(p.tax)/100), 0));
                    const taxPercent = (tax/subtotal)*100;
                    const grandTotal = subtotal + tax ;

                    html += `
                    <!--Invoice additional info start here -->
						<div class="invo-addition-wrap pt-20">
							<div class="bus-term-cond-content">
								<h2 class="d-none">Invoice</h2>
								<h3 class="font-md color-light-black">Terms & Condition:</h3>
								<div class="term-condi-list pt-10">
                                    <p class="font-sm pt-10"><?= $dataInvoice[0]['itoc']; ?></p>
								</div>
							</div>
							<div class="invo-bill-total">
								<table class="invo-total-table">
									<tbody>
										<tr>
											<td class="font-md color-light-black">Sub Total:</td>
											<td class="font-md-grey color-grey text-right ">${currency} ${subtotal.toFixed(2)}</td>
										</tr>
										<tr class="tax-row bottom-border">
											<td class="font-md color-light-black">Tax <span class="color-grey ">(${taxPercent.toFixed(2)}%)</span></td>
											<td class="font-md-grey color-grey text-right">${currency} ${tax.toFixed(2)}</td>
										</tr>
										<tr class="invo-grand-total">
											<td class="color-blue-flight font-lg pt-20">Grand Total:</td>
											<td class="font-18-500 color-light-black pt-20 text-right">${currency} ${grandTotal.toFixed(2)}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!--Invoice additional info end here -->
                    `;
                }

                return html;
            }

            async function generateInvoice() {

                const products = <?= json_encode(array_map(function ($item) use ($dataInvoice) {
                    return [
                        'name' => $item['pname'],
                        'qty' => (int)$item['qty'],
                        'price' => floatval($item['price']),
                        'tax' => floatval($item['tax']),
                        'currency' => $dataInvoice[0]['icurrency']
                    ];
                }, $dataInvoiceDetail)); ?>;



                const firstPageCount = 7;
                const lastPageCount = 7;
                const middlePageCount = 12;

                // Custom function to split products
                function splitProducts(products) {
                    const totalProducts = products.length;
                    const result = [];

                    if (totalProducts <= firstPageCount + lastPageCount) {
                        // Not enough products to fill middle pages
                        result.push(products.slice(0, firstPageCount));
                        if (totalProducts > firstPageCount) {
                            result.push(products.slice(firstPageCount));
                        }
                    } else {
                        result.push(products.slice(0, firstPageCount)); // First page
                        let currentIndex = firstPageCount;
                        const endBeforeLast = totalProducts - lastPageCount;

                        while (currentIndex < endBeforeLast) {
                            result.push(products.slice(currentIndex, currentIndex + middlePageCount));
                            currentIndex += middlePageCount;
                        }

                        result.push(products.slice(currentIndex)); // Last page
                    }

                    return result;
                }

                const pages = splitProducts(products);
                const pdf = new jsPDF('p', 'mm', 'a4');

                for (let i = 0; i < pages.length; i++) {
                    const isFirstPage = (i === 0);
                    const isLastPage = (i === pages.length - 1);
                    const container = document.createElement('div');
                    container.style.width = '210mm';
                    container.style.height = '297mm';
                    container.style.boxSizing = 'border-box';
                    container.style.padding = '10mm';
                    container.style.backgroundColor = 'white';

                    container.innerHTML = `
           		              		<!--Invoice wrap Start here -->
	<div class="invoice_wrap flight">
		<div class="invoice-container">
			<div class="invoice-content-wrap" id="download_section">
				<!--Header start here -->
				<header class="invoice-header flight-header" id="invo_header">
					<div class="flight-bg-top">
						<div class="flight-img1">
							<img src="invoiceassets/images/flight/black-img.svg" alt="background-img">
						</div>
						<div class="flight-img2">
							<img src="invoiceassets/images/flight/purple-img.svg" alt="background-img">
						</div>
					</div>
					<div class="invoice-logo-content invoice-logo-content-flight">
						<div class="invoice-logo">
							<a href="#" class="logo-flight"><?php
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
						</div>
						<div class="flight-img">
							<img src="invoiceassets/images/flight/flight.svg" alt="flight-img">
						</div>
					</div>
				</header>
				<!--Header end here -->
             ${isFirstPage ? `
<!--Invoice content start here -->
				<section class="flight-booking-content" id="flight_booking">
					<div class="container">
						<!--Invoice owner name start here -->
						<div class="invoice-owner-conte-wrap pt-40">
							<div class="invo-to-wrap">
								<div class="invoice-to-content">
									<p class="font-md color-light-black">From:</p>
                                    <?php
                    $lines = explode("\n", $dataInvoice[0]['ifrom']);
                    ?>
									<h1 class="d-none">Hidden</h1>
									<h2 class="color-blue-flight font-lg pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
									<p class="font-md-grey color-grey pt-10"><?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?></p>
								</div>
							</div>
							<div>
								<div class="invo-head-content  ">
									<h1 class="flight-txt">INVOICE</h1>
									<div class="invo-head-wrap pt-20">
										<div class="font-md color-light-black wid-40-hotel">Invoice No:</div>
										<div class="font-md-grey color-grey">#<?= $dataInvoice[0]['iinv_no']; ?></div>
									</div>
									<div class="invo-head-wrap invoi-date-wrap invoi-date-wrap-agency">
										<div class="font-md color-light-black wid-40-hotel">Invoice Date:</div>
										<div class="font-md-grey color-grey"><?= date("d/m/Y",strtotime($dataInvoice[0]['inserted_at'])); ?></div>
									</div>
								</div>
							</div>
						</div>
                        <div class="invoice-owner-conte-wrap pt-40">
                            <div class="invo-to-wrap">
                                <div class="invoice-to-content">
                                    <p class="font-md color-light-black">Bill To:</p>
                                    <?php
                    $lines = explode("\n", $dataInvoice[0]['ibillto']);
                    ?>
                                    <h1 class="d-none">Hidden</h1>
                                    <h2 class="color-blue-flight font-lg pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                    <p class="font-md-grey color-grey pt-10"><?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?></p>
                                </div>
                            </div>
                            <div>
                                <div class="invo-head-content  ">
                                    <div class="invoice-to-content">
                                        <p class="font-md color-light-black">Ship To:</p>
                                        <?php
                    $lines = explode("\n", $dataInvoice[0]['ishipto']);
                    ?>
                                        <h1 class="d-none">Hidden</h1>
                                        <h2 class="color-blue-flight font-lg pt-10"><?= htmlspecialchars($lines[0]); ?></h2>
                                        <p class="font-md-grey color-grey pt-10"><?= nl2br(htmlspecialchars(implode("\n", array_slice($lines, 1)))); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
						<!--Invoice owner name end here -->
                    ` : ''}
                    <!--Flight table data start here -->
					<div class="table-wrapper flight-detail-table pt-32">
                        ${generateTableHTML(pages[i], isLastPage)}
                    </div>
                    <!--Coffee table data end here -->

                    ${isLastPage ? `
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
                    ` : ''}
                </div>
            </section>
            <!--Invoice content end here -->
        </div>
    </div>
</div>
        `;

                    document.body.appendChild(container);
                    const canvas = await html2canvas(container, { scale: 2 });
                    const imgData = canvas.toDataURL('image/png');

                    if (i > 0) pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, 0, 210, 297);
                    document.body.removeChild(container);
                }

                const urlParams = new URLSearchParams(window.location.search);
                const invoiceId = urlParams.get('id') || 'invoice';

                pdf.save('invoice-spark-' + invoiceId + '.pdf');
            }
        </script>
        </body>
        </html>
        <?php
    } else {
        ?>
        <script>
            alert('Page Not Found');
            window.location.href = "index.php";
        </script>
        <?php
    }
} else {
    ?>
    <script>
        alert('Page Not Found');
        window.location.href = "index.php";
    </script>
    <?php
}
?>