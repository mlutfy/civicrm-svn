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
{* This file builds html for address block inline edit *}
{$form.oplock_ts.html}
  <table class="form-layout crm-edit-address-form crm-inline-edit-form">
    <tr>
      <td>
        <div class="crm-submit-buttons"> 
          {include file="CRM/common/formButtons.tpl"}
        </div>
      </td>
    </tr>
    {if $masterAddress.$blockId gt 0 }
        <tr><td><div class="message status"><div class="icon inform-icon"></div>&nbsp; {ts 1=$masterAddress.$blockId}This address is shared with %1 contact record(s). Modifying this address will automatically update the shared address for these contacts.{/ts}</div></td></tr>
    {/if}
     <tr>
        <td>
           <span class="crm-address-element location_type_id-address-element">
            {$form.address.$blockId.location_type_id.label}&nbsp;{$form.address.$blockId.location_type_id.html}
            </span>&nbsp;
            <!--a href="#" title="{ts}Delete Address Block{/ts}">{ts}Delete this address{/ts}</a-->
        </td>
     </tr>
     <tr>
        <td>
           <span class="crm-address-element is_primary-address-element">{$form.address.$blockId.is_primary.html}</span>
           <span class="crm-address-element is_billing-address-element">{$form.address.$blockId.is_billing.html}</span>
        </td>
     </tr>
     
     {* include shared address template *}
     {include file="CRM/Contact/Form/ShareAddress.tpl"}
 
     <tr>
      <td>
        <table id="address_table_{$blockId}" class="form-layout-compressed">
           {* build address block w/ address sequence. *}
           {foreach item=addressElement from=$addressSequence}
            {include file=CRM/Contact/Form/Edit/Address/$addressElement.tpl}
           {/foreach}
           {include file=CRM/Contact/Form/Edit/Address/geo_code.tpl}
       </table>
      </td>
     </tr>
  </table>
  
  <div class="crm-edit-address-custom_data crm-inline-edit-form crm-address-custom-set-block-{$blockId}"> 
    {include file="CRM/Contact/Form/Edit/Address/CustomData.tpl"}
  </div> 
{literal}
<script type="text/javascript">
  cj().crmaccordions();
  {/literal}{* // Enforce unique location_type_id fields *}{literal}
  cj('#address_{/literal}{$blockId}{literal}_location_type_id').change(function() {
    var ele = cj(this);
    var lt = ele.val();
    var container = ele.closest('div.crm-inline-edit.address');
    container.data('location-type-id', '');
    var ok = true;
    if (lt != '') {
      cj('.crm-inline-edit.address').each(function() {
        if (ok && cj(this).data('location-type-id') == lt) {
          var label = cj('option:selected', ele).text();
          ele.val('');
          ele.crmError(label + "{/literal} {ts escape='js'}has already been assigned to another address. Please select another location for this address.{/ts}"{literal});
          ok = false;
        }
      });
      if (ok) {
        container.data('location-type-id', lt);
      }
    }
  });
  {/literal}{* // Enforce unique is_foo fields *}{literal}
  cj(':checkbox[id*="[is_"]', 'form#Address_{/literal}{$blockId}{literal}').change(function() {
    if (cj(this).is(':checked')) {
      var ids = cj(this).attr('id').slice(-9);
      cj('.crm-inline-edit.address.form :checkbox:checked[id$="' + ids + '"]').not(this).removeAttr('checked');
    }
    else if (cj(this).is("[id*=is_primary]")) {
      cj(this).crmError("{/literal} {ts escape='js'}Please choose another address to be primary before changing this one.{/ts}");
      cj(this).attr('checked', 'checked');
    }
  });
  {/literal}{* // Reset location_type_id when cancel button pressed and enforce on other addr forms *}{literal}
  cj(':submit[name$=cancel]', 'form#Address_{/literal}{$blockId}{literal}').click(function() {
    var container = cj(this).closest('div.crm-inline-edit.address');
    var origValue = container.attr('data-location-type-id') || '';
    container.data('location-type-id', origValue);
    cj(':input[id$=_location_type_id]', '.crm-inline-edit.address').each(function() {
      if (cj(this).val() == origValue) {
        cj(this).val('').change();
      }
    });
  });
</script>
{/literal}
