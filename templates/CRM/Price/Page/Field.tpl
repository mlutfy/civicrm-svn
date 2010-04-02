{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Price/Form/Field.tpl"}
{elseif $action eq 8 and !$usedBy}
    {include file="CRM/Price/Form/DeleteField.tpl"}
{elseif $action eq 1024 }
    {include file="CRM/Price/Form/Preview.tpl"}
{else}

 {if $usedBy}
    <div class='spacer'></div>
    <div id="price_set_used_by" class="messages status">
      <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>      
      <dd>
        {if $action eq 8}
            {ts 1=$usedPriceSetTitle}Unable to delete the '%1' Price Field - it is currently in use by one or more active events or contribution pages or contributions.{/ts}
       	{/if}<br />        
        
	{if $usedBy.civicrm_event or $usedBy.civicrm_contribution_page} 
            {include file="CRM/Price/Page/table.tpl"} 
        {/if}

      </dd>
      </dl>
    </div>
    {/if}



    {if $priceField}
    
    <div id="field_page">
     <p></p>
        {strip}
	{* handle enable/disable actions*}
 	{include file="CRM/common/enableDisable.tpl"}
    {include file="CRM/common/jsortable.tpl"}
         <table id="options" class="display">
         <thead>
         <tr>
            <th>{ts}Field Label{/ts}</th>
            <th>{ts}Field Type{/ts}</th>
            <th id="order" class="sortable">{ts}Order{/ts}</th>
            <th>{ts}Req?{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
{*
            <th>{ts}Active On{/ts}</th>
            <th>{ts}Expire On{/ts}</th>
*}
            <th id="nosort">{ts}Price{/ts}</th>
            <th></th>
            <th class="hiddenElement"></th>
        </tr>
        </thead>
        {foreach from=$priceField key=fid item=row}
	    <tr id="row_{$row.id}"class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
            <td>{$row.html_type}</td>
            <td class="nowrap">{$row.order}</td>
            <td>{if $row.is_required eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
{*
            <td>{if $row.active_on}{$row.active_on|date_format:"%Y-%m-%d"}{/if}</td>
            <td>{if $row.expire_on}{$row.expire_on|date_format:"%Y-%m-%d"}{/if}</td>
*}
            <td>{if $row.html_type eq "Text"}{$row.price|crmMoney}{else}<a href="{crmURL p="civicrm/admin/price/field/option" q="action=browse&reset=1&sid=$sid&fid=$fid"}">{ts}Edit Price Options{/ts}</a>{/if}</td>
            <td>{$row.action|replace:'xx':$row.id}</td>
            <td class="order hiddenElement">{$row.weight}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        <table class="form-layout-compressed">
            <tr>
                <td><a href="{crmURL q="reset=1&action=add&sid=$sid"}" id="newPriceField" class="button"><span><div class="icon add-icon"></div>{ts}Add Price Field{/ts}</span></a></td>
                <td style="vertical-align: top"><a href="{crmURL p="civicrm/admin/price" q="action=preview&sid=`$sid`&reset=1&context=field"}">&raquo; {ts}Preview this Price Set (all fields){/ts}</a></td>
            </tr>
        </table>
     </div>

    {else}
        {if $action eq 16}
        <div class="messages status">
        <dl>
        <dt><div class="icon inform-icon"></div></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/price/field q="action=add&reset=1&sid=$sid"}{/capture}
        <dd>{ts 1=$groupTitle 2=$crmURL}There are no fields for price set '%1', <a href='%2'>add one</a>.{/ts}</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
