(function ($) {
  'use strict';

  /*--------------------------------------------------------------
  ## Down Load Button Function
  ----------------------------------------------------------------*/

  $('#generatePDF').on('click', function () {
    const { jsPDF } = window.jspdf;
    var downloadSection = $('#download_section')[0];
    var scale = 3; // Adjust for higher quality

    // 🔍 Function to get 'id' from URL
    function getQueryParam(param) {
      var urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(param);
    }

    var invoiceId = getQueryParam('id') || 'invoice'; // Fallback if no ID

    html2canvas(downloadSection, {
      scale: scale,
      useCORS: true,
      allowTaint: false
    }).then(function (canvas) {
      var imgData = canvas.toDataURL('image/png', 1.0);
      var imgWidth = 210; // A4 width in mm
      var pageHeight = 297; // A4 height in mm
      var pxPerMm = canvas.width / imgWidth;
      var imgHeight = canvas.height / pxPerMm;

      var pdf = new jsPDF('p', 'mm', 'a4');
      var position = 0;

      // Add first page
      pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
      position -= pageHeight;

      // Add extra pages if needed
      while (imgHeight + position > 0) {
        pdf.addPage();
        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
        position -= pageHeight;
      }

      pdf.save('invoiz-'+invoiceId+'.pdf');
    });
  });
})(jQuery); // End of use strict
