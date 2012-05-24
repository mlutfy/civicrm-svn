-- +--------------------------------------------------------------------+
-- | CiviCRM version 4.1                                                |
-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC (c) 2004-2011                                |
-- +--------------------------------------------------------------------+
-- | This file is a part of CiviCRM.                                    |
-- |                                                                    |
-- | CiviCRM is free software; you can copy, modify, and distribute it  |
-- | under the terms of the GNU Affero General Public License           |
-- | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
-- |                                                                    |
-- | CiviCRM is distributed in the hope that it will be useful, but     |
-- | WITHOUT ANY WARRANTY; without even the implied warranty of         |
-- | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
-- | See the GNU Affero General Public License for more details.        |
-- |                                                                    |
-- | You should have received a copy of the GNU Affero General Public   |
-- | License and the CiviCRM Licensing Exception along                  |
-- | with this program; if not, contact CiviCRM LLC                     |
-- | at info[AT]civicrm[DOT]org. If you have questions about the        |
-- | GNU Affero General Public License or the licensing of CiviCRM,     |
-- | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
-- +--------------------------------------------------------------------+

SELECT @domainID := id FROM civicrm_domain where name = 'Default Domain Name';

-- Sample Extended Property Group and Fields

INSERT INTO `civicrm_price_set` (`name`,`title`,`is_active`,`extends`,`is_quick_config`)
VALUES ('help_support_civicrm_amount', 'Help Support CiviCRM!', 1, 2, 1);

