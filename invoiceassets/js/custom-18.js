(function ($) {
  'use strict';

  /*--------------------------------------------------------------
  ## Down Load Button Function
  ----------------------------------------------------------------*/
  $('#generatePDF').on('click', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const invoiceId = urlParams.get('id') || 'invoice';

    const downloadSection = $('#download_section')[0];

    html2canvas(downloadSection, {
      allowTaint: true,
      useCORS: true,
      scale: 2 // better image quality
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

      pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
      heightLeft -= pageHeight;

      while (heightLeft > 0) {
        position = heightLeft - imgHeight;
        pdf.addPage();
        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
      }

      pdf.save('invoice-spark-' + invoiceId + '.pdf');
    });
  });
})(jQuery); // End of use strict
