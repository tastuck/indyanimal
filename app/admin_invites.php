<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: admin_invites.php
 * Description: invite code generator
 */
?>
<?php include 'admin_header.php'; ?>

<h2>Generate Invite Code</h2>

<form id="inviteForm" method="post" action="javascript:void(0);">
    <button type="submit">Generate New Code</button>
</form>

<p id="inviteResult"></p>

<script src="/js/admin_invites.js"></script> <!-- load the JS file -->

<?php include 'footer.php'; ?>
