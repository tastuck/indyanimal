<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: admindashboard.php
 * Description:
 */
?>

<?php include 'admin_header.php';?>

<h2>Admin Dashboard</h2>

<ul style="list-style: none; padding-left: 0;">
    <li style="margin-bottom: 1em;">
        <a href="/admin/invites">
            <button>Generate Invite Code</button>
        </a>
    </li>

    <li style="margin-bottom: 1em;">
        <a href="/admin/media">
            <button>Approve Media Submissions</button>
        </a>
    </li>

    <li style="margin-bottom: 1em;">
        <a href="/admin/events">
            <button>Create New Event</button>
        </a>
    </li>

    <li style="margin-bottom: 1em;">
        <a href="/admin/stages">
            <button>Create New Stage</button>
        </a>
    </li>
</ul>






<?php include 'footer.php'; ?>


