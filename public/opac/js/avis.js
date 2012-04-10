function showCmsAvis(id_news, element)
{
    oImgPlus = element.firstChild;
    oTableAvis = document.getElementById('avis_'+id_news);
    
    if(oTableAvis.style.display=='none')
    {
        oImgPlus.src = oImgPlus.src.replace( 'plus', 'moins' );
        oTableAvis.style.display='block';
    }
    else
    {
        oImgPlus.src = oImgPlus.src.replace( 'moins', 'plus' );
        oTableAvis.style.display='none';
    }
}

function showAvis(id_news,type)
{
    oTableBib = document.getElementById('bib_'+ id_news);
    oTableAbo = document.getElementById('abo_'+ id_news);
    oTableBib.style.display='none';
    oTableAbo.style.display='none';
    if(type =="bib") oTableBib.style.display='block';
    if(type =="abo") oTableAbo.style.display='block';
}