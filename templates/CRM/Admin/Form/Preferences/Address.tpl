<div class="html-adjust">{$form.buttons.html}</div>
<div class="form-item">
    <fieldset><legend>{ts}Mailing Labels{/ts}</legend>
        <table class="form-layout">
    		<tr>
    		    <td class="label">{$form.mailing_format.label}</td>
    			<td>{$form.mailing_format.html|crmReplace:class:huge12}<br />
    			<span class="description">{ts 1=&#123;contact.state_province&#125; 2=&#123;contact.state_province_name&#125;}Address format for mailing labels. Use the %1 token for state/province abbreviation or %2 for full name.{/ts}{help id='label-tokens'}</span>
    			</td>
    		</tr>
    	</table>
    </fieldset>

    <fieldset><legend>{ts}Address Display{/ts}</legend>
        <table class="form-layout">
    	    <tr>
    	        <td class="label">{$form.address_format.label}</td>
    	        <td>{$form.address_format.html|crmReplace:class:huge12}<br />
    	        <span class="description">{ts}Format for displaying addresses in the Contact Summary and Event Information screens.{/ts}<br />{ts 1=&#123;contact.state_province&#125; 2=&#123;contact.state_province_name&#125;}Use %1 for state/province abbreviation or %2 for state province name.{/ts}{help id='address-tokens'}</span>
    	        </td>
    	    </tr>
    	</table>
    </fieldset>
		
    <fieldset><legend>{ts}Address Editing{/ts}</legend>
        <table class="form-layout">
            <tr>
                <td class="label">{$form.address_options.label}
        	    <td>{$form.address_options.html}<br />
        	    <span class="description">{ts}Select the fields to be included when editing a contact or event address.{/ts}</span>
                </td>
            </tr>
        </table>
    </fieldset>

    <fieldset><legend>{ts}Address Standardization{/ts}</legend>
        <table class="form-layout">
            <tr>
                <td colspan="2">
    	            <span class="description">{ts 1=http://www.usps.com/webtools/address.htm}CiviCRM includes an optional plugin for interfacing the the United States Postal Services (USPS) Address Standardization web service. You must register to use the USPS service at <a href='%1' target='_blank'>%1</a>. If you are approved, they will provide you with a User ID and the URL for the service.{/ts}</span>
    	        </td>
            </tr>
            <tr>
            	<td class="label">{$form.address_standardization_provider.label}</td>
            	<td>{$form.address_standardization_provider.html}<br />
            	<span class="description">{ts}Address Standardization Provider. Currently, only 'USPS' is supported.{/ts}</span>
                </td>
            </tr>
            <tr>
            	<td class="label">{$form.address_standardization_userid.label}
            	<td>{$form.address_standardization_userid.html}<br />
            	<span class="description">{ts}USPS-provided User ID.{/ts}</span>
                </td>
            </tr>
            <tr>
            	<td class="label">{$form.address_standardization_url.label}
            	<td>{$form.address_standardization_url.html}<br />
            	<span class="description">{ts}USPS-provided web service URL.{/ts}</span>
            	</td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="html-adjust">{$form.buttons.html}</div>