SELECT @priceSetId := max(id) FROM `civicrm_price_set` WHERE `is_quick_config` = 1 and name = 'help_support_civicrm_amount';

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`, `is_enter_qty`, `weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`, `visibility_id`)
VALUES ( @priceSetId, 'contribution_amount', 'Contribution Amount', 'Radio', 0, 2, 1, 1, 1, 0, 1),
( @priceSetId, 'other_amount', 'Other Amount', 'Text', 0, 3, 0, 1, 1, 0, 1);

INSERT INTO `civicrm_price_set_entity` (`entity_table`,`entity_id`,`price_set_id`) 
         VALUES ('civicrm_contribution_page', 1, @priceSetId);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'contribution_amount';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `is_active`, `is_default`) 
VALUES
    (@priceFieldID,'friend','Friend','1.00',1,1,0),
    (@priceFieldID,'supporter', 'Supporter','5.00',2,1,0),
    (@priceFieldID,'booster','Booster','10.00',3,1,1),
    (@priceFieldID,'sustainer','Sustainer','50.00',4,1,0);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'other_amount';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `is_active`, `is_default`) 
VALUES
     (@priceFieldID, 'other_amount', 'Other Amount', 1, 3, 1, 0);
    
INSERT INTO civicrm_contribution_page
  (title,intro_text,contribution_type_id,is_monetary,is_allow_other_amount,default_amount_id,min_amount,max_amount,goal_amount,thankyou_title,thankyou_text,thankyou_footer,receipt_from_name,receipt_from_email,cc_receipt,bcc_receipt,receipt_text,is_active,footer_text,amount_block_is_active,honor_block_is_active,honor_block_title,honor_block_text,currency,is_email_receipt)
VALUES
  ('Help Support CiviCRM!','Do you love CiviCRM? Do you use CiviCRM? Then please support CiviCRM and Contribute NOW by trying out our new online contribution features!',1,1,1,137,'10.00','10000.00','100000.00','Thanks for Your Support!','<p>Thank you for your support. Your contribution will help us build even better tools.</p><p>Please tell your friends and colleagues about CiviCRM!</p>','<p><a href=http://civicrm.org>Back to CiviCRM Home Page</a></p>','CiviCRM Fundraising Dept.','donationFake@civicrm.org','receipt@example.com','bcc@example.com','Your donation is tax deductible under IRS 501(c)(3) regulation. Our tax identification number is: 93-123-4567',1, NULL, 1,NULL, NULL, NULL, 'USD', 1),
  ('Member Signup and Renewal', 'Members are the life-blood of our organization. If you''re not already a member - please consider signing up today. You can select the membership level the fits your budget and needs below.', 2, 1, NULL, NULL, NULL, NULL, NULL, 'Thanks for Your Support!', 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.', NULL, 'Membership Department', 'memberships@civicrm.org', NULL, NULL, 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.\r\n\r\nKeep this receipt for your records.', 1, NULL, 0, NULL, NULL,NULL, 'USD', 1),
  ('Pledge for CiviCRM!','Do you love CiviCRM? Do you use CiviCRM? Then please support CiviCRM and Pledge NOW by trying out our online contribution features!',1,1,1,NULL,'10.00','10000.00','100000.00','Thanks for Your Support!','<p>Thank you for your support. Your contribution will help us build even better tools like Pledge.</p><p>Please tell your friends and colleagues about CiviPledge!</p>','<p><a href=http://civicrm.org>Back to CiviCRM Home Page</a></p>','CiviCRM Fundraising Dept.','donationFake@civicrm.org','receipt@example.com','bcc@example.com','Your donation is tax deductible under IRS 501(c)(3) regulation. Our tax identification number is: 93-123-4567',1, NULL, 1,NULL, NULL, NULL, 'USD', 1);

INSERT INTO `civicrm_tell_friend`
    (`entity_table`, `entity_id`, `title`, `intro`, `suggested_message`, `general_link`, `thankyou_title`, `thankyou_text`, `is_active`)
VALUES
    ('civicrm_contribution_page', 1, 'Tell A Friend', '<p>Help us spread the word and leverage the power of your contribution by telling your friends. Use the space below to personalize your email message - let your friends know why you support us. Then fill in the name(s) and email address(es) and click ''Send Your Message''.</p>', 'Thought you might be interested in learning about and helping this organization. I think they do important work.', NULL, 'Thanks for Spreading the Word', '<p><strong>Thanks for telling your friends about us and supporting our efforts. Together we can make a difference.</strong></p>', 1),
    ('civicrm_event', 1, 'Tell A Friend', '<p>Help us spread the word about this event. Use the space below to personalize your email message - let your friends know why you''re attending. Then fill in the name(s) and email address(es) and click ''Send Your Message''.</p>', 'Thought you might be interested in checking out this event. I''m planning on attending.', NULL, 'Thanks for Spreading the Word', '<p>Thanks for spreading the word about this event to your friends.</p>', 1);

INSERT INTO `civicrm_pcp_block`
    (`id`, `entity_table`, `entity_id`, `supporter_profile_id`, `is_approval_needed`, `is_tellfriend_enabled`, `tellfriend_limit`, `link_text`, `is_active`, `target_entity_id` )
VALUES
    (1, 'civicrm_contribution_page', 1, 2, 1, 1, 5, 'Create your own Personal Campaign Page!', 1, 1);

INSERT INTO civicrm_contact
    (contact_type, contact_sub_type, legal_identifier, external_identifier, sort_name, display_name, nick_name, source, preferred_communication_method, preferred_mail_format, do_not_phone, do_not_email, do_not_mail, do_not_trade, hash, is_opt_out,organization_name)
VALUES
    ('Organization',NULL,NULL,NULL,'Inner City Arts','Inner City Arts',NULL,NULL,'4','Both',0,0,0,0,'1902067651',0,'Inner City Arts');

INSERT INTO civicrm_membership_type
    (domain_id, name, description, member_of_contact_id, contribution_type_id, minimum_fee, duration_unit, duration_interval, period_type, fixed_period_start_day, fixed_period_rollover_day, relationship_type_id, relationship_direction, visibility, weight, is_active)
VALUES
    (@domainID, 'General', 'Regular annual membership.', 1, 2, 100.00, 'year', 2, 'rolling', NULL, NULL, 7, 'b_a', 'Public', 1, 1),
    (@domainID, 'Student', 'Discount membership for full-time students.', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Public', 2, 1),
    (@domainID, 'Lifetime', 'Lifetime membership.', 1, 2, 1200.00, 'lifetime', 1, 'rolling', NULL, NULL, 7, 'b_a', 'Admin', 3, 1);

INSERT INTO civicrm_membership_block
    (entity_table, entity_id, membership_types, membership_type_default, display_min_fee, is_separate_payment, new_title, new_text, renewal_title, renewal_text, is_required, is_active)
VALUES
    ('civicrm_contribution_page', 2, '{literal}a:2:{i:1;i:0;i:2;i:0;}{/literal}', 1, 1, 0, 'Membership Levels and Fees', 'Please select the appropriate membership level below. You will have a chance to review your selection and the corresponding dues on the next page prior to your credit card being charged.', 'Renew or Upgrade Your Membership', 'Information on your current membership level and expiration date is shown below. You may renew or upgrade at any time - but don''t let your membership lapse!', 1, 1);

INSERT INTO civicrm_pledge_block ( entity_table, entity_id, pledge_frequency_unit, is_pledge_interval, max_reminders, initial_reminder_day, additional_reminder_day)
VALUES 
    ('civicrm_contribution_page', 3, 'weekmonthyear', 1, 1, 5, 5),
    ('civicrm_contribution_page', 1, 'weekmonthyear', 0, 2, 5, 5);
        
INSERT INTO civicrm_premiums 
    VALUES (1, 'civicrm_contribution_page', 1, 1, 'Thank-you Gifts', 'We appreciate your support and invite you to choose from the exciting collection of thank-you gifts below. Minimum contribution amounts for each selection are included in the descriptions. (NOTE: These gifts are shown as examples only. No gifts will be sent to donors.)', 'premiums@example.org', NULL, 1);

INSERT INTO civicrm_product VALUES (1, 'Coffee Mug', 'This heavy-duty mug is great for home or office, coffee or tea or hot chocolate. Show your support to family, friends and colleagues. Choose from three great colors.', 'MUG-101', 'White, Black, Green', NULL, NULL, 12.50, 'USD', 5.00, 2.25, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO civicrm_premiums_product VALUES (1, 1, 1, 1);


-- Add sample activity type

SELECT @option_group_id_act  := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @option_value_max_val := max(ROUND(civicrm_option_value.value)) from civicrm_option_value where option_group_id = @option_group_id_act;

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`) 
VALUES
   (@option_group_id_act, 'Interview', (SELECT @option_value_max_val := @option_value_max_val + 1), 'Interview',  NULL, 0, NULL, @option_value_max_val, 'Conduct a phone or in person interview.', 0, 0, 1);

