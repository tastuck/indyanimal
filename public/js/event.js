document.addEventListener('DOMContentLoaded', () => {
    const eventId = window.location.pathname.split('/').pop();

    // Load event info and populate page
    fetch(`/api/event/${eventId}`)
        .then(res => res.json())
        .then(event => {
            document.getElementById('eventTitle').textContent = event.title;
            document.getElementById('eventDate').textContent = `Date: ${event.event_date}`;

            const statusEl = document.getElementById('eventStatus');
            const buyBtn = document.getElementById('buyBtn');

            if (event.is_cancelled) {
                statusEl.textContent = '❌ Cancelled';
                buyBtn.style.display = 'none';
            } else if (event.is_postponed) {
                statusEl.textContent = '⚠️ Postponed';
                buyBtn.style.display = 'none';
            } else {
                statusEl.textContent = '';
                buyBtn.style.display = 'inline-block';
            }
        })
        .catch(err => {
            document.getElementById('eventTitle').textContent = 'Error loading event';
            console.error(err);
        });

    // Buy ticket logic
    const btn = document.getElementById('buyBtn');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.textContent = 'Redirecting to Stripe...';

        try {
            const res = await fetch(`/payment/create-session/${eventId}`, {
                method: 'POST',
                credentials: 'same-origin'
            });

            const data = await res.json();

            console.log('Stripe session response:', data);


            if (data.url) {
                window.location.href = data.url;
            } else {
                throw new Error(data.error || 'Failed to start checkout.');
            }
        } catch (err) {
            btn.disabled = false;
            btn.textContent = 'Buy Ticket';

            const errorDiv = document.getElementById('paymentResult');
            if (errorDiv) {
                errorDiv.textContent = err.message;
            }
        }
    });
});

