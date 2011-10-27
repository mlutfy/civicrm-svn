-- CRM-6138
-- language list from http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
-- most common languages, according to http://en.wikipedia.org/wiki/List_of_languages_by_number_of_native_speakers enabled

INSERT INTO `civicrm_option_group`
  (`name`, {localize field='title'}`title`{/localize}, {localize field='description'}`description`{/localize}, `is_reserved`, `is_active`)
VALUES
  ('languages', {localize}'{ts escape="sql"}Languages{/ts}'{/localize}, {localize}'{ts escape="sql"}List of Languages{/ts}'{/localize}, 1, 1);
  
SELECT @option_group_id_languages      := max(id) from civicrm_option_group where name = 'languages';

SELECT @counter := 0;
INSERT INTO civicrm_option_value
  (option_group_id, is_default, is_active, name, value, {localize field='label'}label{/localize}, weight)
VALUES
  (@option_group_id_languages, 0, 0, 'ab_GE', 'ab', {localize}'{ts escape="sql"}Abkhaz{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'aa_ET', 'aa', {localize}'{ts escape="sql"}Afar{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'af_ZA', 'af', {localize}'{ts escape="sql"}Afrikaans{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ak_GH', 'ak', {localize}'{ts escape="sql"}Akan{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'sq_AL', 'sq', {localize}'{ts escape="sql"}Albanian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'am_ET', 'am', {localize}'{ts escape="sql"}Amharic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'ar_EG', 'ar', {localize}'{ts escape="sql"}Arabic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'an_ES', 'an', {localize}'{ts escape="sql"}Aragonese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'hy_AM', 'hy', {localize}'{ts escape="sql"}Armenian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'as_IN', 'as', {localize}'{ts escape="sql"}Assamese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'av_RU', 'av', {localize}'{ts escape="sql"}Avaric{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ae_XX', 'ae', {localize}'{ts escape="sql"}Avestan{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ay_BO', 'ay', {localize}'{ts escape="sql"}Aymara{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'az_AZ', 'az', {localize}'{ts escape="sql"}Azerbaijani{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'bm_ML', 'bm', {localize}'{ts escape="sql"}Bambara{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ba_RU', 'ba', {localize}'{ts escape="sql"}Bashkir{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'eu_ES', 'eu', {localize}'{ts escape="sql"}Basque{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'be_BY', 'be', {localize}'{ts escape="sql"}Belarusian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'bn_BD', 'bn', {localize}'{ts escape="sql"}Bengali{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'bh_IN', 'bh', {localize}'{ts escape="sql"}Bihari{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'bi_VU', 'bi', {localize}'{ts escape="sql"}Bislama{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'bs_BA', 'bs', {localize}'{ts escape="sql"}Bosnian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'br_FR', 'br', {localize}'{ts escape="sql"}Breton{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'bg_BG', 'bg', {localize}'{ts escape="sql"}Bulgarian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'my_MM', 'my', {localize}'{ts escape="sql"}Burmese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'ca_ES', 'ca', {localize}'{ts escape="sql"}Catalan; Valencian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ch_GU', 'ch', {localize}'{ts escape="sql"}Chamorro{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ce_RU', 'ce', {localize}'{ts escape="sql"}Chechen{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ny_MW', 'ny', {localize}'{ts escape="sql"}Chichewa; Chewa; Nyanja{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'zh_CN', 'zh', {localize}'{ts escape="sql"}Chinese (China){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'zh_TW', 'zh', {localize}'{ts escape="sql"}Chinese (Taiwan){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'cv_RU', 'cv', {localize}'{ts escape="sql"}Chuvash{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kw_GB', 'kw', {localize}'{ts escape="sql"}Cornish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'co_FR', 'co', {localize}'{ts escape="sql"}Corsican{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'cr_CA', 'cr', {localize}'{ts escape="sql"}Cree{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'hr_HR', 'hr', {localize}'{ts escape="sql"}Croatian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'cs_CZ', 'cs', {localize}'{ts escape="sql"}Czech{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'da_DK', 'da', {localize}'{ts escape="sql"}Danish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'dv_MV', 'dv', {localize}'{ts escape="sql"}Divehi; Dhivehi; Maldivian;{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'nl_NL', 'nl', {localize}'{ts escape="sql"}Dutch{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'dz_BT', 'dz', {localize}'{ts escape="sql"}Dzongkha{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'en_AU', 'en', {localize}'{ts escape="sql"}English (Australia){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'en_CA', 'en', {localize}'{ts escape="sql"}English (Canada){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'en_GB', 'en', {localize}'{ts escape="sql"}English (United Kingdom){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 1, 1, 'en_US', 'en', {localize}'{ts escape="sql"}English (United States){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'eo_XX', 'eo', {localize}'{ts escape="sql"}Esperanto{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'et_EE', 'et', {localize}'{ts escape="sql"}Estonian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ee_GH', 'ee', {localize}'{ts escape="sql"}Ewe{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'fo_FO', 'fo', {localize}'{ts escape="sql"}Faroese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'fj_FJ', 'fj', {localize}'{ts escape="sql"}Fijian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'fi_FI', 'fi', {localize}'{ts escape="sql"}Finnish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'fr_CA', 'fr', {localize}'{ts escape="sql"}French (Canada){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'fr_FR', 'fr', {localize}'{ts escape="sql"}French (France){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ff_SN', 'ff', {localize}'{ts escape="sql"}Fula; Fulah; Pulaar; Pular{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'gl_ES', 'gl', {localize}'{ts escape="sql"}Galician{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ka_GE', 'ka', {localize}'{ts escape="sql"}Georgian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'de_DE', 'de', {localize}'{ts escape="sql"}German{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'de_CH', 'de', {localize}'{ts escape="sql"}German (Swiss){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'el_GR', 'el', {localize}'{ts escape="sql"}Greek, Modern{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'gn_PY', 'gn', {localize}'{ts escape="sql"}Guaraní{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'gu_IN', 'gu', {localize}'{ts escape="sql"}Gujarati{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ht_HT', 'ht', {localize}'{ts escape="sql"}Haitian; Haitian Creole{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ha_NG', 'ha', {localize}'{ts escape="sql"}Hausa{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'he_IL', 'he', {localize}'{ts escape="sql"}Hebrew (modern){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'hz_NA', 'hz', {localize}'{ts escape="sql"}Herero{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'hi_IN', 'hi', {localize}'{ts escape="sql"}Hindi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ho_PG', 'ho', {localize}'{ts escape="sql"}Hiri Motu{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'hu_HU', 'hu', {localize}'{ts escape="sql"}Hungarian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ia_XX', 'ia', {localize}'{ts escape="sql"}Interlingua{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'id_ID', 'id', {localize}'{ts escape="sql"}Indonesian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ie_XX', 'ie', {localize}'{ts escape="sql"}Interlingue{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ga_IE', 'ga', {localize}'{ts escape="sql"}Irish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ig_NG', 'ig', {localize}'{ts escape="sql"}Igbo{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ik_US', 'ik', {localize}'{ts escape="sql"}Inupiaq{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'io_XX', 'io', {localize}'{ts escape="sql"}Ido{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'is_IS', 'is', {localize}'{ts escape="sql"}Icelandic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'it_IT', 'it', {localize}'{ts escape="sql"}Italian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'iu_CA', 'iu', {localize}'{ts escape="sql"}Inuktitut{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'ja_JP', 'ja', {localize}'{ts escape="sql"}Japanese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'jv_ID', 'jv', {localize}'{ts escape="sql"}Javanese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kl_GL', 'kl', {localize}'{ts escape="sql"}Kalaallisut, Greenlandic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kn_IN', 'kn', {localize}'{ts escape="sql"}Kannada{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kr_NE', 'kr', {localize}'{ts escape="sql"}Kanuri{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ks_IN', 'ks', {localize}'{ts escape="sql"}Kashmiri{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kk_KZ', 'kk', {localize}'{ts escape="sql"}Kazakh{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'km_KH', 'km', {localize}'{ts escape="sql"}Khmer{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ki_KE', 'ki', {localize}'{ts escape="sql"}Kikuyu, Gikuyu{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'rw_RW', 'rw', {localize}'{ts escape="sql"}Kinyarwanda{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ky_KG', 'ky', {localize}'{ts escape="sql"}Kirghiz, Kyrgyz{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kv_RU', 'kv', {localize}'{ts escape="sql"}Komi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kg_CD', 'kg', {localize}'{ts escape="sql"}Kongo{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ko_KR', 'ko', {localize}'{ts escape="sql"}Korean{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ku_IQ', 'ku', {localize}'{ts escape="sql"}Kurdish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'kj_NA', 'kj', {localize}'{ts escape="sql"}Kwanyama, Kuanyama{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'la_VA', 'la', {localize}'{ts escape="sql"}Latin{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'lb_LU', 'lb', {localize}'{ts escape="sql"}Luxembourgish, Letzeburgesch{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'lg_UG', 'lg', {localize}'{ts escape="sql"}Luganda{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'li_NL', 'li', {localize}'{ts escape="sql"}Limburgish, Limburgan, Limburger{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ln_CD', 'ln', {localize}'{ts escape="sql"}Lingala{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'lo_LA', 'lo', {localize}'{ts escape="sql"}Lao{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'lt_LT', 'lt', {localize}'{ts escape="sql"}Lithuanian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'lu_CD', 'lu', {localize}'{ts escape="sql"}Luba-Katanga{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'lv_LV', 'lv', {localize}'{ts escape="sql"}Latvian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'gv_IM', 'gv', {localize}'{ts escape="sql"}Manx{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mk_MK', 'mk', {localize}'{ts escape="sql"}Macedonian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mg_MG', 'mg', {localize}'{ts escape="sql"}Malagasy{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ms_MY', 'ms', {localize}'{ts escape="sql"}Malay{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ml_IN', 'ml', {localize}'{ts escape="sql"}Malayalam{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mt_MT', 'mt', {localize}'{ts escape="sql"}Maltese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mi_NZ', 'mi', {localize}'{ts escape="sql"}Māori{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mr_IN', 'mr', {localize}'{ts escape="sql"}Marathi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mh_MH', 'mh', {localize}'{ts escape="sql"}Marshallese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'mn_MN', 'mn', {localize}'{ts escape="sql"}Mongolian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'na_NR', 'na', {localize}'{ts escape="sql"}Nauru{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'nv_US', 'nv', {localize}'{ts escape="sql"}Navajo, Navaho{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'nb_NO', 'nb', {localize}'{ts escape="sql"}Norwegian Bokmål{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'nd_ZW', 'nd', {localize}'{ts escape="sql"}North Ndebele{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ne_NP', 'ne', {localize}'{ts escape="sql"}Nepali{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ng_NA', 'ng', {localize}'{ts escape="sql"}Ndonga{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'nn_NO', 'nn', {localize}'{ts escape="sql"}Norwegian Nynorsk{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'no_NO', 'no', {localize}'{ts escape="sql"}Norwegian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ii_CN', 'ii', {localize}'{ts escape="sql"}Nuosu{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'nr_ZA', 'nr', {localize}'{ts escape="sql"}South Ndebele{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'oc_FR', 'oc', {localize}'{ts escape="sql"}Occitan (after 1500){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'oj_CA', 'oj', {localize}'{ts escape="sql"}Ojibwa{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'cu_BG', 'cu', {localize}'{ts escape="sql"}Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'om_ET', 'om', {localize}'{ts escape="sql"}Oromo{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'or_IN', 'or', {localize}'{ts escape="sql"}Oriya{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'os_GE', 'os', {localize}'{ts escape="sql"}Ossetian, Ossetic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'pa_IN', 'pa', {localize}'{ts escape="sql"}Panjabi, Punjabi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'pi_KH', 'pi', {localize}'{ts escape="sql"}Pāli{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'fa_IR', 'fa', {localize}'{ts escape="sql"}Persian (Iran){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'pl_PL', 'pl', {localize}'{ts escape="sql"}Polish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ps_AF', 'ps', {localize}'{ts escape="sql"}Pashto, Pushto{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'pt_BR', 'pt', {localize}'{ts escape="sql"}Portuguese (Brazil){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'pt_PT', 'pt', {localize}'{ts escape="sql"}Portuguese (Portugal){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'qu_PE', 'qu', {localize}'{ts escape="sql"}Quechua{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'rm_CH', 'rm', {localize}'{ts escape="sql"}Romansh{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'rn_BI', 'rn', {localize}'{ts escape="sql"}Kirundi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'ro_RO', 'ro', {localize}'{ts escape="sql"}Romanian, Moldavian, Moldovan{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'ru_RU', 'ru', {localize}'{ts escape="sql"}Russian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sa_IN', 'sa', {localize}'{ts escape="sql"}Sanskrit{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sc_IT', 'sc', {localize}'{ts escape="sql"}Sardinian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sd_IN', 'sd', {localize}'{ts escape="sql"}Sindhi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'se_NO', 'se', {localize}'{ts escape="sql"}Northern Sami{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sm_WS', 'sm', {localize}'{ts escape="sql"}Samoan{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sg_CF', 'sg', {localize}'{ts escape="sql"}Sango{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sr_RS', 'sr', {localize}'{ts escape="sql"}Serbian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'gd_GB', 'gd', {localize}'{ts escape="sql"}Scottish Gaelic; Gaelic{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sn_ZW', 'sn', {localize}'{ts escape="sql"}Shona{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'si_LK', 'si', {localize}'{ts escape="sql"}Sinhala, Sinhalese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'sk_SK', 'sk', {localize}'{ts escape="sql"}Slovak{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'sl_SI', 'sl', {localize}'{ts escape="sql"}Slovene{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'so_SO', 'so', {localize}'{ts escape="sql"}Somali{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'st_ZA', 'st', {localize}'{ts escape="sql"}Southern Sotho{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'es_ES', 'es', {localize}'{ts escape="sql"}Spanish; Castilian (Spain){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'es_MX', 'es', {localize}'{ts escape="sql"}Spanish; Castilian (Mexico){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'es_PR', 'es', {localize}'{ts escape="sql"}Spanish; Castilian (Puerto Rico){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'su_ID', 'su', {localize}'{ts escape="sql"}Sundanese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'sw_TZ', 'sw', {localize}'{ts escape="sql"}Swahili{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ss_ZA', 'ss', {localize}'{ts escape="sql"}Swati{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'sv_SE', 'sv', {localize}'{ts escape="sql"}Swedish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ta_IN', 'ta', {localize}'{ts escape="sql"}Tamil{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'te_IN', 'te', {localize}'{ts escape="sql"}Telugu{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'tg_TJ', 'tg', {localize}'{ts escape="sql"}Tajik{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'th_TH', 'th', {localize}'{ts escape="sql"}Thai{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ti_ET', 'ti', {localize}'{ts escape="sql"}Tigrinya{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'bo_CN', 'bo', {localize}'{ts escape="sql"}Tibetan Standard, Tibetan, Central{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'tk_TM', 'tk', {localize}'{ts escape="sql"}Turkmen{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'tl_PH', 'tl', {localize}'{ts escape="sql"}Tagalog{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'tn_ZA', 'tn', {localize}'{ts escape="sql"}Tswana{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'to_TO', 'to', {localize}'{ts escape="sql"}Tonga (Tonga Islands){/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'tr_TR', 'tr', {localize}'{ts escape="sql"}Turkish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ts_ZA', 'ts', {localize}'{ts escape="sql"}Tsonga{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'tt_RU', 'tt', {localize}'{ts escape="sql"}Tatar{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'tw_GH', 'tw', {localize}'{ts escape="sql"}Twi{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ty_PF', 'ty', {localize}'{ts escape="sql"}Tahitian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ug_CN', 'ug', {localize}'{ts escape="sql"}Uighur, Uyghur{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'uk_UA', 'uk', {localize}'{ts escape="sql"}Ukrainian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'ur_PK', 'ur', {localize}'{ts escape="sql"}Urdu{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'uz_UZ', 'uz', {localize}'{ts escape="sql"}Uzbek{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 've_ZA', 've', {localize}'{ts escape="sql"}Venda{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 1, 'vi_VN', 'vi', {localize}'{ts escape="sql"}Vietnamese{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'vo_XX', 'vo', {localize}'{ts escape="sql"}Volapük{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'wa_BE', 'wa', {localize}'{ts escape="sql"}Walloon{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'cy_GB', 'cy', {localize}'{ts escape="sql"}Welsh{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'wo_SN', 'wo', {localize}'{ts escape="sql"}Wolof{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'fy_NL', 'fy', {localize}'{ts escape="sql"}Western Frisian{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'xh_ZA', 'xh', {localize}'{ts escape="sql"}Xhosa{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'yi_US', 'yi', {localize}'{ts escape="sql"}Yiddish{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'yo_NG', 'yo', {localize}'{ts escape="sql"}Yoruba{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'za_CN', 'za', {localize}'{ts escape="sql"}Zhuang, Chuang{/ts}'{/localize}, @counter := @counter + 1),
  (@option_group_id_languages, 0, 0, 'zu_ZA', 'zu', {localize}'{ts escape="sql"}Zulu{/ts}'{/localize}, @counter := @counter + 1);
