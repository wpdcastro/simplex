<?php

class Simplex
{
	public $nvariaveis;
	public $nrestricoes;

	public $funcao;
	public $variaveis;
	public $restricoes;
	public $b;
	public $iteracoes;

	public $tabela = array();
	public $parciais = array();
	public $nt = 0;

	public function __construct($variaveis, $restricoes, $funcao, $b, $iteracoes)
	{
		$this->variaveis = $variaveis;
		$this->restricoes = $restricoes;
		$this->funcao = $funcao;
		$this->b = $b;
		$this->iteracoes = $iteracoes;

		$this->nvariaveis = count($this->variaveis)-1;
		$this->nrestricoes = count($this->restricoes)-1;
		$nb = count($this->b)-1;

		//echo $nvariaveis;

		$this->tabela['z'][0] = 1;

		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			$this->tabela['z'][$i] = 0;
		}

		for($i = 1; $i <= $this->nvariaveis; $i++)
		{
			$iv = $this->variaveis[$i]['vr'];
			$vv = -$this->variaveis[$i]['va'];
			$this->tabela[$iv] = array();
			$this->tabela[$iv][0] = intval($vv);

			for($j = 1; $j <= $this->nrestricoes; $j++)
			{
				$this->tabela[$iv][$j] = intval($this->restricoes[$j][$i]);
				//echo json_encode($this->tabela);	
				// echo json_encode(intval($restricoes[$j][$i]));
			}
		}

		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			$iv = 'fx'.$i;
			$this->tabela[$iv] = array();
			$this->tabela[$iv][0] = 0;

