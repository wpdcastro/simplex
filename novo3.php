<?php

require __DIR__ . "/Simplex.php";

$variaveis = $_GET['variavel'];
$totalVariaveis = count($variaveis);
$restricoes = $_GET['restr'];
$totalRestricoes = count($restricoes);

$r = 1;

	for ($r = 1; count($restricoes) >= $r; $r++) {
		array_push($restricoes[$r]['vars'], "f" . $r); 
	}
print_r($restricoes);
?>
<style>
table, th, td {
  border: 1px solid black;
}
</style>

	<table cellpadding="10">
  <tr style="width:100%; border: 1px solid black;">
    <th>Base</th>
    <?php for ($i = 1; $i <= $totalVariaveis; $i++) { ?>
    	<th> p <?php echo $i; ?> </th>
    <?php } ?> 
    <?php for ($i = 1; $i <= $totalRestricoes; $i++) { ?>
    	<th> f <?php echo $i; ?> </th>
    <?php } ?>
    <th>B</th> 
  </tr>
    <?php for ($i = 1; $i <= $totalRestricoes; $i++) { ?>
    	<tr style="width:100%; border: 1px solid black;">
    		<td> f <?php echo $i; ?> </td>
            <?php 
                foreach($restricoes as $r) {
                    echo "<td>" . $r['vars'][$i] . "</td>";

                }

                $contadorTotal = 1;
                foreach($restricoes as $r) {

                    if ($contadorTotal == $i) {
                        echo $i;
                        echo "<td>" . 1 . "</td>";
                    }  else {
                        echo "<td>" . 0 . "</td>";
                    }
                    
                    $contadorTotal++;
                    
                }

                $contadorTotal = 1;
                foreach($restricoes as $r) {

                    if ($contadorTotal == $i) {
                        echo $i;
                        echo "<td>" . $r['total'][$i] . "</td>";
                    } 

                    $contadorTotal++;
                    
                }
    
            ?>
    	</tr>
    <?php } ?>
    <tr style="width:100%; border: 1px solid black;"> 
    	<td>Z</td><?php 
                foreach ($variaveis as $v) {

                    echo "<td>" . -1 * $v . "</td>";

                } 

                for ($z = count($variaveis); $z >= 0; $z--) {
                    echo "<td>" . 0 . "</td>";
                }

                ?>
    </tr>
</table>