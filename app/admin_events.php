<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: admin_events.php
 * Description: events admins post
 */
?>

<?php include 'admin_header.php'; ?>

<h2>Create New Event</h2>

<form id="eventForm">
    <!-- Match DB column name 'title' -->
    <label>Event Name: <input type="text" name="title" required></label><br><br>

    <!-- Match DB column name 'event_date' -->
    <label>Date: <input type="date" name="event_date" required></label><br><br>

    <label for="price_cents">Price (in cents):</label>
    <input type="number" name="price_cents" id="price_cents" value="500" min="0" required>

    <button type="submit">Create Event</button>
</form>

<p id="eventMsg"></p>

<hr>

<h2>Existing Events</h2>
<div id="eventList"></div>

<!-- Link to external JavaScript -->
<script src="/js/admin_events.js"></script>

<?php include 'footer.php'; ?>

