{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
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
{* template for building website block *}
<div id="crm-website-content" class="crm-table2div-layout{if $permission EQ 'edit'} crm-inline-edit" data-edit-params='{ldelim}"cid": "{$contactId}", "class_name": "CRM_Contact_Form_Inline_Website"{rdelim}' title="{ts}Add or edit website{/ts}{/if}">
  <div class="crm-clear"><!-- start of main -->
    {if $permission EQ 'edit'}
      <div class="crm-edit-help">
        <span class="batch-edit"></span>{if empty($website)}{ts}Add website{/ts}{else}{ts}Add or edit website{/ts}{/if}
      </div>
    {/if}
    {if empty($website)}
      <div class="crm-label">{ts}Website{/ts}</div>
      <div class="crm-content"></div>
    {/if}
    {foreach from=$website item=item}
      {if !empty($item.url)}
      <div class="crm-label">{$item.website_type} {ts}Website{/ts}</div>
      <div class="crm-content crm-contact_website"><a href="{$item.url}" target="_blank">{$item.url}</a></div>
      {/if}
    {/foreach}

    </div> <!-- end of main -->
</div>