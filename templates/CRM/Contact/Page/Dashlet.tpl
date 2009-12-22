    <div class="status" style="padding: 1em;">
        {ts}Available dashboard elements - dashlets - are displayed in the dark gray top bar.
        Drag and drop dashlets onto the left or right columns below to add them to your dashboard.
        Changes are automatically saved. Click 'Done' to return to the normal dashboard view.{/ts}
        {help id="id-dash_configure" file="CRM/Contact/Page/Dashboard.hlp"}
    </div><br/>
    <div class="dashlets-header">{ts}Available Dashlets{/ts}</div>
    <div id="available-dashlets" class="dash-column">
        {foreach from=$availableDashlets item=row key=dashID}
    	<div class="portlet">
    		<div class="portlet-header" id="{$dashID}">{$row.label}{if $admin and !$row.is_reserved}&nbsp;<a class="close-icon delete-dashlet"></a>{/if}</div>
    	</div>
        {/foreach}
    </div>
    <br/>
    <div class="clear"></div>
    <div id="dashlets-header-col-0" class="dashlets-header">{ts}Left Column{/ts}</div>
    <div id="dashlets-header-col-1" class="dashlets-header">{ts}Right Column{/ts}</div>
    <div id="existing-dashlets-col-0" class="dash-column">
        {foreach from=$contactDashlets.0 item=row key=dashID}
    	<div class="portlet">
    		<div class="portlet-header" id="{$dashID}">{$row.label}{if $admin and !$row.is_reserved}&nbsp;<a class="close-icon delete-dashlet"></a>{/if}</div>
    	</div>
        {/foreach}
    </div>
    
    <div id="existing-dashlets-col-1" class="dash-column">
        {foreach from=$contactDashlets.1 item=row key=dashID}
    	<div class="portlet">
    		<div class="portlet-header" id="{$dashID}">{$row.label}{if $admin and !$row.is_reserved}&nbsp;<a class="close-icon delete-dashlet"></a>{/if}</div>
    	</div>
        {/foreach}
    </div>

    <div class="clear"></div>

{literal}
<script type="text/javascript">
	cj(function() {
	    var currentReSortEvent;
		cj(".dash-column").sortable({
			connectWith: '.dash-column',
			update: saveSorting
		});

		cj(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
			.find(".portlet-header")
				.addClass("ui-widget-header ui-corner-all")
				.end()
			.find(".portlet-content");

		cj(".dash-column").disableSelection();
		
		function saveSorting(e, ui) {
            // this is to prevent double post call
		    if (!currentReSortEvent || e.originalEvent != currentReSortEvent) {
                currentReSortEvent = e.originalEvent;
                
                // Build a list of params to post to the server.
                var params = {};

                // post each columns
                dashletColumns = Array();
            
                // build post params
                cj('div[id^=existing-dashlets-col-]').each( function( i ) {
                    cj(this).find('.portlet-header').each( function( j ) {
                        var elementID = this.id;
                        var idState = elementID.split('-');
                        params['columns[' + i + '][' + idState[0] + ']'] = idState[1];
                    });
                }); 
            
                // post to server
                var postUrl = {/literal}"{crmURL p='civicrm/ajax/dashboard' h=0 }"{literal};
                params['op'] = 'save_columns';
                cj.post( postUrl, params, function(response, status) {
                    // TO DO show done / disable escape action
                });
            }
        }
        
        cj('.delete-dashlet').click( function( ) {
            var message = {/literal}{ts}"Do you want to permanently delete this dashlet?"{/ts}{literal};
            if ( confirm( message) ) {
                var dashletID = cj(this).parent().attr('id');
                var idState = dashletID.split('-')
                
                // Build a list of params to post to the server.
                var params = {};
                
                params['dashlet_id'] = idState[0];

                // delete dashlet
                var postUrl = {/literal}"{crmURL p='civicrm/ajax/dashboard' h=0 }"{literal};
                params['op'] = 'delete_dashlet';
                cj.post( postUrl, params, function(response, status) {
                    // delete dom object
                    cj('#' + dashletID ).parent().remove();
                });
            }
        });
	});
</script>
{/literal}