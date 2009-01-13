{* Approved message *}
{if $returnContent eq 'Approved'}
============================
Your Personal Campaign Page
============================

Your personal campaign page has been approved and is now live. 

Promote your fundraising page:
&raquo; {$pcpTellFriendURL}

View and update your page:
&raquo; {$pcpInfoURL}

Questions? Send email to:
&raquo; {$pcpNotifyEmailAddress} 

{* Rejected message *}
{else if $returnContent eq 'Rejected'}
============================
Your Personal Campaign Page
============================

Your personal campaign page has been reviewed. There were some issues with the content
which prevented us from approving the page. We are sorry for any inconvenience.

Please contact our site administrator for more information: 
&raquo; {$pcpNotifyEmailAddress} 

{/if}