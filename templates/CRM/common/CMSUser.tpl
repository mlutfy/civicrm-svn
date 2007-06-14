{if $showCMS }{*true if is_cms_user field is set *}
 {* NOTE: We are currently not supporting the Drupal registration mode where user enters their password. But logic is left here for when we figure it out. *}

 {capture assign="loginUrl"}{$config->userFrameworkBaseURL}{/capture}
 <fieldset>
    <div class="messages help">
        {ts}If you would like to create an account on this site, check the box below and enter a user name{/ts}
        {if $form.cms_pass}{ts}and a password{/ts}{/if}.
        {ts 1=$loginUrl}If you already have an account, <a href="%1">please login</a> before completing this form.{/ts}
    </div>
    <div>{$form.cms_create_account.html} {$form.cms_create_account.label}</div>
    <div id="details">
        <table class="form-layout-compressed">
        <tr>
            <td>{$form.cms_name.label}</td>
            <td>{$form.cms_name.html}<br />
                <span class="description">{ts}Your preferred username; punctuation is not allowed except for periods, hyphens, and underscores.{/ts}</span>
            </td>
        </tr>
    
        {if $form.cms_pass}
            <tr><td>{$form.cms_pass.label}</td> <td> {$form.cms_pass.html}</td></tr>        
            <tr><td>{$form.cms_confirm_pass.label}</td>
                <td>{$form.cms_confirm_pass.html}<br />
                    <span class="description">{ts}Provide a password for the new account in both fields.{/ts}
                </td>
            </tr>
        {/if}
        </table>        
    </div>
  </fieldset>
{/if}

