{literal}
<script>
if (FB != undefined) { // reprocess the XFBML tags if called in ajax (otherwise, that's done by the init)
  FB.XFBML.parse(document.getElementById('socialnetwork'));

}
</script>
{/literal}

{* 
You might have a specific page that displays more information that the form.

This is an example (taken from http://www.etownhall.eu) that assumes that there is a Drupal node 
that contains a cck field 'petitionid'. 
This node is going to be used for the social networks promotion instead of the form.

This is an example of how to fetch content from a Drupal node (that contains a cck field 'petitionid')
You will want to customise it based on your configuration.

How to install ?
Create a custom template folder and copy this file into CRM/Campaign/Page/Petition/SocialNetwork.tpl
This assumes you are on Drupal, have installed the fb module (drupal.org/project/fb), 
and in general, is very unlikely to work directly. Please consider this as an example, 
and modify to fit your specific configuration.
*}


{php}
     /**
     * Function to get Petition Drupal Node Path/Alias 
     * 
     * @param int $surveyId
     * @static
     */
    function &getPetitionDrupalNodeData( $surveyId ) {
	/*
	Other approach: using the view
	$view=views_get_view("node_petition"); //replace with the name of your view
	$view->set_arguments(array($surveyId));
	$view->build('default'); //use "default" if you want to retrieve the default display of your view, if not, the name of the specific display
	$view->execute();
	foreach($view->result as $result) { //$result 
	  //  Do something with $result here.  Each result is an object, so for example you can access the nid using $result->nid
	}
	*/
		$config =& CRM_Core_Config::singleton( );
		$surveyId = (int)$surveyId;// sql injection protection
		// if Drupal node uses cck integer field petitionid
		// there will be a 'content_field_petitionid' table in the Drupal database
		// that stores field_petitionid_value against nid (node id)
		
		$result = db_query("SELECT * FROM content_field_petitionid WHERE field_petitionid_value = " . $surveyId);
		
		global $base_url;
		$petition = array();
		$data = db_fetch_array ($result);
	
		if (!$data) {
		return null; 
		}
	
		$petition_node = node_load ($data['nid']);
		$petition_node->url = $base_url . "/" . drupal_get_path_alias("node/".$data['nid']);
		$petition_node->title = node_page_title(node_load($data['nid']));
			
		return array_merge ((array)$petition_node,(array)$data);
	}
			
	$petition_id = $this->get_template_vars('petition_id');
	$node = getPetitionDrupalNodeData($petition_id);
	$this->assign_by_ref('node', $node);
{/php}

{if $node.nid}
<!-- Social Networking -->
<h2>Help spread the word about "{$node.title}"</h2>
Please help us and let your friends, colleagues and followers know about our campaign.
<h3>Do you use Facebook or Twitter ?</h3>
<p>Like it on Facebook or tweet it on Twitter.</p>
<div class="socialnetwork">
  <fb:like href="{$node.url}"></fb:like>
  <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
  <a href="http://twitter.com/share?text=Sign this, I did: {$node.title}&url={$node.url}" class="twitter-share-button" title="tweet about this petition">Tweet</a>
</div>
<h3>Do you have a website for your organisation or yourself?</h3>
You can write a story about it, don't forget to add the link to <a href="{$node.url}">{$node.url}</a>
{/if}