INSERT INTO `civicrm_contact_type`
  ( `name`, `label`,`image_URL`, `parent_id`, `is_active`,`is_reserved`)
 VALUES
  ( 'Student'     , '{ts escape="sql"}Student{/ts}'     , NULL, 1, 1, 0),
  ( 'Parent'      , '{ts escape="sql"}Parent{/ts}'      , NULL, 1, 1, 0),
  ( 'Staff'       , '{ts escape="sql"}Staff{/ts}'       , NULL, 1, 1, 0),
  ( 'Team'        , '{ts escape="sql"}Team{/ts}'        , NULL, 3, 1, 0),
  ( 'Sponsor'     , '{ts escape="sql"}Sponsor{/ts}'     , NULL, 3, 1, 0);
  
    SELECT @domain_id   := min(id) FROM civicrm_domain;
    SELECT @nav_indi    := id FROM civicrm_navigation WHERE name = 'New Individual';
    SELECT @nav_org     := id FROM civicrm_navigation WHERE name = 'New Organization';
    INSERT INTO `civicrm_navigation`
        ( domain_id, url, label, name,permission, permission_operator, parent_id, is_active, has_separator, weight ) 
    VALUES
        (  @domain_id, 'civicrm/contact/add&ct=Individual&cst=Student&reset=1'  , 'New Student', 'New Student', 'add contacts', '', @nav_indi, '1', NULL, 1 ), 
        (  @domain_id, 'civicrm/contact/add&ct=Individual&cst=Parent&reset=1'   , 'New Parent' , 'New Parent' , 'add contacts', '', @nav_indi, '1', NULL, 2 ),
        (  @domain_id, 'civicrm/contact/add&ct=Individual&cst=Staff&reset=1'    , 'New Staff'  , 'New Staff'  , 'add contacts', '', @nav_indi, '1', NULL, 3 ),
        (  @domain_id, 'civicrm/contact/add&ct=Organization&cst=Team&reset=1'   , 'New Team'   , 'New Team'   , 'add contacts', '', @nav_org , '1', NULL, 1 ),
        (  @domain_id, 'civicrm/contact/add&ct=Organization&cst=Sponsor&reset=1', 'New Sponsor', 'New Sponsor', 'add contacts', '', @nav_org , '1', NULL, 2 );

