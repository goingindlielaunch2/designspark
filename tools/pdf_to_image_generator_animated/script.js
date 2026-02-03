const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('fileInput');
const output = document.getElementById('output');

['dragenter', 'dragover'].forEach(eventName => {
  dropZone.addEventListener(eventName, e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
  });
});

['dragleave', 'drop'].forEach(eventName => {
  dropZone.addEventListener(eventName, e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
  });
});

dropZone.addEventListener('drop', e => {
  const file = e.dataTransfer.files[0];
  if (file && file.type === 'application/pdf') {
    renderPDF(file);
  }
});

fileInput.addEventListener('change', () => {
  const file = fileInput.files[0];
  if (file && file.type === 'application/pdf') {
    renderPDF(file);
  }
});

function renderPDF(file) {
  const reader = new FileReader();
  reader.onload = function () {
    const typedarray = new Uint8Array(this.result);

    pdfjsLib.getDocument(typedarray).promise.then(pdf => {
      pdf.getPage(1).then(page => {
        const scale = 2;
        const viewport = page.getViewport({ scale });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        canvas.width = viewport.width;
        canvas.height = viewport.height;

        page.render({ canvasContext: context, viewport }).promise.then(() => {
          output.innerHTML = '';
          output.appendChild(canvas);
          addDownloadLink(canvas);
        });
      });
    });
  };
  reader.readAsArrayBuffer(file);
}

function addDownloadLink(canvas) {
  const link = document.createElement('a');
  link.innerText = 'Download Image';
  link.href = canvas.toDataURL('image/png');
  link.download = 'pdf-image.png';
  link.style.display = 'block';
  link.style.marginTop = '10px';
  link.style.textDecoration = 'none';
  link.style.color = '#007bff';
  output.appendChild(link);
}
