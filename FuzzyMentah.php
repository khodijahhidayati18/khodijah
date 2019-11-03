<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class FuzzyMentah extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

   	public function index (){
   		$kepadatan = 60;
   		$polusi = 1200;
   		$tumbuhan = 63;

   		$param['k_sepi'] = $this->k_sepi($kepadatan);
   		$param['k_sedang'] = $this->k_sedang($kepadatan);
   		$param['k_ramai'] = $this->k_ramai($kepadatan);

   		$param['p_baik'] = $this->p_baik($polusi);
   		$param['p_tidaksehat'] = $this->p_tidaksehat($polusi);
   		$param['p_bahaya'] = $this->p_bahaya($polusi);

   		$param['tu_sedikit'] = $this->tu_sedikit($tumbuhan);
   		$param['tu_sedang'] = $this->tu_sedang($tumbuhan);
   		$param['tu_banyak'] = $this->tu_banyak($tumbuhan);

   		// var_dump ($this->p_spolusi($polusi));
   		$aprediket = $this->rules($param);
   		$hasil = $this->max($aprediket);
   		// print_r($param);
   		//print_r($hasil);

   		$rendah = array();
   		$normal = array();
   		$tinggi = array();
   		for ($i=0; $i<count($hasil) ; $i++) { 
   			if ($hasil[$i]['rule'] == 'rendah') {
   				array_push($rendah, $hasil[$i]['min']);
   			} else  if ($hasil[$i]['rule'] == 'normal') {
   				array_push($normal, $hasil[$i]['min']);
   			} else if ($hasil[$i]['rule'] == 'tinggi') {
   				array_push($tinggi, $hasil[$i]['min']);
   			}
   		}

   		echo "Min Rendah Adalah :";
   		print_r($rendah);
   		echo "<br>";
   		echo "Min Normal Adalah :";
   		print_r($normal);
   		echo "<br>";
   		echo "Min Tinggi Adalah :";
   		print_r($tinggi);
   		echo "<br>";   		

  		$maxR = max($rendah);
  		$maxN = max($normal);
  		$maxT = max($tinggi);


  		echo "Max Rendah Adalah :";
   		print_r($maxR);
   		echo "<br>";
   		echo "Max Normal :";
   		print_r($maxN);
   		echo "<br>";
   		echo "MaxTinggi :";
   		print_r($maxT);
   		echo "<br>";


   		$A3 = $this->a3($maxR);
   		$A2 = $this->a2($maxN);
   		$A1 = $this->a1($maxT);

   		echo "A3 : ";
   		print_r($A3);
   		echo "<br>";

   		echo "A2 :";
   		print_r($A2);
   		echo "<br>";

   		echo "A1 :";
   		print_r($A1);
   		echo "<br>";
  

   		$integralRendah = $this->integralAngka($maxR, $A3, 0);
   		echo "M1:";
   		print_r($integralRendah);
   		echo "<br>";

   		$integralTinggi = $this->integralAngka($maxT, $A1, 0);
   		echo "M2:";
   		print_r($integralTinggi);
   		echo "<br>";

   		$integralPer = $this->integral1($A2, $A1);
   		echo "M3:";
   		print_r($integralPer);
   		echo "<br>";







		$l1=$this->L1($A2,$maxN);
   		echo "L1 : " ;
   		print_r($l1);
   		echo "<br>";

   		$l2=$this->L2($A1, $A2,$maxN, $maxT);
   		echo "L2 : " ;
   		print_r($l2);
   		echo "<br>";


   		$l3=$this->L3($A1,$maxT);
   		echo "L3 : " ;
   		print_r($l3);
   		echo "<br>";


   		$jumlah_momentum= $this->sum($integralRendah, $integralPer, $integralTinggi);
   		echo "Jumlah Momentum  : ";
		print_r($jumlah_momentum);
		echo "<br>";

   		$luas=$this->sum($l1,$l2,$l3);
   		echo "Jumlah Luas : " ;
   		print_r($luas);


   		$hasil = $this->sum($integralTinggi, $integralPer, $integralRendah) / $luas;
   		echo "<br>";
   		echo "Hasil Sebesar: " ;
   		print_r($hasil);
   		echo " Celcius";
   		
   		// echo "<br>";
   		// echo($hasil);
   		// echo "<br>";
   		// echo "Celcius";
   		//print_r($maxR);
   	}

    public function k_sepi($x)
    {
    	if ($x <=30) {
    		return 1;
    	}
    	else if($x<=50 and $x >=30) {
    		return (50-$x)/(50-30);
    	}
    	else if ($x>=50) {
    		return 0;
    	}
    }

   public function k_sedang($x)
   {
   		if (($x <=30) or ($x>=70)) {
    		return 0;
    	}
    	else if(($x<=50) and ($x>=30)) {
    		return ($x-30)/(50-30);
    	}
    	else if ($x >=50 and $x<=70) {
    		return (70-$x)/(70-50);
    	}
   }

   	public function k_ramai($x)
   	{
   		if ($x <=50){
   			return 0;
   		}
   		else if (($x>=50) and ($x<=70)){
   			return ($x-50)/(70-50);
   		}
   		else if ($x>=70) {
   			return 1;
   		}
   	}

	public function p_baik ($x)
	   	{
	   		if ($x <=500){
   				return 1;
   			}
   			else if (($x<=500) or ($x>=1500)){
   				return (1000-$x)/ ((1000-500));
   			}
   			else if ($x>=1000) {
   				return 0;
   			}
	   	}  

	public function p_tidaksehat($x)
	 	{
	 		if (($x<=500) or ($x>=1500)) {
	 			return 0;
	 		}
	 		else if ((500<=$x) and ($x<=1000)){
	 			return ($x-500)/(1000-500);
	 		}
	 		else if ((1000<=$x) and ($x<=1500)){
	 			return (1500-$x)/(1500-1000);
	 		}

	 	} 	

	 public function p_bahaya($x)
	 {
	 	if ($x<=1000) {
	 		return 0;
	 	}
	 	else if (($x>=1000) and ($x<=1500)){
	 		return ($x-1000)/(1500-1000);
	 	}
	 	else if ($x>=1500){
	 		return 1;
	 	}
	 }


	public function tu_sedikit($x)
	{
		if ($x<=30) {
			return 1;
		}
		else if (($x<=30) or ($x<=50)){
			return (50-$x)/(50-30);
		}
		else if ($x>=50){
			return 0;
		}
	}

	public function tu_sedang($x)
	{
		if (($x<=50) or ($x>=70)){
			return 0;
		}
		else if (($x>=50) and ($x<=70)){
			return (70-$x)/(70-50);
		}
		else if ($x<=70){
			return 1;
		}
	}

	public function tu_banyak($x)
	{
		if ($x <=50){
   			return 0;
   		}
   		else if (($x>=50) and ($x<=70)){
   			return ($x-50)/(70-50);
   		}
   		else if ($x>=70) {
   			return 1;
   		}
	}


	public function rules($x)
	{
		$k_sepi = $x['k_sepi'];
		$k_sedang = $x['k_sedang'];
		$k_ramai = $x['k_ramai'];

		$p_baik = $x['p_baik'];
		$p_tidaksehat = $x['p_tidaksehat'];
		$p_bahaya = $x['p_bahaya'];
	
		$tu_sedikit = $x['tu_sedikit'];
		$tu_sedang = $x['tu_sedang'];
		$tu_banyak = $x['tu_banyak'];

		$i = 0; 
		// $aprediket = array(0);
		if ($k_sepi != 0 AND $p_baik != 0 AND $tu_sedikit != 0) {
			$aprediket[$i]['min']=min($k_sepi, $p_baik, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='1';
			$i++;
		}

		if ($k_sepi != 0 AND $p_baik != 0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_sepi, $p_baik, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='2';
			$i++;
		}

		if ($k_sepi !=0 AND $p_baik !=0 AND $tu_banyak !=0) {
			$aprediket[$i]['min']=min($k_sepi, $p_baik, $tu_banyak);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='3';
			$i++;
		}

		if ($k_sepi !=0 AND $p_tidaksehat !=0 AND $tu_sedikit !=0) {
			$aprediket[$i]['min']=min($k_sepi, $p_tidaksehat, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='4';
			$i++;
		}

		if ($k_sepi !=0 AND $p_tidaksehat !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_sepi, $p_tidaksehat, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='5';
			$i++;

		}

		if ($k_sepi !=0 AND $p_tidaksehat !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_sepi, $p_tidaksehat, $tu_banyak);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='6';
			$i++;

		}

		if ($k_sepi !=0 AND $p_bahaya !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_sepi, $p_bahaya, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='7';
			$i++;

		}

		if ($k_sepi !=0 AND $p_bahaya !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_sepi, $p_bahaya, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='8';
			$i++;

		}

		if ($k_sepi !=0 AND $p_bahaya !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_sepi, $p_bahaya, $tu_banyak);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='9';
			$i++;

		}

		if ($k_sedang !=0 AND $p_baik !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_baik, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='10';
			$i++;

		}

		if ($k_sedang !=0 AND $p_baik !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_baik, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='11';
			$i++;
		}

		if ($k_sedang !=0 AND $p_baik !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_baik, $tu_banyak);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='12';
			$i++;
		}

		if ($k_sedang !=0 AND $p_tidaksehat !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_tidaksehat, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='13';
			$i++;
		}

		if ($k_sedang !=0 AND $p_tidaksehat !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_tidaksehat, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='14';
			$i++;
		}

		if ($k_sedang !=0 AND $p_tidaksehat !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_tidaksehat, $tu_banyak);
			$aprediket[$i]['rule']='rendah';
			$aprediket[$i]['ke']='15';
			$i++;
		}

		if ($k_sedang !=0 AND $p_bahaya !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_bahaya, $tu_sedikit);
			$aprediket[$i]['rule']='rendah';
			$aprediket[$i]['ke']='16';
			$i++;
		}


		if ($k_sedang !=0 AND $p_bahaya !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_bahaya, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='17';
			$i++;
		}

		if ($k_sedang !=0 AND $p_bahaya !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_bahaya, $tu_banyak);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='18';
			$i++;
		}

		if ($k_ramai !=0 AND $p_baik !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_baik, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='19';
			$i++;
		}

		if ($k_ramai !=0 AND $p_baik !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_baik, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='20';
			$i++;
		}

		if ($k_ramai !=0 AND $p_baik !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_baik, $tu_banyak);
			$aprediket[$i]['rule']='rendah';
			$aprediket[$i]['ke']='21';
			$i++;
		}


		if ($k_ramai !=0 AND $p_tidaksehat !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_tidaksehat, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='22';
			$i++;
		}

		if ($k_ramai !=0 AND $p_tidaksehat !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_tidaksehat, $tu_sedang);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='23';
			$i++;
		}

		if ($k_ramai !=0 AND $p_tidaksehat !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_tidaksehat, $tu_banyak);
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='24';
			$i++;
		}

		if ($k_ramai !=0 AND $p_bahaya !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_bahaya, $tu_sedikit);
			$aprediket[$i]['rule']='tinggi';
			$aprediket[$i]['ke']='25';
			$i++;
		}

		if ($k_ramai !=0 AND $p_bahaya !=0 AND $tu_sedang !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_bahaya, $tu_sedang);
			$aprediket[$i]['rule']='tinggi';
			$aprediket[$i]['ke']='26';
			$i++;
		}


		if ($k_ramai !=0 AND $p_bahaya !=0 AND $tu_banyak !=0){
			$aprediket[$i]['min']=min($k_ramai, $p_bahaya, $tu_banyak);
			$aprediket[$i]['rule']='tinggi';
			$aprediket[$i]['ke']='27';
			$i++;
		}
		return $aprediket;
	}

	public function max($aprediket)
	{
		for ($i=0; $i < sizeof($aprediket) ; $i++) { 
			$x = $aprediket[$i]['rule'];
			$a = $aprediket[$i]['min'];

			// print_r($x);
			// print_r($a);

			if ($x == 'rendah'){
				if ($a<=22) {
					$z = 1;
				} else if (($a >= 22) AND ($a <= 26)){
					$z = (26-$a)/(26-22);
				} else if ($a >= 26){
					$z = 0;
				}
   				$aprediket[$i]['max'] = 26 - $a*(26-22);
   			} else if ($x == 'normal') {
   				if (($a<=22) OR ($a >= 32)) {
					$z = 0;
				} else if (($a >= 22) AND ($a <= 26)){
					$z = ($a-22)/(26-22);
				} else if (($a >= 26) AND ($a <= 32)){
					$z = (32-$a)/(32-26);
				}
   				$aprediket[$i]['max'] = 32 - $a*(32-26);
   			} else if ($x == 'tinggi')  {
   				if ($a<=26) {
					$z = 0;
				} else if (($a >= 26) AND ($a <= 32)){
					$z = ($a-26)/(32-26);
				} else if ($a >= 32){
					$z = 1;
				}

   				$aprediket[$i]['max'] =26-$a*(32-26);
   			}	
		}
		
		return $aprediket;
	}



	public function a3($x)
	{
		return $x*(26-22)+22;

	}

	public function a2($x)
	{
		return $x*(26-22)+22;
	}


	public function a1($x)
	{
		return (32-($x*(32-26)));
	}


	public function integralAngka($x, $atas, $bawah)
	{
		return ($x/2)*pow($atas,2) - ($x/2)*pow($bawah,2);
	}



	function integralPer($x=1){
	return 
	($x**3-22*($x**2))/8
	-
	((1/24)*$x**3)
	;
	}
	function integral1($x=0,$x2=1){
		return $this->integralPer($x2) - $this->integralPer($x);
	}
	

	public function L1($A2, $maxN)
	{
		return $A2 * $maxN;
	}


	public function L2($maxN, $maxT, $A1,$A2)
	{
		return (($maxN + $maxT) * ($A1 - $A2)/2);
	}

	public function L3($A1, $maxT)
	{
		return $A1 * $maxT;
	}



	// public function L1($deN, $deT, $maxN, $maxT);
	// {
	// 	return $deN * $maxN;
	// }

	// public function luas_daerah($A1, $A2, $A3, $maxR, $maxN, $maxT){
	// 	 return $A3 * $maxR +
	// 	  (($maxN + $maxT)*($A1-$A2/2) + $A1 * $maxT
	// }
			
	public function sum($x, $y, $z)
	{
		return $x+$y+$z;
	}

}
