UPDATE `variables` SET `liste` =
'
0:Unimarc\r\n
1:Ascii tabulé\r\n
2:Ascii séparé par des points-virgule\r\n
3:Ascii séparé par des "|"\r\n
4:Xml\r\n
5:CSV\r\n
6:Marc21\r\n
'
WHERE `clef`= 'import_format';