			for($j = 1; $j <= $this->nrestricoes; $j++)
			{
				if($i === $j)
					$this->tabela[$iv][$j] = 1;
				else
					$this->tabela[$iv][$j] = 0;
			}
		}
		$this->tabela['b'] = array();
		$this->tabela['b'][0] = 0;
		for($i = 1; $i <= $nb; $i++)
		{
			$this->tabela['b'][$i] = intval($b[$i]);
		}
	}

	public function resolve()
	{
		
		while($this->hasNegativeValue() && ($this->iteracoes == 0 || $this->iteracoes > $this->nt))
		{
			$lineInputVariable = $this->getInputVariable();
			$outputLine = $this->getOutputLine($lineInputVariable);
			$elementP = $this->tabela[$lineInputVariable][$outputLine];
			$newLineP = $this->newLineP($outputLine, $elementP);
			
			$this->parciais[$this->nt]["tabela"] = $this->tabela;
			$this->parciais[$this->nt]["solucao"] = $this->getSolution();
			if($this->hasNegativeValue())
				$this->parciais[$this->nt]["otima"] = false;			
			else
				$this->parciais[$this->nt]["otima"] = true;			

			
			$this->calculateNewLines($newLineP, $lineInputVariable, $outputLine);
			$this->nt++;

			if($this->nt == 50) // tratativa para funções ilimitadas
				break;
		}
		
		$this->parciais[$this->nt]["tabela"] = $this->tabela;
		$this->parciais[$this->nt]["solucao"] = $this->getSolution();
		if($this->hasNegativeValue())
		{
			$this->parciais[$this->nt]["otima"] = false;			
			$this->parciais["otimaFinal"] = false;			
		}
		else
		{
			$this->parciais["otimaFinal"] = true;			
		}

		$this->parciais["solucaoFinal"] = $this->getSolution();
		$this->parciais["final"] = $this->tabela;
		$this->parciais["qtdFinal"] = $this->nt;

		echo json_encode($this->parciais);
	}


	public function hasNegativeValue()
	{
		$v = array();
		//variaveis
		for($i = 1; $i <= $this->nvariaveis; $i++)
		{
			$iv = $this->variaveis[$i]['vr'];
			array_push($v, $this->tabela[$iv][0]);
		}
		//folgas
		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			$iv = 'fx'.$i;
			array_push($v, $this->tabela[$iv][0]);
		}

			foreach ($v as $a) {
				if($a < 0)
					return true;
			}



		return false;
	}

	public function getInputVariable()
	{
		$v = array();
		//variaveis
		for($i = 1; $i <= $this->nvariaveis; $i++)
		{
			$iv = $this->variaveis[$i]['vr'];
			$l = array("v"=>$this->tabela[$iv][0], "c"=>$iv);
			array_push($v, $l);
		}
		//folgas
		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			$iv = 'fx'.$i;
			$l = array("v"=>$this->tabela[$iv][0], "c"=>$iv);
			array_push($v, $l);
		}
		
		$m["v"] = 0;

			foreach($v as $a)
			{		
				if($a["v"] < $m["v"])
				{
					$m = $a;
				}
			}
		//echo json_encode($m);
		return $m["c"];
	}

	public function getOutputLine($inputV)
	{
		$input = $this->tabela[$inputV];
		$v = array();
		
		// echo json_encode($input);
		// echo json_encode($this->tabela['b']);

		//restricoes
		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			if($input[$i] != 0)
			{
				$l = array("v"=>$this->tabela['b'][$i]/$input[$i], "l"=>$i);
				array_push($v, $l);
			}
		}

		$m["v"] = 0;
		//echo json_encode($v);

		foreach ($v as $a) {
			if($a["v"] > $m["v"])
				$m = $a;
		}

		foreach ($v as $a) {
			if($a["v"] < $m["v"] && $a["v"] > 0)
				$m = $a;
		}
		//echo json_encode($m);
		return $m["l"];
	}

	public function newLineP($outputLine, $elementP)
	{
		$nlp = array();

		// if($elementP == 0)
		// 	$nlp['z'] = $elementP;
		// else
			$nlp['z'] = $this->tabela['z'][$outputLine]/$elementP;

		for($i = 1; $i <= $this->nvariaveis; $i++)
		{
			$iv = $this->variaveis[$i]['vr'];
			// if($elementP == 0)
			// 	$nlp[$iv] = $elementP;
			// else
				$nlp[$iv] = $this->tabela[$iv][$outputLine]/$elementP;
		}
		//folgas
		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			$iv = 'fx'.$i;
			// if($elementP == 0)
			// 	$nlp[$iv] = $elementP;
			// else
				$nlp[$iv] = $this->tabela[$iv][$outputLine]/$elementP;
		}
		
		// if($elementP == 0)
		// 	$nlp['b'] = $elementP;
		// else
			$nlp['b'] = $this->tabela['b'][$outputLine]/$elementP;

		//echo json_encode($nlp);

		return $nlp;
	}

	public function calculateNewLines($nlp, $lineInputVariable, $outputLine)
	{
		//echo json_encode($nlp);
		for($l = 0; $l < $this->nrestricoes+1; $l++)
		{
			if($l != $outputLine)
			{
				$mult = ($this->tabela[$lineInputVariable][$l])*(-1);

				//echo json_encode($mult);

				$this->tabela['z'][$l] = ($nlp['z']*$mult)+$this->tabela['z'][$l];
				
				for($i = 1; $i <= $this->nvariaveis; $i++)
				{
					$iv = $this->variaveis[$i]['vr'];
					$this->tabela[$iv][$l] = ($nlp[$iv]*$mult)+$this->tabela[$iv][$l];
				}

				for($i = 1; $i <= $this->nrestricoes; $i++)
				{
					$iv = 'fx'.$i;
					$this->tabela[$iv][$l] = ($nlp[$iv]*$mult)+$this->tabela[$iv][$l];
				}

				$this->tabela['b'][$l] = ($nlp['b']*$mult)+$this->tabela['b'][$l];
			}
		}

		$this->tabela['z'][$outputLine] = $nlp['z'];
		for($i = 1; $i <= $this->nvariaveis; $i++)
			{
				$iv = $this->variaveis[$i]['vr'];

				$this->tabela[$iv][$outputLine] = $nlp[$iv];
			}

			for($i = 1; $i <= $this->nrestricoes; $i++)
			{
				$iv = 'fx'.$i;
				$this->tabela[$iv][$outputLine] = $nlp[$iv];
			}
		$this->tabela['b'][$outputLine] = $nlp['b'];
	}

	public function getSolution()
	{
		
		$vs = array();
		for($i = 1; $i <= $this->nvariaveis; $i++)
		{
			$qtd0 = 0;
			$qtd1 = 0;	
			$iv = $this->variaveis[$i]['vr'];
			$qtc = count($this->tabela[$iv])-1;
			$l = 0;
			for($j = 1; $j <= $qtc; $j++)
			{
				if($this->tabela[$iv][$j] == 0)
					$qtd0++;
				else if($this->tabela[$iv][$j] == 1)
				{
					$qtd1++;
					$l = $j;
				}
			}

			if($qtd1 == 1 && $qtd0 == ($qtc-1))
				array_push($vs, array("l"=>$l,"v"=>$iv));
		}

		for($i = 1; $i <= $this->nrestricoes; $i++)
		{
			$qtd0 = 0;
			$qtd1 = 0;	
			$iv = 'fx'.$i;
			$qtc = count($this->tabela[$iv])-1;
			$l = 0;
			for($j = 1; $j <= $qtc; $j++)
			{
				if($this->tabela[$iv][$j] == 0)
					$qtd0++;
				else if($this->tabela[$iv][$j] == 1)
				{
					$qtd1++;
					$l = $j;
				}
			}
			
			if($qtd1 == 1 && $qtd0 == ($qtc-1))
				array_push($vs, array("l"=>$l,"v"=>$iv));
		}

		return $vs;
	}

	public function getResult()
	{
		return $this->tabela;
	}
}