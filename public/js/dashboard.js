document.addEventListener('DOMContentLoaded', async () => {
    const eventList = document.getElementById('upcoming-events');
    const tagSelect = $('#search-tags');
    const resultDiv = document.getElementById('search-results');

    //clear date stuff
    document.getElementById('clear-date').addEventListener('click', () => {
        document.getElementById('search-date').value = '';
    });

    // turn the tag box into a multi-select thing
    tagSelect.select2({
        placeholder: "Select tags",
        allowClear: true
    });

    // force the dropdown to behave visually
    $('.select2-container').attr('style', 'width: 100% !important; z-index: 1000;');

    // load upcoming events and show them in the list
    try {
        const eventRes = await fetch('/api/events/upcoming');
        const events = await eventRes.json();
        eventList.innerHTML = '';
        if (events.length === 0) {
            eventList.innerHTML = '<li>No upcoming events.</li>';
        } else {
            events.forEach(event => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${event.title}</strong> — ${event.event_date} 
                                <a href="/event/${event.event_id}">View</a>`;
                eventList.appendChild(li);
            });
        }
    } catch (err) {
        eventList.innerHTML = '<li>Error loading events.</li>';
    }

    // get all tags and drop them into the select box
    try {
        const tagRes = await fetch('/api/tags');
        const tags = await tagRes.json();
        tags.forEach(tag => {
            const option = new Option(`${tag.name} (${tag.tag_type})`, tag.tag_id, false, false);
            tagSelect.append(option);
        });
        tagSelect.trigger('change');
    } catch (err) {
        console.error('Error loading tags:', err);
    }

    // handle the search form when the user clicks search
    document.getElementById('search-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        resultDiv.textContent = 'Searching...';

        const formData = new FormData(e.target);
        const params = new URLSearchParams(formData);
        params.set('include_archived', 'true');

        const eventTitle = formData.get('event')?.trim() || '';
        const eventDate = formData.get('date') || '';
        const selectedTagNames = Array.from(tagSelect.find(':selected')).map(opt => opt.textContent);
        const tagsText = selectedTagNames.length > 0 ? selectedTagNames.join(', ') : '';
        /*const tags = formData.getAll('tags[]');
        const tagsText = tags.length > 0 ? tags.join(', ') : '';
*/
        try {
            const res = await fetch('/media/search?' + params.toString());
            const data = await res.json();

            // if nothing matched the search
            if (!Array.isArray(data) || data.length === 0) {
                const filters = [];

                if (eventTitle) {
                    filters.push(`event title <strong>${eventTitle}</strong>`);
                }
                if (eventDate) {
                    filters.push(`event date <strong>${eventDate}</strong>`);
                }
                if (selectedTagNames.length > 0) {
                    filters.push(`tags <strong>${tagsText}</strong>`);
                }

                /*if (tags.length > 0) {
                    filters.push(`tags <strong>${tagsText}</strong>`);
                }*/

                const reasonText = filters.length > 0
                    ? 'for ' + filters.join(', ')
                    : 'with no filters applied';

                resultDiv.innerHTML = `
                    <p><strong>No media results found</strong> ${reasonText}.</p>
                `;
                //clear after results
                document.getElementById('search-form').reset();
                tagSelect.val(null).trigger('change');


            } else {
                // show all matching media
                resultDiv.innerHTML = data.map(media => {
                    let display;

                    if (media.media_type === 'image') {
                        display = `<img src="${media.filepath}" alt="Image" style="max-width: 300px; height: auto;">`;
                    } else if (media.media_type === 'video') {
                        display = `<video src="${media.filepath}" controls style="max-width: 300px;"></video>`;
                    } else {
                        display = `<a href="${media.filepath}" target="_blank">${media.filepath}</a>`;
                    }

                    return `
                        <div style="margin-bottom: 16px;">
                            <p><strong>Type:</strong> ${media.media_type}</p>
                            <p><strong>File:</strong> ${display}</p>
                        </div>
                    `;
                }).join('');
            }
        } catch (err) {
            resultDiv.textContent = 'Search failed.';
            console.error(err);
        }
    });
});
