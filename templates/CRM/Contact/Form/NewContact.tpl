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
{* template for adding form elements for selecting existing or creating new contact*}
{if $context ne 'search'}
    <tr id="contact-success" style="display:none;">
	<td></td>
	<td><span class="success-status">{ts}New contact has been created.{/ts}</span></td>
    </tr>
    <tr class="crm-new-contact-form-block-contact">
    	<td class="label">{$form.contact.label}</td>
    	<td>{$form.contact.html}
    	    {if $form.profiles}
    		&nbsp;&nbsp;{ts}OR{/ts}&nbsp;&nbsp;{$form.profiles.html}<div id="contact-dialog" style="display:none;"></div>
    	    {/if}
    	</td>
    </tr>
{/if}
{literal}
<script type="text/javascript">
  var allowMultiClient = Boolean({/literal}{$multiClient}{literal});
  var newToken = '';
  var existingTokens = '';
  cj( function( ) {
      // add multiple client option if configured
      if ( allowMultiClient ) {
      	 addMultiClientOption( newToken );
      } else {
         addSingleClientOption( );
      }
  });

  function newContact( gid ) {
  
      if ( allowMultiClient ) { 
      	 existingTokens = '';
      	 var cid = cj('#contact').val();
      	 var cids = new Array(); 
      	 cids = cid.split(',');
      	 var i = 0;
      	 cj('li.token-input-token-facebook').each(function(){
		var displayName = cj(this).children('p').text();
	 	existingTokens += '{"name":"'+displayName+'","id":"'+cids[i]+'"},';
	  	i++;
      	 });
      }

      var dataURL = {/literal}"{crmURL p='civicrm/profile/create' q='reset=1&snippet=5&context=dialog' h=0 }"{literal};
      dataURL = dataURL + '&gid=' + gid;
      cj.ajax({
         url: dataURL,
         success: function( content ) {
             cj("#contact-dialog").show( ).html( content ).dialog({
         	    	title: "Create New Contact",
             		modal: true,
             		width: 680, 
             		overlay: { 
             			opacity: 0.5, 
             			background: "black" 
             		},

                 beforeclose: function(event, ui) {
                     cj(this).dialog("destroy");
		     cj("#contact-success").fadeOut(5000);
		     cj("#profiles").val('0');
                 }
             });
         }
      });
  }
        
  function addMultiClientOption ( newToken ) {
  
      existingTokens = existingTokens + newToken;
      eval( 'existingTokens = [' + existingTokens + ']');
      eval( 'tokenClass = { tokenList: "token-input-list-facebook", token: "token-input-token-facebook", tokenDelete: "token-input-delete-token-facebook", selectedToken: "token-input-selected-token-facebook", highlightedToken: "token-input-highlighted-token-facebook", dropdown: "token-input-dropdown-facebook", dropdownItem: "token-input-dropdown-item-facebook", dropdownItem2: "token-input-dropdown-item2-facebook", selectedDropdownItem: "token-input-selected-dropdown-item-facebook", inputToken: "token-input-input-token-facebook" } ');

      var hintText = "{/literal}{ts}Type in a partial or complete name of an existing contact.{/ts}{literal}";
      var contactUrl = {/literal}"{crmURL p='civicrm/ajax/checkemail' q='id=1&noemail=1' h=0 }"{literal};
      cj('#contact').tokenInput( contactUrl, { prePopulate:existingTokens ,classes: tokenClass, hintText: hintText });
      cj('ul.token-input-list-facebook, div.token-input-dropdown-facebook' ).css( 'width', '450px');
      
  }
  
  function addSingleClientOption ( ) {
      var contactUrl = {/literal}"{crmURL p='civicrm/ajax/rest' q='className=CRM_Contact_Page_AJAX&fnName=getContactList&json=1&context=newcontact' h=0 }"{literal};

      cj('#contact').autocomplete( contactUrl, { 
          selectFirst : false, matchContains: true, minChars: 1
      }).result( function(event, data, formatted) { 
          cj("input[name=contact_select_id]").val(data[1]);
      }).focus( );

      cj("#contact").click( function( ) {
          cj("input[name=contact_select_id]").val('');
      });
                                  
      cj("#contact").bind("keypress keyup", function(e) {
          if ( e.keyCode == 13 ) {
              return false;
          }
      });  
  }
</script>
{/literal}

