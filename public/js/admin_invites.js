document.getElementById('inviteForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const resultEl = document.getElementById('inviteResult');
    resultEl.innerText = 'Generating...';

    try {
        const res = await fetch('/admin/invite/create', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            }
        });

        const contentType = res.headers.get('content-type');

        if (!res.ok) {
            resultEl.innerText = `Error: ${res.status} ${res.statusText}`;
            return;
        }

        if (!contentType || !contentType.includes('application/json')) {
            resultEl.innerText = 'Error: Expected JSON but got something else.';
            const text = await res.text();
            console.warn('Raw response:', text);
            return;
        }

        const data = await res.json();
        resultEl.innerText = data.invite_code || 'No invite code returned.';
    } catch (err) {
        resultEl.innerText = 'Something went wrong.';
        console.error(err);
    }
});

