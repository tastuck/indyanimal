<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: admindashboard.php
 * Description:
 */
?>

<?php
use api\Models\AdminModel;
use api\Authentication\SessionManager;

include 'admin_header.php';

$adminId = SessionManager::getUserId();
$eventCount = AdminModel::countActiveEventsForAdmin($adminId);
?>

<h2>Admin Dashboard</h2>

<section>
    <h3>Summary</h3>
    <ul style="list-style: none; padding-left: 0; line-height: 2;">
        <li>This section would pull from Stripe, orders, and events
                <br> to give admins summary stats updated dynamically </li>
        <li><strong>Active Events:</strong> <?= $eventCount ?></li>
        <li><strong>Total Ticket Revenue (via Stripe):</strong> $12,540.00</li>
        <li><strong>Tickets Sold This Month:</strong> 287</li>
        <li><strong>Top Event:</strong> Emo Night </li>
    </ul>
</section>

<hr>

<section>
    <h3>Stripe Overview (Mock Data)</h3>
    <p>This section would show real Stripe analytics, such as:</p>
    <ul style="line-height: 2;">
        <li>⚡ Revenue trends by day/week/month</li>
        <li>🧾 Refunds issued and dispute activity</li>
        <li>📈 Conversion rates from invite → ticket purchase</li>
        <li>💳 Payout schedules and gross/net balances</li>
    </ul>
    <p style="font-style: italic; color: #555;">Stripe integration planned but not implemented.</p>
</section>

<?php include 'footer.php'; ?>
