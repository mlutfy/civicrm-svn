<script type="text/javascript" src="{$config->resourceBase}packages/jquery/jquery.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/jquery-ui.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/jquery/themes/smoothness/jquery-ui.css);</style>

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/flexigrid.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/jquery/css/flexigrid.css);</style>

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.autocomplete.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/jquery/css/jquery.autocomplete.css);</style>

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/tree_component.min.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/css.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/jquery/css/tree_component.css);</style>

<!--script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.clickmenu.pack.js"></script-->
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.menu.pack.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.dimensions.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/jquery/css/menu.css);</style>
<!--style type="text/css">@import url({$config->resourceBase}packages/jquery/css/clickmenu.css);</style-->

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.chainedSelects.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.treeview.min.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.bgiframe.pack.js"></script>

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.contextMenu.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.tableHeader.js"></script>

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.tablednd.js"></script>
{if $context eq 'search' || $context eq 'smog'}
    {*allow select/unselect checkboxes functionality only for search*}
    <script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/checkboxselect.js"></script>
{/if}
{if $defaultWysiwygEditor eq 1}
    <script type="text/javascript" src="{$config->resourceBase}packages/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
{/if}
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.textarearesizer.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.progressbar.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.form.js"></script>

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.tokeninput.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/jquery/css/token-input-facebook.css);></style>

<script type="text/javascript">var cj = jQuery.noConflict(); $ = cj;</script>