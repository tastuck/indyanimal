<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: dashboard.php
 * Description: user dashboard
 */

?>

<?php include 'header.php'; ?>

<h2>Welcome to Indyanimal</h2>

<section>
    <h3>Upcoming Events</h3>
    <ul id="upcoming-events">
        <li>Loading events...</li>
    </ul>
</section>

<section>
    <h3>Search Media</h3>
    <form id="search-form">
        <label for="search-event">Event Title:</label>
        <input type="text" id="search-event" name="event">

        <label for="search-date">Event Date:</label>
        <div class="date-wrapper">
            <input type="date" id="search-date" name="date">
            <button type="button" id="clear-date" title="Clear date">&times;</button>
        </div>



        <label for="search-tags">Tags:</label>
        <select id="search-tags" name="tags[]" multiple style="width: 100%;"></select>

        <button type="submit">Search</button>
    </form>
</section>

<section>
    <h3>Search Results</h3>
    <div id="search-results">No results yet.</div>
</section>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/js/dashboard.js"></script>

<?php include 'footer.php'; ?>
