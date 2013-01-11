UPDATE `variables` SET `liste` =
'ID_PERGAME:id_pret Pergame\r\n
IDABON:id abonné\r\n
ORDREABON:no d''ordre\r\n
EN_COURS:prêt en cours\r\n
DATE_PRET:date du prêt\r\n
DATE_RETOUR:date de retour\r\n
ID_NOTICE_ORIGINE:id notice Pergame\r\n
SUPPORT:code support\r\n
CODE_BARRES:code-barres\r\n
NB_PROLONGATIONS:nombre de prolongations'
WHERE `clef` = 'champs_pret';

ALTER TABLE `prets` ADD `NB_PROLONGATIONS` SMALLINT NOT NULL ;