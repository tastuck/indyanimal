<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: event.php
 * Description:
 */
?>


<?php
if (!isset($_SESSION['user'])) {
    header('Location: /signin');
    exit;
}
?>

<?php include 'header.php'; ?>

    <h2 id="eventTitle">Loading...</h2>
    <p id="eventDate"></p>
    <p id="eventStatus"></p>
    <button id="buyBtn" style="display:none;">Buy Ticket</button>
    <div id="paymentResult" style="color: red; margin-top: 0.5rem;"></div>

    <script src="/js/event.js"></script>

<?php include 'footer.php'; ?>