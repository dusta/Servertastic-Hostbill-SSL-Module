<table>
<tr><td><strong>Product/Service</strong></td><td>{$product}</td></tr>
<tr><td><strong>Status</strong></td><td>{$status}</td></tr>
<tr><td><strong>Registration Date</strong></td><td>{$date_created}</td></tr>
<tr><td><strong>Billing Cycle</strong></td><td>{$billingcycle}</td></tr>
{if $id}
<tr><td><strong>Configuration Status:</strong></td><td>{$remotestatus}</td></tr>
<tr><td><strong>Update Certificate Approver Email:</strong></td><td><form method="post"><input type="text" name="newapproveremail" /><input type="submit" value="Update" /></form></td></tr>
{/if}
</table>
