<?php
/**
 * Author: Taniya Tucker
 * Date: 6/25/25
 * File: events.php
 * Description:
 */

?>

<?php include 'header.php'; ?>

<h2>Browse Events</h2>

<form id="eventSearchForm">
    <label for="title">Event Title:</label>
    <input type="text" id="title" name="title" placeholder="Search by title">

    <label for="date">Event Date:</label>
    <input type="date" id="date" name="date">

    <button type="submit">Search</button>
</form>

<div id="eventResults">
    <p>Loading events...</p>
</div>

<script src="/js/events.js"></script>

<?php include 'footer.php'; ?>
