<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
     @vite(['resources/js/app.js'])
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Upload CSV</h1>

        <!-- Upload Area -->
        <form id="upload-form" class="border-2 border-dashed border-gray-300 p-6 rounded text-center bg-gray-50">
            <p class="mb-2 text-gray-500">Drag and drop your CSV file here or click to upload</p>
            <input type="file" id="file" name="file" accept=".csv" class="hidden" />
            <button type="button" onclick="document.getElementById('file').click()"
                class="px-4 py-2 bg-blue-600 text-white rounded">Select File</button>
        </form>

        <!-- Upload Status -->
        <div id="upload-status" class="mt-4 text-green-600 font-semibold hidden">Uploading...</div>

        <!-- Upload History -->
        <h2 class="text-xl font-semibold mt-8 mb-4">Upload History</h2>
        <table class="w-full border text-sm" id="upload-table">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">File Name</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Uploaded At</th>
                    <th class="p-2 border">Time Ago</th>
                </tr>
            </thead>
            <tbody id="upload-list">
                <!-- Data will load here -->
            </tbody>
        </table>
    </div>

    <script>
        const fileInput = document.getElementById('file');
        const form = document.getElementById('upload-form');
        const statusDiv = document.getElementById('upload-status');
        const uploadList = document.getElementById('upload-list');

        fileInput.addEventListener('change', async function () {
            const file = fileInput.files[0];
            if (!file) return;

            statusDiv.classList.remove('hidden');
            statusDiv.textContent = "Uploading...";

            const formData = new FormData();
            formData.append('file', file);

            try {
                await fetch('/api/upload', {
                    method: 'POST',
                    body: formData
                });

                statusDiv.textContent = "Upload successful!";
                loadUploads();
            } catch (error) {
                statusDiv.textContent = "Upload failed.";
            } finally {
                fileInput.value = '';
                setTimeout(() => statusDiv.classList.add('hidden'), 3000);
            }
        });

        async function loadUploads() {
            const res = await fetch('/api/uploads');
            const data = await res.json();

            uploadList.innerHTML = data.data.map(upload => `
                <tr>
                    <td class="p-2 border">${upload.filename}</td>
                    <td class="p-2 border">
                        <span class="${getStatusColor(upload.status)} px-2 py-1 rounded">
                            ${upload.status}
                        </span>
                    </td>
                    <td class="p-2 border">${upload.created_at}</td>
                    <td class="p-2 border text-gray-500">${upload.time_ago}</td>
                </tr>
            `).join('');
        }

        function getStatusColor(status) {
            switch (status) {
                case 'pending': return 'bg-yellow-200 text-yellow-700';
                case 'processing': return 'bg-blue-200 text-blue-700';
                case 'completed': return 'bg-green-200 text-green-700';
                case 'failed': return 'bg-red-200 text-red-700';
                default: return '';
            }
        }

        loadUploads();
        setInterval(loadUploads, 5000); // Poll every 5 seconds
        // window.onload = function() {
        //     window.Echo.channel('uploads')
        //         .listen('UploadStatusUpdated', (e) => {
        //             console.log('Real-time update:', e);
        //             loadUploads(); // Refresh the table
        //     });
        // }
        // window.Echo.channel('uploads')
        //     .listen('UploadStatusUpdated', (e) => {
        //         console.log('Real-time update:', e);
        //         loadUploads(); // Refresh the table
        // });
    </script>
</body>

</html>