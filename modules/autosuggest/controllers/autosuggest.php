<?php
// -------------------------------
// Free Autosuggest 3.0 FOR OXID 4.7x AZURE 2013 PREMIERELINE.DE
// -------------------------------
//Released under the GNU General Public License

// Aktuelle Versionen:
// Autosuggest 3.1: Modulform durch eComStyle.de erstellt (alle Dateien sind nun im Modulordner) und neues Design.
// Autosuggest 3.2: Erweitert durch eComStyle.de um die Suche nach Artikelnr., einige Bugfixes und ein neues Design für Azure und das Oxid Mobiletheme.
// Extension for Variants and some bugs fixed
// Autosuggest 3.3: Anpasung durch eComStyle.de fuer Shopversion 4.9.

class autoSuggest extends oxubase
{
    public function render()
    {
        $sShopURL = oxRegistry::getConfig()->getConfigParam( 'sShopURL' );
        $bActivateVariants = FALSE;  // set to TRUE for search in variants too

        if ( !$bActivateVariants )
            $SQL_FROM = 'SELECT * FROM oxarticles WHERE oxparentid = "" AND oxactive = 1 ';
        else
            $SQL_FROM = 'SELECT '
                    . 'OXID, IF(oxparentid="",a.oxtitle,CONCAT((SELECT b.oxtitle FROM oxarticles b WHERE b.oxid=a.oxparentid),", ",a.oxvarselect)) AS OXTITLE, '
                    . 'OXARTNUM, OXPRICE, IF (oxparentid="",oxpic1,(SELECT b.oxpic1 FROM oxarticles b WHERE b.oxid=a.oxparentid)) AS OXPIC1 '
                    . 'FROM oxarticles a '
                    . 'WHERE oxactive = 1 ';

        $SQL_WHERE = 'oxtitle';

        $searchq		=	strip_tags($_GET['q']);
        $articles_pp = 6;

        $page = !isset($_GET["page"]) ? 1 : intval($_GET["page"]);

        $start =  ($page * $articles_pp) - $articles_pp;

        $dbx  = oxDb::getDb()->qstr(''.$searchq.'');
        $dbx2 = oxDb::getDb()->qstr('%'.$searchq.'%');
        if ( !$bActivateVariants )
            $getRecord_sql = $SQL_FROM." AND (oxtitle LIKE ".$dbx2." or oxartnum LIKE ".$dbx2.") LIMIT ". $start." , ". $articles_pp;
        else
            $getRecord_sql = $SQL_FROM." AND (oxtitle LIKE ".$dbx2." or oxvarselect LIKE ".$dbx2." or oxartnum LIKE ".$dbx2.") LIMIT ". $start." , ". $articles_pp;
        mysql_query( "SET NAMES 'utf8'" ); // Umlaute ausgeben
        $getRecord		=	mysql_query($getRecord_sql);

        if ( !$bActivateVariants )
            $getRecord_sum = $SQL_FROM." AND (oxtitle LIKE ".$dbx2." or oxartnum LIKE ".$dbx2." = 1)";
        else
            $getRecord_sum = $SQL_FROM." AND (oxtitle LIKE ".$dbx2." or oxvarselect LIKE ".$dbx2." or oxartnum LIKE ".$dbx2." = 1)";
        $getRecordsum		=	mysql_query($getRecord_sum);
        if($getRecord) {
            $num_rows1 = mysql_num_rows($getRecordsum);
            $pages_sum = ceil($num_rows1 / $articles_pp);
        }

        if ($num_rows1 == 0) {
            if ( !$bActivateVariants )
                $where_str = "AND ( soundex_match(".$dbx.", oxtitle, ' ') = 1 ) LIMIT ". $start." , ". $articles_pp;
            else
                $where_str = "AND ( soundex_match(".$dbx.", CONCAT(oxtitle,' ',oxvarselect), ' ') = 1 ) LIMIT ". $start." , ". $articles_pp;

            $getRecord_sql = $SQL_FROM.' '.$where_str;
            $getRecord = mysql_query($getRecord_sql);

            if ( !$bActivateVariants )
                $getRecord_sum = $SQL_FROM."AND ( soundex_match(".$dbx.", oxtitle, ' ') = 1 )";
            else
                $getRecord_sum = $SQL_FROM."AND ( soundex_match(".$dbx.", CONCAT(oxtitle,' ',oxvarselect), ' ') = 1 )";
            $getRecordsum = mysql_query($getRecord_sum);
            if($getRecord) {
                $num_rows2 = mysql_num_rows($getRecordsum);
                $pages_sum = ceil($num_rows2 / $articles_pp);
            }
        }
        if ($num_rows1 == 0 && $num_rows2 == 0) {
            if ( !$bActivateVariants )
                $where_str = "AND ( koelner_match(".$dbx.", oxtitle, ' ') = 1 ) LIMIT ". $start." , ". $articles_pp;
            else
                $where_str = "AND ( koelner_match(".$dbx.", CONCAT(oxtitle,' ',oxvarselect), ' ') = 1 ) LIMIT ". $start." , ". $articles_pp;

            $getRecord_sql = $SQL_FROM.' '. $where_str;
            $getRecord = mysql_query($getRecord_sql);

            if ( !$bActivateVariants )
                $getRecord_sum = $SQL_FROM."AND ( koelner_match(".$dbx.", oxtitle, ' ') = 1 )";
            else
                $getRecord_sum = $SQL_FROM."AND ( koelner_match(".$dbx.", CONCAT(oxtitle,' ',oxvarselect), ' ') = 1 )";
            $getRecordsum = mysql_query($getRecord_sum);
            if($getRecord) {
                $num_rows3 = mysql_num_rows($getRecordsum);
                $pages_sum = ceil($num_rows3 / $articles_pp);
            }
        }

        if ($num_rows1 == 0 && $num_rows2 == 0 && $num_rows3 == 0) {
            echo ' <table>';
            exit;
        }

        if(strlen($searchq)>0){

        parent::render();
        $oCurr=oxRegistry::getConfig()->getActShopCurrencyObject();

        echo '<table><tr class="first"><td colspan="4"><span style="color:#fff;"><br>Vorschl&auml;ge f&uuml;r Ihre Suche:</span></td></tr>';
        while ($row = mysql_fetch_array($getRecord)) {

            $query = "select oxseourl from oxseo where oxobjectid = '" . $row['OXID'] . "' and oxlang = 0 and oxparams IN(select oxid from oxcategories)";
            $result = mysql_query($query);
            while($zeile1 = mysql_fetch_array($result))
            {
                $seourl = $zeile1['oxseourl'] ;
            }
            $picname = trim(utf8_encode($row['OXPIC1']));
            $picname = ($picname == '') ? 'nopic.jpg':$picname;
            ?>

            <tr class="resall">
                <td class="title"><?php echo '<a href="/' .$seourl .'">' . $row['OXTITLE'] . '</a>'; ?></td>
                <td class="title artnr"><?php echo '<a href="/' .$seourl .'">' . "Artikelnr." . ' ' . $row['OXARTNUM'] . '</a>'; ?></td>
                <td class="title price"><?php echo  '<a href="/' .$seourl .'">' . (number_format($row['OXPRICE'], 2, ",", "").' '.$oCurr->name) . '</a>'; ?></td>
                <td class="image"><?php echo '<a class="picture" href="/' .$seourl .'"><img src="'.$sShopURL.'out/pictures/generated/product/1/87_87_75/' . $picname . '" alt="' . $row['OXTITLE'] . '" width="50" height="50">'; ?></td>
            </tr>

            <?php

        }

        if ($pages_sum < 2) {
            echo '<tr><td class="pages" colspan="2"></td></tr>';
        }

        if ($pages_sum > 1) {
            echo '<tr><td class="pages" colspan="2"><span style="color:#fff;"><br>Seiten:&nbsp;&nbsp;</span><ul class="pagination">';

            for($i=1; $i<=$pages_sum; $i++)
            {
                if ($i==$page){
                    echo '<li class="active"><a href="' . $i . '">' . $i . '</a></li>';
                }
                else {
                    echo '<li><a href="' . $i . '">' . $i . '</a></li>';
                }
            }

            echo '</ul></td></tr>';
        }

        echo '</table>';
        exit; // Header-Fehler vermeiden
            } else {
            parent::render();
            }
        }
    }
?>