-- CiviReport Component
INSERT INTO civicrm_component (name, namespace) VALUES ('CiviReport', 'CRM_Report' );

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
        (@option_group_id_report , {foreach from=$locales item=locale}'Constituent Report (Summary)',           'Provides a list of address and telephone information for constituent records in your system.',                                                                                                                                                 {/foreach}   'contact/summary',                'CRM_Report_Form_Contact_Summary',                 1,   1, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Constituent Report (Detail)',            'Provides a Contact related information of Contribution, Membership, Participant, Activity',                                                                                                                                                    {/foreach}   'contact/detail',                 'CRM_Report_Form_Contact_Detail',                  2,   1, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Activity Report',                        'Provides a list of constituent activity including activity statistics for one/all contacts during a given date range(required)',                                                                                                               {/foreach}   'activity',                       'CRM_Report_Form_Activity',                        3,   0, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Walk / Phone List Report',               'Provides a detailed report for your walk/phonelist for targetted contacts',                                                                                                                                                                    {/foreach}   'walklist',                       'CRM_Report_Form_Walklist',                        4,   0, NULL ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Current Employer Report',                'Provides detail list of employer employee relationships along with employment details Ex Join Date',                                                                                                                                           {/foreach}   'contact/currentEmployer',        'CRM_Report_Form_Contact_CurrentEmployer',         5,   0, NULL ),        
        (@option_group_id_report , {foreach from=$locales item=locale}'Donor Report (Summary)',                 'Shows contribution / amount statistics by month / week / year .. country / state .. type.',                                                                                                                                                    {/foreach}   'contribute/summary',             'CRM_Report_Form_Contribute_Summary',              6,   1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donor Report (Detail)',                  'Lists detailed contribution(s) for one / all contacts .. Contribution summary report points to this report for specific details.',                                                                                                             {/foreach}   'contribute/detail',              'CRM_Report_Form_Contribute_Detail',               7,   1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Repeat)',       'Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.',                                                                                                 {/foreach}   'contribute/repeatSummary',       'CRM_Report_Form_Contribute_RepeatSummary',        8,   1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Detail Report (Repeat)' ,       'Allows the user to analyze repeat contributions, contributions that come from the same contact during two periods. Ex if a user wants to see contributions made by people in 2007/2008 and then changed in their contribution behaviour',      {/foreach}   'contribute/repeatDetail',        'CRM_Report_Form_Contribute_RepeatDetail',         9,   0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Count)',        'Displays Contribution Summary for one/all users including  the number of times they contributed, the total amount contributed and average',                                                                                                    {/foreach}   'contribute/summaryCount',        'CRM_Report_Form_Contribute_SummaryCount',         10,  0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Organization)', 'Displays a detailed contribution report for Organization relationships with contributors, as to if contribution done was  from an employee of some organization or from that Organization itself.',                                            {/foreach}   'contribute/organizationSummary', 'CRM_Report_Form_Contribute_OrganizationSummary',  11,  0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Donation Summary Report (Household)',    'Provides a detailed report for Contributions made by contributors(Or Household itself) who are having a relationship with household (For ex a Contributor is Head of Household for some household or is a member of.)',                        {/foreach}   'contribute/householdSummary',    'CRM_Report_Form_Contribute_HouseholdSummary',     12,  0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Top Donors Report',                      'Provides a list of the top donors during a time period you define. You can include as many donors as you want (for example, top 100 of your donors).',                                                                                         {/foreach}   'contribute/topDonor',            'CRM_Report_Form_Contribute_TopDonor',             13,  0, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'SYBUNT Report',                          '(some years but unfortunately not this) Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.',                                                  {/foreach}   'contribute/sybunt',              'CRM_Report_Form_Contribute_Sybunt',               14,  1, @contributeCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'LYBUNT Report',                          '(last year but unfortunately not this) Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.',                                                                      {/foreach}   'contribute/lybunt',              'CRM_Report_Form_Contribute_Lybunt',               15,  1, @contributeCompId ),	
        (@option_group_id_report , {foreach from=$locales item=locale}'Soft Credit Report',                     'This Report gives detailson about Soft Credits mentioned while making a contribution by a donor.',                                                                                                                                             {/foreach}   'contribute/softcredit',          'CRM_Report_Form_Contribute_SoftCredit',           16,  1, @contributeCompId ),        
        (@option_group_id_report , {foreach from=$locales item=locale}'Membership Report (Summary)',            'Provides alist of members. You can included address and phone information and group the members based on membership type.',                                                                                                                    {/foreach}   'member/summary',                 'CRM_Report_Form_Member_Summary',                  17,  1, @memberCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Membership Report (Detail)',             'Provides a list of member along with their membership Status and membership details Ex Join Date, start Date, End Date',                                                                                                                       {/foreach}   'member/detail',                  'CRM_Report_Form_Member_Detail',                   18,  1, @memberCompId ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Membership Report (Lapsed)',             'Provides a list of memberships that lapsed or will lapse before the date you specify.',                                                                                                                                                        {/foreach}   'member/lapseSummary',            'CRM_Report_Form_Member_LapseSummary',             19,  1, @memberCompId ),        
        (@option_group_id_report , {foreach from=$locales item=locale}'Event Participant Report (List)',        'Provides lists of sponsors or registrants for an event.',                                                                                                                                                                                      {/foreach}   'event/participantListing',       'CRM_Report_Form_Event_ParticipantListing',        20,  1, @eventCompId  ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Event Report (Summary)',                 'Provides an overview of event finances. You can include key information, such as event ID, registration, attendance, income generated to help you determine the success of an event.',                                                         {/foreach}   'event/eventSummary',             'CRM_Report_Form_Event_EventSummary',              21,  1, @eventCompId  ),			
        (@option_group_id_report , {foreach from=$locales item=locale}'Event Income Report (Detail)',           'Helps you to analyze the income generated by an event. The report can include the Detail Analysis for participant type, participant status, payment methods of registrants.',                                                                  {/foreach}   'event/eventIncome',              'CRM_Report_Form_Event_EventIncome',               22,  1, @eventCompId  ),
        (@option_group_id_report , {foreach from=$locales item=locale}'Pledge Report',                          'Pledge Report',                                                                                                                                                                                                                                {/foreach}   'pledge/summary',                 'CRM_Report_Form_Pledge_Summary',                  23,  0, @pledgeCompId ),			
        (@option_group_id_report , {foreach from=$locales item=locale}'Pledged But not Paid Report',            'Pledged but not Paid Report',                                                                                                                                                                                                                  {/foreach}   'pledge/pbnp',                    'CRM_Report_Form_Pledge_Pbnp',                     24,  0, @pledgeCompId );
        
{else}
    INSERT INTO civicrm_option_value
        (option_group_id, label, value, name, weight, description, is_active, component_id ) 
    VALUES
        (@option_group_id_report , 'Constituent Report (Summary)',            'contact/summary',                'CRM_Report_Form_Contact_Summary',                 1,  'Provides a list of address and telephone information for constituent records in your system.',                                      1, NULL ),    
        (@option_group_id_report , 'Constituent Report (Detail)',             'contact/detail',                 'CRM_Report_Form_Contact_Detail',                  2,  'Provides a Contact related information of Contribution, Membership, Participant, Activity',                                         1, NULL ), 
        (@option_group_id_report , 'Activity Report',                         'activity',                       'CRM_Report_Form_Activity',                        3,  'Provides a list of constituent activity including activity statistics for one/all contacts during a given date range(required)',    0, NULL ),
        (@option_group_id_report , 'Walk / Phone List Report',                'walklist',                       'CRM_Report_Form_Walklist',                        4,  'Provides a detailed report for your walk/phonelist for targetted contacts',                                                         0, NULL ),
        (@option_group_id_report , 'Current Employer Report',                 'contact/currentEmployer',        'CRM_Report_Form_Contact_CurrentEmployer',         5,  'Provides detail list of employer employee relationships along with employment details Ex Join Date',                                0, NULL ),
        (@option_group_id_report , 'Donor Report ( Summary )',                'contribute/summary',             'CRM_Report_Form_Contribute_Summary',              6,  'Shows contribution / amount statistics by month / week / year .. country / state .. type.', 1, @contributeCompId ),
        (@option_group_id_report , 'Donor Report (Detail)',                   'contribute/detail',              'CRM_Report_Form_Contribute_Detail',               7,  'Lists detailed contribution(s) for one / all contacts .. Contribution summary report points to this report for specific details.',                                                                                                          1, @contributeCompId ),
        (@option_group_id_report , 'Donation Summary Report (Repeat)',        'contribute/repeatSummary',       'CRM_Report_Form_Contribute_RepeatSummary',        8,  'Given two date ranges, shows contacts (and their contributions) who contributed in both the date ranges with percentage increase / decrease.',                                                                                              1, @contributeCompId ),
        (@option_group_id_report , 'Donation Detail Report (Repeat)' ,        'contribute/repeatDetail',        'CRM_Report_Form_Contribute_RepeatDetail',         9,  'Allows the user to analyze repeat contributions, contributions that come from the same contact during two periods. Ex if a user wants to see contributions made by people in 2007/2008 and then changed in their contribution behaviour',   0, @contributeCompId ),
        (@option_group_id_report , 'Donation Summary Report (Count)',         'contribute/summaryCount',        'CRM_Report_Form_Contribute_SummaryCount',         10, 'Displays Contribution Summary for one/all users including  the number of times they contributed, the total amount contributed and average',                                                                                                 0, @contributeCompId ),
        (@option_group_id_report , 'Donation Summary Report (Organization)',  'contribute/organizationSummary', 'CRM_Report_Form_Contribute_OrganizationSummary',  11, 'Displays a detailed contribution report for Organization relationships with contributors, as to if contribution done was  from an employee of some organization or from that Organization itself.',                                         0, @contributeCompId ),
        (@option_group_id_report , 'Donation Summary Report (Household)',     'contribute/householdSummary',    'CRM_Report_Form_Contribute_HouseholdSummary',     12, 'Provides a detailed report for Contributions made by contributors(Or Household itself) who are having a relationship with household (For ex a Contributor is Head of Household for some household or is a member of.)',                     0, @contributeCompId ),
        (@option_group_id_report , 'Top Donors Report',                       'contribute/topDonor',            'CRM_Report_Form_Contribute_TopDonor',             13, 'Provides a list of the top donors during a time period you define. You can include as many donors as you want (for example, top 100 of your donors).',                                                                                      0, @contributeCompId ),
        (@option_group_id_report , 'SYBUNT Report',                           'contribute/sybunt',              'CRM_Report_Form_Contribute_Sybunt',               14, '(some years but unfortunately not this) Provides a list of constituents who donated at some time in the history of your organization but did not donate during the time period you specify.',                                               1, @contributeCompId ),
        (@option_group_id_report , 'LYBUNT Report',                           'contribute/lybunt',              'CRM_Report_Form_Contribute_Lybunt',               15, '(last year but unfortunately not this) Provides a list of constituents who donated last year but did not donate during the time period you specify as the current year.',                                                                   1, @contributeCompId ),	
        (@option_group_id_report , 'Soft Credit Report',                      'contribute/softcredit',          'CRM_Report_Form_Contribute_SoftCredit',           16, 'This Report gives detailson about Soft Credits mentioned while making a contribution by a donor.',                                                                                                                                          1, @contributeCompId ),
        (@option_group_id_report , 'Membership Report (Summary)',             'member/summary',                 'CRM_Report_Form_Member_Summary',                  17, 'Provides alist of members. You can included address and phone information and group the members based on membership type.',                                                                                                                 1, @memberCompId ),
        (@option_group_id_report , 'Membership Report (Detail)',              'member/detail',                  'CRM_Report_Form_Member_Detail',                   18, 'Provides a list of member along with their membership Status and membership details Ex Join Date, start Date, End Date',                                                                                                                    1, @memberCompId ),
        (@option_group_id_report , 'Membership Report (Lapsed)',              'member/lapseSummary',            'CRM_Report_Form_Member_LapseSummary',             19, 'Provides a list of memberships that lapsed or will lapse before the date you specify.',                                                                                                                                                     1, @memberCompId ),
        (@option_group_id_report , 'Event Participant Report (List)',         'event/participantListing',       'CRM_Report_Form_Event_ParticipantListing',        20, 'Provides lists of sponsors or registrants for an event.',                                                                                                                                                                                   1, @eventCompId  ),
        (@option_group_id_report , 'Event Report (Summary)',                  'event/eventSummary',             'CRM_Report_Form_Event_EventSummary',              21, 'Provides an overview of event finances. You can include key information, such as event ID, registration, attendance, income generated to help you determine the success of an event.',                                                      1, @eventCompId  ),			
        (@option_group_id_report , 'Event Income Report (Detail)',            'event/eventIncome',              'CRM_Report_Form_Event_EventIncome',               22, 'Helps you to analyze the income generated by an event. The report can include the Detail Analysis for participant type, participant status, payment methods of registrants.',                                                               1, @eventCompId  ),
        (@option_group_id_report , 'Pledge Report',                           'pledge/summary',                 'CRM_Report_Form_Pledge_Summary',                  23, 'Pledge Report',                                                                                                                                                                                                                             0, @pledgeCompId ),			
        (@option_group_id_report , 'Pledged But not Paid Report',             'pledge/pbnp',                    'CRM_Report_Form_Pledge_Pbnp',                     24, 'Pledged but not Paid Report',                                                                                                                                                                                                               0, @pledgeCompId );
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
