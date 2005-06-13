{* this template is used for adding/editing group (name and description only)  *}
<div id="help">
    {if $action eq 2}
        {capture assign=crmURL}{crmURL p="civicrm/group/search" q="reset=1&force=1&context=smog&gid=`$group.id`"}{/capture}
        {ts 1=$crmURL}You can edit the Name and Description for this group here. Click <a href="%1">Show Group Members</a> from Manage Groups to view, add or remove contacts in this group.{/ts}
    {else}
        {ts}Enter a unique name and a description for your new group here. Then click 'Continue' to find contacts to add to your new group.{/ts}
    {/if}
</div>
<div class="form-item">
<fieldset><legend>{ts}Group Name and Description{/ts}</legend>
    <dl>
        <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
    </fieldset>
</div>
