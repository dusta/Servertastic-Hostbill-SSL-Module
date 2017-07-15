<table>
    <tr>
        <td><strong>Configuration Status:</strong></td>
        <td>{$order.status}</td>
    </tr>

    {if isset($order.url.configure) AND !empty(isset($order.url.configure))}
    <tr>
    	<td>Configuration Page</td>
    	<td><a href="{$order.url.configure}" target="_blank">Configure Now</a></td>
    </tr>
    {/if}
</table>


