(function ($) {
  'use strict';

  /*--------------------------------------------------------------
  ## Down Load Button Function
  ----------------------------------------------------------------*/
  $('#generatePDF').on('click', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const invoiceId = urlParams.get('id') || 'invoice';

  const downloadSection = $('#download_section');
  const topLeftMargin = 0;

  html2canvas(downloadSection[0], {
    allowTaint: true,
    useCORS: true,
    scale: 2 // improves resolution
  }).then(function (canvas) {
    const imgData = canvas.toDataURL('image/jpeg', 1.0);
    const pdf = new jsPDF('p', 'pt', 'a4');

    const pageWidth = pdf.internal.pageSize.width;
    const pageHeight = pdf.internal.pageSize.height;

    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;

    const imgWidth = pageWidth;
    const imgHeight = (canvasHeight * imgWidth) / canvasWidth;

    let heightLeft = imgHeight;
    let position = 0;

    // Add first page
    pdf.addImage(imgData, 'JPEG', topLeftMargin, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    // Add more pages if needed
    while (heightLeft > 0) {
      position = heightLeft - imgHeight;
      pdf.addPage();
      pdf.addImage(imgData, 'JPEG', topLeftMargin, position, imgWidth, imgHeight);
      heightLeft -= pageHeight;
    }

    pdf.save('invoice-' + invoiceId + '.pdf');
  });
});

})(jQuery); // End of use strict
