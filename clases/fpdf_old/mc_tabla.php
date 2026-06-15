<?php
class mc_table extends FPDF
{
//=======================Encabezado de la Hoja================================
function Header()
{
$this->Image('../../img/persona.jpeg',15,10,255,10);
//Select Arial bold 15
$this->SetFont('Arial','B',14);
//Move to the right
}

//==================================Pie de pagina================================
function Footer()
{
$this->Image('../../img/barra.jpg',15,200,255,1);
//Go to 1.5 cm from bottom
$this->SetY(-15);
//Select Arial italic 8
$this->SetFont('Arial','I',8);
$this->SetTextColor(0,0,0);
//Print centered page number
$this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C');
}
//==========================================================================
var $widths;
var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'J';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
		$this->SetDrawColor(201,200,200);
		$this->SetTextColor(91,92,92);
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a,0);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
	{
        $this->AddPage($this->CurOrientation);
		$this->Ln(10);
		$this->Cell(21);
	}
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}
//=========================================================================
?>
