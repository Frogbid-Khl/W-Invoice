(function ($) {
  'use strict';

  /*--------------------------------------------------------------
  ## Down Load Button Function
  ----------------------------------------------------------------*/
  (function ($) {
    'use strict';

    $('#generatePDF').on('click', function () {
      var downloadSection = $('#download_section')[0];
      const urlParams = new URLSearchParams(window.location.search);
      const invoiceId = urlParams.get('id');

      html2canvas(downloadSection, {
        allowTaint: true,
        useCORS: true,
        scale: 2 // better resolution
      }).then(function (canvas) {
        var imgData = canvas.toDataURL('image/jpeg', 1.0);
        var pdf = new jsPDF('p', 'pt', 'a4'); // A4 size

        const pageWidth = pdf.internal.pageSize.width;
        const pageHeight = pdf.internal.pageSize.height;

        var canvasWidth = canvas.width;
        var canvasHeight = canvas.height;

        // Calculate image dimensions to fit A4 width and maintain aspect ratio
        var imgWidth = pageWidth;
        var imgHeight = (canvasHeight * imgWidth) / canvasWidth;

        var heightLeft = imgHeight;
        var position = 0;

        // Add first page
        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add more pages if needed
        while (heightLeft > 0) {
          position = heightLeft - imgHeight;
          pdf.addPage();
          pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
          heightLeft -= pageHeight;
        }

        pdf.save('invoice-spark-'+invoiceId+'.pdf');
      });
    });
  })(jQuery);

})(jQuery); // End of use strict
