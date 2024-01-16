<?php
include("autoload.inc.php");
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$id_pdf=$_REQUEST['id_pdf'];
//get color
$db = "6_color";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
$row = mysql_fetch_object($res);
$color=$row->value;
$html = '<!DOCTYPE html>
<html lang="en">
<head>
  <style>
    .logo-page1
    {
      width:150px
    }
    .bottom-page1
    {
       position:absolute;width:150px;bottom:0
    }
    .border-title-logo-page1
    {
       color:#fff;width:150px;
       border-top-right-radius: 10px;
       border-bottom-left-radius: 1px;
       border:1px solid '.$color.';
       background:'.$color.';
       padding:5px;
    }
    .border-title-logo-page1-icon
    {
       color:#fff;width:150px;
       border-top-left-radius: 10px;
       border-bottom-left-radius: 1px;
       border:1px solid '.$color.';
       background:'.$color.';
       padding:5px;
    }
    .border-logo-page1
    {
       border-top-left-radius: 10px;
       border-bottom-left-radius: 1px;
       border:1px solid '.$color.';
       background:'.$color.';
    }
    img.border-logo-page1{height:50px;}
    .headline-page1
    {
       border:1px solid '.$color.';border-radius:0 0 20px 0;border-left:0px solid red;padding:20px;position:relative;
    }
    .headline-page1 p{margin:0}
    .headline-page1 span{margin:0}
    .page2{page-break-before:always}
    .page2 p{margin:0}
    .page3{page-break-before:always}
    .page3 p{margin:0}
    .page2{page-break-before:always}
    .page4{page-break-before:always}
    .page4 p{margin:0}
     table tr td{padding-left:5px}
    .page 2 .detail{margin-top:20px}
    .footer-top{padding:20px 0 30px 20px; border:1px solid '.$color.';border-radius:0 0 0 20px;height:350px}
  </style>
</head>
<body>
<!-- page1  !-->
<div style="float:left;width:70%;">
            <h4 style="font-size:28px;margin-top:0">DATA SPECIFICATIONS</h4>';
//$id_pdf=$_GET['id_pdf'];
//page 1 text top            
$db = "page1_text_top";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
$row = mysql_fetch_object($res);
$page1_text_top=$row->value;
//page 1 text bottom            
$db = "page1_text_bottom";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
$row = mysql_fetch_object($res);
$page1_text_bottom=$row->value;
//page 2 text            
$db = "page2_text";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
$row = mysql_fetch_object($res);
$page2_text=$row->value;

$html.= "<div style='color:".$color.";'>" .$page1_text_top."</div>";
$html.=  '<div class="headline-page1">';
                
//page 1 headline
$db = "page1_headline";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
while ($row = mysql_fetch_object($res)) {
  $html.='<div style="color:'.$color.';font-weight:600">
                   '.$row->title.'
                </div>
                <div style="padding-top:10px;padding-bottom:20px">
                  '.$row->text.'
                </div>';
}
//page 1 text bottom
$html.='</div>
           
       </div>';
$html.='<div style="float:left;position:relative;height:100%">
             <div class="bottom-page1">
                   <p><img src="upload_logo/bottom.png" width="129" height="67" alt="" /></p>
                   '.$page1_text_bottom.'
             </div>';
$html.='   <div class="logo-page1">
                <div class="border-title-logo-page1-icon">isolates</div>
                <p style="padding-left:30px;color:'.$color.';font-familyz:Univers-BlackExt">Soja Protein</p>
                <table style="padding-left:30px" cellspacing="3" cellpadding="8">';        

$db = "page1_logo";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
$k=0;
while ($row = mysql_fetch_object($res)) {
 if($k%2==0)
   $html.='<tr><td><img width="39" src="../pdf/'.$row->image.'" class="border-logo-page1"/></td>';
  else
   $html.='<td><img width="39" src="../pdf/'.$row->image.'" class="border-logo-page1"/></td></tr>';
  $k++;  
}
$html.='</table> ';
$html.=' </div>';
$html.='</div>';       
       
// page2 
 $html.=      '<div style="clear:both"></div>
       <div class="page2">
          <div style="float:left;font-familyz:Univers-BlackExt;width:300px">
              <div class="border-title-logo-page1">isolates</div>
              <p style="color:'.$color.'">Soja Protein</p>
          </div>
          <div style="float:left;"><p style="color:'.$color.';font-familyz:Univers-LightUltraCondensed"><span style="font-size:38px">ISP 920H</span> Emulsion Typ</p><strong>DATA SPECIFICATIONS</strong>
          </div>
          <div style="clear:both"></div>
          
          <!-- header  !-->';
          
