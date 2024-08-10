const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('archivo');
const progressBar = document.getElementById('progressBar');
const progress = document.getElementById('progress');

dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.classList.add('active');
});

dropArea.addEventListener('dragleave', () => {
    dropArea.classList.remove('active');
});

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.classList.remove('active');
    const files = e.dataTransfer.files;
    fileInput.files = files; // Assign dropped files to the input
    uploadFiles(files); // Call upload function
});

fileInput.addEventListener('change', (e) => {
    const files = e.target.files;
    uploadFiles(files); // Call upload function
});

function uploadFiles(files) {
    if (files.length > 0) {
        const formData = new FormData();
        Array.from(files).forEach(file => {
            formData.append('archivo[]', file); // Add each file to the FormData
        });

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // Change the URL if necessary

        // Monitor the upload progress
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const percentComplete = (event.loaded / event.total) * 100;
                progress.style.width = percentComplete + '%';
                progress.innerText = Math.round(percentComplete) + '%';
                progressBar.style.display = 'block'; // Show the progress bar
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('Files uploaded successfully');
                location.reload(); // Reload the page to show new files
            } else {
                console.error('Error uploading files');
            }
        };

        xhr.send(formData); // Send the FormData to the server
    } else {
        alert('Por favor, selecciona al menos un archivo.');
    }
}