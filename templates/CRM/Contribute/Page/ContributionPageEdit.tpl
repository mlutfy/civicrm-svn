{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<h2>{$title}</h2>                                
<div class="messages status">
    <dl>
    {if $is_active}
        <dt><img src="{$config->resourceBase}i/traffic_green.gif" alt="{ts}status{/ts}"/></dt>
        <dd><p><a href="{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}">&raquo; {ts}Go to this LIVE Online Contribution page{/ts}</a></p>
        <p>{ts}Create links to this contribution page by copying and pasting the following URL into any web page.{/ts}:<br />
        <a href="{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}">{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}</a>
        </dd>
    {else}
        <dt><img src="{$config->resourceBase}i/traffic_red.gif" alt="{ts}status{/ts}"/></dt>
        <dd><p>{ts}This page is currently <strong>inactive</strong> (not accessible to visitors).{/ts}</p>
        {capture assign=crmURL}{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}{/capture}
        <p>{ts 1=$crmURL}When you are ready to make this page live, click <a href="%1">Title and Settings</a> and update the <strong>Active?</strong> checkbox.{/ts}</p></dd>
    {/if}
    </dl>
</div>

<div id="help">
    {ts 1="http://wiki.civicrm.org/confluence//x/1Cs" 2=$docURLTitle}Use the links below to update features and content for this Online Contribution Page, as well as to run through the contribution process in <strong>test mode</strong>.
    Refer to the <a href="%1" target="_blank" title="%2">CiviContribute Administration Documentation</a> for more information.{/ts}
</div>
<table class="report"> 
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}" id="idTitleAndSettings">&raquo; {ts}Title and Settings{/ts}</a></td>
    <td>{ts}Set page title, contribution type (donation, campaign contribution, etc.), goal amount, introduction, honoree features, and page status (active or disabled).{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Amount"}" id="idContributionAmounts">&raquo; {ts}Contribution Amounts{/ts}</a></td>
    <td>
        {ts}Configure contribution amount options and labels, minimum and maximum amounts.{/ts}
        {if $config->paymentProcessor EQ 'PayPal_Standard'}{ts}Enable recurring contributions.{/ts}{/if}
    </td>
</tr>
{if $CiviMember}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Membership"}" id="idMembershipSettings">&raquo; {ts}Membership Settings{/ts}</a></td>
    <td>{ts}Configure membership sign-up and renewal options.{/ts}</td>
</tr>
{/if}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=ThankYou"}" id="idThank-youandReceipting">&raquo; {ts}Thank-you and Receipting{/ts}</a></td>
    <td>{ts}Edit thank-you page contents and receipting features.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Custom"}" id="idCustomPageElements">&raquo; {ts}Custom Page Elements{/ts}</a></td>
    <td>{ts}Collect additional information from contributors by selecting CiviCRM Profile(s) to include in this contribution page.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Premium"}" id="idPremiums">&raquo; {ts}Premiums{/ts}</a></td>
    <td>{ts}Enable a Premiums section (incentives / thank-you gifts) for this page, and configure premiums offered to contributors.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/contribute/transact' q="reset=1&action=preview&id=`$id`"}" id="idTest-drive">&raquo; {ts}Test-drive{/ts}</a></td>
    <td>{ts}Test-drive the entire contribution process - including custom fields, confirmation, thank-you page, and receipting. Transactions will be directed to your payment processor's test server. <strong>No live financial transactions will be submitted. However, a contact record will be created or updated and a contribution record will be saved to the database. Use obvious test contact names so you can review and delete these records as needed.</strong>{/ts}</td>
</tr>
{if $is_active}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}" id="idLive">&raquo; {ts}Live Contribution Page{/ts}</a></td>
    <td>{ts}Review your customized <strong>LIVE</strong> online contribution page here. Use the following URL in links and buttons on any website to send visitors to this live page{/ts}:<br />
        <strong>{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}</strong>
    </td>
</tr>
{/if}

</table>