//page 2 header
$db = "page2_header";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
while ($row = mysql_fetch_object($res)) {
  $html.='<div class="detail">
  <h4 style="font-weight:500">'.$row->name.'</h4>
  <table width="100%">
  <tr><td colspan="2" style="background:'.$color.';height:15px"></td></tr>';
  //item of page 2 header
  $db = "page2_header_item";
  $sql = "SELECT * FROM $db where id_page2_header=".$row->id."";
  $res1 = safe_query($sql);
  $k=0;
  while ($row1 = mysql_fetch_object($res1)) {
     if($k%2==1)
      $style='style="background:'.$color.'"';
     else
      $style=''; 
     $html.='<tr style="font-weight:bold">
                <td '.$style.' width="50%">'.$row1->name.'</td>
                <td '.$style.' width="50%">'.$row1->value.'</td>
              </tr>';
     $k++;           
  }
  $html.='</table></div>';
}
//page2-text
$html.=          
          '<div class="page2-text">
            '.$page2_text.'
          </div>
          <!-- footer  !-->';
          
//page 2 footer
$db = "page2_footer";
$sql = "SELECT * FROM $db where id_pdf=".$id_pdf."";
$res = safe_query($sql);
while ($row = mysql_fetch_object($res)) {
  $html.='<div class="detail">
  <h4 style="font-weight:500">'.$row->name.'</h4>
  <table width="50%" style="float:left">
  <tr><td colspan="2" style="background:'.$color.';height:15px"></td></tr>';
  //item of page 2 header
  $db = "page2_footer_item";
  $sql = "SELECT * FROM $db where id_page2_footer=".$row->id."";
  $res1 = safe_query($sql);
  $k=0;
  while ($row1 = mysql_fetch_object($res1)) {
    if($row1->name!="")
    { 
         if($k>=9)
         {
            $html.='</table><table width="50%" style="float:left;padding-left:30px;">
            <tr><td colspan="2" style="background:'.$color.';height:15px"></td></tr>';
            $k=0;
         }
         if($k%2==1)
          $style='style="background:'.$color.'"';
         else
          $style=''; 
         $html.='<tr style="font-weight:bold">
                    <td '.$style.' width="50%">'.$row1->name.'</td>
                    <td '.$style.' width="50%">'.$row1->value.'</td>
                  </tr>';
         $k++; 
    }         
  }
  $html.='</table></div><div style="clear:both"></div>';
}     
$html.='</div>
<!-- page3  !-->    
       <div style="clear:both"></div>
       <div class="page3">
          <div style="float:left;font-familyz:Univers-BlackExt;width:200px">
              <div class="border-title-logo-page1">isolates</div>
              <p style="color:'.$color.'">Soja Protein</p>
          </div>
          <div style="float:left;margin-left:200px"><p style="color:'.$color.';font-familyz:Univers-LightUltraCondensed"><span style="font-size:38px">ISP 920H</span> Emulsion Typ</p><strong>DATA SPECIFICATIONS</strong></div>
          <div style="clear:both"></div>
          <div class="detail">
            <h4 style="font-weight:500">ALLERGENELISTE</h4>
            <table width="100%">
              <tr style="background:'.$color.';color:#fff">
                <td width="50%">Allergen</td>
                <td width="25%">Rezept mit</td>
                <td width="25%">Rezept ohne</td>
              </tr>';
