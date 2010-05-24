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
<div class="crm-form-block crm-block crm-add_to_tag-block">
<h3>
{ts}Tag Contact(s){/ts}
</h3>

<table class="form-layout">
    <tr>
        <td>
            <div class="listing-box">
            {foreach from=$form.tag item="tag_val"}
                <div class="{cycle values="odd-row,even-row"}">
                {$tag_val.html}
            {/foreach}
            </div>
        </td>
    </tr>
    <tr>
        <td>
            {include file="CRM/common/Tag.tpl"}
        </td>
    </tr>

    <tr><td>{include file="CRM/Contact/Form/Task.tpl"}</td></tr>
    <tr><td>{$form.buttons.html}</td></tr>
</table>
</div>