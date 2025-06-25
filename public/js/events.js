document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('eventSearchForm');
    const resultsDiv = document.getElementById('eventResults');

    const loadEvents = async (params = {}) => {
        const query = new URLSearchParams(params).toString();
        try {
            const res = await fetch('/api/events?' + query, {
                credentials: 'same-origin'
            });
            const events = await res.json();

            if (events.length === 0) {
                resultsDiv.innerHTML = '<p>No events found.</p>';
                return;
            }

            const html = events.map(e => `
                <div class="event-card">
                    <h3>${e.title}</h3>
                    <p>Date: ${e.event_date}</p>
                    <a href="/event/${e.event_id}">View Details</a>
                </div>
            `).join('');

            resultsDiv.innerHTML = html;
        } catch (err) {
            resultsDiv.innerHTML = '<p>Error loading events.</p>';
        }
    };

    form.addEventListener('submit', e => {
        e.preventDefault();
        const title = form.title.value.trim();
        const date = form.date.value;
        loadEvents({ title, date });
    });

    // Load all events on page load
    loadEvents();
});