-- Add sample dashlets 

INSERT INTO `civicrm_dashboard` 
    ( `domain_id`, `label`, `url`, `permission`, `permission_operator`, `column_no`, `is_minimized`, `is_active`, `weight`, `is_fullscreen`, `fullscreen_url`) 
    VALUES 
    ( @domain_id, '{ts escape="sql"}Donor Report (Summary){/ts}'       , 'civicrm/report/instance/6&reset=1&section=1&snippet=5&charts=barChart',  'access CiviContribute', 'AND', 0, 0,'1', 4, '1', 'civicrm/report/instance/6&reset=1&section=1&snippet=5&charts=barChart&context=dashletFullscreen'),
    ( @domain_id, '{ts escape="sql"}Top Donors Report{/ts}'            , 'civicrm/report/instance/13&reset=1&section=2&snippet=5',                 'access CiviContribute', 'AND', 0, 0,'1', 5, '1', 'civicrm/report/instance/13&reset=1&section=2&snippet=5&context=dashletFullscreen'),
    ( @domain_id, '{ts escape="sql"}Event Income Report (Summary){/ts}', 'civicrm/report/instance/23&reset=1&section=1&snippet=5&charts=pieChart', 'access CiviEvent'     , 'AND', 0, 0,'1', 6, '1', 'civicrm/report/instance/23&reset=1&section=1&snippet=5&charts=pieChart&context=dashletFullscreen'),
    ( @domain_id, '{ts escape="sql"}Membership Report (Summary){/ts}'  , 'civicrm/report/instance/19&reset=1&section=2&snippet=5',                 'access CiviMember'    , 'AND', 0, 0,'1', 7, '1', 'civicrm/report/instance/19&reset=1&section=2&snippet=5&context=dashletFullscreen');
   
-- INSERT sample data for membership 

SELECT @priceSetID := max(id) FROM `civicrm_price_set` WHERE `name` LIKE 'default_membership_type_amount' AND `is_quick_config` = 1;
INSERT INTO `civicrm_price_field` ( `price_set_id`, `name`, `label`, `html_type` ) 
VALUES ( @priceSetID, '1', 'Membership Amount', 'Radio' );

SELECT @priceFieldId := max(id) FROM `civicrm_price_field` WHERE `name` LIKE '1';

INSERT civicrm_price_field_value ( price_field_id, name, label, description, amount, weight, membership_type_id )
SELECT @priceFieldId, LOWER(name), name, description, minimum_fee, id as weight, id  FROM `civicrm_membership_type`;


SELECT @contribution_type_id := max(id) FROM `civicrm_contribution_type` WHERE `name` = 'Member Dues';

INSERT INTO `civicrm_price_set` (`name`,`title`,`is_active`,`extends`, `contribution_type_id`, `is_quick_config`)
VALUES ('member_signup_and_renewal', 'Member Signup and Renewal', 1, 3, @contribution_type_id, 1),
       ('pledge_for_civicrm', 'Pledge for CiviCRM!',1,2,null,1);

SELECT @priceSetId := max(id) FROM `civicrm_price_set` WHERE `is_quick_config` = 1 and name = 'member_signup_and_renewal';

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`, `is_enter_qty`, `weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`, `visibility_id`) VALUES ( @priceSetId, 'membership_amount', 'Membership Amount', 'Radio', 0, 3, 1, 1, 1, 1, 1);

INSERT INTO `civicrm_price_set_entity` (`entity_table`,`entity_id`,`price_set_id`) VALUES ('civicrm_contribution_page', 2, @priceSetId);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'membership_amount';

SELECT @membershipIdG := max(id) FROM `civicrm_membership_type` WHERE name= 'General';

SELECT @membershipIdS := max(id) FROM `civicrm_membership_type` WHERE name= 'Student';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `membership_type_id`,  `is_active`, `is_default`) 
VALUES
    (@priceFieldID,'General','General','100.00', 1, @membershipIdG, 1, 1),
    (@priceFieldID,'Student','Student','50.00', 1, @membershipIdS , 1, 0);

SELECT @priceSetId := max(id) FROM `civicrm_price_set` WHERE `is_quick_config` = 1 AND `name` = 'pledge_for_civicrm';

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`, `is_enter_qty`, `weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`, `visibility_id`)
VALUES ( @priceSetId, 'other_amount', 'Other Amount', 'Text', 0, 3, 0, 1, 1, 1, 1);

