-- CiviReport Component
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviReport', 'CRM_Report' );

INSERT INTO civicrm_acl
    (name, deny, entity_table, entity_id, operation, object_table, object_id, acl_table, acl_id, is_active) 
VALUES 
    ('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviReport',      NULL, NULL, NULL, 1),
    ('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access Report Criteria', NULL, NULL, NULL, 1);

-- Report Templates
{if $multilingual}
    INSERT INTO civicrm_option_group
        ( name,                 {foreach from=$locales item=locale}description_{$locale},   {/foreach} is_reserved, is_active)
    VALUES
        ( 'report_template',    {foreach from=$locales item=locale}'Report Template',       {/foreach} 0, 1 );
{else}
    INSERT INTO civicrm_option_group
        (name, description, is_reserved, is_active )
    VALUES
        ('report_template', 'Report Template', 0, 1 );
{/if}

SELECT @contributeCompId := max(id) FROM civicrm_component where name = 'CiviContribute';
SELECT @eventCompId      := max(id) FROM civicrm_component where name = 'CiviEvent';
SELECT @memberCompId     := max(id) FROM civicrm_component where name = 'CiviMember';
SELECT @pledgeCompId     := max(id) FROM civicrm_component where name = 'CiviPledge';

SELECT @option_group_id_report         := max(id) from civicrm_option_group where name = 'report_template';

