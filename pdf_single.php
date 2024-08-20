<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF with Draggable and Resizable Image</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        #top-bar {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
        }

        #top-bar button {
            background-color: #0056b3;
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin: 0 5px;
        }

        #top-bar button:hover {
            background-color: #004080;
        }

        #the-canvas {
            border: 2px solid #007bff;
            position: relative;
            margin: 20px auto;
            display: block;
        }

        #drag {
            background-color: rgba(255, 255, 255, 0.9);
            position: absolute;
            cursor: pointer;
            overflow: hidden;
            display: none;
            z-index: 10;
            width: 50px;
        }

        #drag img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <div id="top-bar">
        <button id="previous-page-btn">Previous Page</button>
        <button id="next-page-btn">Next Page</button>
        <button id="add-signature-btn">Add Signature</button>
        <button id="signature-sign-btn">Signature Sign</button>
    </div>
    <canvas id="the-canvas"></canvas>
    <div id="drag">
        <img src="https://esign.psru.ac.th/images/signature/SXRBGNTVV1A1R4Z8TMSVCFVG9F7JZH.png" />
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        const pdfURL = 'https://sign.logoutwifi.me/assets/pdfs/master_PQDR62J2RP99M16TAN1KTFJRKJJBSN.pdf';
        let pdfDoc = null;
        let currentPage = 1;

        let signatureX = 0;
        let signatureY = 0;
        let signatureWidth = 0;
        let signatureHeight = 0;
        let signatureData = "";

        function renderPage(num) {
            pdfDoc.getPage(num).then(function (page) {
                console.log('Page loaded');

                var scale = 1;
                var viewport = page.getViewport({ scale: scale });

                var canvas = document.getElementById('the-canvas');
                var context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                var renderTask = page.render(renderContext);
                renderTask.promise.then(function () {
                    console.log('Page rendered');
                });
            });
        }

        $(document).ready(function () {
            pdfjsLib.getDocument(pdfURL).promise.then(function (pdf) {
                pdfDoc = pdf;
                renderPage(currentPage);

                $('#next-page-btn').click(function () {
                    if (pdfDoc && currentPage < pdfDoc.numPages) {
                        currentPage++;
                        renderPage(currentPage);
                    }
                });

                $('#previous-page-btn').click(function () {
                    if (pdfDoc && currentPage > 1) {
                        currentPage--;
                        renderPage(currentPage);
                    }
                });

                $('#add-signature-btn').click(function () {
                    $('#drag').show().css({
                        top: '200px',
                        left: '50%'
                    });
                });

                $('#signature-sign-btn').click(function () {
                    signatureData = $('#drag img').attr('src');
                    console.log("signatureX : " + signatureX);
                    console.log("signatureY : " + signatureY);
                    console.log("signatureWidth : " + signatureWidth);
                    console.log("signatureHeight : " + signatureHeight);
                    console.log("signatureData : " + signatureData);


                    console.log(currentPage)
                    return;
                    // Create a form to send the data
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': 'signAPI.php',
                        'target': '_blank'
                    });

                    // Add form fields
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'signatureX',
                        'value': signatureX
                    }));
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'signatureY',
                        'value': signatureY
                    }));
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'signatureWidth',
                        'value': signatureWidth
                    }));
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'signatureHeight',
                        'value': signatureHeight
                    }));
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'signatureData',
                        'value': signatureData
                    }));

                    // Append the form to the body and submit
                    form.appendTo('body').submit().remove();
                });

                $('#drag').resizable({
                    containment: '#the-canvas',
                    stop: function (event, ui) {
                        var w = $(this).width();
                        var h = $(this).height();
                        console.log('New Width: ' + w);
                        console.log('New Height: ' + h);

                        signatureWidth = w;
                        signatureHeight = h;
                    }
                }).draggable({
                    containment: '#the-canvas',
                    start: function () {
                        coordinates(this);
                    },
                    stop: function () {
                        coordinates(this);
                    }
                });
            });
        });

        var coordinates = function (element) {
            element = $(element);
            var offset = element.offset();
            var canvasOffset = $('#the-canvas').offset();
            var top = offset.top - canvasOffset.top;
            var left = offset.left - canvasOffset.left;

            console.log('PDF X: ' + top + ' PDF Y: ' + left);
            signatureX =  top;
            signatureY =  left;
        }
    </script>
</body>

</html>