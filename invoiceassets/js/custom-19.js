(function ($) {
  'use strict';

  $('#generatePDF').on('click', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const invoiceId = urlParams.get('id') || 'invoice';

    const downloadSection = $('#download_section')[0];

    html2canvas(downloadSection, {
      allowTaint: true,
      useCORS: true,
      scale: 2
    }).then(function (canvas) {
      const pdf = new jsPDF('p', 'pt', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();

      const imgWidth = pageWidth;
      const canvasWidth = canvas.width;
      const canvasHeight = canvas.height;

      const imgHeight = (canvasHeight * imgWidth) / canvasWidth;

      const headerHeight = 40;
      const footerHeight = 40;
      const contentHeight = pageHeight - headerHeight - footerHeight;

      let position = 0;
      let pageNum = 1;

      const addHeader = () => {
        pdf.setFontSize(12);
        pdf.text('Invoice Header', pageWidth / 2, 30, { align: 'center' });
        pdf.line(40, 40, pageWidth - 40, 40);
      };

      const addFooter = () => {
        pdf.line(40, pageHeight - 40, pageWidth - 40, pageHeight - 40);
        pdf.setFontSize(10);
        pdf.text(`Page ${pageNum}`, pageWidth / 2, pageHeight - 25, { align: 'center' });
      };

      while (position < canvas.height) {
        const pageCanvas = document.createElement('canvas');
        pageCanvas.width = canvas.width;
        pageCanvas.height = (contentHeight * canvasWidth) / pageWidth;

        const pageCtx = pageCanvas.getContext('2d');
        pageCtx.drawImage(
            canvas,
            0, position,
            canvas.width, pageCanvas.height,
            0, 0,
            canvas.width, pageCanvas.height
        );

        const imgData = pageCanvas.toDataURL('image/jpeg', 1.0);

        if (pageNum > 1) pdf.addPage();
        addHeader();
        addFooter();

        pdf.addImage(imgData, 'JPEG', 0, headerHeight, imgWidth, contentHeight);

        position += pageCanvas.height;
        pageNum++;
      }

      pdf.save(`invoice-${invoiceId}.pdf`);
    });
  });

})(jQuery);
