{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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

{literal}
<script type="text/javascript">

function buildPaymentBlock( type )
{
	var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4&type='}"{literal} + type; 

	console.log(type);
	
	{/literal}
		{if $urlPathVar}
			dataUrl = dataUrl + '&' + '{$urlPathVar}'
		{/if}
	
		{if $qfKey}
			dataUrl = dataUrl + '&qfKey=' + '{$qfKey}'
		{/if}
	
	{literal}

	console.log(dataUrl);
	var fname = '#payment_information';	
	var response = cj.ajax({	
                                                url: dataUrl,
                                                async: false
                                        }).responseText;

        cj( fname ).html( response );	

}

</script>
{/literal}
