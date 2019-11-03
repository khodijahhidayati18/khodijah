<?php


//submit

defined('BASEPATH') OR exit('No direct script access allowed');

class Fuzzy extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->model("Grafik_model");
        $this->load->library('form_validation');
    }

   	public function index (){

   		// $data["gas_grafik"] = $this->Product_model->getAll();
     //    $data['tgl'] = $date;
        // $data["perWaktu"] =$this->Grafik_model->getPerHour()
      // $this->load->view("admin/product/v_fuzzy", $data);
   		$this->load->view('admin/product/v_input');
   	}

   	public function inputdata(){
   		$this->load->view('admin/product/v_input');
   	}

   	public function submit(){
   		$k=$this->input->get('kepadatan');
   		$p=$this->input->get('polusi');
   		$t=$this->input->get('tumbuhan');

   		// echo $k."k_sepi".$k."k_sedang".$t."k_ramai";
   		// echo $p."p_tpolusi".$p."p_sedang".$p."p_spolusi";
   		// echo $t."tu_sedikit".$t."tu_sedang".$t."tu_banyak";
   	

   		$fuz=$this->fuzzy($k,$p,$t);
   		$data['fuz']=$fuz;
   		$data['k']=$k;
   		$data['p']=$p;
   		$data['t']=$t;
   		$this->load->view('admin/product/v_input',$data);
   	}

   	public function fuzzy($k,$p,$t){

   		$kepadatan = $k;
   		$polusi = $p;
   		$tumbuhan = $t;

   		// $kepadatan = 60;
   		// $polusi = 1200;
   		// $tumbuhan = 63;

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
   		// print_r($aprediket);
   		$hasil = $this->max($aprediket);
   		// print_r($param);
   		//print_r($hasil);
   		/*
   		http://localhost/monitoring/index.php/fuzzy/submit?kepadatan=70&polusi=1200&tumbuhan=80&Submit=Kirim+Kueri

   		http://localhost/monitoring/index.php/fuzzy/submit?kepadatan=70&polusi=1000&tumbuhan=70&Submit=Kirim+Kueri

   		*/

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

   		// echo "Min rendah";
   		// print_r($rendah);
   		// echo"Min Normal";
   		// print_r($normal);
   		// echo "Min Tinggi";	
   		// print_r($tinggi);
   		
   		if(sizeof($rendah)>0){
  			$maxR = max($rendah);
   		}else{
   			$maxR=0;
   			$rendah=0;
   		}

   		if(sizeof($normal)>0){
  			$maxN = max($normal);
   		}else{
   			$maxN=0;
   			$normal=0;
   		}

   		if(sizeof($tinggi)>0){
  			$maxT = max($tinggi);
   		}else{
   			$maxT=0;
   			$tinggi=0;
   		}
  		// $maxN = max($normal);
  		// $maxT = max($tinggi);

   		// echo "Max Rendah";
   		// print_r($maxR);
   		// echo "</br>";
   		// echo "Max Normal";
   		// print_r($maxN);
   		// echo "</br>";
   		// echo "Max Tinggi";
   		// print_r($maxT);
   		// echo "</br>";

   		$A3 = $this->a1($maxR);
   		$A2 = $this->a2($maxN);
   		$A1 = $this->a3($maxT);

   		// print_r($deR);
   		// echo "<br>";
   		
   		// print_r($deN);
   		// echo "<br>";
   		
   		// print_r($deT);
   		// echo "<br>";

   		$integralRendah = $this->integralAngka($maxR, $A3, 0);

   		// echo "Defuzzi Rendah:";
   		// print_r($integralRendah);
   		// echo "<br>";

   		$integralTinggi = $this->integralAngka($maxT, $A1, 0);
   		// echo "Defuzzifikasi Tinggi:";
   		// print_r($integralTinggi);
   		// echo "<br>";

   		$integralPer=$this->integral1($A2, $A1);
   		// echo "Defuzzifikasi Normal:";
   		// print_r($integralPer);
   		// echo "<br>";

   		$l1=$this->L1($A2,$maxN);

   		$l2=$this->L2($A1, $A2,$maxN, $maxT);

   		$l3=$this->L3($A1,$maxT);

   		// echo "Luas Daerah Adalah :" ;
   		$jumlah_momentum=$this->sum($integralRendah,$integralPer, $integralTinggi);

   		$luas=$this->sum($l1, $l2, $l3);

   		$hasil = $this->sum($integralTinggi, $integralPer, $integralRendah) / $luas;
   		// echo($hasil);
   		// echo "Celcius";
   		$ret=sprintf("
   			Min Rendah: %0.1f \n <br>
   			Min Normal: %0.1f \n <br>
   			Min Tinggi: %0.1f \n <br>

   			Max Rendah: %0.1f \n <br>
   			Max Normal: %0.1f \n <br>
   			Max Tinggi: %0.1f \n <br>

   			A3: %d \n <br>
   			A2: %d \n <br>
   			A1: %d \n <br>

   			M1: %0.1f \n <br>
   			M2: %0.1f \n <br>
   			M3: %0.1f \n <br>

   			L1: %0.2f \n <br>
   			L2: %0.2f \n <br>
   			L3: %0.2f \n <br>

   			
   			Jumlah Momentum: %d \n <br>
   			Jumlah Luas: %d \n <br>

   			Hasil: %0.9f \n <br>
   			",
   			// array(
   				$rendah[0],
   				$normal[0],
   				$tinggi[0],

   				$maxR,
   				$maxN,
   				$maxT,

   				$A1,
   				$A2,
   				$A3,


   				$integralRendah,
   				$integralPer,
   				$integralTinggi,

   				$l1,
   				$l2,
   				$l3,

   				$jumlah_momentum,
   				$luas,
   				$hasil
   			// )
   		);
   		return $ret;
   	}

    public function k_sepi($x)
    {
    	if ($x <=75) {
    		return 1;
    	}
    	else if($x<=150 and $x >=75) {
    		return (150-$x)/(150-75);
    	}
    	else if ($x>=150) {
    		return 0;
    	}
    }

   public function k_sedang($x)
   {
   		if (($x <=75) or ($x>=225)) {
    		return 0;
    	}
    	else if(($x<=150) and ($x>=75)) {
    		return ($x-75)/(150-75);
    	}
    	else if ($x >=150 and $x<=225) {
    		return (225-$x)/(225-150);
    	}
   }

   	public function k_ramai($x)
   	{
   		if ($x <=225){
   			return 0;
   		}
   		else if (($x>=150) and ($x<=225)){
   			return ($x-150)/(225-150);
   		}
   		else if ($x>=225) {
   			return 1;
   		}
   	}

	public function p_baik ($x)
	   	{
	   		if ($x <=100){
   				return 1;
   			}
   			else if (($x<=100) or ($x>=200)){
   				return (200-$x)/ ((200-100));
   			}
   			else if ($x>=200) {
   				return 0;
   			}
	   	}  

	public function p_tidaksehat($x)
	 	{
	 		if (($x<=100) or ($x>=300)) {
	 			return 0;
	 		}
	 		else if ((100<=$x) and ($x<=200)){
	 			return ($x-100)/(200-100);
	 		}
	 		else if ((200<=$x) and ($x<=300)){
	 			return (300-$x)/(300-200);
	 		}

	 	} 	

	 public function p_bahaya($x)
	 {
	 	if ($x<=200) {
	 		return 0;
	 	}
	 	else if (($x>=200) and ($x<=300)){
	 		return ($x-200)/(300-200);
	 	}
	 	else if ($x>=300){
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
			$aprediket[$i]['rule']='rendah';
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
			$aprediket[$i]['rule']='normal';
			$aprediket[$i]['ke']='15';
			$i++;
		}

		if ($k_sedang !=0 AND $p_bahaya !=0 AND $tu_sedikit !=0){
			$aprediket[$i]['min']=min($k_sedang, $p_bahaya, $tu_sedikit);
			$aprediket[$i]['rule']='normal';
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
			$aprediket[$i]['rule']='normal';
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
			$aprediket[$i]['rule']='tinggi';
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
			$aprediket[$i]['rule']='normal';
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

	// function integralPer($x=1)
	//  {		

	// 		return ($x**3-22*($x**2))/8-((1/24)*$x**3);
	// }
	// 	function integral1($x=0,$x2=1){
	// 	return integralPer($x2)-integralPer($x);
	
	// 	echo "hasil ".integral1(24, 29.6);


	//integral yang per
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

	
	// public function luas_daerah($deR, $deN, $deT, $maxR, $maxN, $maxT){
	// 	 return $deR * $maxR +
	// 	  (($maxN + $maxT)*($deT-$deN)/2) +
	// 	   $deT * $maxT;
	// }
			
	public function sum($x, $y, $z)
	{
		return $x+$y+$z;
	}

}