//page3
$db = "checkbox";
$sql = "SELECT * FROM $db where format_page=1";
$res = safe_query($sql);
$k=0;
while ($row = mysql_fetch_object($res)) { 
   $sql="select * from pdf_checkbox where id_pdf=".$id_pdf." and id_checkbox=".$row->id." ";
   $res1 = safe_query($sql);
   $row1 = mysql_fetch_object($res1);
   if($k%2==1)
      $style='style="background:#F9EECF;font-weight:bold"';
     else
      $style='style="font-weight:bold"';
   $html.='<tr '.$style.'><td width="50%">'.$row->title.'</td>';
   if($row1->id!="")
     $html.='<td width="25%"><input type="checkbox" checked/></td>
                <td width="25%"></td></tr>';
     else
      $html.='<td width="25%"></td>
                <td width="25%"><input type="checkbox" checked/></td></tr>';
    $k++;                       
}
$html.='</table></div></div>';
$html.='
<!-- page4  !-->    
       <div style="clear:both"></div>
       <div class="page4">
          <div style="float:left;font-familyz:Univers-BlackExt;width:200px">
              <div class="border-title-logo-page1">isolates</div>
              <p style="color:'.$color.'">Soja Protein</p>
          </div>
          <div style="float:left;margin-left:200px"><p style="color:'.$color.';font-familyz:Univers-LightUltraCondensed"><span style="font-size:38px">ISP 920H</span> Emulsion Typ</p><strong>DATA SPECIFICATIONS</strong></div>
          <div style="clear:both"></div>
          <div class="detail">
            <h4 style="font-weight:500">ALLERGENELISTE</h4>
            <table width="100%">
              <tr style="background:'.$color.';color:#fff">
                <td width="50%">Zusätzliche Allergen</td>
                <td width="25%">Rezept mit</td>
                <td width="25%">Rezept ohne</td>
              </tr>';
//page4
$db = "checkbox";
$sql = "SELECT * FROM $db where format_page=2";
$res = safe_query($sql);
$k=0;
while ($row = mysql_fetch_object($res)) { 
   $sql="select * from pdf_checkbox where id_pdf=".$id_pdf." and id_checkbox=".$row->id." ";
   $res1 = safe_query($sql);
   $row1 = mysql_fetch_object($res1);
   if($k%2==1)
      $style='style="background:#F9EECF;font-weight:bold"';
     else
      $style='style="font-weight:bold"';
   $html.='<tr '.$style.'><td width="50%">'.$row->title.'</td>';
   if($row1->id!="")
     $html.='<td width="25%"><input type="checkbox" checked/></td>
                <td width="25%"></td></tr>';
     else
      $html.='<td width="25%"></td>
                <td width="25%"><input type="checkbox" checked/></td></tr>';
    $k++;                       
}
$html.='</table></div>';
$html.='<!-- footer  !-->
      <div style="clear:both"></div>
      <br><br><br><br><br><br>
<!-- footer top  !-->
<div class="footer-top">
  <div style="float:left;width:40%">
    <h4>Geeignet für</h4>
    <p>Inflammation<br>Vegan<br>Vegetarier</p>
    <h4>Rückverfolgbarkeit</h4>
    <p>Die Rückverfolgbarkeit des Produktes ist anhand
        des Kundenauftrages und der Chargennummer
        gewährleistet.</p>
  </div>
  <div style="float:left;padding-left:20px;width:40%">
    <h4>Lebensmittelrecht und Zertifikate</h4>
    <p>Das Produkt entspricht den Anforderungen des
    deutschen Lebensmittelrechts sowie anzuwendender
    EU Verordnungen.</p>
    <p>ISO 9001:2008; ISO 22000:2005; Halal; Koscher;
    NON GMO von SGS; IP Zertifikat; HACCP</p>
    <strong>Diese Spezifikation hat Gültigkeit bis auf Widerruf
    und ersetzte alle bisherigen Ausgaben.</strong>
  </div>
</div>
<!-- footer bottom  !-->
<div style="clear:both"></div>
<br><br><br><br>
<div class="footer-bottom">
  <div style="float:left;width:30%"><img src="upload_logo/footer.png" alt="" /></div>
  <div style="float:left;width:30%;padding-left:20px">
    Efos Global Services GmbH <br>
    Elzmattenstraße 30 <br>
    79312 Emmendingen / Germany <br>
  </div>
  <div style="float:left;width:30%;padding-left:20px">
    Phone: +49 7641 95 93 701 <br>
    E-Mail: info@efos.de <br>
  </div>
  <div style="clear:both"></div>
  <br>
  <p>Alle Angaben auf Datenblättern oder Spezifikationen dienen in erster Linie der Information und sind in keiner
Weise rechtlich verbindlich. Der Anwender ist verantwortlich für die rechtliche Zulässigkeit im Verbraucherland.
© Copyright by efos GmbH | 11.01.2016</p>
</div>
      </div>                  
       </body>
       </html>
       ';
//$html.="<div style='page-break-before:always'>fdfdfdfd</div>";
//echo $html;die();       
$dompdf->loadHtml($html);
//$dompdf->loadHtml('helooo');


// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'vertical');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
//header('Content-Type: application/octet-stream');
//header('Content-Disposition: attachment; filename="morpheus.pdf"');
$dompdf->stream('morpheus');
?>