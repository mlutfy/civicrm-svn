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


<div class="crm-block crm-form-block crm-petition-form-block">

{if $cookie_message}
	{$cookie_message}
{else}

	{if !$contact_id}
		{if !empty($fbconnect)}
			<div>{ts}Sign using your Facebook Account{/ts} {$fbconnect} <div>
			<div id="nofb">{ts}Don't have a facebook account? <a href="#signwithoutfb" id="signwithoutfb">Sign here</a>{/ts} </div>
		
			{literal}
	
			<script type="text/javascript">
				jQuery(document).ready(function($) {initCiviPetition($)});
	
				function initCiviPetition ($) 			{
				   $('.crm-group').hide();//not sure we need to hide the sign button
				   $('.crm-submit-buttons').hide();
				   $('#signwithoutfb').click( function(){$('.crm-group').slideDown();$('.crm-submit-buttons').slideDown();});
				   Drupal.settings.fb.reload_url = document.baseURI;
				};		
	
		  //TODO. Check that it is called only once. not sure how not to verify that jquery.init has/has not been fired.
		  initCiviPetition ($);
			</script>
			{/literal}
		{/if}
		
		{if !empty($fblogout)}
			<div> {$fblogout} <br />&nbsp; <div>
			{literal}
			<script type="text/javascript">
				jQuery(document).ready(function($) 
				{
				   Drupal.settings.fb.reload_url = document.baseURI;
				});		
			</script>
			{/literal}		
		{/if}
	{/if}
		<div class="crm-group">
			{include file="CRM/Campaign/Form/Petition/Block.tpl" fields=$petitionContactProfile} 	
		</div>
		
		<div class="crm-group">
			{include file="CRM/Campaign/Form/Petition/Block.tpl" fields=$petitionActivityProfile} 	
		</div>
		
		<div class="crm-submit-buttons">
			{include file="CRM/common/formButtons.tpl" location="top"}
		</div>
	</div>

{/if}