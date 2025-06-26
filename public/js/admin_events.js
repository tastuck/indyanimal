document.addEventListener('DOMContentLoaded', () => {
    const modeInputs = document.getElementsByName('mode');
    const eventSelector = document.getElementById('eventSelector');
    const updateSelect = document.getElementById('updateSelect');
    const form = document.getElementById('eventForm');
    const submitBtn = document.getElementById('submitBtn');
    const msg = document.getElementById('eventMsg');

    const titleInput = document.getElementById('title');
    const dateInput = document.getElementById('event_date');
    const priceInput = document.getElementById('price_cents');
    const idInput = document.getElementById('event_id');
    const clearDateBtn = document.getElementById('clear-date');

    let currentMode = 'create';

    modeInputs.forEach(input => {
        input.addEventListener('change', () => {
            currentMode = input.value;
            if (currentMode === 'update') {
                updateSelect.style.display = 'block';
                submitBtn.textContent = 'Update Event';
                loadEventOptions();
            } else {
                updateSelect.style.display = 'none';
                clearForm();
                submitBtn.textContent = 'Create Event';
            }
        });
    });

    async function loadEventOptions() {
        const res = await fetch('/api/admin/events/list');
        const events = await res.json();
        eventSelector.innerHTML = `<option value="">-- Choose Event --</option>`;
        events.forEach(event => {
            const opt = document.createElement('option');
            opt.value = event.event_id;
            opt.textContent = `${event.title} (${event.event_date})`;
            opt.dataset.title = event.title;
            opt.dataset.date = event.event_date;
            opt.dataset.price = event.price_cents || 500;
            eventSelector.appendChild(opt);
        });
    }

    eventSelector.addEventListener('change', () => {
        const selected = eventSelector.selectedOptions[0];
        if (!selected.value) return clearForm();
        idInput.value = selected.value;
        titleInput.value = selected.dataset.title;
        dateInput.value = selected.dataset.date;
        priceInput.value = selected.dataset.price;
    });

    function clearForm() {
        form.reset();
        idInput.value = '';
        priceInput.value = 500;
        msg.innerText = '';
    }

    clearDateBtn.addEventListener('click', () => {
        dateInput.value = '';
        dateInput.blur();
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const plain = Object.fromEntries(formData.entries());

        let url = '/admin/event/create';
        if (currentMode === 'update' && plain.event_id) {
            url = `/admin/event/update/${plain.event_id}`;
        }

        const res = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams(plain)
        });

        msg.innerText = await res.text();
        clearForm();
        if (currentMode === 'update') loadEventOptions();
    });
});
