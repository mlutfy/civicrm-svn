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
<div id="crm-contactinfo-content" class="crm-table2div-layout{if $permission EQ 'edit'} crm-inline-edit" data-edit-params='{ldelim}"cid": "{$contactId}", "class_name": "CRM_Contact_Form_Inline_ContactInfo"{rdelim}' title="{ts}Add or edit info{/ts}{/if}">
  <div class="crm-clear"><!-- start of main -->
    {if $permission EQ 'edit'}
    <div class="crm-edit-help">
      <span class="batch-edit"></span>{ts}Add or edit info{/ts}
    </div>
    {/if}

      {if $contact_type eq 'Individual'}
      <div class="crm-label">{ts}Employer{/ts}</div>
      <div class="crm-content crm-contact-current_employer">
        {if !empty($current_employer_id)} 
        <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$current_employer_id`"}" title="{ts}view current employer{/ts}">{$current_employer}</a>
        {/if}
      </div>
      <div class="crm-label">{ts}Position{/ts}</div>
      <div class="crm-content crm-contact-job_title">{$job_title}</div>
      {/if}
      <div class="crm-label">{ts}Nickname{/ts}</div>
      <div class="crm-content crm-contact-nick_name">{$nick_name}</div>

      {if $contact_type eq 'Organization'}
      <div class="crm-label">{ts}Legal Name{/ts}</div>
      <div class="crm-content crm-contact-legal_name">{$legal_name}</div>
      <div class="crm-label">{ts}SIC Code{/ts}</div>
      <div class="crm-content crm-contact-sic_code">{$sic_code}</div>
      {/if}
      <div class="crm-label">{ts}Source{/ts}</div>
      <div class="crm-content crm-contact_source">{$source}</div>

    </div> <!-- end of main -->
</div>
