CREATE TABLE `forum_mp` (
  mp_id int(11) NOT NULL AUTO_INCREMENT,
  mp_expediteur int(11) NOT NULL,
  mp_receveur int(11) NOT NULL,
  mp_titre varchar(100) collate latin1_general_ci NOT NULL,
  mp_text text collate latin1_general_ci NOT NULL,
  mp_time int(11) NOT NULL,
  mp_lu enum('0','1') collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`mp_id`)
)
