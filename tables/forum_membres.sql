CREATE TABLE `forum_membres` (
  `membre_id` int(11) NOT NULL AUTO_INCREMENT,
  `membre_pseudo` varchar(30) collate latin1_general_ci NOT NULL,
  `membre_mdp` varchar(32) collate latin1_general_ci NOT NULL,
  `membre_email` varchar(250) collate latin1_general_ci NOT NULL,
  `membre_msn` varchar(250) collate latin1_general_ci NOT NULL,
  `membre_siteweb` varchar(100) collate latin1_general_ci NOT NULL,
  `membre_avatar` varchar(100) collate latin1_general_ci NOT NULL,
  `membre_signature` varchar(200) collate latin1_general_ci NOT NULL,
  `membre_localisation` varchar(100) collate latin1_general_ci NOT NULL,
  `membre_inscrit` int(11) NOT NULL,
  `membre_derniere_visite` int(11) NOT NULL,
  `membre_rang` tinyint (4) DEFAULT 2,
  `membre_post` int(11) NOT NULL,
  PRIMARY KEY  (`membre_id`)
); 