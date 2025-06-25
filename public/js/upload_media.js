
document.addEventListener('DOMContentLoaded', () => {
    $('#tags').select2({
        placeholder: "Select tags",
        allowClear: true
    });

    const form = document.getElementById('uploadForm');
    const resultDiv = document.getElementById('uploadResult');
    const fileInput = document.getElementById('media_file');
    const categorySelect = document.getElementById('media_category');

    categorySelect.addEventListener('change', () => {
        if (categorySelect.value === 'lostfound') {
            fileInput.accept = 'image/*';
        } else {
            fileInput.accept = 'image/*,video/*';
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        resultDiv.textContent = 'Uploading...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                resultDiv.textContent = `Upload successful! Media ID: ${data.media_id}`;
                form.reset();
                $('#tags').val(null).trigger('change');
                fileInput.accept = 'image/*,video/*';
            } else {
                resultDiv.textContent = `Error: ${data.error || 'Unknown error'}`;
            }
        } catch (err) {
            resultDiv.textContent = 'Upload failed: ' + err.message;
        }
    });
});
