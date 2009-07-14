{* This form is for Contact Add/Edit interface *}
{if $addBlock}
{include file="CRM/Contact/Form/Edit/$blockName.tpl"}
{else}
<div class="crm-submit-buttons">
   {$form.buttons.html}
</div>
<span style="float:right;"><a href="#expand" id="expand">{ts}Expand all tabs{/ts}</a></span>
<br/>
<div class="accordion ui-accordion ui-widget ui-helper-reset">
    <h3 class="head"> 
        <span class="ui-icon ui-icon-triangle-1-e" id='contact'></span><a href="#">{ts}Contact Details{/ts}</a>
    </h3>
    <div id="contact-details" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
        {include file="CRM/Contact/Form/Edit/$contactType.tpl"}
        <br/>
        <table class="form-layout-compressed">
            {foreach from=$blocks item="label" key="block"}
               {include file="CRM/Contact/Form/Edit/$block.tpl"}
            {/foreach}
		</table>
		<table class="form-layout-compressed">
            <tr class="last-row">
            {if $form.home_URL}
              <td>{$form.home_URL.label}<br />
                  {$form.home_URL.html}
              </td>
            {/if}
              <td>{$form.contact_source.label}<br />
                  {$form.contact_source.html}
              </td>
              <td>{$form.external_identifier.label}<br />
                  {$form.external_identifier.html}
              </td>
              <td><label for="internal_identifier">Internal Id</label><br />
               {$entityID}</td>
            </tr>            
        </table>

        {*  add dupe buttons *}
        {$form._qf_Contact_refresh_dedupe.html}
        {if $isDuplicate}&nbsp;&nbsp;{$form._qf_Contact_next_duplicate.html}{/if}
        <div class="spacer"></div>

    </div>
    
    {foreach from = $editOptions item = "title" key="name"}
        {include file="CRM/Contact/Form/Edit/$name.tpl"}
    {/foreach}
    
</div>
<div class="crm-submit-buttons">
   {$form.buttons.html}
</div>

{literal}
<script type="text/javascript" >
cj(function( ) {
    cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all");

    cj('.accordion .head').hover( function( ) { 
        cj(this).addClass( "ui-state-hover");
    }, function() { 
        cj(this).removeClass( "ui-state-hover");
    }).bind('click', function( ) { 
        var checkClass = cj(this).find('span').attr( 'class' );
        var len        = checkClass.length;
        if ( checkClass.substring( len - 1, len ) == 's' ) {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-e');
        } else {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
        }
        cj(this).next( ).toggle('blind'); return false; 
    }).next( ).hide( );
    
    cj('span#contact').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
    cj("#contact-details").show( );
});

cj( function( ) {
{/literal}
{if $generateAjaxRequest}
    {foreach from=$ajaxRequestBlocks key="blockName" item="instances"}
      //reset count to 1 since each time counter get increamented.
      cj( "#hidden_" + "{$blockName}" + "_Instances" ).val( 1 );
        {foreach from=$instances key="instance" item="active"}
             buildAdditionalBlocks( '{$blockName}', '{$contactType}' );
        {/foreach}  	
    {/foreach}
{/if}
{literal}
});

function buildAdditionalBlocks( blockName, contactType ) {

  var previousBlockCount = cj( "#hidden_" + blockName + "_Instances" ).val( ).substr(-1);
  var currentBlockCount  = parseInt( previousBlockCount ) + 1; 

  var dataUrl = {/literal}"{crmURL p='civicrm/contact/add' h=0 q='snippet=4&ct='}"{literal} + contactType + '&block=' + blockName + '&count=' + currentBlockCount;
	
  {/literal}
  {if $action eq 2}
  dataUrl += "&action=update&cid={$entityID}";
  {/if}
  dataUrl += "&qfKey={$qfKey}";
  {literal}
   
  blockId = (cj('#' + blockName + '_Block_'+ previousBlockCount ).html()) ? previousBlockCount : 1;  
  var fname = '#' + blockName + '_Block_'+ blockId;

  cj('#addMore' + blockName ).hide();
  cj.ajax({ 
            url     : dataUrl,   
            async   : false,
            success : function(html){
                         var html = html.split('<!-Add->',2);
                         cj(fname).after(html[1]);
                      }
         });
  cj( "#hidden_" + blockName + "_Count" ).val( currentBlockCount );

  //build the hidden block instance string used in post.
  var prevousBlockCntStr = cj( "#hidden_" + blockName + "_Instances" ).val( );
  var currentBlockCntStr = prevousBlockCntStr + ',' + currentBlockCount;
  cj( "#hidden_" + blockName + "_Instances" ).val( currentBlockCntStr );

  if ( blockName == 'Address' ) cj("#addressBlock").show( );

}

cj('a#expand').click( function( ){
     if( cj(this).attr('href') == '#expand') {   
          var message = {/literal}{ts}"Collapse all tabs"{/ts}{literal};
          var class   = 'ui-icon ui-icon-triangle-1-s';
          var event   = 'show';
          cj(this).attr('href', '#collapse');
     } else {
          var message = {/literal}{ts}"Expand all tabs"{/ts}{literal};
          var class   = 'ui-icon ui-icon-triangle-1-e';
          var event   = 'hide';
          cj(this).attr('href', '#expand');
     }
          cj(this).html(message);
          cj('div.accordion div.ui-accordion-content').each(function() {
             cj(this).parent().find('h3 span').removeClass( ).addClass(class);
                 eval( " var showHide = cj(this)." + event + "();" );
          }); 
});

//current employer default setting
var employerId = "{/literal}{$currentEmployer}{literal}";
if( employerId ) {
   var dataUrl = "{/literal}{crmURL p='civicrm/ajax/search' h=0 q="org=1&id=" }{literal}" + employerId ;
    cj.ajax({ 
        url     : dataUrl,   
        async   : false,
        success : function(html){
                    //fixme for showing address in div
                    htmlText = html.split( '|' , 2);
                    cj('input#current_employer').val(htmlText[0]);
                    cj('input#current_employer_id').val(htmlText[1]);
                  }
            }); 
}

//select single is_bulk & is_primary
function singleSelect( blockName, blockId, flagName ) {
  var instances = cj( "#hidden_" + blockName + "_Instances" ).val( ).split(',');
  var instance  = 1;
  cj(instances).each( function( ) { 
    if ( instance != blockId ) {
        cj( '#'+blockName+'_'+instance+'_'+flagName).attr( 'checked', false );
    }
    instance++;	
  });
}

function removeBlock( blockName, blockId ) {
 //update string for removing block instance from qf during post.
 var updateStr = cj( "#hidden_" + blockName + "_Instances" ).val( ).replace( ',' + blockId, '' );
 cj( "#hidden_" + blockName + "_Instances" ).val(  updateStr );
 
 //unset block from html
 cj( "#"+ blockName + "_Block_" + blockId ).remove();
}

</script>
{/literal}
{/if}