{if $multilingual}
    INSERT INTO civicrm_option_value
	(option_group_id, {foreach from=$locales item=locale}label_{$locale}, description_{$locale}, {/foreach} value, name, weight, is_active, component_id )
    VALUES
        (@option_group_id_report , {foreach from=$locales item=locale}'Constituent Report (Summary)',           'Provides a list of address and telephone information for constituent records in your system.',                                                                                                                           {/foreach}   'contact/summary',                'CRM_Report_Form_Contact_Summary',                 1,   1, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Constituent Report (Detail)',            'Provides contact-related information on contributions, memberships, events and activities.',                                                                                                                             {/foreach}   'contact/detail',                 'CRM_Report_Form_Contact_Detail',                  2,   1, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Activity Report',                        'Provides a list of constituent activity including activity statistics for one/all contacts during a given date range(required)',                                                                                         {/foreach}   'activity',                       'CRM_Report_Form_Activity',                        3,   0, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Walk / Phone List Report',               'Provides a detailed report for your walk/phonelist for targetted contacts',                                                                                                                                              {/foreach}   'walklist',                       'CRM_Report_Form_Walklist',                        4,   0, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Current Employer Report',                'Provides detail list of employer employee relationships along with employment details Ex Join Date',                                                                                                                     {/foreach}   'contact/currentEmployer',        'CRM_Report_Form_Contact_CurrentEmployer',         5,   0, NULL ),        
        (@option_group_id_report , {foreach from=$locales item=locale}'Donor Report (Summary)',                 'Shows contribution statistics by month / week / year .. country / state .. type.',                                                                                                                                       {/foreach}   'contribute/summary',             'CRM_Report_Form_Contribute_Summary',              6,   1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donor Report (Detail)',                  'Lists detailed contribution(s) for one / all contacts. Contribution summary report points to this report for specific details.',                                                                                         {/foreach}   'contribute/detail',              'CRM_Report_Form_Contribute_Detail',               7,   1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Repeat)',       'Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.',                                                                           {/foreach}   'contribute/repeat',              'CRM_Report_Form_Contribute_Repeat',               8,   1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Organization)', 'Displays a detailed contribution report for Organization relationships with contributors, as to if contribution done was  from an employee of some organization or from that Organization itself.',                      {/foreach}   'contribute/organizationSummary', 'CRM_Report_Form_Contribute_OrganizationSummary',  9,   0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Household)',    'Provides a detailed report for Contributions made by contributors(Or Household itself) who are having a relationship with household (For ex a Contributor is Head of Household for some household or is a member of.)',  {/foreach}   'contribute/householdSummary',    'CRM_Report_Form_Contribute_HouseholdSummary',     10,  0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Top Donors Report',                      'Provides a list of the top donors during a time period you define. You can include as many donors as you want (for example, top 100 of your donors).',                                                                   {/foreach}   'contribute/topDonor',            'CRM_Report_Form_Contribute_TopDonor',             11,  0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'SYBUNT Report',                          'Some year(s) but not this year. Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.',                                    {/foreach}   'contribute/sybunt',              'CRM_Report_Form_Contribute_Sybunt',               12,  1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'LYBUNT Report',                          'Last year but not this year. Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.',                                                          {/foreach}   'contribute/lybunt',              'CRM_Report_Form_Contribute_Lybunt',               13,  1, @contributeCompId ),	
        (@option_group_id_report , {foreach from=$locales item=locale}'Soft Credit Report',                     'Soft Credit details.',                                                                                                                                                                                                   {/foreach}   'contribute/softcredit',          'CRM_Report_Form_Contribute_SoftCredit',           14,  1, @contributeCompId ),        
        (@option_group_id_report , {foreach from=$locales item=locale}'Membership Report (Summary)',            'Provides a list of members. You can included address and phone information and group the members based on membership type.',                                                                                             {/foreach}   'member/summary',                 'CRM_Report_Form_Member_Summary',                  15,  1, @memberCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Membership Report (Detail)',             'Provides a list of members along with their membership status and membership details (Join Date, Start Date, End Date).',                                                                                                {/foreach}   'member/detail',                  'CRM_Report_Form_Member_Detail',                   16,  1, @memberCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Membership Report (Lapsed)',             'Provides a list of memberships that lapsed or will lapse before the date you specify.',                                                                                                                                  {/foreach}   'member/lapse',                   'CRM_Report_Form_Member_Lapse',                    17,  1, @memberCompId ),        
        (@option_group_id_report , {foreach from=$locales item=locale}'Event Participant Report (List)',        'Provides lists of participants for an event.',                                                                                                                                                                           {/foreach}   'event/participantListing',       'CRM_Report_Form_Event_ParticipantListing',        18,  1, @eventCompId  ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Event Income Report (Summary)',          'Provides an overview of event income. You can include key information such as event ID, registration, attendance, and income generated to help you determine the success of an event.',                                  {/foreach}   'event/summary',                  'CRM_Report_Form_Event_Summary',                   19,  1, @eventCompId  ),			
        (@option_group_id_report , {foreach from=$locales item=locale}'Event Income Report (Detail)',           'Helps you to analyze the income generated by an event. The report can include details by participant type, status and payment method.',                                                                                  {/foreach}   'event/income',                   'CRM_Report_Form_Event_Income',                    20,  1, @eventCompId  ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Pledge Report',                          'Pledge Report',                                                                                                                                                                                                          {/foreach}   'pledge/summary',                 'CRM_Report_Form_Pledge_Summary',                  21,  0, @pledgeCompId ),			
        (@option_group_id_report , {foreach from=$locales item=locale}'Pledged But not Paid Report',            'Pledged but not Paid Report',                                                                                                                                                                                            {/foreach}   'pledge/pbnp',                    'CRM_Report_Form_Pledge_Pbnp',                     22,  0, @pledgeCompId );
        
{else}
    INSERT INTO civicrm_option_value
        (option_group_id, label, value, name, weight, description, is_active, component_id ) 
    VALUES
        (@option_group_id_report , 'Constituent Report (Summary)',            'contact/summary',                'CRM_Report_Form_Contact_Summary',                 1,  'Provides a list of address and telephone information for constituent records in your system.',                                      1, NULL ),    
        (@option_group_id_report , 'Constituent Report (Detail)',             'contact/detail',                 'CRM_Report_Form_Contact_Detail',                  2,  'Provides contact-related information on contributions, memberships, events and activities.',                                        1, NULL ), 
        (@option_group_id_report , 'Activity Report',                         'activity',                       'CRM_Report_Form_Activity',                        3,  'Provides a list of constituent activity including activity statistics for one/all contacts during a given date range(required)',    0, NULL ),
        (@option_group_id_report , 'Walk / Phone List Report',                'walklist',                       'CRM_Report_Form_Walklist',                        4,  'Provides a detailed report for your walk/phonelist for targetted contacts',                                                         0, NULL ),
        (@option_group_id_report , 'Current Employer Report',                 'contact/currentEmployer',        'CRM_Report_Form_Contact_CurrentEmployer',         5,  'Provides detail list of employer employee relationships along with employment details Ex Join Date',                                0, NULL ),
        (@option_group_id_report , 'Donor Report (Summary)',                  'contribute/summary',             'CRM_Report_Form_Contribute_Summary',              6,  'Shows contribution statistics by month / week / year .. country / state .. type.',                                                  1, @contributeCompId ),
        (@option_group_id_report , 'Donor Report (Detail)',                   'contribute/detail',              'CRM_Report_Form_Contribute_Detail',               7,  'Lists detailed contribution(s) for one / all contacts. Contribution summary report points to this report for specific details.',                                                                                        1, @contributeCompId ),
        (@option_group_id_report , 'Donation Summary Report (Repeat)',        'contribute/repeat',              'CRM_Report_Form_Contribute_Repeat',               8,  'Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.',                                                                          1, @contributeCompId ),        
        (@option_group_id_report , 'Donation Summary Report (Organization)',  'contribute/organizationSummary', 'CRM_Report_Form_Contribute_OrganizationSummary',  9,  'Displays a detailed contribution report for Organization relationships with contributors, as to if contribution done was  from an employee of some organization or from that Organization itself.',                     0, @contributeCompId ),
        (@option_group_id_report , 'Donation Summary Report (Household)',     'contribute/householdSummary',    'CRM_Report_Form_Contribute_HouseholdSummary',     10, 'Provides a detailed report for Contributions made by contributors(Or Household itself) who are having a relationship with household (For ex a Contributor is Head of Household for some household or is a member of.)', 0, @contributeCompId ),
        (@option_group_id_report , 'Top Donors Report',                       'contribute/topDonor',            'CRM_Report_Form_Contribute_TopDonor',             11, 'Provides a list of the top donors during a time period you define. You can include as many donors as you want (for example, top 100 of your donors).',                                                                  0, @contributeCompId ),
        (@option_group_id_report , 'SYBUNT Report',                           'contribute/sybunt',              'CRM_Report_Form_Contribute_Sybunt',               12, 'Some year(s) but not this year. Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.',                                   1, @contributeCompId ),
        (@option_group_id_report , 'LYBUNT Report',                           'contribute/lybunt',              'CRM_Report_Form_Contribute_Lybunt',               13, 'Last year but not this year. Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.',                                                         1, @contributeCompId ),	
        (@option_group_id_report , 'Soft Credit Report',                      'contribute/softcredit',          'CRM_Report_Form_Contribute_SoftCredit',           14, 'Soft Credit details.',                                                                                                                                                                                                  1, @contributeCompId ),
        (@option_group_id_report , 'Membership Report (Summary)',             'member/summary',                 'CRM_Report_Form_Member_Summary',                  15, 'Provides a list of members. You can included address and phone information and group the members based on membership type.',                                                                                            1, @memberCompId ),
        (@option_group_id_report , 'Membership Report (Detail)',              'member/detail',                  'CRM_Report_Form_Member_Detail',                   16, 'Provides a list of members along with their membership status and membership details (Join Date, Start Date, End Date).',                                                                                               1, @memberCompId ),
        (@option_group_id_report , 'Membership Report (Lapsed)',              'member/lapse',                   'CRM_Report_Form_Member_Lapse',                    17, 'Provides a list of memberships that lapsed or will lapse before the date you specify.',                                                                                                                                 1, @memberCompId ),
        (@option_group_id_report , 'Event Participant Report (List)',         'event/participantListing',       'CRM_Report_Form_Event_ParticipantListing',        18, 'Provides lists of participants for an event.',                                                                                                                                                                          1, @eventCompId  ),
        (@option_group_id_report , 'Event Income Report (Summary)',           'event/summary',                  'CRM_Report_Form_Event_Summary',                   19, 'Provides an overview of event income. You can include key information such as event ID, registration, attendance, and income generated to help you determine the success of an event.',                                 1, @eventCompId  ),			
        (@option_group_id_report , 'Event Income Report (Detail)',            'event/income',                   'CRM_Report_Form_Event_Income',                    20, 'Helps you to analyze the income generated by an event. The report can include details by participant type, status and payment method.',                                                                                 1, @eventCompId  ),
        (@option_group_id_report , 'Pledge Report',                           'pledge/summary',                 'CRM_Report_Form_Pledge_Summary',                  21, 'Pledge Report',                                                                                                                                                                                                         0, @pledgeCompId ),			
        (@option_group_id_report , 'Pledged But not Paid Report',             'pledge/pbnp',                    'CRM_Report_Form_Pledge_Pbnp',                     22, 'Pledged but not Paid Report',                                                                                                                                                                                           0, @pledgeCompId );
{/if}

-- civicrm_report_instance
CREATE TABLE civicrm_report_instance (
    id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Report Instance ID',
    title varchar(255)    COMMENT 'Report Instance Title.',
    report_id varchar(64) NOT NULL   COMMENT 'FK to civicrm_option_value for the report template',
    description varchar(255)    COMMENT 'Report Instance description.',
    permission varchar(255)    COMMENT 'permission required to be able to run this instance',
    form_values text    COMMENT 'Submitted form values for this report',
    is_active tinyint    COMMENT 'Is this entry active?',
    email_subject varchar(255)    COMMENT 'Subject of email',
    email_to text    COMMENT 'comma-separated list of email addresses to send the report to',
    email_cc text    COMMENT 'comma-separated list of email addresses to send the report to',
    header text    COMMENT 'comma-separated list of email addresses to send the report to',
    footer text    COMMENT 'comma-separated list of email addresses to send the report to',
    PRIMARY KEY ( id ) 
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- report instance 
INSERT INTO civicrm_report_instance
    ( title, report_id, description, permission, form_values )
VALUES 
    ( '{ts escape="sql"}Constituent Report (Summary){/ts}',     'contact/summary',          'Provides a list of address and telephone information for constituent records in your system.',    '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:16:{s:6:"fields";a:4:{s:12:"display_name";s:1:"1";s:14:"street_address";s:1:"1";s:4:"city";s:1:"1";s:10:"country_id";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:9:"source_op";s:3:"has";s:12:"source_value";s:0:"";s:13:"country_id_op";s:2:"in";s:16:"country_id_value";a:0:{}s:20:"state_province_id_op";s:2:"in";s:23:"state_province_id_value";a:0:{}s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:11:"description";s:92:"Provides a list of address and telephone information for constituent records in your system.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Constituent Report (Detail){/ts}',      'contact/detail',           'Provides contact-related information on contributions, memberships, events and activities.',      '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:10:{s:6:"fields";a:20:{s:12:"display_name";s:1:"1";s:10:"country_id";s:1:"1";s:15:"contribution_id";s:1:"1";s:12:"total_amount";s:1:"1";s:20:"contribution_type_id";s:1:"1";s:12:"receive_date";s:1:"1";s:22:"contribution_status_id";s:1:"1";s:13:"membership_id";s:1:"1";s:18:"membership_type_id";s:1:"1";s:9:"join_date";s:1:"1";s:10:"start_date";s:1:"1";s:8:"end_date";s:1:"1";s:9:"status_id";s:1:"1";s:14:"participant_id";s:1:"1";s:8:"event_id";s:1:"1";s:21:"participant_status_id";s:1:"1";s:7:"role_id";s:1:"1";s:13:"register_date";s:1:"1";s:9:"fee_level";s:1:"1";s:10:"fee_amount";s:1:"1";}s:15:"display_name_op";s:3:"has";s:18:"display_name_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:1:{i:0;s:1:"2";}s:11:"description";s:89:"Provides contact-related information on contributions, memberships, participants and activities.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Donor Report (Summary){/ts}',           'contribute/summary',       'Shows contribution statistics by month / week / year .. country / state .. type.',                '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:23:{s:6:"fields";a:1:{s:12:"total_amount";s:1:"1";}s:13:"country_id_op";s:2:"in";s:16:"country_id_value";a:0:{}s:20:"state_province_id_op";s:2:"in";s:23:"state_province_id_value";a:0:{}s:21:"receive_date_relative";s:1:"0";s:17:"receive_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:15:"receive_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:16:"total_amount_min";s:0:"";s:16:"total_amount_max";s:0:"";s:15:"total_amount_op";s:3:"lte";s:18:"total_amount_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:7:"options";a:1:{s:19:"include_grand_total";s:1:"1";}s:9:"group_bys";a:1:{s:12:"receive_date";s:1:"1";}s:14:"group_bys_freq";a:1:{s:12:"receive_date";s:5:"MONTH";}s:11:"description";s:89:"Shows contribution statistics by month / week / year .. country / state .. type.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:6:"charts";s:0:"";}{/literal}'),
    ( '{ts escape="sql"}Donor Report (Detail){/ts}',            'contribute/detail',        'Lists detailed contribution(s) for one / all contacts. Contribution summary report points to this report for specific details.',                 '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:24:{s:6:"fields";a:6:{s:12:"display_name";s:1:"1";s:5:"email";s:1:"1";s:5:"phone";s:1:"1";s:12:"total_amount";s:1:"1";s:12:"receive_date";s:1:"1";s:10:"country_id";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:21:"receive_date_relative";s:1:"0";s:17:"receive_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:15:"receive_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:16:"total_amount_min";s:0:"";s:16:"total_amount_max";s:0:"";s:15:"total_amount_op";s:3:"lte";s:18:"total_amount_value";s:0:"";s:13:"country_id_op";s:2:"in";s:16:"country_id_value";a:0:{}s:20:"state_province_id_op";s:2:"in";s:23:"state_province_id_value";a:0:{}s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:13:"ordinality_op";s:2:"in";s:16:"ordinality_value";a:0:{}s:7:"options";a:1:{s:18:"include_statistics";s:1:"1";}s:11:"description";s:128:"Lists detailed contribution(s) for one / all contacts. Contribution summary report points to this report for specific details.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Donation Summary Report (Repeat){/ts}', 'contribute/repeat',        'Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.',   '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:24:{s:6:"fields";a:3:{s:12:"display_name";s:1:"1";s:13:"total_amount1";s:1:"1";s:13:"total_amount2";s:1:"1";}s:22:"receive_date1_relative";s:13:"previous.year";s:18:"receive_date1_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:16:"receive_date1_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:22:"receive_date2_relative";s:9:"this.year";s:18:"receive_date2_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:16:"receive_date2_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:17:"total_amount1_min";s:0:"";s:17:"total_amount1_max";s:0:"";s:16:"total_amount1_op";s:3:"lte";s:19:"total_amount1_value";s:0:"";s:17:"total_amount2_min";s:0:"";s:17:"total_amount2_max";s:0:"";s:16:"total_amount2_op";s:3:"lte";s:19:"total_amount2_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:9:"group_bys";a:1:{s:2:"id";s:1:"1";}s:11:"description";s:140:"Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:6:"groups";s:0:"";}{/literal}' ),
    ( '{ts escape="sql"}SYBUNT Report{/ts}',                    'contribute/sybunt',        'Some year(s) but not this year. Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.',  '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:12:{s:6:"fields";a:4:{s:12:"display_name";s:1:"1";s:5:"email";s:1:"1";s:12:"total_amount";s:1:"1";s:12:"receive_date";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:6:"yid_op";s:2:"eq";s:9:"yid_value";s:4:"2009";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:11:"description";s:187:"Some year(s) but not this year. Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}LYBUNT Report{/ts}',                    'contribute/lybunt',        'Last year but not this year. Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.',                        '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:12:{s:6:"fields";a:3:{s:12:"display_name";s:1:"1";s:5:"email";s:1:"1";s:5:"phone";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:6:"yid_op";s:2:"eq";s:9:"yid_value";s:4:"2009";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:11:"description";s:167:"Last year but not this year. Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Soft Credit Report{/ts}',               'contribute/softcredit',    'Soft Credit details.',                                                                                                       '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:18:{s:6:"fields";a:5:{s:21:"display_name_creditor";s:1:"1";s:24:"display_name_constituent";s:1:"1";s:14:"email_creditor";s:1:"1";s:14:"phone_creditor";s:1:"1";s:12:"total_amount";s:1:"1";}s:5:"id_op";s:2:"in";s:8:"id_value";a:0:{}s:21:"receive_date_relative";s:1:"0";s:17:"receive_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:15:"receive_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:16:"total_amount_min";s:0:"";s:16:"total_amount_max";s:0:"";s:15:"total_amount_op";s:3:"lte";s:18:"total_amount_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:7:"options";a:1:{s:19:"include_grand_total";s:1:"1";}s:11:"description";s:96:"Soft Credit details.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Membership Report (Summary){/ts}',      'member/summary',           'Provides a list of members. You can included address and phone information and group the members based on membership type.', '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:15:{s:6:"fields";a:2:{s:18:"membership_type_id";s:1:"1";s:12:"total_amount";s:1:"1";}s:18:"join_date_relative";s:1:"0";s:14:"join_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:12:"join_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:21:"membership_type_id_op";s:2:"in";s:24:"membership_type_id_value";a:0:{}s:9:"group_bys";a:2:{s:9:"join_date";s:1:"1";s:18:"membership_type_id";s:1:"1";}s:14:"group_bys_freq";a:1:{s:9:"join_date";s:5:"MONTH";}s:11:"description";s:121:"Provides a list of members. You can included address and phone information and group members based on membership type.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:6:"charts";s:0:"";s:7:"options";N;}{/literal}'),
    ( '{ts escape="sql"}Membership Report (Detail){/ts}',       'member/detail',            'Provides a list of members along with their membership status and membership details (Join Date, Start Date, End Date).',    '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:18:{s:6:"fields";a:4:{s:12:"display_name";s:1:"1";s:18:"membership_type_id";s:1:"1";s:10:"start_date";s:1:"1";s:8:"end_date";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:6:"id_min";s:0:"";s:6:"id_max";s:0:"";s:5:"id_op";s:3:"lte";s:8:"id_value";s:0:"";s:12:"join_date_op";s:3:"has";s:15:"join_date_value";s:0:"";s:6:"sid_op";s:3:"has";s:9:"sid_value";s:0:"";s:6:"gid_op";s:3:"has";s:9:"gid_value";s:0:"";s:11:"description";s:118:"Provides a list of members along with their membership status and details.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Membership Report (Lapsed){/ts}',       'member/lapse',             'Provides a list of memberships that lapsed or will lapse before the date you specify.',                                      '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:12:{s:6:"fields";a:5:{s:12:"display_name";s:1:"1";s:18:"membership_type_id";s:1:"1";s:8:"end_date";s:1:"1";s:4:"name";s:1:"1";s:10:"country_id";s:1:"1";}s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:17:"end_date_relative";s:1:"0";s:13:"end_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:11:"end_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:11:"description";s:85:"Provides a list of memberships that lapsed or will lapse before the date you specify.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:6:"groups";s:0:"";}{/literal}' ),
    ( '{ts escape="sql"}Event Participant Report (List){/ts}',  'event/participantListing', 'Provides lists of participants for an event.',                                                                               '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:23:{s:6:"fields";a:4:{s:12:"display_name";s:1:"1";s:8:"event_id";s:1:"1";s:9:"status_id";s:1:"1";s:7:"role_id";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:8:"email_op";s:3:"has";s:11:"email_value";s:0:"";s:12:"event_id_min";s:0:"";s:12:"event_id_max";s:0:"";s:11:"event_id_op";s:3:"lte";s:14:"event_id_value";s:0:"";s:6:"sid_op";s:2:"in";s:9:"sid_value";a:0:{}s:6:"rid_op";s:2:"in";s:9:"rid_value";a:0:{}s:22:"register_date_relative";s:1:"0";s:18:"register_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:16:"register_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:6:"eid_op";s:2:"in";s:9:"eid_value";a:0:{}s:11:"description";s:55:"Lists registrants for an event.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";}{/literal}'),
    ( '{ts escape="sql"}Event Income Report (Summary){/ts}',    'event/summary',            'Provides an overview of event income. You can include key information such as event ID, registration, attendance, and income generated to help you determine the success of an event.', '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:18:{s:6:"fields";a:2:{s:5:"title";s:1:"1";s:13:"event_type_id";s:1:"1";}s:6:"eid_op";s:2:"in";s:9:"eid_value";a:3:{i:0;i:1;i:1;i:3;i:2;i:2;}s:6:"tid_op";s:2:"in";s:9:"tid_value";a:0:{}s:19:"start_date_relative";s:1:"0";s:15:"start_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:13:"start_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:17:"end_date_relative";s:1:"0";s:13:"end_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:11:"end_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:11:"description";s:180:"Provides an overview of event income. You can include key information such as event ID, registration, attendance and income generated to help you determine the success of an event.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:6:"groups";s:0:"";s:6:"charts";s:0:"";}{/literal}'),
    ( '{ts escape="sql"}Event Income Report (Detail){/ts}',     'event/income',             'Helps you to analyze the income generated by an event. The report can include details by participant type, status and payment method.', '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:8:{s:5:"id_op";s:2:"in";s:8:"id_value";a:3:{i:0;i:1;i:1;i:3;i:2;i:2;}s:11:"description";s:171:"Helps you analyze the income generated by an event. The report can include details by participant type, participant status and payment method.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:6:"groups";s:0:"";}{/literal}'),
    ( '{ts escape="sql"}Attendee List{/ts}',                    'event/participantListing', 'Provides lists of event attendees.',                                                                                                    '{literal}a:2:{i:0;a:1:{i:0;s:18:"administer CiviCRM";}i:1;s:3:"and";}{/literal}', '{literal}a:24:{s:6:"fields";a:4:{s:12:"display_name";s:1:"1";s:14:"participant_id";s:1:"1";s:9:"status_id";s:1:"1";s:7:"role_id";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:8:"email_op";s:3:"has";s:11:"email_value";s:0:"";s:11:"event_id_op";s:2:"in";s:14:"event_id_value";a:1:{i:0;s:1:"1";}s:6:"sid_op";s:2:"in";s:9:"sid_value";a:0:{}s:6:"rid_op";s:2:"in";s:9:"rid_value";a:0:{}s:34:"participant_register_date_relative";s:1:"0";s:30:"participant_register_date_from";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:28:"participant_register_date_to";a:3:{s:1:"M";s:0:"";s:1:"d";s:0:"";s:1:"Y";s:0:"";}s:6:"eid_op";s:2:"in";s:9:"eid_value";a:0:{}s:16:"blank_column_end";s:1:"1";s:11:"description";s:44:"Provides lists of participants for an event.";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:18:"administer CiviCRM";s:6:"groups";s:0:"";s:7:"options";N;}{/literal}');
