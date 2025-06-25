document.getElementById('eventForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const plain = Object.fromEntries(formData.entries());

    const res = await fetch('/admin/event/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(plain)
    });

    document.getElementById('eventMsg').innerText = await res.text();
    e.target.reset();
    loadEvents();
});

async function loadEvents() {
    const res = await fetch('/api/events');
    const events = await res.json();
    const container = document.getElementById('eventList');
    container.innerHTML = '';

    events.forEach(event => {
        const wrapper = document.createElement('div');
        wrapper.style.border = '1px solid #ccc';
        wrapper.style.padding = '1em';
        wrapper.style.marginBottom = '1em';

        const form = document.createElement('form');
        form.innerHTML = `
            <strong>Edit Event #${event.event_id}</strong><br><br>
            <label>Name: <input type="text" name="title" value="${event.title}" required></label><br><br>
            <label>Date: <input type="date" name="event_date" value="${event.event_date}" required></label><br><br>
            <label>Price (in cents): <input type="number" name="price_cents" value="${event.price_cents || 500}" required></label><br><br>
            <button type="submit">Update Event</button>
            <p class="eventStatus"></p>
        `;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const plain = Object.fromEntries(formData.entries());

            const res = await fetch(`/admin/event/update/${event.event_id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(plain)
            });

            form.querySelector('.eventStatus').innerText = await res.text();
            loadEvents();
        });

        wrapper.appendChild(form);

        const stageForm = document.createElement('form');
        stageForm.innerHTML = `
            <h4>Add Stage to Event</h4>
            <input type="hidden" name="event_id" value="${event.event_id}">
            <label>Stage Name: <input type="text" name="name" required></label><br><br>
            <label>Description: <input type="text" name="description"></label><br><br>
            <button type="submit">Add Stage</button>
            <p class="stageStatus"></p>
        `;

        stageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(stageForm);
            const plain = Object.fromEntries(formData.entries());

            const res = await fetch('/admin/stage/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(plain)
            });

            stageForm.querySelector('.stageStatus').innerText = await res.text();
            stageForm.reset();
        });

        wrapper.appendChild(stageForm);
        container.appendChild(wrapper);
    });
}

loadEvents();

