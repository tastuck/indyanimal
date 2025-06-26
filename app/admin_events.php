<?php include 'admin_header.php'; ?>

<h2>Event Management</h2>

<label>
    <input type="radio" name="mode" value="create" checked> Create New Event
</label>
<label style="margin-left: 1em;">
    <input type="radio" name="mode" value="update"> Update Existing Event
</label>

<br><br>

<div id="updateSelect" style="display:none;">
    <label for="eventSelector">Select an event to update:</label>
    <select id="eventSelector">
        <option value="">-- Choose Event --</option>
    </select>
</div>

<br>

<form id="eventForm">
    <input type="hidden" name="event_id" id="event_id">

    <label>Event Name:
        <input type="text" name="title" id="title" required>
    </label><br><br>

    <label>Date:</label>
    <div class="date-wrapper">
        <input type="date" name="event_date" id="event_date" required>
        <button type="button" id="clear-date" title="Clear date">&times;</button>
    </div>
    <br><br>


    <label>Price (in cents):
        <input type="number" name="price_cents" id="price_cents" value="500" min="0" required>
    </label><br><br>

    <button type="submit" id="submitBtn">Create Event</button>
</form>

<p id="eventMsg"></p>

<script src="/js/admin_events.js"></script>

<?php include 'footer.php'; ?>
