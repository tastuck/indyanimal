document.addEventListener('DOMContentLoaded', () => {
    fetch('/admin/media/pending')
        .then(res => res.json())
        .then(data => renderMedia(data));
});

function renderMedia(mediaList) {
    const container = document.getElementById('mediaList');
    if (!mediaList.length) {
        container.innerHTML = '<p>No pending media.</p>';
        return;
    }

    const table = document.createElement('table');
    table.innerHTML = `
    <thead>
      <tr>
        <th>ID</th>
        <th>Preview</th>
        <th>Type</th>
        <th>Category</th>
        <th>Event</th>
        <th>Uploaded</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      ${mediaList.map(media => `
        <tr>
          <td>${media.media_id}</td>
          <td>${media.media_type === 'image'
        ? `<img src="${media.filepath}" width="100">`
        : `<video width="150" controls><source src="${media.filepath}"></video>`}</td>
          <td>${media.media_type}</td>
          <td>${media.media_category}</td>
          <td>${media.event_title} (${media.event_date})</td>
          <td>${media.uploaded_at}</td>
          <td>
            <button onclick="moderateMedia(${media.media_id}, 'approve')">Approve</button>
            <button onclick="moderateMedia(${media.media_id}, 'reject')">Reject</button>
          </td>
        </tr>
      `).join('')}
    </tbody>
  `;
    container.appendChild(table);
}

function moderateMedia(mediaId, action) {
    fetch(`/admin/media/${action}/${mediaId}`, {
        method: 'POST'
    }).then(() => location.reload());
}

