-- CRM-6421
ALTER TABLE civicrm_mapping_field
MODIFY name varchar(255) COMMENT 'Mapping field key';

-- CRM-6554
SELECT @domainID := min(id) FROM civicrm_domain;
SELECT @navid := id FROM civicrm_navigation WHERE name='Option Lists';
SELECT @wt := max(weight) FROM civicrm_navigation WHERE parent_id=@navid;
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
( @domainID, 'civicrm/admin/options/wordreplacements&reset=1',                                                              '{ts escape="sql"}Word Replacements{/ts}',       'Word Replacements',                         'administer CiviCRM', '',   @navid, '1', NULL, @wt + 1);
