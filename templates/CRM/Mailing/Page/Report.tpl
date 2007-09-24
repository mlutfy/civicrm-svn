{if $report.jobs.0.start_date}
<fieldset>
<legend>{ts}Delivery Summary{/ts}</legend>
  {strip}
  <table class="form-layout">
  <tr><td class="label"><a href="{$report.event_totals.links.queue}">{ts}Intended Recipients{/ts}</a></td><td>{$report.jobs.0.queue}</td></tr>
  <tr><td class="label"><a href="{$report.event_totals.links.delivered}">{ts}Succesful Deliveries{/ts}</a></td><td>{$report.jobs.0.delivered} ({$report.jobs.0.delivered_rate|string_format:"%0.2f"}%)</td></tr>
  <tr><td class="label">{ts}Spooled Mails{/ts}</td><td>{$report.jobs.0.spool}</td></tr>
  {if $report.mailing.open_tracking}
    <tr><td class="label"><a href="{$report.event_totals.links.opened}">{ts}Tracked Opens{/ts}</a></td><td>{$report.jobs.0.opened}</td></tr>
  {/if}
  {if $report.mailing.url_tracking}
    <tr><td class="label"><a href="{$report.event_totals.links.clicks}">{ts}Click-throughs{/ts}</a></td><td>{$report.jobs.0.url}</td></tr>
  {/if}
  <tr><td class="label"><a href="{$report.event_totals.links.forward}">{ts}Forwards{/ts}</a></td><td>{$report.jobs.0.forward}</td></tr>
  <tr><td class="label"><a href="{$report.event_totals.links.reply}">{ts}Replies{/ts}</a></td><td>{$report.jobs.0.reply}</td></tr>
  <tr><td class="label"><a href="{$report.event_totals.links.bounce}">{ts}Bounces{/ts}</a></td><td>{$report.jobs.0.bounce} ({$report.jobs.0.bounce_rate|string_format:"%0.2f"}%)</td></tr>
  <tr><td class="label"><a href="{$report.event_totals.links.unsubscribe}">{ts}Unsubscribe Requests{/ts}</a></td><td>{$report.jobs.0.unsubscribe} ({$report.jobs.0.unsubscribe_rate|string_format:"%0.2f"}%)</td></tr>
  <tr><td class="label">{ts}Scheduled Date{/ts}</td><td>{$report.jobs.0.scheduled_date}</td></tr>
  <tr><td class="label">{ts}Status{/ts}</td><td>{$report.jobs.0.status}</td></tr>
  <tr><td class="label">{ts}Start Date{/ts}</td><td>{$report.jobs.0.start_date}</td></tr>
  <tr><td class="label">{ts}End Date{/ts}</td><td>{$report.jobs.0.end_date}</td></tr>
  </table>
  {/strip}
</fieldset>
{/if}


{if $report.mailing.url_tracking && $report.click_through|@count > 0}
<fieldset>
<legend>{ts}Click-through Summary{/ts}</legend>
{strip}
<table>
<tr>
<th><a href="{$report.event_totals.links.clicks}">{ts}Clicks{/ts}</a></th>
<th><a href="{$report.event_totals.links.clicks_unique}">{ts}Unique Clicks{/ts}</a></th>
<th>{ts}Success Rate{/ts}</th>
<th>{ts}URL{/ts}</th></tr>
{foreach from=$report.click_through item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{if $row.clicks > 0}<a href="{$row.link}">{$row.clicks}</a>{else}{$row.clicks}{/if}</td>
<td>{if $row.unique > 0}<a href="{$row.link_unique}">{$row.unique}</a>{else}{$row.unique}{/if}</td>
<td>{$row.rate|string_format:"%0.2f"}%</td>
<td><a href="{$row.url}">{$row.url}</a></td>
</tr>
{/foreach}
</table>
{/strip}
</fieldset>
{/if}


<fieldset>
<legend>{ts}Recipients{/ts}</legend>
{if $report.group.include|@count}
<span class="label">{ts}Included{/ts}</span>
{strip}
<table>
{foreach from=$report.group.include item=group}
<tr class="{cycle values="odd-row,even-row"}">
<td>
{if $group.mailing}
{ts}Recipients of <a href="{$group.link}">{$group.name}</a>{/ts}
{else}
{ts}Members of <a href="{$group.link}">{$group.name}</a>{/ts}
{/if}
</td>
</tr>
{/foreach}
</table>
{/strip}
{/if}

{if $report.group.exclude|@count}
<span class="label">{ts}Excluded{/ts}</span>
{strip}
<table>
{foreach from=$report.group.exclude item=group}
<tr class="{cycle values="odd-row,even-row"}">
<td>
{if $group.mailing}
{ts}Recipients of <a href="{$group.link}">{$group.name}</a>{/ts}
{else}
{ts}Members of <a href="{$group.link}">{$group.name}</a>{/ts}
{/if}
</td>
</tr>
{/foreach}
</table>
{/strip}
{/if}
</fieldset>


<fieldset>
<legend>
    {ts}Mailing Settings{/ts}
</legend>
{strip}
<table class="form-layout">
<tr><td class="label">{ts}Mailing Name{/ts}</td><td>{$report.mailing.name}</td></tr>
<tr><td class="label">{ts}Subject{/ts}</td><td>{$report.mailing.subject}</td></tr>
<tr><td class="label">{ts}From{/ts}</td><td>{$report.mailing.from_name} &lt;{$report.mailing.from_email}&gt;</td></tr>
<tr><td class="label">{ts}Reply-to email{/ts}</td><td>&lt;{$report.mailing.replyto_email}&gt;</td></tr>

<tr><td class="label">{ts}Forward replies{/ts}</td><td>{if $report.mailing.forward_replies}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Auto-respond to replies{/ts}</td><td>{if $report.mailing.auto_responder}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>

<tr><td class="label">{ts}Open tracking{/ts}</td><td>{if $report.mailing.open_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}URL Click-through tracking{/ts}</td><td>{if $report.mailing.url_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
</table>
{/strip}
</fieldset>

<fieldset>
<legend>{ts}Content / Components{/ts}</legend>
{strip}
<table class="form-layout">
{foreach from=$report.component item=component}
<tr><td class="label">{$component.type}</td><td>
<a href="{$component.link}">{$component.name}</a></td></tr>
{/foreach}
{if $report.mailing.body_text}
<tr>
  <td class="label">{ts}Text Body{/ts}<br />
    <small><a href='{$textViewURL}'>{ts}View Text Body{/ts}</a></small>
  </td>
  <td class="report">{$report.mailing.body_text|escape|nl2br}</td>
</tr>
{/if}
{if $report.mailing.body_html}
<tr>
  <td class="label">{ts}HTML Body{/ts}<br/>
    <small><a href='{$htmlViewURL}'>{ts}View HTML Body{/ts}</a></small>
  </td>
  <td class="report">{$report.mailing.body_html|escape|nl2br}</td></tr>
{/if}
</table>
{/strip}
</fieldset>




