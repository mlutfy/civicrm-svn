{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
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
{* Voting Tab Interface - Easy way to get the voter Interview. *}
{if $subPageType eq 'reserve'}
   {* build the voter interview grid here *}
   {include file='CRM/Campaign/Form/Task/Interview.tpl'}
{elseif $subPageType eq 'interview'}
   {* build the ajax search and voters reserve interface here *}
   {include file='CRM/Campaign/Form/Gotv.tpl'}
{else}
 {* build normal page *}
 <div id='votingTabs' class="ui-tabs ui-widget ui-widget-content ui-corner-all">
     <ul class="crm-vote-tabs-list">
           {foreach from=$allTabs key=tabName item=tabValue}
           <li id="tab_{$tabValue.id}" class="crm-tab-button ui-corner-bottom">
            	<a href="{$tabValue.url}" title="{$tabValue.title}"><span></span>{$tabValue.title}</a>
           </li>
           {/foreach}
     </ul>
 </div>
 <div class="spacer"></div>

{literal}
<script type="text/javascript">

//explicitly stop spinner
function stopSpinner( ) {
  cj('li.crm-tab-button').each(function(){ cj(this).find('span').text(' ');})	 
}

cj(document).ready( function( ) {
     {/literal}
     var spinnerImage = '<img src="{$config->resourceBase}i/loading.gif" style="width:10px;height:10px"/>';
     {literal} 
     
     var selectedTabIndex = {/literal}{$selectedTabIndex}{literal};
     cj("#votingTabs").tabs( { 
                             selected: selectedTabIndex, 
                             spinner: spinnerImage, 
		             cache: false, 
		             load: stopSpinner 
		           });
});
           
</script>
{/literal}
{/if}