INSERT INTO `civicrm_price_set_entity` (`entity_table`,`entity_id`,`price_set_id`) 
         VALUES ('civicrm_contribution_page', 3, @priceSetId);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'other_amount';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `is_active`, `is_default`) 
VALUES
    (@priceFieldID,'other_amount','Other Amount','1',1,1,0);


-- Insert sample data for event

INSERT INTO `civicrm_price_set` (`name`,`title`,`is_active`,`extends`, `is_quick_config`)
VALUES ('rain_forest_cup_youth_soccer_tournament', 'Rain-forest Cup Youth Soccer Tournament', 1, 1, 1),
       ('fall_fundraiser_dinner', 'Fall Fundraiser Dinner', 1, 1, 1),
       ('summer_solstice_festival_day_concert', 'Summer Solstice Festival Day Concert',1,1,1);

SELECT @priceSetId := max(id) FROM `civicrm_price_set` WHERE `is_quick_config` = 1 AND `name` = 'rain_forest_cup_youth_soccer_tournament';

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`, `is_enter_qty`, `weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`, `visibility_id`)
VALUES ( @priceSetId, 'tournament_fees', 'Tournament Fees', 'Radio', 0, 1, 1, 1, 1, 1, 1);

INSERT INTO `civicrm_price_set_entity` (`entity_table`,`entity_id`,`price_set_id`) 
         VALUES ('civicrm_event', 3, @priceSetId);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'tournament_fees';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `is_active`, `is_default`) 
VALUES
    (@priceFieldID,'tiny_tots__ages_5_8_','Tiny-tots (ages 5-8)','800',1,1,1),
    (@priceFieldID,'junior_Stars__ages_9_12_','Junior Stars (ages 9-12)','1000',2,1,0),
    (@priceFieldID,'super_Stars__ages_13_18_','Super Stars (ages 13-18)','1500',3,1,0);

SELECT @priceSetId := max(id) FROM `civicrm_price_set` WHERE `is_quick_config` = 1 AND `name` = 'fall_fundraiser_dinner';

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`, `is_enter_qty`, `weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`, `visibility_id`)
VALUES ( @priceSetId, 'dinner_contribution', 'Dinner Contribution', 'Radio', 0, 1, 1, 1, 1, 1, 1);

INSERT INTO `civicrm_price_set_entity` (`entity_table`,`entity_id`,`price_set_id`) 
         VALUES ('civicrm_event', 1, @priceSetId);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'dinner_contribution';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `is_active`, `is_default`) 
VALUES
    (@priceFieldID,'single','Single','50',1,1,1),
    (@priceFieldID,'couple','Couple','100',2,1,0),
    (@priceFieldID,'family','Family','200',3,1,0);

SELECT @priceSetId := max(id) FROM `civicrm_price_set` WHERE `is_quick_config` = 1 AND `name` = 'summer_solstice_festival_day_concert';

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`, `is_enter_qty`, `weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`, `visibility_id`)
VALUES ( @priceSetId, 'festival_fee', 'Festival Fee', 'Radio', 0, 1, 1, 1, 1, 1, 1);

INSERT INTO `civicrm_price_set_entity` (`entity_table`,`entity_id`,`price_set_id`) 
         VALUES ('civicrm_event', 2, @priceSetId);

SELECT @priceFieldID := max(id) FROM `civicrm_price_field` WHERE `price_set_id` = @priceSetId AND name = 'festival_fee';

INSERT INTO 
       `civicrm_price_field_value` (`price_field_id`,`name`,`label`, `amount`, `weight`, `is_active`, `is_default`) 
VALUES
    (@priceFieldID,'bass','Bass','25',1,1,1),
    (@priceFieldID,'tenor','Tenor','40',2,1,0),
    (@priceFieldID,'soprano','Soprano','50',3,1,0);


